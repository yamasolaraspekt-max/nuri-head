@extends('admin.layouts.app')
@section('title') Ausgabenarten @stop
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
                        <h2 class="content-header-title float-left mb-0">Filialkosten </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="">Details</a>
                                </li>

                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Table Hover Animation start -->
            <div class="row" id="table-hover-animation">
                <div class="col-md-6 col-12 mb-1">
                    <form action="">
                        <fieldset>
                            <div class="input-group">

                                <input type="text" class="form-control"
                                    placeholder="Geben Sie die Details Ihrer Suche ein" aria-describedby="button-addon2"
                                    name="search">
                                <div class="input-group-append" id="button-addon2">
                                    <button class="btn btn-primary waves-effect waves-light" type="button"><i
                                            class="feather icon-search"></i></button>
                                </div>

                            </div>

                        </fieldset>
                    </form>
                </div>
                <div class="col-md-2 mb-1">

                    <button type="button" class="btn btn-outline-primary block btn-lg" data-toggle="modal"
                        data-target="#default">
                        Neue hinzufügen
                    </button>
                    <!-- Modal -->
                    <div class="modal fade text-left" id="default" tabindex="-1" role="dialog"
                        aria-labelledby="myModalLabel1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel1">Neu</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form class="form-horizontal" novalidate method="post"
                                        action="{{action('App\Http\Controllers\ExpenseTypeController@store')}}"
                                        class="custom-file-upload" enctype="multipart/form-data">
                                        @csrf
                                        <fieldset>
                                            <div class="row">


                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                            Title
                                                        </label>

                                                        <input type="text" class="form-control" name="title" required>
                                                        @if ($errors->has('title'))<p style="color:red;">
                                                            {!!$errors->first('title')!!}</p>@endif
                                                    </div>
                                                </div>


                                            </div>
                                        </fieldset>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Einreichen</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal End -->

        </div>
  
        <!-- 3rd Row  start -->
        <section id="content-types">
            <div class="row match-height">
                <!-- Basic Expense Start -->
                <div class="col-xl-4 col-md-6 col-sm-12">
                    <div class="card" style="height: 614.562px;">
                        <div class="card-content">
                            <div class="card-body">
                                <h4 class="card-title"> Kostenübersicht für <div class="badge badge-primary">{{
                                        $data->branch }}</div>
                                </h4>
                                <p class="card-text">In diesem Abschnitt werden die grundlegenden und sonstigen Ausgaben
                                    der Zweigstelle untersucht</p>
                            </div>
                            <ul class="list-group list-group-flush">
                                @foreach ($branch_rent as $br_rent)
                                @if($br_rent->expense_details_id == request()->id)
                                <li class="list-group-item">

                                    <div class="btn-group dropup dropdown-icon-wrapper mr-0 mb-0">
                                        <button type="button"
                                            class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="feather icon-menu dropdown-icon"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <span class="dropdown-item">
                                                <a data-toggle="modal" data-target="#UpdateModal{{ $br_rent->id }}"><i
                                                        class="feather icon-edit"></i></a>
                                            </span>
                                            <span class="dropdown-item">
                                                <!-- Button trigger modal -->
                                                <a data-toggle="modal" data-target="#deleteModal{{ $br_rent->id }}"><i
                                                        class="feather icon-trash"></i></a>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Modal -->
                                    <div class="modal fade" id="deleteModal{{ $br_rent->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                            role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="deleteModalLabel">Bestätigung löschen
                                                    </h4>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Sind Sie sicher, dass Sie dieses Element löschen möchten?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Nein</button>
                                                    <a href="{{ url('branch_rent_destroy/'.$br_rent->id) }}"
                                                        type="button" class="btn btn-danger">Ja</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- UpdateModal -->
                                    <div class="modal fade" id="UpdateModal{{ $br_rent->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                            role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="deleteModalLabel">Bestätigung
                                                        Aktualisieren</h4>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <form class="form-horizontal" novalidate method="post"
                                                    action="{{action('App\Http\Controllers\BranchRentController@update')}}"
                                                    class="custom-file-upload" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <label>Name der Mieteigenschaft </label>
                                                        <div class="form-group">
                                                            <input type="hidden" name="expense_details_id" value={{
                                                                $data->id }}>
                                                            <input type="hidden" name="id" value={{ $br_rent->id }}>
                                                            <input type="text" placeholder="Name der Mieteigenschaft..."
                                                                class="form-control" name="object_name"
                                                                value="{{ $br_rent->object_name }}">
                                                        </div>

                                                        <div class="form-group">
                                                            <input type="text" placeholder="Name der Straße ..."
                                                                class="form-control" name="street" id="location-input"
                                                                value="{{ $br_rent->street }}">
                                                            @if ($errors->has('street'))<p style="color:red;">
                                                                {!!$errors->first('street')!!}</p>@endif
                                                        </div>

                                                        <div class="form-group">
                                                            <input type="text" placeholder="Hous-nummer..."
                                                                class="form-control" name="house_no"
                                                                id="administrative_area_level_1"
                                                                value="{{ $br_rent->house_no }}">
                                                            @if ($errors->has('house_no'))<p style="color:red;">
                                                                {!!$errors->first('house_no')!!}</p>@endif
                                                        </div>

                                                        <div class="form-group">
                                                            <input type="text" placeholder="Postleitzahl"
                                                                class="form-control" name="postcode"
                                                                id="postal_code-input" value="{{ $br_rent->postcode }}">
                                                            @if ($errors->has('postcode'))<p style="color:red;">
                                                                {!!$errors->first('postcode')!!}</p>@endif
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="text" placeholder="Stadt..."
                                                                class="form-control" name="city" id="locality-input"
                                                                value="{{ $br_rent->city }}">
                                                            @if ($errors->has('city'))<p style="color:red;">
                                                                {!!$errors->first('city')!!}</p>@endif
                                                        </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit"
                                                            class="btn btn-primary waves-effect waves-light">Einreichen</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Action Buttons -->
                                    <a href="{{ url('branch_rent/'.request()->id.'/'.$br_rent->id) }}"> {{
                                        $br_rent->object_name }}</a>
                                    <small><code>{{ $br_rent->street }} {{ $br_rent->house_no }} {{ $br_rent->postcode }}, {{ $br_rent->city }}</code></small>
                                    <span class="badge badge-pill bg-primary float-right">{{ number_format(
                                        $br_rent->total , 2, ',', '.') }}€</span>

                                </li>
                                @endif
                                @endforeach


                            </ul>
                            <div class="card-body">
                                <div class="form-group">
                                    <!-- Button trigger modal -->
                                    <a type="button" class="btn btn-outline-success waves-effect waves-light"
                                        data-toggle="modal" data-target="#rent">
                                        Ausgaben hinzufügen
                                    </a>

                                    <!-- Modal -->
                                    <div class="modal fade text-left" id="rent" tabindex="-1" role="dialog"
                                        aria-labelledby="myModalLabel33" style="display: none;" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                            role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="myModalLabel33">Kostenübersicht für <div
                                                            class="badge badge-primary">{{ $data->branch }}</div>
                                                    </h4>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                </div>
                                                <form class="form-horizontal" novalidate method="post"
                                                    action="{{action('App\Http\Controllers\BranchRentController@store')}}"
                                                    class="custom-file-upload" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <label>Name der Mieteigenschaft </label>
                                                        <div class="form-group">
                                                            <input type="hidden" name="expense_details_id" value={{
                                                                $data->id }}>
                                                            <input type="text" placeholder="Name der Mieteigenschaft..."
                                                                class="form-control" name="object_name"
                                                                value="{{ old('object_name') }}">
                                                        </div>

                                                        <div class="form-group">
                                                            <input type="text" placeholder="Name der Straße ..."
                                                                class="form-control" name="street" id="location-input"
                                                                value="{{ old('street') }}">
                                                            @if ($errors->has('street'))<p style="color:red;">
                                                                {!!$errors->first('street')!!}</p>@endif
                                                        </div>

                                                        <div class="form-group">
                                                            <input type="text" placeholder="Hous-nummer..."
                                                                class="form-control" name="house_no"
                                                                id="administrative_area_level_1"
                                                                value="{{ old('house_no') }}">
                                                            @if ($errors->has('house_no'))<p style="color:red;">
                                                                {!!$errors->first('house_no')!!}</p>@endif
                                                        </div>

                                                        <div class="form-group">
                                                            <input type="text" placeholder="Postleitzahl"
                                                                class="form-control" name="postcode"
                                                                id="postal_code-input" value="{{ old('postcode') }}">
                                                            @if ($errors->has('postcode'))<p style="color:red;">
                                                                {!!$errors->first('postcode')!!}</p>@endif
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="text" placeholder="Stadt..."
                                                                class="form-control" name="city" id="locality-input"
                                                                value="{{ old('city') }}">
                                                            @if ($errors->has('city'))<p style="color:red;">
                                                                {!!$errors->first('city')!!}</p>@endif
                                                        </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit"
                                                            class="btn btn-primary waves-effect waves-light">Einreichen</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Basic  Expense End -->

                <!-- Insurance Expense Start -->
                <div class="col-xl-4 col-md-6 col-sm-12">
                    <div class="card" style="height: 614.562px;">
                        <div class="card-content">
                            <div class="card-body">
                                <h4 class="card-title"> Versicherungsaufwand von <div class="badge badge-primary">{{
                                        $data->branch }}</div>
                                </h4>
                                <p class="card-text">Alle Versicherungsdetails und Kosten </p>
                            </div>
                            <ul class="list-group list-group-flush">
                                @foreach ($insurances as $insure)

                                <li class="list-group-item">
                                    <div class="btn-group dropup dropdown-icon-wrapper mr-0 mb-0">
                                        <button type="button"
                                            class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="feather icon-menu dropdown-icon"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <span class="dropdown-item">
                                                <a data-toggle="modal" data-target="#insureUpdate{{ $insure->id }}"><i
                                                        class="feather icon-edit"></i></a>
                                            </span>
                                            <span class="dropdown-item">
                                                <!-- Button trigger modal -->
                                                <a data-toggle="modal" data-target="#insuredelete{{ $insure->id }}"><i
                                                        class="feather icon-trash"></i></a>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Modal -->
                                    <div class="modal fade" id="insuredelete{{ $insure->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                            role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="deleteModalLabel">Bestätigung löschen
                                                    </h4>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Sind Sie sicher, dass Sie dieses Element löschen möchten?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Nein</button>
                                                    <a href="{{ url('branch_insurance_delete/'.$insure->id) }}"
                                                        type="button" class="btn btn-danger">Ja</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- UpdateModal -->
                                    <div class="modal fade" id="insureUpdate{{ $insure->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                            role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="deleteModalLabel">Bestätigung
                                                        Aktualisieren</h4>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <form class="form-horizontal" novalidate method="post"
                                                    action="{{action('App\Http\Controllers\BranchRentController@update')}}"
                                                    class="custom-file-upload" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <label>Name der Mieteigenschaft </label>
                                                        <div class="form-group">
                                                            <input type="hidden" name="expense_details_id" value="{{  $data->id }}">
                                                            <input type="hidden" name="id" value="{{ $br_rent->id }}">
                                                            <input type="text" placeholder="Name der Mieteigenschaft..."
                                                                class="form-control" name="object_name"
                                                                value="{{ $br_rent->object_name }}">
                                                        </div>

                                                        <div class="form-group">
                                                            <input type="text" placeholder="Name der Straße ..."
                                                                class="form-control" name="street" id="location-input"
                                                                value="{{ $br_rent->street }}">
                                                            @if ($errors->has('street'))<p style="color:red;">
                                                                {!!$errors->first('street')!!}</p>@endif
                                                        </div>

                                                        <div class="form-group">
                                                            <input type="text" placeholder="Hous-nummer..."
                                                                class="form-control" name="house_no"
                                                                id="administrative_area_level_1"
                                                                value="{{ $br_rent->house_no }}">
                                                            @if ($errors->has('house_no'))<p style="color:red;">
                                                                {!!$errors->first('house_no')!!}</p>@endif
                                                        </div>

                                                        <div class="form-group">
                                                            <input type="text" placeholder="Postleitzahl"
                                                                class="form-control" name="postcode"
                                                                id="postal_code-input" value="{{ $br_rent->postcode }}">
                                                            @if ($errors->has('postcode'))<p style="color:red;">
                                                                {!!$errors->first('postcode')!!}</p>@endif
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="text" placeholder="Stadt..."
                                                                class="form-control" name="city" id="locality-input"
                                                                value="{{ $br_rent->city }}">
                                                            @if ($errors->has('city'))<p style="color:red;">
                                                                {!!$errors->first('city')!!}</p>@endif
                                                        </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit"
                                                            class="btn btn-primary waves-effect waves-light">Einreichen</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="badge badge-pill bg-primary float-right">{{ number_format(
                                        $insure->monthly_payable, 2, ',', '.') }}€</span>
                                    {{ $insure->insurance_for }} - {{ $insure->provider }}
                                </li>
                                @endforeach
                                <li class="list-group-item">
                                    <span class="badge badge-pill bg-primary float-right">{{ number_format(
                                        $insurances->sum('monthly_payable'), 2, ',', '.') }}€</span>
                                    Zwischensumme
                                </li>

                            </ul>
                            <div class="card-body">
                                <!-- Button trigger modal -->
                                <a type="button" class="btn btn-outline-success waves-effect waves-light"
                                    data-toggle="modal" data-target="#insurance">
                                    Ausgaben hinzufügen
                                </a>

                                <!-- Modal -->
                                <div class="modal fade text-left" id="insurance" tabindex="-1" role="dialog"
                                    aria-labelledby="myModalLabel33" style="display: none;" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                        role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="myModalLabel33">Kostenübersicht für <div
                                                        class="badge badge-primary">{{ $data->branch }}</div>
                                                </h4>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <form class="form-horizontal" novalidate method="post"
                                                action="{{action('App\Http\Controllers\BranchInsuranceController@store')}}"
                                                class="custom-file-upload" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-body">
                                                    <label>Versicherung für </label>
                                                    <div class="form-group">
                                                        <input type="hidden" name="expense_types_id" value={{
                                                            request()->id }}>
                                                        <select name="insurance_for" class="form-control">
                                                            <option value="Gesundheit">Gesundheit</option>
                                                            <option value="Haftung">Haftung</option>
                                                            <option value="Eigentum">Eigentum</option>
                                                            <option value="Reisen">Reisen</option>
                                                            <option value="Berufshaftpflicht">Berufshaftpflicht</option>
                                                            <option value="Cyber">Cyber</option>
                                                            <option value="Betriebsunterbrechung">Betriebsunterbrechung
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <label>Versicherungsnummer</label>
                                                    <div class="form-group">
                                                        <input type="text" placeholder="Versicherungsnummer..."
                                                            class="form-control" name="policy_number"
                                                            value="{{ old('policy_number') }}">
                                                        @if ($errors->has('policy_number'))<p style="color:red;">
                                                            {!!$errors->first('policy_number')!!}</p>@endif
                                                    </div>
                                                    <label>Versicherer</label>
                                                    <div class="form-group">
                                                        <input type="text" placeholder="Versicherer..."
                                                            class="form-control" name="provider"
                                                            value="{{ old('provider') }}">
                                                        @if ($errors->has('provider'))<p style="color:red;">
                                                            {!!$errors->first('provider')!!}</p>@endif
                                                    </div>
                                                    <label>Deckungsbetrag</label>
                                                    <div class="form-group">
                                                        <input type="text" placeholder="Deckungsbetrag"
                                                            class="form-control" name="coverage_amount"
                                                            value="{{ old('coverage_amount') }}">
                                                        @if ($errors->has('coverage_amount'))<p style="color:red;">
                                                            {!!$errors->first('coverage_amount')!!}</p>@endif
                                                    </div>

                                                    <label>Monatlich zahlbar </label>
                                                    <div class="form-group">
                                                        <input type="text" placeholder="Monatlich zahlbar "
                                                            class="form-control" name="monthly_payable"
                                                            value="{{ old('monthly_payable') }}">
                                                        @if ($errors->has('monthly_payable'))<p style="color:red;">
                                                            {!!$errors->first('monthly_payable')!!}</p>@endif
                                                    </div>
                                                    <label>Startdatum</label>
                                                    <div class="form-group">
                                                        <input type="date" placeholder="Startdatum..."
                                                            class="form-control" name="start_date" id="locality-input"
                                                            value="{{ old('start_date') }}">
                                                        @if ($errors->has('start_date'))<p style="color:red;">
                                                            {!!$errors->first('start_date')!!}</p>@endif
                                                    </div>
                                                    <label>Enddatum</label>
                                                    <div class="form-group">
                                                        <input type="date" placeholder="Enddatum..."
                                                            class="form-control" name="end_date" id="locality-input"
                                                            value="{{ old('end_date') }}">
                                                        @if ($errors->has('end_date'))<p style="color:red;">
                                                            {!!$errors->first('end_date')!!}</p>@endif
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit"
                                                        class="btn btn-primary waves-effect waves-light">Einreichen</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Insurance Expenses End -->

                <!-- Employees Expense Start -->

                <div class="col-xl-4 col-md-6 col-sm-12">
                    <section id="nav-justified">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card overflow-hidden">
                                    <div class="card-header">
                                        <h4 class="card-title">Mitarbeiterkosten des Solaraspekts</h4>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body">
                                            <p>Die personen- und mitarbeiterkostenbasierten Mitarbeiter und Abteilungen
                                            </p>
                                            <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" id="home-tab-justified" data-toggle="tab"
                                                        href="#home-just" role="tab" aria-controls="home-just"
                                                        aria-selected="true">Mitarbeiter</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="profile-tab-justified" data-toggle="tab"
                                                        href="#profile-just" role="tab" aria-controls="profile-just"
                                                        aria-selected="true">Abteilung</a>
                                                </li>


                                            </ul>

                                            <!-- Tab panes -->
                                            <div class="tab-content pt-1">
                                                <div class="tab-pane active" id="home-just" role="tabpanel"
                                                    aria-labelledby="home-tab-justified">
                                                    <ul class="list-group list-group-flush">

                                                        @foreach ($employees as $emp)
                                                        <li class="list-group-item">
                                                            <span class="badge badge-pill bg-primary float-right">{{
                                                                number_format(
                                                                $emp->salary, 2, ',', '.') }}€</span>
                                                            {{ $emp->name }} {{ $emp->lastname }} @ {{
                                                            $emp->department_name }} - {{
                                                            $emp->position }}
                                                        </li>
                                                        @endforeach
                                                        <li class="list-group-item">
                                                            <span class="badge badge-pill bg-primary float-right">{{
                                                                number_format(
                                                                $employees->sum('salary'), 2, ',', '.')
                                                                }}€</span>
                                                            Zwischensumme
                                                        </li>

                                                    </ul>
                                                </div>
                                                <div class="tab-pane" id="profile-just" role="tabpanel"
                                                    aria-labelledby="profile-tab-justified">
                                                    <p>
                                                    <ul class="list-group list-group-flush">

                                                        @foreach ($departments as $dept)
                                                        <li class="list-group-item">
                                                            <span class="badge badge-pill bg-primary float-right">{{
                                                                number_format(
                                                                $dept->total_salary, 2, ',', '.') }}€</span>
                                                            {{ $dept->department_name }}
                                                        </li>
                                                        @endforeach
                                                        <li class="list-group-item">
                                                            <span class="badge badge-pill bg-primary float-right">{{
                                                                number_format(
                                                                $departments->sum('total_salary'), 2, ',', '.')
                                                                }}€</span>
                                                            Zwischensumme
                                                        </li>

                                                    </ul>
                                                    </p>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <!-- Employees Expenses End -->



                <!-- Machine / Cars Expense Start -->
                <div class="col-xl-4 col-md-6 col-sm-12">
                    <div class="card" style="height: 614.562px;">
                        <div class="card-content">
                            <div class="card-body">
                                <h4 class="card-title"> Maschinen- und Fahrzeugdetails sowie Kosten </h4>
                                <p class="card-text">Einzelheiten zu Kosten, Raten und Ausgaben für Maschinen und Autos
                                    von
                                <div class="badge badge-primary">{{ $data->branch }}</div>
                                </p>
                            </div>
                            <ul class="list-group list-group-flush">

                                @foreach ($machines as $mach)
                                <li class="list-group-item">
                                    <span class="badge badge-pill bg-primary float-right">{{ number_format(
                                        $mach->purchase_price, 2, ',', '.') }}€</span>
                                    {{ $mach->name }} {{ $mach->model }} @ {{ $mach->year }} - {{ $mach->article_group
                                    }}
                                </li>
                                @endforeach
                                <li class="list-group-item">
                                    <span class="badge badge-pill bg-primary float-right">{{ number_format(
                                        $machines->sum('purchase_price'), 2, ',', '.') }}€</span>
                                    Zwischensumme
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Machine Expenses End -->


                <!-- Assets Expense Start -->
                <div class="col-xl-4 col-md-6 col-sm-12">
                    <div class="card" style="height: 614.562px;">
                        <div class="card-content">
                            <div class="card-body">
                                <h4 class="card-title"> Filialvermögen Kosten </h4>
                                <p class="card-text">Details zu den Inventarkosten der Vermögenswerte in der aktuellen
                                    Branche
                                <div class="badge badge-primary">{{ $data->branch }}</div>
                                </p>
                            </div>
                            <ul class="list-group list-group-flush">

                                @foreach ($assets as $asset)
                                <li class="list-group-item">
                                    <span class="badge badge-pill bg-primary float-right">{{ number_format(
                                        $asset->purchase_price, 2, ',', '.') }}€</span>
                                    {{ $asset->item }} {{ $asset->model }} @ {{ $asset->category }}
                                </li>
                                @endforeach
                                <li class="list-group-item">
                                    <span class="badge badge-pill bg-primary float-right">{{ number_format(
                                        $assets->sum('purchase_price'), 2, ',', '.') }}€</span>
                                    Zwischensumme
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Assets Expenses End -->

                <!-- asset Installments Start -->
                <div class="col-xl-4 col-md-6 col-sm-12">
                    <div class="card" style="height: 614.562px;">
                        <div class="card-content">
                            <div class="card-body">
                                <h4 class="card-title"> Vermögenswert bei Ratenzahlungen </h4>
                                <p class="card-text">Einzelheiten zur Ratenzahlung von Vermögenswerten
                                <div class="badge badge-primary">{{ $data->branch }}</div>
                                </p>
                            </div>
                            <ul class="list-group list-group-flush">

                                @foreach ($installments as $install)
                                <li class="list-group-item">
                                    <span class="badge badge-pill bg-primary float-right">{{ number_format(
                                        $install->total, 2, ',', '.') }}€</span>
                                    {{ $install->item }} {{ $install->model }}
                                </li>
                                @endforeach
                                <!-- <li class="list-group-item">
                                    <span class="badge badge-pill bg-primary float-right">{{ number_format(
                                        $installments->sum('purchase_price'), 2, ',', '.') }}€</span>
                                    Zwischensumme
                                </li> -->

                            </ul>
                        </div>
                    </div>
                </div>
                <!-- asset End -->

                <!-- asset Installments Start -->
                <div class="col-xl-4 col-md-6 col-sm-12">
                    <div class="card" style="height: 614.562px;">
                        <div class="card-content">
                            <div class="card-body">
                                <h4 class="card-title"> Machine bei Ratenzahlungen </h4>
                                <p class="card-text">Einzelheiten zur Ratenzahlung von Maschine
                                <div class="badge badge-primary">{{ $data->branch }}</div>
                                </p>
                            </div>
                            <ul class="list-group list-group-flush">

                                @foreach ($machine_installments as $install)
                                <li class="list-group-item">
                                    <span class="badge badge-pill bg-primary float-right">{{ number_format(
                                        $install->total, 2, ',', '.') }}€</span>
                                    {{ $install->name }} {{ $install->model }}
                                </li>
                                @endforeach
                                <!-- <li class="list-group-item">
                                    <span class="badge badge-pill bg-primary float-right">{{ number_format(
                                        $installments->sum('purchase_price'), 2, ',', '.') }}€</span>
                                    Zwischensumme
                                </li> -->

                            </ul>
                        </div>
                    </div>
                </div>
                <!-- asset End -->
            </div>
        </section>
    </div>
</div>
</div>
</div>
<!-- END: Content-->
@stop

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




@endsection