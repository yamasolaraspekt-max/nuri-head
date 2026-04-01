@extends('admin.layouts.app')

@section('title') Employee Details @endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/card-analytics.css') }}">
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
                        <h2 class="content-header-title float-left mb-0">MITARBEITERKONTO</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">HOME</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ url('/emp') }}">MITARBEITER</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#">NEUE</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="container">
                <div id="accordion">
                <!-- MITARBEITERPROFIL Section -->
                    <div class="cards">
                        <div class="card-header" id="headingOne" style="background:transparent !important;">
                            <h5 class="mb-0">
                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    <h2 class="primary text-bold-700">MITARBEITERPROFIL</h2>
                                </button>
                            </h5>
                        </div>
                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                            <div class="card-body">
                               
                                @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                                <form novalidate method="post" action="{{ action('App\Http\Controllers\EmployeeController@add') }}" class="custom-file-upload" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row"> 
                                        <!-- Personal Information Fields -->
                                        <div class="col-lg-6 col-md-12 col-sm-12">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                    <div class="col-lg-2 col-md-4 col-sm-12">
                                                        <label for="lastname">Vorname</label> 
                                                    </div>
                                                      <div class="col-lg-10 col-md-4 col-sm-12"> 
                                                        <input type="text" class="form-control" id="lastname" name="lastname" value="{{ old('lastname') }}">
                                                    </div>

                                                </div>
                                                <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                    <div class="col-lg-2 col-md-4 col-sm-12">
                                                        <label for="name">Name</label> 
                                                    </div>
                                                     <div class="col-lg-10 col-md-4 col-sm-12"> 
                                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                    <div class="col-lg-2 col-md-4 col-sm-12">
                                                        <label for="plzOrt">Straße / Nr.</label> 
                                                    </div>
                                                     <div class="col-lg-10 col-md-4 col-sm-12"> 
                                                        <input type="text" class="form-control" id="street" name="street" value="{{ old('street') }}">
                                                    </div>
                                                </div>
                                                  <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                    <div class="col-lg-2 col-md-4 col-sm-12">
                                                        <label for="plzOrt">PLZ / Ort</label> 
                                                    </div>
                                                     <div class="col-lg-10 col-md-4 col-sm-12"> 
                                                        <input type="text" class="form-control" id="postcode" name="postcode" value="{{ old('postcode') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                    <div class="col-lg-2 col-md-4 col-sm-12">
                                                        <label for="telHandy">Tel./Handy</label> 
                                                    </div>
                                                     <div class="col-lg-10 col-md-4 col-sm-12"> 
                                                        <input type="text" class="form-control" id="telHandy" name="telHandy" value="{{ old('telHandy') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                    <div class="col-lg-2 col-md-4 col-sm-12">
                                                        <label for="email">E-Mail</label> 
                                                    </div>
                                                     <div class="col-lg-10 col-md-4 col-sm-12"> 
                                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                    <div class="col-lg-2 col-md-4 col-sm-12">
                                                        <label for="dob">Geburtsdatum</label> 
                                                    </div>
                                                    <div class="col-lg-10 col-md-4 col-sm-12"> 
                                                        <input type="date" class="form-control" id="dob" name="dob" value="{{ old('dob') }}" onchange="calculateAge()">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                    <div class="col-lg-2 col-md-4 col-sm-12">
                                                        <label for="age">Alter</label> 
                                                    </div>
                                                       <div class="col-lg-10 col-md-4 col-sm-12"> 
                                                        <input type="text" class="form-control" id="age" value=" " oninput="calculateBirthDate()">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-12 col-sm-12">
                                            <div class="row">
                                                <div class="col-lg-5 col-md-6 col-sm-12" style="background:white;">
                                                    <div class="media-body" style="align-text:center; text-align: -webkit-center;">
                                                        <div style="position: relative; display: inline-block;">
                                                            <img src="{{ asset('images/gender/male.png') }}" class="rounded mr-75" id="picture" alt="profile image" height="200" width="200">
                                                            <button type="button" class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1 waves-effect waves-light" id="removeButton" onclick="removePicture()" style="position: absolute; top: 0; right: 0; display: none;">
                                                                <i class="feather icon-trash"></i>
                                                            </button>
                                                        </div>
                                                        <div class="cards text-center mt-2">
                                                            <div class="card-content">
                                                                <div class="col-12" style="margin-top:10px !important;">
                                                                    <button type="button" class="btn btn-primary mr-1 mb-1 waves-effect waves-light" data-toggle="modal" data-target="#default">
                                                                        <i class="feather icon-image"></i> Foto hochladen
                                                                    </button>
                                                                    <!-- Modal -->
                                                                    <div class="modal fade text-left" id="default" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h4 class="modal-title" id="myModalLabel1">Profilbild</h4>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <fieldset>
                                                                                        <div class="row">
                                                                                            <div class="col-md-12">
                                                                                                <div class="col-lg-2 col-md-4 col-sm-12">
                                                                                                    <label for="Title">
                                                                                                        <code><strong>Hinweis: Die Größe des Bildes wirkt sich auf die Leistung der Datenbank aus</strong></code>
                                                                                                    </label>
                                                                                                    <input type="file" class="form-control" id="upload" name="image" onchange="previewImage()" required>
                                                                                                    @if ($errors->has('image'))
                                                                                                    <p style="color:red;">{!! $errors->first('image') !!}</p>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" data-dismiss="modal" class="btn btn-primary">Schließen</button>
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
                                    </div>

                                    <div class="row">
                                          <div class="col-lg-6 col-md-12 col-sm-12 mt-4">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                    <div class="col-lg-2 col-md-4 col-sm-12">
                                                        <label for="lastname">Geburtsort</label> 
                                                    </div>
                                                      <div class="col-lg-10 col-md-4 col-sm-12"> 
                                                        <input type="text" class="form-control" id="lastname" name="lastname" value="{{ old('lastname') }}">
                                                    </div>

                                                </div>
                                                <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                    <div class="col-lg-2 col-md-4 col-sm-12">
                                                        <label for="name">RV-Nr.</label> 
                                                    </div>
                                                     <div class="col-lg-10 col-md-4 col-sm-12"> 
                                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                    <div class="col-lg-2 col-md-4 col-sm-12">
                                                        <label for="plzOrt">Steuer ID</label> 
                                                    </div>
                                                     <div class="col-lg-10 col-md-4 col-sm-12"> 
                                                        <input type="text" class="form-control" id="street" name="street" value="{{ old('street') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                    <div class="col-lg-2 col-md-4 col-sm-12">
                                                        <label for="plzOrt">Steuer-Klasse</label> 
                                                    </div>
                                                     <div class="col-lg-10 col-md-4 col-sm-12"> 
                                                        @if(count($taxes))
                                                        <select class="form-control" name="tax_class">
                                                            @foreach ($taxes as $tax)
                                                            <option value="{{ $tax->id }}" @if(old('tax_class')) selected @endif>{{ $tax->tax }}% - {{$tax->class}}</option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('tax'))
                                                        <p style="color:red;">{!! $errors->first('tax_class') !!}</p>
                                                        @endif
                                                        @else
                                                        <a class="btn btn-success col-12" href="{{ url('/tax') }}">STEUER KLASSE HINZUFÜGEN</a>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                    <div class="col-lg-2 col-md-4 col-sm-12">
                                                        <label for="telHandy">Kinder</label> 
                                                    </div>
                                                     <div class="col-lg-10 col-md-4 col-sm-12"> 
                                                        <select class="form-control" name="kids" id="kids">
                                                            <option selected disabled>Hat der Mitarbeiter Kinder?</option>
                                                            <option value="Yes" {{ old('kids') == 'Yes' ? 'selected' : '' }}>Ja</option>
                                                            <option value="No" {{ old('kids') == 'No' ? 'selected' : '' }}>Nein</option>
                                                        </select>
                                                        @if ($errors->has('kids'))
                                                        <p style="color:red;">{!! $errors->first('kids') !!}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                    <div class="col-lg-2 col-md-4 col-sm-12">
                                                        <label for="email">Konfession</label> 
                                                    </div>
                                                     <div class="col-lg-10 col-md-4 col-sm-12"> 
                                                         <select class="form-control" name="religion" id="religion">
                                                            <option value="Katholisch" {{ old('religion') == 'Katholisch' ? 'selected' : '' }}>Katholisch</option>
                                                            <option value="Evangelisch" {{ old('religion') == 'Evangelisch' ? 'selected' : '' }}>Evangelisch</option>
                                                            <option value="Muslimisch" {{ old('religion') == 'Muslimisch' ? 'selected' : '' }}>Muslimisch</option>
                                                            <option value="Orthodox" {{ old('religion') == 'Orthodox' ? 'selected' : '' }}>Orthodox</option>
                                                            <option value="Keine" {{ old('religion') == 'Keine' ? 'selected' : '' }}>Keine</option>
                                                            <option value="Hinduistisch" {{ old('religion') == 'Hinduistisch' ? 'selected' : '' }}>Hinduistisch</option>
                                                            <option value="Buddhistisch" {{ old('religion') == 'Buddhistisch' ? 'selected' : '' }}>Buddhistisch</option>
                                                            <option value="Jüdisch" {{ old('religion') == 'Jüdisch' ? 'selected' : '' }}>Jüdisch</option>
                                                            <option value="Andere" {{ old('religion') == 'Andere' ? 'selected' : '' }}>Andere</option>
                                                        </select>
                                                        @if ($errors->has('religion'))
                                                        <p style="color:red;">{!! $errors->first('religion') !!}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                    <div class="col-lg-2 col-md-4 col-sm-12">
                                                        <label for="dob">nationaliät</label> 
                                                    </div>
                                                    <div class="col-lg-10 col-md-4 col-sm-12"> 
                                                        <select class="form-control" id="nationality" name="nationality">
                                                            @foreach ($countries as $country)
                                                            <option value="{{ $country->id }}" @if(old('nationality'))
                                                                selected @endif>{{ $country->nationality }}</option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('nationality'))
                                                                <p style="color:red;">{!! $errors->first('nationality') !!}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                    <div class="col-lg-2 col-md-4 col-sm-12">
                                                        <label for="age">Sprachen</label> 
                                                    </div>
                                                       <div class="col-lg-10 col-md-4 col-sm-12"> 
                                                       <select class="form-control" id="language" multiple="multiple" name="language[]">
                                                            @foreach ($languages as $lang)
                                                                <option value="{{ $lang->id }}"    >  {{ $lang->language }}  </option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('language'))
                                                            <p style="color:red;">{!! $errors->first('language') !!}</p>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                    <div class="col-lg-2 col-md-4 col-sm-12">
                                                        <label for="health_insurance">Krankenkasse</label> 
                                                    </div>
                                                     <div class="col-lg-10 col-md-4 col-sm-12"> 
                                                        <input type="text" class="form-control" id="health_insurance" name="health_insurance" value="{{ old('health_insurance') }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                    <div class="col-lg-2 col-md-4 col-sm-12">
                                                        <label for="bank_name">Bank</label> 
                                                    </div>
                                                     <div class="col-lg-10 col-md-4 col-sm-12"> 
                                                        <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ old('bank_name') }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                    <div class="col-lg-2 col-md-4 col-sm-12">
                                                        <label for="iban">IBAN</label> 
                                                    </div>
                                                     <div class="col-lg-10 col-md-4 col-sm-12"> 
                                                        <input type="text" class="form-control" id="iban" name="iban" value="{{ old('iban') }}">
                                                    </div>
                                                </div>
                                            </div>
                                          </div>
                                        <div class="col-lg-6 col-md-12 col-sm-12">
                                            <div class="col-lg-12">
                                                <div class="default-collapse collapse-bordered">
                                                    <div class="cards collapse-header">
                                                        <div id="headingCollapse1" class="card-header" style="background:transparent" data-toggle="collapse" role="button" data-target="#collapse1" aria-expanded="false" aria-controls="collapse1">
                                                            <span class="lead collapse-title">
                                                                bei EQ Maßnahmen: <i class="feather icon-chevron-down"></i>
                                                            </span>
                                                        </div>
                                                        <div id="collapse1" role="tabpanel" aria-labelledby="headingCollapse1" class=" " style="">
                                                            <div class="card-content">
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                <label for="resident_permit">Aufenthaltstitel</label> 
                                                                            </div>
                                                                            <div class="col-lg-9 col-md-4 col-sm-12"> 
                                                                                <input type="text" class="form-control" id="resident_permit" name="resident_permit" value="{{ old('resident_permit') }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                <label for="work_permit">Arbeitsberechtigung</label> 
                                                                            </div>
                                                                            <div class="col-lg-9 col-md-4 col-sm-12"> 
                                                                                <input type="text" class="form-control" id="resident_permit" name="work_permit" value="{{ old('work_permit') }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                <label for="salary_per_hour">Anzahl Std.</label> 
                                                                            </div>
                                                                            <div class="col-lg-9 col-md-4 col-sm-12"> 
                                                                                <input type="text" class="form-control" id="salary_per_hour" name="salary_per_hour" value="{{ old('salary_per_hour') }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-12 col-md-4 col-sm-12 d-flex mb-1">
                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                <label for="salary_per_hour">Befristung bis?</label> 
                                                                            </div>
                                                                            <div class="col-lg-9 col-md-4 col-sm-12"> 
                                                                                <input type="text" class="form-control" id="salary_per_hour" name="salary_per_hour" value="{{ old('salary_per_hour') }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-12 col-md-4 col-sm-12  d-flex mb-1">
                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                <label for="salary_per_hour">zuständige Behörde</label> 
                                                                            </div>
                                                                            <div class="col-lg-9 col-md-4 col-sm-12"> 
                                                                                    @if(count($branches))
                                                                                        <select class="form-control" name="branch">
                                                                                            @foreach ($branches as $bran)
                                                                                            <option value="{{ $bran->id }}" @if(old('branch')) selected @endif>{{ $bran->branch }}</option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                        @if ($errors->has('branch'))
                                                                                        <p style="color:red;">{!! $errors->first('branch') !!}</p>
                                                                                        @endif
                                                                                    @else
                                                                                        <a class="btn btn-success col-12" href="{{ url('/branch') }}">ZWEIG HINZUFÜGEN</a>
                                                                                    @endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-12 col-md-4 col-sm-12  d-flex mb-1">
                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                <label for="salary_per_hour">Ansprechpartner</label> 
                                                                            </div>
                                                                            <div class="col-lg-9 col-md-4 col-sm-12"> 
                                                                                <input type="text" class="form-control" id="salary_per_hour" name="salary_per_hour" value="{{ old('salary_per_hour') }}">
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
                                      

                                    <!-- Additional Fields -->
                                    <div class="row">
                                        <!-- Add your additional fields here -->
                                    </div>

                                    <br>
                                    <hr>
                                    <div class="col-12 d-flex mb-1 flex-sm-row flex-column justify-content-end">
                                        <button type="submit" class="btn btn-primary mr-sm-1 mb-1 mb-sm-0">Speichern</button>
                                        <button type="reset" class="btn btn-outline-warning">Stornieren</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- QUALIFIKATION Section -->
                    <div class="cards">
                        <div class="card-header" id="headingTwo" style="background:transparent !important;">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                <h2 class="primary text-bold-700"> QUALIFIKATION </h2>
                                </button>
                            </h5>
                        </div>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                            <div class="card-body">
                                <!-- Qualifikation Content -->
                            </div>
                        </div>
                    </div>

                    <!-- KOMPETENZEN Section -->
                    <div class="cards">
                        <div class="card-header" id="headingThree" style="background:transparent !important;">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    <h2 class="primary text-bold-700">KOMPETENZEN</h2>
                                </button>
                            </h5>
                        </div>
                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                            <div class="card-body">
                                <!-- Kompetenzen Content -->
                            </div>
                        </div>
                    </div>

                    <!-- URLAUB/KRANKHEIT Section -->
                    <div class="cards">
                        <div class="card-header" id="headingFour" style="background:transparent !important;">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    <h2 class="primary text-bold-700">URLAUB/KRANKHEIT</h2>
                                </button>
                            </h5>
                        </div>
                        <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordion">
                            <div class="card-body">
                                <!-- Urlaub/Krankheit Content -->
                            </div>
                        </div>
                    </div>

                    <!-- ÜBERGABE-LISTE Section -->
                    <div class="cards">
                        <div class="card-header" id="headingFive" style="background:transparent !important;">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                    <h2 class="primary text-bold-700">ÜBERGABE-LISTE</h2>
                                </button>
                            </h5>
                        </div>
                        <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#accordion">
                            <div class="card-body">
                                <!-- Übergabe-Liste Content -->
                            </div>
                        </div>
                    </div>

                    <!-- FÜHRERSCHEIN Section -->
                    <div class="cards">
                        <div class="card-header" id="headingSix" style="background:transparent !important;">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                <h2 class="primary text-bold-700"> FÜHRERSCHEIN</h2>
                                </button>
                            </h5>
                        </div>
                        <div id="collapseSix" class="collapse" aria-labelledby="headingSix" data-parent="#accordion">
                            <div class="card-body">
                                <!-- Führerschein Content -->
                            </div>
                        </div>
                    </div>

                    <!-- BEKLEIDUNG Section -->
                    <div class="cards">
                        <div class="card-header" id="headingSeven" style="background:transparent !important;">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                    <h2 class="primary text-bold-700">BEKLEIDUNG</h2>
                                </button>
                            </h5>
                        </div>
                        <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven" data-parent="#accordion">
                            <div class="card-body">
                                <!-- Bekleidung Content -->
                            </div>
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
<!-- BEGIN: Page Vendor JS-->
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{ asset('app-assets/js/core/app-menu.js')}}"></script>
<script src="{{ asset('app-assets/js/core/app.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/components.js')}}"></script>
<!-- END: Theme JS-->

<!-- BEGIN: Page JS-->
<script src="{{ asset('app-assets/js/scripts/pages/account-setting.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/cards/card-statistics.js') }}"></script>

<script>
$(document).ready(function() {
    $('#branch').select2();
    $('#supervisor').select2();
    $('#language').select2();
    $('#place_birth').select2();
    $('#nationality').select2();
});
</script>

<script>
var i = 0;

function loadPositions(departmentId, $positionsSelect) {
    $.ajax({
        url: '/get-positions/' + departmentId,
        type: 'GET',
        success: function(data) {
            $positionsSelect.empty();
            $.each(data, function(key, value) {
                $positionsSelect.append('<option value="' + value.id + '">' + value.position +
                    '</option>');
            });
            $positionsSelect.select2(); // Reinitialize select2
        }
    });
}

$(document).ready(function() {
    // Initialize select2 for the first row
    $('#department').select2();
    $('#position').select2();

    // Add new row
    $('#add_record').click(function() {
        ++i;
        $('#record_table').append(
            '<div class="col-12 d-flex mb-1 original-record" style="background: #eeeeee; padding: 0;">' +
            '<div class="col-5">' +
            '<label for="Department">Abteilung</label>' +
            '<select class="form-control department-select" id="department-' + i +
            '" name="department[' + i + ']" style="width:100% !important;">' +
            '<option disabled selected>Abteilung auswählen</option>' +
            '@foreach ($departments as $dept)' +
            '<option value="{{ $dept->id }}">{{ $dept->department_name }}</option>' +
            '@endforeach' +
            '</select>' +
            '</div>' +
            '<div class="col-5">' +
            '<div class="col-lg-2 col-md-4 col-sm-12">' +
            '<label for="languageselect2">Position</label>' +
            '<select class="form-control position-select" id="position-' + i + '" name="position[' +
            i + '][]" multiple="multiple" style="width: 100%"></select>' +
            '</div>' +
            '</div>' +
            '<div class="col-2">' +
            '<div class="col-lg-2 col-md-4 col-sm-12">' +
            '<label for="languageselect2"></label>' +
            '<button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mt-2 waves-effect waves-light remove_record"><i class="feather icon-minus-square"></i></button>' +
            '</div>' +
            '</div>' +
            '</div>'
        );

        // Initialize select2 for the new department and position selects
        $('#department-' + i).select2();
        $('#position-' + i).select2();

        // Bind change event to the new department select
        $('#department-' + i).change(function() {
            var departmentId = $(this).val();
            var $positionsSelect = $('#position-' + i);
            loadPositions(departmentId, $positionsSelect);
        });
    });

    // Delete row
    $(document).on('click', '.remove_record', function() {
        $(this).closest('.col-12').remove();
    });

    // Initial binding for the first row
    $('#department').change(function() {
        var departmentId = $(this).val();
        var $positionsSelect = $('#position');
        loadPositions(departmentId, $positionsSelect);
    });
});
</script>

<script>
$(document).ready(function() {
    // Save language
    $('#save-language-button').click(function(e) {
        e.preventDefault();
        var language = $('input[name="language"]').val();

        $.ajax({
            url: '{{ route("save.language") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                language: language
            },
            success: function(response) {
                toastr.success("Language saved successfully");
                $('#new_lang').modal('hide');
                loadLanguages();
            },
            error: function(response) {
                toastr.error("Error: Language not saved");
            }
        });
    });

    // Load languages
    function loadLanguages() {
        $.ajax({
            url: '{{ route("load.languages") }}',
            type: 'GET',
            success: function(response) {
                var select = $('#language');
                select.empty(); // Clear existing options
                $.each(response, function(index, language) {
                    select.append('<option value="' + language.id + '">' + language
                        .language + '</option>');
                });
            },
            error: function(response) {
                toastr.error("Error: Languages could not be loaded");
            }
        });
    }

    // Call loadLanguages on page load
    loadLanguages();
});
</script>

<script>
var i = 0;
$('#add_qualification').click(function() {
    ++i;
    $('#qualification_table').append(
        '<tr><input type="hidden" name="emp_id" value="1"> <td><input type="text" class="form-control" placeholder="Degree" name="qual[' +
        i + '][degree]"></td><td><input type="text" class="form-control" placeholder="Major" name="qual[' +
        i +
        '][major]"></td><td><input type="text" class="form-control" placeholder="Institution" name="qual[' +
        i +
        '][institution]"></td><td><input type="date" class="form-control" placeholder="Year" name="qual[' +
        i + '][year]"></td><td><input type="text" class="form-control" placeholder="Grade" name="qual[' +
        i +
        '][grade]"></td><td><button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" id="remove_qualification"><i class="feather icon-minus-square"></i></button></td>'
        );
});

$(document).on('click', '#remove_qualification', function() {
    $(this).parents('tr').remove();
})
</script>

<script>
var i = 0;
$('#add_address').click(function() {
    ++i;
    $('#address_table').append(
        '       <tr><input type="hidden" name="add[0][emp_id]" value="1" ><td><input type="text" class="form-control required" placeholder="Address Title" name="add[0][title]"></td><td><input type="text" class="form-control required" placeholder="Street" name="add[0][street]"></td><td><input type="text" class="form-control required" placeholder="Apartment" name="add[0][apartment]"></td><td><input type="text" class="form-control required" placeholder="Postcode" name="add[0][postcode]"></td><td><input type="text" class="form-control" placeholder="City" name="add[0][city]"></td><td><button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" id="add_remove"><i class="feather icon-minus-square"></i></button></td></tr>'
    );
});

$(document).on('click', '#add_remove', function() {
    $(this).parents('tr').remove();
})
</script>

<script>
$(document).ready(function() {
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
var hasUploadedImage = false;

function changePicture() {
    if (!hasUploadedImage) {
        var dropdown = document.getElementById("gender");
        var selectedValue;

        if (dropdown.value == 'Male') {
            selectedValue = '{{ asset('images/gender/male.png') }}';
        } else {
            selectedValue = '{{ asset('images/gender/female.png') }}';
        }

        var picture = document.getElementById("picture");

        // Update the source of the image
        picture.src = selectedValue;
        document.getElementById("removeButton").style.display = 'none'; // Hide the delete button
    }
}

function previewImage() {
    var file = document.getElementById("upload").files[0];
    var reader = new FileReader();
    
    reader.onloadend = function () {
        var picture = document.getElementById("picture");
        picture.src = reader.result;
        hasUploadedImage = true;  // Indicate that an image has been uploaded
        document.getElementById("removeButton").style.display = 'block'; // Show the delete button
    }

    if (file) {
        reader.readAsDataURL(file);
    } else {
        picture.src = "{{ asset('images/gender/male.png') }}";
        document.getElementById("removeButton").style.display = 'none'; // Hide the delete button
    }
}

function removePicture() {
    var picture = document.getElementById("picture");
    var dropdown = document.getElementById("gender");
    hasUploadedImage = false;

    // Clear the file input
    document.getElementById("upload").value = '';

    if (dropdown.value == 'Male') {
        picture.src = '{{ asset('images/gender/male.png') }}';
    } else {
        picture.src = '{{ asset('images/gender/female.png') }}';
    }
    document.getElementById("removeButton").style.display = 'none'; // Hide the delete button
}
</script>

<!-- Age Calculation  -->
<script>
function calculateAge() {
    var birthDate = document.getElementById("dob").value;
    console.log(birthDate);
    if (birthDate) {
        var today = new Date();
        var birthDateObj = new Date(birthDate);
        var age = today.getFullYear() - birthDateObj.getFullYear();
        var monthDiff = today.getMonth() - birthDateObj.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDateObj.getDate())) {
            age--;
        }
        document.getElementById("age").value = age;
    } else {
        document.getElementById("age").value = "";
    }
}

function calculateBirthDate() {
    var age = document.getElementById("age").value;
    if (age) {
        var today = new Date();
        var birthYear = today.getFullYear() - age;
        var birthDateObj = new Date(birthYear, today.getMonth(), today.getDate());
        var monthDiff = today.getMonth() - birthDateObj.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDateObj.getDate())) {
            birthYear--;
            birthDateObj = new Date(birthYear, today.getMonth(), today.getDate());
        }
        var birthDateString = birthDateObj.toISOString().split('T')[0];
        document.getElementById("dob").value = birthDateString;
    } else {
        document.getElementById("dob").value = "";
    }
}
</script>

@endsection
