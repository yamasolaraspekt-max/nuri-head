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
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" id="list_view" style="display: none;">Listenansicht</a>
                                <a class="dropdown-item" id="card_view">Box-Ansicht</a>
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
            
                      <!-- Search Section -->
                <div class="row"  >  
                    <div class="col-12 " style="    display: flex !important; flex-direction: row-reverse;">
                        <form action="{{ action('App\Http\Controllers\NewLeadsController@view', $customer->id) }}">
                            <fieldset>
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search Form" aria-describedby="button-addon2">
                                    <div class="input-group-append" id="button-addon2">
                                        <button class="btn btn-primary" type="submit">Go</button>
                                    </div>
                                </div>
                            </fieldset>
                        </form> 
                      
                        <a type="button" class="btn btn-outline-primary square mr-1 mb-1 waves-effect waves-light" href="{{ url('new_object/'.$customer->id) }}" >Neue Objekt</a> 
                     </div>  
                </div>
                <div   id="card_preview" style="display:none;"> 
                    <div class="row">
                          @foreach ($alternative as $alt)  
                                <div class="col-md-4 col-sm-3 ">
                                    <div class="card mb-1">
                                        <div class="card-header p-1" >
                                            <div class="col-5"> 
                                                <p style="font-size: 12px;font-weight: bold; margin-bottom:0; position:absolute"> 
                                                    <a href="{{ url('new_lead_edit/'.$customer->id.'/'.$alt->id)}}"><i class="feather icon-edit primary"></i></a> 
                                                    <a href="{{url('new_lead_profile_object/'.$alt->lead_id.'/'.$alt->id )}}">
                                                         {{ strtoupper($alt->object_name) }} 
                                                    </a>
                                                </p> 
                                                <p style="font-size: 11px;font-weight: bold; margin-top:14px;"> <small>
                                                        {{ $alt->street }}  {{ $alt->postcode }} - {{ $alt->city }}
                                                </small></p> 
                                            </div> 
                                            <div class="col-7" style="text-align: center"> 
                                                @php
                                                    // Filter screenshots to get only the ones belonging to this $alt->id
                                                    $filteredScreenshots = $screenshots->where('alternative_id', $alt->id);
                                                    $firstImage = $filteredScreenshots->first(); // Get the first image of the set
                                                @endphp

                                                @if ($firstImage)
                                                    <div class="col-12">
                                                        <!-- Clickable Thumbnail (First Image) -->
                                                        <img src="{{ asset('images/customers/'.$firstImage->image) }}" 
                                                            alt="" class="" 
                                                            style="width: 130px; cursor: pointer;" 
                                                            data-toggle="modal" data-backdrop="false" data-target="#imageModal{{$alt->id}}"> 
                                                    </div> 

                                                    <div class="modal fade text-left" id="imageModal{{$alt->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true" style="display: none;">
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
                                                                        {{ $alt->street }}  {{ $alt->postcode }} - {{ $alt->city }}
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
                                                <p class="pb-0">Objektart: {{ $alt->objective ?? '' }}</p> 
                                            </div>  
                                        </div>
                                            
                                        <div class="card-content collapse" style=""> 
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
                                                                
                                                                    
                                                                    @if($alt->stage == 'lead')
                                                                    <i class="feather icon-heart " style="font-size: 24px;"></i> 
                                                                    @elseif($alt->stage == 'plan')
                                                                    <img src=" {{ asset('images/dashboard/icon_gears.svg') }}" alt="" class="p-0" style="width: 52px;">
                                                                    @elseif($alt->stage == 'offer')
                                                                    <img src=" {{ asset('images/dashboard/icon_document.svg') }}" alt="" class="p-0" style="width: 52px;">
                                                                    @elseif($alt->stage == 'deal')
                                                                    <img src=" {{ asset('images/dashboard/icon_memoboard.svg') }}" alt="" class="p-0" style="width: 52px;">
                                                                    @elseif($alt->stage == 'project')
                                                                    <img src=" {{ asset('images/dashboard/icon_memoboard.svg') }}" alt="" class="p-0" style="width: 52px;">
                                                                    @elseif($alt->stage == 'end')
                                                                    <img src=" {{ asset('images/dashboard/icon_target.svg') }}" alt="" class="p-0" style="width: 52px;">
                                                                    @elseif($p_status == 'end')
                                                                        {{ $alt->project_date }}
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
                                            <!-- Edit Buttons  -->
                                            <div class="row">
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
                </div> 
                <div class="row" id="card_row_preview">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr style="background:white; ">  
                                    <th>ID</th>  
                                    <th style=" display: flex;justify-content: space-around;     width: 135px;    border: 0;">
                                    <a class="secondary" data-toggle="popover" 
                                        data-content="Bitte dringend die Anfrage bearbeiten" 
                                        data-trigger="hover" 
                                        data-original-title="Wichtigkeit grad sehr hoch!">
                                        <i class="feather icon-alert-circle " style="font-size: 20px;"></i>
                                    </a>
                            
                                    <a class="secondary" data-toggle="popover" 
                                        data-content="die Anfrage liegt länger als 48 Stunden es muss dringend bearbeitet werden" 
                                        data-trigger="hover" 
                                        data-original-title="Zeit von 48 Stunden überschritten!">
                                        <i class="feather icon-bell " style="font-size: 20px;"></i>
                                    </a>
                                    <a class="secondary" data-toggle="popover" 
                                        data-content="bitte innerhalb von 48 Stunden die Anfrage Qualifzieren" 
                                        data-trigger="hover" 
                                        data-original-title="Neue Anfrage">
                                        <i class="feather icon-star " style="font-size: 20px;"> </i>
                                    </a>
                                    </th>
                                    <th>DATUM</th>  
                                    <th>OBJEKT</th>    
                                    <th>ANSPRECHPARTNER</th>    
                                    <th>NOTIZ</th> 
                                    <th>TICKET</th>
                                    <th>PRODUKT</th>   
                                    <th width="2">BEARBEITEN</th> 
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($alternative->where('lead_id', $alt->lead_id) as $alter) 
                                    <tr style="background:white;border-bottom: 1px solidrgb(81, 81, 81);" class="mb-2">   
                                        <td>{{$alter->id}}</td>
                                        <th scope="row" style="width:20px">
                                                <div style="display: flex; flex-wrap: nowrap; align-content: center; justify-content: space-evenly;width: 93px;">
                                                    <?php 
                                                    $currentDateTime = new DateTime(); // Current date and time
                                                    $requestDateTime = new DateTime($alter->request_date); // Request date and time

                                                    $interval = $currentDateTime->diff($requestDateTime); // Difference between current date and request date
                                                    $hoursDifference = ($interval->days * 24) + $interval->h; // Convert difference to hours

                                                    // Check if the priority is 'sehr dringend'
                                                    if (strtolower($alter->periority) === 'sehr dringend') {
                                                        echo '<a href=""><i class="feather icon-alert-circle danger blink" id="alert' . $alter->id . '" style="font-size: 20px;"></i></a><br>';
                                                    } else {
                                                        echo '<a href=""><i class="feather icon-alert-circle secondary" id="alert' . $alter->id . '" style="font-size: 20px;"></i></a><br>';
                                                    }

                                                    // Check if the request date is more than 48 hours ago
                                                    if ($hoursDifference > 48) {
                                                        echo '<a href=""><i class="feather icon-bell danger blink" id="bell' . $alter->id . '"  style="font-size: 20px;"></i></a><br>';
                                                    } else {
                                                        echo '<a href=""><i class="feather icon-bell secondary" id="bell' . $alter->id . '" style="font-size: 20px;"></i></a><br>';
                                                    }

                                                    // Check if the request date is within 48 hours
                                                    if ($hoursDifference <= 48) {
                                                        echo '<a href=""><i class="feather icon-star warning" id="stars' . $alter->id . '" style="font-size: 20px;"></i></a><br>';
                                                    } else {
                                                        echo '<a href=""><i class="feather icon-star secondary" id="stars' . $alter->id . '" style="font-size: 20px;"></i></a><br>';
                                                    }
                                                    ?>
                                                </div>
                                            </th> 
                                        <td>
                                            <i class="feather icon-calendar"></i> {{ \Carbon\Carbon::parse($alter->request_date)->isoFormat('DD.MM.YY') }} <br>
                                            <code> <strong> 
                                                {{ \Carbon\Carbon::parse($alter->request_date)->diffForHumans() }}                                   
                                            </strong></code>  
                                        </td>
                                        <td>
                                            <a href="{{url('new_lead_profile_object/'.$alter->lead_id.'/'.$alter->id )}}">
                                                <p class="p-0 m-0">{{ $alter->object_name }}</p>
                                            </a>
                                            <small>
                                                {{ $alter->street }} {{ $alter->postcode }} - {{ $alter->city }}
                                            </small> 
                                        </td> 
                                        <td>
                                            @foreach ($contactPeople->where('alternative_id', $alter->id) as $contact )
                                               <div>
                                                 <span>Name: {{$contact->name}} {{$contact->lastname}} ({{ $contact->relation }})</span>
                                                    <p><i class="feather icon-phone"></i> {{$contact->phone}} - {{$contact->office}} - {{ $contact->home}}</p>
                                                    <p><i class="feather icon-mail"></i> {{$contact->email}} </p>
                                               </div>

                                            @endforeach
                                        </td>
                                        <td>
                                            @if($alter->note)
                                            <!-- Button to open modal -->
                                            <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary" data-toggle="modal" data-target="#info{{$alter->id}}">
                                                <i class="fa fa-sticky-note-o"></i>
                                            </button>
                                            @else
                                            <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger" >
                                                <i class="fa fa-sticky-note-o"></i>
                                            </button>
                                            @endif
                                            <!-- Modal -->
                                            <div class="modal fade" id="info{{$alter->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary white">
                                                            <h5 class="modal-title" id="myModalLabel120">Notizen</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="col-md-10"> 
                                                                <h1>{{ $customer->title }} {{$customer->name}} {{ $customer->lastname}}</h1> 
                                                                <p>{{ $customer->street}}<br>{{ $customer->postcode }}
                                                                    @if($alter->main == 1)
                                                                    <small><code>Die Adresse des Kunden stimmt nicht mit seiner Hauptwohnadresse überein</code></small>
                                                                    @endif
                                                                </p>
                                                                <p style="margin:0; line-height:0px"><i class="feather icon-phone-call" ></i> {{ $customer->telephone }}</p>
                                                                <p style="margin:0; line-height:0px"><i class="feather icon-smartphone" ></i> {{ $customer->phone }}</p>
                                                                <p style="margin:0; line-height:0px"><i class="feather icon-mail" ></i> {{ $customer->email }}</p>
                                                            </div>
                                                            <hr>
                                                            <h1 class="mb-2">Notizen</h1>
                                                            <div class="col-md-12">
                                                                <p>{{ $alter->note }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <!-- Modal footer (optional) -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td> 
                                        <td> 
                                             @php
                                                    // Ensure the filtered collection only contains non-soft-deleted leads
                                                    $filteredTicket = $tickets->where('customer_id', $customer->id);
                                                    $groupedTickets = $filteredTicket->groupBy('product_id');
                                                @endphp

                                                @foreach ($groupedTickets as $productId => $products)
                                                    @php
                                                        $product_initial = $tickets->first(); // Get the first product instance
                                                        $ticketCount = $tickets->count(); // Count how many times the product exists
                                                    @endphp

                                                    <div class="position-relative d-inline-block mr-2" style="background: #8fc73e; padding: 15px; border-radius: 50%; font-size: 8px; width: 10px; height: 10px;">
                                                        <span style="padding: 0; margin: 0; font-size: 8px; position: relative; top: -5px; left: -10px; color: white;">
                                                            {{ $product_initial->initial }}
                                                        </span>
                                                        <span class="badge badge-pill badge-primary badge-up" style="position: absolute; top: -7px; right: -7px; border: 1px solid; font-size: 8px !important; background:#73B1D5 !important;">
                                                            {{ $ticketCount }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                        </td>
                                        <td class="">
                                                @php
                                                    // Ensure the filtered collection only contains non-soft-deleted leads
                                                    $filteredProducts = $productcount->where('customer_id', $customer->id)->where('alternative_id', $alter->id);
                                                    $groupedProducts = $filteredProducts->groupBy('product_id');
                                                @endphp

                                                @foreach ($groupedProducts as $productId => $products)
                                                    @php
                                                        $productC = $products->first(); // Get the first product instance
                                                        $productCount = $products->count(); // Count how many times the product exists
                                                    @endphp

                                                    <div class="position-relative d-inline-block mr-2" style="background: #8fc73e; padding: 15px; border-radius: 50%; font-size: 8px; width: 10px; height: 10px;">
                                                        <span style="padding: 0; margin: 0; font-size: 8px; position: relative; top: -5px; left: -10px; color: white;">
                                                            {{ $productC->initial }}
                                                        </span>
                                                        <span class="badge badge-pill badge-primary badge-up" style="position: absolute; top: -7px; right: -7px; border: 1px solid; font-size: 8px !important; background:#73B1D5 !important;">
                                                            {{ $productCount }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                        </td>   
                                        <td> 
                                            <div class="btn-group dropup dropdown-icon-wrapper mr-1 mb-1"> 
                                                <button type="button" class="btn   dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="feather icon-menu dropdown-icon"></i>
                                                </button>
                                                <div class="dropdown-menu">

                                                    @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_update', '=', 'on')->first())
                                                    <span class="dropdown-item">
                                                    <a  data-toggle="modal" 
                                                        data-target="#add_employee{{$alter->id}}"   
                                                        data-employee-id="" 
                                                        data-product-id="" 
                                                        data-new-lead-id="{{ $customer->id }}" >
                                                        <i class="feather icon-user" ></i> 
                                                        Verantwortlicher bearbeiten
                                                    </a> 
                                                    </span>
                                                    @endif
                                                    @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_update', '=', 'on')->first())
                                                    <span class="dropdown-item">
                                                    <a  href="{{ url('/new_lead_edit/'.$customer->id.'/'.$alter->id)}}" ><i class="feather icon-edit" ></i> Bearbeiten</a> 
                                                    </span>
                                                    @endif
                                                    @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_delete', '=', 'on')->first())
                                                        @if(Route::currentRouteName() != 'deleted.leads')
                                                        <span class="dropdown-item danger">
                                                            <a data-toggle="modal" data-target="#delete-alter{{$alter->id}}"><i class="feather icon-trash-2 danger" ></i>Löschen</a>
                                                        </span>
                                                        @else
                                                        <span class="dropdown-item danger">
                                                            <a data-toggle="modal" data-target="#delete-alter{{$alter->id}}"><i class="feather icon-trash-2 danger" ></i>Wiederherstellen</a>
                                                        </span>
                                                        @endif
                                                    @endif

                                                    <!-- @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_delete', '=', 'on')->first())
                                                        @if($customer->status!="Junk")
                                                        <span class="dropdown-item danger">
                                                            <a data-toggle="modal" data-target="#alter_junk{{$alter->id}}"><i class="fa fa-power-off danger " ></i> Junk</a>
                                                        </span>
                                                        @else
                                                        <span class="dropdown-item danger">
                                                            <a data-toggle="modal" data-target="#alter_unjunk{{$alter->id}}"><i class="fa fa-power-off primary" ></i> Unjunk</a>
                                                        </span>
                                                        @endif
                                                    @endif -->


                                                        <span class="dropdown-item primary">
                                                               <a href="javascript:void(0);" onclick="openContactModal({{ $customer->id }}, {{ $alter->id }})">
                                                                    <i class="feather icon-users primary"></i> Ansprechpartner
                                                                </a>                                                      
                                                        </span>

                                    
                                                </div>
                                            </div>
 
                                            <div class="modal fade" id="delete-alter{{$alter->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger white">
                                                            <h5 class="modal-title" id="myModalLabel120"> @if(Route::currentRouteName() != 'deleted.leads') Objekt Löschen @else Wiederherstellen @endif</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                        </div>
                                                        @if(Route::currentRouteName() != 'deleted.leads')
                                                        <div class="modal-body">
                                                            <h5>Aufzeichnung löschen</h5>
                                                            <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                            <p>Die Datensatznummer lautet:{{$customer->customer_no}}. {{ $customer->name }} {{ $customer->lastname }} </p>
                                                            <p>Die Objektname: {{$alter->object_name}}</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a type="button" href="{{url('/delete_lead_alternative').'/'.$alter->id}}" class="btn btn-danger">Ja</a>
                                                        </div>
                                                        @else

                                                <div class="modal-body">
                                                        <h5>Daten wiederherstellen: </h5>
                                                        <p>Möchten Sie diese Daten wirklich wiederherstellen?</p>
                                                        <p>Die Datensatznummer lautet:{{$customer->id}}. {{ $customer->name }} {{ $customer->lastname }} </p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a type="button" href="{{url('/restore_alternative_leads').'/'.$alter->id}}" class="btn btn-danger">Ja</a>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div> 


                                          <!-- Modal -->
                                            <div class="modal fade" id="contactModal" tabindex="-1" role="dialog">
                                            <div class="modal-dialog modal-xl" role="document">
                                                <form id="contactPeopleForm">
                                                @csrf
                                                <input type="hidden" name="customer_id" id="customer_id">
                                                <input type="hidden" name="alternative_id" id="alternative_id">

                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                    <h5 class="modal-title">Ansprechpartner</h5>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>

                                                    <div class="modal-body">
                                                    <div id="contact-people-container"></div>

                                                    <button type="button" class="btn btn-sm btn-primary mt-3" onclick="addContactPerson()">+ Neue Person</button>
                                                    </div>

                                                    <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">Speichern</button>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                                                    </div>
                                                </div>
                                                </form>
                                            </div>
                                            </div>
                                        </td>  
                                    </tr>   
                                @endforeach
                            </tbody>

                            

                        </table>
                    </div>
                </div>
                <!-- account setting page end --> 
                  
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
            type: 'get',
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
                                method: 'get'
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
 <script>
let contactIndex = 0;

// Open modal and load data
function openContactModal(customerId, alternativeId) {
  $('#contactModal').modal('show');
  $('#customer_id').val(customerId);
  $('#alternative_id').val(alternativeId);
  $('#contact-people-container').html('');
  contactIndex = 0;

  fetch(`/customer-contact/fetch/${customerId}/${alternativeId}`)
    .then(res => res.json())
    .then(data => {
      if (data.length) {
        data.forEach(person => {
          addContactPerson(person);
        });
      } else {
        addContactPerson(); // empty form
      }
    });
}

// Add new or pre-filled contact row
function addContactPerson(data = {}) {
  const container = document.getElementById('contact-people-container');
  const row = document.createElement('div');
  row.classList.add('border', 'p-2', 'mb-3', 'contact-person-entry');

  row.innerHTML = `
    <input type="hidden" name="contact_people[${contactIndex}][id]" value="${data.id || ''}">
    <div class="form-row">
      <div class="col">
        <select name="contact_people[${contactIndex}][relation]" class="form-control">
          <option value="">-- Beziehung wählen --</option>
          <option value="Ehepartner" ${data.relation === 'Ehepartner' ? 'selected' : ''}>Ehepartner</option>
          <option value="Vater" ${data.relation === 'Vater' ? 'selected' : ''}>Vater</option>
          <option value="Mutter" ${data.relation === 'Mutter' ? 'selected' : ''}>Mutter</option>
          <option value="Sohn" ${data.relation === 'Sohn' ? 'selected' : ''}>Sohn</option>
          <option value="Tochter" ${data.relation === 'Tochter' ? 'selected' : ''}>Tochter</option>
          <option value="Bruder" ${data.relation === 'Bruder' ? 'selected' : ''}>Bruder</option>
          <option value="Schwester" ${data.relation === 'Schwester' ? 'selected' : ''}>Schwester</option>
          <option value="Freund" ${data.relation === 'Freund' ? 'selected' : ''}>Freund</option>
          <option value="Kollege" ${data.relation === 'Kollege' ? 'selected' : ''}>Kollege</option>
        </select>
      </div>
      <div class="col"><input type="text" name="contact_people[${contactIndex}][name]" value="${data.name || ''}" class="form-control" placeholder="Name"></div>
      <div class="col"><input type="text" name="contact_people[${contactIndex}][lastname]" value="${data.lastname || ''}" class="form-control" placeholder="Nachname"></div>
    </div>

    <div class="form-row mt-2">
      <div class="col"><input type="text" name="contact_people[${contactIndex}][phone]" value="${data.phone || ''}" class="form-control" placeholder="Telefon"></div>
      <div class="col"><input type="text" name="contact_people[${contactIndex}][office]" value="${data.office || ''}" class="form-control" placeholder="Büro"></div>
      <div class="col"><input type="text" name="contact_people[${contactIndex}][home]" value="${data.home || ''}" class="form-control" placeholder="Privat"></div>
      <div class="col"><input type="email" name="contact_people[${contactIndex}][email]" value="${data.email || ''}" class="form-control" placeholder="E-Mail"></div>
    </div>

    <div class="text-right mt-2">
      <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this, ${data.id || 'null'})">
        <i class="fa fa-trash"></i> Entfernen
      </button>
    </div>
  `;
  container.appendChild(row);
  contactIndex++;
}

// Remove a contact row (existing or new)
function deleteRow(button, id = null) {
  Swal.fire({
    title: 'Bist du sicher?',
    text: "Dieser Kontakt wird gelöscht.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ja, löschen',
    cancelButtonText: 'Abbrechen'
  }).then((result) => {
    if (result.isConfirmed) {
      if (id) {
        fetch(`/customer-contact/delete/${id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          }
        }).then(() => {
          button.closest('.contact-person-entry').remove();
          Swal.fire('Gelöscht!', '', 'success');
        });
      } else {
        button.closest('.contact-person-entry').remove();
      }
    }
  });
}

// Save/update contacts
document.getElementById('contactPeopleForm').addEventListener('submit', function (e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);

  fetch('{{ route("customer.contact.update") }}', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      Swal.fire('Gespeichert!', 'Kontakte wurden gespeichert.', 'success');
    });
});

// 💡 When modal is closed, reload full page
$('#contactModal').on('hidden.bs.modal', function () {
  location.reload();
});
</script>


<!-- Loading and fetching the project task managment data: start -->
 
 
@endsection
     
 
 