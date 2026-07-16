 @extends('admin.layouts.app')
 @section('title')
 KUNDE PROFILE
 @endsection

 @section('style')
 <link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" />
<script src="{{ asset('js/dropzone.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

 <style>
        .select2-selection {
       border: 2px !important;
        width: 100% !important;
        background: #efeded !important;
        height: 40px !important;
        font-size: 20px;
        align-content: center;
        font-weight: bolder;
    }
    .circle {
      width: 35px;
      height: 35px;
      background-color: #8fc73e;
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

    hr {
        background: #c0c0c0 !important;
        width: 255px;
        align-self: center;
    }
    p {
        font-size: 13px;
    }
    .selected-nav {
        background-color: transparent !important;
        color: #92c15c !important;
        font-weight: bold;
    }
  </style>

   <style>
       

        .progress_card {
            font-family: Arial, sans-serif;
            padding: 20px;
            display: flex;
            justify-content: left;
            align-items: center;
            background-color: transparent;
        }
        .step-progress {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            overflow-x: auto;
            width: 100%;
            max-width: 1200px;
        }

        .step {
            position: relative;
            padding: 10px 20px;
            color: white;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .step span {
            font-size: 14px;
        }

        .step:after {
            content: '';
            position: absolute;
            top: 0px;
            right: -19px;
            border-top: 19px solid transparent;
            border-bottom: 23px solid transparent;
            border-left: 20px solid;
            z-index: 1000;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Default shadow */
        }


        .step:last-child:after {
            display: none;
        }


        @media screen and (max-width: 768px) {
            .step span {
                font-size: 12px;
            }

            .step:after {
                border-top: 15px solid transparent;
                border-bottom: 15px solid transparent;
                border-left: 15px solid;
            }
        }

    </style>

    <style>
        .media-list {
            list-style:none;
        }
        .icon-menu, .icon-file , .icon-heart {
            font-size:15px;
            color:#7dc242;
            margin-left:5px;
        }

         .icon-menu:hover, .icon-file:hover, .icon-heart:hover {
                font-size: 20px;
                color: #b0d5f2;
            }

         
       
        .customer-title p {
            font-weight: bolder;
             font-size: 20px !important;
        }
        .customer-div {
                border-right: 1px solid #c0c0c0;
        } 

        .alternative-div {
            padding-bottom: 1.5rem !important;
            border-bottom: 1px solid #c0c0c0 !important;
            width: 89% !important;
            align-self: center !important;
        }
        .card {
            box-shadow: 0 0 !important;
        }
        .carousel-indicators {
            display:none;
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
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">KUNDENPROFIL</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a>
                                    </li> 
                                    <li class="breadcrumb-item"><a href="{{ url('/new_lead_view') }}">Kunde</a>
                                    </li> 
                                    <li class="breadcrumb-item"><a href="{{ url('/new_lead_view') }}">
                                        @php
                                            $previousUrl = url()->previous();
                                        @endphp

                                        @if(Str::contains($previousUrl, 'leads'))
                                            Leads
                                        @elseif(Str::contains($previousUrl, 'plan'))
                                            Planung
                                        @elseif(Str::contains($previousUrl, 'deal'))
                                            Aufträge
                                        @elseif(Str::contains($previousUrl, 'offer'))
                                        Angebote
                                        @elseif(Str::contains($previousUrl, 'project'))
                                        Projekt
                                        @else
                                            Leads
                                        @endif 
                                    </a>
                                    </li>
                                    <li class="breadcrumb-item active"> {{ $customer->name }} {{ $customer->lastname }}
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
                <di class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                               <div class="row"> 
                                <div class="col-12 col-md-3 customer-div">
                                    <span class="customer-title"> 
                                       
                                        <p class="mb-0">  {{ strtoupper($customer->title) }}</p>
                                        <p>{{ strtoupper($customer->name) }} {{ strtoupper($customer->lastname) }}</p>
                                    </span>
                                      <a href="{{ url('new_lead_details_edit/'.$customer->id)}}"><i class="feather icon-edit"></i></a>
                                </div>

                                <!-- Address: Moves below on small screens -->
                                <div class="col-12 col-md-2 customer-div">
                                    <span>
                                        <p class="mb-0">{{ $customer->street }}</p>  
                                        <p>{{ $customer->postcode }} {{ $customer->city }}</p>  
                                    </span>
                                </div>

                                <!-- Contact Info -->
                                <div class="col-12 col-md-2 customer-div">
                                    <span>
                                        <p class="mb-0">{{ $customer->email }}</p>  
                                        <p class="mb-0">{{ $customer->phone }}</p>  
                                        <p>{{ $customer->telephone }}</p>  
                                    </span>
                                </div>

                                <!-- Source -->
                                <div class="col-12 col-md-1 customer-div">
                                    <span>
                                        <p class="mb-0">Quelle: {{ $customer->source }}</p>  
                                    </span>
                                </div>

                                <!-- Additional Info -->
                                <div class="col-12 col-md-4">
                                    <span>
                                     
                                        <textarea name="" id=""   rows="5" disabled style="background: transparent; border:0; width:100% ">
                                            {{ $customer->info }}
                                        </textarea>
                                    </span>
                                </div>
                            </div>

                            </div>
                        </div>
                    </div>
                </di>
                <!-- account setting page start -->
            
                <div class="row">
                    <!-- left menu section --> 
                    <div class="col-md-3 mb-2 mb-0 fixed_part pt-0" >
                            
                        @foreach ($alternative as $alt)  
                        <div class="col-xl-12 col-sm-12 p-0">
                            <div class="card mb-1">
                                <div class="card-header mb-0 pb-0 alternative-div" >
                                    <div class="col-12"> 
                                        <p style="font-size: 18px;font-weight: bold;"> <a href="{{ url('new_lead_edit/'.$customer->id.'/'.$alt->id)}}"><i class="feather icon-edit"></i></a> {{ strtoupper($alt->object_name) }} </p>
                                    
                                        <p class="mb-0">{{$alt->street}}</p>
                                            <p class="">{{$alt->postcode}}, {{$alt->city}}</p>  
                                    </div> 
                                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3" style=" font-size: 25px;"></i></a>
                                    <div class="heading-elements">
                                        <ul class="list-inline mb-0">
                                            <li><a data-action="collapse" class=""><i class="feather icon-chevron-down" style=" font-size: 25px;"></i></a></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="card-header mb-0 pb-0 alternative-div" >
                                   <div class="row d-flex">
                                        <div class="col-6 "> 
                                            <p class="pb-0">Objektart: {{ $alt->objective ?? 'Nicht definiert' }}</p>  
                                        </div>
                                        <div class="col-6 "> 
                                             <div class="screenshot" data-toggle="modal" data-target="#screenshot{{$alt->id}}">
                                                 <div id="carousel-example-generic" class="carousel slide" data-ride="carousel" data-interval="200000" data-pause="hover"> 
                                                    @php $count = $screenshots->where('alternative_id', $alt->id)->count(); @endphp

                                                    @if($count > 0)
                                                        <!-- ✅ Generate Indicators Dynamically -->
                                                        <ol class="carousel-indicators">
                                                            @foreach ($screenshots->where('alternative_id', $alt->id) as $index => $screenshot)
                                                                <li data-target="#carousel-example-generic" data-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}"></li>
                                                            @endforeach
                                                        </ol>

                                                        <!-- ✅ Carousel Items -->
                                                        <div class="carousel-inner" role="listbox">
                                                            @foreach ($screenshots->where('alternative_id', $alt->id) as $index => $screenshot)
                                                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                                    <img class="img-fluid" src="{{ asset('images/customers/' . $screenshot->image) }}" alt="{{ $screenshot->image_name }}">
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        <!-- ✅ Navigation Controls -->
                                                        <a class="carousel-control-prev" href="#carousel-example-generic" role="button" data-slide="prev">
                                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                            <span class="sr-only">Previous</span>
                                                        </a>
                                                        <a class="carousel-control-next" href="#carousel-example-generic" role="button" data-slide="next">
                                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                            <span class="sr-only">Next</span>
                                                        </a>
                                                    @else
                                                        <p class="text-center text-muted">Keine Screenshots verfügbar.</p>
                                                    @endif 
                                                </div> 
                                                 
                                                <!-- Modal of pciture  -->
                                                 <div class="modal fade text-left" id="screenshot{{$alt->id}}" tabindex="-1" role="dialog" 
                                                    aria-labelledby="myModalLabel17" style="display: none;" aria-hidden="true"
                                                    data-backdrop="static" data-keyboard="false">  <!-- ✅ Prevent backdrop close -->

                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel17">{{ $alt->object_name }}</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">×</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div id="carousel-example-generic" class="carousel slide" data-ride="carousel" data-interval="200000" data-pause="hover"> 
                                                                    @php $count = $screenshots->where('alternative_id', $alt->id)->count(); @endphp

                                                                    @if($count > 0)
                                                                        <!-- ✅ Generate Indicators Dynamically -->
                                                                        <ol class="carousel-indicators">
                                                                            @foreach ($screenshots->where('alternative_id', $alt->id) as $index => $screenshot)
                                                                                <li data-target="#carousel-example-generic" data-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}"></li>
                                                                            @endforeach
                                                                        </ol>

                                                                        <!-- ✅ Carousel Items -->
                                                                        <div class="carousel-inner" role="listbox">
                                                                            @foreach ($screenshots->where('alternative_id', $alt->id) as $index => $screenshot)
                                                                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                                                    <img class="img-fluid" src="{{ asset('images/customers/' . $screenshot->image) }}" alt="{{ $screenshot->image_name }}">
                                                                                </div>
                                                                            @endforeach
                                                                        </div>

                                                                        <!-- ✅ Navigation Controls -->
                                                                        <a class="carousel-control-prev" href="#carousel-example-generic" role="button" data-slide="prev">
                                                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                                            <span class="sr-only">Previous</span>
                                                                        </a>
                                                                        <a class="carousel-control-next" href="#carousel-example-generic" role="button" data-slide="next">
                                                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                                            <span class="sr-only">Next</span>
                                                                        </a>
                                                                    @else
                                                                        <p class="text-center text-muted">Keine Screenshots verfügbar.</p>
                                                                    @endif 
                                                                </div> 
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">abbrechen</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- End of Modal  -->

                                             </div>
                                        </div> 
                                   </div> 
                                </div>
                                
                                <div class="card-content collapse" style="">
                                    <div class="card-body mt-0 pt-0"> 
                                        <p>
                                            <ul class="nav nav-pills flex-column mt-md-0 mt-1"> 
                                                <li class="nav-item">
                                                    <a class="nav-link d-flex py-75 energy-link"  id="energe" data-id="{{$alt->id}}"  data-alternative="{{$alt->id}}" data-customer="{{$customer->id}}" data aria-expanded="true"> 
                                                        Objekt & Energiedaten
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link d-flex py-75" id="image" data-id="{{$alt->id}}"  data-alternative="{{$alt->id}}" data-customer="{{$customer->id}}" aria-expanded="false"> 
                                                    Bilder & Unterlagen
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link d-flex py-75" id="account-pill-info" data-toggle="pill" href="#account-vertical-info" aria-expanded="false">
                                                    Activities
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link d-flex py-75" id="account-pill-social" data-toggle="pill" href="#account-vertical-social" aria-expanded="false"> 
                                                        Geschäftsvorgänge
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link d-flex py-75" id="account-pill-connections" data-toggle="pill" href="#account-vertical-connections" aria-expanded="false"> 
                                                        Historie
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link d-flex py-75" id="account-pill-notifications" data-toggle="pill" href="#account-vertical-notifications" aria-expanded="false"> 
                                                        Kalender
                                                    </a>
                                                </li>
                                            </ul>

                                        </p> 
                                    </div> 
                                    <div class="card-body">
                                        <p style="font-size: 12px;font-weight: bold;">PRODUCKTE & DIENSTLEISTUNGEN</p>
                                        <div>
                                            <table class="table">  
                                                @php
                                                        $product_list = collect($productList);
                                                    @endphp

                                                @foreach ($product_list->where('customer_id', $customer->id)->where('alternative_id', $alt->id)->unique(fn($product) => $product->product_id.'-'.$product->alternative_id) as $product)
                                                <tr>    
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

                                                            $service = $services[$product->service] ?? $product->service;
                                                            $status = $services[$product->res_status] ?? $product->res_status;
                                                            $reason = $services[$product->reason] ?? $product->reason;
                                                        @endphp
                                                        @php
                                                            $name = null;
                                                            $lastname = null;
                                                            $emp_image = null;
                                                            $gender = null;
                                                            $msg = 'Not Defined';
                                                            $state = null;
                                                            $p_status = null;

                                                            if (isset($productEmployees) && is_iterable($productEmployees)) {
                                                                foreach ($productEmployees as $employee) {
                                                                    if ($employee->id == $product->current_employee) {
                                                                        $name = $employee->name;
                                                                        $lastname = $employee->lastname;
                                                                        $emp_image = $employee->image;
                                                                        $gender = $employee->gender;
                                                                        $state = $product->res_status ?? null;
                                                                        $p_status = $product->status ?? null;
                                                                        $msg = null;
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                        @endphp

                                                        @php
                                                                // Determine the default image based on gender
                                                                $defaultImage = $gender === "Male" 
                                                                    ? asset('images/gender/male.png') 
                                                                    : asset('images/gender/female.png');

                                                                // Determine the actual image to use
                                                                $employeeImage = file_exists('images/employee/'.$emp_image) && $emp_image 
                                                                    ? asset('images/employee/'.$emp_image) 
                                                                    : $defaultImage;
                                                            @endphp 
                                                        
                                                        <td class="p-0 text-left">
                                                                <div class="circle">{{ $product->initial }}</div> 
                                                        </td>  
                                                        <td class="" >  
                                                            <p style="font-size: 12px;">{{ $services[$product->service] ?? $product->service }}</p> 
                                                        </td>
                                                        <td class="p-0">
                                                            @if($p_status == 'buy')
                                                            <img src=" {{ asset('images/dashboard/icon_target.svg') }}" alt="" class="p-0" style="width: 52px;">
                                                            @elseif($p_status == 'end')
                                                                {{ $alt->project_date }}
                                                            @else
                                                            
                                                            <i class="feather icon-heart " ></i>
                                                            <i class="feather icon-menu"  ></i>
                                                            <i class="feather icon-file" 
                                                            data-id="{{$alt->id}}"  
                                                            data-alternative="{{$alt->id}}" 
                                                            data-customer="{{$customer->id}}" 
                                                            data-product="{{$product->product_id}}" 
                                                            data-service="{{ $product->service }}"
                                                            ></i>
                                                            @endif
                                                        </td>
                                                    </tr>   
                                                @endforeach  
                                            </table> 
                                        </div>
                                    </div> 
                                        <div class="card-body">
                                        <p style="font-size: 12px;font-weight: bold;">PROBLEME (TICKET)</p>
                                        <div>
                                            <table class="table">  
                                                @foreach ($tickets as $ticket)
                                                    <tr>
                                                            <td class="p-0 text-left">
                                                                <div class="circle">{{ $ticket->initial }}</div> 
                                                        </td> 
                                                            <td class="p-0 text-left ">
                                                            @foreach ($problems->where('problem_id', $ticket->id) as $problem) 
                                                                    <p style="font-size: 12px; margin:0;" >  {{ $problem->problem_types }},</p> 
                                                            @endforeach
                                                        </td> 
                                                        <td class="p-0"> 
                                                            <img src=" {{ asset('images/dashboard/icon_speechbubbles.svg') }}" alt=""   style="width: 48px;">
                                                            
                                                        </td> 
                                                    </tr>
                                                @endforeach 
                                            </table> 
                                        </div>
                                    </div>
                                    <!-- Edit Buttons  -->
                                    <div class="row">
                                        <a type="button" class="btn btn-outline-primary square ml-2 mb-1 waves-effect waves-light" href="{{ url('new_lead_edit/'.$alt->lead_id.'/'.$alt->id)}}">Bearbeiten</a>
                                        <button type="button" data-toggle="modal" data-target="#delete-alter{{$alt->id}}" class="btn btn-outline-primary square ml-2 mb-1 waves-effect waves-light">Löschen</button>
                                    </div>
                                    <!-- Edit Buttons  -->
                                    <div class="modal fade" id="delete-alter{{$alt->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger white">
                                                    <h5 class="modal-title" id="myModalLabel120">   Objekt Löschen </h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                </div>
                                            
                                                <div class="modal-body">
                                                    <h5>Aufzeichnung löschen</h5>
                                                    <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                    <p>Die Datensatznummer lautet:{{$customer->customer_no}}. {{ $customer->name }} {{ $customer->lastname }} </p>
                                                    <p>Die Objektname: {{$alt->object_name}}</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <a type="button" href="{{url('/delete_lead_alternative').'/'.$alt->id}}" class="btn btn-danger">Ja</a>
                                                </div>
                
                                            </div>
                                        </div>
                                    </div>                  
                                </div>
                                
                            </div> 
                        </div>   
                        @endforeach
                    </div>
                    <!-- right content section -->
                        <div class="col-md-9 display_part" > 
                            <div class="card" id="energie" data-id="" style="display: none;" >
                                <div class="card-header">
                                    <h4>OBJEKT & ENERGIEDATEN</h4>
                                </div>
                                <div class="card-body">
                                    
                                    <div class="row">
                                            <div class="col-md-6">
                                                <!-- Objektdaten Section -->
                                                <div class="col-12">
                                                    <p class="primary"><strong>OBJEKTDATEN</strong></h2> 
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-12">
                                                            <p class="bold">Welche Objektart handelt es sich?</p>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            <label id="objective"></label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-12">
                                                            <p class="bold">Baujahr Ihres Hauses?</p>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            <label id="house_year"></label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-12">
                                                            <p class="bold">Wieviel Wohneinheit hat das Objekt?</p>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            <label id="house_we"></label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-12">
                                                            <p class="bold">Wieviel Geschoß hat das Objekt?</p>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            <label id="number_stories"></label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-12">
                                                            <p class="bold">Wie groß ist die Beheizte Wohnfläche?</p>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            <label id="living_space"> m²</label>
                                                        </div>  
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-12">
                                                            <p class="bold">Wie groß ist die Nutzfläche?</p>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            <label id="unusable_space"> m²</label>
                                                        </div>  
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-12">
                                                            <p class="bold">Wieviel Personen wohnen in diesem Objekt?</p>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            <label id="number_people"> </label>
                                                        </div>  
                                                    </div>
                                                </div>

                                                <!-- Dach-Information Section -->
                                                <div class="col-12">
                                                    <p class="primary"><strong>DACH-INFORMATION</strong></h2> 
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-12">
                                                            <p class="bold">Welche Art vom Dach haben Sie?</p>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            <label id="roof_type"></label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-12">
                                                            <p class="bold">Wie alt ist Ihr Dach?</p>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            <label id="roof_age"> Jahr</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-12">
                                                            <p class="bold">Welche Dacheindeckung hat das Dach?</p>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            <label id="tile_name"></label>
                                                        </div>
                                                    </div>
                                                </div>  
                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-12">
                                                            <p class="bold">Welche dachneigung hat ihr Dach?</p>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            <label id="roof_pitch"></label>
                                                        </div>
                                                    </div>
                                                </div> 
                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-12">
                                                            <p class="bold">Welche himmelsausrechtung hat ihr Dach?</p>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            <label id="roof_direction"></label>
                                                            
                                                        </div>
                                                    </div>
                                                </div> 
                                            </div>

                                            <div class="col-md-6">
                                                <!-- Heizungs-Information Section -->
                                                <div class="col-12">
                                                    <p class="primary"><strong>HEIZUNGS-INFORMATION</strong></h2>
                                                    <hr>
                                                </div> 

                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-12">
                                                            <p class="bold">Welche Art von Heizungsanlage haben Sie?</p>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            <label id="heating_system_type"></label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-12">
                                                            <p class="bold">Wie alt ist Ihre Heizungsanlage?</p>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            <label id="heating_system_age"> Jahr</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-12">
                                                            <p class="bold">Baujahr der Heizungsanlage?</p>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            <label id="heating_system_year" ></label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-12">
                                                            <p class="bold">Welches Heizsystem ist verbaut?</p>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            <label id="heating_type">{{ $customer->heating_type ?? 'N/A' }}</label>
                                                        </div>  
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-12">
                                                            <p class="bold">Wo befindet sich die aktuelle Heizungsanlage?</p>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            <label id="installation_location"></label>
                                                            <label id="installation_location_extra" ></label>
                                                        </div>  
                                                    </div>
                                                </div>

                                                <!-- Stromverbrauch Section -->
                                                <div class="col-12">
                                                    <p class="primary"><strong>STROMVERBRAUCH</strong></h2>
                                                    <hr>
                                                </div> 

                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-12">
                                                            <p class="bold">Wie hoch ist Ihr jährlicher Stromverbrauch?</p>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            <label id="annual_consumption" > kWh</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Heizenergie Verbrauch Section -->
                                                <div class="col-12">
                                                    <p class="primary"><strong>HEIZENERGIE VERBRAUCH</strong></h2>
                                                    <hr>
                                                </div> 

                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-12">
                                                            <p class="bold">Wie hoch ist Ihr jährlicher Verbrauch an Heizenergie?</p>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            <label id="annual_heating_energy_consumption"> CMB</label>
                                                            <label id="annual_heating_energy_consumption_kwh"> kWh</label> 
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- E-Mobilität Section -->
                                                <div class="col-12">
                                                    <p class="primary"><strong>E-MOBILITÄT</strong></h2>
                                                    <hr>
                                                </div> 

                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-12">
                                                            <p class="bold">Haben Sie ein Elektroauto? Oder planen Sie, welche zukaufen?</p>
                                                        </div>
                                                        <div class="col-md-6 flex_me">
                                                            <label id="electric_car"></label>
                                                        </div>
                                                        <div class="col-md-6 flex_me">
                                                            <label id="electric_car_plan"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-12">
                                                            <p class="bold">Wieviel Kilometer Fahren Sie pro PKW im Jahr?</p>
                                                        </div>
                                                        <div class="col-md-6 flex_me">
                                                            <label id="car_kilo"> km</label>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                        <button type="button" class="btn btn-flat-primary mr-1 mb-1 waves-effect waves-light edit_energy" data-toggle="modal" data-target="#editenergy" >
                                                Bearbeiten  
                                        </button> 
                                        <div class="modal fade text-left" id="editenergy" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="myModalLabel17">ENERGIEVERBRAUCH & OBJEKTDATEN</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('lead.info.data') }}" method="post">
                                                        @csrf
                                                        <div class="modal-body"> 
                                                    <input type="hidden" name="customer_id" value="" id="customer_id">
                                                        <input type="hidden" name="alternative_id" value="" id="alternative_id">
                                                            <div class="row" style="    background: white;">
                                                                <div class="col-md-6">
                                                                    <div class="col-12">
                                                                            <p class="primary"><strong>OBJEKTDATEN</strong></h2>
                                                                            <hr>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group row form-element">
                                                                                <div class="col-md-12">
                                                                                    <p class="bold">Welche Objektart handelt es sich?</p>
                                                                                </div>
                                                                                <div class="col-md-12 flex_me">
                                                                            <select name="objective" id="" class="form-control">
                                                                                <option value="">Bitte wählen</option>
                                                                                <option value="EFH" >EFH</option>
                                                                                <option value="MFH" >MFH</option>
                                                                                <option value="Gewerbe" >Gewerbe</option>
                                                                                <option value="others" >Sonstigis</option>
                                                                            </select>

                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-12">
                                                                            <div class="form-group row form-element">
                                                                                <div class="col-md-12">
                                                                                    <p class="bold">Baujahr Ihres Hauses?</p>
                                                                                </div>
                                                                                <div class="col-md-12 flex_me">
                                                                                    <input type="number" class="form-control form-element" name="house_year" id="house_year" value="" />
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                            
                                                                        <div class="col-12">
                                                                            <div class="form-group row form-element">
                                                                                <div class="col-md-12">
                                                                                    <p class="bold">Wieveil Wohneinheit hat das Obejekt?</p>
                                                                                </div>
                                                                                <div class="col-md-12 flex_me">
                                                                                    <input type="number" class="form-control textbox" name="number_we" value="">
                                                                                
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-12">
                                                                            <div class="form-group row form-element">
                                                                                <div class="col-md-12">
                                                                                    <p class="bold">Wieviel Geschoß hat das Objekt?   </p>
                                                                                </div>
                                                                                <div class="col-md-12 flex_me">
                                                                                <input type="number" class="form-control"  name="number_stories" value="">
                                                                                
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-12">
                                                                            <div class="form-group row form-element">
                                                                                <div class="col-md-12">
                                                                                    <p class="bold">Wie groß ist die Beheizte Wohnfläche?</p>
                                                                                </div>
                                                                                <div class="col-md-12 flex_me">
                                                                                <input type="number" class="form-control" name="living_space" value="">
                                                                                    <span style="position: absolute; right: 20px;"> m²</span>
                                                                                
                                                                                </div>  
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-12">
                                                                            <div class="form-group row form-element">
                                                                                <div class="col-md-12">
                                                                                    <p class="bold">Wie groß ist die Nutzfläche?</p>
                                                                                </div>
                                                                                <div class="col-md-12 flex_me">
                                                                                <input type="number" class="form-control" name="unusable_space"  value="">
                                                                                    <span style="position: absolute; right: 20px;"> m²</span> 
                                                                                </div>  
                                                                            </div>
                                                                        </div>


                                                                        <div class="col-12">
                                                                            <div class="form-group row form-element">
                                                                                <div class="col-md-12">
                                                                                    <p class="bold">Wieviel Personen wohnen in diesem Objekt?</p>
                                                                                </div>
                                                                                <div class="col-md-12 flex_me">
                                                                                <input type="number" class="form-control" name="number_people" id="number_people"  value="" > 
                                                                                </div>  
                                                                            </div>
                                                                        </div>
                                                                    
                                                                        <div class="col-12"><p class="primary"><strong>DACH-INFORMATION</strong></h2><hr></div> 
                                                                        <div class="col-12">
                                                                            <div class="form-group row form-element">
                                                                                <div class="col-md-12">
                                                                                    <p class="bold">Welche Art vom Dach haben Sie?</p>
                                                                                </div>
                                                                                <div class="col-md-12 flex_me">
                                                                                    <select class="form-control form-element" name="roof_type" id="roof">
                                                                                        <option selected></option>
                                                                                        <option value="Satteldach"    >Satteldach</option>
                                                                                        <option value="Flachdach"  >Flachdach</option>
                                                                                        <option value="Carpot"  >Carpot</option>
                                                                                        <option value="Garage"   >Garage</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group row form-element">
                                                                                <div class="col-md-12">
                                                                                    <p class="bold">Wie alt ist Ihr Dach?</p>
                                                                                </div>
                                                                                <div class="col-md-12 flex_me">
                                                                                    <input type="number" class="form-control form-element" name="roof_age" id="roof_age" value="" />
                                                                                    <span style="position: absolute; right: 20px;">Jahr</span>
                                                                                
                                                                                </div>
                                                                                <div class="col-md-12">
                                                                                    <span id="roof_age_error" class="text-danger"></span>
                                                                                </div>
                                                                                
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group row form-element">
                                                                                <div class="col-md-12">
                                                                                    <p class="bold">Welche Dacheindeckung hat das Dach?</p>
                                                                                </div>
                                                                                <div class="col-md-12 flex_me">
                                                                                    <input type="number" class="form-control textbox" name="tile_name" value="">
                                                                                
                                                                                </div>
                                                                            </div>
                                                                        </div> 

                                                                        <div class="col-12">
                                                                            <div class="form-group row form-element">
                                                                                <div class="col-md-12">
                                                                                    <p class="bold">Welche Dacheindeckung hat das Dach? 
                                                                                        <i class="feather icon-info warning" 
                                                                                        data-toggle="popover" 
                                                                                        data-placement="top" 
                                                                                        data-container="body" 
                                                                                        data-original-title="Achtung" 
                                                                                        data-content="Der verfügbare Wert liegt zwischen 0,5, 10, 15, 20 und 60."></i>
                                                                                    </p>
                                                                                </div>
                                                                                <div class="col-md-12 flex_me">
                                                                                    <input type="number" class="form-control textbox" name="tile_name" value=""> 
                                                                                </div>
                                                                            </div>
                                                                        </div>  
                                                        
                                                                        <!-- Make i button to show from which to which number can be  -->
                                                                        <div class="col-12">
                                                                            <div class="form-group row form-element">
                                                                                <div class="col-md-12">
                                                                                    <h6 class="bold">Welche dachneigung hat ihr Dach?</h6>
                                                                                </div>
                                                                                <div class="col-md-12 flex_me">
                                                                                    <input type="number" class="form-control textbox" name="roof_pitch" value=""> 
                                                                                </div>
                                                                            </div>
                                                                        </div> 

                                                                            <div class="col-12">
                                                                            <div class="form-group row form-element">
                                                                                <div class="col-md-12">
                                                                                    <p class="bold">Welche himmelsausrechtung hat ihr Dach?</p>
                                                                                </div>
                                                                                <div class="col-md-12 flex_me">
                                                                                    <select name="roof_direction" id="" class="form-control"> 
                                                                                            <option value="south">Süden </option>
                                                                                            <option value="south-west">Süd-west </option>
                                                                                            <option value="west">Westen </option>
                                                                                            <option value="north-west">Nord-west </option>
                                                                                            <option value="north">Norden </option>
                                                                                            <option value="north-east">Nord-ost </option>
                                                                                            <option value="east">Osten </option>
                                                                                            <option value="south-east">Süd-ost </option>  
                                                                                            <option value="east-west">Ost-West</option>  
                                                                                            <option value="north-south">Nord-Süd</option>  
                                                                                    </select> 
                                                                                </div>
                                                                            </div>
                                                                        </div> 
                                                                </div>
                                                                
                                                                <div class="col-md-6">
                                                                    <div class="col-12"><p class="primary"><strong>HEIZUNGS-INFORMATION</strong></h2><hr></div> 
                                                                        <div class="col-12">
                                                                            <div class="form-group row form-element">
                                                                                <div class="col-md-12">
                                                                                    <p class="bold">Welche Art von Heizungsanlage haben Sie?</p>
                                                                                </div>
                                                                                <div class="col-md-12 flex_me">
                                                                                    <select class="form-control form-element" name="heating_system_type" id="heating_system_type_edit">
                                                                                        <option selected disabled> </option>
                                                                                        <option value="Gas" >Gas</option>
                                                                                        <option value="Öl" >Öl</option>
                                                                                        <option value="Wärmepumpe" >Wärmepumpe</option>
                                                                                        <option value="Nachtspeicher" >Nachtspeicher</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-12">
                                                                            <div class="form-group row form-element">
                                                                                <div class="col-md-12">
                                                                                    <p class="bold">Wie alt ist Ihre Heizungsanlage?</p>
                                                                                </div>
                                                                                <div class="col-md-12 flex_me">
                                                                                    <input type="number" class="form-control form-element" name="heating_system_age" id="heating_system_age" value=""/>
                                                                                    <span style="position: absolute; right: 20px;">Jahr</span>
                                                                                </div>
                                                                                <div class="col-md-12">
                                                                                    <span id="heating_system_age_error" class="text-danger"></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group row form-element">
                                                                                <div class="col-md-12">
                                                                                    <p class="bold"> Baujahr der Heizungsanlage?</p>
                                                                                </div>
                                                                                <div class="col-md-12 flex_me">
                                                                                    <input type="number" class="form-control form-element" name="heating_system_year" id="heating_system_year" value="" />
                                                                                </div>
                                                                                <div class="col-md-12">
                                                                                    <span id="heatingYearError" class="text-danger"></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-12">
                                                                            <div class="form-group row form-element">
                                                                                <div class="col-md-12">
                                                                                    <p class="bold">Welches Heizsystem ist verbaut?</p>
                                                                                </div>
                                                                                <div class="col-md-12 flex_me">
                                                                            <select name="heating_type" id="heating_type" class="form-control">
                                                                                <option value="">Bitte wählen</option>
                                                                                <option value="underfloor_heating">Fußbodenheizung</option>
                                                                                <option value="heating_system">Heizkörper</option>
                                                                                <option value="both" >Fußbodenheizung + Heizkörper</option>
                                                                                <option value="none">Keine</option>
                                                                            </select>

                                                                                </div>  
                                                                            </div>
                                                                        </div>
                                                                    

                                                                        <div class="col-12">
                                                                            <div class="form-group row form-element">
                                                                                <div class="col-md-12">
                                                                                    <p class="bold">Wo befindet sich die aktuelle Heizungsanlage?</p>
                                                                                </div>
                                                                                <div class="col-md-12 flex_me">
                                                                            <select name="installation_location" id="installation_location" class="form-control">
                                                                                    <option value="">Bitte wählen</option>
                                                                                        <option value="KG">KG</option>
                                                                                    <option value="EG">EG</option>
                                                                                    <option value="OG"> OG</option>
                                                                                    <option value="DG"> DG</option> 
                                                                                    <option value="SONSTIGES"> SONSTIGES</option> 
                                                                                </select>

                                                                                    <input type="text" class="form-control" name="installation_location_extra" id="installation_location_extra" value="{{ old('installation_location_extra', $customer->installation_location_extra)}}" placeholder="SONSTIGIES..">
                                                                                </div>  
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-12"><p class="primary"><strong>STROMVERBRAUCH</strong></h2><hr></div> 

                                                                    
                                                                        <div class="col-12">
                                                                            <div class="form-group row form-element">
                                                                                <div class="col-md-12">
                                                                                    <h3 class="bold">Wie hoch ist Ihr jährlicher Stromverbrauch?</h3>
                                                                                </div>
                                                                                <div class="col-md-12 flex_me">
                                                                                    <input type="number" class="form-control form-element" name="annual_consumption" value="{{ old('annual_consumption')}}"  />
                                                                                    <span style="position: absolute;right: 20px;">kWh</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12"><h2 class="primary"><strong>HEIZENERGIE VERBRAUCH</strong></h2><hr></div> 
                                                                        
                                                                        <div class="col-12">
                                                                            <div class="form-group row form-element">
                                                                                <div class="col-md-12">
                                                                                    <h3>Wie hoch ist Ihr jährlicher Verbrauch an Heizenergie?</he>
                                                                                </div>
                                                                                <div class="col-md-12 flex_me">
                                                                                    <!-- Conersion of CMB to KWH, cmb * 10  -->
                                                                                    <input type="number" class="form-control form-element mr-1" name="annual_heating_energy_consumption" id="annual_heating_energy_consumption" value="{{ old('annual_heating_energy_consumption')}}" />
                                                                                    <span  id="heat-energy">m³</span>
                                                                                    <input type="number" class="form-control form-element mr-1" name="annual_heating_energy_consumption_kwh" id="annual_heating_energy_consumption_kwh"  value="{{ old('annual_heating_energy_consumption_kwh')}}" /> 
                                                                                    <span >kWh</span>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12"><h2 class="primary"><strong>E-MOBILITÄT</strong></h2><hr></div> 

                                                                        <div class="col-12">
                                                                            <div class="form-group row form-element">
                                                                                <div class="col-md-12">
                                                                                    <h3 class="bold" >Haben Sie ein Elektroauto? Oder planen Sie eins zu kaufen?</h3>
                                                                                </div>
                                                                                <br>
                                                                                <div class="col-md-6 flex_me">
                                                                                    <select class="form-control form-element" name="electric_car" id="electric_car">
                                                                                        <option selected disabled></option>
                                                                                        <option value="Ja">Ja</option>
                                                                                        <option value="Nein">Nein</option>
                                                                                    </select>
                                                                                    <!-- When Nein, the below text box should be hidden -->
                                                                                </div>
                                                                                <div class="col-md-6 flex_me">
                                                                                    <input type="number" class="form-control form-element" name="electric_car_plan" id="electric_car_plan" value=""  />
                                                                                    <span style="display:none;position: absolute; right: 20px;"  id="electric_car_plan_l">Anzahl</span>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group row form-element">
                                                                                <div class="col-md-12">
                                                                                    <h3 class="bold">Wieviele Kilometer hat das Auto gefahren? (Alle Kilometer addieren)</h3>
                                                                                </div>
                                                                                <div class="col-md-12 flex_me">
                                                                                    <input type="number" class="form-control form-element" name="car_kilo" value=""  />
                                                                                    <span style="position: absolute;right: 20px;">km</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary waves-effect waves-light"  >Speichern</button>
                                                            <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Stornieren</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div> 
                                </div>
                            </div>

                                <div class="card" id="customer-image" data-id=""  style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-outline-warning waves-effect waves-light upload-image" data-customer="" data-alteranative="" data-toggle="modal" data-target="#large">
                                            UPLOAD
                                            </button>

                                            <div class="modal fade text-left" id="large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true" style="display: none;">
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
                                                                    <input type="hidden" name="customer_id" value="">
                                                                    <input type="hidden" name="alternative_id" value="">
                                                                    <input type="hidden" name="product_id" id="image_product_id" value="">
                                                                    <input type="hidden" name="stage_id" id="stage_id" value="">

                                                                    <div>
                                                                        <label for="article_group">Gewerke auswählen:</label>
                                                                        <select id="article_group" class="form-control" name="product_id">
                                                                            <option value="">-- Wählen Sie eine Artikelgruppe --</option> 
                                                                            <!-- Options will be dynamically populated -->
                                                                        </select>
                                                                    </div>
                                                                    <div>
                                                                        <label for="swal-stage">Stufe auswählen:</label>
                                                                        <select id="swal-stage" class="form-control" name="stage_id">
                                                                            <option value="">-- Wählen Sie eine Stufe --</option>
                                                                            <option value="customer">Kunde</option>
                                                                            <option value="montage">Montage</option>
                                                                            <option value="end">Abnahme</option>
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
                                        </div>
                                
                                        <div class="col-12 mt-2">
                                            <div class="divider">
                                                <div class="divider-text">FOTO</div>
                                            </div>
                                        </div> 
                                        <div class="col-md-12">
                                            <div id="photo_image" class="row"></div> 
                                        </div>

                                        <div class="col-12">
                                            <div class="divider">
                                                <div class="divider-text">DUKUMENT</div>
                                            </div>
                                        </div>

                                            <div class="col-12 mt-2 d-flex"> 
                                            <div id="document_container" class="row"></div> 
                                        </div>

                                        <!-- Modal Structure -->
                                        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="imageModalLabel">BILDVORSCHAU</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <div class="image-container" style="overflow: hidden; max-height: 80vh;">
                                                            <img id="modalImage" src="" alt="Preview" style="max-width: 100%; max-height: 100%; transform-origin: center center;">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="range" id="image_zoom" min="1" max="5" step="0.1" class="form-control" value="1" style="width: 100%;">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                            <div class="modal fade text-left" id="customer_document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="myModalLabel16">DOKUMENT VIEWER</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body text-center" id="document_viewer_body">
                                                            <!-- Content will be loaded dynamically based on file type -->
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a id="download_button" href="#" download class="btn btn-success">Download Document</a>
                                                            <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                                    
                                            
                                    </div> 
                            </div>

                            <div class="card" id="project">
                                <div class="progress_card">
                                    <div class="step-progress">
                                        <div class="step">
                                            <span>Anfrage</span>
                                            <i class="feather icon-check-square"></i>
                                        </div>
                                    
                                    </div>
                                </div>
                                <section id="nav-justified">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="overflow-hidden">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">

                                                            <li class="nav-item tab-control">
                                                                <a class="nav-link tab-control active" id="home-tab-justified" data-toggle="tab" href="#home-just" role="tab"
                                                                    aria-controls="home-just" aria-selected="true">CHECKLISTE</a>
                                                            </li>
                                                        
                                                            <li class="nav-item tab-control">
                                                                <a class="nav-link tab-control" id="messages-tab-justified" data-toggle="tab" href="#messages-just" role="tab"
                                                                    aria-controls="messages-just" aria-selected="true">PROJEKTMANAGEMENT</a>
                                                            </li> 
                                                        </ul>

                                                        <!-- Tab panes -->
                                                        <div class="tab-content pt-1">
                                                            <div class="tab-pane active" id="home-just" role="tabpanel"
                                                                aria-labelledby="home-tab-justified"> 
                                                                all Data of checklist
                                                            </div>
                                                            <div class="tab-pane" id="messages-just" role="tabpanel"
                                                                aria-labelledby="messages-tab-justified">
                                                                    <div class="phases"></div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>

                            <!-- Done Task Modal: -->
                            <div class="modal fade" id="doneModal" tabindex="-1" role="dialog" aria-labelledby="doneModalTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="doneModalTitle">Aufgabenerledigungsmodal</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                        <form id="doneTaskForm"> 
                                            @csrf
                                            <div class="modal-body">
                                                <input type="hidden" name="customer_id" value="">
                                                <input type="hidden" name="product_id" value="">
                                                <input type="hidden" name="alternative" value="">
                                                <input type="hidden" name="phase_id" value="">
                                                <input type="hidden" name="activities_id" value="">
                                                <input type="hidden" name="sub_task_id" value="">
                                                <input type="hidden" name="type" value="main">
                                                <input type="hidden" name="contact_person" value="{{ auth()->user()->name}}">
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Datum</span>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="position-relative has-icon-left">
                                                                <input type="date" class="form-control" name="done_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" placeholder="Datum" data-np-intersection-state="visible">
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
                                                                        <img src="{{ asset('images/employee/'.$current_user->image) }}" alt="{{ $current_user->name }}" height="32" width="32">
                                                                    </div>
                                                                    <label for="avatar" class="mt-0" style="font-size:14px">
                                                                        {{ $current_user->name }} {{ $current_user->lastname }}
                                                                    </label> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="responsible_person">Verantwortlicher</label>
                                                    <select name="responsible_person" class="form-control select_option" style="width:100%;">
                                                        <option></option>
                                                        @foreach($employees as $contact)
                                                            <option value="{{ $contact->id }}">{{ $contact->name }} {{ $contact->lastname }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="responsible_person">Der Out-Source-Typ</label>

                                                    <div class="card-body"> 
                                                    <ul class="list-unstyled mb-0">
                                                        <li class="d-inline-block mr-2">
                                                            <fieldset>
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" class="custom-control-input internal" checked name="outside_type" id="internal" checked="">
                                                                    <label class="custom-control-label" for="internal">Intern</label>
                                                                </div>
                                                            </fieldset>
                                                        </li>
                                                        <li class="d-inline-block mr-2">
                                                            <fieldset>
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" class="custom-control-input external" name="outside_type" id="external">
                                                                    <label class="custom-control-label" for="external">Extern</label>
                                                                </div>
                                                            </fieldset>
                                                        </li> 
                                                    </ul>
                                                </div>
                                                </div>
                                                <div class="form-group outside_company">
                                                    <label for="outside_company">Ausführende <code>Ausgelagert</code></label>
                                                    <select name="outside_company" class="form-control select_option " style="width:100%;"> 
                                                        <option></option>
                                                        @foreach($outside as $out)
                                                            <option value="{{ $out->id }}">{{ $out->company_name }} - {{ number_format($out->price, 2) }} € </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group outside_service ">
                                                    <label for="outside_service">Ausführende</label>
                                                    <select name="outside_service" class="form-control select_option" style="width:100%;">
                                                        <option></option> 
                                                        @foreach($employees as $contact)
                                                            <option value="{{ $contact->id }}">{{ $contact->name }} {{ $contact->lastname }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Dokument Name</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="position-relative has-icon-left">
                                                                <input type="text" id="file-icon" class="form-control" name="document_name" value="" placeholder="Dukument Name" data-np-intersection-state="visible">
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
                                                                <input type="text" id="file-icon" class="form-control" name="document_sum" value="" placeholder="Dukument Summe" data-np-intersection-state="visible">
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
                                                                <textarea name="document_note" class="form-control" value="" placeholder="Dukument Notiz" data-np-intersection-state="visible"></textarea>
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
                                                            <span>PDF</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="position-relative has-icon-left">  
                                                                <input type="file" name="document" class="form-control"> 
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                                                <button type="button" class="btn btn-primary" id="save-task-btn">Speichern</button>
                                            </div>
                                        </form> 
                                    </div>
                                </div>
                            </div>          
                                <!-- Sub Task Modal -->
                            <div class="modal fade" id="doneSubTaskModal" tabindex="-1" role="dialog" aria-labelledby="doneSubTaskModalTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="doneSubTaskModalTitle">Unteraufgabe</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                        <form id="doneSubTaskForm"> 
                                            @csrf
                                            <div class="modal-body">
                                                <input type="hidden" name="customer_id" value="">
                                                <input type="hidden" name="product_id" value="">
                                                <input type="hidden" name="alternative" value="">
                                                <input type="hidden" name="phase_id" value="">
                                                <input type="hidden" name="activities_id" value="">
                                                <input type="hidden" name="sub_task_id" value="">
                                                <input type="hidden" name="type" value="sub">
                                                <input type="hidden" name="contact_person" value="{{$current_user->id}}">

            
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Datum</span>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="position-relative has-icon-left">
                                                                <input type="date" class="form-control" name="done_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" placeholder="Datum" data-np-intersection-state="visible">
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
                                                                        <img src="{{ asset('images/employee/'.$current_user->image) }}" alt="{{ $current_user->name }}" height="32" width="32">
                                                                    </div>
                                                                    <label for="avatar" class="mt-0" style="font-size:14px">
                                                                        {{ $current_user->name }} {{ $current_user->lastname }}
                                                                    </label>
                                                                    <input type="hidden" name="contact_person" value="13" class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="responsible_person">Verantwortlicher</label>
                                                    <select name="responsible_person" class="form-control select_option" style="width:100%;">
                                                        @foreach($employees as $contact)
                                                            <option value="{{ $contact->id }}">{{ $contact->name }} {{ $contact->lastname }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="responsible_person">Der Out-Source-Typ</label>

                                                    <div class="card-body"> 
                                                    <ul class="list-unstyled mb-0">
                                                        <li class="d-inline-block mr-2">
                                                            <fieldset>
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" class="custom-control-input subinternal" name="outside_types" id="subinternal" checked="">
                                                                    <label class="custom-control-label" for="subinternal">Intern</label>
                                                                </div>
                                                            </fieldset>
                                                        </li>
                                                        <li class="d-inline-block mr-2">
                                                            <fieldset>
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" class="custom-control-input subexternal" name="outside_types" id="subexternal">
                                                                    <label class="custom-control-label" for="subexternal">Extern</label>
                                                                </div>
                                                            </fieldset>
                                                        </li> 
                                                    </ul>
                                                </div>
                                                </div>
                                                <div class="form-group outside_company">
                                                    <label for="outside_company">Ausführende <code>Ausgelagert</code></label>
                                                    <select name="outside_company" class="form-control select_option" style="width:100%;">
                                                        @foreach($outside as $out)
                                                            <option value="{{ $out->id }}">{{ $out->company_name }} - {{ number_format($out->price, 2) }} € </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group outside_service">
                                                    <label for="outside_service">Ausführende</label>
                                                    <select name="outside_service" class="form-control select_option" style="width:100%;">
                                                        @foreach($employees as $contact)
                                                            <option value="{{ $contact->id }}">{{ $contact->name }} {{ $contact->lastname }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Dokument Name</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="position-relative has-icon-left">
                                                                <input type="text" id="file-icon" class="form-control" name="document_name" value="" placeholder="Dukument Name" data-np-intersection-state="visible">
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
                                                                <input type="text" id="file-icon" class="form-control" name="document_sum" value="" placeholder="Dukument Summe" data-np-intersection-state="visible">
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
                                                                <textarea name="document_note" class="form-control" value="" placeholder="Dukument Notiz" data-np-intersection-state="visible"></textarea>
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
                                                            <span>PDF</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="position-relative has-icon-left">  
                                                                <input type="file" name="document" class="form-control"> 
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                                                <button type="button" class="btn btn-primary" id="save-sub-task-btn">Speichern</button>
                                            </div>
                                        </form> 
                                    </div>
                                </div>
                            </div>

                        </div>
                    
                

                </div>
        
                <!-- account setting page end -->

            </div>
        </div>
    </div>
    <!-- END: Content-->
 @endsection

@section('script')
<!-- Image Gallary :start  -->
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   
<script src="{{ asset('js/select2.min.js') }}"></script>  
<script>
    $(document).ready(function () {
        $('.select_option').select2({
            theme: 'bootstrap4'
        });
    });
</script>

<script>
    $(document).ready(function () {
        // Initially hide both dropdowns
        $('.outside_company').hide();
        $('.outside_service').hide();

        // Function to toggle dropdowns for the main modal based on radio button selection
        function toggleDropdowns() {
            if ($('.internal').is(':checked')) {
                $('.outside_service').show();  // Show internal service dropdown
                $('.outside_company').hide();  // Hide external company dropdown
            } else if ($('.external').is(':checked')) {
                $('.outside_company').show();  // Show external company dropdown
                $('.outside_service').hide();  // Hide internal service dropdown
            }
        }

        // Function to toggle dropdowns for the subtask modal based on radio button selection
        function toggleDropdown() {
            if ($('.subinternal').is(':checked')) {
                $('.outside_service').show();  // Show internal service dropdown
                $('.outside_company').hide();  // Hide external company dropdown
            } else if ($('.subexternal').is(':checked')) {
                $('.outside_company').show();  // Show external company dropdown
                $('.outside_service').hide();  // Hide internal service dropdown
            }
        }

        // Call the toggle function for the main modal when the page loads
        toggleDropdowns();

        // Call the toggle function for the subtask modal when the page loads
        toggleDropdown();

        // Attach event listener to the radio buttons in the main modal to toggle the dropdowns on change
        $('input[name="outside_type"]').change(function () {
            toggleDropdowns();
        });

        // Attach event listener to the radio buttons in the subtask modal to toggle the dropdowns on change
        $('input[name="outside_types"]').change(function () {
            toggleDropdown();
        });
    });
</script>
<!-- Energie:start  -->
 <script> 
    document.addEventListener("DOMContentLoaded", function () {
        // Attach click event to all nav links
        document.querySelectorAll(".nav-link").forEach((link) => {
            link.addEventListener("click", function () {
                // Remove 'selected-nav' from all nav-links
                document.querySelectorAll(".nav-link").forEach((item) => {
                    item.classList.remove("selected-nav");
                });

                // Add 'selected-nav' to the clicked link
                this.classList.add("selected-nav");

                const customerImageCard = document.getElementById("customer-image");
                const energyCard = document.getElementById("energie");

                // Hide cards initially
                customerImageCard.style.display = "none";
                energyCard.style.display = "none";

                // Handle "Bilder & Unterlagen"
                if (this.id === "image") {
                    const customer = this.getAttribute("data-customer");
                    const alternative = this.getAttribute("data-alternative");

                    if (!customer || !alternative) {
                        console.error("Missing customer or alternative data from nav link");
                        toastr.error("Daten fehlen.");
                        return;
                    }

                    // Update customer-image card data
                    customerImageCard.setAttribute("data-id", alternative);
                    customerImageCard.style.display = "block";

                    console.log("Nav link clicked: Bilder & Unterlagen");
                } 
                
                // Handle "Objekt & Energiedaten"
                else if (this.id === "energe") {
                    const customer = this.getAttribute("data-customer");
                    const alternative = this.getAttribute("data-alternative");

                    if (!customer || !alternative) {
                        console.error("Missing customer or alternative data from nav link");
                        toastr.error("Daten fehlen.");
                        return;
                    }

                    // Update energy card data
                    energyCard.setAttribute("data-id", alternative);
                    energyCard.style.display = "block";

                    console.log("Nav link clicked: Objekt & Energiedaten");

                    // Fetch and display energy data
                    fetch(`/get_object_data/${customer}/${alternative}`)
                        .then(response => response.json())
                        .then(data => {
                            if (!data || !data.id) {
                                toastr.error("Keine Daten gefunden.");
                                return;
                            }

                            // Populate form hidden fields
                            document.querySelector("[name='customer_id']").value = customer;
                            document.querySelector("[name='alternative_id']").value = alternative;

                            const labelMapping = {
                                objective: "objective",
                                house_year: "house_year",
                                number_we: "house_we",
                                number_stories: "number_stories",
                                living_space: "living_space",
                                unusable_space: "unusable_space",
                                number_people: "number_people",
                                roof_type: "roof_type",
                                roof_age: "roof_age",
                                tile_name: "tile_name",
                                roof_pitch: "roof_pitch",
                                roof_direction: "roof_direction",
                                heating_system_type: "heating_system_type",
                                heating_system_age: "heating_system_age",
                                heating_system_year: "heating_system_year",
                                heating_type: "heating_type",
                                installation_location: "installation_location",
                                installation_location_extra: "installation_location_extra",
                                annual_consumption: "annual_consumption",
                                annual_heating_energy_consumption: "annual_heating_energy_consumption",
                                annual_heating_energy_consumption_kwh: "annual_heating_energy_consumption_kwh",
                                electric_car: "electric_car",
                                electric_car_plan: "electric_car_plan",
                                car_kilo: "car_kilo"
                            };

                            // Populate labels
                            for (const [key, labelId] of Object.entries(labelMapping)) {
                                const label = document.getElementById(labelId);
                                if (label) {
                                    label.textContent = data[key] || "N/A";
                                }
                            }
                        })
                        .catch(error => {
                            console.error("Error fetching data:", error);
                            toastr.error("Fehler beim Abrufen der Daten.");
                        });
                }
            });
        });

        // Handle Edit Button Click
        document.querySelector(".edit_energy").addEventListener("click", () => {
            const formFields = [
                "id", "objective", "house_year", "number_we", "number_stories", "living_space",
                "unusable_space", "number_people", "roof_type", "roof_age", "tile_name",
                "roof_pitch", "roof_direction", "heating_system_type", "heating_system_age",
                "heating_system_year", "heating_type", "installation_location",
                "installation_location_extra", "annual_consumption", "annual_heating_energy_consumption",
                "annual_heating_energy_consumption_kwh", "electric_car", "electric_car_plan", "car_kilo"
            ];

            formFields.forEach(field => {
                const input = document.querySelector(`[name='${field}']`);
                const label = document.getElementById(field);

                if (input && label) {
                    input.value = label.textContent.trim() || "";
                }
            });

            $('#editenergy').modal('show');
        });

        // Handle Form Submission
        document.querySelector("form[action='{{ route('lead.info.data') }}']").addEventListener("submit", function (event) {
            event.preventDefault();

            const formData = new FormData(this);

            fetch("{{ route('lead.info.data') }}", {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Accept": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#editenergy').modal('hide');
                    toastr.success("Daten erfolgreich aktualisiert!");
                    location.reload();
                } else {
                    toastr.error("Fehler beim Speichern der Daten.");
                }
            })
            .catch(error => {
                console.error("Error submitting form:", error);
                toastr.error("Fehler beim Senden des Formulars.");
            });
        });
    });
</script> 
<!-- Energie:end  -->

    
<!-- Image Card: start:  -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Handle clicking any nav link
        document.querySelectorAll(".nav-link").forEach((link) => {
            link.addEventListener("click", function () {
                // Remove 'selected-nav' class from all nav links
                document.querySelectorAll(".nav-link").forEach((nav) => {
                    nav.classList.remove("selected-nav");
                });

                // Add 'selected-nav' class to the clicked link
                this.classList.add("selected-nav");

                // Show the customer-image card only when #image is clicked
                const customerImageCard = document.getElementById("customer-image");

                if (this.id === "image") {
                    const customer = this.getAttribute("data-customer");
                    const alternative = this.getAttribute("data-alternative");

                    if (!customer || !alternative) {
                        console.error("Missing customer or alternative data from nav link");
                        return;
                    }

                    // Update customer-image card data
                    customerImageCard.setAttribute("data-id", alternative);
                    customerImageCard.style.display = "block";

                    console.log("Nav link clicked:");
                    console.log("Set customer:", customer);
                    console.log("Set alternative:", alternative);

                    // Update upload button data
                    const uploadButton = document.querySelector(".upload-image");
                    uploadButton.setAttribute("data-customer", customer);
                    uploadButton.setAttribute("data-alternative", alternative);
                } else {
                    // Hide the customer-image card if another nav link is clicked
                    customerImageCard.style.display = "none";
                }
            });
        }); 

        // Handle clicking the upload button
        document.querySelectorAll(".upload-image").forEach((button) => {
            button.addEventListener("click", function () {
                const customerId = button.getAttribute("data-customer");
                const alternativeId = button.getAttribute("data-alternative");

                // Validate that both attributes are set
                if (!customerId || !alternativeId) {
                    console.error("Missing customerId or alternativeId in upload button");
                    alert("Kundennummer oder Alternativnummer fehlt!");
                    return;
                }

                // Update hidden inputs in the modal form
                document.querySelector("#upload-customer-image input[name='customer_id']").value = customerId;
                document.querySelector("#upload-customer-image input[name='alternative_id']").value = alternativeId;

                console.log("Upload button clicked:");
                console.log("customer_id:", customerId);
                console.log("alternative_id:", alternativeId);

                // Load article groups
                loadArticleGroups(customerId, alternativeId);
            });
        });

        // Function to load article groups
        function loadArticleGroups(customerId, alternativeId) {
            const url = `/get_lead_product_list/${customerId}/${alternativeId}`;
            console.log("Loading article groups from URL:", url);

            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    console.log("Received article groups response:", response);

                    const articleGroupSelect = $("#article_group");
                    articleGroupSelect.find("option:not(:first)").remove(); // Clear existing options

                    response.forEach(function (item) {
                        articleGroupSelect.append(new Option(item.article_group, item.id));
                    });
                },
                error: function (xhr, status, error) {
                    console.error("Error loading article groups:", error);
                    console.log("Error details:", xhr.responseText);
                }
            });
        }
        }); 
</script>
  
    <!-- // Handle clicking the customer image nav link -->

    <script>
        const imageBasePath = "{{ asset('images/customers') }}";
    </script>

<script>
    // Attach functions to the global scope
window.makeEditable = function (element) {
    const input = element.nextElementSibling;
    input.style.display = "block";
    element.style.display = "none";
    input.focus();
};

window.handleEnter = function (event) {
    if (event.key === "Enter") {
        const input = event.target;
        const imageId = input.getAttribute("data-id");
        const imageName = input.value.trim();

        if (!imageName) {
            alert("Bildname darf nicht leer sein.");
            input.style.display = "none";
            input.previousElementSibling.style.display = "block";
            return;
        }

        // Make a POST request to save the new image name
        fetch(`/customer_image_name`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                id: imageId,
                image_name: imageName,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                console.log("Rename Response:", data);
                if (data.success) {
                    alert("Bildname erfolgreich aktualisiert.");
                    input.previousElementSibling.textContent = imageName;
                    input.previousElementSibling.style.display = "block";
                    input.previousElementSibling.setAttribute("data-image-name", imageName);
                } else {
                    alert("Fehler beim Aktualisieren des Bildnamens.");
                }
                input.style.display = "none";
            })
            .catch((error) => {
                console.error("Error updating image name:", error);
                alert("Fehler beim Aktualisieren des Bildnamens.");
                input.style.display = "none";
                input.previousElementSibling.style.display = "block";
            });
    }
};

</script>
 
<script>
    document.addEventListener("DOMContentLoaded", function () {
         // Handle clicking the customer image nav link
    document.querySelectorAll(".nav-link#image").forEach((link) => {
        link.addEventListener("click", function () {
            const customer = this.getAttribute("data-customer");
            const alternative = this.getAttribute("data-alternative");
            const customerImageCard = document.getElementById("customer-image");

            if (!customer || !alternative) {
                console.error("Missing customer or alternative data from nav link");
                return;
            }

            customerImageCard.setAttribute("data-id", alternative);

            const uploadButton = document.querySelector(".upload-image");
            uploadButton.setAttribute("data-customer", customer);
            uploadButton.setAttribute("data-alternative", alternative);

            console.log("Nav link clicked. Loading images and documents...");
            fetchImagesAndDocuments(customer, alternative);
        });
    });

        // Fetch Images and Documents
    function fetchImagesAndDocuments(customer, alternative) {
        const url = `/customer_image_get/${customer}/${alternative}`;
        console.log("Fetching data from:", url);

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error("Network response was not ok");
                return response.json();
            })
            .then(data => {
                console.log("Fetched Response:", data);

                const images = data.data || [];
                const photoContainer = document.querySelector("#photo_image");
                const documentContainer = document.querySelector("#document_container");

                if (!photoContainer || !documentContainer) {
                    console.error("Containers not found in DOM");
                    return;
                }

                photoContainer.innerHTML = "";
                documentContainer.innerHTML = "";

                if (images.length === 0) {
                    alert("Keine Bilder oder Unterlagen gefunden.");
                    return;
                }

                images.forEach((item) => {
                    const fileType = item.file_path.split('.').pop().toLowerCase();
                    const imagePath = `${imageBasePath}/${item.image}`;

                    if (['jpeg', 'jpg', 'png', 'gif'].includes(fileType)) {
                        const imageHtml = `
                            <div class="col-md-3" data-id="${item.id}">
                                <div class="card-content">
                                    <img class="card-img-top img-fluid open-modal" src="${imagePath}" alt="${item.image}" data-image="${imagePath}">
                                    <div class="card-body p-0">
                                        <h6 class="card-title edit_image_name mt-1" data-id="${item.id}" ondblclick="makeEditable(this)">
                                            ${item.image_name}
                                        </h6>
                                        <input type="text" class="form-control edit_input" 
                                            data-id="${item.id}" 
                                            value="${item.image_name}" 
                                            style="display:none;" 
                                            onkeypress="handleEnter(event)">
                                    </div>
                                    <div class="card-footer p-0 mt-1"> 
                                        <button type="button" class="btn btn-icon btn-flat-danger delete-btn" data-id="${item.id}">
                                            <i class="feather icon-trash"></i> Löschen
                                        </button>
                                    </div>
                                </div>
                            </div>`;


                        photoContainer.insertAdjacentHTML("beforeend", imageHtml);
                    } else if (['pdf', 'docx', 'xlsx', 'doc'].includes(fileType)) {
                        const docIcon = fileType === "pdf" ? "fa-file-pdf-o" :
                            ["docx", "doc"].includes(fileType) ? "fa-file-word-o" :
                            fileType === "xlsx" ? "fa-file-excel-o" : "";

                        const docHtml = `
                            <div class="col-md-12">
                                <div class="card-content">
                                    <div class="file-preview" style="text-align: center; padding: 10px;">
                                        ${fileType === "pdf" 
                                            ? `<iframe src="${imagePath}" frameborder="0" style="width: 100%; height: 150px;"></iframe>` 
                                            : `<i class="fa ${docIcon}" style="font-size: 50px;"></i>`}
                                    </div>
                                   <div class="card-body">
                                        <h6 class="card-title edit_image_name" data-id="${item.id}" data-image-name="${item.image_name}" ondblclick="makeEditable(this)">
                                            ${item.image_name}
                                        </h6>  
                                          <input type="text" class="form-control edit_input" 
                                            data-id="${item.id}" 
                                            value="${item.image_name}" 
                                            style="display:none;" 
                                            onkeypress="handleEnter(event)"> 
                                        
                                    </div>
                                    <div class="card-footer"> 
                                        <button type="button" class="btn btn-icon btn-flat-danger delete-btn" data-id="${item.id}">
                                            <i class="feather icon-trash"></i> Löschen
                                        </button>
                                        <button type="button" class="btn btn-icon btn-flat-primary open-file" data-id="${item.id}" data-file-url="${imagePath}">
                                            <i class="feather icon-eye"></i> Offen
                                        </button>
                                    </div>
                                </div>
                            </div>`;
                        documentContainer.insertAdjacentHTML("beforeend", docHtml);
                    }
                });

                document.querySelectorAll(".delete-btn").forEach(button => {
                    button.addEventListener("click", deleteImage);
                });
            })
            .catch(error => {
                console.error("Error fetching data:", error);
                alert("Fehler beim Abrufen der Daten. Bitte versuchen Sie es erneut.");
            });
    }
 

    // File Upload
     Dropzone.autoDiscover = false;

    const myDropzone = new Dropzone("#upload-customer-image", {
        url: "/customer_upload", // Laravel route for uploading files
        method: "POST",
        paramName: "file", // Backend expects this name
        maxFilesize: 2, // 2 MB max file size
        acceptedFiles: ".jpg,.jpeg,.png,.pdf,.doc,.docx", // Accepted file types
        addRemoveLinks: true, // Add remove links for files
        uploadMultiple: true, // Handle multiple uploads
        parallelUploads: 10, // Number of files to process simultaneously
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"), // Include CSRF token
        },
        init: function () {
            this.on("success", function (file, response) {
                console.log("File uploaded successfully:", response);
                toastr.success('Datei erfolgreich hochgeladen'); // Success message
            });

            this.on("error", function (file, errorMessage) {
                console.error("File upload error:", errorMessage);
                toastr.error("Error uploading file.");
            });

            this.on("removedfile", function (file) {
                console.log("File removed:", file);
            });
        },
    });

   // Delete Image Function
        function deleteImage() {
            const imageId = this.getAttribute("data-id");
            console.log("Deleting Image ID:", imageId);

            // Confirm deletion with SweetAlert
            Swal.fire({
                title: "Sind Sie sicher?",
                text: "Möchten Sie dieses Bild wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ja, löschen!",
                cancelButtonText: "Abbrechen",
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/customer_image_destroy/${imageId}`, {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        },
                    })
                        .then((response) => response.json())
                        .then((data) => {
                            console.log("Delete Response:", data);
                            if (data.success) {
                                Swal.fire(
                                    "Gelöscht!",
                                    "Das Bild wurde erfolgreich gelöscht.",
                                    "success"
                                );
                                this.closest(".col-md-3, .col-md-5").remove();
                            } else {
                                Swal.fire(
                                    "Fehler!",
                                    "Das Bild konnte nicht gelöscht werden.",
                                    "error"
                                );
                            }
                        })
                        .catch((error) => {
                            console.error("Error deleting image:", error);
                            Swal.fire(
                                "Fehler!",
                                "Beim Löschen des Bildes ist ein Fehler aufgetreten.",
                                "error"
                            );
                        });
                }
            });
        }

    
 

    });

 </script>

 <!-- image dialog:  -->
  <script>

    // Attach event listener to dynamically created images
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("open-modal")) {
            const imageSrc = event.target.getAttribute("data-image");
            console.log("Opening Image Modal for:", imageSrc);

            // Set the modal image source
            const modalImage = document.getElementById("modalImage");
            modalImage.src = imageSrc;

            // Reset zoom range value
            const zoomRange = document.getElementById("image_zoom");
            zoomRange.value = 1;
            modalImage.style.transform = `scale(${zoomRange.value})`;

            // Show the modal
            $("#imageModal").modal("show");
        }
    });

    // Handle zoom range change
    document.getElementById("image_zoom").addEventListener("input", function () {
        const zoomValue = this.value;
        const modalImage = document.getElementById("modalImage");

        console.log("Zoom Level:", zoomValue);

        // Apply zoom
        modalImage.style.transform = `scale(${zoomValue})`;
    }); 
  </script>
    

<script>
     document.addEventListener("click", function (event) {
    const openButton = event.target.closest(".open-file");
    if (openButton) {
        const fileUrl = openButton.getAttribute("data-file-url");

        if (!fileUrl) {
            console.error("File URL not found");
            alert("Dokumentvorschau nicht verfügbar.");
            return;
        }

        console.log("Opening Document Modal for:", fileUrl);

        const documentViewerBody = document.getElementById("document_viewer_body");
        const downloadButton = document.getElementById("download_button");

        // Clear previous content
        documentViewerBody.innerHTML = "";

        // Check file type based on extension
        const fileExtension = fileUrl.split('.').pop().toLowerCase();

        if (fileExtension === "pdf") {
            documentViewerBody.innerHTML = `<iframe src="${fileUrl}" frameborder="0" style="width: 100%; height: 600px;"></iframe>`;
        } else if (["docx", "doc"].includes(fileExtension)) {
            documentViewerBody.innerHTML = `
                <div style="font-size: 50px; color: #007bff; text-align: center;">
                    <i class="fa fa-file-word-o"></i>
                    <p>Word-Dokument: Herunterladen erforderlich</p>
                </div>`;
        } else if (fileExtension === "xlsx") {
            documentViewerBody.innerHTML = `
                <div style="font-size: 50px; color: #28a745; text-align: center;">
                    <i class="fa fa-file-excel-o"></i>
                    <p>Excel-Datei: Herunterladen erforderlich</p>
                </div>`;
        } else {
            documentViewerBody.innerHTML = `
                <div style="color: red; text-align: center;">
                    <p>Dokumentvorschau nicht verfügbar für Dateityp: ${fileExtension || "Unbekannt"}</p>
                </div>`;
        }

        // Set download link
        downloadButton.href = fileUrl;
        downloadButton.download = fileUrl.split('/').pop();

        // Show the modal
        $("#customer_document").modal("show");
    }
});

</script>
 

<!-- Image Card: end:  -->
<script>
    $(document).on('click', '.feather.icon-file', function () {
        const alternativeId = $(this).data('alternative');
        const customerId = $(this).data('customer');
        const productId = $(this).data('product');
        const serviceId = $(this).data('service');
        const url = `/customer/phase/get/${customerId}/${alternativeId}/${productId}`;

        // Update hidden input fields with the fetched data
        $('input[name="project_alternative"]').val(alternativeId);
        $('input[name="project_customer"]').val(customerId);
        $('input[name="project_product"]').val(productId);
        $('input[name="project_service"]').val(serviceId);

        $('#project .progress_card').html('<div>Loading...</div>');

        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if (response.length === 0) {
                    Swal.fire({
                        title: 'No Phase Steps Found',
                        text: 'You have not created the phase steps for this product.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Create Phase Steps',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = $('<form>', {
                                action: '/customer/phase/manage',
                                method: 'POST'
                            });

                            form.append($('<input>', {
                                type: 'hidden',
                                name: '_token',
                                value: $('meta[name="csrf-token"]').attr('content')
                            }));

                            form.append($('<input>', {
                                type: 'hidden',
                                name: 'alternative_id',
                                value: alternativeId
                            }));

                            form.append($('<input>', {
                                type: 'hidden',
                                name: 'customer',
                                value: customerId
                            }));

                            form.append($('<input>', {
                                type: 'hidden',
                                name: 'product',
                                value: productId
                            }));
                            form.append($('<input>', {
                                type: 'hidden',
                                name: 'service',
                                value: serviceId
                            }));

                            $('body').append(form);
                            form.submit();
                        }
                    });

                    $('#project .progress_card').html('<div>No data available</div>');
                } else {
                    let progressHTML = '<div class="step-progress">';
                    let dynamicStyles = '<style>';

                    response.forEach((phase, index) => {
                        const stepIndex = index + 1;
                        const color = phase.color || '#ccc';

                        progressHTML += `
                            <div class="step" style="background-color: ${color};">
                                <span>${phase.phase_name}</span>
                                ${phase.jump_steps === 'complete' ? '<i class="feather icon-check-square"></i>' : ''}
                            </div>`;

                        dynamicStyles += `
                            .step:nth-child(${stepIndex}):after {
                                border-left-color: ${color};
                            }`;
                    });

                    progressHTML += '</div>';
                    dynamicStyles += '</style>';

                    $('#project .progress_card').html(progressHTML);
                    $('head').append(dynamicStyles);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error fetching phases:', error);
                $('#project .progress_card').html('<div>Error loading phases. Please try again.</div>');
            }
        });

        $('#project').show();
    });

    $('#project').hide();
</script>
<script>
    $(document).on('click', '.feather.icon-file', function () {
        const alternativeId = $(this).data('alternative');
        const customerId = $(this).data('customer');
        const productId = $(this).data('product');
        const serviceId = $(this).data('service');
        const phaseUrl = `/customer/phase/get/${customerId}/${alternativeId}/${productId}`;

        // Update hidden input fields with the fetched data
        $('input[name="project_alternative"]').val(alternativeId);
        $('input[name="project_customer"]').val(customerId);
        $('input[name="project_product"]').val(productId);
        $('input[name="project_service"]').val(serviceId);

        $('.phases').html('<div>Loading...</div>');

        // Fetch Phases
        $.ajax({
            url: phaseUrl,
            type: 'GET',
            success: function (phases) {
                console.log('Phases Response:', phases); // Log the phase data

                if (phases.length === 0) {
                    Swal.fire({
                        title: 'No Phase Steps Found',
                        text: 'You have not created the phase steps for this product.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Create Phase Steps',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = $('<form>', {
                                action: '/customer/phase/manage',
                                method: 'POST'
                            });

                            form.append($('<input>', {
                                type: 'hidden',
                                name: '_token',
                                value: $('meta[name="csrf-token"]').attr('content')
                            }));

                            form.append($('<input>', {
                                type: 'hidden',
                                name: 'alternative_id',
                                value: alternativeId
                            }));

                            form.append($('<input>', {
                                type: 'hidden',
                                name: 'customer',
                                value: customerId
                            }));

                            form.append($('<input>', {
                                type: 'hidden',
                                name: 'product',
                                value: productId
                            }));
                            form.append($('<input>', {
                                type: 'hidden',
                                name: 'service',
                                value: serviceId
                            }));

                            $('body').append(form);
                            form.submit();
                        }
                    });

                    $('.phases').html('<div>No data available</div>');
                } else {
                    let accordionHTML = '<section class="phases">';
                    phases.forEach((phase, index) => {
                        const activitiesUrl = `/customer/get/product/${phase.phase_id}/${productId}/${alternativeId}/${serviceId}`;
                        console.log(`Fetching activities for phase ${phase.phase_id} from ${activitiesUrl}`); // Log the URL

                        accordionHTML += `
                            <div class="accordion" id="accordionExample${index}">
                                <div class="card">
                                    <div class="card-header" id="heading${index}">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link collapse_card" type="button" data-toggle="collapse" data-target="#collapse${index}" aria-expanded="true" aria-controls="collapse${index}">
                                                <h4 style="font-weight: bold;" class="primary">
                                                    <i class="icon-toggle feather icon-plus"></i> ${phase.phase_name}
                                                </h4>
                                            </button>
                                        </h5>
                                    </div>

                                    <div id="collapse${index}" class="collapse" aria-labelledby="heading${index}" data-parent="#accordionExample${index}">
                                        <div class="card-body">
                                            <ul class="todo-task-list-wrapper media-list mt-1" id="activities-list-${index}">
                                                <li>Loading activities...</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>`;

                        // Fetch activities for the current phase
                       $.ajax({
                            url: activitiesUrl,
                            type: 'GET',
                            success: function (activitiesResponse) {
                                console.log(`Activities Response for Phase:`, activitiesResponse); // Log activities response
                                const activitiesList = activitiesResponse.tasks.map(task => `
                                    <li class="todo-item">
                                        <div class="todo-title-wrapper d-flex justify-content-between mb-50">
                                            <div class="todo-title-area d-flex align-items-center">
                                                <div class="title-wrapper d-flex">
                                                    <div class="vs-checkbox-con">
                                                        <input type="checkbox" ${task.done ? 'checked' : ''}>
                                                        <span class="vs-checkbox vs-checkbox-sm">
                                                            <span class="vs-checkbox--check">
                                                                <i class="vs-icon feather icon-check"></i>
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <h6 class="todo-title mt-50 mx-50">${task.title}</h6>
                                                </div>
                                                <div class="chip-wrapper">
                                                    <div class="chip mb-0">
                                                        <div class="chip-body">
                                                            <span class="chip-text" data-value="${task.section_name}">
                                                                <span class="bullet bullet-primary bullet-xs"></span> ${task.section_name}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="float-right todo-item-action d-flex">
                                                ${task.cname ? `
                                                <a class="todo-item-info mr-1">Verfasser: 
                                                    <img class="media-object rounded-circle" data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" title="${task.cname} ${task.clastname}" 
                                                    src="${task.cimage ? '/images/employee/' + task.cimage : '/images/default-avatar.png'}" alt="Avatar" height="30" width="30"> 
                                                </a>` : ''}
                                                ${task.rname ? `
                                                <a class="todo-item-info mr-1">Verantwortlich: 
                                                    <img class="media-object rounded-circle" data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" title="${task.rname} ${task.rlastname}" 
                                                    src="${task.rimage ? '/images/employee/' + task.rimage : '/images/default-avatar.png'}" alt="Avatar" height="30" width="30"> 
                                                </a>` : ''}
                                                ${task.osname ? `
                                                <a class="todo-item-info mr-1">Ausführende: 
                                                    <img class="media-object rounded-circle" data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" title="${task.osname} ${task.oslastname}" 
                                                    src="${task.osimage ? '/images/employee/' + task.osimage : '/images/default-avatar.png'}" alt="Avatar" height="30" width="30"> 
                                                </a>` : ''}
                                            </div>
                                        </div>
                                        <p class="todo-desc truncate mb-0">${task.description}</p>
                                    </li>
                                `).join('');

                                $(`#activities-list-${index}`).html(activitiesList);
                            },
                            error: function () {
                                console.error(`Error fetching activities for phase`); // Log error
                                $(`#activities-list-${index}`).html('<li>Error loading activities.</li>');
                            }
                        });

                    });
                    accordionHTML += '</section>';

                    $('.phases').html(accordionHTML);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error fetching phases:', error); // Log phase fetch error
                $('.phases').html('<div>Error loading phases. Please try again.</div>');
            }
        });


        $('#project').show();
    });

    $('#project').hide();
</script>



<!-- Loading and fetching the project task managment data: start -->
 
 
@endsection
     
 
 