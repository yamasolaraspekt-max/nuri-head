@extends('admin.layouts.app')

@section('title') Employee Profile @endsection
 
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
                        <h2 class="content-header-title float-left mb-0">MITARBEITERPROFIL</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">HOME</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ url('/emp') }}">MITARBEITER</a>
                                </li>
                                <li class="breadcrumb-item active">PROFIL</li>
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
                            <h4 class="float-right mb-2 ">
                                    <a href="{{url('employee_cv/'.$data->id)}}" type="button" class="btn btn-primary mr-1 mb-1 waves-effect waves-light" >
                                        <i class="feather icon-printer"></i> Labenslauf
                                    </a>  
                              </h4> 
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
                                            <div class="form-group row">
                                                <label for="lastname" class="col-sm-2 col-form-label">Vorname</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="lastname" name="lastname" value="{{ $data->lastname }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="name" class="col-sm-2 col-form-label">Name</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="name" name="name" value="{{ $data->name }}" readonly>
                                                </div>
                                            </div>
                                         
                                          
                                            <div class="form-group row">
                                                <label for="telHandy" class="col-sm-2 col-form-label">Tel./Handy</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="telHandy" name="telHandy" value="{{ $data->phone }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="email" class="col-sm-2 col-form-label">E-Mail</label>
                                                <div class="col-sm-10">
                                                    <input type="email" class="form-control" id="email" name="email" value="{{ $data->email }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="dob" class="col-sm-2 col-form-label">Geburtsdatum</label>
                                                <div class="col-sm-10">
                                                    <input type="date" class="form-control" id="dob" name="dob" value="{{ $data->dob }}" onchange="calculateAge()" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="age" class="col-sm-2 col-form-label">Alter</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="age" value=" " oninput="calculateBirthDate()" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-12 col-sm-12 text-center">
                                            @if($data->gender=="Male" && $data->image==Null)
                                            <img src="{{ asset('images/gender/male.png') }}" class="rounded-circle" id="picture" alt="profile image" height="200" width="200">
                                            @elseif($data->gender=="Female" && $data->image==Null)
                                            <img src="{{ asset('images/gender/Female.png') }}" class="rounded-circle" id="picture" alt="profile image" height="200" width="200">

                                            @else
                                            <img src="{{ asset('images/employee/'.$data->image) }}" class="rounded-circle" id="picture" alt="profile image" height="200" width="200"> 
                                            @endif
                                          
                                        </div>
                                      
                                    </div>

                                    <!-- Additional Personal Information Fields -->
                                    <div class="row mt-4">
                                        <div class="col-lg-6 col-md-12 col-sm-12">
                                            <div class="form-group row">
                                                <label for="lastname" class="col-sm-2 col-form-label">Geburtsort</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="lastname" name="lastname" value="{{ $data->country }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="name" class="col-sm-2 col-form-label">RV-Nr.</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="name" name="name" value="{{ $data->pension_no }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="street" class="col-sm-2 col-form-label">Steuer ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control"  readonly value="{{ $data->tax_id }}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="tax_class" class="col-sm-2 col-form-label">Steuer-Klasse</label>
                                                 <div class="col-sm-10">
                                                    <input type="text" class="form-control" readonly value="{{$data->class}}" data-toggle="popover" data-content="{{$data->remark}}" data-trigger="hover" data-origintal-title="{{$data->class}} - {{$data->tax}}"> 
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="kids" class="col-sm-2 col-form-label">Kinder</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control"  readonly @if($data->kids=="Yes") value="Ja" @else value="Nein" @endif>
                                                     
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="religion" class="col-sm-2 col-form-label">Konfession</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control"  readonly value="{{ $data->religion }}">
                                                    
                                                </div>
                                            </div>
                                          
                                            <div class="form-group row">
                                                <label for="language" class="col-sm-2 col-form-label">Sprachen</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" readonly value="@foreach ($language as $lang){{ $lang->language }}@if (!$loop->last), @endif @endforeach">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="health_insurance" class="col-sm-2 col-form-label">Krankenkasse</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" readonly value="@if($data->health_insurance) {{$data->health_insurance}} @else Fehlende Information @endif">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="bank_name" class="col-sm-2 col-form-label">Bank</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="bank_name" name="bank_name" value="@if($data->bank_name) {{$data->bank}} @else Fehlende Information @endif" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="iban" class="col-sm-2 col-form-label">IBAN</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="iban" name="iban" value="@if($data->iban) {{$data->iban}} @else Fehlende Information @endif" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-12 col-sm-12">
                                            <div class="default-collapse collapse-bordered">
                                                <div class="cards collapse-header">
                                                    <div id="headingCollapse1" class="card-header" style="background:transparent" data-toggle="collapse" role="button" data-target="#collapse1" aria-expanded="false" aria-controls="collapse1">
                                                        <span class="lead collapse-title">
                                                            bei EQ Maßnahmen: <i class="feather icon-chevron-down"></i>
                                                        </span>
                                                    </div>
                                                    <div id="collapse1" role="tabpanel" aria-labelledby="headingCollapse1" class="collapse">
                                                        <div class="card-content">
                                                            <div class="card-body">
                                                                <div class="form-group row">
                                                                    <label for="resident_permit" class="col-sm-3 col-form-label">Aufenthaltstitel</label>
                                                                    <div class="col-sm-9">
                                                                        <input type="text" class="form-control"readonly value="@if($data->resident_permit) @if($data->resident_permit=='Yes') Ja @else Nein @endif @else Fehlende Information @endif">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label for="work_permit" class="col-sm-3 col-form-label">Arbeitsberechtigung</label>
                                                                    <div class="col-sm-9">
                                                                        <input type="text" class="form-control" readonly value="@if($data->work_permit) @if($data->work_permit=='Yes') Ja @else Nein @endif @else Fehlende Information @endif">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label for="salary_per_hour" class="col-sm-3 col-form-label">Anzahl Std.</label>
                                                                    <div class="col-sm-9">
                                                                        <input type="text" class="form-control" readonly value="@if($data->salary_per_hour)  &euro; {{ number_format($data->salary_per_hour, 2, ',', '.') }} @else Fehlende Information @endif">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label for="contract_end" class="col-sm-3 col-form-label">Befristung bis</label>
                                                                    <div class="col-sm-9">
                                                                        <input type="text" class="form-control" readonly value="@if($data->contract_date) {{$data->contract_date}} @else Fehlende Information @endif">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label for="branch" class="col-sm-3 col-form-label">zuständige Behörde</label>
                                                                    <div class="col-sm-9">
                                                                        <input type="text" class="form-control" readonly value="@if($data->branch) {{$data->branch}} @else Fehlende Information @endif">
                                                                       
                                                                    </div>
                                                                </div>
                                                                @php
                                                                if ($data->supervisor) {
                                                                    $supervisor = DB::table('employees')
                                                                        ->select('name', 'lastname')
                                                                        ->where('id', $data->supervisor)
                                                                        ->first();

                                                                    if ($supervisor) {
                                                                        $supervisor = $supervisor->name . ' ' . $supervisor->lastname;
                                                                    } else {
                                                                        $supervisor = 'Fehlende Information';
                                                                    }
                                                                } else {
                                                                    $supervisor = 'Fehlende Information';
                                                                }
                                                                @endphp
                                                                <div class="form-group row">
                                                                    <label for="contact_person" class="col-sm-3 col-form-label">Ansprechpartner</label>
                                                                    <div class="col-sm-9">
                                                                        <input type="text" class="form-control" readonly value="{{ $supervisor }}">
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                                <h2 class="primary text-bold-700">QUALIFIKATION</h2>
                                </button>
                            </h5>
                        </div>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0" id="a">
                                        <thead>
                                            <tr>
                                                <th>Degree/Fakultät</th>
                                                <th>Wesentlich</th>
                                                <th>Institution</th>
                                                <th>Startjahr</th>
                                                <th>Abschlussdatum</th>
                                                <th>Grade</th> 
                                            </tr>
                                        </thead>
                                         <tbody>
                                            @foreach ($qualifications as $qualification)
                                            <tr>
                                                
                                                <td>{{ $qualification->degree }}</td>
                                                <td>{{ $qualification->major }}</td>
                                                <td>{{ $qualification->institution }}</td>
                                                <td>{{ $qualification->q_start_year }}</td>
                                                <td>{{ $qualification->q_end_year }}</td>
                                                <td>{{ $qualification->grade }}</td> 
                                            </tr>
                                            
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                  <div class="divider divider-primary">
                                    <div class="divider-text">Kurse & Workshops</div>
                                </div>
                                    <div class="table-responsive">
                                        <table class="table" >
                                            <thead>
                                                <tr>
                                                    <th>Kurs</th>
                                                    <th>Major</th>
                                                    <th>Institution</th>
                                                    <th>Jahr</th>
                                                    <th>Fähigkeiten</th>
                                                    <th>Beschreibung</th> 
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($feducation as $fedu)
                                                <tr>
                                                    
                                                    <td>{{ $fedu->course }}</td>
                                                    <td>{{ $fedu->major }}</td>
                                                    <td>{{ $fedu->institution }}</td>
                                                    <td>{{ $fedu->year }}</td>
                                                    <td>{{ $fedu->skill }}</td>
                                                    <td>{{ $fedu->description }}</td>
                                                
                                                </tr>
                                                
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>                                    
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
                                <div class="table-responsive">
                                    <table class="table" id="">
                                        <thead>
                                            <tr> 
                                                <th>Gewerk</th>
                                                <th>Beratung</th>
                                                <th>Planung</th>
                                                <th>Kalkulation</th>
                                                <th>Montage</th>
                                                <th>Projektierung</th>
                                                <th>Bauleitung</th> 
                                            </tr>
                                        </thead>
                                    <tbody>
                                            @foreach ($skills as $skil)
                                            <tr> 
                                                <td>{{ $skil->article_group }}</td>
                                                <td>
                                                    <div class="fonticon-wrap">
                                                    @for ($i = 1; $i <=  $skil->advice; $i++)
                                                    <i class="fa fa-star" style="color:gold"></i>
                                                    @endfor
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="fonticon-wrap">
                                                    @for ($i = 1; $i <=  $skil->plan; $i++)
                                                    <i class="fa fa-star" style="color:gold"></i>
                                                    @endfor
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="fonticon-wrap">
                                                    @for ($i = 1; $i <= $skil->calculation; $i++)
                                                    <i class="fa fa-star" style="color:gold"></i>
                                                    @endfor
                                                    </div> 
                                                </td>

                                                <td>
                                                    <div class="fonticon-wrap">
                                                    @for ($i = 1; $i <= $skil->montage; $i++)
                                                    <i class="fa fa-star" style="color:gold"></i>
                                                    @endfor
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="fonticon-wrap">
                                                    @for ($i = 1; $i <= $skil->project_planing; $i++)
                                                    <i class="fa fa-star" style="color:gold"></i>
                                                    @endfor
                                                    </div>
                                                </td>
                                                <td>
                                                <div class="fonticon-wrap">
                                                    @for ($i = 1; $i <= $skil->site_management; $i++)
                                                    <i class="fa fa-star" style="color:gold"></i>
                                                    @endfor
                                                    </div>
                                                </td> 
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
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
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Mitarbeitername</th> 
                                                <th>Bezahlt</th>
                                                <th>Genehmigt</th>
                                                <th>Datum</th>
                                                <th>Grund</th>
                                                <th>Beschreibung</th>
                                                <th>Status</th> 
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($leaves as $leave)
                                                <tr>
                                                    <td>{{ $data->name }} {{ $data->lastname }}<br>
                                                        @if($leave->leave_type=="Personal")
                                                           <div class="badge badge-warning mr-1 mb-1">
                                                                Persönlicher
                                                            </div>
                                                        @else
                                                         <div class="badge badge-danger mr-1 mb-1">
                                                                 Krankheits
                                                            </div>
                                                        @endif
                                                    </td> 
                                                    <td>
                                                        @if($leave->paid == "Yes")
                                                            <div class="badge badge-success mr-1 mb-1">
                                                                <i class="feather icon-check-square"></i> Ja
                                                            </div>
                                                        @else
                                                            <div class="badge badge-danger mr-1 mb-1">
                                                                <i class="fa fa-times-circle"></i> Nein
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($leave->approved == "Yes")
                                                            <div class="badge badge-success mr-1 mb-1">
                                                                <i class="feather icon-check-square"></i> Ja
                                                            </div>
                                                        @else
                                                            <div class="badge badge-success mr-1 mb-1">
                                                                <i class="fa fa-times-circle"></i> Nein
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>{{ $leave->start_date }} bis {{ $leave->end_date }}
                                                        <br>
                                                        <div class="badge badge-danger mr-1 mb-1">
                                                            <i class="fa fa-times-circle"></i> Verbleibende Tage: {{ $leave->remaining_day }}
                                                        </div>
                                                    </td>
                                                
                                                    <!-- Reason Modal: Start -->
                                                    <td>
                                                        {{ $leave->reason }}
                                                    </td>
                                                    <!-- Reason Modal: End -->

                                                    <!-- Description Modal: Start -->
                                                    <td>
                                                        <button type="button" class="btn btn-icon rounded-circle btn-outline-warning mr-1 mb-1" data-toggle="modal" data-target="#description{{ $leave->id }}"><i class="fa fa-expand"></i></button>
                                                        <div class="modal fade text-left" id="description{{ $leave->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title" id="myModalLabel1">{{ $data->name }} {{ $data->lastname }}: Beschreibung</h4>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <fieldset>
                                                                            <p>{{ $leave->description }}</p>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" data-dismiss="modal" class="btn btn-primary">OK</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="badge badge-danger mr-1 mb-1">
                                                            <i class="fa fa-times-circle"></i>  {{ $leave->status }}
                                                        </div> 
                                                    </td>
                                                    <!-- Description Modal: End -->  
                                                 </tr>
                                             @endforeach
                                        </tbody>
                                    </table>
                                </div>
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
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th Scope="col">ID</th>
                                                <th scope="col">Element</th>
                                                <th scope="col">Übergaben</th>
                                                <th scope="col">Menge</th>
                                                <th scope="col">Bild</th>
                                                <th scope="col">Status</th> 
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($handover as $item)
                                            <tr>
                                                <th scope="row">{{$item->id}}</th>
                                                <td>{{ $item->item }} 
                                                </br>
                                                    <div class="badge badge-square badge-primary mr-1 mb-1">
                                                        <i class="feather icon-hashtag"></i>
                                                        <span>Seriennummer: {{ $item->serial_no }}</span>
                                                    </div>
                                                </br>

                                                    <div class="badge badge-square badge-primary mr-1 mb-1">
                                                        <i class="feather icon-facebook"></i>
                                                        <span>Artikelnummer: {{ $item->article_no }}</span>
                                                    </div>

                                                
                                                </td>
                                                <td class="p-1">
                                                    <ul class="list-unstyled users-list m-0  d-flex align-items-center">
                                                        <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Übergabe von: {{ $item->hand_from_name }}  {{ $item->hand_from_lastname }}" class="avatar pull-up">
                                                            <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$item->hand_from_image) }}" alt="Avatar" height="30" width="30">
                                                        </li>
                                                        <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Übergeben: {{ $item->hand_to_name }} {{ $item->hand_to_lastname }} " class="avatar pull-up">
                                                            <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$item->hand_to_image) }}" alt="Avatar" height="60" width="60">
                                                        </li>
                                                    
                                                        @foreach ($employee as $hand_by )
                                                        @if($hand_by->id==$item->handover_by)
                                                        <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Übergabe durch: {{ $hand_by->name }} {{ $hand_by->lastname }}" class="avatar pull-up">
                                                            <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$hand_by->image) }}" alt="Avatar" height="30" width="30">
                                                        </li>
                                                        @endif
                                                        @endforeach
                                                    </ul>
                                                </td>
                                        
                                                <td>{{ $item->quantity }}</td>
                                                <td>
                                                    <!-- Image Modal -->
                                                    <a type="button" class="btn btn-icon btn-icon  mr-1 mb-1" data-toggle="modal" data-target="#image{{$item->id}}">
                                                    <div class="avatar mr-1 ">
                                                        <img src="{{ asset('images/asset/'.$item->image) }}" alt="avtar img holder" height="32" width="32">
                                                    </div>
                                                    </a>

                                                        <!-- Modal -->
                                                        <div class="modal fade text-left" id="image{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        {{ $item->item }} | {{ $item->serial_no }}
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body" style="text-align: center;">
                                                                        <img src="{{ asset('images/asset/'.$item->image) }}" alt="avtar img holder" height="200" width="200">
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                    {!! $item->purpose !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- End Image Modal -->
                                                
                                                </td>
                                                <td>{{ $item->status }}</td>   
                                            </tr>
                                            @endforeach

                                        </tbody>
                                    </table> 
                                </div>
                                
                            </div>
                        </div>
                    </div>

                    <!-- FÜHRERSCHEIN Section -->
                    <div class="cards">
                        <div class="card-header" id="headingSix" style="background:transparent !important;">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                <h2 class="primary text-bold-700">FÜHRERSCHEIN</h2>
                                </button>
                            </h5>
                        </div>
                        <div id="collapseSix" class="collapse" aria-labelledby="headingSix" data-parent="#accordion">
                            <div class="card-body">
                                <div class="table-responsive">
                                      <table class="table" id="">
                                        <thead>
                                            <tr> 
                                                <th>Lizenzgrad</th>
                                                <th>Amtliches Kennzeichen</th>
                                                <th>Verfallsdatum</th>
                                                <th>Lizenfoto</th>
                                                <th>Status</th> 
                                            </tr>
                                        </thead>
                                    <tbody>
                                            @foreach ($license as $lice)
                                            <tr>  
                                                <td>
                                                    @foreach ($employee_license as $license )
                                                    <div class="badge badge-success mr-1 mb-1">
                                                    {{ $license->initials }}-{{ $license->de_name }}
                                                    </div>
                                                    @endforeach 
                                                </td> 
                                                <td>{{ $lice->license_no }}</td>
                                            
                                                <td>{{ $lice->expiry_date }}
                                                    <br>
                                                    @if($lice->expiry_date== \Carbon\Carbon::parse(now())->isoFormat('DD.MM.YYY'))
                                                    <div class="badge badge-danger mr-1 mb-1">
                                                        <i class="fa fa-times-circle"></i >Die Lizenz ist abgelaufen
                                                    </div>
                                                    @endif
                                                </td>
                                                <!-- Reason Modal:Start -->
                                                <td>
                                                <button type="button" class="btn btn-icon rounded-circle btn-outline-warning mr-1 mb-1" data-toggle="modal" data-target="#reason{{$lice->id}}"><i class="fa fa-expand"></i></button>
                                                    <!-- Qualification Edit Model: Start -->
                                                    <div class="modal fade text-left" id="reason{{$lice->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title" id="myModalLabel1">{{ $data->name }} {{ $data->lastname }}: Grund</h4>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <img class="card-img-bottom img-fluid" src="{{ asset('images/employee/license/'.$lice->image) }}" alt="{{ $data->name }}">
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" data-dismiss="modal" class="btn btn-primary">OK</button>
                                                                </div> 
                                                            </div> 
                                                        </div> 
                                                    </div>            
                                                </td>
                                                <!-- Reason Modal: End -->

                                                <td>
                                                    {{ $lice->status }}<br>
                                                    @if($lice->status!=Null)
                                                    <div class="badge badge-danger mr-1 mb-1">
                                                        <i class="fa fa-times-circle"></i > {{ $lice->duration }}
                                                    </div>
                                                    <div class="badge badge-danger mr-1 mb-1">
                                                    {{ $lice->suspend_date }}
                                                    </div>
                                                    @else
                                                    <div class="badge badge-success mr-1 mb-1">
                                                        <i class="fa fa-check"></i >Aktiv
                                                    </div>
                                                    @endif
                                                </td> 
                
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
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
                                <table class="table" id="a">
                                    <thead>
                                        <tr> 
                                            <th>Kleidung Typ</th>
                                            <th>Größe</th> 
                                        </tr>
                                    </thead>
                                <tbody>
                                        @foreach ($cloths as $cloth)
                                        <tr> 
                                            <td>{{ $cloth->type }}</td>
                                            <td>{{ $cloth->size }}</td>
                                                                                                                         
                                            
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
</div>
<!-- END: Content-->

@endsection

 