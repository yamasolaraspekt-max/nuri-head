 @extends('admin.layouts.app')
 @section('title')
 KUNDE PROFILE
 @endsection

 @section('style')
 <link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" />
<script src="{{ asset('js/dropzone.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

    <meta name="csrf-token" content="{{ csrf_token() }}"> 

<!-- Include stylesheet -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>   
 
<link rel="stylesheet" type="text/css" href="{{ asset('css/customer_product.css')}}"> 
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script> 
  
<style>
    .section_title {
      border-left: 8px solid #94c11f;
    color: #94c11f;
    padding: 6px;
    }
</style>

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
                            <h2 class="content-header-title float-left mb-0">Dachkonfiguration</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a>
                                    </li> 
                                    <li class="breadcrumb-item"><a href="{{ url('/new_lead_view') }}">Kunde</a>
                                    </li>  
                                    <li class="breadcrumb-item"><a href="{{ url('/new_lead_profile/'.$customer->id) }}"> {{ $customer->name }} {{ $customer->lastname }} </a>
                                    </li>
                                    <li class="breadcrumb-item active"> <a href="{{ url('/new_lead_profile_object/'.$customer->id.'/'.$alternative->id) }}"> {{ $alternative->object_name }}  </a>
                                    </li>
                                    <li class="breadcrumb-item active"> Dachkonfiguration
                                    </li>
                                </ol>
                            </div>
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
                        <div class="col-md-3 col-sm-3 ">
                            <div class="card mb-1">
                                <div class="card-header p-1" >
                                    <div class="col-12"> 
                                        <p style="font-size: 12px;font-weight: bold; margin-bottom:0; position:absolute"> 
                                            <a href="{{ url('new_lead_edit/'.$customer->id.'/'.$alternative->id)}}"><i class="feather icon-edit primary"></i></a> 
                                            <a href="{{url('new_lead_profile_object/'.$alternative->lead_id.'/'.$alternative->id )}}">
                                                    {{ strtoupper($alternative->object_name) }} 
                                            </a>
                                        </p> 
                                        <p style="font-size: 11px;font-weight: bold; margin-top:14px;"> <small>
                                                {{ $alternative->street }}  {{ $alternative->postcode }} - {{ $alternative->city }}
                                        </small></p> 
                                    </div> 
                                    <div class="col-12 pl-0" style="text-align: left"> 
                                        @php
                                            // Filter screenshots to get only the ones belonging to this $alternative->id
                                            $filteredScreenshots = $screenshots->where('alternative_id', $alternative->id);
                                            $firstImage = $filteredScreenshots->first(); // Get the first image of the set
                                        @endphp

                                        @if ($firstImage)
                                            <div class="col-12">
                                                <!-- Clickable Thumbnail (First Image) -->
                                                <img src="{{ asset('images/customers/'.$firstImage->image) }}" 
                                                    alt="" class="" 
                                                    style="width: 130px; cursor: pointer;" 
                                                    data-toggle="modal" data-backdrop="false" data-target="#imageModal{{$alternative->id}}"> 
                                            </div> 

                                            <div class="modal fade text-left" id="imageModal{{$alternative->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true" style="display: none;">
                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="myModalLabel4">Galerie</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <i class="feather icon-x"></i>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>
                                                                {{ $alternative->street }}  {{ $alternative->postcode }} - {{ $alternative->city }}
                                                            </p>

                                                            @foreach ($filteredScreenshots as $profile)
                                                                <img src="{{ asset('images/customers/'.$profile->image) }}" 
                                                                    alt="" class="img-fluid m-2 gallery-image" 
                                                                    style="width: 100px; cursor: pointer;" 
                                                                    onclick="showFullSize('{{ asset('images/customers/'.$profile->image) }}', '{{$profile->image_name}}')">
                                                            @endforeach
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal">Schließen</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
    
                                    </div>
                                
                                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3" style=" font-size: 25px;"></i></a>
                                    <div class="heading-elements">
                                        <ul class="list-inline mb-0">
                                            <li><a data-action="collapse" class=""><i class="feather icon-chevron-down card-down" style=" font-size: 25px;"></i></a></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="card-header mb-0 pb-0 pt-0" > 
                                    <div class="col-6 "> 
                                        <p class="pb-0">Objektart: {{ $alternative->objective ?? '' }}</p> 
                                    </div>  
                                </div>
                                    
                                <div class="card-content" style=""> 
                                    <div class="card-body">
                                        <p style="font-size: 12px;font-weight: bold;">PRODUCKTE & DIENSTLEISTUNGEN</p>
                                        <div>
                                            <table class="table">  
                                                @php
                                                        $product_list = collect($productList);
                                                    @endphp

                                                @foreach ($product_list->where('customer_id', $customer->id)->where('alternative_id', $alternative->id)->unique(fn($product) => $product->product_id.'-'.$product->alternative_id) as $product)
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
                                                        
                                                            
                                                            @if($alternative->stage == 'lead')
                                                            <i class="feather icon-heart " style="font-size: 24px;"></i> 
                                                            @elseif($alternative->stage == 'plan')
                                                            <img src=" {{ asset('images/dashboard/icon_gears.svg') }}" alt="" class="p-0" style="width: 52px;">
                                                            @elseif($alternative->stage == 'offer')
                                                            <img src=" {{ asset('images/dashboard/icon_document.svg') }}" alt="" class="p-0" style="width: 52px;">
                                                            @elseif($alternative->stage == 'deal')
                                                            <img src=" {{ asset('images/dashboard/icon_memoboard.svg') }}" alt="" class="p-0" style="width: 52px;">
                                                            @elseif($alternative->stage == 'project')
                                                            <img src=" {{ asset('images/dashboard/icon_memoboard.svg') }}" alt="" class="p-0" style="width: 52px;">
                                                            @elseif($alternative->stage == 'end')
                                                            <img src=" {{ asset('images/dashboard/icon_target.svg') }}" alt="" class="p-0" style="width: 52px;">
                                                            @elseif($p_status == 'end')
                                                                {{ $alternative->project_date }}
                                                            @else
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
                                                 
                                </div>
                                
                            </div> 
                        </div> 

                        <div class="col-md-9"> 
                            <div class="cards">   
                                <div class="row"> 
                                    <div class="col-md-2 image">
                                        <img src="{{ asset('images/articles/pv.png') }}"
                                            alt="alternative" style="width: 70px;">
                                    </div>
                                    <div class="col-md-7 contents">
                                        <input type="hidden" name="customer_id"
                                            value="{{ $customer->id }}">
                                        <h2 class="title" style="color: #74b2d3">PHOTOVOLTAIK</h2>
                                        <p  style="color:rgb(65, 65, 65)">Dachkonfiguration</p> 
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-warning waves-effect waves-light  " data-toggle="modal" data-target="#new_roof">
                                            Neue Dach
                                        </button>  
                                    </div> 
                                </div>  
                                  <div class="row mt-1" id="card_row_preview">
                                    <div class="table-responsive">
                                        <table class="table" id="roof_table">
                                            <thead>
                                                <tr style="background:white; ">   
                                                    <th>Bezeichnung</th>  
                                                    <th>Dacktyp</th>  
                                                    <th>Dacheindeckung</th> 
                                                    <th>Beschreibung</th>  
                                                    <th width="2">Bearbeiten</th> 
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($roofs as $roof_d)
                                                        <tr> 
                                                            <td>{{$roof_d->designation}}</td>
                                                            <td>{{$roof_d->roof}}</td>
                                                            <td>
                                                                <div class="avatar mr-1 avatar-lg">
                                                                        <img src="{{ asset('images/products/'.$roof_d->image) }}" alt="avtar img holder">
                                                                    </div>
                                                                {{$roof_d->product}}
                                                            </td>
                                                            <td>
                                                                <small>
                                                                    <p>Neigung: {{$roof_d->tilt}}</p>
                                                                    <p>Aufdachdämmung: {{$roof_d->roof_insulation ?? 'Nein'}}</p>
                                                                    <p>Zwischen sparrendämmung: {{$roof_d->between_rafter_insulation ?? 'Nein'}}</p>
                                                                    <p>Asbesthaltig: {{$roof_d->asbestos ?? 'Nein'}}</p>
                                                                    <p>Dachsanierung notwendig: {{$roof_d->roof_renovation	?? 'Nein'}}</p>
                                                                </small>
                                                            </td>
                                                          <td>
                                                              <button type="button" class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1 waves-effect waves-light " data-toggle="modal" data-target="#edit_roof{{$roof_d->id}}">
                                                                <i class="feather icon-edit"></i>
                                                            </button>  
                                                                <div class="modal fade text-left" id="edit_roof{{$roof_d->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h4 class="modal-title" id="myModalLabel17">Bearbeiten</h4>
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">×</span>
                                                                                </button>
                                                                            </div>
                                                                                <form id="roof_form_update">
                                                                                    @csrf
                                                                                    <div class="modal-body"> 
                                                                                        <input type="hidden" name="customer_id" value="{{$customer->id}}">
                                                                                        <input type="hidden" name="alternative_id" value="{{$alternative->id}}"> 
                                                                                        <input type="hidden" name="id" value="{{$roof_d->id}}"> 
                                                                                            <div class="col-12">
                                                                                                <div class="form-group row"> 
                                                                                                    <div class="col-md-2">
                                                                                                        <span>Bezeichnung</span>
                                                                                                    </div>
                                                                                                    <div class="col-md-10">
                                                                                                        <input type="text" class="form-control"
                                                                                                            name="designation[0]" value="{{$roof_d->designation}}">
                                                                                                    </div>
                                                                                                        
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="col-12" style="margin-bottom: 40px;">
                                                                                                <div class="form-group row">
                                                                                                    <div class="col-md-12">
                                                                                                        <ul class="list-unstyleds mb-0">
                                                                                                            <li class="d-inline-block mr-1">
                                                                                                                <fieldset>
                                                                                                                    <img src="{{ asset('images/roofs/Satteldach.png') }}"
                                                                                                                        alt="" srcset=""
                                                                                                                        style="width: 60px;"
                                                                                                                        for="roof_Satteldach_0">
                                                                                                                    <div
                                                                                                                        class="custom-control custom-radio">
                                                                                                                        <input type="radio"
                                                                                                                            class="custom-control-input"
                                                                                                                            name="roof[0]"
                                                                                                                            id="roof_Satteldach_0"
                                                                                                                            value="Satteldach" @if($roof_d->roof=="Satteldach") checked  @endif>
                                                                                                                        <label class="custom-control-label"
                                                                                                                            for="roof_Satteldach_0">Satteldach</label>
                                                                                                                    </div>
                                                                                                                </fieldset>
                                                                                                            </li>
                                                                                                            <li class="d-inline-block mr-1">
                                                                                                                <fieldset>
                                                                                                                    <img src="{{ asset('images/roofs/Flachdach.png') }}"
                                                                                                                        alt="" srcset=""
                                                                                                                        style="width: 60px;"
                                                                                                                        for="roof_Flachdach_0">
                                                                                                                    <div
                                                                                                                        class="custom-control custom-radio">
                                                                                                                        <input type="radio"
                                                                                                                            class="custom-control-input"
                                                                                                                            name="roof[0]"
                                                                                                                            id="roof_Flachdach_0"
                                                                                                                            value="Flachdach" @if($roof_d->roof=="Flachdach") checked @endif>
                                                                                                                        <label class="custom-control-label"
                                                                                                                            for="roof_Flachdach_0">Flachdach</label>
                                                                                                                    </div>
                                                                                                                </fieldset>
                                                                                                            </li>
                                                                                                            <li class="d-inline-block mr-1">
                                                                                                                <fieldset>
                                                                                                                    <img src="{{ asset('images/roofs/Garage.png') }}"
                                                                                                                        alt="" srcset=""
                                                                                                                        style="width: 60px;"
                                                                                                                        for="roof_Garage_0">
                                                                                                                    <div
                                                                                                                        class="custom-control custom-radio">
                                                                                                                        <input type="radio"
                                                                                                                            class="custom-control-input"
                                                                                                                            name="roof[0]"
                                                                                                                            id="roof_Garage_0"
                                                                                                                            value="Garage" @if($roof_d->roof=="Garage") checked @endif>
                                                                                                                        <label class="custom-control-label"
                                                                                                                            for="roof_Garage_0">Garage</label>
                                                                                                                    </div>
                                                                                                                </fieldset>
                                                                                                            </li>
                                                                                                            <li class="d-inline-block mr-1">
                                                                                                                <fieldset>
                                                                                                                    <img src="{{ asset('images/roofs/Carport.png') }}"
                                                                                                                        alt="" srcset=""
                                                                                                                        style="width: 60px;"
                                                                                                                        for="roof_Carport_0">
                                                                                                                    <div
                                                                                                                        class="custom-control custom-radio">
                                                                                                                        <input type="radio"
                                                                                                                            class="custom-control-input"
                                                                                                                            name="roof[0]"
                                                                                                                            id="roof_Carport_0"
                                                                                                                            value="Carport" @if($roof_d->roof=="Carport") checked @endif>
                                                                                                                        <label class="custom-control-label"
                                                                                                                            for="roof_Carport_0">Carport</label>
                                                                                                                    </div>
                                                                                                                </fieldset>
                                                                                                            </li>
                                                                                                        </ul>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="col-12">
                                                                                                <div class="form-group row">
                                                                                                    <div class="col-md-2">
                                                                                                        <p class="bold">Dacheindeckung</p>
                                                                                                    </div>
                                                                                                    <div class="col-md-4">
                                                                                                        <select class="roof_covering" name="roof_covering[0]"
                                                                                                            style="width:100%">
                                                                                                            @foreach ($tiles as $tile)
                                                                                                            <option value="{{ $tile->product_id }}"
                                                                                                                data-image="{{ asset('images/products/'.$tile->image) }}"
                                                                                                                data-roof-type="{{ $tile->roof_type }}"
                                                                                                                @if($roof_d->roof_covering == $tile->product_id) selected @endif>
                                                                                                                {{ $tile->product }} ->
                                                                                                                {{ $tile->roof_type }}
                                                                                                            </option>
                                                                                                            @endforeach
                                                                                                        </select>
                                                                                                    </div>
                                                                                                    <div class="col-md-6" id="construction_fluid_section_0">
                                                                                                        <ul class="list-unstyled mb-0">
                                                                                                            <li class="d-inline-block mr-1">
                                                                                                                <fieldset>
                                                                                                                    <div
                                                                                                                        class="custom-control custom-radio">
                                                                                                                        <input type="radio"
                                                                                                                            class="custom-control-input"
                                                                                                                            name="construction_fluid[0]"
                                                                                                                            id="construction_fluid_boton_0"
                                                                                                                            value="Beton"
                                                                                                                            @if($roof_d->construction_fluid=="Beton") checked @endif
                                                                                                                            >
                                                                                                                        <label class="custom-control-label"
                                                                                                                            for="construction_fluid_boton_0">Beton</label>
                                                                                                                    </div>
                                                                                                                </fieldset>
                                                                                                            </li>
                                                                                                            <li class="d-inline-block mr-1">
                                                                                                                <fieldset>
                                                                                                                    <div
                                                                                                                        class="custom-control custom-radio">
                                                                                                                        <input type="radio"
                                                                                                                            class="custom-control-input"
                                                                                                                            name="construction_fluid[0]"
                                                                                                                            id="construction_fluid_ton_0"
                                                                                                                            value="Ton"
                                                                                                                             @if($roof_d->construction_fluid=="Ton") checked @endif
                                                                                                                            >
                                                                                                                        <label class="custom-control-label"
                                                                                                                            for="construction_fluid_ton_0">Ton</label>
                                                                                                                    </div>
                                                                                                                </fieldset>
                                                                                                            </li>
                                                                                                        </ul>
                                                                                                    </div>

                                                                                                    <div class="col-md-6" id="tilt_section_0">
                                                                                                        <div class="form-group row">
                                                                                                            <div class="col-md-4">
                                                                                                                <p class="bold">Neigung</p>
                                                                                                            </div>
                                                                                                            <div class="col-md-8">
                                                                                                                <input type="text" class="form-control"
                                                                                                                    name="tilt[0]" value="{{$roof_d->tilt}}">
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="col-12" id="insulation_section_0">
                                                                                                <div class="form-group row">
                                                                                                    <div class="col-md-2">
                                                                                                        <p class="bold">Aufdachdämmung</p>
                                                                                                    </div>
                                                                                                    <div class="col-md-10">
                                                                                                        <ul class="list-unstyled mb-0">
                                                                                                            <li class="d-inline-block mr-1">
                                                                                                                <fieldset>
                                                                                                                    <div
                                                                                                                        class="custom-control custom-radio">
                                                                                                                        <input type="radio"
                                                                                                                            class="custom-control-input"
                                                                                                                            name="pv_insulation[0]"
                                                                                                                            id="insulation_ja_0" value="ja"
                                                                                                                               @if($roof_d->roof_insulation=="ja") checked @endif
                                                                                                                            >
                                                                                                                        <label class="custom-control-label"
                                                                                                                            for="insulation_ja_0">ja</label>
                                                                                                                    </div>
                                                                                                                </fieldset>
                                                                                                            </li>
                                                                                                            <li class="d-inline-block mr-1">
                                                                                                                <fieldset>
                                                                                                                    <div
                                                                                                                        class="custom-control custom-radio">
                                                                                                                        <input type="radio"
                                                                                                                            class="custom-control-input"
                                                                                                                            name="pv_insulation[0]"
                                                                                                                            id="insulation_nein_0"
                                                                                                                            @if($roof_d->roof_insulation=="nein") checked @endif
                                                                                                                            value="nein">
                                                                                                                        <label class="custom-control-label"
                                                                                                                            for="insulation_nein_0">nein</label>
                                                                                                                    </div>
                                                                                                                </fieldset>
                                                                                                            </li>
                                                                                                            <li class="d-inline-block mr-1"
                                                                                                                style="width:330px">
                                                                                                                <div class="form-group row">
                                                                                                                    <div class="col-md-4">
                                                                                                                        <p class="bold">Stärke</p>
                                                                                                                    </div>
                                                                                                                    <div
                                                                                                                        class="col-md-8 textbox-container empty">
                                                                                                                        <input type="text"
                                                                                                                            class="form-control textbox"
                                                                                                                            name="thickness_roof_insulation[0]"
                                                                                                                            value="{{$roof_d->thickness_roof_insulation}}"
                                                                                                                            placeholder=" ">
                                                                                                                        <div class="indicator"></div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </li>
                                                                                                        </ul>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="col-12" id="rafter_section_0">
                                                                                                <div class="form-group row">
                                                                                                    <div class="col-md-2">
                                                                                                        <p class="bold">Zwischen sparrendämmung</p>
                                                                                                    </div>
                                                                                                    <div class="col-md-10">
                                                                                                        <ul class="list-unstyled mb-0">
                                                                                                            <li class="d-inline-block mr-1">
                                                                                                                <fieldset>
                                                                                                                    <div
                                                                                                                        class="custom-control custom-radio">
                                                                                                                        <input type="radio"
                                                                                                                            class="custom-control-input"
                                                                                                                            name="between_rafter_insulation[0]"
                                                                                                                            @if($roof_d->between_rafter_insulation=="ja") checked @endif
                                                                                                                            id="rafter_ja_0" value="ja">
                                                                                                                        <label class="custom-control-label"
                                                                                                                            for="rafter_ja_0">ja</label>
                                                                                                                    </div>
                                                                                                                </fieldset>
                                                                                                            </li>
                                                                                                            <li class="d-inline-block mr-1">
                                                                                                                <fieldset>
                                                                                                                    <div
                                                                                                                        class="custom-control custom-radio">
                                                                                                                        <input type="radio"
                                                                                                                            class="custom-control-input"
                                                                                                                            name="between_rafter_insulation[0]"
                                                                                                                               @if($roof_d->between_rafter_insulation=="nein") checked @endif
                                                                                                                            id="rafter_nein_0" value="nein"  >
                                                                                                                        <label class="custom-control-label"
                                                                                                                            for="rafter_nein_0">nein</label>
                                                                                                                    </div>
                                                                                                                </fieldset>
                                                                                                            </li>
                                                                                                            <li class="d-inline-block mr-1"
                                                                                                                style="width:330px">
                                                                                                                <div class="form-group row">
                                                                                                                    <div class="col-md-4">
                                                                                                                        <p class="bold">Stärke</p>
                                                                                                                    </div>
                                                                                                                    <div
                                                                                                                        class="col-md-8 textbox-container empty">
                                                                                                                        <input type="text"
                                                                                                                            class="form-control textbox"
                                                                                                                            value="{{$roof_d->thickness_between_rafter}}"
                                                                                                                            name="thickness_between_rafter[0]">
                                                                                                                        <div class="indicator"></div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </li>
                                                                                                        </ul>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="col-12" id="asbestos_section_0">
                                                                                                <div class="form-group row">
                                                                                                    <div class="col-md-2">
                                                                                                        <p class="bold">Asbesthaltig</p>
                                                                                                    </div>
                                                                                                    <div class="col-md-10">
                                                                                                        <ul class="list-unstyled mb-0">
                                                                                                            <li class="d-inline-block mr-1">
                                                                                                                <fieldset>
                                                                                                                    <div
                                                                                                                        class="custom-control custom-radio">
                                                                                                                        <input type="radio"
                                                                                                                            class="custom-control-input"
                                                                                                                            name="asbestos[0]"
                                                                                                                               @if($roof_d->asbestos=="ja") checked @endif

                                                                                                                            id="asbestos_ja_0" value="ja">
                                                                                                                        <label class="custom-control-label"
                                                                                                                            for="asbestos_ja_0">ja</label>
                                                                                                                    </div>
                                                                                                                </fieldset>
                                                                                                            </li>
                                                                                                            <li class="d-inline-block mr-1">
                                                                                                                <fieldset>
                                                                                                                    <div
                                                                                                                        class="custom-control custom-radio">
                                                                                                                        <input type="radio"
                                                                                                                            class="custom-control-input"
                                                                                                                            name="asbestos[0]"
                                                                                                                            id="asbestos_nein_0"
                                                                                                                               @if($roof_d->asbestos=="nein") checked @endif

                                                                                                                            value="nein" >
                                                                                                                        <label class="custom-control-label"
                                                                                                                            for="asbestos_nein_0">nein</label>
                                                                                                                    </div>
                                                                                                                </fieldset>
                                                                                                            </li>
                                                                                                        </ul>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="col-12" id="roof_renovation_section_0">
                                                                                                <div class="form-group row">
                                                                                                    <div class="col-md-2">
                                                                                                        <p class="bold">Dachsanierung notwendig</p>
                                                                                                    </div>
                                                                                                    <div class="col-md-10">
                                                                                                        <ul class="list-unstyled mb-0">
                                                                                                            <li class="d-inline-block mr-1">
                                                                                                                <fieldset>
                                                                                                                    <div
                                                                                                                        class="custom-control custom-radio">
                                                                                                                        <input type="radio"
                                                                                                                            class="custom-control-input"
                                                                                                                            name="roof_renovation[0]"
                                                                                                                            id="roof_renovation_ja_0"
                                                                                                                               @if($roof_d->roof_renovation=="ja") checked @endif

                                                                                                                            value="ja">
                                                                                                                        <label class="custom-control-label"
                                                                                                                            for="roof_renovation_ja_0">ja</label>
                                                                                                                    </div>
                                                                                                                </fieldset>
                                                                                                            </li>
                                                                                                            <li class="d-inline-block mr-1">
                                                                                                                <fieldset>
                                                                                                                    <div
                                                                                                                        class="custom-control custom-radio">
                                                                                                                        <input type="radio"
                                                                                                                            class="custom-control-input"
                                                                                                                            name="roof_renovation[0]"
                                                                                                                            id="roof_renovation_nein_0"
                                                                                                                               @if($roof_d->roof_renovation=="nein") checked @endif

                                                                                                                            value="nein" >
                                                                                                                        <label class="custom-control-label"
                                                                                                                            for="roof_renovation_nein_0">nein</label>
                                                                                                                    </div>
                                                                                                                </fieldset>
                                                                                                            </li>
                                                                                                        </ul>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>  
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">abbrechen</button>
                                                                                        <button type="submit" class="btn btn-primary waves-effect waves-light" >speichern</button>
                                                                                    </div>
                                                                                </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                             <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1 waves-effect waves-light delete-roof " data-id="{{$roof_d->id}}">
                                                                <i class="feather icon-trash"></i>
                                                            </button> 
                                                            </td>
                                                        </tr>
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
    <!-- END: Content-->

        
         <!-- Full Size Modal -->
            <div class="modal fade" id="fullSizeModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Galerie</h5>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                <i class="feather icon-x"></i>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <!-- Full Size Image -->
                            <img id="fullSizeImage" src="" class="img-fluid">

                            <!-- Address Display -->
                            <p id="imageAddress" class="mt-3"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Roof  -->
                <div class="modal fade text-left" id="new_roof" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel17">DACH</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                                <form id="roof_form">
                                    @csrf
                                    <div class="modal-body"> 
                                        <input type="hidden" name="customer_id" id="customer_id" value="{{$customer->id}}">
                                        <input type="hidden" name="alternative_id" id="alternative_id" value="{{$alternative->id}}"> 
                                            <div class="col-12">
                                                <div class="form-group row"> 
                                                    <div class="col-md-2">
                                                        <span>Bezeichnung</span>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <input type="text" class="form-control"
                                                            name="designation[0]" value="">
                                                    </div>
                                                        
                                                </div>
                                            </div>

                                            <div class="col-12" style="margin-bottom: 40px;">
                                                <div class="form-group row">
                                                    <div class="col-md-12">
                                                        <ul class="list-unstyleds mb-0">
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <img src="{{ asset('images/roofs/Satteldach.png') }}"
                                                                        alt="" srcset=""
                                                                        style="width: 60px;"
                                                                        for="roof_Satteldach">
                                                                    <div
                                                                        class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="roof[0]"
                                                                            id="roof_Satteldach"
                                                                            value="Satteldach" @if($alternative->roof_type=="Satteldach") checked  @endif>
                                                                        <label class="custom-control-label"
                                                                            for="roof_Satteldach">Satteldach</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <img src="{{ asset('images/roofs/Flachdach.png') }}"
                                                                        alt="" srcset=""
                                                                        style="width: 60px;"
                                                                        for="roof_Flachdach">
                                                                    <div
                                                                        class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="roof[0]"
                                                                            id="roof_Flachdach"
                                                                            value="Flachdach" @if($alternative->roof_type=="Flachdach") checked @endif>
                                                                        <label class="custom-control-label"
                                                                            for="roof_Flachdach">Flachdach</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <img src="{{ asset('images/roofs/Garage.png') }}"
                                                                        alt="" srcset=""
                                                                        style="width: 60px;"
                                                                        for="roof_Garage">
                                                                    <div
                                                                        class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="roof[0]"
                                                                            id="roof_Garage"
                                                                            value="Garage" @if($alternative->roof_type=="Garage") checked @endif>
                                                                        <label class="custom-control-label"
                                                                            for="roof_Garage">Garage</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <img src="{{ asset('images/roofs/Carport.png') }}"
                                                                        alt="" srcset=""
                                                                        style="width: 60px;"
                                                                        for="roof_Carport">
                                                                    <div
                                                                        class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="roof[0]"
                                                                            id="roof_Carport"
                                                                            value="Carport" @if($alternative->roof_type=="Carport") checked @endif>
                                                                        <label class="custom-control-label"
                                                                            for="roof_Carport">Carport</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-2">
                                                        <p class="bold">Dacheindeckung</p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select class="roof_covering" name="roof_covering[0]"
                                                            style="width:100%">
                                                            @foreach ($tiles as $tile)
                                                            <option value="{{ $tile->product_id }}"
                                                                data-image="{{ asset('images/products/'.$tile->image) }}"
                                                                data-roof-type="{{ $tile->roof_type }}">
                                                                {{ $tile->product }} ->
                                                                {{ $tile->roof_type }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6" id="construction_fluid_section_0">
                                                        <ul class="list-unstyled mb-0">
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div
                                                                        class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="construction_fluid[0]"
                                                                            id="construction_fluid_boton_0"
                                                                            value="Beton">
                                                                        <label class="custom-control-label"
                                                                            for="construction_fluid_boton_0">Beton</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div
                                                                        class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="construction_fluid[0]"
                                                                            id="construction_fluid_ton_0"
                                                                            value="Ton">
                                                                        <label class="custom-control-label"
                                                                            for="construction_fluid_ton_0">Ton</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                        </ul>
                                                    </div>

                                                    <div class="col-md-6" id="tilt_section_0">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <p class="bold">Neigung</p>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control"
                                                                    name="tilt[0]">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12" id="insulation_section_0">
                                                <div class="form-group row">
                                                    <div class="col-md-2">
                                                        <p class="bold">Aufdachdämmung</p>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <ul class="list-unstyled mb-0">
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div
                                                                        class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="pv_insulation[0]"
                                                                            id="insulation_ja" value="ja">
                                                                        <label class="custom-control-label"
                                                                            for="insulation_ja">ja</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div
                                                                        class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="pv_insulation[0]"
                                                                            id="insulation_nein"
                                                                            value="nein" checked>
                                                                        <label class="custom-control-label"
                                                                            for="insulation_nein">nein</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1"
                                                                style="width:330px">
                                                                <div class="form-group row">
                                                                    <div class="col-md-4">
                                                                        <p class="bold">Stärke</p>
                                                                    </div>
                                                                    <div
                                                                        class="col-md-8 textbox-container empty">
                                                                        <input type="text"
                                                                            class="form-control textbox"
                                                                            name="thickness_roof_insulation[0]"
                                                                            placeholder=" ">
                                                                        <div class="indicator"></div>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12" id="rafter_section_0">
                                                <div class="form-group row">
                                                    <div class="col-md-2">
                                                        <p class="bold">Zwischen sparrendämmung</p>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <ul class="list-unstyled mb-0">
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div
                                                                        class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="between_rafter_insulation[0]"
                                                                            id="rafter_ja" value="ja">
                                                                        <label class="custom-control-label"
                                                                            for="rafter_ja">ja</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div
                                                                        class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="between_rafter_insulation[0]"
                                                                            id="rafter_nein" value="nein" checked>
                                                                        <label class="custom-control-label"
                                                                            for="rafter_nein">nein</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1"
                                                                style="width:330px">
                                                                <div class="form-group row">
                                                                    <div class="col-md-4">
                                                                        <p class="bold">Stärke</p>
                                                                    </div>
                                                                    <div
                                                                        class="col-md-8 textbox-container empty">
                                                                        <input type="text"
                                                                            class="form-control textbox"
                                                                            name="thickness_between_rafter[0]">
                                                                        <div class="indicator"></div>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12" id="asbestos_section_0">
                                                <div class="form-group row">
                                                    <div class="col-md-2">
                                                        <p class="bold">Asbesthaltig</p>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <ul class="list-unstyled mb-0">
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div
                                                                        class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="asbestos[0]"
                                                                            id="asbestos_ja" value="ja">
                                                                        <label class="custom-control-label"
                                                                            for="asbestos_ja">ja</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div
                                                                        class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="asbestos[0]"
                                                                            id="asbestos_nein"
                                                                            value="nein" checked>
                                                                        <label class="custom-control-label"
                                                                            for="asbestos_nein">nein</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12" id="roof_renovation_section_0">
                                                <div class="form-group row">
                                                    <div class="col-md-2">
                                                        <p class="bold">Dachsanierung notwendig</p>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <ul class="list-unstyled mb-0">
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div
                                                                        class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="roof_renovation[0]"
                                                                            id="roof_renovation_ja"
                                                                            value="ja">
                                                                        <label class="custom-control-label"
                                                                            for="roof_renovation_ja">ja</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div
                                                                        class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="roof_renovation[0]"
                                                                            id="roof_renovation_nein"
                                                                            value="nein" checked>
                                                                        <label class="custom-control-label"
                                                                            for="roof_renovation_nein">nein</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>  
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">abbrechen</button>
                                        <button type="submit" class="btn btn-primary waves-effect waves-light" >spechiern</button>
                                    </div>
                                </form>
                        </div>
                    </div>
                </div>

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


<!-- JavaScript to Update Modal Picture -->
<script>
    function showFullSize(imageSrc, address) {
        document.getElementById('fullSizeImage').src = imageSrc;
        document.getElementById('imageAddress').innerText = address; // Set address as text

        new bootstrap.Modal(document.getElementById('fullSizeModal')).show();
    }
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
 
<!-- PV Checklist CRUD Update  -->
 
 
<script>
document.addEventListener('DOMContentLoaded', function () {
    function updateField(element) {
        let newValue = element.value.trim();
        let field = element.getAttribute('data-field');
        let customerId = document.getElementById('pv_customer_id').value;
        let alternativeId = document.getElementById('pv_alternative_id').value;

        if (!field || !customerId || !alternativeId) {
            console.error("Missing required data for update.");
            return;
        }

        fetch("{{ route('update-field.pv') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
            },
            body: JSON.stringify({
                customer_id: customerId,
                alternative_id: alternativeId,
                field: field,
                value: newValue
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(err.message); });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                toastr.success(`Updated <strong>${field}</strong> to <strong>${newValue}</strong>`, 'Success!', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-top-right"
                });
            } else {
                toastr.error(data.message, "Error", { closeButton: true, progressBar: true });
            }
        })
        .catch(error => {
            console.error("Error:", error);
            toastr.error("Something went wrong! " + error.message, "Error", { closeButton: true, progressBar: true });
        });
    }

    // Listen for changes on select elements
    document.querySelectorAll('.editable').forEach(element => {
        if (element.tagName === 'SELECT' || element.type === 'date') {
            element.addEventListener('change', function () {
                updateField(this);
            });
        }
    });
});

</script>
<!-- Editable PV progress bar start  -->
 <script>
    document.addEventListener('DOMContentLoaded', function () {
    function updateProgress() {
        let editableFields = document.querySelectorAll('.editable');
        let filledFields = 0;
        let totalFields = editableFields.length;

        // Count how many fields are filled
        editableFields.forEach(field => {
            let value = null;

            if (field.tagName === 'SELECT') {
                value = field.value.trim();
            } else if (field.querySelector('input')) {
                value = field.querySelector('input').value.trim();
            } else {
                value = field.innerText.trim();
            }

            if (value && value !== 'N/V') {
                filledFields++;
            }
        });

        // Calculate percentage
        let progressPercent = Math.round((filledFields / totalFields) * 100);

        // Update progress bar
        let progressBar = document.querySelector('#pv-progress .progress-bar');
        let percentText = document.querySelector('#pv-progress #percent');

        if (progressBar && percentText) {
            progressBar.style.width = progressPercent + '%';
            progressBar.setAttribute('aria-valuenow', progressPercent);
            percentText.innerText = progressPercent + '%';
        }

        console.log(`Progress Updated: ${filledFields} of ${totalFields} fields filled (${progressPercent}%)`);
    }

    // Initial Progress Update
    updateProgress();

    // Listen for input or change events on all editable fields
    document.querySelectorAll('.editable').forEach(element => {
        if (element.tagName === 'SELECT' || element.type === 'date') {
            element.addEventListener('change', updateProgress);
        } else {
            element.addEventListener('blur', updateProgress);
        }
    });

    document.addEventListener('input', function (event) {
        if (event.target.closest('.editable')) {
            updateProgress();
        }
    });
});

 </script>

<!-- Editable PV progress bar end  -->

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

<!-- JavaScript card and list view -->
<script>
    document.getElementById("list_view").addEventListener("click", function() {
        document.getElementById("card_row_preview").style.display = "block"; // Show List View
        document.getElementById("card_preview").style.display = "none"; // Hide Card View
        
        // Toggle buttons
        document.getElementById("list_view").style.display = "none";
        document.getElementById("card_view").style.display = "block";
    });

    document.getElementById("card_view").addEventListener("click", function() {
        document.getElementById("card_row_preview").style.display = "none"; // Hide List View
        document.getElementById("card_preview").style.display = "block"; // Show Card View
        
        // Toggle buttons
        document.getElementById("list_view").style.display = "block";
        document.getElementById("card_view").style.display = "none";
    });
</script>



<!-- JavaScript to Update Modal Picture -->
<script>
    function showFullSize(imageSrc, address) {
        document.getElementById('fullSizeImage').src = imageSrc;
        document.getElementById('imageAddress').innerText = address; // Set address as text

        new bootstrap.Modal(document.getElementById('fullSizeModal')).show();
    }
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
 
 



<!-- {{-- roof type drop down:start --}} -->
   
<script>
    $(document).ready(function () {
        $("#roof_form").submit(function (e) {
            e.preventDefault(); // Prevent default form submission
            
            let formData = new FormData(this); // Collect form data

            $.ajax({
                url: "{{ route('lead.roof.store') }}", // Update with your correct route
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") // Ensure CSRF token is included
                },
                beforeSend: function () {
                    Swal.fire({
                        title: "Saving...",
                        text: "Please wait while saving the data.",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function (response) {
                    Swal.fire({
                        title: "Success!",
                        text: "Data saved successfully.",
                        icon: "success",
                        timer: 2000, // Auto close after 2 seconds
                        showConfirmButton: false
                    }).then(() => {
                        location.reload(); // Reload the page after success
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        title: "Error!",
                        text: xhr.responseJSON?.message || "An error occurred. Please try again.",
                        icon: "error"
                    });
                }
            });
        });


        

        
    });
</script>


   
<script>
    $(document).ready(function () {
        $("#roof_form_update").submit(function (e) {
            e.preventDefault(); // Prevent default form submission
            
            let formData = new FormData(this); // Collect form data

            $.ajax({
                url: "{{ route('lead.roof.update') }}", // Update with your correct route
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") // Ensure CSRF token is included
                },
                beforeSend: function () {
                    Swal.fire({
                        title: "Saving...",
                        text: "Please wait while saving the data.",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function (response) {
                    Swal.fire({
                        title: "Success!",
                        text: "Data saved successfully.",
                        icon: "success",
                        timer: 2000, // Auto close after 2 seconds
                        showConfirmButton: false
                    }).then(() => {
                        location.reload(); // Reload the page after success
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        title: "Error!",
                        text: xhr.responseJSON?.message || "An error occurred. Please try again.",
                        icon: "error"
                    });
                }
            });
        });


        

        
    });
</script>

<script>
    $(document).ready(function() {
        $(document).on('click', '.delete-roof', function() {
            let roofId = $(this).data('id');
            let url = "{{ route('lead.roof.delete', ':roof_id') }}".replace(':roof_id', roofId);

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: "Deleted!",
                                text: response.message,
                                icon: "success",
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload(); // Reload page after deletion
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: "Error!",
                                text: "There was an issue deleting the record.",
                                icon: "error"
                            });
                        }
                    });
                }
            });
        });
    });
</script>


<script>
$(document).ready(function() {
    function formatOption(option) {
        if (!option.id) {
            return option.text;
        }
        var imageUrl = $(option.element).data("image");
        var roofType = $(option.element).data("roof-type");
        var $option = $(
            `<div style="display: flex; align-items: center;">
                <span style="flex-grow: 1;">${option.text}</span>
                <img src="${imageUrl}" style="width: 40px; height: 40px; margin-left: 10px; border-radius: 4px;">
            </div>`
        );
        return $option;
    }

    $(".roof_covering").select2({
        templateResult: formatOption,
        templateSelection: formatOption,
        width: '100%',
        escapeMarkup: function (markup) {
            return markup;
        }
    });
});
</script>



@endsection
     
 
 