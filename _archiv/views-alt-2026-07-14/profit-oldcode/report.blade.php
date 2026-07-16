@extends('admin.layouts.app')
@section('title') WIRTSCHAFTLICHKEITSBERECHNUNG @endsection
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css')}}"> 
<meta name="csrf-token" content="{{ csrf_token() }}">
 <!-- In your main Blade layout (e.g. admin.layouts.app or similar) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css">
<link href="https://unpkg.com/cropperjs/dist/cropper.min.css" rel="stylesheet">
    <style>
        .select2-container--default .select2-selection--single {
            height:51px;
        }
        .card {
            box-shadow: 0 0px !important
        }
 
            .section {
                margin-bottom: 20px;
            }

            /* Responsive styling */
            @media screen and (max-width: 768px) {
                .a4-page {
                    max-width: 100%; /* Make it fit the screen width */
                }
            }

             
            .card ul li {
                margin-bottom: 5px;
            }

    </style>


<style>
    .wizard-nav {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
        gap: 10px;
        flex-wrap: wrap;
    }

    .wizard-step {
        flex: 1;
        text-align: center;
        padding: 10px 5px;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #666;
        font-weight: normal;
    }

    .wizard-step img {
        width: 90px;
        margin-bottom: 5px;
        transition: transform 0.3s ease;
    }

    .wizard-step.active {
        color: #8fc73e;
        font-weight: bold;
    }

    .wizard-step.active .wizard-progress-count {
        color: #8fc73e;
        font-weight: bold;
    }

    .wizard-step:hover img {
        transform: scale(1.05);
    }

    .wizard-progress-count {
        display: block;
        font-size: 0.8rem;
        color: #aaa;
        font-weight: normal;
    }

    .tab-pane .row {
        padding: 0 20px;
    }

    @media print {
        .no-print {
            display: none !important;
        }
    }

</style>


<style>
@media print {
    body * {
        visibility: hidden;
    }

    .a4-page, .a4-page * {
        visibility: visible;
    }

    .a4-page {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none !important;
        border: none !important;
    }

    .btn, .header-navbar, .footer, .breadcrumb, .step-content:not(#step-2) {
        display: none !important;
    }
}

@media print {
    @page {
        /* size: A4; */
        margin: 0;
    }

    body {
        margin: 0 !important;
        padding: 0 !important;
    }

    .a4-page {
        width: 100% !important;
        min-height: 100% !important;
        margin: 0 auto !important;
        padding: 40px !important;
        box-shadow: none !important;
        border: none !important;
    }
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
                <div class="content-header-left col-md-9 col-12 mb-1">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h6 class="content-header-title float-left mb-0">WIRTSCHAFTLICHKEITSBERECHNUNG</h6>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="{{ url('new_lead_view') }}">Kunde</a></li>
                                    <li class="breadcrumb-item active"><a href="{{ url('new_lead_profile/'.$customer->customer->id) }}">{{ $customer->customer->name }} {{$customer->customer->lastname}} ({{ $customer->product->article_group }})</a></li>
                                    <li class="breadcrumb-item active"><a href="{{ url('customer_profit/'.$customer->customer->id.'/'.$customer->alternative->id.'/'.$customer->product->id.'/'.$customer->service->id) }}">WIRTSCHAFTLICHKEITSBERECHNUNG</a></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-body">
                <!-- BEGIN: Step Wizard -->
                <div class="container" style="max-width:2000px !important;">
                    <!-- Step Indicators -->
                    <div class="row">
                        <div class="col-12">
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" id="progress-bar" style="width: 20%;">
                                    Step 1 of 5
                                </div>
                            </div>
                        </div>
                    </div>
 
                    <!-- Step Navigation Buttons -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <ul class="nav nav-pills justify-content-center">
                                <li class="nav-item">
                                    <a class="nav-link active" id="step-1-tab" href="#" onclick="showStep(1)"><i class="feather icon-grid"></i>OBJEKTDATEN</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="step-2-tab" href="#" onclick="showStep(2)">Ergebnis</a>
                                </li>  
                            </ul>
                        </div>
                    </div>

                    <!-- Step Content -->
                    <div class="row mt-5">
                        <div class="col-12">
                            <!-- Step 1: Product Selection -->
                                <div id="step-1" class="step-content">  
                                    <div class="row">
                                        <div class="col-md-2 col-sm-12">
                                            <div class="card p-3">
                                                <div class="text-center"> 
                                                    <h5 class="font-weight-bold mb-1">{{ $customer->customer->name }} {{ $customer->customer->lastname }}</h5>
                                                    <p class="text-muted small mb-0">{{ $customer->customer->street }}</p>
                                                    <p class="text-muted small mb-0">{{ $customer->customer->postcode }} {{ $customer->customer->city }}</p>
                                                </div>

                                                <hr>
                                                <input type="hidden" class="pid" value="{{$customer->id}}">
                                                <ul class="list-unstyled mb-1">
                                                    <li><strong>Firma:</strong> {{ $customer->customer->firma }}</li> 
                                                    <li><strong>Telefon:</strong> {{ $customer->customer->phone }}</li>
                                                    <li><strong>E-Mail:</strong> {{ $customer->customer->email }}</li>
                                                </ul>

                                                <h6 class="primary font-weight-bold mt-3">Die interessierten Gewerke</h6>
                                                <hr>

                                                <div class="text-center">
                                                    <div class="card text-center shadow-none border">
                                                        <div class="card-body p-0">
                                                            <div class="avatar bg-rgba-info p-50 mb-1 mx-auto" style="width: 50px; height: 50px; border-radius: 50%;">
                                                                <div class="avatar-content">
                                                                    <img src="{{ asset('images/articles/'.$customer->product->image) }}" alt="" style="width: 100%; height: 100%; object-fit: contain;">
                                                                </div>
                                                            </div>
                                                            <h6 class="font-weight-bold mb-0">{{ $customer->product->article_group }}</h6>
                                                            <small class="text-muted">{{ $customer->product->initial }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="col-md-7 col-md-7 col-sm-12"> 
                                            <div class="section-content"> 
                                                <div class="col-12 p-0"> 
                                                    <div class="cards"> 
                                                        <div class="card-header  d-flex justify-content-between align-items-center mb-1">
                                                            <h5 class="mb-0"><i class="feather icon-settings"></i> Energieverbrauch & Objektdaten</h5>  
                                                        </div> 
                                                        <div class="card-body p-0">
                                                            <div class="wizard-nav">
                                                                <div class="wizard-step active" onclick="showTab(1)">
                                                                    <img src="{{ asset('images/icons/dokumente.svg') }}" alt="" style="width: 72px;"> <br> 
                                                                            Objektdaten 
                                                                        <span class="wizard-progress-count" id="step1-count">(0/17)</span>
                                                                    </div> 
                                                                    <div class="wizard-step" onclick="showTab(2)">
                                                                            <img src="{{ asset('images/icons/haus_schraeg.svg') }}" alt="" style="width: 72px;"> <br> 
                                                                                Dachinformation 
                                                                            <span class="wizard-progress-count" id="step2-count">(0/26)</span>
                                                                        </div>
                                                                    <div class="wizard-step" onclick="showTab(3)">
                                                                        <img src="{{ asset('images/articles/warmpumpe.png') }}" alt="" style="width: 100px;"> <br> 
                                                                        Heizungsinformation
                                                                        <span class="wizard-progress-count" id="step3-count">(0/21)</span>
                                                                    </div>
                                                                    <div class="wizard-step" onclick="showTab(4)">
                                                                    <img src="{{ asset('images/articles/battery.png') }}" alt="" style="width: 100px;"> <br>
                                                                        E-Mobilität 
                                                                        <span class="wizard-progress-count" id="step4-count">(0/10)</span>
                                                                    </div>

                                                                    <div class="wizard-step" onclick="showTab(5)">
                                                                    <img src="{{ asset('images/icons/zaehler.svg') }}" alt="" style="width: 72px;"> <br>
                                                                        Energieverbrauch 
                                                                        <span class="wizard-progress-count" id="step5-count">(0/12)</span>
                                                                    </div> 

                                                                    <div class="wizard-step" onclick="showTab(6)">
                                                                    <img src="{{ asset('images/icons/zaehler.svg') }}" alt="" style="width: 72px;"> <br>
                                                                        PVGS Daten  
                                                                    </div>
                                                            </div>

                                                            <div class="tab-content pt-2">
                                                                <div class="tab-pane active" id="step1" role="tabpanel">
                                                                @include('admin.checklist.profitablity_calculation.partials.object_data', ['alternative_id' => $customer->alternative->id])
                                                                </div>
                                                                 
                                                                <div class="tab-pane" id="step2" role="tabpanel">
                                                                @include('admin.checklist.profitablity_calculation.partials.roof_info', ['alternative_id' => $customer->alternative->id])
                                                                </div>
                                                                <div class="tab-pane" id="step3" role="tabpanel">
                                                                    @include('admin.checklist.profitablity_calculation.partials.heating_info', ['alternative_id' => $customer->alternative->id])
                                                                </div>
                                                                <div class="tab-pane" id="step4" role="tabpanel">
                                                                    @include('admin.checklist.profitablity_calculation.partials.e_mobility', ['alternative_id' => $customer->alternative->id])
                                                                </div>
                                                                <div class="tab-pane" id="step5" role="tabpanel">
                                                                 @include('admin.checklist.profitablity_calculation.partials.energy_usage', ['alternative_id' => $customer->alternative->id])
                                                                
                                                                </div>

                                                                <div class="tab-pane" id="step5" role="tabpanel">
                                                                     @include('admin.checklist.profitablity_calculation.partials.weather',['alternative_id'=>$customer->alternative->id, 'customer_id'=>$customer->customer->id, 'postcode'=> $customer->alternative->postcode]) 
                                                                </div>
                                                            </div> 
                                                        </div> 
                                                    </div> 
                                                </div> 
                                            </div>

                                        </div> 
                                        <div class="col-md-3 col-sm-12">
                                            <div class="card rounded-lg overflow-hidden">
                                                <div class="card-body px-1 py-1">
                                                    <h5 class="text-center text-primary font-weight-bold mb-3">
                                                        <i class="feather icon-bar-chart-2 mr-1"></i> Wirtschaftlichkeitsdaten
                                                    </h5>

                                                    @php
                                                        $pd = $profit_data ?? null;
                                                    @endphp

                                                    {{-- 📊 Photovoltaik --}}
                                                    <form method="POST" action="{{ route('profitability.save') }}" class="mb-2 border p-2 rounded">
                                                        @csrf
                                                        <input type="hidden" name="p_id" value="{{ $customer->id }}">
                                                        <input type="hidden" name="customer_id" value="{{ $customer->customer->id }}">
                                                        <input type="hidden" name="alternative_id" value="{{ $customer->alternative->id }}">
                                                        <input type="hidden" name="product_id" value="{{ $customer->product->id }}">
                                                        <h6><i class="feather icon-sun text-warning mr-1"></i> Photovoltaik</h6>
                                                        <div class="form-group mb-2">
                                                            <label>Modulfläche (m²)</label>
                                                            <input type="number" class="form-control form-control-sm" name="pv_module_area" value="{{ $pd->pv_module_area ?? '' }}">
                                                        </div>
                                                        <div class="form-group mb-2">
                                                            <label>Modulleistung (kWp)</label>
                                                            <input type="number" step="0.1" class="form-control form-control-sm" name="pv_power_kwp" value="{{ $pd->pv_power_kwp ?? '' }}">
                                                        </div>
                                                        <div class="form-group mb-2">
                                                            <label>Eigenverbrauch (%)</label>
                                                            <input type="number" step="1" class="form-control form-control-sm" name="pv_self_use" value="{{ $pd->pv_self_use ?? '' }}">
                                                        </div>
                                                        <button type="submit" class="btn btn-sm btn-primary btn-block">PV speichern</button>
                                                    </form>

                                                    {{-- 🔋 Batterie --}}
                                                    <form method="POST" action="{{ route('profitability.save') }}" class="mb-2 border p-2 rounded">
                                                        @csrf
                                                        <input type="hidden" name="p_id" value="{{ $customer->id }}">
                                                        <input type="hidden" name="customer_id" value="{{ $customer->customer->id }}">
                                                        <input type="hidden" name="alternative_id" value="{{ $customer->alternative->id }}">
                                                        <input type="hidden" name="product_id" value="{{ $customer->product->id }}">
                                                        <h6><i class="feather icon-battery-charging text-secondary mr-1"></i> Batteriespeicher</h6>
                                                        <div class="form-group mb-2">
                                                            <label>Kapazität (kWh)</label>
                                                            <input type="number" step="0.1" class="form-control form-control-sm" name="battery_capacity" value="{{ $pd->battery_capacity ?? '' }}">
                                                        </div>
                                                        <div class="form-group mb-2">
                                                            <label>Autarkiegrad (%)</label>
                                                            <input type="number" step="1" class="form-control form-control-sm" name="autarky_level" value="{{ $pd->autarky_level ?? '' }}">
                                                        </div>
                                                        <button type="submit" class="btn btn-sm btn-primary btn-block">Batterie speichern</button>
                                                    </form>

                                                    {{-- ♨ Wärmepumpe --}}
                                                    <form method="POST" action="{{ route('profitability.save') }}" class="mb-2 border p-2 rounded">
                                                        @csrf
                                                        <input type="hidden" name="p_id" value="{{ $customer->id }}">
                                                        <input type="hidden" name="customer_id" value="{{ $customer->customer->id }}">
                                                        <input type="hidden" name="alternative_id" value="{{ $customer->alternative->id }}">
                                                        <input type="hidden" name="product_id" value="{{ $customer->product->id }}">
                                                        <h6><i class="feather icon-thermometer text-danger mr-1"></i> Wärmepumpe</h6>
                                                        <div class="form-group mb-2">
                                                            <label>JAZ (Jahresarbeitszahl)</label>
                                                            <input type="number" step="0.1" class="form-control form-control-sm" name="jaz_value" value="{{ $pd->jaz_value ?? '' }}">
                                                        </div>
                                                        <div class="form-group mb-2">
                                                            <label>Verbrauch (kWh/Jahr)</label>
                                                            <input type="number" class="form-control form-control-sm" name="wp_consumption" value="{{ $pd->wp_consumption ?? '' }}">
                                                        </div>
                                                        <button type="submit" class="btn btn-sm btn-primary btn-block">WP speichern</button>
                                                    </form>

                                                    {{-- 🚗 Wallbox --}}
                                                    <form method="POST" action="{{ route('profitability.save') }}" class="mb-2 border p-2 rounded">
                                                        @csrf
                                                        <input type="hidden" name="p_id" value="{{ $customer->id }}">
                                                        <input type="hidden" name="customer_id" value="{{ $customer->customer->id }}">
                                                        <input type="hidden" name="alternative_id" value="{{ $customer->alternative->id }}">
                                                        <input type="hidden" name="product_id" value="{{ $customer->product->id }}">
                                                        <h6><i class="feather icon-zap text-primary mr-1"></i> Wallbox / E-Mobilität</h6>
                                                        <div class="form-group mb-2">
                                                            <label>Fahrzeuge</label>
                                                            <select class="form-control form-control-sm" name="ev_count">
                                                                <option value="0" {{ isset($pd) && $pd->ev_count == 0 ? 'selected' : '' }}>Keine</option>
                                                                <option value="1" {{ isset($pd) && $pd->ev_count == 1 ? 'selected' : '' }}>1 Fahrzeug</option>
                                                                <option value="2" {{ isset($pd) && $pd->ev_count == 2 ? 'selected' : '' }}>2 Fahrzeuge</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group mb-2">
                                                            <label>Fahrstromverbrauch (kWh/Jahr)</label>
                                                            <input type="number" class="form-control form-control-sm" name="ev_consumption" value="{{ $pd->ev_consumption ?? '' }}">
                                                        </div>
                                                        <button type="submit" class="btn btn-sm btn-primary btn-block">Wallbox speichern</button>
                                                    </form>

                                                    {{-- ⚡ Gebäude & Verbrauch --}}
                                                    <form method="POST" action="{{ route('profitability.save') }}" class="mb-2 border p-2 rounded">
                                                        @csrf
                                                        <input type="hidden" name="p_id" value="{{ $customer->id }}">
                                                        <input type="hidden" name="customer_id" value="{{ $customer->customer->id }}">
                                                        <input type="hidden" name="alternative_id" value="{{ $customer->alternative->id }}">
                                                        <input type="hidden" name="product_id" value="{{ $customer->product->id }}">
                                                        <h6><i class="feather icon-home text-dark mr-1"></i> Gebäude & Strom</h6>
                                                        <div class="form-group mb-2">
                                                            <label>Haushaltsstrom (kWh/Jahr)</label>
                                                            <input type="number" class="form-control form-control-sm" name="household_power" value="{{ $pd->household_power ?? '' }}">
                                                        </div>
                                                        <div class="form-group mb-2">
                                                            <label>Heizenergie (kWh/Jahr)</label>
                                                            <input type="number" class="form-control form-control-sm" name="heating_energy" value="{{ $pd->heating_energy ?? '' }}">
                                                        </div>
                                                        <div class="form-group mb-2">
                                                            <label>Gebäudeart</label>
                                                            <select class="form-control form-control-sm" name="building_type">
                                                                <option {{ isset($pd) && $pd->building_type == 'EFH' ? 'selected' : '' }}>EFH</option>
                                                                <option {{ isset($pd) && $pd->building_type == 'MFH' ? 'selected' : '' }}>MFH</option>
                                                                <option {{ isset($pd) && $pd->building_type == 'Gewerbe' ? 'selected' : '' }}>Gewerbe</option>
                                                            </select>
                                                        </div>
                                                        <button type="submit" class="btn btn-sm btn-primary btn-block">Objekt speichern</button>
                                                    </form>

                                                    {{-- 📈 Energiepreise & Inflation --}}
                                                    <form method="POST" action="{{ route('profitability.save') }}" class="mb-2 border p-2 rounded">
                                                        @csrf
                                                        <input type="hidden" name="p_id" value="{{ $customer->id }}">
                                                        <input type="hidden" name="customer_id" value="{{ $customer->customer->id }}">
                                                        <input type="hidden" name="alternative_id" value="{{ $customer->alternative->id }}">
                                                        <input type="hidden" name="product_id" value="{{ $customer->product->id }}">
                                                        <h6><i class="feather icon-trending-up text-info mr-1"></i> Energiepreise & Inflation</h6>
                                                        <div class="form-group mb-2">
                                                            <label>Strompreis (€/kWh)</label>
                                                            <input type="number" step="0.01" class="form-control form-control-sm" name="electricity_price" value="{{ $pd->electricity_price ?? '0.23' }}">
                                                        </div>
                                                        <div class="form-group mb-2">
                                                            <label>Ölpreis (€/Liter)</label>
                                                            <input type="number" step="0.01" class="form-control form-control-sm" name="oil_price" value="{{ $pd->oil_price ?? '1.20' }}">
                                                        </div>
                                                        <div class="form-group mb-2">
                                                            <label>Gaspreis (€/kWh)</label>
                                                            <input type="number" step="0.01" class="form-control form-control-sm" name="gas_price" value="{{ $pd->gas_price ?? '0.11' }}">
                                                        </div>
                                                        <div class="form-group mb-2">
                                                            <label>Kraftstoffpreis (€/Lit)</label>
                                                            <input type="number" step="0.01" class="form-control form-control-sm" name="fuel_price" value="{{ $pd->fuel_price ?? '0.11' }}">
                                                        </div>
                                                        <div class="form-group mb-2">
                                                            <label>Fernwärme (€/kWh)</label>
                                                            <input type="number" step="0.01" class="form-control form-control-sm" name="district_heating_price" value="{{ $pd->district_heating_price ?? '0.15' }}">
                                                        </div>
                                                        <div class="form-group mb-2">
                                                            <label>Energiepreis-Inflation (% / Jahr)</label>
                                                            <input type="number" step="0.1" class="form-control form-control-sm" name="energy_inflation" value="{{ $pd->energy_inflation ?? '12' }}">
                                                        </div>
                                                        <button type="submit" class="btn btn-sm btn-primary btn-block">Preis speichern</button>
                                                    </form>

                                                    {{-- 🌱 CO₂ Einsparung --}}
                                                    <form method="POST" action="{{ route('profitability.save') }}" class="mb-2 border p-2 rounded">
                                                        @csrf
                                                        <input type="hidden" name="p_id" value="{{ $customer->id }}">
                                                        <input type="hidden" name="customer_id" value="{{ $customer->customer->id }}">
                                                        <input type="hidden" name="alternative_id" value="{{ $customer->alternative->id }}">
                                                        <input type="hidden" name="product_id" value="{{ $customer->product->id }}">
                                                        <h6><i class="feather icon-wind text-success mr-1"></i> CO₂ Werte</h6>
                                                        <div class="form-group mb-2">
                                                            <label>CO₂ Faktor Strom (kg/kWh)</label>
                                                            <input type="number" step="0.001" class="form-control form-control-sm" name="co2_factor_electricity" value="{{ $pd->co2_factor_electricity ?? '0.4' }}">
                                                        </div>
                                                        <div class="form-group mb-2">
                                                            <label>CO₂ Einsparung (t/Jahr)</label>
                                                            <input type="number" step="0.1" class="form-control form-control-sm" name="co2_emission_saved" value="{{ $pd->co2_emission_saved ?? '' }}" readonly>
                                                        </div>
                                                        <button type="submit" class="btn btn-sm btn-primary btn-block">CO₂ speichern</button>
                                                    </form>

                                                </div>
                                            </div>
                                        </div>

                                     </div>
                                    <button type="button" class="btn btn-primary" onclick="nextStep(2)">Nächste</button> 
                                </div>

                                <!-- Step 2: Placeholder -->
                                <div id="step-2" class="step-content d-none">   
                                    <input type="hidden" id="postcode" value="{{$customer->alternative->postcode}}">
                                        @include('admin.checklist.profitablity_calculation.partials.result')
                                 </div> 

                       
 
                        </div>
                    </div>
                </div>
                <!-- END: Step Wizard -->
            </div>
        </div>
    </div>

@endsection

@section('script')
  <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
document.querySelectorAll('form[action="{{ route('profitability.save') }}"]').forEach(form => {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<i class="fa fa-spinner fa-spin"></i> Speichern...`;

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Gespeichert',
                    text: data.message,
                    timer: 1200,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Fehler',
                    text: data.message || 'Ein Fehler ist aufgetreten.',
                });
            }
        })
        .catch(error => {
            console.error(error);
            Swal.fire({
                icon: 'error',
                title: 'Fehler',
                text: 'Netzwerkfehler oder Serverfehler.',
            });
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Speichern';
        });
    });
});
</script>

  <!-- Product Drop down and Image -->
  <script>
     $(document).ready(function() {
        // Initialize Select2 with custom template for displaying images
        $('#product').select2({
            templateResult: formatProduct, // Custom formatting for dropdown list
            templateSelection: formatProductSelection, // Custom formatting for selected item
            escapeMarkup: function(m) { return m; } // Let Select2 handle HTML markup
        });

        // Function to format the dropdown items with image on the left
        function formatProduct(product) {
            if (!product.id) {
                return product.text; // Return the default label for the item (without formatting)
            }

            // Get the image URL from the data attribute
            var imageUrl = $(product.element).data('image');

            // Create the HTML for the item with the image on the left
            var $productOption = $(
                '<span><img src="' + imageUrl + '" class="img-thumbnail mr-2" style="width: 40px; height: 40px;" />' +
                '<span>' + product.text + '</span></span>'
            );
            
            return $productOption;
        }

        // Function to format the selected item
        function formatProductSelection(product) {
            if (!product.id) {
                return product.text;
            }

            // Get the image URL from the data attribute for the selected item
            var imageUrl = $(product.element).data('image');
            
            // Create the HTML for the selected item with the image
            var $productSelected = $(
                '<span><img src="' + imageUrl + '" class="img-thumbnail mr-2" style="width: 40px; height: 40px;" />' +
                '<span>' + product.text + '</span></span>'
            );
            
            return $productSelected;
        }
    }); 
  </script>

  <!-- Product Title for A4 Page -->
   <script>
    $(document).ready(function() {
        // Event listener for the select field
        $('#product').on('change', function() {
            // Get the selected product's text (article group)
            var selectedProduct = $("#product option:selected").text();
            
            // Update the #product-title span with the selected product name
            $('.product-title').text(selectedProduct);
        });
    });
   </script>

<script>
    let currentStep = 1;
    const totalSteps = 5;

    function showStep(step) {
        currentStep = step;

        // Hide all steps
        document.querySelectorAll('.step-content').forEach(div => div.classList.add('d-none'));

        // Show selected step
        const current = document.getElementById('step-' + step);
        if (current) current.classList.remove('d-none');

        // Update nav pills
        document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
        const navLink = document.getElementById('step-' + step + '-tab');
        if (navLink) navLink.classList.add('active');

        // Update progress bar
        const progress = Math.round((step / totalSteps) * 100);
        const progressBar = document.getElementById('progress-bar');
        if (progressBar) {
            progressBar.style.width = progress + '%';
            progressBar.innerText = 'Step ' + step + ' of ' + totalSteps;
        }
    }

    function nextStep(step) {
        if (step > totalSteps) return;
        showStep(step);
    }

    // Optional: Auto-init on page load
    document.addEventListener('DOMContentLoaded', () => {
        showStep(currentStep);
    });
</script>


<script>
    $(document).ready(function() {
        // Event listener for the toggle checkbox
        $('#toggleDiv').on('change', function() {
            // Check if the checkbox is checked or not
            if ($(this).is(':checked')) {
                // Enable all input fields within the div except the checkbox
                $('#montageDiv .montage-input').prop('disabled', false);
                // Remove the disabled background color
                $('#montageDiv').css('background', '');
            } else {
                // Disable all input fields within the div except the checkbox
                $('#montageDiv .montage-input').prop('disabled', true);
                // Apply the disabled background color
                $('#montageDiv').css('background', '#e7e7e775');
            }
        });

        // Initially set the background color as disabled
        $('#montageDiv').css('background', '#e7e7e775');
    });
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.save-partial-form').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const form = button.closest('form.partial-form');
            if (!form) {
                console.error('❌ Form not found for this button.');
                return;
            }

            const section = form.dataset.section;
            const id = form.dataset.id;

            const formData = new FormData(form);
            formData.append('id', id);

            fetch('/new_lead_profile/alternative/object/save', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) return response.text().then(text => { throw new Error(text); });
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Gespeichert',
                    text: `Abschnitt "${section}" erfolgreich gespeichert.`
                });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Fehler',
                    text: error.message
                });
            });
        });
    });
});


document.querySelectorAll('form.partial-form').forEach(f => {
    console.log("FORM FOUND:", f.dataset.section, f.dataset.id);
});

</script>


<script>
  function showTab(step) {
      document.querySelectorAll('.tab-pane').forEach((pane, idx) => {
          pane.classList.remove('active');
          if (idx === step - 1) pane.classList.add('active');
      });

      document.querySelectorAll('.wizard-step').forEach((stepEl, idx) => {
          stepEl.classList.remove('active');
          if (idx === step - 1) stepEl.classList.add('active');
      });
      updateProgressCounts();

  }


    function navigateTab(direction) {
        const steps = document.querySelectorAll('.wizard-step');
        let currentIndex = [...steps].findIndex(step => step.classList.contains('active'));
        let nextIndex = currentIndex + direction;
        if (nextIndex >= 0 && nextIndex < steps.length) {
            showTab(nextIndex + 1);
        }
    }

    function updateProgressCounts() {
        const forms = document.querySelectorAll('form.partial-form');

        forms.forEach(form => {
            const section = form.dataset.section;
            const counterEl = document.getElementById(`step${getStepIndex(section)}-count`);
            if (!counterEl) return;

            const tabPane = form.closest('.tab-pane');
            const wasHidden = tabPane && !tabPane.classList.contains('active');

            // 🧠 Temporarily show hidden tab to count inputs
            if (wasHidden) {
                tabPane.classList.add('temporary-visible');
                tabPane.classList.add('active');
            }

            const inputs = form.querySelectorAll('input, select, textarea');
            let total = 0;
            let filled = 0;

            inputs.forEach(input => {
                const type = input.type;
                const isHidden = input.offsetParent === null; // skip visually hidden (e.g., display: none)
                if (input.name === '_token' || isHidden) return;

                total++;

                if (['checkbox', 'radio'].includes(type)) {
                    if (input.checked) filled++;
                } else {
                    const val = input.value?.trim();
                    if (val !== '') filled++;
                }
            });

            counterEl.textContent = `(${filled}/${total})`;

            // 🔄 Re-hide tab if it was not active before
            if (wasHidden) {
                tabPane.classList.remove('active');
                tabPane.classList.remove('temporary-visible');
            }
        });

        function getStepIndex(section) {
            const map = ['object_data', 'roof_info', 'heating_info', 'e_mobility', 'energy_usage'];
            return map.indexOf(section) + 1;
        }
    }

 
        function loadFullAlternativeObject(button) {
                const customerId    = button.dataset.customerId;
                const alternativeId = button.dataset.alternativeId;
                const productId     = button.dataset.productId;

                const url = `/customer/alternative/partials/${customerId}/${alternativeId}/${productId}/objekt`;

                const mainContent = document.getElementById('mainContent');
                mainContent.innerHTML = `<div class="text-center py-4">Lade Objektdaten...</div>`;

                fetch(url)
                    .then(response => {
                        if (!response.ok) throw new Error('Fehler beim Laden des Objekts');
                        return response.text();
                    })
                    .then(html => {
                        // ✅ Inject the new content
                        mainContent.innerHTML = html;

                        // ✅ Replace feather icons
                        if (typeof feather !== 'undefined') feather.replace();

                        // ✅ Re-initialize power calculator
                        initPowerCalculatorWithIDs(mainContent);

                        // ✅ Recalculate progress indicators
                        updateProgressCounts();
                    })
                    .catch(error => {
                        mainContent.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
                        console.error('❌ Fehler beim Laden:', error);
                    });
            }



    function initPowerCalculatorWithIDs(context = document) {
        const $household    = $(context).find('#power_household_input');
        const $heatpump     = $(context).find('#power_heatpump_input');
        const $electricCar  = $(context).find('#power_electric_car_input');
        const $other        = $(context).find('#power_other_input');
        const $total        = $(context).find('#power_total');        // Display for user (with comma)
        const $totalHidden  = $(context).find('#power_total_hidden');  // Actual value (with dot)

        function parseInput($el) {
            const val = $el.val().trim().replace(',', '.');
            const num = parseFloat(val);
            if (isNaN(num) || num < 0) {
                $el.addClass('is-invalid');
                return 0;
            } else {
                $el.removeClass('is-invalid');
                return num;
            }
        }

        function updateTotal() {
            const h  = parseInput($household);
            const wp = parseInput($heatpump);
            const ev = parseInput($electricCar);
            const o  = parseInput($other);

            const total = h + wp + ev + o;

            // 👁️ Display total with comma
            $total.val(total.toFixed(2).replace('.', ','));

            // 🧠 Hidden input for DB (with dot)
            $totalHidden.val(total.toFixed(2));

            // ℹ️ kWh / year
            let $year = $(context).find('#power_total_year');
            if (!$year.length) {
                $year = $('<small id="power_total_year" class="form-text text-muted"></small>').insertAfter($total);
            }
            const yearly = total * 365;
            $year.text('≈ ' + yearly.toLocaleString('de-DE') + ' kWh / Jahr');
        }


        $household.add($heatpump).add($electricCar).add($other)
            .off('input.powercalc')
            .on('input.powercalc', updateTotal);

        updateTotal();
    }


    $(document).ready(function () {
        // 🔁 Initial progress calculation when page loads
        updateProgressCounts();

        // 🔄 Recalculate on any form input change
        $(document).on('input change', 'form.partial-form input, form.partial-form select, form.partial-form textarea', updateProgressCounts);
    });



</script>
 
<script>
    console.log('✅ addNewRoofEditProfile is defined here');

    let roofIndex = {{ isset($roofs) ? count($roofs) : 0 }};

    function addNewRoofEditProfile() {
        console.log('📦 Called addNewRoofEditProfile');
        fetch(`/admin/roofs/partial-edit-profile/${roofIndex}`)
            .then(res => res.text())
            .then(html => {
                const wrapper = document.getElementById('roof-wrapper');
                const newDiv = document.createElement('div');
                newDiv.innerHTML = html;
                wrapper.appendChild(newDiv);
                roofIndex++;
            })
            .catch(err => console.error('Fehler beim Laden des neuen Daches:', err));
    }
</script>

<!-- Weather Information  -->


 
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("profitability-form");

    form?.addEventListener("submit", function (e) {
        e.preventDefault(); // prevent default form submission

        const pidEl = document.querySelector('.pid');
        if (!pidEl) return console.warn("p_id element not found");

        const p_id = pidEl.value;

        fetch(`/get-profitability-data/${p_id}`)
            .then(res => res.json())
            .then(data => {
                const requiredFields = [
                    'pv_module_area', 'pv_power_kwp', 'pv_self_use',
                    'battery_capacity', 'autarky_level',
                    'jaz_value', 'wp_consumption',
                    'ev_count', 'ev_consumption',
                    'household_power', 'heating_energy', 'building_type',
                    'electricity_price', 'oil_price', 'gas_price', 'district_heating_price',
                    'energy_inflation', 'fuel_price', 'co2_factor_electricity'
                ];

                const missing = requiredFields.filter(field => !data[field]);

                if (missing.length > 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Fehlende Daten',
                        html: `Folgende Felder fehlen:<br><ul style="text-align:left">${missing.map(m => `<li>${m}</li>`).join('')}</ul>`,
                        confirmButtonText: 'Ok'
                    });
                } else {
                    document.querySelectorAll('.step-content').forEach(el => el.classList.add('d-none'));
                    document.getElementById(`step-2`).classList.remove('d-none');
                    loadProfitabilityResults(data);
                }
            })
            .catch(err => {
                console.error('Fetch error:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Fehler',
                    text: 'Profitability-Daten konnten nicht geladen werden.'
                });
            });
    });


     
    function loadProfitabilityResults(data) {
            const parse = (v, fallback = 0) => parseFloat(data[v] || fallback);

            const electricityPrice = parse('electricity_price');
            const householdPower = parse('household_power');
            const pvSelfUseRate = parse('pv_self_use'); // in %
            const pvYield = parse('pv_energy_yield');  // ← from PVGIS

            // Estimate usable PV energy for household: E_y × self-use %
            const pvSelfUsedKWh = pvYield * (pvSelfUseRate / 100);
            const pvSelfUsedEuro = pvSelfUsedKWh * electricityPrice;

            const strom_before = householdPower * electricityPrice;
            const strom_after = (householdPower - pvSelfUsedKWh) * electricityPrice;

            // Heating
            const heatingEnergy = parse('heating_energy');
            const oilPrice = parse('oil_price');
            const gasPrice = parse('gas_price');
            const districtHeatingPrice = parse('district_heating_price');
            const wpConsumption = parse('wp_consumption');
            const buildingType = data.building_type;

            let heizung_before = 0;
            if (buildingType === 'EFH') heizung_before = heatingEnergy * gasPrice;
            else if (buildingType === 'MFH') heizung_before = heatingEnergy * districtHeatingPrice;
            else if (buildingType === 'Gewerbe') heizung_before = heatingEnergy * oilPrice;

            const heizung_after = wpConsumption * electricityPrice;

            // Fuel
            const fuelPricePerCar = parse('fuel_price', 1785);
            const evCount = parseInt(data.ev_count || 0);
            const evConsumption = parse('ev_consumption');

            const fuel_before = evCount * fuelPricePerCar;
            const fuel_after = evConsumption * electricityPrice;

            // Totals
            const total_before = strom_before + heizung_before + fuel_before;
            const total_after = strom_after + heizung_after + fuel_after;
            const total_savings = total_before - total_after;

            // UI updates
            document.getElementById('evu_price_before').innerText = formatEuro(strom_before);
            document.getElementById('evu_after_price').innerText = formatEuro(strom_after);
            document.getElementById('heizung_price').innerText = formatEuro(heizung_before);
            document.getElementById('heizung_after_price').innerText = formatEuro(heizung_after);
            document.getElementById('fuel_price').innerText = formatEuro(fuel_before);
            document.getElementById('feul_price_after').innerText = formatEuro(fuel_after);
            document.getElementById('total_per_year').innerText = formatEuro(total_before);
            document.getElementById('total_price_after').innerText = formatEuro(total_after);
            document.querySelectorAll('.savings-box small')[0].innerText = formatEuro(total_savings);
            document.querySelectorAll('.savings-box small')[1].innerText = formatEuro(total_savings * 25);
            document.getElementById('total_price_25').innerText = formatEuro(total_before * 25);
            document.getElementById('total_price_25_after').innerText = formatEuro(total_after * 25);

            // CO₂ Savings
            const co2_factor = parse('co2_factor_electricity', 0.4);
            const co2_household = householdPower * co2_factor;
            const co2_heating = heatingEnergy * co2_factor;
            const co2_autos = evCount * 2436;
            const co2_total = co2_household + co2_heating + co2_autos;

            document.getElementById('haushalt_strom').innerText = `Haushaltsstrom: ${formatKg(co2_household)}`;
            document.getElementById('heiz_energy').innerText = `Heizenergie: ${formatKg(co2_heating)}`;
            document.getElementById('autos').innerText = `Verbrenner-Autos: ${formatKg(co2_autos)}`;
            document.getElementById('co2_total').innerText = `${formatKg(co2_total)} kg`;
        }

    function formatEuro(value) {
        return new Intl.NumberFormat('de-DE', {
            style: 'currency',
            currency: 'EUR'
        }).format(value);
    }

    function formatKg(value) {
        return Math.round(value).toLocaleString('de-DE');
    }

});
</script>


@endsection
