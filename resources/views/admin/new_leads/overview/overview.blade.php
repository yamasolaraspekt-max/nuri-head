@extends('admin.layouts.app')

@section('title') PLANUNG @stop

@section('style')
<meta name="csrf-token" content="{{ csrf_token() }}">

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
    
    .openli {
        color: white;background: #e53060;display: flex;padding: 6px 6px 6px 6px;
    }
     .activeli {
        color: white;background:#92b532;display: flex;padding: 6px 6px 6px 6px;
    }
     .inactiveli {
        color: white;background: #78a7cc;display: flex;padding: 6px 6px 6px 6px;
    }
     .endedli {
        color: white;background: #213985;display: flex;padding: 6px 6px 6px 6px;
    }
     .cancelli {
        color: white;background: #7e7d7d;display: flex;padding: 6px 6px 6px 6px;
    }
    .sumli {
          color: white;background: #782567;display: flex;padding: 6px 6px 6px 6px;
    }
    .openli1 {
            display: flex;
    align-content: center;
    border: 1px #e53060;
    border-style: solid;
    }
     .activeli1 {
            display: flex;
    align-content: center;
    border: 1px #92b532;
    border-style: solid;
    }
     .inactiveli1 {
            display: flex;
    align-content: center;
    border: 1px #78a7cc;
    border-style: solid;
    }
     .endedli1 {
            display: flex;
    align-content: center;
    border: 1px #213985;
    border-style: solid;
    }
     .cancelli1 {
            display: flex;
    align-content: center;
    border: 1px #7e7d7d;
    border-style: solid;
    }

    .sumli1 {
            display: flex;
    align-content: center;
    border: 1px #782567;
    border-style: solid;
    }
    .simpleli {
        display: flex;padding: 6px 6px 6px 6px;
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
        font-size: 12px;
        font-weight: 500;
        color: #555;
        margin-top: 10px;
        place-self: anchor-center;
    }
    .overview_table td {
        width:20px !important;
    }

    
    
    .overview_table td { 
        justify-items: center;
    }

    .overview_table th { 
       text-align: center;
    }


    #progress_menu {
        width: 50px;
        height: 50px;
        max-width: 150px;
        max-height: 150px;
        background: #e3effb; 
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        position: relative;
        transition: all 0.3s ease;
    }

    #progress_menu:hover {
        background: var(--success-color); 
        color: white;
    }

    

    #progress_menu:hover .red-icon {
    filter: brightness(0) invert(1);
    }


    #progress_menu:hover h6 {
        color: white;
    }
    .product-items>h6 {
        color: #74b2d4;
        font-size: 14px;
        font-weight: bolder; 
        text-wrap: balance;
    }
    

    #progress_menu>.product-items>p {
        color: white;
        font-size: 10px;
        text-wrap: balance;
    }

    .product-items {
        padding: 19px;
        border-radius: 50%;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

  #container_product {
        display: flex ;
        flex-direction: row;
        flex-wrap: nowrap;
        white-space: nowrap;
        width: 100%;
        justify-content: space-evenly;
    }


    .active_product {
        background: var(--success-color) !important; 
        color: white !important;
        font-weight: bold;

    }
     .progress_product {
        background: #B0D5F6 !important; 
        color: white !important;
        font-weight: bold;

    }
    .active_product .product-items h6 {
        color:white !important;
    }

    .active_product .product-items .red-icon {
        filter: brightness(0) invert(1);

    }

    .title {
            font-weight: bold;
    text-transform: uppercase;
    margin-bottom: 0;
    }

    .overview_table tr {
        background: white;
          border-bottom: 4px solid #f1f1f1;
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
                        <h2 class="content-header-title float-left mb-0">LEADPROZESS PHASE</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashbaord</a></li>
                                <li class="breadcrumb-item"><a href="{{ url('/new_lead_view') }}">Kunde</a></li>
                                 <li class="breadcrumb-item">Prozess</li>
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
                                            <div class="col-5 mb-1">
                                               <form   method="GET">
                                                    <fieldset>
                                                        <div class="input-group">
                                                            <input type="text" name="search" class="form-control" placeholder="Search by name, location, or employee" aria-describedby="button-addon2" value="{{ request('search') }}">
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
                                                <table class="table overview_table" >
                                                    <thead>
                                                         <tr style="background:white; "> 
                                                            <th  >NAME</th> 
                                                            <th >PRODUKT</th>    
                                                            <th>LEADS</th>   
                                                            <th>PLANUNG</th>   
                                                            <th>ANGEBOTE</th>   
                                                            <th>AUFTRÄGE</th>   
                                                            <th>PROJEKTE</th>     
                                                            <th>ABGESCHLOSSEN</th>     
                                                            <th>JUNK</th>     
                                                               
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $product_list = collect($productList);
                                                        @endphp
                                                      
                                                        @foreach ($product_list->unique(fn($product) => $product->product_id.'-'.$product->alternative_id) as $product)
                                                          <tr>
                                                             <td>
                                                                <p class="title">{{$product->customer_name}} {{ $product->customer_lastname}}</p>
                                                                <p>{{ $product->street}} {{$product->postcode}} {{$product->city}}</p>
                                                             </td>
                                                             <td>
                                                                @php
                                                                    $services = [
                                                                        'complete' => 'Komplettlösung',
                                                                        'montage' => 'Montage',
                                                                        'product' => 'Produkt',
                                                                        'plan' => 'Planung',
                                                                        'maintenance' => 'Wartung',
                                                                        'repair' => 'Reparatur',
                                                                        'emergency' => 'Notdienst',
                                                                        'others' => 'Sonstiges',
                                                                    ];
                                                                    $service = $services[$product->service] ?? $product->service;
                                                                    $status = $services[$product->res_status] ?? $product->res_status;
                                                                    $reason = $services[$product->reason] ?? $product->reason;
                                                                @endphp

                                                                @php
                                                                    $name = $lastname = $emp_image = $gender = $msg = $state = null;

                                                                    if (isset($productEmployees) && is_iterable($productEmployees)) {
                                                                        foreach ($productEmployees as $employee) {
                                                                            // Ensure both are the same type (cast if necessary)
                                                                            if ((string)$employee->id === (string)$product->current_employee) {
                                                                                $name = $employee->name;
                                                                                $lastname = $employee->lastname;
                                                                                $emp_image = $employee->image;
                                                                                $gender = $employee->gender;
                                                                                $state = $product->res_status ?? null;
                                                                                $msg = null;
                                                                                break;
                                                                            }
                                                                        }
                                                                    }

                                                                @endphp

                                                                @php
                                                                    $defaultImage = $gender === "Male" 
                                                                        ? asset('images/gender/male.png') 
                                                                        : asset('images/gender/female.png');

                                                                    $employeeImage = file_exists('images/employee/'.$emp_image) && $emp_image 
                                                                        ? asset('images/employee/'.$emp_image) 
                                                                        : $defaultImage;
                                                                @endphp 

                                                                <div class="d-flex flex-column align-items-center mr-1">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="circle" data-toggle="tooltip" data-original-title="{{ $product->article_group }}">
                                                                            {{ $product->initial }}
                                                                        </div>
                                                                        <div class="line"></div> 
                                                                        <div class="image" data-toggle="tooltip" data-original-title="{{ $name && $lastname ? $name . ' ' . $lastname : 'Nicht zugewiesen' }}">
                                                                            <img src="{{ $employeeImage }}" alt="Profile" 
                                                                                data-employee-id="{{ $product->current_employee ?? '' }}" 
                                                                                data-product-id="{{ $product->product_id }}" 
                                                                                data-new-lead-id="" 
                                                                                data-alternative-id="" 
                                                                                data-toggle="modal" data-target="#addEmployee"
                                                                                class="@if($status=='accept') profile @elseif($status=='reject') profile-r @else profile-s @endif">
                                                                        </div> 
                                                                    </div>

                                                                
                                                                    <div class="text">{{ $services[$product->service] ?? $product->service }}</div> 
                                                                </div>  
                                                                </td> 

                                                                <td> 
                                                                    @php
                                                                        $statusesMap = [
                                                                            'new' => 'Neue',
                                                                            'completed' => 'Abgeschlossen',
                                                                            'edited' => 'Bearbeiten',
                                                                            'confirm' => 'Bistichtigite',
                                                                            'junk' => 'Junk', 
                                                                        ];

                                                                        $currentStatus = $product->status; // Active status from lead_product_lists

                                                                        // Fetch status from lead_alternative_adds
                                                                        $lead_product = DB::table('lead_alternative_adds') 
                                                                            ->select('stage')
                                                                            ->where('id', '=', $product->alternative_id)
                                                                            ->where('lead_id', '=', $product->customer_id)
                                                                            ->first();
                                                                        
                                                                        $leadStatusText = $statusesMap[$lead_product->stage ?? 'new'] ?? 'Neue';
                                                                    @endphp

                                                                    <!-- LEADS -->
                                                                    <a id="progress_menu" href="javascript:void(0);" 
                                                                         data-employee-id="{{ $product->current_employee ?? '' }}" 
                                                                        data-product-id="{{ $product->product_id }}" 
                                                                        data-new-lead-id="{{ $product->customer_id }}" 
                                                                        data-alternative-id="{{ $product->alternative_id }}" 
                                                                        data-status="lead"
                                                                        class="pro_menu  
                                                                        @if($currentStatus == 'lead') 
                                                                            active_product 
                                                                        @else
                                                                            progress_product
                                                                        @endif">
                                                                        <div class="product-items">
                                                                            <img src="{{ asset('images/dashboard/icon_roket.svg') }}" alt="Gauge Icon" class="dashboard-image red-icon" style="width:40px !important"> 
                                                                        </div> 
                                                                        
                                                                    </a>   
                                                                    <h6 class="text">{{ $leadStatusText }}</h6> 
                                                                </td>

                                                                <td> 
                                                                    @php
                                                                        $plan_product = DB::table('planings') 
                                                                            ->select('status')
                                                                            ->where('customer_id', $product->customer_id)
                                                                            ->where('alternative_id', $product->alternative_id)
                                                                            ->where('product_id', $product->product_id)
                                                                            ->first();

                                                                        $planStatusText = $statusesMap[$plan_product->status ?? 'new'] ?? 'Neue';
                                                                    @endphp

                                                                    <!-- PLANUNG -->
                                                                    <a id="progress_menu" href="javascript:void(0);" 
                                                                         data-employee-id="{{ $product->current_employee ?? '' }}" 
                                                                        data-product-id="{{ $product->product_id }}" 
                                                                        data-new-lead-id="{{ $product->customer_id }}" 
                                                                        data-alternative-id="{{ $product->alternative_id }}" 
                                                                        data-status="plan"
                                                                        class="pro_menu 
                                                                        @if($currentStatus == 'plan') 
                                                                            active_product 
                                                                        @else
                                                                            progress_product
                                                                        @endif">
                                                                        <div class="product-items">
                                                                            <img src="{{ asset('images/dashboard/icon_gears.svg') }}" alt="Gauge Icon" class="dashboard-image red-icon" style="width:40px !important"> 
                                                                        </div>
                                                                        
                                                                    </a>  
                                                                    <h6 class="text">{{ $planStatusText }}</h6>
                                                                </td>

                                                                <td>
                                                                    @php
                                                                        $offer_product = DB::table('offers') 
                                                                            ->select('status')
                                                                            ->where('customer_id', $product->customer_id)
                                                                            ->where('alternative_id', $product->alternative_id)
                                                                            ->where('product_id', $product->product_id)
                                                                            ->first();

                                                                        $offerStatusText = $statusesMap[$offer_product->status ?? 'new'] ?? 'Neue';
                                                                    @endphp

                                                                    <!-- ANGEBOTE -->
                                                                    <a id="progress_menu" href="javascript:void(0);" 
                                                                         data-employee-id="{{ $product->current_employee ?? '' }}" 
                                                                        data-product-id="{{ $product->product_id }}" 
                                                                        data-new-lead-id="{{ $product->customer_id }}" 
                                                                        data-alternative-id="{{ $product->alternative_id }}" 
                                                                        data-status="offer"
                                                                        class="pro_menu 
                                                                        @if($currentStatus == 'offer')
                                                                            active_product 
                                                                        @else
                                                                            progress_product
                                                                        @endif">
                                                                        <div class="product-items">
                                                                            <img src="{{ asset('images/dashboard/icon_document.svg') }}" alt="Gauge Icon" class="dashboard-image red-icon" style="width:40px !important"> 
                                                                        </div>
                                                                     
                                                                    </a>  
                                                                       <h6 class="text">{{ $offerStatusText }}</h6>
                                                                </td>

                                                                <td>
                                                                    @php
                                                                        $deal_product = DB::table('deals') 
                                                                            ->select('status')
                                                                            ->where('customer_id', $product->customer_id)
                                                                            ->where('alternative_id', $product->alternative_id)
                                                                            ->where('product_id', $product->product_id)
                                                                            ->first();

                                                                        $dealStatusText = $statusesMap[$deal_product->status ?? 'new'] ?? 'Neue';
                                                                    @endphp

                                                                    <!-- AUFTRÄGE -->
                                                                    <a id="progress_menu" href="javascript:void(0);" 
                                                                         data-employee-id="{{ $product->current_employee ?? '' }}" 
                                                                        data-product-id="{{ $product->product_id }}" 
                                                                        data-new-lead-id="{{ $product->customer_id }}" 
                                                                        data-alternative-id="{{ $product->alternative_id }}" 
                                                                        data-status="deal"
                                                                        class="pro_menu 
                                                                        @if($currentStatus == 'deal')
                                                                            active_product 
                                                                        @else
                                                                            progress_product
                                                                        @endif">
                                                                        <div class="product-items">
                                                                            <img src="{{ asset('images/dashboard/icon_target.svg') }}" alt="Gauge Icon" class="dashboard-image red-icon" style="width:40px !important"> 
                                                                        </div>
                                                                     
                                                                    </a>  
                                                                       <h6 class="text">{{ $dealStatusText }}</h6>
                                                                </td>

                                                                <td> 
                                                                    @php
                                                                        $project_product = DB::table('projects') 
                                                                            ->select('status')
                                                                            ->where('customer_id', $product->customer_id)
                                                                            ->where('alternative_id', $product->alternative_id)
                                                                            ->where('product_id', $product->product_id)
                                                                            ->first();

                                                                        $projectStatusText = $statusesMap[$project_product->status ?? 'new'] ?? 'Neue';
                                                                    @endphp

                                                                    <!-- PROJEKTE -->
                                                                    <a id="progress_menu" href="javascript:void(0);" 
                                                                         data-employee-id="{{ $product->current_employee ?? '' }}" 
                                                                        data-product-id="{{ $product->product_id }}" 
                                                                        data-new-lead-id="{{ $product->customer_id }}" 
                                                                        data-alternative-id="{{ $product->alternative_id }}" 
                                                                        data-status="project"
                                                                        class="pro_menu 
                                                                        @if($currentStatus == 'project')
                                                                            active_product 
                                                                        @else
                                                                            progress_product
                                                                        @endif">
                                                                        <div class="product-items">
                                                                            <img src="{{ asset('images/dashboard/icon_memoboard.svg') }}" alt="Gauge Icon" class="dashboard-image red-icon" style="width:40px !important"> 
                                                                        </div>
                                                                      
                                                                    </a>   
                                                                      <h6 class="text">{{ $projectStatusText }}</h6>
                                                                </td>


                                                                  <td> 
                                                                    

                                                                    <!-- COMPLETE -->
                                                                    <a id="progress_menu" href="javascript:void(0);" 
                                                                         data-employee-id="{{ $product->current_employee ?? '' }}" 
                                                                        data-product-id="{{ $product->product_id }}" 
                                                                        data-new-lead-id="{{ $product->customer_id }}" 
                                                                        data-alternative-id="{{ $product->alternative_id }}" 
                                                                        data-status="completed"
                                                                        class="pro_menu 
                                                                         @if($currentStatus == 'completed') 
                                                                            active_product 
                                                                        @else
                                                                            progress_product
                                                                        @endif">
                                                                        <div class="product-items">
                                                                           <i class="feather icon-feather dashboard-image red-icon" style="    font-size: 16px;"></i>
                                                                        </div>
                                                                      
                                                                    </a>   
                                                                      <h6 class="text">{{ $projectStatusText }}</h6>
                                                                </td>


                                                                  <td> 
                                                              

                                                                    <!-- TICKET -->
                                                                    <a id="progress_menu" href="javascript:void(0);" 
                                                                         data-employee-id="{{ $product->current_employee ?? '' }}" 
                                                                        data-product-id="{{ $product->product_id }}" 
                                                                        data-new-lead-id="{{ $product->customer_id }}" 
                                                                        data-alternative-id="{{ $product->alternative_id }}" 
                                                                        data-status="ticket"
                                                                        class="pro_menu 
                                                                        @if($currentStatus == 'ticket')
                                                                            active_product 
                                                                        @else
                                                                            progress_product
                                                                        @endif">
                                                                        <div class="product-items"> 
                                                                            <img src="{{ asset('images/dashboard/icon_speaker.svg') }}" alt="Gauge Icon" class="dashboard-image red-icon" style="width:40px !important"> 

                                                                        </div>
                                                                      
                                                                    </a>   
                                                                      <h6 class="text">{{ $projectStatusText }}</h6>
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
                    <!-- Table head options end -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Content-->
@endsection
 
@section('script')  
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

<script>
document.getElementById('colaps').addEventListener('click', function() {
    var section = document.getElementById('upper_view');
    var icon = this.querySelector('i');
    
    if (section.style.display === 'none' || section.style.display === '') {
        section.style.display = 'block';
        icon.classList.remove('feather', 'icon-chevron-down');
        icon.classList.add('feather', 'icon-chevron-up');
    } else {
        section.style.display = 'none';
        icon.classList.remove('feather', 'icon-chevron-up');
        icon.classList.add('feather', 'icon-chevron-down');
    }
});
</script>

<script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script>
 
 
<script>
    document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.pro_menu').forEach(item => {
        item.addEventListener('click', function () {
            let employeeId = this.getAttribute('data-employee-id') || null;
            let productId = this.getAttribute('data-product-id');
            let customerId = this.getAttribute('data-new-lead-id');
            let alternativeId = this.getAttribute('data-alternative-id');
            let stage = this.getAttribute('data-status');
            let service = this.getAttribute('data-service') || 'default_service';

            Swal.fire({
                title: "Sind Sie sicher?",
                text: "Möchten Sie die Phase dieses Kunden wirklich ändern?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ja, ändern!",
                cancelButtonText: "Abbrechen"
            }).then((result) => {
                if (result.isConfirmed) {
                    changeStage(customerId, alternativeId, productId, employeeId, service, stage);
                }
            });
        });
    });

    function changeStage(customerId, alternativeId, productId, employeeId, service, stage) {
        let url = `/lead/kanban/${customerId}/${alternativeId}/${productId}/${employeeId}/${service}/${stage}`;

        fetch(url, {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire("Erfolgreich!", data.message, "success").then(() => {
                    location.reload(); // Reload to update UI
                });
            } else {
                Swal.fire("Fehler!", data.message, "error");
            }
        })
        .catch(error => {
            Swal.fire("Fehler!", "Beim Aktualisieren des Status ist ein Fehler aufgetreten.", "error");
        });
    }
});

</script>
@endsection
