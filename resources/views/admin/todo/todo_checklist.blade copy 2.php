@extends('admin.layouts.app')

@section('title')
    Checklists
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('app-assets/css/pages/app-todo.css') }}">
    <link rel="stylesheet" href="{{ asset('app-assets/css/pages/app-todo.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

    <style>
        .app-content {
            padding: 20px;
        }

        .sidebar-left {
            border-right: 1px solid #ddd;
        }

        .todo-item {
            cursor: pointer;
        }

        .todo-item:hover {
            background-color: #f8f9fa;
        }

        .no-results {
            text-align: center;
            padding: 20px;
            color: #999;
        }

        .select2-selection {
            border: 0px !important;
        }

        .img-flag {
            width: 32px !important;
            height: 32px !important;
            border-radius: 50% !important;
        }

        .hidden {
            display: none;
        }

        .table-hover-animation thead th {
            background-color: #fff0 !important;
        }
        .table-hover-animation tbody tr {
            background-color: #fff0 !important;
        }
        tr {
            border-bottom: 1px solid #d8d6d6 !important;
        }
    </style>
@endsection

@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Checkliste für Produktaufgaben</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Home</a></li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <div class="row match-height">
                    <div class="col-xl-3 col-md-6 col-sm-12">
                        <div class="card collapse-icon accordion-icon-rotate">
                            <div class="card-body">
                                <div class="row match-height">
                                    <div class="col-12">
                                        <div class="form-group row"> 
                                            <div class="col-md-6">
                                                <ul class="list-unstyled mb-0">
                                                    <li class="d-inline-block mr-1">
                                                        <fieldset>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" name="customer_type" id="customer_type1"
                                                                    @if($customer->customer_type=="privat") checked @endif value="privat">
                                                                <label class="custom-control-label" for="customer_type1">privat</label>
                                                            </div>
                                                        </fieldset>
                                                    </li>
                                                    <li class="d-inline-block mr-2">
                                                        <fieldset>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" name="customer_type" id="customer_type2" value="Gewerbe"
                                                                    @if($customer->customer_type=="Gewerbe") checked @endif>
                                                                <label class="custom-control-label" for="customer_type2">Gewerbe</label>
                                                            </div>
                                                        </fieldset>
                                                    </li>
                                                    <li class="d-inline-block mr-2">
                                                        <fieldset>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" name="customer_type" id="customer_type3" value="Kummune"
                                                                    @if($customer->customer_type=="Kummune") checked @endif>
                                                                <label class="custom-control-label" for="customer_type3">Kummune</label>
                                                            </div>
                                                        </fieldset>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Title</span>
                                            </div>
                                            <div class="col-md-8 textbox-container empty">
                                                <input type="text" id="first-name" class="form-control textbox" value="{{ $customer->title }}" name="firma" readonly>
                                                <div class="indicator"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Firma</span>
                                            </div>
                                            <div class="col-md-8 textbox-container empty">
                                                <input type="text" id="first-name" class="form-control textbox" value="{{ $customer->firma }}" name="firma" readonly>
                                                <div class="indicator"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Name</span>
                                            </div>
                                            <div class="col-md-8 textbox-container empty">
                                                <input type="text" class="form-control textbox" value="{{ $customer->name }} {{ $customer->lastname }}" name="lastname" readonly>
                                                <div class="indicator"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Straße / Nr.</span>
                                            </div>
                                            <div class="col-md-8 textbox-container empty">
                                                <input type="text" class="form-control textbox" name="street" value="{{ $customer->street }}" readonly>
                                                <div class="indicator"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>PLZ / Ort</span>
                                            </div>
                                            <div class="col-md-8 textbox-container empty">
                                                <input type="text" class="form-control textbox" value="{{ $customer->postcode }} {{ $customer->city }}" name="postcode" readonly>
                                                <div class="indicator"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Tel</span>
                                            </div>
                                            <div class="col-md-8 textbox-container empty">
                                                <input type="text" id="contact-info" class="form-control textbox" value="{{ $customer->phone }}" name="phone" readonly>
                                                <div class="indicator"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>E-Mail</span>
                                            </div>
                                            <div class="col-md-8 textbox-container empty">
                                                <input type="email" id="contact-info" class="form-control textbox" name="email" value="{{ $customer->email }}" readonly>
                                                <div class="indicator"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9 col-12">
                        <div class="cars">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="accordion" id="accordionExample" data-toggle-hover="true">
                                        @foreach ($articles as $product)
                                            @if (in_array($product->id, $productList))
                                                <div class="collapse-margin" style="box-shadow:none !important;">
                                                    <div class="card-header collapsed" id="heading{{ $product->id }}" data-toggle="collapse" role="button" data-target="#collapse{{ $product->id }}" aria-expanded="false" aria-controls="collapse{{ $product->id }}" style="background: #c4d983;border-radius: 58px;">
                                                        <span class="lead collapse-title collapsed">
                                                            <img src="{{ asset('images/articles/'.$product->image) }}" alt="{{ $product->article_group }}" style="width:75px">
                                                           <i class="feather icon-chevron-down float-right white mr-1 mt-2" style="    font-size: 25px;"></i>
                                                           <strong> <span class="white  ">{{ $product->article_group }}</span></strong>
                                                        </span>
                                                    </div>
                                                    <div id="collapse{{ $product->id }}" class="collapse" aria-labelledby="heading{{ $product->id }}" data-parent="#accordionExample">
                                                        <div class="card-body">
                                                            <div class="cards">
                                                                <div class="card-content">
                                                                    <div class="card-body">
                                                                        <div class="row" id="table-hover-animation">
                                                                            <div class="col-12">
                                                                                <div class="cards">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body"> 
                                                                                            <div class="row">
                                                                                                <div class="col-md-8 col-12">
                                                                                                    <div class="cards"> 
                                                                                                        <div class="card-content">
                                                                                                            <div class="card-body">
                                                                                                                <div class="form-body">
                                                                                                                    <div class="row">
                                                                                                                        <div class="table-responsive">
                                                                                                                            <table class="table table-hover-animation mb-0">
                                                                                                                                <thead>
                                                                                                                                    <tr> 
                                                                                                                                        <th scope="col">#</th>
                                                                                                                                        <th scope="col">PHASE</th>
                                                                                                                                        <th scope="col">Aufgaben Titel</th>
                                                                                                                                        <th scope="col">Aufgaben Beschreibung</th>
                                                                                                                                        <th scope="col">Verfasser</th>
                                                                                                                                        <th scope="col">Status</th>
                                                                                                                                    </tr>
                                                                                                                                </thead>
                                                                                                                                <tbody>
                                                                                                                                    @foreach ($task_phase as $task)
                                                                                                                                        @if ($task->product_id == $product->id && $task->order == 0)
                                                                                                                                            <tr>  
                                                                                                                                              <th>
                                                                                                                                                    @foreach ($tasks as $done) 
                                                                                                                                                        @if($done->phase_id == $task->id && $done->customer_id == request()->id && $task->p_active_id == $done->activities_id)
                                                                                                                                                            <fieldset>
                                                                                                                                                                <div class="custom-control custom-checkbox">
                                                                                                                                                                    <!-- Make the id unique by appending $done->id -->
                                                                                                                                                                    <input type="checkbox" class="custom-control-input" checked name="customCheck" id="done_checkbox_{{ $done->id }}" disabled>
                                                                                                                                                                    <label class="custom-control-label" for="done_checkbox_{{ $done->id }}"></label>
                                                                                                                                                                </div>
                                                                                                                                                            </fieldset> 
                                                                                                                                                        @endif
                                                                                                                                                    @endforeach   
                                                                                                                                                </th>

                                                                                                                                                <th scope="row"> {{ $task->phase_name }}</th>
                                                                                                                                                <td>{{ $task->title }}</td>
                                                                                                                                                <td>{{ $task->description }}</td>
                                                                                                                                                <td>
                                                                                                                                                    @php
                                                                                                                                                    $contact_person = DB::table('employees')
                                                                                                                                                        ->where('id', $customer->contact_person)
                                                                                                                                                        ->select('id', 'name', 'lastname', 'image')
                                                                                                                                                        ->first();
                                                                                                                                                    @endphp
                                                                                                                                                    <div class="photo" style="display: flex; align-items: center;">
                                                                                                                                                        <div class="avatar mr-1">
                                                                                                                                                            <img src="{{ asset('images/employee/'.$contact_person->image) }}" alt="{{ $contact_person->name }}" height="32" width="32">
                                                                                                                                                        </div>
                                                                                                                                                        <label for="avatar" class="mt-0" style="font-size:14px">
                                                                                                                                                            {{ $contact_person->name }} {{ $contact_person->lastname }}
                                                                                                                                                        </label>
                                                                                                                                                    </div>
                                                                                                                                                </td>
                                                                                                                                                <td class="d-flex"> 
                                                                                                                                                    <button type="button" class="btn   waves-effect waves-light" id="save-btn-{{ $task->title }}{{$task->id}}" style=" padding: 1px;">
                                                                                                                                                        <i class="feather icon-check primary"></i>   <small>Speichern</small>
                                                                                                                                                    </button>  
                                                                                                                                              
                                                                                                                                                      <button type="button" class="btn   waves-effect waves-light" id="update-btn-{{ $task->title }}{{$task->id}}" style=" padding: 1px;">
                                                                                                                                                        <i class="feather icon-edit primary"></i>  <small>Bearbiten</small>
                                                                                                                                                    </button>  
                                                                                                                                                   <button type="button" class="btn waves-effect waves-light loadTask" style="padding: 1px;"
                                                                                                                                                            phase-id="{{ $task->id }}" 
                                                                                                                                                            customer-id="{{ request()->id }}" 
                                                                                                                                                            activity-id="{{ $task->p_active_id }}" 
                                                                                                                                                            product-id="{{ $task->product_id }}">
                                                                                                                                                        <i class="feather icon-info info"></i> <small>Zeigen</small>
                                                                                                                                                    </button>

                                                                                                                                                    <!-- Task Details Modal -->
                                                                                                                                                        <div class="modal fade" id="taskModal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
                                                                                                                                                            <div class="modal-dialog modal-lg" role="document">
                                                                                                                                                                <div class="modal-content">
                                                                                                                                                                    <div class="modal-header">
                                                                                                                                                                        <h5 class="modal-title" id="taskModalLabel">Task Details</h5>
                                                                                                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                                                                            <span aria-hidden="true">&times;</span>
                                                                                                                                                                        </button>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="modal-body">
                                                                                                                                                                        <!-- Insert your task-details-card HTML here -->
                                                                                                                                                                         <div class="task-details-card "> 
                                                                                                                                                                            <div class="card-content">
                                                                                                                                                                                <div class="card-body"> 
                                                                                                                                                                                    <div class="form-body">
                                                                                                                                                                                        <div class="row">
                                                                                                                                                                                            <div class="col-12">
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                                        <span>Status</span>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                                        <div class="position-relative has-icon-left">
                                                                                                                                                                                                            <input type="text" class="status form-control" name="status" placeholder="Status" readonly>
                                                                                                                                                                                                            <div class="form-control-position">
                                                                                                                                                                                                                <i class="feather icon-clock"></i>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                            <div class="col-12">
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                                        <span>Date</span>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                                        <div class="position-relative has-icon-left">
                                                                                                                                                                                                            <input type="date"  class="done_date form-control" name="done_date" placeholder="Datum" readonly>
                                                                                                                                                                                                            <div class="form-control-position">
                                                                                                                                                                                                                <i class="feather icon-lock"></i>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                            <div class="col-12">
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                                        <span>Verfasser</span>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                                        <div class="position-relative has-icon-left">
                                                                                                                                                                                                            <div class="photo" style="display: flex; align-items: center;">
                                                                                                                                                                                                                <div class="avatar mr-1">
                                                                                                                                                                                                                    <img class="contact_person_image" src="" alt="" height="32" width="32">
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                <label id="contact_person_name" for="avatar" class="contact_person_name mt-0" style="font-size:14px"></label> 
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                            <div class="col-12">
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                                        <span>Transfer</span>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                                        <div class="position-relative has-icon-left">
                                                                                                                                                                                                            <div class="photo" style="display: flex; align-items: center;">
                                                                                                                                                                                                                <div class="avatar mr-1">
                                                                                                                                                                                                                    <img class="r_image" src="" alt="" height="32" width="32">
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                <label   for="avatar" class="r_name mt-0" style="font-size:14px"></label>
                                                                                                                                                                                                                <input type="hidden" name="responsible_person" id="responsible_person_id" class="form-control">
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                            <div class="col-12">
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                                        <span>Außendienst</span>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                                        <div class="position-relative has-icon-left">
                                                                                                                                                                                                            <div class="photo" style="display: flex; align-items: center;">
                                                                                                                                                                                                                <div class="avatar mr-1">
                                                                                                                                                                                                                    <img class="o_image" src="" alt="" height="32" width="32">
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                <label   for="avatar" class="o_name mt-0" style="font-size:14px"></label>
                                                                                                                                                                                                                <input type="hidden" name="outside_person" id="outside_person_id" class="form-control outside_person">
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div> 
                                                                                                                                                                                            <div class="col-6">
                                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                                    <label for="document_name">Dokument Name</label>
                                                                                                                                                                                                    <input type="text"  class="document_name form-control" name="document_name" placeholder="Dokument Name" readonly>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                            <div class="col-6">
                                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                                    <label for="document_sum">Dokument Summe</label>
                                                                                                                                                                                                    <input type="number"  class="document_sum form-control" name="document_sum" placeholder="Dokument Summe" readonly>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                            <div class="col-12">
                                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                                    <label for="note">Notiz</label>
                                                                                                                                                                                                    <textarea class="note form-control" name="note"   readonly></textarea>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div> 

                                                                                                                                                                                        <div class="col-12">
                                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                                    <input type="text" id="delete_id" placeholder="Enter ID to delete">
                                                                                                                                                                                                    <button type="button" class="btn btn-outline-danger mr-1 mb-1 waves-effect waves-light" id="openDeleteModal"><i class="feather icon-trash"></i> Löschen</button>
                                                                                                                                                                                                    
                                                                                                                                                                                                    <div class="modal fade text-left" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" style="display: none;">
                                                                                                                                                                                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                                                                                                                                            <div class="modal-content">
                                                                                                                                                                                                                <div class="modal-header bg-danger white">
                                                                                                                                                                                                                    <h5 class="modal-title" id="myModalLabel120">Delete Record</h5>
                                                                                                                                                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                                                                                                                        <span aria-hidden="true">×</span>
                                                                                                                                                                                                                    </button>
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                <div class="modal-body">
                                                                                                                                                                                                                    Do you really want to delete this record?
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                <div class="modal-footer">
                                                                                                                                                                                                                    <form id="deleteForm" method="POST" style="display:inline;">
                                                                                                                                                                                                                        @csrf
                                                                                                                                                                                                                        @method('DELETE')
                                                                                                                                                                                                                        <button type="submit" class="btn btn-danger waves-effect waves-light">Ja</button>
                                                                                                                                                                                                                    </form>
                                                                                                                                                                                                                    <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">Nein</button>
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>

                                                                                                                                                                                        </div>
                                                                                                                                                                                    </div> 
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="modal-footer">
                                                                                                                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                                                                                                        <button type="button" class="btn btn-outline-danger" id="openDeleteModal">
                                                                                                                                                                            <i class="feather icon-trash"></i> Löschen
                                                                                                                                                                        </button>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                        </div>

                                                                                                                                                        <!-- Delete Confirmation Modal -->
                                                                                                                                                            <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                                                                                                                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                                                                                                    <div class="modal-content">
                                                                                                                                                                        <div class="modal-header bg-danger white">
                                                                                                                                                                            <h5 class="modal-title" id="deleteModalLabel">Delete Record</h5>
                                                                                                                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                                                                                <span aria-hidden="true">×</span>
                                                                                                                                                                            </button>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="modal-body">
                                                                                                                                                                            Do you really want to delete this record?
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="modal-footer">
                                                                                                                                                                            <form id="deleteForm" method="POST" style="display:inline;">
                                                                                                                                                                                @csrf
                                                                                                                                                                                @method('DELETE')
                                                                                                                                                                                <button type="submit" class="btn btn-danger">Ja</button>
                                                                                                                                                                            </form>
                                                                                                                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Nein</button>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>

                                                                                                                                                </td>


                                                                                                                                            </tr>  
                                                                                                                                            <!-- Save Dialog -->
                                                                                                                                            <tr style="display:none" id="saveDialog{{ $task->title }}{{$task->id}}">
                                                                                                                                                <td colspan="12">
                                                                                                                                                    <div class="cards" style="height:auto"> 
                                                                                                                                                        <div class="card-header" style=" background: #cfe09b; text-align-last: center;">
                                                                                                                                                            <i class="feather icon-save float-left"></i> <h5 class="card-title mb-0">Aufgabe speichern:{{ $task->title }}</h5>
                                                                                                                                                        </div>
                                                                                                                                                        <div class="card-content">
                                                                                                                                                            <div class="card-body">
                                                                                                                                                                <form  method="post" action="{{ action('App\Http\Controllers\TaskToDoController@store') }}" enctype="multipart/form-data">
                                                                                                                                                                    @csrf
                                                                                                                                                                    <div class="form-body">
                                                                                                                                                                        <div class="row">
                                                                                                                                                                            <div class="col-12">
                                                                                                                                                                                <input type="hidden" name="activities_id" value="{{ $task->p_active_id }}">
                                                                                                                                                                                <input type="hidden" name="product_id" value="{{ $task->product_id }}">
                                                                                                                                                                                <input type="hidden" name="phase_id" value="{{ $task->id }}">
                                                                                                                                                                                <input type="hidden" name="customer_id" value="{{ request()->id }}">
                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                        <span>Done</span>
                                                                                                                                                                                    </div>
                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                        <div class="position-relative has-icon-left">
                                                                                                                                                                                            <fieldset>
                                                                                                                                                                                                <div class="vs-checkbox-con vs-checkbox-success">
                                                                                                                                                                                                    <input type="checkbox" value="1" name="done" id="done" required>
                                                                                                                                                                                                    <span class="vs-checkbox">
                                                                                                                                                                                                        <span class="vs-checkbox--check">
                                                                                                                                                                                                            <i class="vs-icon feather icon-check"></i>
                                                                                                                                                                                                        </span>
                                                                                                                                                                                                    </span>
                                                                                                                                                                                                    <code id="task_label">Aufgabe erledigt?</code> 
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </fieldset> 
                                                                                                                                                                                        </div>
                                                                                                                                                                                    </div>
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div class="col-12">
                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                        <span>Date</span>
                                                                                                                                                                                    </div>
                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                        <div class="position-relative has-icon-left">
                                                                                                                                                                                            <input type="date" class="form-control" name="done_date" placeholder="Datum">
                                                                                                                                                                                            <div class="form-control-position">
                                                                                                                                                                                                <i class="feather icon-calendar"></i>
                                                                                                                                                                                            </div>
                                                                                                                                                                                        </div>
                                                                                                                                                                                    </div>
                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                        <div class="position-relative has-icon-left">
                                                                                                                                                                                            <fieldset>
                                                                                                                                                                                                <div class="vs-checkbox-con vs-checkbox-success">
                                                                                                                                                                                                    <input type="checkbox" value="1" name="calendar">
                                                                                                                                                                                                    <span class="vs-checkbox">
                                                                                                                                                                                                        <span class="vs-checkbox--check">
                                                                                                                                                                                                            <i class="vs-icon feather icon-check"></i> 
                                                                                                                                                                                                        </span>
                                                                                                                                                                                            
                                                                                                                                                                                                    </span>
                                                                                                                                                                                                    Zum Kalender hinzufügen  
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </fieldset> 
                                                                                                                                                                                        </div>
                                                                                                                                                                                    </div>
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div class="col-12">
                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                        <span>Verfasser</span>
                                                                                                                                                                                    </div>
                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                        <div class="position-relative has-icon-left">
                                                                                                                                                                                            <div class="photo" style="display: flex; align-items: center;">
                                                                                                                                                                                                <div class="avatar mr-1">
                                                                                                                                                                                                    <img src="{{ asset('images/employee/'.$contact_person->image) }}" alt="{{ $contact_person->name }}" height="32" width="32">
                                                                                                                                                                                                </div>
                                                                                                                                                                                                <label for="avatar" class="mt-0" style="font-size:14px">
                                                                                                                                                                                                    {{ $contact_person->name }} {{ $contact_person->lastname }}
                                                                                                                                                                                                </label>
                                                                                                                                                                                                <input type="hidden" name="contact_person" value="{{ $contact_person->id }}" class="form-control">
                                                                                                                                                                                            </div>
                                                                                                                                                                                        </div>
                                                                                                                                                                                    </div>
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div class="col-12">
                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                        <span>Transfer</span>
                                                                                                                                                                                    </div>
                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                        <div class="position-relative has-icon-left">
                                                                                                                                                                                            <select name="responsible_person" id="responsible" class="form-control">
                                                                                                                                                                                                <option value="" disabled selected>Select Responsible Person</option>
                                                                                                                                                                                                @foreach ($responsibles as $emp)
                                                                                                                                                                                                    <option value="{{ $emp->emp_id }}">{{ $emp->name }} {{ $emp->lastname }}</option>
                                                                                                                                                                                                @endforeach
                                                                                                                                                                                            </select> 
                                                                                                                                                                                        </div>
                                                                                                                                                                                    </div>
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div class="col-12">
                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                        <span>Außendienst</span>
                                                                                                                                                                                    </div>
                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                        <div class="position-relative has-icon-left">
                                                                                                                                                                                            <select name="outside_service" id="outside" class="form-control">
                                                                                                                                                                                                <option disabled selected value="">Bitte wählen Sie den Außendienst aus</option>
                                                                                                                                                                                                @foreach ($employees as $emp)
                                                                                                                                                                                                    <option value="{{ $emp->id }}" data-image="{{ asset('images/employee/'.$emp->image)}}">{{ $emp->name }} {{ $emp->lastname }}</option>
                                                                                                                                                                                                @endforeach
                                                                                                                                                                                            </select>
                                                                                                                                                                                        </div>
                                                                                                                                                                                    </div>
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div class="col-12">
                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                        <span>Dokument Name</span>
                                                                                                                                                                                    </div>
                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                        <div class="position-relative has-icon-left">
                                                                                                                                                                                            <input type="text" id="file-icon" class="form-control" name="document_name" value="{{ old('document_name')}}" placeholder="Dukument Name">
                                                                                                                                                                                            <div class="form-control-position">
                                                                                                                                                                                                <i class="feather icon-file"></i>
                                                                                                                                                                                            </div>
                                                                                                                                                                                        </div>
                                                                                                                                                                                    </div>
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div class="col-12">
                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                        <span>Dokument Summe</span>
                                                                                                                                                                                    </div>
                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                        <div class="position-relative has-icon-left">
                                                                                                                                                                                            <input type="text" id="file-icon" class="form-control" name="document_sum" value="{{ old('document_sum')}}" placeholder="Dukument Summe">
                                                                                                                                                                                            <div class="form-control-position">
                                                                                                                                                                                                <i class="feather icon-sum"></i>
                                                                                                                                                                                            </div>
                                                                                                                                                                                        </div>
                                                                                                                                                                                    </div>
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div class="col-12"> 
                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                        <span>Notiz</span>
                                                                                                                                                                                    </div>
                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                        <div class="position-relative has-icon-left"> 
                                                                                                                                                                                            <textarea name="document_note" class="form-control" value="{{ old('document_note')}}" placeholder="Dukument Notiz"></textarea>
                                                                                                                                                                                            <div class="form-control-position">
                                                                                                                                                                                                <i class="feather icon-file"></i>
                                                                                                                                                                                            </div>
                                                                                                                                                                                        </div>
                                                                                                                                                                                    </div>
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div class="col-12"> 
                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                        <span>Status</span>
                                                                                                                                                                                    </div>
                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                        <div class="position-relative has-icon-left"> 
                                                                                                                                                                                            <select name="document_status" id="" class="form-control">
                                                                                                                                                                                                <option value="">Bitte wählen Sie die Dokument Status</option>
                                                                                                                                                                                                <option value="Rechnung">Rechnung</option>
                                                                                                                                                                                                <option value="verschickt">verschickt</option>
                                                                                                                                                                                                <option value="bezahlt">bezahlt</option>
                                                                                                                                                                                                <option value="Warten auf Kunden">Warten auf Kunden</option>
                                                                                                                                                                                            </select>
                                                                                                                                                                                        </div>
                                                                                                                                                                                    </div>
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div class="col-12"> 
                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                        <span>PDF</span>
                                                                                                                                                                                    </div>
                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                        <div class="position-relative has-icon-left">  
                                                                                                                                                                                            <input type="file" name="document" class="form-control"> 
                                                                                                                                                                                        </div>
                                                                                                                                                                                    </div>
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div class="col-md-8 offset-md-4">
                                                                                                                                                                                <button type="submit" class="btn btn-primary mr-1 mb-1 waves-effect waves-light">Speichern</button> 
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </form>
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                </td>
                                                                                                                                            </tr>
                                                                                                                                            <!-- update Dialog --> 
                                                                                                                                            <tr style="display:none" id="updateDialog{{ $task->title }}{{$task->id}}">          
                                                                                                                                                <td colspan="12">
                                                                                                                                                    <div class="cards" style="height:auto"> 
                                                                                                                                                        <div class="card-header" style=" background: #cfe09b; text-align-last: center;">
                                                                                                                                                           <i class="feather icon-edit float-left"></i> <h5 class="card-title mb-0">Aufgabe Bearbiten: {{ $task->title }}</h5>
                                                                                                                                                        </div>
                                                                                                                                                        <div class="card-content">
                                                                                                                                                            <div class="card-body">
                                                                                                                                                       
                                                                                                                                                                     @foreach ($tasks as $update ) 
                                                                                                                                                                        @if($update->phase_id == $task->id && $update->activities_id == $task->p_active_id )
                                                                                                                                                                             @if(!$update)
                                                                                                                                                                                <div class="alert alert-warning" role="alert">
                                                                                                                                                                                    <h4 class="alert-heading">Info</h4>
                                                                                                                                                                                    <p class="mb-0">
                                                                                                                                                                                        Es ist noch nichts zum Bearbeiten gespeichert!
                                                                                                                                                                                    </p>
                                                                                                                                                                                </div> 
                                                                                                                                                                            @else
                                                                                                                                                                           <form method="post" action="{{ route('task.update', ['id' => $update->id]) }}" enctype="multipart/form-data">
                                                                                                                                                                                @csrf
                                                                                                                                                                                @method('PUT')
                                                                                                                                                                                    <div class="form-body">
                                                                                                                                                                                        <div class="row">
                                                                                                                                                                                            <div class="col-12">
                                                                                                                                                                                                <input type="hidden" name="activities_id" value="{{ $task->p_active_id }}">
                                                                                                                                                                                                <input type="hidden" name="product_id" value="{{ $task->product_id }}">
                                                                                                                                                                                                <input type="hidden" name="phase_id" value="{{ $task->id }}">
                                                                                                                                                                                                <input type="hidden" name="customer_id" value="{{ request()->id }}">
                                                                                                                                                                                                <input type="hidden" name="id" value="{{ $update->id }}">
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                                        <span>Done</span>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                                        <div class="position-relative has-icon-left">
                                                                                                                                                                                                            <fieldset>
                                                                                                                                                                                                                <div class="vs-checkbox-con vs-checkbox-success">
                                                                                                                                                                                                                    <input type="checkbox" value="1" name="done" id="done" @if($update->done=="true") checked @endif>
                                                                                                                                                                                                                    <span class="vs-checkbox">
                                                                                                                                                                                                                        <span class="vs-checkbox--check">
                                                                                                                                                                                                                            <i class="vs-icon feather icon-check"></i>
                                                                                                                                                                                                                        </span>
                                                                                                                                                                                                                    </span>
                                                                                                                                                                                                                    <code id="task_label">Aufgabe erledigt?</code> 
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                            </fieldset> 
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                            <div class="col-12">
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                                        <span>Date</span>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                                        <div class="position-relative has-icon-left">
                                                                                                                                                                                                            <input type="date" class="form-control" name="done_date" placeholder="Datum" value="{{ $update->done_date }}">
                                                                                                                                                                                                            <div class="form-control-position">
                                                                                                                                                                                                                <i class="feather icon-calendar"></i>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                                        <div class="position-relative has-icon-left">
                                                                                                                                                                                                            <fieldset>
                                                                                                                                                                                                                <div class="vs-checkbox-con vs-checkbox-success">
                                                                                                                                                                                                                    <input type="checkbox" value="1" name="calendar" @if($update->done=="true") checked @endif>
                                                                                                                                                                                                                    <span class="vs-checkbox">
                                                                                                                                                                                                                        <span class="vs-checkbox--check">
                                                                                                                                                                                                                            <i class="vs-icon feather icon-check"></i> 
                                                                                                                                                                                                                        </span>
                                                                                                                                                                                                            
                                                                                                                                                                                                                    </span>
                                                                                                                                                                                                                    Zum Kalender hinzufügen  
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                            </fieldset> 
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                            <div class="col-12">
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                                        <span>Verfasser</span>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                                        <div class="position-relative has-icon-left">
                                                                                                                                                                                                            <div class="photo" style="display: flex; align-items: center;">
                                                                                                                                                                                                                <div class="avatar mr-1">
                                                                                                                                                                                                                    <img src="{{ asset('images/employee/'.$contact_person->image) }}" alt="{{ $contact_person->name }}" height="32" width="32">
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                <label for="avatar" class="mt-0" style="font-size:14px">
                                                                                                                                                                                                                    {{ $contact_person->name }} {{ $contact_person->lastname }}
                                                                                                                                                                                                                </label>
                                                                                                                                                                                                                <input type="hidden" name="contact_person" value="{{ $contact_person->id }}" class="form-control">
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                            <div class="col-12">
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                                        <span>Transfer</span>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                                        <div class="position-relative has-icon-left">
                                                                                                                                                                                                            <select name="responsible_person" id="responsible" class="form-control responsible"> 
                                                                                                                                                                                                                @foreach ($responsibles as $emp)
                                                                                                                                                                                                                    <option value="{{ $emp->emp_id }}" @if($update->cid == $emp->emp_id) selected @endif>{{ $emp->name }} {{ $emp->lastname }}</option>
                                                                                                                                                                                                                @endforeach
                                                                                                                                                                                                            </select> 
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                            <div class="col-12">
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                                        <span>Außendienst</span>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                                        <div class="position-relative has-icon-left">
                                                                                                                                                                                                            <select name="outside_service" id="outside" class="form-control outside"> 
                                                                                                                                                                                                                @foreach ($employees as $emp)
                                                                                                                                                                                                                    <option value="{{ $emp->id }}" data-image="{{ asset('images/employee/'.$emp->image)}}"  @if($update->rid == $emp->id) selected @endif>{{ $emp->name }} {{ $emp->lastname }}</option>
                                                                                                                                                                                                                @endforeach
                                                                                                                                                                                                            </select>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                            <div class="col-12">
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                                        <span>Dokument Name</span>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                                        <div class="position-relative has-icon-left">
                                                                                                                                                                                                            <input type="text" id="file-icon" class="form-control" name="document_name" value="{{ $update->document_name}}" placeholder="Dukument Name">
                                                                                                                                                                                                            <div class="form-control-position">
                                                                                                                                                                                                                <i class="feather icon-file"></i>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                            <div class="col-12">
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                                        <span>Dokument Summe</span>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                                        <div class="position-relative has-icon-left">
                                                                                                                                                                                                            <input type="text" id="file-icon" class="form-control" name="document_sum" value="{{ $update->document_sum }}" placeholder="Dukument Summe">
                                                                                                                                                                                                            <div class="form-control-position">
                                                                                                                                                                                                                <i class="feather icon-sum"></i>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                            <div class="col-12"> 
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                                        <span>Notiz</span>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                                        <div class="position-relative has-icon-left"> 
                                                                                                                                                                                                            <textarea name="document_note" class="form-control" placeholder="Dukument Notiz">{{ $update->note }}</textarea>
                                                                                                                                                                                                            <div class="form-control-position">
                                                                                                                                                                                                                <i class="feather icon-file"></i>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                            <div class="col-12"> 
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                                        <span>Status</span>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                                        <div class="position-relative has-icon-left"> 
                                                                                                                                                                                                            <select name="document_status" id="" class="form-control">
                                                                                                                                                                                                                <option value="{{$update->status }}"> -> {{$update->status}}</option>
                                                                                                                                                                                                                <option value="Rechnung">Rechnung</option>
                                                                                                                                                                                                                <option value="verschickt">verschickt</option>
                                                                                                                                                                                                                <option value="bezahlt">bezahlt</option>
                                                                                                                                                                                                                <option value="Warten auf Kunden">Warten auf Kunden</option>
                                                                                                                                                                                                            </select>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                            <div class="col-12"> 
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                                                                        <span>PDF</span>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                                                                        <div class="position-relative has-icon-left">  
                                                                                                                                                                                                            <input type="file" name="document" class="form-control"> 
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                            <div class="col-md-8 offset-md-4"> 
                                                                                                                                                                                                <button type="submit" class="btn btn-primary mr-1 mb-1 waves-effect waves-light">Speichern</button>  
                                                                                                                                                                                            </div>
                                                                                                                                                                                        </div>
                                                                                                                                                                                    </div>
                                                                                                                                                                             </form> 
                                                                                                                                                                            @endif  
                                                                                                                                                                         @endif  
                                                                                                                                                                    @endforeach 

                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                </td>
                                                                                                                                            </tr>
                                                                                                                                        @endif
                                                                                                                                    @endforeach
                                                                                                                                </tbody>
                                                                                                                            </table>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div> 
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                               <div class="col-md-4 col-12"> 
                                                                                                    <div class="task-details-card " style="height: 501.438px; display:none; "> 
                                                                                                        <div class="card-content">
                                                                                                            <div class="card-body"> 
                                                                                                                <div class="form-body">
                                                                                                                    <div class="row">
                                                                                                                        <div class="col-12">
                                                                                                                            <div class="form-group row">
                                                                                                                                <div class="col-md-4">
                                                                                                                                    <span>Status</span>
                                                                                                                                </div>
                                                                                                                                <div class="col-md-8">
                                                                                                                                    <div class="position-relative has-icon-left">
                                                                                                                                        <input type="text" class="status form-control" name="status" placeholder="Status" readonly>
                                                                                                                                        <div class="form-control-position">
                                                                                                                                            <i class="feather icon-clock"></i>
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-12">
                                                                                                                            <div class="form-group row">
                                                                                                                                <div class="col-md-4">
                                                                                                                                    <span>Date</span>
                                                                                                                                </div>
                                                                                                                                <div class="col-md-8">
                                                                                                                                    <div class="position-relative has-icon-left">
                                                                                                                                        <input type="date"  class="done_date form-control" name="done_date" placeholder="Datum" readonly>
                                                                                                                                        <div class="form-control-position">
                                                                                                                                            <i class="feather icon-lock"></i>
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-12">
                                                                                                                            <div class="form-group row">
                                                                                                                                <div class="col-md-4">
                                                                                                                                    <span>Verfasser</span>
                                                                                                                                </div>
                                                                                                                                <div class="col-md-8">
                                                                                                                                    <div class="position-relative has-icon-left">
                                                                                                                                        <div class="photo" style="display: flex; align-items: center;">
                                                                                                                                            <div class="avatar mr-1">
                                                                                                                                                <img class="contact_person_image" src="" alt="" height="32" width="32">
                                                                                                                                            </div>
                                                                                                                                            <label id="contact_person_name" for="avatar" class="contact_person_name mt-0" style="font-size:14px"></label> 
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-12">
                                                                                                                            <div class="form-group row">
                                                                                                                                <div class="col-md-4">
                                                                                                                                    <span>Transfer</span>
                                                                                                                                </div>
                                                                                                                                <div class="col-md-8">
                                                                                                                                    <div class="position-relative has-icon-left">
                                                                                                                                        <div class="photo" style="display: flex; align-items: center;">
                                                                                                                                            <div class="avatar mr-1">
                                                                                                                                                <img class="r_image" src="" alt="" height="32" width="32">
                                                                                                                                            </div>
                                                                                                                                            <label   for="avatar" class="r_name mt-0" style="font-size:14px"></label>
                                                                                                                                            <input type="hidden" name="responsible_person" id="responsible_person_id" class="form-control">
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-12">
                                                                                                                            <div class="form-group row">
                                                                                                                                <div class="col-md-4">
                                                                                                                                    <span>Außendienst</span>
                                                                                                                                </div>
                                                                                                                                <div class="col-md-8">
                                                                                                                                    <div class="position-relative has-icon-left">
                                                                                                                                        <div class="photo" style="display: flex; align-items: center;">
                                                                                                                                            <div class="avatar mr-1">
                                                                                                                                                <img class="o_image" src="" alt="" height="32" width="32">
                                                                                                                                            </div>
                                                                                                                                            <label   for="avatar" class="o_name mt-0" style="font-size:14px"></label>
                                                                                                                                            <input type="hidden" name="outside_person" id="outside_person_id" class="form-control outside_person">
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div> 
                                                                                                                        <div class="col-6">
                                                                                                                            <div class="form-group">
                                                                                                                                <label for="document_name">Dokument Name</label>
                                                                                                                                <input type="text"  class="document_name form-control" name="document_name" placeholder="Dokument Name" readonly>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-6">
                                                                                                                            <div class="form-group">
                                                                                                                                <label for="document_sum">Dokument Summe</label>
                                                                                                                                <input type="number"  class="document_sum form-control" name="document_sum" placeholder="Dokument Summe" readonly>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-12">
                                                                                                                            <div class="form-group">
                                                                                                                                <label for="note">Notiz</label>
                                                                                                                                <textarea class="note form-control" name="note"   readonly></textarea>
                                                                                                                            </div>
                                                                                                                        </div> 

                                                                                                                       <div class="col-12">
                                                                                                                            <div class="form-group">
                                                                                                                                <input type="text" id="delete_id" placeholder="Enter ID to delete">
                                                                                                                                <button type="button" class="btn btn-outline-danger mr-1 mb-1 waves-effect waves-light" id="openDeleteModal"><i class="feather icon-trash"></i> Löschen</button>
                                                                                                                                
                                                                                                                                <div class="modal fade text-left" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" style="display: none;">
                                                                                                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                                                                        <div class="modal-content">
                                                                                                                                            <div class="modal-header bg-danger white">
                                                                                                                                                <h5 class="modal-title" id="myModalLabel120">Delete Record</h5>
                                                                                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                                                    <span aria-hidden="true">×</span>
                                                                                                                                                </button>
                                                                                                                                            </div>
                                                                                                                                            <div class="modal-body">
                                                                                                                                                Do you really want to delete this record?
                                                                                                                                            </div>
                                                                                                                                            <div class="modal-footer">
                                                                                                                                                <form id="deleteForm" method="POST" style="display:inline;">
                                                                                                                                                    @csrf
                                                                                                                                                    @method('DELETE')
                                                                                                                                                    <button type="submit" class="btn btn-danger waves-effect waves-light">Ja</button>
                                                                                                                                                </form>
                                                                                                                                                <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">Nein</button>
                                                                                                                                            </div>
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>

                                                                                                                    </div>
                                                                                                                </div> 
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>     
                                                                                            </div>
                                                                                        </div>
                                                                                    </div> 
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer" style="    background: transparent;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="float-left"> 
                                            <div class="alert alert-warning mb-2" role="alert">
                                           <i class="feather icon-info"></i> <strong>Achtung!</strong> Bitte markieren Sie die Aufgaben, bevor Sie zur nächsten Phase übergehen
                                        </div>
                                        </div>
                                        <div class="float-right">
                                            <button type="button" class="btn btn-outline-primary round mr-1 mb-1 waves-effect waves-light"><i class="feather icon-arrow-right" data-toggle="modal" data-target="#small"></i> Nächste</button> 
                                                    </button>
                                            <div class="modal fade text-left" id="small" tabindex="-1" role="dialog" aria-labelledby="myModalLabel19" aria-hidden="true" style="display: none;">
                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="myModalLabel19">Small Modal</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Haben Sie die Aufgaben markiert?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a type="button" class="btn btn-primary waves-effect waves-light"  >Ja</a>
                                                            <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal">Nein</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script>
    <script>
       $(document).ready(function() {
            // Display validation errors and messages
            @if($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.error("{{ $error }}");
                @endforeach
            @endif
            @if(Session::has('update_msg'))
                toastr.success("{{ session('update_msg') }}");
            @endif
            @if(Session::has('save_msg'))
                toastr.success("{{ session('save_msg') }}");
            @endif
            @if(Session::has('delete_msg'))
                toastr.error("{{ session('delete_msg') }}");
            @endif

            // Initialize Select2
            function initializeSelect2() {
                $('select').select2({
                    width: '100%'
                });
            }

            // Call initializeSelect2 initially to apply to all selects
            initializeSelect2();

            // Handle the display of the Save dialog
            document.querySelectorAll('button[id^="save-btn-"]').forEach(function(button) {
                button.addEventListener('click', function() {
                    const taskId = this.id.split('save-btn-')[1];
                    const dialogRow = document.getElementById(`saveDialog${taskId}`);
                    if (dialogRow) {
                        dialogRow.style.display = dialogRow.style.display === 'none' ? 'table-row' : 'none';
                        initializeSelect2(); // Re-initialize Select2 for newly shown elements
                    }
                });
            });

            // Handle the display of the Update dialog
            document.querySelectorAll('button[id^="update-btn-"]').forEach(function(button) {
                button.addEventListener('click', function() {
                    const taskId = this.id.split('update-btn-')[1];
                    const UpdateRow = document.getElementById(`updateDialog${taskId}`);
                    if (UpdateRow) {
                        UpdateRow.style.display = UpdateRow.style.display === 'none' ? 'table-row' : 'none';
                        initializeSelect2(); // Re-initialize Select2 for newly shown elements
                    }
                });
            });

            // Listen for dynamic content to be displayed and re-initialize Select2
            $('tr[id^="saveDialog"], tr[id^="updateDialog"]').on('show', function() {
                initializeSelect2();
            });
        });

        
    </script>


        <script>
     $(document).ready(function() {
    // Event listener for the loadTask button
    $(document).on('click', '.loadTask', function() {
        console.log("LoadTask button clicked.");

        var phase_id = $(this).attr('phase-id');
        var customer_id = $(this).attr('customer-id');
        var product_id = $(this).attr('product-id');
        var activity_id = $(this).attr('activity-id');

        console.log("Phase ID:", phase_id, "Customer ID:", customer_id, "Product ID:", product_id, "Activity ID:", activity_id);

        $.ajax({
            url: '/todo_check_load/' + phase_id + '/' + customer_id + '/' + product_id + '/' + activity_id,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log("AJAX success, response received:", response);
                if (response && Object.keys(response).length > 0) {
                    // Populate the modal with the response data
                    $('#taskModal').find('.status').val(response.status);
                    $('#taskModal').find('.done_date').val(response.done_date);
                    $('#taskModal').find('.contact_person_image').attr('src', '/images/employee/' + response.contact_image);
                    $('#taskModal').find('.contact_person_name').text(response.contact_name + ' ' + response.contact_lastname);
                    $('#taskModal').find('.r_image').attr('src', '/images/employee/' + response.rimage);
                    $('#taskModal').find('.r_name').text(response.rname + ' ' + response.rlastname); 
                    $('#taskModal').find('.o_image').attr('src', '/images/employee/' + response.oimage);
                    $('#taskModal').find('.o_name').text(response.oname + ' ' + response.olastname); 
                    $('#taskModal').find('.document_name').val(response.document_name);
                    $('#taskModal').find('.document_sum').val(response.document_sum);
                    $('#taskModal').find('.note').val(response.note);
                    $('#taskModal').find('.delete_id').val(response.id);

                    console.log("Populating modal and showing it.");
                    $('#taskModal').modal('show'); // Show the modal
                } else {
                    console.log("No data found for this task.");
                    toastr.warning('Für die ausgewählten Kriterien wurden keine Aufgabendaten gefunden', 'Keine Daten gefunden');
                }
            },
            error: function(error) {
                console.error("There was an error loading the task data: ", error);
                toastr.error('There was an error loading the task data.', 'Error');
            }
        });
    });

    // Event listener for the delete button inside the modal
    $(document).on('click', '#openDeleteModal', function() {
        var deleteId = $('#taskModal').find('.delete_id').val();

        if (deleteId) {
            console.log("Delete ID found:", deleteId);
            var deleteUrl = '/task-delete/' + deleteId;
            $('#deleteForm').attr('action', deleteUrl);
            $('#deleteModal').modal('show');
        } else {
            console.log("No delete ID provided.");
            toastr.warning('Please enter an ID to delete the record.', 'No ID Provided');
        }
    });
});

        </script>

        <script>
             $(document).on('click', '#openDeleteModal', function() {
        var $container = $(this).closest('.task-container');
        var deleteId = $container.find('.delete_id').val();

        if (deleteId) {
            console.log("Delete ID found:", deleteId);
            var deleteUrl = '/task-delete/' + deleteId;
            $('#deleteForm').attr('action', deleteUrl);
            $('#deleteModal').modal('show');
        } else {
            console.log("No delete ID provided.");
            toastr.warning('Please enter an ID to delete the record.', 'No ID Provided');
        }
    });
        </script>


 <script>
    $(document).ready(function() {
    // Attach click event to the update button
    $('[id^=update-btn-]').on('click', function() {
        // Extract IDs from the button's ID
        let btnId = $(this).attr('id');
        let taskTitle = btnId.split('-')[2];
        let taskId = btnId.split('-')[3];

        // Construct the dialog ID
        let dialogId = `#updateDialog${taskTitle}${taskId}`;
        
        // Replace these variables with the actual phase_id, customer_id, product_id, and activity_id
        let phaseId = $(this).data('phase-id'); // Add data attributes to the button in Blade
        let customerId = $(this).data('customer-id'); // Add data attributes to the button in Blade
        let productId = $(this).data('product-id'); // Add data attributes to the button in Blade
        let activityId = $(this).data('activity-id'); // Add data attributes to the button in Blade

        // Make an AJAX request to load the task data
        $.ajax({
            url: `/todo_check_loads/${phaseId}/${customerId}/${productId}/${activityId}`,
            method: 'GET',
            success: function(data) {
                // Populate the form fields with the fetched data
                $(dialogId).find('input[name="done"]').prop('checked', data.done === 'true');
                $(dialogId).find('input[name="done_date"]').val(data.done_date);
                $(dialogId).find('input[name="calendar"]').prop('checked', data.calendar === 'true');
                $(dialogId).find('input[name="document_name"]').val(data.document_name);
                $(dialogId).find('input[name="document_sum"]').val(data.document_sum);
                $(dialogId).find('textarea[name="document_note"]').val(data.note);
                $(dialogId).find('select[name="document_status"]').val(data.status);
                $(dialogId).find('select[name="responsible_person"]').val(data.responsible_person);
                $(dialogId).find('select[name="outside_service"]').val(data.outside_service);

                // Update contact person details
                let contactAvatar = $(dialogId).find('.avatar img');
                contactAvatar.attr('src', `/images/employee/${data.contact_image}`);
                $(dialogId).find('label[for="avatar"]').text(`${data.contact_name} ${data.contact_lastname}`);
                $(dialogId).find('input[name="contact_person"]').val(data.contact_person);

                // Show the dialog
                $(dialogId).show();
            },
            error: function(error) {
                console.log('Error loading task data:', error);
            }
        });
    });
});

 </script>

 
 
@endsection
