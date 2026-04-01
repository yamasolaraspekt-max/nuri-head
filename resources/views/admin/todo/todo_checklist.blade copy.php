@extends('admin.layouts.app')

@section('title') Checklists @endsection

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
</style>
<style>
    .img-flag {
        width: 32px !important;
        height: 32px !important;
        border-radius: 50% !important;
    }

    .hidden {
        display: none;
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
                <h2 class="content-header-title float-left mb-0">TO DO</h2>
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
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled mb-0">
                                                <li class="d-inline-block mr-1">
                                                    <fieldset>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" class="custom-control-input" name="customer_type" id="customer_type1"
                                                                @if($customer->customer_type=="privat") checked enabled @else disabled @endif value="privat">
                                                            <label class="custom-control-label" for="customer_type1">privat</label>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                                <li class="d-inline-block mr-2">
                                                    <fieldset>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" class="custom-control-input" name="customer_type" id="customer_type2" value="Gewerbe"
                                                                @if($customer->customer_type=="Gewerbe") checked enabled @else disabled @endif>
                                                            <label class="custom-control-label" for="customer_type2">Gewerbe</label>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                                <li class="d-inline-block mr-2">
                                                    <fieldset>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" class="custom-control-input" name="customer_type" id="customer_type3" value="Kummune"
                                                                @if($customer->customer_type=="Kummune") checked enabled @else disabled @endif>
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
                    <div class="cardS">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="accordion" id="accordionExample" data-toggle-hover="true">
                                    @foreach ($articles as $product)
                                        @if (in_array($product->id, $productList))
                                            <div class="collapse-margin">
                                                <div class="card-header collapsed" id="heading{{ $product->id }}" data-toggle="collapse" role="button" data-target="#collapse{{ $product->id }}" aria-expanded="false" aria-controls="collapse{{ $product->id }}">
                                                    <span class="lead collapse-title collapsed">
                                                        <img src="{{ asset('images/articles/'.$product->image) }}" alt="{{ $product->article_group }}" style="width:75px">
                                                        {{ $product->article_group }}
                                                    </span>
                                                </div>
                                                <div id="collapse{{ $product->id }}" class="collapse" aria-labelledby="heading{{ $product->id }}" data-parent="#accordionExample">
                                                    <div class="card-body">
                                                        <div class="cardS">
                                                            <div class="card-content">
                                                                <div class="card-body">
                                                                    <div class="row" id="table-hover-animation">
                                                                        <div class="col-12">
                                                                            <div class="cardS">
                                                                                <div class="card-content">
                                                                                    <div class="card-body">
                                                                                        <div class="table-responsive">
                                                                                            <table class="table table-hover-animation mb-0">
                                                                                                <thead>
                                                                                                    <tr> 
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
                                                                                                                <th scope="row">{{ $task->phase_name }}</th>
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
                                                                                                                 

                                                                                                                      
                                                                                                                            <button type="button" class="btn btn-outline-primary mr-1 mb-1 waves-effect waves-light" id="show{{ $task->id }}">
                                                                                                                                <i class="feather icon-save"></i> Speichern
                                                                                                                            </button>
                                                                                                                      
                                                                                                                        @if(!$tasks->isEmpty())  
                                                                                                                        
                                                                                                                                <button type="button" class="btn btn-outline-primary mr-1 mb-1 waves-effect waves-light" id="info{{ $task->id }}">
                                                                                                                                    <i class="feather icon-info"></i> Info
                                                                                                                                </button> 
                                                                                                                        @endif
                                                                                                               
  
                                                                                                                </td>
                                                                                                            </tr>

                                                                                                            <div id="infoTable{{$task->id}}" style="display:none" >
                                                                                                                <td colspan="12">
                                                                                                                    <div class="cards">
                                                                                                                        <div class="card-body">
                                                                                                                            <div class="row">
                                                                                                                                <div class="table-responsive">
                                                                                                                                    <table class="table table-hover-animation mb-0">
                                                                                                                                        <thead>
                                                                                                                                            <tr>
                                                                                                                                                <th scope="col">DONE</th>
                                                                                                                                                <th scope="col">DATE</th>
                                                                                                                                                <th scope="col">Verfasser</th>
                                                                                                                                                <th scope="col">Transfer</th>
                                                                                                                                                <th scope="col">Außendienst</th>
                                                                                                                                                <th scope="col">Dokument</th>
                                                                                                                                            </tr>
                                                                                                                                        </thead>
                                                                                                                                        <tbody>
                                                                                                                                            @foreach ($tasks as $do) 
                                                                                                                                                @if($do->phase_id == $task->id) 
                                                                                                                                                <tr>
                                                                                                                                                    <td>
                                                                                                                                                        <fieldset>
                                                                                                                                                            <div class="vs-checkbox-con vs-checkbox-success">
                                                                                                                                                                <input type="checkbox" value="1" name="done" @if($do->done=="true") checked disabled @endif>
                                                                                                                                                                <span class="vs-checkbox">
                                                                                                                                                                    <span class="vs-checkbox--check">
                                                                                                                                                                        <i class="vs-icon feather icon-check"></i>
                                                                                                                                                                    </span>
                                                                                                                                                                </span>
                                                                                                                                                            </div>
                                                                                                                                                        </fieldset>
                                                                                                                                                    </td>
                                                                                                                                                    <td>
                                                                                                                                                        <input type="date" name="done_date" class="form-control" value="{{ $do->done_date }}" disabled>
                                                                                                                                                    </td>
                                                                                                                                                    <td>
                                                                                                                                                        <div class="photo" style="display: flex; align-items: center;">
                                                                                                                                                            <div class="avatar mr-1">
                                                                                                                                                                <img src="{{ asset('images/employee/'.$do->cimage) }}" alt="{{ $do->cname }}" height="32" width="32">
                                                                                                                                                            </div>
                                                                                                                                                            <label for="avatar" class="mt-0" style="font-size:14px">
                                                                                                                                                                {{ $do->contact_name }} {{ $do->contact_lastname }}
                                                                                                                                                            </label> 
                                                                                                                                                        </div>
                                                                                                                                                    </td>
                                                                                                                                                    <td>
                                                                                                                                                        <div class="photo" style="display: flex; align-items: center;">
                                                                                                                                                            <div class="avatar mr-1">
                                                                                                                                                                <img src="{{ asset('images/employee/'.$do->rimage) }}" alt="{{ $do->cname }}" height="32" width="32" required>
                                                                                                                                                            </div>
                                                                                                                                                            <label for="avatar" class="mt-0" style="font-size:14px">
                                                                                                                                                                {{ $do->responsible_name }} {{ $do->responsible_lastname }}
                                                                                                                                                            </label> 
                                                                                                                                                        </div>
                                                                                                                                                    </td>
                                                                                                                                                    <td>
                                                                                                                                                        <div class="photo" style="display: flex; align-items: center;">
                                                                                                                                                            <div class="avatar mr-1">
                                                                                                                                                                <img src="{{ asset('images/employee/'.$do->oimage) }}" alt="{{ $do->cname }}" height="32" width="32">
                                                                                                                                                            </div>
                                                                                                                                                            <label for="avatar" class="mt-0" style="font-size:14px">
                                                                                                                                                                {{ $do->outside_name }} {{ $do->outside_lastname }}
                                                                                                                                                            </label> 
                                                                                                                                                        </div>
                                                                                                                                                    </td>
                                                                                                                                                    <td>
                                                                                                                                                        <i class="feather icon-file primary" style="font-size: 20px;"  data-toggle="modal" data-target="#dark{{$do->task_id}}"></i>
                                                                                                                                                         
                                                                                                                                                       
                                                                                                                                                    </td>
                                                                                                                                                </tr>
                                                                                                                                                <tr>
                                                                                                                                                    <td colspan="12">
                                                                                                                                                        <textarea name="note" cols="30" disabled class="form-control">{{$do->note}}</textarea>
                                                                                                                                                    </td>
                                                                                                                                                </tr>
                                                                                                                                                <tr>
                                                                                                                                                    <td colspan="12">
                                                                                                                                                         <div class="modal fade text-left" id="dark{{$do->task_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel150" aria-hidden="true" style="display: none;">
                                                                                                                                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                                                                                                                                                                <div class="modal-content">
                                                                                                                                                                    <div class="modal-header bg-dark white">
                                                                                                                                                                        <h5 class="modal-title" id="myModalLabel150">Dark Modal</h5>
                                                                                                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                                                                            <span aria-hidden="true">×</span>
                                                                                                                                                                        </button>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="modal-body">
                                                                                                                                                                        <div class="table-responsive">
                                                                                                                                                                            <table class="table">
                                                                                                                                                                                <thead>
                                                                                                                                                                                    <tr>
                                                                                                                                                                                        <th>Dokument</th>
                                                                                                                                                                                        <th>Note</th>
                                                                                                                                                                                        <th>Summe</th>
                                                                                                                                                                                        <th>Status</th>
                                                                                                                                                                                    </tr>
                                                                                                                                                                                </thead>
                                                                                                                                                                                <tbody>
                                                                                                                                                                                    @foreach ($document as $doc)
                                                                                                                                                                                        @if($doc->phase_id == $do->phase_id && $doc->activity_id == $do->activity_id)
                                                                                                                                                                                            <tr>
                                                                                                                                                                                                <th scope="row">{{$doc->document_name}}</th>
                                                                                                                                                                                                <td>{{ $doc->document_note }}</td>
                                                                                                                                                                                                <td>{{ $doc->document_sum}}</td>
                                                                                                                                                                                                <td>{{ $doc->document_status}}</td>
                                                                                                                                                                                            </tr>
                                                                                                                                                                                        @endif
                                                                                                                                                                                    @endforeach 
                                                                                                                                                                                </tbody>
                                                                                                                                                                            </table>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>

                                                                                                                                                                    <div class="modal-footer">
                                                                                                                                                                        <button type="button" class="btn btn-dark waves-effect waves-light" data-dismiss="modal">Accept</button>
                                                                                                                                                                    </div>
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
                                                                                                                </td>
                                                                                                            </div>
                                                                                                           

                                                                                                            <div id="done{{ $task->id }}" style="display:none">
                                                                                                                <td colspan="12">
                                                                                                                    <div class="cards">
                                                                                                                        <div class="card-header">
                                                                                                                            <div class="card-title">
                                                                                                                                <h5>Aufgaben Speichern</h5>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                        <form class="taskForm" enctype="multipart/form-data" method="post" action="{{ route('task_to_dos.store') }}">
                                                                                                                            @csrf
                                                                                                                            <div class="card-body">
                                                                                                                                <div class="row">
                                                                                                                                    <input type="text" name="task_id" value="{{ $task->id }}">
                                                                                                                                    <input type="hidden" name="activities_id" value="{{ $task->p_active_id }}">
                                                                                                                                    <input type="hidden" name="product_id" value="{{ $task->product_id }}">
                                                                                                                                    <input type="hidden" name="phase_id" value="{{ $task->id }}">
                                                                                                                                    <input type="hidden" name="customer_id" value="{{ request()->id }}">
                                                                                                                                    <div class="table-responsive">
                                                                                                                                        <table class="table table-hover-animation mb-0">
                                                                                                                                            <thead>
                                                                                                                                                <tr>
                                                                                                                                                    <th scope="col">DONE</th>
                                                                                                                                                    <th scope="col">DATE</th>
                                                                                                                                                    <th scope="col">Verfasser</th>
                                                                                                                                                    <th scope="col">Transfer</th>
                                                                                                                                                    <th scope="col">Außendienst</th>
                                                                                                                                                </tr>
                                                                                                                                            </thead>
                                                                                                                                            <tbody>
                                                                                                                                                <tr>
                                                                                                                                                    <td>
                                                                                                                                                        <fieldset>
                                                                                                                                                            <div class="vs-checkbox-con vs-checkbox-success">
                                                                                                                                                                <input type="checkbox" value="1" name="done">
                                                                                                                                                                <span class="vs-checkbox">
                                                                                                                                                                    <span class="vs-checkbox--check">
                                                                                                                                                                        <i class="vs-icon feather icon-check"></i>
                                                                                                                                                                    </span>
                                                                                                                                                                </span>
                                                                                                                                                            </div>
                                                                                                                                                        </fieldset>
                                                                                                                                                    </td>
                                                                                                                                                    <td>
                                                                                                                                                        <input type="date" name="done_date" class="form-control" value="{{ now()->format('Y-m-d') }}">
                                                                                                                                                    </td>
                                                                                                                                                    <td>
                                                                                                                                                        <div class="photo" style="display: flex; align-items: center;">
                                                                                                                                                            <div class="avatar mr-1">
                                                                                                                                                                <img src="{{ asset('images/employee/'.$contact_person->image) }}" alt="{{ $contact_person->name }}" height="32" width="32">
                                                                                                                                                            </div>
                                                                                                                                                            <label for="avatar" class="mt-0" style="font-size:14px">
                                                                                                                                                                {{ $contact_person->name }} {{ $contact_person->lastname }}
                                                                                                                                                            </label>
                                                                                                                                                            <input type="hidden" name="contact_person" value="{{ $contact_person->id }}" class="form-control">
                                                                                                                                                        </div>
                                                                                                                                                    </td>
                                                                                                                                                    <td>
                                                                                                                                                        <select name="responsible_person" id="transfer" class="form-control">
                                                                                                                                                            <option disabled selected data-image="{{ asset('images/gender/male.png')}}">Wer hat diese Aufgabe erledigt?</option>
                                                                                                                                                            @foreach ($responsibles as $emp)
                                                                                                                                                                <option value="{{ $emp->emp_id }}" data-image="{{ asset('images/employee/'.$emp->image)}}">{{ $emp->name }} {{ $emp->lastname }}</option>
                                                                                                                                                            @endforeach
                                                                                                                                                        </select>
                                                                                                                                                    </td>
                                                                                                                                                    <td>
                                                                                                                                                        <select name="outside_service" id="outside" class="form-control">
                                                                                                                                                            <option disabled selected data-image="{{ asset('images/gender/male.png')}}">Bitte wählen Sie den Außendienst aus</option>
                                                                                                                                                            @foreach ($employees as $emp)
                                                                                                                                                                <option value="{{ $emp->id }}" data-image="{{ asset('images/employee/'.$emp->image)}}">{{ $emp->name }} {{ $emp->lastname }}</option>
                                                                                                                                                            @endforeach
                                                                                                                                                        </select>
                                                                                                                                                    </td>
                                                                                                                                                    <tr>
                                                                                                                                                        <td colspan="12">
                                                                                                                                                            <textarea name="note" cols="30" class="form-control"></textarea>
                                                                                                                                                        </td>
                                                                                                                                                    </tr>
                                                                                                                                                    <tr>
                                                                                                                                                        <td colspan="12">Wenn für diese Aufgabe Dokumente vorliegen, füllen Sie diese Informationen bitte entsprechend aus</td>
                                                                                                                                                    </tr>
                                                                                                                                                    <tr>
                                                                                                                                                        <td colspan="1">
                                                                                                                                                            <input type="text" name="document_name" class="form-control" value="{{ old('document_name')}}" placeholder="Dukument Name" required>
                                                                                                                                                        </td>
                                                                                                                                                        <td colspan="2">
                                                                                                                                                            <textarea name="document_details" class="form-control" value="{{ old('document_details')}}" placeholder="Dukument Details"></textarea>
                                                                                                                                                        </td>
                                                                                                                                                        <td colspan="1">
                                                                                                                                                            <input type="text" name="document_sum" class="form-control" value="{{ old('document_sum')}}" placeholder="Dukument Summe">
                                                                                                                                                        </td>
                                                                                                                                                        <td colspan="1">
                                                                                                                                                            <select name="document_status" id="" class="form-control">
                                                                                                                                                                <option value="">Bitte wählen Sie die Dokument Status</option>
                                                                                                                                                                <option value="Rechnung">Rechnung</option>
                                                                                                                                                                <option value="Quittung">verschickt</option>
                                                                                                                                                                <option value="Rechnung">bezahlt</option>
                                                                                                                                                                <option value="Rechnung">Warten auf Kunden</option>
                                                                                                                                                            </select>
                                                                                                                                                        </td>
                                                                                                                                                    </tr>
                                                                                                                                                    <tr>
                                                                                                                                                        <td colspan="12">
                                                                                                                                                            <input type="file" name="document" class="form-control">
                                                                                                                                                        </td>
                                                                                                                                                    </tr>
                                                                                                                                                </tr>
                                                                                                                                            </tbody>
                                                                                                                                        </table>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                            <div class="card-footer">
                                                                                                                                <button type="submit" class="btn btn-primary" id="saveTaskButton">Speichern</button>
                                                                                                                            </div>
                                                                                                                        </form>
                                                                                                                    </div>
                                                                                                                </td>
                                                                                                            </div>

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
                    </div>
                </div>  
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

<script src="{{ asset('js/select2.min.js') }}"></script>
<script>
$(document).ready(function() {
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

$('#transfer').select2({
    templateResult: formatOption,
    templateSelection: formatOption
});

function formatOption(option) {
    if (!option.id) {
        return option.text;
    }
    var $option = $('<span><img src="' + $(option.element).data('image') + '" class="img-flag" /> ' + option.text +
        '</span>');
    return $option;
}
 
$('#outside').select2({
    templateResult: formatOption,
    templateSelection: formatOption
});
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll("button[id^='show']").forEach(function(button) {
            button.addEventListener("click", function() {
                var taskId = this.id.replace("show", "");
                var doneRow = document.getElementById("done" + taskId);
                if (doneRow) { // Check if the element exists
                    doneRow.style.display = (doneRow.style.display === "none" || doneRow.style.display === "") ? "table-row" : "none";
                }
            });
        });

        document.querySelectorAll("button[id^='info']").forEach(function(button) {
            button.addEventListener("click", function() {
                var taskId = this.id.replace("info", "");
                var infoRow = document.getElementById("infoTable" + taskId);
                if (infoRow) { // Check if the element exists
                    infoRow.style.display = (infoRow.style.display === "none" || infoRow.style.display === "") ? "table-row" : "none";
                }
            });
        });
    });
</script>

 


@endsection
