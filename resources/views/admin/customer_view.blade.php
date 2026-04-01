@extends('admin.layouts.app')

@section('title') AUFTRÄGE @stop

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css')}}">
 <link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" />
<script src="{{ asset('js/dropzone.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/customer_product.css')}}"> 
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

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
                                                        <form method="POST" action="{{ route('deal.store') }}">
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
                                            <div class="col-4 mb-1">
                                                <form action="{{ action('App\Http\Controllers\PlaningController@index') }}">
                                                    <fieldset>
                                                        <div class="input-group">
                                                            <input type="text" name="search" class="form-control" placeholder="Search Form" aria-describedby="button-addon2">
                                                            <div class="input-group-append" id="button-addon2">
                                                                <button class="btn btn-primary" type="submit">Go</button>
                                                            </div>
                                                            
                                                        </div>
                                                    </fieldset>
                                                </form>
                                                
                                            </div>  
                                           
                                        </div>

                                    
                                        <!-- Contents Details of Customer -->
                                        <div class="row"> 
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                         <tr style="background:white; "> 
                                                            <th style="width: 45px;" >ID</th> 
                                                            <th  class="bolders ">DATUM</th> 
                                                            <th  class="bolders ">NAME</th> 
                                                            <th  class="bolders ">KONTAKT</th> 
                                                            <th  class="bolders ">INFO</th>  
                                                            <th  class="bolders ">GEWERKE</th> 
                                                            <th style="width:20px !important" >   <span data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">STATUS  </span> 
                                                                    <div class="dropdown-menu">
                                                                        <span><label for="">Filtern nach</label></span>
                                                                         <span class="dropdown-item">
                                                                           <a  href="{{ url('/lead_qualified_sort') }}" ><i class="fa fa-circle primary" ></i> QUALIFIZIERT</a> 
                                                                        </span>
                                                                       
                                                                        <span class="dropdown-item">
                                                                             <a  href="{{ url('/lead_not_qualified_sort') }}" ><i class="fa fa-circle warning" ></i> ERFORDERLICHE INFORMATIONEN</a>  
                                                                        </span>

                                                                        <span class="dropdown-item">
                                                                             <a  href="{{ url('/lead_incomplete_sort') }}" ><i class="fa fa-circle danger" ></i> NICHT QUALIFIZIERT</a>  
                                                                        </span> 

                                                                        <span class="dropdown-item">
                                                                             <a  href="{{ url('/lead_junk_sort') }}" ><i class="fa fa-power-off danger" ></i> JUNKS</a>  
                                                                        </span> 
                                                                    </div> 
                                                            </th>
                                                            <th>VERFASSER</th>
                                                            <th>PREIS</th>
                                                            <th>UNTERSCHRIFTSDATUM</th>
                                                            <th>DOKUMENT</th>
                                                            <th width="2">BEARBEITEN</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($data as $item)    
                                                            <tr style="background:white;border-bottom: 1px solid rgb(243, 243, 243)" class="mb-2"> 
                                                                <th scope="row">{{ $item->id }}</th>
                                                                
                                                                <td>
                                                                    <i class="feather icon-calendar"></i> {{ \Carbon\Carbon::parse($item->created_at)->isoFormat('DD.MM.YY') }} <br>
                                                                    <code> <strong> 
                                                                        {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}                                   
                                                                    </strong></code>  
                                                                </td>
                                                                <td><a href="{{url('new_lead_profile/'.$item->customer_id )}}">
                                                                        {{ $item->name }}  {{ $item->lastname }} <br>
                                                                        <small>
                                                                            {{ $item->full_address }}
                                                                        </small>
                                                                    </a>
                                                                </td>
                                                                    
                                                                <td>
                                                                    <p class="mb-0" ><i class="feather icon-phone-call" ></i> {{ $item->telephone }}</p>
                                                                    <p class="mb-0" ><i class="feather icon-smartphone" ></i> {{ $item->phone }}</p>
                                                                    <p class="mb-0" ><i class="feather icon-mail" ></i> {{ $item->email }}</p>
                                                                </td> 
                                                                <td>
                                                                    <button type="button" class="btn btn-outline-warning waves-effect waves-light" data-toggle="modal" data-target="#jobs{{ $item->id }}">
                                                                        <i class="feather icon-info" ></i>  
                                                                </button> 
                                                                <!-- Description Edit Modal -->
                                                                <div class="modal fade text-left" id="jobs{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true" style="display: none;">
                                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h4 class="modal-title" id="myModalLabel17">Arbeitsbeschreibung: {{$item->name}} {{$item->lastname}}</h4>
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">×</span>
                                                                                </button>
                                                                            </div>
                                                                            <form class="form form-horizontal" method="post" action="{{ route('deal.info') }}"  class="custom-file-upload" enctype="multipart/form-data">
                                                                                @csrf  
                                                                                <div class="modal-body"> 
                                                                                    <input type="hidden" value="{{ $item->id }}" name="id"> 
                                                                                     <div class="row"> 
                                                                                        <div id="editor_edit" class="editor-container form-control" data-target="#editor_text_edit" style="height: 400px !important;">{!! old('description', $item->info) !!}</div>
                                                                                        <textarea name="description" hidden id="editor_text_edit" cols="30" rows="10">{!! old('info', $item->info) !!}</textarea> 
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-danger waves-effect waves-light"  data-dismiss="modal" >abbrechen</button>
                                                                                    <button type="submit" class="btn btn-primary waves-effect waves-light">speichern</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                </td>
                                                                
                                                                <td>
                                                                    <div style="justify-items: center;display: flex;align-items: center;justify-content: flex-start;flex-wrap: nowrap;">
                                                             
 
                                                                            @php
                                                                                $services = [
                                                                                    'complete' => 'Komplettlösung',
                                                                                    'montage' => 'Montage',
                                                                                    'product' => 'Produkt',
                                                                                    'plan' => 'Planung',
                                                                                    'maintenance' => 'Wartung',
                                                                                    'repair' => 'Reparatur',
                                                                                    'others' => 'Sonstiges',
                                                                                ]; 
                                                                                $service = $services[$item->service] ?? $item->service;  
                                                                            @endphp
                                                                
 
                                                                                 @php
                                                                                        // Determine the default image based on gender
                                                                                        $defaultImage = $item->gender === "Male" 
                                                                                            ? asset('images/gender/male.png') 
                                                                                            : asset('images/gender/female.png');

                                                                                        // Determine the actual image to use
                                                                                        $employeeImage = file_exists('images/employee/'.$item->emp_image) && $item->emp_image 
                                                                                            ? asset('images/employee/'.$item->emp_image) 
                                                                                            : $defaultImage;
                                                                                    @endphp 

                                                                                    <div class="d-flex flex-column align-items-center mr-1">
                                                                                        <div class="d-flex align-items-center">
                                                                                            <div class="circle">{{ $item->initial }}</div>
                                                                                            <div class="line"></div> 
                                                                                            <div class="image" data-toggle="tooltip" 
                                                                                                data-original-title="{{ $item->emp_name && $item->emp_lastname ? $item->emp_name . ' ' . $item->emp_lastname : 'Nicht zugewiesen' }}">
                                                                                                <img src="{{ $employeeImage }}" alt="Profile"  
                                                                                                
                                                                                                class="profile">
                                                                                            </div> 
                                                                                        </div>
                                                                                    <div class="text">{{ $service }}</div>
                                                                                </div>
                                                                </td>  
                                                                
                                                         
                                                                <td class="editable-cell status-cell" data-field="status" data-id="{{ $item->id }}">
                                                                    <div class="badge badge-primary">
                                                                        @if($item->status == 'confirm') Bestätigt
                                                                        @elseif($item->status == 'inconfirm') Unbestätigt
                                                                        @else Nicht gesetzt
                                                                        @endif
                                                                    </div>
                                                                </td>

                                                                </td>
                                                                @php
                                                                    $employee = DB::table('employees')->where('id', $item->contact_person)->select('name', 'lastname', 'image')->first();
                                                                    $c_image = $employee->image;
                                                                    $c_name = $employee->name;
                                                                    $c_lastname = $employee->lastname;
                                                                @endphp     
                                                                <td style="width:20px">
                                                                    <div class="image">
                                                                        <div class="avatar mr-1 ">
                                                                            <img src="{{ asset('images/employee/'.$c_image)}}" alt="avtar img holder" height="32" width="32" data-toggle="tooltip" data-placement="top" title data-original-tiitle="{{ $c_name }} {{ $c_lastname}}">
                                                                        </div>
                                                                        <div class="text">
                                                                            <span class="font-weight-bold"></span>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="editable-cell price-cell" data-field="price" data-id="{{ $item->id }}">
                                                                    {{ $item->price ?? 'unbekannt' }}
                                                                </td>

                                                                <td class="editable-cell sign-date-cell" data-field="sign_date" data-id="{{ $item->id }}">
                                                                    {{ $item->sign_date ?? 'unbekannt' }}
                                                                </td>

                                                                 <td>
                                                                    
                                                                        <button type="button" class="btn btn-outline-warning waves-effect waves-light"  data-toggle="modal" data-target="#upload{{$item->id}}">
                                                                                Hochladen
                                                                        </button> 
                                                                        <div class="modal fade text-left" id="upload{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true" style="display: none;">
                                                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h4 class="modal-title" id="myModalLabel17">UPLOAD</h4>
                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                            <span aria-hidden="true">×</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                            <form  action="{{ url('customer_upload') }}" method="POST" class="dropzone" id="upload-customer-image" enctype="multipart/form-data" style="background: transparent; border: 1px dashed #8fc73e; border-radius: 20px;">
                                                                                                @csrf
                                                                                                <input type="hidden" name="customer_id" value="{{$item->customer_id}}">
                                                                                                <input type="hidden" name="alternative_id" value="{{$item->alternative_id}}">
                                                                                                <input type="hidden" name="product_id" value="{{$item->product_id}}">
                                                                                                <input type="hidden" name="stage_id" id="stage_id" value="">
                                                                                                  <input type="hidden" name="status" value="deal">
                                                                                                <div>
                                                                                                    <label for="swal-stage">Stufe auswählen:</label>
                                                                                                    <select id="swal-stage" class="form-control" name="stage_id">
                                                                                                        <option value="">-- Wählen Sie eine Stufe --</option>
                                                                                                        <option value="order">Kundenauftrag</option>
                                                                                                        <option value="confirmed_order">Auftragsbestätigung</option>
                                                                                                        <option value="offer">Angebot</option>
                                                                                                    </select>
                                                                                                </div>
                                                                                            </form>

                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal">OK</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div> 
                                                                         
                                                                            <!-- Button to Open the Modal -->
                                                                        <button type="button" class="btn btn-outline-warning waves-effect waves-light open_image"
                                                                            data-customer="{{$item->customer_id}}"
                                                                            data-alternative="{{$item->alternative_id}}"
                                                                            data-product="{{$item->product_id}}"
                                                                            data-status="deal"
                                                                            data-toggle="modal"
                                                                            data-target="#document{{$item->customer_id}}">
                                                                            Dokumente
                                                                        </button>
                                                                        <!-- Main Document Modal -->
                                                                        <div class="modal fade text-left" id="document{{$item->customer_id}}" tabindex="-1" role="dialog" aria-labelledby="documentModalTitle" aria-hidden="true">
                                                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                            <span aria-hidden="true">&times;</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <!-- Document content will be loaded here dynamically -->
                                                                                        <p class="text-center text-muted">Laden...</p>
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal">OK</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Image Preview Modal -->
                                                                        <div class="modal fade text-left" id="imagePreviewModal" tabindex="-1" role="dialog" aria-labelledby="imagePreviewTitle" aria-hidden="true">
                                                                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h4 class="modal-title">Bildvorschau</h4>
                                                                                        <button type="button" class="close close-image-modal" data-dismiss="modal">&times;</button>
                                                                                    </div>
                                                                                    <div class="modal-body text-center">
                                                                                        <img id="imagePreview" src="" class="img-fluid">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- PDF Viewer Modal -->
                                                                        <div class="modal fade text-left" id="pdfViewerModal" tabindex="-1" role="dialog" aria-labelledby="pdfViewerTitle" aria-hidden="true">
                                                                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h4 class="modal-title">PDF Vorschau</h4>
                                                                                        <button type="button" class="close close-pdf-modal" data-dismiss="modal">&times;</button>
                                                                                    </div>
                                                                                    <div class="modal-body text-center">
                                                                                        <iframe id="pdfViewer" src="" width="100%" height="500px"></iframe>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        
                                                                </td>
                                                                <td>

                                                                <div class="btn-group dropup dropdown-icon-wrapper mr-1 mb-1"> 
                                                                    <button type="button" class="btn   dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                        <i class="feather icon-menu dropdown-icon"></i>
                                                                    </button>
                                                                    <div class="dropdown-menu"> 


                                                                        @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_update', '=', 'on')->first())
                                                                            
                                                                            <span class="dropdown-item">
                                                                                <a data-toggle="modal" class="primary" data-target="#skip{{$item->id}}"><i class="feather icon-fast-forward primary" ></i>Überspringen</a>
                                                                            </span>
                                                                            
                                                                        @endif

                                                                          @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_update', '=', 'on')->first())
                                                                          
                                                                            <span class="dropdown-item">
                                                                                <a data-toggle="modal" class="danger" data-target="#delete-pro{{$item->id}}">
                                                                                @if($item->deleted_at == Null )<i class="feather icon-trash danger" ></i> Löschen @else <i class="feather icon-refresh-ccw" ></i> Wiederherstellen @endif</a>
                                                                            </span> 
                                                                        @endif
                                                                        @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_delete', '=', 'on')->first())
                                                                            @if($item->status!="Junk")
                                                                            <span class="dropdown-item">
                                                                                <a data-toggle="modal" class="danger" data-target="#junk{{$item->id}}"><i class="fa fa-power-off danger" ></i> Junk</a>
                                                                            </span>
                                                                            @else
                                                                             <span class="dropdown-item">
                                                                                <a data-toggle="modal" class="danger" data-target="#unjunk{{$item->id}}"><i class="fa fa-power-off primary" ></i>Un-Junk</a>
                                                                            </span>
                                                                            @endif
                                                                        @endif  
                                                                    </div>
                                                                </div>
                                                                
                                                                    <!-- Delete Modal -->
                                                                    <div class="modal fade" id="delete-pro{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header bg-danger white">
                                                                                    <h5 class="modal-title" id="myModalLabel120">Daten Löschen</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">×</span>
                                                                                    </button>
                                                                                </div>
                                                                                @if($item->deleted_at == Null)
                                                                                <div class="modal-body">
                                                                                    <h5>Aufzeichnung löschen</h5>
                                                                                    <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                                    <p>Die Datensatznummer lautet:{{$item->id}}. {{ $item->name }} {{ $item->lastname }} </p>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <a type="button" href="{{url('/deal_delete').'/'.$item->id}}" class="btn btn-danger">Ja</a>
                                                                                </div>
                                                                                @else
                                                                                    <div class="modal-body"> 
                                                                                    <p>Möchten Sie diesen Datensatz wiederherstellen?</p>
                                                                                    <p>Die Datensatznummer lautet:{{$item->id}}. {{ $item->name }} {{ $item->lastname }} </p>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <a type="button" href="{{url('/deal_restore').'/'.$item->id}}" class="btn btn-danger">Ja</a>
                                                                                </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Delete Modal -->
                                                                    <div class="modal fade" id="junk{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header bg-danger white">
                                                                                    <h5 class="modal-title" id="myModalLabel120">{{ $item->name }} {{ $item->lastname }}</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">×</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <h5>Junk record</h5>
                                                                                    <p>Möchten Sie diese Anfrage als Junk festlegen?</p>
                                                                                    <p>Die Datensatznummer lautet:{{$item->id}} </p>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <a type="button" href="{{url('/deal_junk').'/'.$item->id}}" class="btn btn-danger">Ja</a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                           <!-- Unjunk Modal -->
                                                                    <div class="modal fade" id="unjunk{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header bg-primary white">
                                                                                    <h5 class="modal-title" id="myModalLabel120">{{ $item->name }} {{ $item->lastname }}</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">×</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <h5>Junk record</h5>
                                                                                    <p>Möchten Sie die Junk-Anfrage wiederherstellen?</p>
                                                                                    <p>Die Datensatznummer lautet:{{$item->id}} </p>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <a type="button" href="{{url('/deal_unjunk').'/'.$item->id}}" class="btn btn-primary">Ja</a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>  
                                                                        <!-- //Jump  -->
                                                                      <div class="modal fade" id="skip{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header bg-danger white">
                                                                                    <h5 class="modal-title" id="myModalLabel120">Optionen überspringen</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">×</span>
                                                                                    </button>
                                                                                </div>
                                                                                <form action="{{ route('planing.jump')}}" method="post">
                                                                                    @csrf
                                                                                    <div class="modal-body"> 
                                                                                        <p>Möchten Sie zu einer anderen Stufe springen?</p>
                                                                                        <input type="hidden" name="customer_id" value="{{$item->customer_id}}">
                                                                                        <input type="hidden" name="product_id" value="{{$item->product_id}}">
                                                                                        <input type="hidden" name="alternative_id" value="{{$item->alternative_id}}">
                                                                                        <input type="hidden" name="employee_id" value="{{$item->employee_id}}">
                                                                                        <input type="hidden" name="service" value="{{$item->service}}"> 
                                                                                        <select name="project_status" id="" class="form-control">
                                                                                            <option value="offer">Angebote</option>
                                                                                            <option value="deals">Aufträge</option>
                                                                                            <option value="project">Projekt</option>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="submit"  class="btn btn-primary">OK</button>
                                                                                    </div>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td> 
                                                            </tr>  
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        {{$data->links()}}
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
@endsection
 
@section('script')
<script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script> 
<script src="{{asset('app-assets/vendors/js/editors/quill/quill.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    $(document).on('dblclick', '.editable-cell', function () {
        const field = $(this).data('field');
        const id = $(this).data('id');
        const value = $(this).text().trim();

        let inputHTML = '';

        if (field === 'sign_date') {
            inputHTML = `<input type="date" class="form-control edit-field"
                              data-id="${id}"
                              data-field="${field}"
                              value="${value === 'unbekannt' ? '' : value}"
                              style="width:120px;">`;
        } else if (field === 'price') {
            inputHTML = `<input type="number" class="form-control edit-field"
                              data-id="${id}"
                              data-field="${field}"
                              value="${value === 'unbekannt' ? '' : value}"
                              style="width:120px;">`;
        } else if (field === 'status') {
            inputHTML = `
                <select class="form-control edit-field" data-id="${id}" data-field="status" style="width:130px;">
                    <option value="confirm" ${value === 'Bestätigt' ? 'selected' : ''}>Bestätigt</option>
                    <option value="inconfirm" ${value === 'Unbestätigt' ? 'selected' : ''}>Unbestätigt</option>
                </select>`;
        }

        $(this).html(inputHTML).find('.edit-field').focus();
    });

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
$(document).ready(function(){
    let currentModal = null;

    // Open modal and fetch documents
    $(document).on("click", ".open_image", function() {
        let customerId = $(this).data("customer");
        let alternativeId = $(this).data("alternative");
        let productId = $(this).data("product");
        let status = $(this).data("status");
        let modalId = "#document" + customerId;

        currentModal = modalId;

        // Show loading message
        $(modalId + " .modal-body").html("<p class='text-center text-muted'>Laden...</p>");

        // Fetch documents from API
        $.ajax({
            url: `/customer/get/document/${customerId}/${alternativeId}/${productId}/${status}`,
            type: "GET",
            success: function(response) {
                console.log("Documents Received:", response.data);

                if (response.data.length === 0) {
                    $(modalId + " .modal-body").html("<p class='text-center text-muted'>Keine Dokumente gefunden.</p>");
                } else {
                    let sortedFiles = categorizeFiles(response.data);
                    renderDocumentList(sortedFiles, modalId);
                }

                $(modalId).modal("show");
            },
            error: function(xhr) {
                console.error("API Error:", xhr.responseText);
                Swal.fire("Fehler", "Dokumente konnten nicht geladen werden!", "error");
            }
        });
    });

    // Sort files by type (Fix PDF Issue)
    function categorizeFiles(files) {
        let sorted = { images: [], pdfs: [], others: [] };

        files.forEach(file => {
            let ext = file.file_type.toLowerCase();
            if (["jpg", "jpeg", "png", "gif", "bmp"].includes(ext)) {
                sorted.images.push(file); // Store only images here
            } else if (ext === "pdf") {
                sorted.pdfs.push(file); // Store PDFs separately
            } else {
                sorted.others.push(file); // Store Word, Excel, etc.
            }
        });

        return sorted;
    }

    // Display documents inside modal
    function renderDocumentList(files, modalId) {
        let content = "<h5>Dokumente:</h5><ul class='list-group'>";

        function fileItemHTML(file) {
            return `<div class="file-item text-center d-flex">
                        <div class="file-info">
                            <span class="file-name editable-name" data-id="${file.id}" data-old-name="${file.image_name}" contenteditable="false">
                                ${file.image_name}
                            </span>
                            <br>
                            <span class="file-stage text-muted">${file.stage}</span>
                        </div>
                        <div class="file-actions">
                            <button class="btn btn-icon btn-flat-warning mr-1 mb-1 waves-effect waves-light delete-file ml-2" data-id="${file.id}"><i class="feather icon-trash"></i></button>
                        </div>
                    </div>`;
        }

        // Images (Only Images)
        if (files.images.length) {
            content += "<li class='list-group-item'><strong>Bilder:</strong><br>";
            files.images.forEach(file => {
                content += `<div class="d-flex align-items-center">
                                <img src="${file.file_path}" class="img-thumbnail open-image" data-src="${file.file_path}" style="max-width: 100px; margin: 5px; cursor: pointer;">
                                ${fileItemHTML(file)}
                            </div>`;
            });
            content += "</li>";
        }

        // PDFs (Properly Open in Modal)
        if (files.pdfs.length) {
            content += "<li class='list-group-item'><strong>PDFs:</strong><br>";
            files.pdfs.forEach(file => {
                content += `<a href="#" class="open-pdf btn btn-danger btn-sm" data-src="${file.file_path}" data-modal="${modalId}"><i class="fa fa-file-pdf-o"></i></a> ${fileItemHTML(file)}`;
            });
            content += "</li>";
        }

        // Other Files (Word, Excel, ZIP - Download Only)
        if (files.others.length) {
            content += "<li class='list-group-item'><strong>Andere Dateien (Download):</strong><br>";
            files.others.forEach(file => {
                content += `<a href="${file.file_path}" class="btn btn-primary btn-sm" download>${file.image_name} (Download)</a> ${fileItemHTML(file)}`;
            });
            content += "</li>";
        }

        content += "</ul>";
        $(modalId + " .modal-body").html(content);
    }

     // Enable Inline Editing on Double Click
    $(document).on("dblclick", ".editable-name", function() {
        $(this).attr("contenteditable", "true").focus();
    });

    // Save Name on Enter Key Press
    $(document).on("keypress", ".editable-name", function(e) {
        if (e.which === 13) { // Enter key
            let newName = $(this).text().trim();
            let fileId = $(this).data("id");
            let oldName = $(this).data("old-name");

            if (newName !== oldName) {
                updateFileName(fileId, newName, $(this));
            }

            $(this).attr("contenteditable", "false");
        }
    });

    function updateFileName(id, newName, element) {
        $.ajax({
            url: "/customer_image_name",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: id,
                image_name: newName
            },
            success: function(response) {
                Swal.fire("Erfolgreich!", "Dateiname wurde geändert!", "success");
                element.data("old-name", newName); // Update old name to prevent duplicate updates
            },
            error: function() {
                Swal.fire("Fehler!", "Dateiname konnte nicht geändert werden!", "error");
            }
        });
    }
    // Open Image Preview Modal
    $(document).on("click", ".open-image", function() {
        let imgSrc = $(this).data("src");
        $("#imagePreview").attr("src", imgSrc);
        $("#imagePreviewModal").modal("show");
    });

    // Open PDF Viewer Modal
    $(document).on("click", ".open-pdf", function() {
        let pdfSrc = $(this).data("src");
        $("#pdfViewer").attr("src", pdfSrc);
        $("#pdfViewerModal").modal("show");
    });

});

 $(document).on("click", ".delete-file", function() {
        let fileId = $(this).data("id");

        Swal.fire({
            title: "Sind Sie sicher?",
            text: "Diese Datei wird dauerhaft gelöscht!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ja, löschen!",
            cancelButtonText: "Abbrechen"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/customer_image_destroy/${fileId}`,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        Swal.fire("Gelöscht!", "Die Datei wurde erfolgreich gelöscht.", "success");
                        location.reload(); // Reload to refresh the list
                    },
                    error: function() {
                        Swal.fire("Fehler!", "Die Datei konnte nicht gelöscht werden.", "error");
                    }
                });
            }
        });
    });

</script>


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

@endsection
