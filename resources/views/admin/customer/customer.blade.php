@extends('admin.layouts.app')

@section('title') KUNDEN UND OBJEKTDATEN @endsection

@section('content')


    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                         
                            <h2 class="content-header-title float-left mb-0">KUNDEN INFORMATION</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('customer_details') }}">KUNDEN</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">{{ $data->name }} {{ $data->lastname }}</a>
                                    </li>
                                </ol>
                            </div>
                      
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrum-right">
                        <div class="dropdown">
                            <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="feather icon-settings"></i></button>
                            <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">Chat</a><a class="dropdown-item" href="#">Email</a><a class="dropdown-item" href="#">Calendar</a></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Basic Horizontal form layout section start -->
                <section id="basic-horizontal-layouts">
                    <div class="row match-height">
                        <div class="col-md-6 col-12">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <form class="form form-horizontal">
                                            <div class="form-body">
                                         
                                                <div class="row">
                                               
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Title</span>
                                                            </div>
                                                           <div class="col-md-8">
                                                                <input type="text" disabled id="first-name" value="{{$data->title }}"class="form-control" name="title" >
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Firma</span>
                                                            </div>
                                                           <div class="col-md-8">
                                                                <input type="text" disabled id="first-name" class="form-control" value="{{$data->firma }}" name="firma" >
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Vorname</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" disabled id="first-name" class="form-control" value="{{$data->lastname }}" name="lastname" >
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Name</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" disabled id="first-name" class="form-control" value="{{$data->name }}" name="name">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Straße / Nr.</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" disabled id="first-name" class="form-control" value="{{$data->street }}" name="street">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>PLZ / Ort</span>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text" disabled id="first-name" class="form-control" value="{{$data->postcode }}" name="postcode">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text" disabled id="first-name" class="form-control" value="{{$data->city }}"name="city">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Tel</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="number" disabled id="contact-info" class="form-control" value="{{$data->phone }}" name="phone">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>E-Mail</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="email" disabled id="contact-info" class="form-control" value="{{$data->email }}" name="email" >
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>BV abweichende Adresse</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                   
                                        

                                                   
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Ersteller</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" disabled id="contact-info" class="form-control" value="{{$data->empname }} {{$data->emplastname }}" name="date">
                                                            </div>
                                                        </div>
                                                    </div>     
                                                    
                                            </div>
                                        </form>
                                    </div>

                                    <div class="card-header">
                                        <h4 class="card-title">Die Produktliste</h4>
                                    </div>
                                    
                                </div>
                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-md-8">
                                            <a type="button" class="btn btn-primary" href="{{ url('customer_details')}}"> Back </a>
                                        </div>
                                    </div>
                                </div>     
                            </div>
                        </div>
                    </div>
                        <div class="col-md-6 col-12">
                            <section id="nav-justified">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="card overflow-hidden">
                                           
                                            <div class="card-content">
                                                <div class="card-body">
                                                    <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">
                                                        <li class="nav-item">
                                                            <a class="nav-link active" id="home-tab-justified" data-toggle="tab" href="#home-just" role="tab" aria-controls="home-just" aria-selected="true">Profile</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" id="profile-tab-justified" data-toggle="tab" href="#profile-just" role="tab" aria-controls="profile-just" aria-selected="true">Tickets</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" id="messages-tab-justified" data-toggle="tab" href="#messages-just" role="tab" aria-controls="messages-just" aria-selected="false">Emails</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" id="settings-tab-justified" data-toggle="tab" href="#settings-just" role="tab" aria-controls="settings-just" aria-selected="false">Calender</a>
                                                        </li>
                                                    </ul>
            
                                                    <!-- Tab panes -->
                                                    <div class="tab-content pt-1">
                                                        <div class="tab-pane active" id="home-just" role="tabpanel" aria-labelledby="home-tab-justified">
                                                            @include('admin.customer.customer_page.profile')
                                                        </div>
                                                        <div class="tab-pane" id="profile-just" role="tabpanel" aria-labelledby="profile-tab-justified">
                                                            @include('admin.customer.customer_page.ticket')
                                                        </div>
                                                        <div class="tab-pane" id="messages-just" role="tabpanel" aria-labelledby="messages-tab-justified">
                                                            @include('admin.customer.customer_page.email')
                                                        </div>
                                                        <div class="tab-pane" id="settings-just" role="tabpanel" aria-labelledby="settings-tab-justified">
                                                            @include('admin.customer.customer_page.calender')
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                        
                </section>
                <!-- // Basic Horizontal form layout section end -->
            </div>
        </div>
    </div>
    <!-- END: Content-->

@endsection