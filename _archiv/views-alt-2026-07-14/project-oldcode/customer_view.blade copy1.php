@extends('admin.layouts.app')

@section('title') PROJEKT @stop

@section('style')
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
      font-size: 10px;
      font-weight: 500;
      color: #555;
      text-align: center;
      margin-top: 10px;
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
                        <h2 class="content-header-title float-left mb-0">PROJEKT</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Home</a></li>
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
                                        <div class="row" > 
                                            <div class="col-12 mb-1">
                                                <form action="{{ action('App\Http\Controllers\ProjectController@index') }}">
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
                                                         <tr style="background:#cfe09a; "> 
                                                            <th style="width: 45px;" >ID</th> 
                                                            <th  class="bolders ">DATUM</th> 
                                                            <th  class="bolders ">KUNDEN-NUMMER</th> 
                                                            <th  class="bolders ">NAME</th> 
                                                            <th  class="bolders ">OBJEKTNAME</th> 
                                                            <th  class="bolders ">KONTAKT</th> 
                                                            <th  class="bolders ">GEWERKE</th> 
                                                            <th  class="bolders ">BETEILIGTE PERSONEN</th> 
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
                                                            <th>PROJEKT-STATUS</th>
                                                            <th width="2">BEARBEITEN</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($data as $item)    
                                                            <tr style="background:white;border-bottom: 13px solid #f8f8f8;" class="mb-2"> 
                                                                <th scope="row">{{ $item->id }}</th>
                                                                
                                                                <td>
                                                                    <i class="feather icon-calendar"></i> {{ \Carbon\Carbon::parse($item->created_at)->isoFormat('DD.MM.YY') }} <br>
                                                                    <code> <strong> 
                                                                        {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}                                   
                                                                    </strong></code>  
                                                                </td>
                                                                <td>
                                                                    {{ $item->customer_no }}
                                                                </td>
                                                                <td><a href="{{url('new_lead_profile/'.$item->customer_id )}}">
                                                                        {{ $item->name }}  {{ $item->lastname }} <br>
                                                                        <small>
                                                                            <i class="feather icon-map-pin"></i> {{ $item->street }} <br>
                                                                                {{ $item->postcode }} <br>
                                                                                {{ $item->city }}
                                                                        </small>
                                                                    </a>
                                                                </td>
                                                                    
                                                                <td>
                                                                    <p class="mb-0" ><i class="feather icon-phone-call" ></i> {{ $item->telephone }}</p>
                                                                    <p class="mb-0" ><i class="feather icon-smartphone" ></i> {{ $item->phone }}</p>
                                                                    <p class="mb-0" ><i class="feather icon-mail" ></i> {{ $item->email }}</p>
                                                                </td> 
                                                                <td>
                                                                    {{ $item->object_name }}
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
                                                                 <td>
                                                                    <ul class="list-unstyled users-list m-0  d-flex align-items-center">
                                                                        @foreach ($project_employees as $p_emp) 
                                                                            @php  

                                                                                $member_list = [
                                                                                    'member'    =>  'Mitglied',
                                                                                    'guest'     =>  'Gast',
                                                                                    'comentator'    =>  'Kommentator{in}'
                                                                                ];
                                                                                $member = $member_list[$p_emp->member_type] ?? 'Mitglied unbekannt';


                                                                                $genderIcon = $p_emp->gender === 'Male' 
                                                                                    ? asset('images/gender/male.png') 
                                                                                    : asset('images/gender/female.png');

                                                                                $profileImage = !empty($p_emp->image) 
                                                                                    ? asset('images/employee/' . $p_emp->image) 
                                                                                    : $genderIcon;

                                                                                
                                                                            @endphp
                                                                            @if($p_emp->project_id == $item->id)
                                                                                <div class="change_employee" data-project="{{$item->id}}" data-employee="{{$p_emp->employee_id}}" data-toggle="modal" data-target="#employee_change"> 
                                                                                    <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" 
                                                                                        data-original-title="{{ $p_emp->name }} {{ $p_emp->lastname}} ({{$member}})" 
                                                                                        class="avatar pull-up">
                                                                                        <img class="media-object rounded-circle @if($p_emp->status=='send') send_request @elseif($p_emp->status=='accept') accept_request @else reject_request @endif" src="{{ $profileImage }}" 
                                                                                            alt="Avatar" height="30" width="30">
                                                                                            @if($p_emp->status=='send')
                                                                                            <span class="avatar-status-away"></span>
                                                                                            @elseif($p_emp->status=='accept')
                                                                                            <span class="avatar-status-offline"></span>
                                                                                            @else
                                                                                            <span class="avatar-status-busy" style="width: 13px;height: 13px;">x</span>
                                                                                            @endif
                                                                                    </li>
                                                                                </div>
                                                                                @if($p_emp->employee_id == auth()->user()->name)  
                                                                                    @if($p_emp->status=='send')
                                                                                    <button type="button" class="btn btn-outline-warning square mr-1 mb-1 waves-effect waves-light btn-sm" id="accept_button" data-project="{{$p_emp->project_id}}" data-employee="{{$p_emp->employee_id}}">Antwort</button>
                                                                                    @else
                                                                                    <button type="button" 
                                                                                            class="btn btn-outline-warning square mr-1 mb-1 waves-effect waves-light btn-sm change_employee" 
                                                                                            data-project="{{$p_emp->project_id}}" 
                                                                                            data-employee="{{$p_emp->employee_id}}">
                                                                                        Change
                                                                                    </button>

                                                                                    @endif
                                                                                    
                                                                                @endif
                                                                                
                                                                            @endif
                                                                        @endforeach 
                                                                    </ul>
                                                                </td> 
                                                                
                                                                <td>
                                                                    <div class="badge badge-primary">@if($item->status=="new") Neue @endif</div>
                                                                </td>
                                                                 
                                                                 <td> 
                                                                    <div class="prograss">
                                                                        <div class="main-grid-cell-inner" style="justify-self: center !important;">
                                                                            <span class="main-grid-cell-content" data-prevent-default="false" >
                                                                            
                                                                                    <table class="crm-list-stage-bar-table">
                                                                                        <tbody>
                                                                                            <tr>  
                                                                                                @php
                                                                                                    // Step 1: Initialize an array to store colors for each unique customer-product-phase if done is true
                                                                                                    $phaseColors = [];
                                                                                                @endphp

                                                                                                @foreach ($phases as $phase)
                                                                                                    @if ($phase->done == 'true')
                                                                                                        @php
                                                                                                            // Store the color specifically for each unique customer-product-phase combination
                                                                                                            $phaseColors[$phase->customer][$phase->product][$phase->phase_name] = $phase->color;
                                                                                                        @endphp
                                                                                                    @endif
                                                                                                @endforeach

                                                                                                <!-- Step 2: Display each phase, applying color only if done is true for that specific customer-product-phase -->
                                                                                                @if (!empty($phases) && count($phases) > 0)
                                                                                                @php $hasMatchingPhase = false; @endphp
                                                                                                @foreach ($phases as $phase)
                                                                                                    @if ($phase->customer == $item->customer_id && $phase->service == $item->service)
                                                                                                        @php $hasMatchingPhase = true; @endphp
                                                                                                        <td class="crm-list-stage-bar-part"
                                                                                                            style="background: {{ $phaseColors[$phase->customer][$phase->product][$phase->phase_name] ?? '#FFFFFF' }}; padding:10px; border: 1px solid #afafaf;"
                                                                                                            data-toggle="tooltip" data-placement="top" 
                                                                                                            title="{{ $phase->phase_name }}">
                                                                                                            <span style="color:gray">{{ $phase->phase_name }}</span>
                                                                                                        </td>
                                                                                                    @endif
                                                                                                @endforeach

                                                                                                @if (!$hasMatchingPhase)
                                                                                                    <td class="crm-list-stage-bar-part" style="padding:10px; border: 1px solid #afafaf; text-align: center;">
                                                                                                        <span style="color: red;">No matching phases found for this customer and service</span>
                                                                                                    </td>
                                                                                                @endif
                                                                                                @else
                                                                                                    <td class="crm-list-stage-bar-part" style="padding:10px; border: 1px solid #afafaf; text-align: center;">
                                                                                                        <span style="color: red;">Not defined</span>
                                                                                                    </td>
                                                                                                @endif 
                                                                                            </tr>  
                                                                                        </tbody>
                                                                                    </table>  
                                                                                    @foreach ($tasks as $task)
                                                                                        @if($task->customer_id == $item->customer_id && $task->product_id == $item->product_id) 
                                                                                                <a href="{{ url('customer_product_details/'.$item->id.'/'.$item->product_id.'/'.$item->alternative_id) }}#project-management"> {{ $task->task_title }} </a>
                                                                                        @endif
                                                                                    @endforeach 
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </td> 
                                                                <td> 
                                                                <div class="btn-group dropdown dropdown-icon-wrapper mr-1 mb-1"> 
                                                                    <button type="button" class="btn dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                        <i class="feather icon-menu dropdown-icon"></i>
                                                                    </button>
                                                                    <div class="dropdown-menu"> 
                                                                        @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_delete', '=', 'on')->first())
                                                                            @if($item->status!="junk")
                                                                            <span class="dropdown-item">
                                                                                <a data-toggle="modal" class="danger" data-target="#junk{{$item->id}}"><i class="fa fa-power-off danger" ></i> Junk</a>
                                                                            </span>
                                                                            @else
                                                                             <span class="dropdown-item">
                                                                                <a data-toggle="modal" class="danger" data-target="#unjunk{{$item->id}}"><i class="fa fa-power-off primary" ></i>Un-Junk</a>
                                                                            </span>
                                                                            @endif
                                                                        @endif

                                                                        @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_delete', '=', 'on')->first())
                                                                            @if($item->deleted_at == null)
                                                                            <span class="dropdown-item">
                                                                                <a data-toggle="modal" class="danger" data-target="#delete-pro{{$item->id}}"><i class="fa fa-power-off danger" ></i> Löschen</a>
                                                                            </span>
                                                                            @else
                                                                             <span class="dropdown-item">
                                                                                <a data-toggle="modal" class="danger" data-target="#delete-pro{{$item->id}}"><i class="fa fa-power-off primary" ></i>Wiederherstellen</a>
                                                                            </span>
                                                                            @endif
                                                                        @endif

                                    
                                                                        @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_update', '=', 'on')->first())
                                                                            
                                                                            <span class="dropdown-item">
                                                                                <a data-toggle="modal" class="primary" data-target="#skip{{$item->id}}"><i class="feather icon-fast-forward primary" ></i>Überspringen</a>
                                                                            </span>

                                                                              <span class="dropdown-item">
                                                                                <a   class="primary add_employee" data-project="{{$item->id}}" data-toggle="modal" data-target="#employee" ><i class="feather icon-users primary" ></i> Mitarbeiter zur Aufgabe hinzufügen</a>
                                                                            </span>

                                                                            

                                                                            <span class="dropdown-item">
                                                                                <form method="get" action="{{ route('customer.phase.manage.edit') }}">
                                                                                        @csrf
                                                                                        <input type="hidden" name="alternative_id" value="{{$item->alternative_id}}">
                                                                                        <input type="hidden" name="customer" value="{{$item->customer_id}}">
                                                                                        <input type="hidden" name="product" value="{{$item->product_id}}">
                                                                                        <input type="hidden" name="service" value="{{$item->service}}">
                                                                                    <button class="btn btn-flat-primary waves-effect m-0 p-0 waves-light" type="submit"  ><i class="feather icon-menu primary" ></i> Phase-Management</button>
                                                                                </form>                                                                             
                                                                            </span> 
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
                                                                                @if($item->deleted_at == null)
                                                                                <div class="modal-body">
                                                                                    <h5>Aufzeichnung löschen</h5>
                                                                                    <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                                    <p>Die Datensatznummer lautet:{{$item->id}}. {{ $item->name }} {{ $item->lastname }} </p>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <a type="button" href="{{url('/project_destroy').'/'.$item->id}}" class="btn btn-danger">Ja</a>
                                                                                </div>
                                                                                @else
                                                                                <div class="modal-body">
                                                                                    <h5>Datenwiederherstellung</h5>
                                                                                    <p>Möchten Sie diesen Datensatz wiederherstellen?</p>
                                                                                    <p>Die Datensatznummer lautet:{{$item->id}}. {{ $item->name }} {{ $item->lastname }} </p>
                                                                                    </div>
                                                                                <div class="modal-footer">
                                                                                    <a type="button" href="{{url('/project_restore').'/'.$item->id}}" class="btn btn-danger">Ja</a>
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
                                                                                    <a type="button" href="{{url('/project_junk').'/'.$item->id}}" class="btn btn-danger">Ja</a>
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
                                                                                    <a type="button" href="{{url('/project_unjunk').'/'.$item->id}}" class="btn btn-primary">Ja</a>
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


        <!-- Accept Request Modal  -->
        <div class="modal fade" id="acceptModal" tabindex="-1" role="dialog" aria-hidden="false">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary white">
                        <h5 class="modal-title" id="myModalLabel160">Stellenanfrage</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('project.task.accept') }}" method="post" id="accept-request-form">
                        @csrf
                        <div class="modal-body">
                            <p><i class="feather icon-info warning"></i> Sie wurden als Verantwortlicher für den folgenden Kunden ausgewählt</p>
                            <div class="row">
                                <input type="hidden" name="project_id" id="accept_project_id" value="">
                                <input type="hidden" name="employee_id" id="accept_employee_id" value="">
                                <div class="col-xl-12 col-md-12 col-12 mb-1">
                                    <fieldset class="form-group">
                                        <label for="response">Antwort anfordern</label>
                                        <select name="response" class="form-control" required>
                                            <option value="accept">Akzeptieren</option>
                                            <option value="reject">Ablehnen</option>
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-xl-12 col-md-12 col-12 mb-1">
                                    <fieldset class="form-group">
                                        <label for="reason">Notiz</label>
                                        <textarea name="reason" class="form-control" rows="5" placeholder="Optional"></textarea>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Speichern</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



            <!-- Modal for Adding Employee -->
        <div class="modal fade text-left" id="employee" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary white">
                        <h5 class="modal-title" id="myModalLabel160">Mitarbeiter hinzufügen</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="{{ route('add.employee.to.project')}}" method="post" id="add_employe_form">
                        @csrf
                        <input type="hidden" name="project_id" id="modal_project_id" value="">
                        <input type="hidden" name="old_employee" id="modal_old_employee" value="">
                        <div class="modal-body">
                            <label for="employee_id">Mitarbeiter auswählen</label>
                            <select name="employee_id[]" id="employee_id" class="form-control employee" style="width: 100%;" multiple="true">
                                @foreach ($employees as $emp)
                                    <option value="{{$emp->id}}" 
                                            data-image="{{asset('images/employee/'.$emp->image)}}">
                                        {{$emp->name}} {{$emp->lastname}}
                                    </option>
                                @endforeach
                            </select>

                            <label for="employee_roll">Mitarbeiterfunktion</label>
                            <select name="employee_roll" id="employee_id" class="form-control employee" style="width: 100%;" >
                                <option value="member">Mitglied</option>
                                <option value="guest">Gast</option>
                                <option value="comentator">Kommentator(in)</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary waves-effect waves-light" id="save-add-employee">Hinzufügen</button>
                            <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">Abbrechen</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
            
        <!-- Change Employee  -->
    <div class="modal fade text-left" id="change_employee" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary white">
                        <h5 class="modal-title" id="myModalLabel160">Mitarbeiter ändern</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="{{ route('update.employee.project') }}" method="post" id="change_employee_form">
                        @csrf
                        <input type="hidden" name="project_id" id="change_project_id" value="">
                        <input type="hidden" name="old_employee" id="change_old_employee" value="">
                        <div class="modal-body">
                            <label for="employee_id">Mitarbeiter auswählen</label>
                            <select name="employee_id" id="employee_id" class="form-control employee" style="width: 100%;">
                                @foreach ($employees as $emp)
                                    <option value="{{$emp->id}}" 
                                            data-image="{{asset('images/employee/'.$emp->image)}}">
                                        {{$emp->name}} {{$emp->lastname}}
                                    </option>
                                @endforeach
                            </select>

                            <label for="employee_roll">Mitarbeiterfunktion</label>
                            <select name="employee_roll" id="employee_roll" class="form-control" style="width: 100%;">
                                <option value="member">Mitglied</option>
                                <option value="guest">Gast</option>
                                <option value="comentator">Kommentator(in)</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Speichern</button>
                            <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">Abbrechen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
@endsection
 
@section('script')  
<script src="{{ asset('js/select2.min.js') }}"></script>

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

 
<script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script>
 



<!-- Add Employee to proejct: start  -->
 
<script>
    $(document).ready(function () {
        // Initialize Select2 with custom template for displaying employee image
        $('#employee_id').select2({
            templateResult: formatEmployee,
            templateSelection: formatEmployee,
            escapeMarkup: function (markup) {
                return markup;
            }
        });

        // Add Employee button click handler
        $('.add_employee').on('click', function () {
            let projectId = $(this).data('project');
            $('#modal_project_id').val(projectId); // Set project_id in hidden input
        });

        // Function to format employee dropdown with image
        function formatEmployee(emp) {
            if (!emp.id) {
                return emp.text;
            }
            var imageUrl = $(emp.element).data('image');
            var markup = `
                <div class="d-flex align-items-center">
                    <img src="${imageUrl}" alt="" class="rounded-circle" style="width: 30px; height: 30px; margin-right: 10px;">
                    <span>${emp.text}</span>
                </div>
            `;
            return markup;
        }

        // AJAX form submission
        $('#add_employe_form').on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            let form = $(this);
            let url = form.attr('action'); // Get form action URL
            let data = form.serialize(); // Serialize form data

            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function (response) {
                    // Show success message with SweetAlert
                    Swal.fire({
                        title: 'Erfolg',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Close modal and refresh the page after success
                        $('#employee').modal('hide');
                        location.reload();
                    });
                },
                error: function (xhr) {
                    // Show validation errors with SweetAlert
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessages = '';
                        for (let field in errors) {
                            errorMessages += `${errors[field][0]}<br>`;
                        }

                        Swal.fire({
                            title: 'Validierungsfehler',
                            html: errorMessages, // Display errors in HTML format
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            title: 'Fehler',
                            text: 'Ein unerwarteter Fehler ist aufgetreten.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });
    });
</script>

 <!-- Add Employee to proejct: end  -->

<!-- accepting request modal: start -->
 <script>
    $(document).ready(function () {
        // Open Modal and Populate Data
        $(document).on('click', '#accept_button', function () {
            const projectId = $(this).data('project');
            const employeeId = $(this).data('employee');

            // Populate hidden inputs in the modal
            $('#accept_project_id').val(projectId);
            $('#accept_employee_id').val(employeeId);

            // Open the modal
            $('#acceptModal').modal('show');
        });

        // Submit Form with AJAX
        $('#accept-request-form').on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            const form = $(this);
            const url = form.attr('action'); // Get form action URL
            const data = form.serialize(); // Serialize form data

            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function (response) {
                    // Show success alert
                    Swal.fire({
                        title: 'Erfolg',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Close modal and refresh the page
                        $('#acceptModal').modal('hide');
                        location.reload();
                    });
                },
                error: function (xhr) {
                    // Show error messages
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessages = '';
                        for (let field in errors) {
                            errorMessages += `${errors[field][0]}<br>`;
                        }

                        Swal.fire({
                            title: 'Fehler',
                            html: errorMessages,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            title: 'Fehler',
                            text: 'Ein unerwarteter Fehler ist aufgetreten.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });
    });
</script>

<!-- accepting request modal: end -->
<script>
    $(document).ready(function () {
        // Open Modal and Populate Data
        $(document).on('click', '.change_employee', function () {
            const projectId = $(this).data('project'); // Get data-project value
            const employeeId = $(this).data('employee'); // Get data-employee value

            // Debugging to ensure values are captured
            console.log("Project ID:", projectId);
            console.log("Employee ID:", employeeId);

             $('#change_project_id').val(projectId);
            $('#change_old_employee').val(employeeId);
            $('#change_employee').modal('show');

        });

        // Submit Form with AJAX
        $('#change_employee_form').on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            const form = $(this);
            const url = form.attr('action'); // Get form action URL
            const data = form.serialize(); // Serialize form data

            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function (response) {
                    // Show success alert
                    Swal.fire({
                        title: 'Erfolg',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Close modal and refresh the page
                        $('#change_employee').modal('hide');
                        location.reload();
                    });
                },
                error: function (xhr) {
                    // Show error messages
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessages = '';
                        for (let field in errors) {
                            errorMessages += `${errors[field][0]}<br>`;
                        }

                        Swal.fire({
                            title: 'Fehler',
                            html: errorMessages,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            title: 'Fehler',
                            text: 'Ein unerwarteter Fehler ist aufgetreten.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });
    });
</script>


<script>
    $(document).ready(function() {
    $('.employee').select2({
        templateResult: formatEmployee,
        templateSelection: formatEmployee,
        escapeMarkup: function(markup) {
            return markup;
        }
    });
});

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

</script>
 
@endsection
