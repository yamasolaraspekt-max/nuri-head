@extends('admin.layouts.app')
@section('title') Ausgabenarten @stop
@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
<style>
    .ths {
       padding: 2px !important;
    border: 1px solid #ece8e8 !important;
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
                            <h2 class="content-header-title float-left mb-0">Filiale mieten</h2>
                            <div class="breadcrumb-wrapper col-12">
                                      @php
                                        $expense = DB::table('branch_expenses')->where('id', '=', request()->expense)->first();
                                        $year = $expense->year;
                                        $id = $expense->id;
                                        $branch = $expense->branch_id;
                                    @endphp
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li> 
                                    <li class="breadcrumb-item">
                                        <a href="{{ url('expense_details/'.$id.'/'.$branch.'/'.$year) }}">
                                            {{ optional(DB::table('branch_rents')->where('expense_details_id', '=', request()->id)->first())->object_name ?? 'No Object Name' }}
                                        </a>
                                    </li>                              
                                    <li class="breadcrumb-item"><a href="">Details</a></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-body">  
                    <div class="nav-vertical">
                        <ul class="nav nav-tabs nav-left flex-column" role="tablist" style="height: 56px;">
                            <li class="nav-item">
                                <a class="nav-link active" id="baseVerticalLeft-tab1" data-toggle="tab" aria-controls="tabVerticalLeft1" href="#tabVerticalLeft1" role="tab" aria-selected="true" style="font-size: 25px;"><i class="fa fa-home"></i> Immobilie</a>
                            </li>
                            <li class="nav-item">
                             <a class="nav-link" id="baseVerticalLeft-tab2" data-toggle="tab" aria-controls="tabVerticalLeft2" href="#tabVerticalLeft2" role="tab  " aria-selected="false" style="font-size: 25px;"><i class="fa  fa-building"></i> Mietvertrag</a>

                            </li>
                            
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tabVerticalLeft1" role="tabpanel" aria-labelledby="baseVerticalLeft-tab1">
                                <div class="cards">
                                    <div class="card-content">
                                        <div class="p-50 float-right">
                                            <a type="button" class="btn btn-outline-success waves-effect waves-light" data-toggle="modal" data-target="#rent_property">
                                                <i class="feather icon-plus"></i> Neue
                                            </a>
                                        </div>
                                        <div class="card-body">
                                            <h4 class="card-title">Details der Immobilie <span class="badge badge-primary"></span></h4>
                                            <p class="card-text">Vertrag, Eigentümer und Kontaktdaten der Immobilienbehörden</p>
                                        </div>

                                        <!-- Rent Operation Modal:start -->
                                        <div class="modal fade" id="rent_property" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="deleteModalLabel">Bestätigung Aktualisieren</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                        <form id="rentPropertyForm">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <label>Name des Eigentümers</label>
                                                            <div class="form-group"> 
                                                                <input type="hidden" name="object_id" value="{{ request()->expense }}">
                                                                <input type="text" placeholder="Eigentümer..." class="form-control" name="owner" id="owner" value="{{ old('owner') }}" required>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                        <label>Wohnfläche <code>m³</code></label>
                                                                    <div class="form-group"> 
                                                                        <input type="text" placeholder="Wohnfläche..." class="form-control" name="living_space" value="{{ old('living_space') }}" required>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                        <label>Stellplatz</label>
                                                                    <div class="form-group"> 
                                                                            <select name="parking" id="parking" class="form-control">
                                                                            <option value="1">Ja</option>
                                                                            <option value="0" selected>Nein</option>
                                                                            </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row" id="parking-details" style="display:none;">
                                                                <div class="col-md-6">
                                                                    <label>Parkkosten</label>
                                                                    <div class="form-group">
                                                                        <input type="number" placeholder="Parkkosten..." class="form-control" name="parking_cost" value="{{ old('parking_cost') }}">
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label>Anzahl der Parkplätze</label>
                                                                    <div class="form-group">
                                                                        <input type="number" placeholder="Anzahl der Parkplätze..." class="form-control" name="parking_count" value="{{ old('parking_count') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            
                                                            <label>Vertrag-typ</label>
                                                            <div class="form-group">
                                                                <select class="form-control" name="contract_type" id="contract_type"> 
                                                                    <option value="Limited">Befristet</option>
                                                                    <option value="Permanent">Unbefristet</option>
                                                                </select>
                                                                @if ($errors->has('contract_date'))
                                                                    <p style="color:red;">{!!$errors->first('contract_date')!!}</p>
                                                                @endif
                                                            </div> 

                                                            <div class="row">
                                                                <div class="col-4">
                                                                        <label>Vertragsbeginn</label>  
                                                                    <div class="form-group">
                                                                        <input type="date" class="form-control" name="contract_date" id="contract_date" value="{{ old('contract_date') }}" required>  
                                                                        @if ($errors->has('contract_date'))
                                                                            <p style="color:red;">{!!$errors->first('contract_date')!!}</p>
                                                                        @endif
                                                                    </div> 
                                                                </div>
                                                                <div class="col-8" id="termination-details" style="display: none; display: flex;" >
                                                                    <div class="col-6">
                                                                        <label>Vertragsbeendigungsdatum</label>
                                                                        <div class="form-group">
                                                                            <input type="date" class="form-control" name="termination_date" id="termination_date" value="{{ old('termination_date') }}">
                                                                            @if ($errors->has('termination_date'))
                                                                                <p style="color:red;">{!!$errors->first('termination_date')!!}</p>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label>Art der Vertragsbeendigung</label> 
                                                                        <div class="form-group">
                                                                            <select class="form-control" name="termination_type">
                                                                                <option disabled selected>Wählen Sie eine aus</option>
                                                                                <option value="Monatsanfang">Monatsanfang</option>
                                                                                <option value="Ende des Monats">Ende des Monats</option> 
                                                                            </select>
                                                                            @if ($errors->has('termination_type'))
                                                                                <p style="color:red;">{!!$errors->first('termination_type')!!}</p>
                                                                            @endif 
                                                                        </div>
                                                                    </div> 
                                                                </div>
                                                                    <div class="col-12">
                                                                    <strong class="danger" id="contract_year">Dauer: </strong>
                                                                    </div>
                                                                
                                                            </div> 
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <label>Kaltmiete</label>
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control" name="cold_rent" value="{{ old('cold_rent') }}" required>
                                                                        @if ($errors->has('cold_rent'))
                                                                            <p style="color:red;">{!!$errors->first('cold_rent')!!}</p>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                    <div class="col-6">
                                                                    <label>Warmmiete</label>
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control" name="warm_rent" value="{{ old('warm_rent') }}" required>
                                                                        @if ($errors->has('warm_rent'))
                                                                            <p style="color:red;">{!!$errors->first('warm_rent')!!}</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        
                                                            <label>Kaution</label>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="advance_rent" value="{{ old('advance_rent') }}" required>
                                                                @if ($errors->has('advance_rent'))
                                                                    <p style="color:red;">{!!$errors->first('advance_rent')!!}</p>
                                                                @endif
                                                            </div>
                                                            <label>Bankinhaber</label>
                                                                <fieldset class="float-right">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" class="custom-control-input" checked="" name="same" id="same_name"  >
                                                                        <label class="custom-control-label" for="same_name">Gleich wie Eigentümername</label>
                                                                    </div>
                                                                </fieldset>
                                                            <div class="form-group"> 
                                                                <input type="text" class="form-control" name="bank_user" id="bank_user" value="{{ old('bank_user') }}" required>
                                                                @if ($errors->has('bank_user'))
                                                                    <p style="color:red;">{!!$errors->first('bank_user')!!}</p>
                                                                @endif
                                                            </div>
                                                            <label>Name der Bank</label>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="bank_name"  value="{{ old('bank_name') }}" required>
                                                                @if ($errors->has('bank_name'))
                                                                    <p style="color:red;">{!!$errors->first('bank_name')!!}</p>
                                                                @endif
                                                            </div>
                                                            <label>IBAN</label>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="iban" value="{{ old('iban') }}" required>
                                                                @if ($errors->has('iban'))
                                                                    <p style="color:red;">{!!$errors->first('iban')!!}</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary waves-effect waves-light">Einreichen</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Rent Operation Modal:end -->

                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Eigentümer</th>
                                                                <th>Kontakt</th>
                                                                <th>Wohnungsdetails</th>
                                                                <th>Vertrag-typ</th> 
                                                                <th>Mietbeginn</th>
                                                                <th>Miete</th> 
                                                                <th>Kaution</th>
                                                                <th>Bankdetails</th>
                                                                <th>Vertrag PDF</th>
                                                                <th>Status</th>
                                                                <th>Aktion</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($rent_property as $rent_proper)
                                                            <tr>
                                                                <th scope="row">{{ $rent_proper->id }}</th>
                                                                <th scope="row">{{ $rent_proper->owner }}</th>
                                                                <th>
                                                                    <a type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1 waves-effect waves-light" disabled href="{{ url('branch_contract_details/'.$rent_proper->object_id) }}">
                                                                        <i class="feather icon-phone"></i>
                                                                    </a>
                                                                </th>
                                                                <th scope="row">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered mb-0">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <th class="ths">Wohnfläche m³:</th> 
                                                                                    <th class="ths" >{{ $rent_proper->living_space ?? 'Unbekannt' }} m³</th> 
                                                                                </tr> 
                                                                                <tr>
                                                                                    <th class="ths" >Stellplatz</th> 
                                                                                    @if($rent_proper->parking==0)<th class="ths"> Nein</th>@else
                                                                                    <th class="ths">
                                                                                        <div class="badge badge-primary mr-1 mb-0">
                                                                                            <i class="fa fa-car"></i>
                                                                                            <span>Parkkosten: {{ $rent_proper->parking_cost }}</span>
                                                                                        </div><br>
                                                                                        <div class="badge badge-primary mr-1 mt-1">
                                                                                            <i class="fa fa-car"></i>
                                                                                            <span>Anzahl der Parkplätze: {{ $rent_proper->parking_count }}</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    @endif
                                                                                </tr>  
                                                                            </tbody>
                                                                        </table>
                                                                    </div> 
                                                                </th>
                                                                <th scope="row"> @if($rent_proper->contract_type == 'Limited') Befristet @else Unbefristet @endif</th>
                                                                <th scope="row">{{ \Carbon\Carbon::parse($rent_proper->contract_date)->isoFormat('DD.MM.YYYY') }} @if($rent_proper->contract_type=="Limited") - {{ $rent_proper->termination_date }} <br>    
                                                                    <div class="badge badge-primary mr-1">
                                                                    <i class="fa fa-calendar"></i>
                                                                        <span>Art der Vertragsbeendigung: <br><strong class="danger" >{{ $rent_proper->termination_type }}</strong></span>
                                                                    </div>@endif </th> 
                                                                <th scope="row">
                                                                        <div class="table-responsive">
                                                                        <table class="table table-bordered mb-0">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <th class="ths">  Kalte:</th> 
                                                                                    <th class="ths" >{{ number_format($rent_proper->cold_rent, 2, ',', '.') }}€</th> 
                                                                                </tr> 
                                                                                <tr>
                                                                                    <th class="ths" > Nabenkosten: </th> 
                                                                                        <th class="ths"> {{ number_format($rent_proper->extra_cost, 2, ',', '.') }}€</th>  
                                                                                </tr>  
                                                                                    <tr style="    background: #e8e1e1;">
                                                                                    <th class="ths" >  Gesamt</th> 
                                                                                    @php
                                                                                        $total_rent = $rent_proper->cold_rent + $rent_proper->extra_cost;
                                                                                    @endphp
                                                                                        <th class="ths"> {{ number_format($total_rent, 2, ',', '.') }}€</th>  
                                                                                </tr> 
                                                                            </tbody>
                                                                        </table>
                                                                    </div> 
                                                                </th>
                                                                <th scope="row">{{ number_format($rent_proper->advance_rent, 2, ',', '.') }}€</th>

                                                                <th scope="row">
                                                                        <div class="table-responsive">
                                                                        <table class="table table-bordered mb-0">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <th class="ths">Bankinhaber:</th> 
                                                                                    <th class="ths" >{{ $rent_proper->bank_user}}</th> 
                                                                                </tr> 
                                                                                <tr>
                                                                                    <th class="ths" >Bankname</th> 
                                                                                        <th class="ths"> {{ $rent_proper->bank_name }}</th>  
                                                                                </tr>  
                                                                                    <tr>
                                                                                    <th class="ths" >IBAN</th> 
                                                                                        <th class="ths"> {{ $rent_proper->iban }}</th>  
                                                                                </tr> 
                                                                            </tbody>
                                                                        </table>
                                                                    </div> 
                                                                </th>
                                                                <th>
                                                                    <a type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1 waves-effect waves-light" data-toggle="modal" data-target="#pdffile{{ $rent_proper->id }}" href="{{ url('branch_contract_details/'.$rent_proper->object_id) }}">
                                                                        <i class="feather icon-book"></i>
                                                                    </a>
                                                                </th>
                                                                <th scope="row">@if($rent_proper->status=="Published") Aktiv @else Deaktiviert  @endif </th>
                                                                <th>
                                                                    <div class="btn-group dropup dropdown-icon-wrapper mr-1 mb-1">
                                                                    @if($rent_proper->status!="Published")
                                                                        <a type="button" class="btn btn-primary waves-effect waves-light" href="{{ url('rent_property_publish/'.$rent_proper->id) }}">
                                                                            Aktiv
                                                                        </a>
                                                                    @else
                                                                        <a type="button" class="btn btn-danger waves-effect waves-light"  href="{{ url('rent_property_unpublish/'.$rent_proper->id) }}">
                                                                            Deaktiviert
                                                                        </a>
                                                                    @endif
                                                                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                            <i class="feather icon-menu dropdown-icon"></i>
                                                                        </button>
                                                                        <div class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(79px, -233px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                                            <span class="dropdown-item">
                                                                                <i class="feather icon-edit"></i>
                                                                            </span> 
                                                                            <span class="dropdown-item">
                                                                                <i class="feather icon-trash-2"></i>
                                                                            </span> 
                                                                        </div>
                                                                    </div>
                                                                </th>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item">
                                                    <span class="badge badge-pill bg-primary float-right">{{ number_format($rent_property->sum(function($rent) {  return $rent->cold_rent + $rent->warm_rent; }), 2, ',', '.') }}€
                                                    </span>
                                                    Zwischensumme
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tabVerticalLeft2" role="tabpanel" aria-labelledby="baseVerticalLeft-tab2">
                                  <div class="cards" >
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="float-right p-50">
                                                    <a type="button" class="btn btn-outline-success waves-effect waves-light" data-toggle="modal" data-target="#addrent"><i class="feather icon-plus" ></i> Neue</a>
                                                </div>
                                                <h4 class="card-title">Mietverwaltung <div class="badge badge-primary"></div></h4>
                                                <p class="card-text">Alle Mietdetails und Status</p>
                                            </div>

                                    
                                            <!-- Rent Operation Modal:start -->
                                            <div class="modal fade" id="addrent" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="deleteModalLabel">Bestätigung Aktualisieren</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        
                                                            <form id="rentForm" method="POST"> 
                                                            @csrf
                                                            <div class="modal-body">
                                                                <!-- Expense Details and Object ID (Hidden) -->
                                                                <input type="hidden" name="expense_details_id" value="{{ request()->expense }}">
                                                                <input type="hidden" name="object_id" value="{{ request()->id }}">

                                                                <!-- Apartment ID -->
                                                                <label>Vermieter</label>
                                                                <div class="form-group">
                                                                    <select name="apartment_id" id="apartment_id" class="form-control">
                                                                        <option>Wählen Sie die Wohnung</option> 
                                                                        @foreach ($rent_property as $rent_cost)
                                                                            <option value="{{ $rent_cost->id }}">{{ $rent_cost->owner }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('apartment_id')
                                                                        <p style="color:red;">{{ $message }}</p>
                                                                    @enderror
                                                                </div>

                                                                <!-- Cold Rent -->
                                                                <label>Kaltmeite</label>
                                                                <div class="form-group">
                                                                    <input type="number" id="cold_rent" class="form-control" name="cold_rent" value="{{ old('cold_rent') }}">
                                                                    @error('cold_rent')
                                                                        <p style="color:red;">{{ $message }}</p>
                                                                    @enderror
                                                                </div>

                                                                <!-- Electricity Cost -->
                                                                <label>Stromkosten</label>
                                                                <div class="form-group">
                                                                    <input type="number" class="form-control" name="electricity_cost" value="{{ old('electricity_cost') }}">
                                                                    @error('electricity_cost')
                                                                        <p style="color:red;">{{ $message }}</p>
                                                                    @enderror
                                                                </div>

                                                                <!-- Heating Cost -->
                                                                <label>Heizkosten</label>
                                                                <div class="form-group">
                                                                    <input type="number" class="form-control" name="heating_cost" value="{{ old('heating_cost') }}">
                                                                    @error('heating_cost')
                                                                        <p style="color:red;">{{ $message }}</p>
                                                                    @enderror
                                                                </div>

                                                                <!-- Repair Cost -->
                                                                <label>Reparaturkosten / Instandhaltungskosten</label>
                                                                <div class="form-group">
                                                                    <input type="number" class="form-control" name="repair_cost" value="{{ old('repair_cost') }}">
                                                                    @error('repair_cost')
                                                                        <p style="color:red;">{{ $message }}</p>
                                                                    @enderror
                                                                </div>

                                                                <!-- Extra Costs -->
                                                                <div class="form-group">
                                                                    <div class="table-responsive">
                                                                        <table class="table" id="extra_costs">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Nebenkosten</th>
                                                                                    <th>Kosten</th>
                                                                                    <th>Zahlung an</th>
                                                                                    <th>Aktion</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <th scope="row">
                                                                                        <input type="text" name="extra[0][name]" placeholder="Name..." class="form-control">
                                                                                    </th>
                                                                                    <td>
                                                                                        <input type="number" name="extra[0][cost]" placeholder="Kosten..." class="form-control">
                                                                                    </td>
                                                                                    <td>
                                                                                        <select name="extra[0][paid_to]" class="form-control paid_to">
                                                                                            <option value="Vermieter">Vermieter</option>
                                                                                            <option value="Firma">Firma</option>
                                                                                        </select>
                                                                                        <input type="text" class="form-control company" name="extra[0][company]" placeholder="Firmaname" style="display: none;">
                                                                                    </td>
                                                                                    <td>
                                                                                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1 waves-effect waves-light" id="add_cost">
                                                                                            <i class="feather icon-plus"></i>
                                                                                        </button>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>

                                                                <!-- Payment Date -->
                                                                <label for="">Zahlungsdatum</label>
                                                                <div class="form-group">
                                                                    <input type="date" class="form-control" name="payment_date" value="{{ old('payment_date') }}">
                                                                    @error('payment_date')
                                                                        <p style="color:red;">{{ $message }}</p>
                                                                    @enderror
                                                                </div>

                                                                <!-- Payee -->
                                                                <label for="">Bezahlt von</label>
                                                                <div class="form-group">
                                                                    <select class="select2 form-control" name="payee" id="payee" style="width: 100%;">
                                                                        @foreach ($employees as $emp)
                                                                            <option value="{{ $emp->id }}">{{ $emp->name }} {{ $emp->lastname }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('payee')
                                                                        <p style="color:red;">{{ $message }}</p>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary waves-effect waves-light">Einreichen</button>
                                                            </div>
                                                        </form>

                                                    </div>
                                                </div>
                                                
                                            </div>
                                                <!-- Rent Operation Modal:End -->
                                            <div class="card-content">
                                                <div class="card-body">

                                                <div class="accordion" id="accordionExample" data-toggle-hover="true">
                                                    @foreach ($rent_property as $extra )  
                                                    <div class="collapse-margin">
                                                        <div class="card-header collapsed" id="headingOne" data-toggle="collapse" role="button" data-target="#collapse{{$extra->id}}" aria-expanded="false" aria-controls="collapseOne">
                                                            <span class="lead collapse-title">
                                                                {{ $extra->owner }} : <div class="badge badge-primary">@if($extra->status=="Published") Aktiv @else Deaktiv @endif</div>
                                                            </span>
                                                        </div>

                                                        <div id="collapse{{$extra->id}}" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample" style="">
                                                            <div class="card-body">
                                                                <div class="table-responsive">
                                                                        <table class="table">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>ID</th>
                                                                                    <th>Kaltmiete</th>
                                                                                    <th>Stromkosten</th>  
                                                                                    <th>Nabenkosten</th>
                                                                                    <th>Gesamt</th>
                                                                                    <th>Zahlungsdatum</th>
                                                                                    <th>Zahlungsempfänger</th>
                                                                                    <th>Status</th>
                                                                                    <th>Aktion</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <tr>
                                                                                    @foreach ($rent_infos as $info)
                                                                                    @if($info->apartment_id== $extra->id)
                                                                                    <th scope="row">{{ $info->id }}</th>
                                                                                    <th scope="row">{{ number_format( $info->cold_rent, 2, ',', '.') }}€</th> 
                                                                                    <th scope="row">{{ number_format( $info->electricity_cost, 2, ',', '.') }}€</th>  
                                                                                    <th>
                                                                                        <button type="button" class="btn btn-outline-info waves-effect waves-light" data-toggle="modal" data-target="#info{{$info->id}}">
                                                                                            Info
                                                                                        </button>
                                                                                        <div class="modal fade text-left" id="info{{$info->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel130" style="display: none;" aria-hidden="true">
                                                                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                                                                <div class="modal-content">
                                                                                                    <div class="modal-header bg-info white">
                                                                                                        <h5 class="modal-title" id="myModalLabel130">{{ $extra->owner }}: Nabenkosten</h5>
                                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                            <span aria-hidden="true">×</span>
                                                                                                        </button>
                                                                                                    </div>
                                                                                                    <div class="modal-body">
                                                                                                        <div class="table-responsive">
                                                                                                            <table class="table table-striped mb-0">
                                                                                                                <thead>
                                                                                                                    <tr>
                                                                                                                        <th scope="col">Titel</th>
                                                                                                                        <th scope="col">Kosten</th>
                                                                                                                        <th scope="col">Zahlung an</th>
                                                                                                                        <th scope="col"></th> 
                                                                                                                    </tr>
                                                                                                                </thead>
                                                                                                                <tbody>
                                                                                                                    <tr>
                                                                                                                        <td>Heizkosten</td>
                                                                                                                        <td>{{ number_format( $info->heating_cost, 2, ',', '.') }}€</td>
                                                                                                                    </tr>
                                                                                                                        <tr>
                                                                                                                        <td>Reparaturkosten</td>
                                                                                                                        <td>{{ number_format( $info->repair_cost, 2, ',', '.') }}€</td>
                                                                                                                    </tr>
                                                                                                                    @php
                                                                                                                        $totalCost = 0; 
                                                                                                                        $mainCosts = 0; 
                                                                                                                    @endphp
                                                                                                                    @foreach ($rent_extra_costs as $extra_cost) 
                                                                                                                        @if($extra_cost->branch_rent_infos_id == $info->id)
                                                                                                                            @php 
                                                                                                                            if($extra_cost->paid_to != 'Firma'){
                                                                                                                                $ExtraCost =+ $info->extra_cost;
                                                                                                                                $totalCost = $ExtraCost  + $info->heating_cost + $info->repair_cost + $info->electricity_cost; 
                                                                                                                            } 
                                                                                                                                $mainCosts = $info->total - $totalCost;
                                                                                                                            @endphp
                                                                                                                            <tr>
                                                                                                                                <th scope="row">{{ $extra_cost->title }}</th>
                                                                                                                                <td>{{ $extra_cost->cost }}</td>
                                                                                                                                <td>{{ $extra_cost->paid_to }}: <br> @if($extra_cost->paid_to == 'Firma' ) {{ $extra_cost->company}} @endif</td>
                                                                                                                                <td>
                                                                                                                                    <button type="button" class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1 waves-effect waves-light" data-id="{{ $extra_cost->id }}" data-cost="{{$extra_cost->cost}}" data-rent="{{$info->id}}">
                                                                                                                                        <i class="feather icon-trash"></i>
                                                                                                                                    </button>
                                                                                                                                </td> 
                                                                                                                            </tr>
                                                                                                                        @endif
                                                                                                                    @endforeach 
                                                                                                                    
                                                                                                                </tbody>
                                                                                                            </table>
                                                                                                                 <form id="new_extra_cost" method="POST">
                                                                                                                    @csrf
                                                                                                                    <input type="hidden" name="expense_details_id" value="{{ request()->expense }}">
                                                                                                                    <input type="hidden" name="object_id" value="{{ request()->id }}">
                                                                                                                    <input type="hidden" name="rent_id" value="{{ $info->id }}">
                                                                                                                    <input type="hidden" name="apartment_id" value="{{ $extra->id }}">
                                                                                                                    <table class="table" id="extra_costs_dialog">
                                                                                                                        <thead>
                                                                                                                            <tr>
                                                                                                                                <th>Nebenkosten</th>
                                                                                                                                <th>Kosten</th>
                                                                                                                                <th>Zahlung an</th> 
                                                                                                                            </tr>
                                                                                                                        </thead>
                                                                                                                        <tbody>
                                                                                                                            <tr>
                                                                                                                                <th scope="row">
                                                                                                                                    <input type="text" name="extra[0][name]" placeholder="Name..." class="form-control">
                                                                                                                                </th>
                                                                                                                                <td>
                                                                                                                                    <input type="number" name="extra[0][cost]" placeholder="Kosten..." class="form-control">
                                                                                                                                </td>
                                                                                                                                <td>
                                                                                                                                    <select name="extra[0][paid_to]" class="form-control paid_to">
                                                                                                                                        <option value="Vermieter">Vermieter</option>
                                                                                                                                        <option value="Firma">Firma</option>
                                                                                                                                    </select>
                                                                                                                                    <input type="text" class="form-control company" name="extra[0][company]" placeholder="Firmaname" style="display: none;">
                                                                                                                                </td>
                                                                                                                                <td>
                                                                                                                                    <button type="button" class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1 waves-effect waves-light" id="add_cost_dialog">
                                                                                                                                        <i class="feather icon-plus"></i>
                                                                                                                                    </button>
                                                                                                                                </td> 
                                                                                                                            </tr>
                                                                                                                            <tr>
                                                                                                                            <td colspan="3">
                                                                                                                                <button type="submit" class="btn btn-outline-primary mr-1 mb-1 waves-effect waves-light">Neue Kosten speichern</button>
                                                                                                                            </td>
                                                                                                                            </tr> 
                                                                                                                        </tbody>
                                                                                                                    </table>
                                                                                                                </form> 
                                                                                                            <table>
                                                                                                                    <tr>
                                                                                                                        <td colspan="2"><strong>Nabenkosten:</strong></td>
                                                                                                                        <td><strong>{{ number_format($totalCost, 2, ',', '.') }} €</strong></td>
                                                                                                                    </tr>
                                                                                                                    <tr>
                                                                                                                        <td colspan="2"><strong>Houptkosten:</strong></td>
                                                                                                                        <td><strong>{{ number_format($info->cold_rent, 2, ',', '.') }} €</strong></td>
                                                                                                                    </tr>
                                                                                                                        <tr>
                                                                                                                        <td colspan="2"><strong>Gesamt:</strong></td>
                                                                                                                        <td><strong>{{ number_format($info->total, 2, ',', '.') }} €</strong></td>
                                                                                                                    </tr>
                                                                                                            </table>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="modal-footer">
                                                                                                        <button type="button" class="btn btn-info waves-effect waves-light" data-dismiss="modal">Accept</button>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </th> 
                                                                                    <th scope="row">{{ number_format( $info->total, 2, ',', '.') }}€</th> 
                                                                                
                                                                                    <th scope="row">{{ $info->payment_date }}</th>
                                                                                    <th scope="row">{{ $info->name }} {{ $info->lastname }} </th>
                                                                                    <th scope="row">{{ $info->status }}</th>
                                                                                    <th>
                                                                                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-flat-danger mr-1 mb-1 waves-effect waves-light" data-toggle="modal" data-target="#delete{{$info->id}}"><i class="feather icon-trash"></i></button>
                                                                                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-flat-success mr-1 mb-1 waves-effect waves-light"><i class="feather icon-edit"></i></button>
                                                                                    </th> 
                                                                                        <div class="modal fade text-left" id="delete{{$info->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel19" style="display: none;" aria-hidden="true">
                                                                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
                                                                                                <div class="modal-content">
                                                                                                    <div class="modal-header">
                                                                                                        <h4 class="modal-title" id="myModalLabel19">Löschen</h4>
                                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                            <span aria-hidden="true">×</span>
                                                                                                        </button>
                                                                                                    </div>
                                                                                                    <div class="modal-body">
                                                                                                       Möchten Sie diesen Datensatz löschen?
                                                                                                    </div>
                                                                                                    <div class="modal-footer">
                                                                                                        <a type="button" class="btn btn-danger waves-effect waves-light" href="{{ url('delete_branch_rent/'.$info->id) }}" >Ja</a>
                                                                                                        <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal">Nein</button>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                            </div>
                                                        </div>
                                                    </div> 
                                                    @endforeach 
                                                </div> 
                                                    
                                                </div>
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item">
                                                        <span class="badge badge-pill bg-primary float-right">{{ number_format( $rent_infos->sum('total_cost'), 2, ',', '.') }}€</span>
                                                        Zwischensumme
                                                    </li> 
                                                </ul>
                                            </div> 
                                            <!-- Rent Operation Modal:end -->
                                        </div>
                                    </div>
                            </div> 
                        </div>
                    </div>
            </div> <!-- Close the content-body -->
        </div> <!-- Close the content-wrapper -->
    </div>
    <!-- END: Content-->
@stop

@section('script')
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function(){
        @if(Session::has('update_msg'))
            toastr.success("{{ session('update_msg') }}");
        @endif
        @if(Session::has('save_msg'))
            toastr.success("{{ session('save_msg') }}");
        @endif
        @if(Session::has('delete_msg'))
            toastr.error("{{ session('delete_msg') }}");
        @endif
    });

    $('#payee').select2();
</script>

<script>
    $(document).ready(function() {
        // Function to toggle the visibility of the parking details
        function toggleParkingDetails() {
            var parkingValue = $('#parking').val();
            if (parkingValue == '1') {
                $('#parking-details').show();  // Show the parking details if "Ja" is selected
            } else {
                $('#parking-details').hide();  // Hide the parking details if "Nein" is selected
            }
        }

        // Function to toggle the visibility of the termination details
        function toggleTerminationDetails() {
            var contractTypeValue = $('#contract_type').val();
            if (contractTypeValue == 'Limited') {
                $('#termination-details').show();  // Show the termination details if "Limited" is selected
            } else {
                $('#termination-details').hide();  // Hide the termination details if "Permanent" is selected
                // Clear the termination fields when "Permanent" is selected
                $('#termination_date').val('');   // Clear termination_date
                $('#termination_type').val('');   // Clear termination_type
            }
        }

        // Run on page load
        toggleParkingDetails();
        toggleTerminationDetails();

        // Trigger toggleParkingDetails when the select value changes
        $('#parking').change(function() {
            toggleParkingDetails();
        });

        // Trigger toggleTerminationDetails when the contract_type value changes
        $('#contract_type').change(function() {
            toggleTerminationDetails();
        });
    });
</script>

 
<script>
    $(document).ready(function() {
        // Function to sync bank_user with owner if the checkbox is checked
        function syncBankUser() {
            var ownerValue = $('#owner').val();  // Get the value of the owner field
            var isChecked = $('#same_name').is(':checked');  // Check if the checkbox is checked

            if (isChecked) {
                $('#bank_user').val(ownerValue);  // Set the bank_user value to owner value if checkbox is checked
            }
        }

        // Sync on page load if checkbox is checked
        syncBankUser();

        // Trigger syncBankUser whenever the checkbox is checked/unchecked
        $('#same_name').change(function() {
            syncBankUser();
        });

        // Update bank_user in real-time as the owner field is changed, only if the checkbox is checked
        $('#owner').on('input', function() {
            if ($('#same_name').is(':checked')) {
                syncBankUser();  // Update bank_user value dynamically if owner is changed and checkbox is checked
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Function to check if the contract_type is 'Limited' and calculate the difference
        function calculateDifference() {
            var contractTypeValue = $('#contract_type').val();  // Check the contract type

            // Only proceed if the contract type is 'Limited'
            if (contractTypeValue === 'Limited') {
                var contractDate = $('#contract_date').val();
                var terminationDate = $('#termination_date').val();

                if (contractDate && terminationDate) {
                    // Convert input values to Date objects
                    var startDate = new Date(contractDate);
                    var endDate = new Date(terminationDate);

                    // Ensure the termination date is after the contract date
                    if (endDate >= startDate) {
                        // Calculate the difference in milliseconds
                        var timeDifference = endDate.getTime() - startDate.getTime();

                        // Convert milliseconds to days
                        var daysDifference = Math.floor(timeDifference / (1000 * 3600 * 24));

                        // Calculate years, months, and days
                        var years = endDate.getFullYear() - startDate.getFullYear();
                        var months = endDate.getMonth() - startDate.getMonth();
                        var days = endDate.getDate() - startDate.getDate();

                        if (days < 0) {
                            months--;
                            days += new Date(startDate.getFullYear(), startDate.getMonth() + 1, 0).getDate();
                        }

                        if (months < 0) {
                            years--;
                            months += 12;
                        }

                        // Display the result in the <strong> element
                        $('#contract_year').html(
                            'Dauer: ' + years + ' Jahr(e), ' + months + ' Monat(e), ' + days + ' Tag(e)'
                        );
                    } else {
                        $('#contract_year').html('Vertragsbeendigungsdatum muss nach dem Vertragsbeginn liegen.');
                    }
                } else {
                    $('#contract_year').html('');
                }
            } else {
                // Clear the result if the contract type is not 'Limited'
                $('#contract_year').html('');
            }
        }

        // Trigger the calculation when dates are changed
        $('#contract_date, #termination_date, #contract_type').change(function() {
            calculateDifference();
        });

        // Run calculation on page load if dates are already set and contract type is 'Limited'
        calculateDifference();
    });
</script>
<script>
    $(document).ready(function() {
        $('#rentPropertyForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

            // Clear previous messages
            $('#messages').html('');

            // Gather form data
            var formData = $(this).serialize();

            // Send AJAX POST request
            $.ajax({
                url: '{{ route('rent_property.store') }}', // URL to send the data to
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);  // Display success message using Toastr
                        $('#rentPropertyForm')[0].reset(); // Optionally reset the form if needed
                        
                        // Close the modal
                        $('#yourModalId').modal('hide');
                    } else {
                        toastr.error(response.message);   // Display error message using Toastr
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        toastr.error(value[0]); // Show each validation error using Toastr
                    });
                }
            });
        });
    });
</script>


<!-- Adding Extra Cost: start  -->
 <script> 
    $(document).ready(function() {
        var extraIndex = 0;

        // Dynamically add extra cost fields
        $('#add_cost').on('click', function(e) {
            e.preventDefault();
            extraIndex++;

            $('#extra_costs tbody').append(
                '<tr>' +
                    '<th scope="row"><input type="text" name="extra[' + extraIndex + '][name]" placeholder="Name..." class="form-control"></th>' +
                    '<td><input type="number" name="extra[' + extraIndex + '][cost]" placeholder="Kosten..." class="form-control"></td>' +
                    '<td>' +
                        '<select name="extra[' + extraIndex + '][paid_to]" class="form-control paid_to">' +
                            '<option value="Vermieter">Vermieter</option>' +
                            '<option value="Firma">Firma</option>' +
                        '</select>' +
                        '<input type="text" class="form-control company" name="extra[' + extraIndex + '][company]" placeholder="Firmaname" style="display: none;">' +
                    '</td>' +
                    '<td><button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger waves-effect waves-light remove_cost">' +
                        '<i class="feather icon-trash"></i></button></td>' +
                '</tr>'
            );
        });

        // Remove cost row
        $(document).on('click', '.remove_cost', function() {
            $(this).closest('tr').remove();
        });

        // Show/Hide company input when "Firma" is selected
        $(document).on('change', '.paid_to', function() {
            var selectedValue = $(this).val();
            var companyInput = $(this).closest('td').find('.company');

            if (selectedValue === 'Firma') {
                companyInput.show();
            } else {
                companyInput.hide();
            }
        });
    });

</script>


<!-- Adding Extra Cost: End  -->


<!-- Getting Cold Rent: Start  -->
 <script>
    $(document).ready(function() {
        $('#apartment_id').on('change', function() {
            var apartment_id = $(this).val();

            // Check if a valid apartment_id is selected
            if (apartment_id) {
                // Perform AJAX request to fetch cold_rent
                $.ajax({
                    url: '/get_cold_rent/' + apartment_id, // URL to fetch cold_rent
                    method: 'GET',
                    success: function(response) {
                        // Update the cold_rent input field with the response
                        $('#cold_rent').val(response);
                    },
                    error: function(xhr, status, error) {
                        console.log('Error fetching cold rent:', error);
                    }
                });
            }
        });
    });
</script>
<!-- Getting Cold Rent: End  -->

<script>
    $(document).on('change', '.paid_to', function() {
    var selectedValue = $(this).val();
    var companyInput = $(this).closest('tr').find('.company'); // Target the company input in the same row

    if (selectedValue === 'Firma') {
        companyInput.show();
    } else {
        companyInput.hide();
    }
});
</script>


    <script>
        $(document).ready(function () {
            $('#rentForm').on('submit', function (e) {
                e.preventDefault();
                
                $.ajax({
                    url: "{{ route('branch_rent_info.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.success);
                            // Redirect to the desired page after success
                            setTimeout(function () {
                                window.location.href = "{{ url()->current() }}"; // Reload the current page
                            }, 2000); // Wait 2 seconds before redirecting
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                toastr.error(value[0]); // Show validation error in Toastr
                            });
                        } else if (xhr.responseJSON.error) {
                            toastr.error(xhr.responseJSON.error);
                        } else {
                            toastr.error('Ein unerwarteter Fehler ist aufgetreten.');
                        }
                    }
                });
            });
        });
    </script>


<!-- Deleteing Extra Cost :start -->
 <script>
    $(document).on('click', '.btn-outline-danger', function() {
        var id = $(this).data('id');
        var cost = $(this).data('cost');
        var rent = $(this).data('rent');

        // Use SweetAlert to show a confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Perform the AJAX request to delete the record
                $.ajax({
                    url: '/delete_extra_cost/' + id + '/' + cost + '/' + rent,
                    type: 'GET',
                    success: function(response) {
                        // Show success message and reload the page
                        Swal.fire(
                            'Deleted!',
                            'The record has been deleted.',
                            'success'
                        ).then(() => {
                            location.reload(); // Reload the page after success
                        });
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            'There was an error deleting the record.',
                            'error'
                        );
                    }
                });
            }
        });
    });
</script>
<!-- Deleteing Extra Cost :End -->


<script> 
    $(document).ready(function() {
        var extraIndex = 0;

        // Dynamically add extra cost fields
        $('#add_cost_dialog').on('click', function(e) {
            e.preventDefault();
            extraIndex++;

            $('#extra_costs_dialog tbody').append(
                '<tr>' +
                    '<th scope="row"><input type="text" name="extra[' + extraIndex + '][name]" placeholder="Name..." class="form-control"></th>' +
                    '<td><input type="number" name="extra[' + extraIndex + '][cost]" placeholder="Kosten..." class="form-control"></td>' +
                    '<td>' +
                        '<select name="extra[' + extraIndex + '][paid_to]" class="form-control paid_to">' +
                            '<option value="Vermieter">Vermieter</option>' +
                            '<option value="Firma">Firma</option>' +
                        '</select>' +
                        '<input type="text" class="form-control company" name="extra[' + extraIndex + '][company]" placeholder="Firmaname" style="display: none;">' +
                    '</td>' +
                    '<td><button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger waves-effect waves-light remove_cost_dialog">' +
                        '<i class="feather icon-trash"></i></button></td>' +
                '</tr>'
            );
        });

        // Remove cost row
        $(document).on('click', '.remove_cost_dialog', function() {
            $(this).closest('tr').remove();
        });

        // Show/Hide company input when "Firma" is selected
        $(document).on('change', '.paid_to', function() {
            var selectedValue = $(this).val();
            var companyInput = $(this).closest('td').find('.company');

            if (selectedValue === 'Firma') {
                companyInput.show();
            } else {
                companyInput.hide();
            }
        });
    });

</script>


<script>
    $(document).ready(function () {
        // Handle form submission
        $('#new_extra_cost').on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission
            
            $.ajax({
                url: '{{ route('store.extra.cost.rent') }}', // Adjust the route name accordingly
                method: 'POST',
                data: $(this).serialize(),
                success: function (response) {
                    if (response.success) {
                        // Display success message using Toastr
                        toastr.success(response.success);

                        // Refresh the page after 2 seconds
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        // Loop through errors and display them using Toastr
                        $.each(errors, function (key, value) {
                            toastr.error(value[0]);
                        });
                    } else {
                        toastr.error('An unexpected error occurred.');
                    }
                }
            });
        });

        // Dynamically add more extra cost fields
        $('#add_cost_dialog').on('click', function () {
            let row = `
                <tr>
                    <th scope="row">
                        <input type="text" name="extra[${Date.now()}][name]" placeholder="Name..." class="form-control">
                    </th>
                    <td>
                        <input type="number" name="extra[${Date.now()}][cost]" placeholder="Kosten..." class="form-control">
                    </td>
                    <td>
                        <select name="extra[${Date.now()}][paid_to]" class="form-control">
                            <option value="Vermieter">Vermieter</option>
                            <option value="Firma">Firma</option>
                        </select>
                        <input type="text" class="form-control company" name="extra[${Date.now()}][company]" placeholder="Firmaname" style="display: none;">
                    </td>
                </tr>`;
            $('#extra_costs_dialog tbody').append(row);
        });
    });
</script>

@endsection
