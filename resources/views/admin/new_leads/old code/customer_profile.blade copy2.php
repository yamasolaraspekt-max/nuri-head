 @extends('admin.layouts.app')
 @section('title')
 KUNDE PROFILE
 @endsection

 @section('style')
 <link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" />
<script src="{{ asset('js/dropzone.min.js') }}"></script>

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
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">KUNDENPROFIL</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a>
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
                <!-- account setting page start -->
                <section id="page-account-settings">
                    <div class="row">
                        <!-- left menu section --> 
                            <div class="col-md-3 mb-2 mb-md-0">
                                <div class="col-xl-12 col-md-12 col-sm-12 profile-card-2">
                                <div class="card">
                                    <div class="card-header mx-auto pb-0">
                                        <div class="row m-0">
                                            <div class="col-sm-12 text-center">
                                                <h4>{{ $customer->title }} {{ $customer->name }} {{ $customer->lastname}}</h4>
                                            </div>
                                            <div class="col-sm-12 text-center"> 
                                                <p class="">{{$customer->street}} {{$customer->postcode}}, {{$customer->city}} <br> <small><code>   @if($customer->main == 1) Die Hauptadresse ist die Projektadresse @endif</code></small></p> 
                                                 <div class="chip chip-primary mr-1">
                                                        <div class="chip-body">
                                                            <span class="chip-text">Stage: @if($customer->stage == Null) Neue @else {{$customer->stage}} @endif</span>
                                                        </div>
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body text-center mx-auto"> 
                                            <div class="d-flex justify-content-between mt-1" style="text-align:left;">
                                                <div class="uploads">
                                                    <p class="mb-1"> <i class="feather icon-mail"></i> {{ $customer->email }}</p> 
                                                    <p class="mb-1"> <i class="feather icon-phone"></i> {{ $customer->telephone }}</p> 
                                                    <p class="mb-0"> <i class="feather icon-mail"></i> {{ $customer->phone }}</p>  
                                                </div>
                                                
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <ul class="nav nav-pills flex-column mt-md-0 mt-1">
                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75 active" id="account-pill-general" data-toggle="pill" href="#account-vertical-general" aria-expanded="true"> 
                                        PRODUKT & LEISTUNG
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75" id="account-pill-password" data-toggle="pill" href="#account-vertical-password" aria-expanded="false"> 
                                       OBJEKT & ENERGIEDATEN 
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75" id="account-pill-info" data-toggle="pill" href="#account-vertical-info" aria-expanded="false">
                                      
                                       BILDER & DOKOMENTE
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75" id="account-pill-social" data-toggle="pill" href="#account-vertical-social" aria-expanded="false">
                                        <i class="feather icon-camera mr-50 font-medium-3"></i>
                                        Social links
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75" id="account-pill-connections" data-toggle="pill" href="#account-vertical-connections" aria-expanded="false">
                                        <i class="feather icon-feather mr-50 font-medium-3"></i>
                                        Connections
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75" id="account-pill-notifications" data-toggle="pill" href="#account-vertical-notifications" aria-expanded="false">
                                        <i class="feather icon-message-circle mr-50 font-medium-3"></i>
                                        Notifications
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- right content section -->
                        <div class="col-md-9">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <div role="tabpanel" class="tab-pane active" id="account-vertical-general" aria-labelledby="account-pill-general" aria-expanded="true">
                                                <p>PRODUKT & LEISTUNG</p>
                                                <hr>
                                                <form novalidate>
                                                   <div class="col-xl-12 col-md-12 col-12 mb-1">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Produkt</th>
                                                                    <th>Leistung</th>
                                                                    <th>Verantwortlich</th>
                                                                    <th>Status</th>
                                                                    <th>Aktion</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($product_list->filter(fn($product) => $product->customer_id == $customer->id) as $product)
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
                                                                            <td>  
                                                                                {{ $loop->index + 1 }}
                                                                            </td>
                                                                            <td>
                                                                                    <div class="circle">{{ $product->initial }}</div>
                                                                            </td>
                                                                            <td>
                                                                                {{ $services[$product->service] ?? $product->service }}
                                                                            </td>
                                                                            <td>
                                                                                
                                                                                    <div class="image" data-toggle="tooltip" 
                                                                                    data-original-title="{{ $name && $lastname ? $name . ' ' . $lastname : 'Nicht zugewiesen' }}">
                                                                                    <img src="{{ $employeeImage }}" alt="Profile" 
                                                                                        data-employee-id="{{ $employee->id }}" 
                                                                                        data-product-id="{{ $product->product_id }}" 
                                                                                        data-new-lead-id="{{ $customer->id }}" 
                                                                                        data-toggle="modal" data-target="#addEmployee"
                                                                                    
                                                                                    class="@if($status=='accept') profile @elseif($status=='reject') profile-r @else profile-s @endif">
                                                                                {{ $name && $lastname ? $name . ' ' . $lastname : 'Nicht zugewiesen' }}

                                                                                </div> 
                                                                            </td> 
                                                                            <td>
                                                                            @if($status=='accept')  Aufgabe akzeptiert @elseif($status=='reject') Kunde abgelehnt @else Warten auf Annahme @endif 
                                                                            </td>
                                                                            <td>
                                                                            @if($status=='accept' & $p_status != 'plan' )
                                                                            <button type="button"  
                                                                                class="btn btn-outline-primary waves-effect waves-light p-1 sendToPlaning"
                                                                                data-employee="{{ $employee->id }}" 
                                                                                data-product="{{ $product->product_id }}" 
                                                                                data-customer="{{ $item->id }}"
                                                                                data-service = "{{$product->service}}"
                                                                                data-product-list="{{ $product->p_list_id}}">
                                                                                <i class="feather icon-arrow-right"></i> Weiter zur Planung
                                                                            </button> 
                                                                            @endif
                                                                            </td>
                                                                        </tr> 
                                                                @endforeach  
                                                            </tbody>
                                                        </table>
                                                    </div> 
                                                </form>
                                            </div>
                                            <div class="tab-pane fade " id="account-vertical-password" role="tabpanel" aria-labelledby="account-pill-password" aria-expanded="false">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <!-- Objektdaten Section -->
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
                                                                    <label>{{ $customer->objective ?? 'Bitte wählen' }}</label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <p class="bold">Baujahr Ihres Hauses?</p>
                                                                </div>
                                                                <div class="col-md-12 flex_me">
                                                                    <label>{{ $customer->house_year ?? 'N/A' }}</label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <p class="bold">Wieviel Wohneinheit hat das Objekt?</p>
                                                                </div>
                                                                <div class="col-md-12 flex_me">
                                                                    <label>{{ $customer->number_we ?? 'N/A' }}</label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <p class="bold">Wieviel Geschoß hat das Objekt?</p>
                                                                </div>
                                                                <div class="col-md-12 flex_me">
                                                                    <label>{{ $customer->number_stories ?? 'N/A' }}</label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <p class="bold">Wie groß ist die Beheizte Wohnfläche?</p>
                                                                </div>
                                                                <div class="col-md-12 flex_me">
                                                                    <label>{{ $customer->living_space ?? 'N/A' }} m²</label>
                                                                </div>  
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <p class="bold">Wie groß ist die Nutzfläche?</p>
                                                                </div>
                                                                <div class="col-md-12 flex_me">
                                                                    <label>{{ $customer->unusable_space ?? 'N/A' }} m²</label>
                                                                </div>  
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <p class="bold">Wieviel Personen wohnen in diesem Objekt?</p>
                                                                </div>
                                                                <div class="col-md-12 flex_me">
                                                                    <label>{{ $customer->number_people ?? 'N/A' }}</label>
                                                                </div>  
                                                            </div>
                                                        </div>

                                                        <!-- Dach-Information Section -->
                                                        <div class="col-12">
                                                            <p class="primary"><strong>DACH-INFORMATION</strong></h2>
                                                            <hr>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <p class="bold">Welche Art vom Dach haben Sie?</p>
                                                                </div>
                                                                <div class="col-md-12 flex_me">
                                                                    <label>{{ $customer->roof_type ?? 'N/A' }}</label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <p class="bold">Wie alt ist Ihr Dach?</p>
                                                                </div>
                                                                <div class="col-md-12 flex_me">
                                                                    <label>{{ $customer->roof_age ?? 'N/A' }} Jahr</label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <p class="bold">Welche Dacheindeckung hat das Dach?</p>
                                                                </div>
                                                                <div class="col-md-12 flex_me">
                                                                    <label>{{ $customer->tile_name ?? 'N/A' }}</label>
                                                                </div>
                                                            </div>
                                                        </div>  
                                                        <div class="col-12">
                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <p class="bold">Welche dachneigung hat ihr Dach?</p>
                                                                </div>
                                                                <div class="col-md-12 flex_me">
                                                                    <label>{{ $customer->roof_pitch ?? 'N/A' }}</label>
                                                                </div>
                                                            </div>
                                                        </div> 
                                                        <div class="col-12">
                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <p class="bold">Welche himmelsausrechtung hat ihr Dach?</p>
                                                                </div>
                                                                <div class="col-md-12 flex_me">
                                                                <select name="roof_direction" id="" class="form-control" disabled> 
                                                                        <option value="0" @if($customer->roof_direction == 0) selected @endif>Süden</option>
                                                                        <option value="45" @if($customer->roof_direction == 45) selected @endif>Süd-west</option>
                                                                        <option value="90" @if($customer->roof_direction == 90) selected @endif>Westen</option>
                                                                        <option value="135" @if($customer->roof_direction == 135) selected @endif>Nord-west</option>
                                                                        <option value="180" @if($customer->roof_direction == 180) selected @endif>Norden</option>
                                                                        <option value="-135" @if($customer->roof_direction == -135) selected @endif>Nord-ost</option>
                                                                        <option value="-90" @if($customer->roof_direction == -90) selected @endif>Osten</option>
                                                                        <option value="-45" @if($customer->roof_direction == -45) selected @endif>Süd-ost</option>  
                                                                    </select> 
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
                                                                    <label>{{ $customer->heating_system_type ?? 'N/A' }}</label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <p class="bold">Wie alt ist Ihre Heizungsanlage?</p>
                                                                </div>
                                                                <div class="col-md-12 flex_me">
                                                                    <label>{{ $customer->heating_system_age ?? 'N/A' }} Jahr</label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <p class="bold">Baujahr der Heizungsanlage?</p>
                                                                </div>
                                                                <div class="col-md-12 flex_me">
                                                                    <label>{{ $customer->heating_system_year ?? 'N/A' }}</label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <p class="bold">Welches Heizsystem ist verbaut?</p>
                                                                </div>
                                                                <div class="col-md-12 flex_me">
                                                                    <label>{{ $customer->heating_type ?? 'N/A' }}</label>
                                                                </div>  
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <p class="bold">Wo befindet sich die aktuelle Heizungsanlage?</p>
                                                                </div>
                                                                <div class="col-md-12 flex_me">
                                                                    <label>{{ $customer->installation_location ?? 'N/A' }}</label>
                                                                    <label>{{ $customer->installation_location_extra ?? '' }}</label>
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
                                                                    <label>{{ $customer->annual_consumption ?? 'N/A' }} kWh</label>
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
                                                                    @if($customer->annual_heating_energy_consumption)<label>{{ $customer->annual_heating_energy_consumption ?? 'N/A' }} CMB</label>@endif
                                                                    @if($customer->annual_heating_energy_consumption_kwh)<label>{{ $customer->annual_heating_energy_consumption_kwh ?? 'N/A' }} kWh</label>@endif
                                                                
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
                                                                    <label>{{ $customer->electric_car ?? 'N/A' }}</label>
                                                                </div>
                                                                <div class="col-md-6 flex_me">
                                                                    <label>{{ $customer->electric_car_plan ?? '' }}</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <p class="bold">Wieviel Kilometer Fahren Sie pro PKW im Jahr?</p>
                                                                </div>
                                                                <div class="col-md-6 flex_me">
                                                                    <label>{{ $customer->car_kilo ?? 'N/A' }} km</label>
                                                                </div>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> 
                                                <button type="button" class="btn btn-flat-primary mr-1 mb-1 waves-effect waves-light" data-toggle="modal" data-target="#editenergy">
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
                                                                <input type="hidden" name="id" value="{{$customer->id}}"> 
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
                                                                                            <input type="text" class="form-control form-element" name="house_year" id="house_year" value="" />
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                    
                                                                                <div class="col-12">
                                                                                    <div class="form-group row form-element">
                                                                                        <div class="col-md-12">
                                                                                            <p class="bold">Wieveil Wohneinheit hat das Obejekt?</p>
                                                                                        </div>
                                                                                        <div class="col-md-12 flex_me">
                                                                                            <input type="text" class="form-control textbox" name="number_we" value="">
                                                                                        
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-12">
                                                                                    <div class="form-group row form-element">
                                                                                        <div class="col-md-12">
                                                                                            <p class="bold">Wieviel Geschoß hat das Objekt?   </p>
                                                                                        </div>
                                                                                        <div class="col-md-12 flex_me">
                                                                                        <input type="text" class="form-control"  name="number_stories" value="">
                                                                                        
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-12">
                                                                                    <div class="form-group row form-element">
                                                                                        <div class="col-md-12">
                                                                                            <p class="bold">Wie groß ist die Beheizte Wohnfläche?</p>
                                                                                        </div>
                                                                                        <div class="col-md-12 flex_me">
                                                                                        <input type="text" class="form-control" name="living_space" value="">
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
                                                                                        <input type="text" class="form-control" name="unusable_space"  value="">
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
                                                                                        <input type="text" class="form-control" name="number_people" id="number_people"  value="" > 
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
                                                                                            <input type="text" class="form-control form-element" name="roof_age" id="roof_age" value="" />
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
                                                                                            <input type="text" class="form-control textbox" name="tile_name" value="">
                                                                                        
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
                                                                                            <input type="text" class="form-control textbox" name="tile_name" value=""> 
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
                                                                                            <input type="text" class="form-control textbox" name="roof_pitch" value=""> 
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
                                                                                            <input type="text" class="form-control form-element" name="heating_system_age" id="heating_system_age" value=""/>
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
                                                                                            <input type="text" class="form-control form-element" name="heating_system_year" id="heating_system_year" value="" />
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
                                                                                            <input type="text" class="form-control form-element" name="annual_consumption" value="{{ old('annual_consumption')}}"  />
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
                                                                                            <input type="text" class="form-control form-element mr-1" name="annual_heating_energy_consumption" id="annual_heating_energy_consumption" value="{{ old('annual_heating_energy_consumption')}}" />
                                                                                            <span  id="heat-energy">m³</span>
                                                                                            <input type="text" class="form-control form-element mr-1" name="annual_heating_energy_consumption_kwh" id="annual_heating_energy_consumption_kwh"  value="{{ old('annual_heating_energy_consumption_kwh')}}" /> 
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
                                                                                            <input type="text" class="form-control form-element" name="electric_car_plan" id="electric_car_plan" value="{{ old('electric_car_plan')}}" style="display:none;" />
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
                                                                                            <input type="text" class="form-control form-element" name="car_kilo" value="{{ old('car_kilo')}}"  />
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
                                            <div class="tab-pane fade" id="account-vertical-info" role="tabpanel" aria-labelledby="account-pill-info" aria-expanded="false">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <button type="button" class="btn btn-outline-warning waves-effect waves-light" data-toggle="modal" data-target="#large">
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
                                                                                <form action="{{ route('customer.upload') }}" method="POST" class="dropzone" id="file-dropzone" enctype="multipart/form-data" style="background: transparent; border: 1px dashed #8fc73e; border-radius: 20px;">
                                                                                    @csrf
                                                                                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                                                                    <input type="hidden" name="address_no" value="{{ $customer->address_no}}">
                                                                                    <input type="hidden" name="product_id" id="image_product_id" value="">
                                                                                    <input type="hidden" name="stage_id" id="stage_id" value="">

                                                                                    <div>
                                                                                        <label for="article_group">Gewerke auswählen:</label>
                                                                                        <select id="article_group" class="form-control">
                                                                                            <option value="">-- Wählen Sie eine Artikelgruppe --</option> 
                                                                                            <!-- Options will be dynamically populated -->
                                                                                        </select>
                                                                                    </div>
                                                                                    <div>
                                                                                        <label for="swal-stage">Stufe auswählen:</label>
                                                                                        <select id="swal-stage" class="form-control">
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
                                                        <div class="col-md-6 col-12">
                                                            <div class="text-bold-600 font-medium-2">
                                                                <i class="feather icon-search"></i> Filter
                                                            </div> 
                                                            <fieldset class="form-group">
                                                                <select class="form-control" id="filter_image">
                                                                    <option >FILTER AUSWÄHLEN</option>                                                        
                                                                    <option value="1">KUNDENBILD FILTERN</option>
                                                                    <option value="2">MONTAGEBILD FILTERN</option>
                                                                    <option value="3">ENDBILD FILTERN</option>
                                                                    <option value="4">ARTIKELBILD FILTERN</option>
                                                                    <option value="5">ALLE FOTOS</option>
                                                                </select>
                                                            </fieldset>
                                                        </div>

                                                        <div class="photo" id="photo_image" >
                                                            <div class="col-12 mt-2">
                                                                <div class="divider">
                                                                    <div class="divider-text">FOTO</div>
                                                                </div>
                                                            </div> 
                                                            <div class="row mt-2">  
                                                                @foreach ($images as $image) 
                                                                    @if(in_array(strtolower($image->file_type), ['jpeg', 'jpg', 'png', 'gif']))
                                                                        <div class="col-md-3">
                                                                            <div class="card-content">
                                                                                <img class="card-img-top img-fluid open-modal" src="{{ asset('images/customers/'.$image->image) }}" alt="{{ $image->image_name }}" data-image="{{ asset('images/customers/'.$image->image) }}">
                                                                                <div class="card-body p-0">
                                                                                    <h6 class="card-title edit_image_name mt-1" data-id="{{ $image->id }}">{{ $image->image_name }}</h6>
                                                                                    <input type="text" data-id="{{$image->id}}" name="image_name" value="{{$image->image_name}}" class="form-control" style="display:none;">
                                                                                </div>
                                                                                <div class="card-footer p-0 mt-1"> 
                                                                                    <button type="button" class="btn btn-icon btn-flat-danger mr-1 waves-effect waves-light" data-id="{{$image->id}}"><i class="feather icon-trash"></i> Löschen</button> 
                                                                                </div>
                                                                            </div>
                                                                        </div> 
                                                                    @endif
                                                                @endforeach 
                                                            </div> 
                                                        </div>
                                                        
                                                        <div class="article_image col-12" id="article_image" style="display:none" >
                                                            <div class="col-12 mt-2">
                                                                <div class="divider">
                                                                    <div class="divider-text">SORTIEREN NACH GEWERK</div>
                                                                </div>
                                                            </div> 
                                                            <div class="col-md-12">  
                                                                @foreach ($image_p_sort as $group => $images) <!-- $group is the article_group name, $images is the array of images -->
                                                                    <div class="default-collapse collapse-bordered">
                                                                        <div class="cards collapse-header">
                                                                            <div id="headingCollapse{{ $group }}" class="card-header" data-toggle="collapse" role="button" data-target="#collapse{{ $group }}" aria-expanded="false" aria-controls="collapse{{ $group }}"
                                                                                style="background: transparent; border-bottom: 1px solid #8fc73e;">
                                                                                <div class="lead collapse-title col-12">
                                                                                    <h2 class="primary bold">{{ $group }}</h2> <!-- Display the article_group name -->
                                                                                </div>
                                                                            </div>
                                                                            <div id="collapse{{ $group }}" role="tabpanel" aria-labelledby="headingCollapse{{ $group }}" class="collapse">
                                                                                <div class="card-content">
                                                                                    <div class="card-body">
                                                                                        <div class="row">
                                                                                            @foreach ($images as $pImage) <!-- Loop through each image in the article_group -->
                                                                                                <div class="col-md-3">
                                                                                                    <div class="card-content">
                                                                                                        <img class="card-img-top img-fluid open-modal" src="{{ asset('images/customers/'.$pImage->image) }}" alt="{{ $pImage->image_name }}" data-image="{{ asset('images/customers/'.$pImage->image) }}">
                                                                                                        <div class="card-body p-0">
                                                                                                            <h6 class="card-title edit_image_name mt-1" data-id="{{ $pImage->id }}">{{ $pImage->image_name }}</h6>
                                                                                                            <input type="text" data-id="{{ $pImage->id }}" name="image_name" value="{{ $pImage->image_name }}" class="form-control" style="display:none;">
                                                                                                        </div>
                                                                                                        <div class="card-footer p-0 mt-1"> 
                                                                                                            <button type="button" class="btn btn-icon btn-flat-danger mr-1 waves-effect waves-light" data-id="{{ $pImage->id }}">
                                                                                                                <i class="feather icon-trash"></i> Löschen
                                                                                                            </button> 
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            @endforeach
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>   
                                                                    </div>
                                                                @endforeach 
                                                            </div>

                                                        </div>
                                            
                                                        <div class="customer_image col-12" id="customer_image"  style="display:none" >
                                                            <div class="col-12 mt-2">
                                                                <div class="divider">
                                                                    <div class="divider-text">SORTIEREN NACH KUNDE</div>
                                                                </div>
                                                            </div> 
                                                            <div class="col-md-12">
                                                                <div class="default-collapse collapse-bordered">
                                                                    <div class="cards collapse-header">
                                                                        <div id="headingCustomer" class="card-header " data-toggle="collapse" role="button" data-target="#collapseCustomer" aria-expanded="false" aria-controls="collapseCustomer"
                                                                            style="background: transparent; border-bottom: 1px solid #8fc73e;">
                                                                            <div class="lead collapse-title col-12">
                                                                                <h2 class="primary bold">BILDER VOM KUNDE</h2> <!-- Display 'Customer' as the group header -->
                                                                            </div>
                                                                        </div>
                                                                        <div id="collapseCustomer" role="tabpanel" aria-labelledby="headingCustomer" class="collapse show">
                                                                            <div class="card-content">
                                                                                <div class="card-body">
                                                                                    <div class="row">
                                                                                        @foreach ($image_c_sort['customer'] as $pImage) <!-- Loop through each image under 'customer' stage -->
                                                                                            <div class="col-md-3">
                                                                                                <div class="card-content">
                                                                                                    <img class="card-img-top img-fluid open-modal" src="{{ asset('images/customers/'.$pImage->image) }}" alt="{{ $pImage->image_name }}" data-image="{{ asset('images/customers/'.$pImage->image) }}">
                                                                                                    <div class="card-body p-0">
                                                                                                        <h6 class="card-title edit_image_name mt-1" data-id="{{ $pImage->id }}">{{ $pImage->image_name }}</h6>
                                                                                                        <input type="text" data-id="{{ $pImage->id }}" name="image_name" value="{{ $pImage->image_name }}" class="form-control" style="display:none;">
                                                                                                    </div>
                                                                                                    <div class="card-footer p-0 mt-1"> 
                                                                                                        <button type="button" class="btn btn-icon btn-flat-danger mr-1 waves-effect waves-light" data-id="{{ $pImage->id }}">
                                                                                                            <i class="feather icon-trash"></i> Löschen
                                                                                                        </button> 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        @endforeach
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>   
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="montage_image col-12" id="montage_image"  style="display:none" >
                                                            <div class="col-12 mt-2">
                                                                <div class="divider">
                                                                    <div class="divider-text">SORTIEREN NACH MONTAGE</div>
                                                                </div>
                                                            </div> 
                                                            <div class="col-md-12">
                                                                <div class="default-collapse collapse-bordered">
                                                                    <div class="cards collapse-header">
                                                                        <div id="headingCustomer" class="card-header" data-toggle="collapse" role="button" data-target="#collapseMontage" aria-expanded="false" aria-controls="collapseMontage"
                                                                            style="background: transparent; border-bottom: 1px solid #8fc73e;">
                                                                            <div class="lead collapse-title col-12">
                                                                                <h2 class="primary bold">BILDER VOM MONTAGE</h2> <!-- Display 'Customer' as the group header -->
                                                                            </div>
                                                                        </div>
                                                                        <div id="collapseMontage" role="tabpanel" aria-labelledby="headingMontage" class="collapse show">
                                                                            <div class="card-content">
                                                                                <div class="card-body">
                                                                                    <div class="row">
                                                                                        @foreach ($image_m_sort['montage'] as $pImage) <!-- Loop through each image under 'customer' stage -->
                                                                                            <div class="col-md-3">
                                                                                                <div class="card-content">
                                                                                                    <img class="card-img-top img-fluid open-modal" src="{{ asset('images/customers/'.$pImage->image) }}" alt="{{ $pImage->image_name }}" data-image="{{ asset('images/customers/'.$pImage->image) }}">
                                                                                                    <div class="card-body p-0">
                                                                                                        <h6 class="card-title edit_image_name mt-1" data-id="{{ $pImage->id }}">{{ $pImage->image_name }}</h6>
                                                                                                        <input type="text" data-id="{{ $pImage->id }}" name="image_name" value="{{ $pImage->image_name }}" class="form-control" style="display:none;">
                                                                                                    </div>
                                                                                                    <div class="card-footer p-0 mt-1"> 
                                                                                                        <button type="button" class="btn btn-icon btn-flat-danger mr-1 waves-effect waves-light" data-id="{{ $pImage->id }}">
                                                                                                            <i class="feather icon-trash"></i> Löschen
                                                                                                        </button> 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        @endforeach
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>   
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                            
                                                        <div class="end_image col-12"id="end_image" style="display:none" >
                                                            <div class="col-12 mt-2">
                                                                <div class="divider">
                                                                    <div class="divider-text">SORTIEREN NACH ABNAHME</div>
                                                                </div>
                                                            </div> 
                                                            <div class="col-md-12">
                                                                <div class="default-collapse collapse-bordered">
                                                                    <div class="cards collapse-header">
                                                                        <div id="headingCustomer" class="card-header" data-toggle="collapse" role="button" data-target="#collapseend" aria-expanded="false" aria-controls="collapseCustomer"
                                                                            style="background: transparent; border-bottom: 1px solid #8fc73e;">
                                                                            <div class="lead collapse-title col-12">
                                                                                <h2 class="primary bold">BILDER VOM ABNAHME</h2> <!-- Display 'End' as the group header -->
                                                                            </div>
                                                                        </div>
                                                                        <div id="collapseend" role="tabpanel" aria-labelledby="headingCustomer" class="collapse show">
                                                                            <div class="card-content">
                                                                                <div class="card-body">
                                                                                    <div class="row"> 
                                                                                        <div class="col-md-3">
                                                                                            <div class="card-content">
                                                                                                <img class="card-img-top img-fluid open-modal" src="" alt="" data-image="">
                                                                                                <div class="card-body p-0">
                                                                                                    <h6 class="card-title edit_image_name mt-1" data-id=""></h6>
                                                                                                    <input type="text" data-id="" name="image_name" value="" class="form-control" style="display:none;">
                                                                                                </div>
                                                                                                <div class="card-footer p-0 mt-1"> 
                                                                                                    <button type="button" class="btn btn-icon btn-flat-danger mr-1 waves-effect waves-light" data-id="">
                                                                                                        <i class="feather icon-trash"></i> Löschen
                                                                                                    </button> 
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
            
                                                            <!-- Document Section with Click Event to Open Modal -->
                                                        <div class="col-12">
                                                            <div class="divider">
                                                                <div class="divider-text">DUKUMENT</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 mt-2 d-flex"> 
                                                            @foreach ($images as $image) 
                                                                @if(in_array(strtolower($image->file_type), ['pdf', 'docx', 'xlsx', 'doc'])) 
                                                                    <div class="col-md-5">
                                                                        <div class="card-content">
                                                                            <div class="file-preview" style="text-align: center; padding: 10px;">
                                                                                @if(strtolower($image->file_type) === 'pdf')
                                                                                    <iframe src="{{ asset('images/customers/'.$image->image) }}" frameborder="0" style="width: 100%; height: 150px;"></iframe>
                                                                                @elseif(strtolower($image->file_type) === 'docx' || strtolower($image->file_type) === 'doc')
                                                                                    <i class="fa fa-file-word-o primary" style="font-size: 50px; color: #007bff;"></i>
                                                                                @elseif(strtolower($image->file_type) === 'xlsx')
                                                                                    <i class="fa fa-file-excel-o primary" style="font-size: 50px; color: #28a745;"></i>
                                                                                @endif
                                                                            </div>
                                                                            <div class="card-body">
                                                                                <h6 class="card-title edit_image_name" data-id="{{ $image->id }}">
                                                                                    <span class="open-document" data-file-type="{{ $image->file_type }}" 
                                                                                    data-file-name="{{ $image->image_name }}" data-file-url="{{ asset('images/customers/'.$image->image) }}" 
                                                                                    data-toggle="tooltip" data-placement="top" title="" data-original-title="Klicken Sie hier, um die Datei zu öffnen">
                                                                                    <strong > {{ $image->image_name }}</strong>
                                                                                    </span> 
                                                                                </h6>  
                                                                                <input type="text" data-id="{{$image->id}}" name="image_name" value="{{$image->image_name}}" class="form-control">
                                                                            </div>
                                                                            <div class="card-footer"> 
                                                                                <button type="button" class="btn btn-icon btn-flat-danger mr-1 waves-effect waves-light" data-id="{{$image->id}}"><i class="feather icon-trash"></i> Delete</button>  
                                                                            </div>
                                                                        </div>
                                                                    </div> 
                                                                @endif
                                                            @endforeach 
                                                        </div>
                                                
                                                        <!-- Modal with Range-Based Zoom and Dynamic Title -->
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
                                                        <!-- Image Preview Modal -->

                                                        <!-- Document Modal with Icon or PDF Preview -->
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
                                                        <!-- Document Dialog: End  --> 
                                                    </div> 
                                            </div>
                                            <div class="tab-pane fade " id="account-vertical-social" role="tabpanel" aria-labelledby="account-pill-social" aria-expanded="false">
                                                <form>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="account-twitter">Twitter</label>
                                                                <input type="text" id="account-twitter" class="form-control" placeholder="Add link" value="https://www.twitter.com">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="account-facebook">Facebook</label>
                                                                <input type="text" id="account-facebook" class="form-control" placeholder="Add link">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="account-google">Google+</label>
                                                                <input type="text" id="account-google" class="form-control" placeholder="Add link">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="account-linkedin">LinkedIn</label>
                                                                <input type="text" id="account-linkedin" class="form-control" placeholder="Add link" value="https://www.linkedin.com">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="account-instagram">Instagram</label>
                                                                <input type="text" id="account-instagram" class="form-control" placeholder="Add link">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="account-quora">Quora</label>
                                                                <input type="text" id="account-quora" class="form-control" placeholder="Add link">
                                                            </div>
                                                        </div>
                                                        <div class="col-12 d-flex flex-sm-row flex-column justify-content-end">
                                                            <button type="submit" class="btn btn-primary mr-sm-1 mb-1 mb-sm-0">Save
                                                                changes</button>
                                                            <button type="reset" class="btn btn-outline-warning">Cancel</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="tab-pane fade" id="account-vertical-connections" role="tabpanel" aria-labelledby="account-pill-connections" aria-expanded="false">
                                                <div class="row">
                                                    <div class="col-12 mb-3">
                                                        <a href="javascript: void(0);" class="btn btn-info">Connect to
                                                            <strong>Twitter</strong></a>
                                                    </div>
                                                    <div class="col-12 mb-3">
                                                        <button class=" btn btn-sm btn-secondary float-right">edit</button>
                                                        <h6>You are connected to facebook.</h6>
                                                        <span>Johndoe@gmail.com</span>
                                                    </div>
                                                    <div class="col-12 mb-3">
                                                        <a href="javascript: void(0);" class="btn btn-danger">Connect to
                                                            <strong>Google</strong>
                                                        </a>
                                                    </div>
                                                    <div class="col-12 mb-2">
                                                        <button class=" btn btn-sm btn-secondary float-right">edit</button>
                                                        <h6>You are connected to Instagram.</h6>
                                                        <span>Johndoe@gmail.com</span>
                                                    </div>
                                                    <div class="col-12 d-flex flex-sm-row flex-column justify-content-end">
                                                        <button type="submit" class="btn btn-primary mr-sm-1 mb-1 mb-sm-0">Save
                                                            changes</button>
                                                        <button type="reset" class="btn btn-outline-warning">Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="account-vertical-notifications" role="tabpanel" aria-labelledby="account-pill-notifications" aria-expanded="false">
                                                <div class="row">
                                                    <h6 class="m-1">Activity</h6>
                                                    <div class="col-12 mb-1">
                                                        <div class="custom-control custom-switch custom-control-inline">
                                                            <input type="checkbox" class="custom-control-input" checked id="accountSwitch1">
                                                            <label class="custom-control-label mr-1" for="accountSwitch1"></label>
                                                            <span class="switch-label w-100">Email me when someone comments
                                                                onmy
                                                                article</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mb-1">
                                                        <div class="custom-control custom-switch custom-control-inline">
                                                            <input type="checkbox" class="custom-control-input" checked id="accountSwitch2">
                                                            <label class="custom-control-label mr-1" for="accountSwitch2"></label>
                                                            <span class="switch-label w-100">Email me when someone answers on
                                                                my
                                                                form</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mb-1">
                                                        <div class="custom-control custom-switch custom-control-inline">
                                                            <input type="checkbox" class="custom-control-input" id="accountSwitcp">
                                                            <label class="custom-control-label mr-1" for="accountSwitcp"></label>
                                                            <span class="switch-label w-100">Email me hen someone follows
                                                                me</span>
                                                        </div>
                                                    </div>
                                                    <h6 class="m-1">Application</h6>
                                                    <div class="col-12 mb-1">
                                                        <div class="custom-control custom-switch custom-control-inline">
                                                            <input type="checkbox" class="custom-control-input" checked id="accountSwitch4">
                                                            <label class="custom-control-label mr-1" for="accountSwitch4"></label>
                                                            <span class="switch-label w-100">News and announcements</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mb-1">
                                                        <div class="custom-control custom-switch custom-control-inline">
                                                            <input type="checkbox" class="custom-control-input" id="accountSwitch5">
                                                            <label class="custom-control-label mr-1" for="accountSwitch5"></label>
                                                            <span class="switch-label w-100">Weekly product updates</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mb-1">
                                                        <div class="custom-control custom-switch custom-control-inline">
                                                            <input type="checkbox" class="custom-control-input" checked id="accountSwitch6">
                                                            <label class="custom-control-label mr-1" for="accountSwitch6"></label>
                                                            <span class="switch-label w-100">Weekly blog digest</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 d-flex flex-sm-row flex-column justify-content-end">
                                                        <button type="submit" class="btn btn-primary mr-sm-1 mb-1 mb-sm-0">Save
                                                            changes</button>
                                                        <button type="reset" class="btn btn-outline-warning">Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- account setting page end -->

            </div>
        </div>
    </div>
    <!-- END: Content-->
 @endsection

@push('scripts')
<!-- Image Gallary :start  -->
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   
 <script>
$(document).ready(function() {

    
    const customer_id = {{ $customer->id }}; // Ensure customer_id is globally defined

    // Load article groups dynamically into the dropdown on page load or modal show
    function loadArticleGroups() {
        console.log("Loading article groups for customer_id:", customer_id);

        $.ajax({
            url: `/get_lead_product_list/${customer_id}`,
            type: 'GET',
            success: function(response) {
                console.log("Received article groups response:", response);

                const articleGroupSelect = $('#article_group');
                articleGroupSelect.find('option:not(:first)').remove(); // Clear existing options

                // Populate the select element with the data
                response.forEach(function(item) {
                    articleGroupSelect.append(new Option(item.article_group, item.id));
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading article groups:', error);
                console.log("Error details:", xhr.responseText);
            }
        });
    }

    // Initial load for article groups
    loadArticleGroups();

    // Double-click to edit image name
    $(document).on('dblclick', '.edit_image_name', function() {
        const imageId = $(this).data('id');
        console.log("Editing image name for ID:", imageId);

        $(this).hide();
        $('input[name="image_name"][data-id="' + imageId + '"]').show().focus();
    });

    $(document).on('blur', 'input[name="image_name"]', function() {
        const input = $(this);
        const imageId = input.data('id');
        const newName = input.val();

        console.log("Updating image name for ID:", imageId, "to:", newName);

        // AJAX request to update the image name
        $.ajax({
            url: '/customer_image_name',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: imageId,
                image_name: newName
            },
            success: function(response) {
                if (response.success) {
                    console.log("Image name updated successfully:", response);
                    $('.edit_image_name[data-id="' + imageId + '"]').text(newName).show();
                    input.hide();
                    toastr.success(response.success);
                } else {
                    toastr.error("Error updating image name");
                }
            },
            error: function(xhr) {
                console.error("Error updating image name:", xhr);
                alert('Error updating image name');
            }
        });
    });

    // Delete image
    $(document).on('click', '.btn-flat-danger', function() {
        const imageId = $(this).data('id');
        const imageCard = $(this).closest('.col-md-3');

        console.log("Attempting to delete image ID:", imageId);

        Swal.fire({
            title: 'Bist du sicher?',
            text: "Sie können dies nicht rückgängig machen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ja, löschen!'
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX request to delete image
                $.ajax({
                    url: `/customer_image_destroy/${imageId}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log("Image deleted successfully:", response);
                            toastr.success(response.message);
                            imageCard.remove();
                        } else {
                            toastr.error("Error deleting image");
                        }
                    },
                    error: function(xhr) {
                        console.error("Error deleting image:", xhr);
                        alert('Error deleting image');
                    }
                });
            }
        });
    });
});

 $(document).ready(function() {
        // When an option in the article_group dropdown is selected
        $('#article_group').on('change', function() {
            const selectedArticleGroup = $(this).val(); // Get the selected option value
            $('#image_product_id').val(selectedArticleGroup); // Set it in the hidden input
            console.log("Selected product_id (image_product_id):", selectedArticleGroup); // Debugging log
        });

        // When an option in the swal-stage dropdown is selected
        $('#swal-stage').on('change', function() {
            const selectedStage = $(this).val(); // Get the selected option value
            $('#stage_id').val(selectedStage); // Set it in the hidden input
            console.log("Selected stage_id:", selectedStage); // Debugging log
        });
    });
</script>

  <script>
    $(document).ready(function() {
    const modalImage = $('#modalImage');
    const zoomRange = $('#image_zoom');
    const modalTitle = $('#imageModalLabel');
    const imageContainer = $('.image-container');

    // Open modal, set image source, title, and reset zoom
    $(document).on('click', '.open-modal', function() {
        const imageUrl = $(this).data('image');
        const imageName = $(this).attr('alt'); // Use image's alt attribute as the name

        // Set image source and modal title
        modalImage.attr('src', imageUrl);
        modalTitle.text(imageName);

        // Reset zoom level and overflow
        zoomRange.val(1);
        modalImage.css('transform', 'scale(1)');
        imageContainer.css('overflow', 'hidden'); // Hide scroll by default

        // Show the modal
        $('#imageModal').modal('show');
    });

    // Update zoom level and enable scrolling if zoomed
    zoomRange.on('input', function() {
        const zoomLevel = $(this).val();
        modalImage.css('transform', `scale(${zoomLevel})`);
        
        // Enable scrollbars when zoom level exceeds 1
        if (zoomLevel > 1) {
            imageContainer.css('overflow', 'auto'); // Enable scrollbars
        } else {
            imageContainer.css('overflow', 'hidden'); // Hide scrollbars when zoom is 1
        }
    });
});
</script>

 <script>
$(document).ready(function() {
    const documentViewerBody = $('#document_viewer_body');
    const downloadButton = $('#download_button');
 

    // Open document in modal for preview based on file type
    $(document).on('click', '.open-document', function() {
        const fileType = $(this).data('file-type').toLowerCase();
        const fileName = $(this).data('file-name');
        const fileUrl = $(this).data('file-url');

        // Set the modal title with the file name
        $('#myModalLabel16').text(`DOKUMENT VIEWER: ${fileName}`);
        
        // Clear previous content
        documentViewerBody.empty();

        // Set download button link and file name
        downloadButton.attr('href', fileUrl);
        downloadButton.attr('download', fileName);

        // Load document preview based on file type
        if (fileType === 'pdf') {
            // Show PDF in an iframe
            documentViewerBody.html(`<iframe src="${fileUrl}" frameborder="0" style="width:100%; height:80vh;"></iframe>`);
        } else if (fileType === 'docx' || fileType === 'doc') {
            // Display Word document icon
            documentViewerBody.html(`
                <i class="fa fa-file-word-o" style="font-size: 100px; color: #007bff;"></i>
                <p>This document is a Word file. Click "Download Document" to view it.</p>
            `);
        } else if (fileType === 'xlsx') {
            // Display Excel document icon
            documentViewerBody.html(`
                <i class="fa fa-file-excel-o" style="font-size: 100px; color: #28a745;"></i>
                <p>This document is an Excel file. Click "Download Document" to view it.</p>
            `);
        } else {
            // For unsupported file types, show a message
            documentViewerBody.html(`<p>Preview not available for this document type.</p>`);
        }

        // Open the modal
        $('#customer_document').modal('show');
    });

       
});
</script>
 
<script>
    // Handle clicking the "Offen" button to open the document modal
document.addEventListener("click", function (event) {
    if (event.target.closest(".delete-btn")) {
        const parentCard = event.target.closest(".card-content");

        if (!parentCard) {
            console.error("Parent card not found");
            return;
        }

        // Extract necessary file info
        const fileType = parentCard.querySelector("h6.card-title").getAttribute("data-file-type");
        const fileName = parentCard.querySelector("h6.card-title").textContent.trim();
        const fileUrl = parentCard.querySelector("iframe") 
            ? parentCard.querySelector("iframe").getAttribute("src") 
            : parentCard.querySelector("h6.card-title").getAttribute("data-file-url");

        console.log("Opening Document Modal for:", fileUrl);

        const documentViewerBody = document.getElementById("document_viewer_body");
        const downloadButton = document.getElementById("download_button");

        // Clear previous content
        documentViewerBody.innerHTML = "";

        // Load content based on file type
        if (fileType === "pdf") {
            documentViewerBody.innerHTML = `<iframe src="${fileUrl}" frameborder="0" style="width: 100%; height: 600px;"></iframe>`;
        } else if (fileType === "docx" || fileType === "doc") {
            documentViewerBody.innerHTML = `
                <div style="font-size: 50px; color: #007bff;">
                    <i class="fa fa-file-word-o"></i>
                    <p>${fileName}</p>
                    <p>Word-Dokument kann heruntergeladen werden</p>
                </div>`;
        } else if (fileType === "xlsx") {
            documentViewerBody.innerHTML = `
                <div style="font-size: 50px; color: #28a745;">
                    <i class="fa fa-file-excel-o"></i>
                    <p>${fileName}</p>
                    <p>Excel-Datei kann heruntergeladen werden</p>
                </div>`;
        } else {
            documentViewerBody.innerHTML = `
                <div style="color: red;">
                    <p>Dokumentvorschau nicht verfügbar.</p>
                </div>`;
        }

        // Set download link
        downloadButton.href = fileUrl;
        downloadButton.download = fileName;

        // Show the modal
        $("#customer_document").modal("show");
    }
});

</script>
 
 
<!-- Image Gallary :end  -->
    
@endpush
     
 
 