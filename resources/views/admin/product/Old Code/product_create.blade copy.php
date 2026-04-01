@extends('admin.layouts.app')

@section('title') PRODUCT DETAILS @endsection
@section('style')
<!-- Include stylesheet -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
@endsection

<style>
    .img-flag{
        width : 20px !important;
    }
    
    .hidden {
        display: none; 
    }


</style>
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
                            <h2 class="content-header-title float-left mb-0">PRODUKTS</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">HOME</a>
                                    </li>
                                      <li class="breadcrumb-item"><a href="{{ url('/product') }}">PRODUKTDETAILS</a>
                                    </li>
                                     <li class="breadcrumb-item"><a href=" ">NUE</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                         @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                <!-- Basic Horizontal form layout section start -->
                <section id="basic-horizontal-layouts">
                    <div class="row match-height">
                        <form id="productForm" class="form form-horizontal" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                 <div class="col-md-8 col-12">
                                    <div class="cards">
                                        <div class="card-content"> 
                                                <div class="card-body"> 
                                                        <div class="form-body">
                                                            <div class="row"> 
                                                                <div class="col-12">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-4">
                                                                            <span>Artikel Nummer</span>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <input type="text" id="article_no" class="form-control" value="{{old('article_no')}}" name="article_no" required>
                                                                            <span class="text-danger" id="article_no-error"></span>

                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-12">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-4">
                                                                            <span>EAN Nummer</span>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <input type="text" id="ean" class="form-control" value="{{old('ean')}}" name="ean" required>
                                                                                <span class="text-danger" id="ean-error"></span>

                                                                        </div>
                                                                    </div>
                                                                </div> 
                                                                <div class="col-12">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-4 col-md-4 col-12">
                                                                            <span>Hersteller</span>
                                                                        </div>
                                                                        <div class="col-md-8 col-md-8 col-12">
                                                                            <div class="row">
                                                                                <div class="col-md-10">
                                                                                    <select id='brand' name="brand_id" style="width:100%" required>
                                                                                        <option selected disabled data-image="{{ asset('logo/logo.png') }}">Bitte wählen Sie die Hersteller aus</option>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col-md-2">
                                                                                    <button type="button" class="btn btn-icon btn-icon btn-outline-primary  waves-effect waves-light " data-toggle="modal" data-target="#new_brand"><i class="feather icon-plus"></i>Neue Hersteller</button>
                                                                                    <!-- Modal Dialog: start -->
                                                                                
                                                                                </div> 
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-12">
                                                                    <div class="form-group row">
                                                                        <div class="col-4">
                                                                            <span>Artikel Name</span>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <input type="text" id="product" class="form-control" value="{{old('product')}}" name="product" required>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-12">
                                                                    <div class="form-group row">
                                                                        <div class="col-4">
                                                                            <span>Typ Bezeischnung</span>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <input type="text" id="model" class="form-control" value="{{old('model')}}" name="model" required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12"><hr></div>
 
                                                                <div class="col-12">
                                                                    <div class="form-group row">
                                                                        <div class="col-4">
                                                                            <span>Eigenschaft</span>
                                                                        </div>
                                                                        <div class="col-8">
                                                                            <fieldset class="form-group"> 
                                                                                <select id="category" name="category" class="form-contorl" style="width:100%;" required onchange="toggleRoofTypeSection()">
                                                                                    <option value="Produkt" selected>Produkt</option> 
                                                                                    <option value="Dachziegel" >Dachziegel</option>
                                                                                    <option value="Ziegel" >Ziegel</option>
                                                                                    <option value="Fenster" >Fenster</option>
                                                                                    <option value="Tür" >Tür</option>
                                                                                </select>
                                                                
                                                                                
                                                                            </fieldset>
                                                                        </div>
                                                                    </div>
                                                                </div>


                                                                <div class="col-12 hidden" id="roof_type_section" >
                                                                    <div class="form-group row">
                                                                        <div class="col-4">
                                                                            <span>Dachtyp</span>
                                                                        </div>
                                                                        <div class="col-8">
                                                                            <fieldset class="form-group"> 
                                                                                <select id="roof_type" name="roof_type" class="form-contorl" style="width:100%;" required>
                                                                                    <option value="Satteldach" selected>Satteldach</option> 
                                                                                    <option value="Flachdach">Flachdach</option>
                                                                                    <option value="Garage">Garage</option>
                                                                                    <option value="Carport">Carport</option>
                                                                                    <option value="Pultdach">Pultdach</option>
                                                                                    <option value="Kombiniertes Pultdach">Kombiniertes Pultdach</option>
                                                                                    <option value="Mansarddach">Mansarddach</option>
                                                                                    <option value="Walmdach">Walmdach</option>
                                                                                    <option value="Krüppelwalmdach">Krüppelwalmdach</option>
                                                                                    <option value="Zeltdach">Zeltdach</option> 
                                                                                </select> 
                                                                            </fieldset>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                        
                                                                <div class="col-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-4">
                                                                                <span>Artikel-Gruppe</span>
                                                                            </div>
                                                                            <div class="col-8">
                                                                                <fieldset class="form-group">
                                                                                    @if(count($article_groups))
                                                                                    <select id="article_group" name="article_group" style="width:100%" required>
                                                                                        <option selected disabled>Bitte wählen Sie die Article-Gruppe aus</option>
                                                                                        @foreach ($article_groups as $art_group)
                                                                                        <option value="{{ $art_group->id }}">{{ $art_group->article_group }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                    @else
                                                                                    <a type="button" class="btn btn-primary" href="{{ route('article_group.info') }}">Neu Artikel-Gruppe</a>
                                                                                    @endif
                                                                                </fieldset>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-4">
                                                                                <span>Artikel Kategorie</span>
                                                                            </div>
                                                                            <div class="col-8">
                                                                                <fieldset class="form-group">
                                                                                    <select id="sub_article" name="sub_article" style="width:100%" required>
                                                                                        <option selected disabled>Bitte wählen Sie die Sub Article aus</option>
                                                                                        <!-- Options will be dynamically added here by jQuery -->
                                                                                    </select>
                                                                                </fieldset>
                                                                            </div>
                                                                        </div>
                                                                    </div>
            

                                                                <div class="col-12">
                                                                    <div class="form-group row">
                                                                        <div class="col-4">
                                                                            <span>Farbe</span>
                                                                        </div>
                                                                        <div class="col-8">
                                                                            <fieldset class="form-group">
                                                                            <select class="select2-customize-result form-control" name="color" id="color" placeholder="Select Color" required>
                                                                                <option value="nicht ausgewählt  ">Nicht ausgewählt  </option>
                                                                                <option value="Weiß">Weiß</option>
                                                                                <option value="Schwarz">Schwarz</option>
                                                                                <option value="Grau">Grau</option>
                                                                                <option value="Braun">Braun</option>
                                                                                <option value="Beige">Beige</option>
                                                                                <option value="Gold">Gold</option>
                                                                                <option value="Blau">Blau</option>
                                                                                <option value="Gelb">Gelb</option>
                                                                                <option value="Lila">Lila</option>
                                                                                <option value="Silver">Silver</option>
                                                                        
                                                                            </select>
                                                                            </fieldset>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            
                                                            
                                                                <div class="col-12">
                                                                    <div class="form-group row">
                                                                        <div class="col-4">
                                                                            <span>Mengeneinheit:</span>
                                                                        </div>
                                                                        <div class="col-8">
                                                                            <fieldset class="form-group">
                                                                            
                                                                                    <fieldset class="form-group">
                                                                                        @if(count($measures))
                                                                                        <select id='measure_unit' name="measure_unit" style="width:100%" required>
                                                                                        @foreach ($measures as $measure)
                                                                                        <option value="{{ $measure->id }}" > {{ $measure->measure }}</option>
                                                                                        @endforeach
                                                                                        </select>
                                                                                        @else
                                                                                        <a type="button" class="btn btn-primary" href="{{ route('measure.info')}}">Neu Mengeneinheit</a>
                                                                                        @endif
                                                                                    </fieldset>
                                                                            </fieldset>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-12">
                                                                    <div class="form-group row">
                                                                        <div class="col-4">
                                                                            <span>Preiseinheit</span>
                                                                        </div>
                                                                        <div class="col-8">
                                                                            <input type="text" id="price_unit" class="form-control" value="{{old('price_unit')}}" name="price_unit" required>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-12">
                                                                    <div class="form-group row">
                                                                        <div class="col-4">
                                                                            <span>Packungseinheit</span>
                                                                        </div>
                                                                        <div class="col-8">
                                                                            <textarea  id="package_unit" class="form-control" value="{{old('package_unit')}}" name="package_unit" required></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>       
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="cards" style=" height:420px !important;">
                                        <div class="card-content">
                                            <div class="card-body" >  
                                                <div class="form-body">
                                                    <div class="row"> 
                                                        <div class="col-12"> 
                                                            <div class="col-12">
                                                                <div class="form-group row">
                                                                    <div class="col-4">
                                                                        <span>kurze Beschreibung </span>
                                                                    </div>
                                                                    
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <fieldset>
                                                                    <div id="editor" class="form-control"  style="height: 400px !important;">
                                                                    
                                                                    </div>
                                                                    <textarea name="editor_text" hidden id="editor_text"  style="text-align:right !important;"cols="30" rows="10"></textarea>
                        
                                                                    <div class="modal-footer">
                                                                        <button type="submit" class="btn btn-primary" id="saveProductBtn"> <i class="fa fa-save"></i> Speichern und Weiter</button> 
                                                                    </div>
                                                                </fieldset>
                                                            </div>  
                                                        </div>
                                                    </div> 
                                                </div> 
                                            </div> 
                                        </div> 
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-md-4 col-md-4 col-1">
                                            <span>Lieferant</span>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-md-10">
                                                    <fieldset class="form-group">  
                                                        <select id="distributor" name="distributor[]" style="width:100%" multiple="multiple">

                                                            <!-- Options will be populated by AJAX -->
                                                        </select>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-icon btn-icon btn-outline-primary waves-effect waves-light" data-toggle="modal" data-target="#distributors">
                                                        <i class="feather icon-plus"></i>Neue Lieferant
                                                    </button> 
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                    <div class="col-12"> 
                                        <table class="table" id="distributor_price">
                                            <thead>
                                                <tr>
                                                    <th>Lieferant</th>
                                                    <th>Art.Nr</th>
                                                    <th>UVP <code><small>Listenpreis</small></code></th>
                                                    <th>EK-Einzelpreis</th>
                                                    <th>Rabbat Gruppe</th>
                                                    <th>Rabbat-Euro</th>
                                                    <th>Rabbat %</th>
                                                    <th>Datum</th>
                                                    <th>Verfügbarkeit</th>
                                                    <th>Aktion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Initial row will be inserted here -->
                                            </tbody>
                                        </table>

                                        <!-- Plus button to add new rows -->
                                        <button id="add_price" type="button" class="btn btn-icon  btn-outline-success">
                                            <i class="feather icon-plus"></i> Neue Zeile
                                        </button>
                                    </div>

                            </div> 
                        </form>
                    </div>
            
                </section>
                <!-- // Basic Horizontal form layout section end --> 

            </div>
        </div>
    </div>
    <!-- END: Content-->

            <!-- New Company Modal: Start -->
            <div class="modal fade text-left" id="new_brand" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel1">Neue Marke hinzufügen</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="brandForm" class="form-horizontal" novalidate enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body"> 
                                <fieldset>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="name">Hersteller</label>
                                                <input type="text" class="form-control" name="name" required>
                                                <p class="text-danger" id="name-error"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="initial">Initial</label>
                                                <input type="text" class="form-control" name="initial">
                                                <p class="text-danger" id="initial-error"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="purpose">Zweckkategorie</label>
                                                <select name="purpose" class="form-control">
                                                    <option value="PHOTOVOLTAIK">PHOTOVOLTAIK</option>
                                                    <option value="BATTERIESPEICHER">BATTERIESPEICHER</option>
                                                    <option value="WÄRMEPUMPE">WÄRMEPUMPE</option>
                                                    <option value="WALLBOX">WALLBOX</option>
                                                    <option value="ELEKTRO">ELEKTRO</option>
                                                    <option value="SANITÄR">SANITÄR</option>
                                                    <option value="BAD">BAD</option>
                                                    <option value="BAUELEMENTE">BAUELEMENTE</option>
                                                    <option value="KÜCHE">KÜCHE</option>
                                                    <option value="SOLAR CARPORT">SOLAR CARPORT</option>
                                                    <option value="SOFTWARE">SOFTWARE</option>
                                                    <option value="HARDWARE">HARDWARE</option>
                                                </select>
                                                <p class="text-danger" id="purpose-error"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <table class="table" id="add_department"> 
                                                    <thead>
                                                        <tr> 
                                                            <th>Abteilung</th>
                                                            <th>Ansprechpartner</th>
                                                            <th>Position</th>
                                                            <th>Email</th>
                                                            <th>Phone</th>
                                                            <th>Festnetznummer</th>
                                                            <th>Büro</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <form method="post" > 
                                                        <tbody>
                                                            <tr> 
                                                            
                                                                <td><input type="text" class="form-control required" placeholder="Abteilung" name="brand[0][brand_department]"></td>
                                                                <td><input type="text" class="form-control required" placeholder="Gesprächspartner" name="brand[0][name]"></td>
                                                                <td><input type="text" class="form-control required" placeholder="Position" name="brand[0][position]"></td>
                                                
                                                                <td><input type="text" class="form-control required" placeholder="E-Mail" name="brand[0][email]"></td>
                                                            
                                                                <td><input type="text" class="form-control required" placeholder="Handynummer" name="brand[0][phone]"></td>
                                                                <td><input type="text" class="form-control required" placeholder="Festnetznummer" name="brand[0][home]"></td>
                                                                <td><input type="text" class="form-control required" placeholder="Büro-Telefonnummer" name="brand[0][office]"></td> 
                                                                <td>
                                                                <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" id="add_brand"><i class="feather icon-plus"></i></button>
                                                                </td>
                                                            </tr>
                                                        </tbody> 
                                                    </form>
                                                </table>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="image">Logo <code> .PNG</code></label>
                                                <input type="file" class="form-control" name="image">
                                                <p class="text-danger" id="image-error"></p>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" id="saveBrandBtn">Speichern</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div> 
            <!-- New Company Modal: End  -->

        <!-- New Delivery Modal: Start  -->
        <div class="modal fade text-left" id="distributors" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel16">Neue Liferant</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <!-- Form with no action, as we will handle the submission with AJAX -->
                    <form id="distributorForm" class="form-horizontal" novalidate enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body"> 
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Title">Unternehmen/Marke</label> 
                                        <input type="text" class="form-control" name="name" required>
                                        <p class="text-danger" id="name-error"></p>
                                    </div>
                                </div> 
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Title">Address</label> 
                                        <input type="text" class="form-control" name="address" required>
                                        <p class="text-danger" id="address-error"></p>
                                    </div>
                                </div> 
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="Title">Logo <code>.PNG</code></label> 
                                        <input type="file" class="form-control" name="image" required>
                                        <p class="text-danger" id="image-error"></p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <table class="table" id="add_distributor_department"> 
                                        <thead>
                                            <tr> 
                                                <th>Abteilung</th>
                                                <th>Ansprechpartner</th>
                                                <th>Position</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Festnetznummer</th>
                                                <th>Büro</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead> 
                                        <tbody id="distributor_department_body"> 
                                            <tr> 
                                                <td><input type="text" class="form-control" placeholder="Abteilung" name="d[0][d_department]"></td>
                                                <td><input type="text" class="form-control" placeholder="Gesprächspartner" name="d[0][name]"></td>
                                                <td><input type="text" class="form-control" placeholder="Position" name="d[0][position]"></td> 
                                                <td><input type="email" class="form-control" placeholder="E-Mail" name="d[0][email]"></td> 
                                                <td><input type="text" class="form-control" placeholder="Handynummer" name="d[0][phone]"></td>
                                                <td><input type="text" class="form-control" placeholder="Festnetznummer" name="d[0][home]"></td>
                                                <td><input type="text" class="form-control" placeholder="Büro-Telefonnummer" name="d[0][office]"></td> 
                                                <td>
                                                    <button type="button" class="btn btn-icon rounded-circle btn-outline-primary" id="add_distributor"><i class="feather icon-plus"></i></button>
                                                </td>
                                            </tr>
                                        </tbody> 
                                    </table>
                                </div>
                            </div> 
                        </div>  
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="saveDistributorBtn">Speichern</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- New Delivery Modal: End  -->

@endsection

@section('script')

<script src="{{asset('app-assets/vendors/js/editors/quill/quill.min.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
  <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>

<script src="{{ asset('js/select2.min.js') }}"></script>
<!-- Quill Other Editor -->
 
<script>
    $(document).ready(function(){
           
        var toolbarOptions = [
                ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                ['blockquote', 'code-block'],

                [{ 'header': 1 }, { 'header': 2 }],               // custom button values
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
                [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
                [{ 'direction': 'rtl' }],                         // text direction

                [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

                [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
                [{ 'font': [] }],
                [{ 'align': [] }],
                ['link', 'image', 'video', 'formula'],
        
                ['clean']                                         // remove formatting button
                ];

    var quill = new Quill('#editor', {
    modules: {
        toolbar: toolbarOptions
    },
    theme: 'snow'
    });

        quill.on('text-change', function(delta, oldDelta, source) {
                        if (source == 'api') {
                            console.log("An API call triggered this change.");
                        } else if (source == 'user') {
                            $('#editor_text').text($(".ql-editor").html())
                            console.log("A user action triggered this change.");
                        }
            });

    });

        </script>




<!-- loading the brand and compnay: start  -->
<script type="text/javascript">
    $(document).ready(function () {
        const defaultOption = `<option disabled selected data-image="{{ asset('logo/logo.png') }}">Bitte wählen Sie die Hersteller aus</option>`;
        const brandSelect = $('#brand');

        brandSelect.empty().append(defaultOption);

        $.ajax({
            url: "{{ route('product.create.get.brand') }}",
            method: 'GET',
            success: function (response) {
                if (Array.isArray(response) && response.length > 0) {
                    response.forEach(brand => {
                        if (brand.status === "Published") {
                            const option = `<option value="${brand.id}" data-image="{{ asset('images/brand') }}/${brand.image}">
                                                ${brand.name}
                                            </option>`;
                            brandSelect.append(option);
                        }
                    });
                }
            },
            error: function (xhr) {
                console.error('Fehler beim Laden der Hersteller:', xhr.responseText);
            }
        });
    });
    </script>

<!-- loading the brand and compnay: End  -->
 <!-- Brand address: start  -->
<script>
        var i = 0;
        $('#add_brand').click(function(){
            ++i;
            $('#add_department').append(
                '       <tr> <td><input type="text" class="form-control required" placeholder="Abteilung" name="brand['+i+'][brand_department]"></td><td><input type="text" class="form-control required" placeholder="Gesprächspartner" name="brand['+i+'][name]"></td><td><input type="text" class="form-control required" placeholder="Position" name="brand['+i+'][position]"></td><td><input type="text" class="form-control required" placeholder="E-Mail" name="brand['+i+'][email]"></td><td><input type="text" class="form-control required" placeholder="Handynummer" name="brand['+i+'][phone]"></td><td><input type="text" class="form-control required" placeholder="Festnetznummer" name="brand['+i+'][home]"></td><td><input type="text" class="form-control required" placeholder="Büro-Telefonenummer" name="brand['+i+'][office]"></td><td><button type="button"  class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" id="add_remove"><i class="feather icon-minus-square"></i></button></td></tr> ' 
                );
        });

        $(document).on('click', '#add_remove', function(){
            $(this).parents('tr').remove();
        })

    </script>
 <!-- Brand address: end  -->

<!-- saving new compnay: start  -->
 
<script>
    $(document).ready(function () {
        // Handle the form submission when the button is clicked
        $('#saveBrandBtn').on('click', function (e) {
            e.preventDefault(); // Prevent default button behavior

            // Create a FormData object for handling file uploads
            var formData = new FormData($('#brandForm')[0]);

            // Add CSRF token to the FormData
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
             // DEBUG: Log the form data to the console
                for (var pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }
            // Clear any previous error messages
            $('.text-danger').text('');

            $.ajax({
                url: "{{ route('product.store.brand') }}", // Your route to store the brand
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    toastr.success(response.save_msg); // Show a success message
                    $('#brandForm')[0].reset(); // Reset the form inside the modal
                    $('#new_brand').modal('hide'); // Hide the modal
                    updateBrandSelect(response.brand); // Update the brand select options
                },
                error: function (xhr) {
                    console.log(xhr.responseText); // Log the error response for debugging
                    toastr.error(xhr.responseText);
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        if (errors.name) {
                            $('#name-error').text(errors.name[0]);
                        }
                        if (errors.initial) {
                            $('#initial-error').text(errors.initial[0]);
                        }
                        if (errors.purpose) {
                            $('#purpose-error').text(errors.purpose[0]);
                        }
                        if (errors.image) {
                            $('#image-error').text(errors.image[0]);
                        }
                    }
                }
            });
        });

        // Function to update the brand select element dynamically
        function updateBrandSelect(brand) {
            $('#brand').append('<option value="' + brand.id + '">' + brand.name + '</option>');
        }
    });
</script> 
<!-- saving new compnay: end  -->

<!-- geting the Liferant:start  --> 
    <script type="text/javascript">
        $(document).ready(function() {
            // Fetch the distributor data when the page loads
            fetchDistributors();

            function fetchDistributors() {
                $.ajax({
                    url: "{{ route('product.get.distributor') }}", // Use the route to get the distributors
                    method: 'GET',
                    success: function(response) {
                        // Clear the existing options
                        $('#distributor').empty();

                        // Check if response contains data
                        if (response.length > 0) {
                            $.each(response, function(index, distributor) {
                                // Append new options to the select box
                                $('#distributor').append('<option value="' + distributor.id + '" data-image="{{ asset('images/distributor/') }}/' + distributor.image + '">' + distributor.name + '</option>');
                            });
                        } else {
                            $('#distributor').append('<option disabled>No distributors found</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching distributors:', error);
                    }
                });
            }
        });
    </script>
<!-- geting the Liferant:end  -->
 

<!-- saving New Distributor: Start  -->
 
 <script>
    // Add new department row
    var d = 0;
    $('#add_distributor').on('click', function(e){
        e.preventDefault();
        ++d;
        $('#distributor_department_body').append(
            '<tr> \
                <td><input type="text" class="form-control" placeholder="Abteilung" name="d['+d+'][d_department]"></td>\
                <td><input type="text" class="form-control" placeholder="Gesprächspartner" name="d['+d+'][name]"></td>\
                <td><input type="text" class="form-control" placeholder="Position" name="d['+d+'][position]"></td>\
                <td><input type="email" class="form-control" placeholder="E-Mail" name="d['+d+'][email]"></td>\
                <td><input type="text" class="form-control" placeholder="Handynummer" name="d['+d+'][phone]"></td>\
                <td><input type="text" class="form-control" placeholder="Festnetznummer" name="d['+d+'][home]"></td>\
                <td><input type="text" class="form-control" placeholder="Büro-Telefonnummer" name="d['+d+'][office]"></td>\
                <td><button type="button" class="btn btn-icon rounded-circle btn-outline-danger distributor_remove"><i class="feather icon-minus-square"></i></button></td>\
            </tr>'
        );
    });

    // Remove department row
    $(document).on('click', '.distributor_remove', function(){
        $(this).parents('tr').remove();
    });

    // AJAX submission of distributor form
    $('#saveDistributorBtn').on('click', function(e) {
        e.preventDefault(); // Prevent default form submission

        var formData = new FormData($('#distributorForm')[0]);

        // Add CSRF token
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            url: "{{ route('product.store.distributor') }}", // The route to storeDistributor method
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                toastr.success(response.save_msg);

                // Reset the form
                $('#distributorForm')[0].reset();

                // Close the modal
                $('#distributors').modal('hide');

                // Immediately add the new distributor to the select dropdown
                $('#distributor').append(
                    '<option value="' + response.distributor.id + '" data-image="{{ asset('images/distributor/') }}/' + response.distributor.image + '">' + response.distributor.name + '</option>'
                );

                // Optionally, set the newly added distributor as selected in the dropdown
                $('#distributor').val(response.distributor.id).trigger('change');
            },
            error: function(xhr) {
                // Handle validation errors
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        $('#name-error').text(errors.name[0]);
                    }
                    if (errors.address) {
                        $('#address-error').text(errors.address[0]);
                    }
                    if (errors.image) {
                        $('#image-error').text(errors.image[0]);
                    }
                    // You can also loop through errors for department validation if needed
                }
            }
        });
    });
</script>

<!-- saving New Distributor: End  -->


<script>
        $(document).ready(function() {
        var input1 = $('#article_no');
        var input2 = $('#invoice_no');
        
        // Add event listener to input1
        input1.on('change', function() {
            // Set the value of input2 to the value of input1
            input2.val(input1.val());
        });
        });
</script>


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
        $('#distributor').select2({
    templateResult: formatOption,
    templateSelection: formatOption
});

function formatOption(option) {
    if (!option.id) {
        return option.text;
    }

    var $option = $('<span><img src="' + $(option.element).data('image') + '" class="img-flag" /> ' + option.text + '</span>');
    return $option;
}
    </script>

          
  
<script>
    $('#brand').select2({
templateResult: formatOption,
templateSelection: formatOption
});

function formatOption(option) {
if (!option.id) {
    return option.text;
}

var $option = $('<span><img src="' + $(option.element).data('image') + '" class="img-flag" /> ' + option.text + '</span>');
return $option;
}
</script>
 

<!-- {{-- Purchase Price Calculation By Percent--}} -->
<script>
    $( "#discount_group" ).change(function() {
        var retail_price = parseInt($('#retail_price').val());
        var discount = parseInt($('#discount_group').val());
        var purchase = document.getElementById('purchase_price');
        console.log('The discount:'+discount);
        var result = retail_price - retail_price / 100 * discount;
        purchase.value = Math.round((result));

        console.log('Result:' + result );
    });
</script>
<!-- {{-- Purchase Price Calculation by Percent--}} -->

<!-- {{-- Sale Price Calculation --}} -->
<script>
   function sales() {
  var sale_price_input = document.getElementById('sale_price');
  var purchase_price = parseFloat(document.getElementById('purchase_price').value);
  var price = parseFloat(document.getElementById('purchase_price').value);
  var payment_method = document.getElementById('payment_method_type').value;
  var advance_p = parseFloat(document.getElementById('advance_price_percent').value);
  var advance_e = parseFloat(document.getElementById('advance_price_euro').value);

  var plus = document.getElementById('plus_percent').value
  var plus_p = parseFloat(document.getElementById('plus_price_percent').value);
  var plus_e = parseFloat(document.getElementById('plus_price_euro').value);

  if (payment_method == "Percent") {
    var advance_payment = price * advance_p / 100;
  } else if (payment_method == "Euro") {
    var advance_payment = advance_e;
  } else {
    var advance_payment = 0;
  }

  var plus_result;
  if (plus == "Percent") {
    plus_result = price * plus_p / 100;
  } else if (plus == "Euro") {
    plus_result = plus_e;
  } else {
    plus_result = 0;
  }

  var total = (purchase_price - advance_payment) + plus_result;
  sale_price_input.value = total;

  console.log('Result: ' + total + ' advance_payment: ' + advance_payment + ' plus Result: ' + plus_result);
}

</script>
<!-- {{-- Sale Price Calculation --}} -->


<!-- {{-- Purchase Price Calculation By Euro--}} -->
<script>
    $( "#r_discount_e" ).change(function() {

        var retail_price = parseInt($('#retail_price').val());
        var retail_euro = parseInt($('#r_discount_e').val());
        var purchase = document.getElementById('purchase_price');

        var result = retail_price - retail_euro;
        purchase.value = result;

        console.log('Result:' + result );
    });
</script>
<!-- {{-- Purchase Price Calculation by Euro--}} -->

<script>
    function calculate(){
        var price = parseFloat(document.getElementById('purchase_price').value);
        var sale_price = parseFloat(document.getElementById('sale_price').value);
        var payment_method =  document.getElementById('payment_method_type').value;
        var advance_p =  document.getElementById('advance_price_percent').value;
        var advance_e =  document.getElementById('advance_price_euro').value;

        var plus =  document.getElementById('plus_percent').value
        var plus_p =  parseFloat(document.getElementById('plus_price_percent').value);
        var plus_e =  parseFloat(document.getElementById('plus_price_euro').value);
        var tax = parseFloat(document.getElementById('tax').value);
        var discount_type = document.getElementById('discount_type').value;
        var discount_p = document.getElementById('discount_percent').value;
        var discount_e = document.getElementById('discount_euro').value;
        var total =  document.getElementById('total')

            var discount= 0;
            if( discount_type == "Percent"){
                 discount = sale_price/100 * discount_p;
            } else if( discount_type == "Euro"){
                discount = discount_e;
            }
            var tax_result = sale_price / 100 * tax;
            var total_price = sale_price + tax_result - discount ;
           
            var net_total = price - total_price;
            total.value=total_price;
            console.log('price ' + price + ' Discount: ' + discount + ' tax: '+tax_result +' Total Price: ' + total_price + ' Net Total :' + net_total);}


</script>

<script>
 $( "#sale_price" ).change(function() {
    tax_result();
 })

 $( "#plus_price_percent" ).change(function() {
    tax_result();
 })

 $( "#plus_price_euro" ).change(function() {
    tax_result();
 })

function tax_result() {
    var tax_result = document.getElementById('tax_result');
    var sale_price = parseFloat(document.getElementById('sale_price').value);
    var tax = parseInt(document.getElementById('tax').value);
    var result = sale_price / 100 * tax;

    if (isNaN(sale_price)) {
        tax_result.innerText = "Der Preis ist nicht definiert";
    } else {
        tax_result.innerText = result + ' Euro';
    }

    console.log('tax result is: ' + result + ' sale Price ' + sale_price);
}

</script>


<script>
    function toggleRoofTypeSection() {
        var category = document.getElementById("category").value;
        var roofTypeSection = document.getElementById("roof_type_section");
        if (category === "Dachziegel") {
            roofTypeSection.classList.remove("hidden");
        } else {
            roofTypeSection.classList.add("hidden");
        }
    }
</script>


<!-- Sub Article Group Get Request:  -->

<script>
    $(document).ready(function () {
        $('#article_group').change(function () {
            // Get the selected article group ID
            var articleGroupId = $(this).val();

            // Ensure a valid article group is selected
            if (articleGroupId) {
                // Make the AJAX request to get sub-articles
                $.ajax({
                    url: '{{ route("product.get.sub.article") }}', // Laravel route
                    type: 'GET',
                    data: { article: articleGroupId }, // Send the selected article group ID
                    success: function (response) {
                        // Empty the sub-article dropdown before adding new options
                        $('#sub_article').empty();
                        $('#sub_article').append('<option selected disabled>Bitte wählen Sie die Sub Article aus</option>');

                        // Iterate over the returned data and populate the sub-article dropdown
                        $.each(response, function (key, value) {
                            $('#sub_article').append('<option value="' + value.id + '">' + value.sub_article + '</option>');
                        });
                    },
                    error: function (xhr) {
                        console.log('Error:', xhr);
                    }
                });
            } else {
                // Clear sub-article dropdown if no article group is selected
                $('#sub_article').empty();
                $('#sub_article').append('<option selected disabled>Bitte wählen Sie die Sub Article aus</option>');
            }
        });
    });
</script>

<!-- Cloning the value of Distributor select into drop down of distributor table : start -->
<script>
    $(document).ready(function() {
        // When the main distributor dropdown changes
        $('#distributor').on('change', function() {
            // Get the selected options (array of selected distributor IDs)
            var selectedDistributors = $(this).val();

            // Log the selected distributors to check if they are captured correctly
            console.log("Selected Distributors: ", selectedDistributors);

            // For each row in the pricing table, update the distributor dropdown accordingly
            $('#distributor_price tbody tr').each(function() {
                var row = $(this);

                // Clear the distributor_id dropdown in this specific row
                row.find('select[name*="[distributor_id]"]').empty();

                // If there are selected distributors, append them to the distributor_id dropdown for this row
                if (selectedDistributors && selectedDistributors.length > 0) {
                    selectedDistributors.forEach(function(distributorId) {
                        // Get the corresponding text (distributor name) from the main distributor dropdown
                        var distributorName = $('#distributor option[value="' + distributorId + '"]').text();

                        // Check if the distributor name is being fetched correctly
                        console.log("Appending Distributor to row: ", distributorId, distributorName);

                        // Append the selected distributor to the distributor_id dropdown for this row
                        row.find('select[name*="[distributor_id]"]').append('<option value="' + distributorId + '">' + distributorName + '</option>');
                    });
                }

                // Optionally, add a placeholder or default empty option
                row.find('select[name*="[distributor_id]"]').prepend('<option value="">Select Distributor</option>');
            });
        });
    });
</script>


<!-- Cloning the value of Distributor select into drop down of distributor table : end -->

 


<!-- Calculating the Price and discount of Distributor: Start  -->
<script>
    var d = 0;
    const todayDate = "{{ \Carbon\Carbon::now()->format('Y-m-d') }}";

    const discountGroupOptions = `
        <option value="">-- Gruppe --</option>
        @foreach($discount_group as $dg)
            <option value="{{ $dg->id }}" data-percent="{{ $dg->discount }}">{{ $dg->discount_group }} - {{ $dg->discount }}%</option>
        @endforeach
    `;

    function getDistributorOptions() {
        let options = '';
        $('#distributor option').each(function () {
            const value = $(this).val();
            const text = $(this).text();
            options += '<option value="' + value + '">' + text + '</option>';
        });
        return options;
    }

    function addNewRow(distributorId) {
        ++d;
        $('#distributor_price tbody').append(`
            <tr>
                <th scope="row">
                    <select name="price[${d}][distributor_id]" class="form-control distributor_id">
                        <option value="${distributorId}">${$('#distributor option[value="' + distributorId + '"]').text()}</option>
                    </select>
                </th>
                <td><input type="text" name="price[${d}][article_no]" class="form-control" placeholder="artikel#"></td>
                <td><input type="text" name="price[${d}][price]" class="form-control price_field" placeholder="Listenpreis"></td>
                <td><input type="text" name="price[${d}][purchase_price]" class="form-control purchase_price" placeholder="Einkaufspreis"></td>
                <td>
                    <select name="price[${d}][discount_group_id]" class="form-control discount_group_select">
                        ${discountGroupOptions}
                    </select>
                </td>
                <td><input type="text" name="price[${d}][discount_price]" class="form-control discount_price" placeholder="Euro"></td>
                <td><input type="text" name="price[${d}][discount_percent]" class="form-control discount_percent" placeholder="Perzent"></td> 
                <td><input type="date" name="price[${d}][price_date]" class="form-control" value="${todayDate}"></td>
                <td><input type="text" name="price[${d}][availability]" class="form-control" value="Sofort Lieferbar"></td>
                <td><button type="button" class="btn btn-icon rounded-circle btn-outline-danger remove_price_row"><i class="feather icon-minus-square"></i></button></td>
            </tr>
        `);
    }

    $('#add_price').on('click', function (e) {
        e.preventDefault();
        ++d;
        $('#distributor_price tbody').append(`
            <tr>
                <th scope="row">
                    <select name="price[${d}][distributor_id]" class="form-control distributor_id">
                        <option value=""></option>
                        ${getDistributorOptions()}
                    </select>
                </th>
                <td><input type="text" name="price[${d}][article_no]" class="form-control" placeholder="artikel#"></td>
                <td><input type="text" name="price[${d}][price]" class="form-control price_field" placeholder="Listenpreis"></td>
                <td><input type="text" name="price[${d}][purchase_price]" class="form-control purchase_price" placeholder="Einkaufspreis"></td>
                <td>
                    <select name="price[${d}][discount_group_id]" class="form-control discount_group_select">
                        ${discountGroupOptions}
                    </select>
                </td>
                <td><input type="text" name="price[${d}][discount_price]" class="form-control discount_price" placeholder="Euro"></td>
                <td><input type="text" name="price[${d}][discount_percent]" class="form-control discount_percent" placeholder="Perzent"></td> 
                <td><input type="date" name="price[${d}][price_date]" class="form-control" value="${todayDate}"></td>
                <td><input type="text" name="price[${d}][availability]" class="form-control" value="Sofort Lieferbar"></td>
                <td><button type="button" class="btn btn-icon rounded-circle btn-outline-danger remove_price_row"><i class="feather icon-minus-square"></i></button></td>
            </tr>
        `);
    });

    $(document).on('change', 'input.price_field, input.discount_price, input.discount_percent, input.purchase_price', function () {
        const row = $(this).closest('tr');
        calculateRowValues(row);
    });

    $(document).on('change', '.discount_group_select', function () {
        const selected = $(this).find('option:selected');
        const discountPercent = parseFloat(selected.data('percent')) || 0;
        const row = $(this).closest('tr');

        row.find('input.discount_percent').val(discountPercent.toFixed(2));
        row.find('input.discount_percent').trigger('change');
    });

    $(document).on('click', '.remove_price_row', function () {
        $(this).closest('tr').remove();
    });

    $('#distributor').on('change', function () {
        let selectedDistributors = $(this).val() || [];

        $('#distributor_price tbody tr').each(function () {
            const existingId = $(this).find('select.distributor_id').val();
            selectedDistributors = selectedDistributors.filter(id => id != existingId);
        });

        selectedDistributors.forEach(function (id) {
            addNewRow(id);
        });
    });

    function calculateRowValues(row) {
        let price = parseFloat(row.find('input.price_field').val()) || 0;
        let discountPrice = parseFloat(row.find('input.discount_price').val()) || 0;
        let discountPercent = parseFloat(row.find('input.discount_percent').val()) || 0;
        let purchasePrice = parseFloat(row.find('input.purchase_price').val()) || 0;

        if (price > 0 && purchasePrice > 0) {
            discountPrice = price - purchasePrice;
            discountPercent = (discountPrice / price) * 100;
        } else if (price > 0 && discountPrice > 0) {
            discountPercent = (discountPrice / price) * 100;
            purchasePrice = price - discountPrice;
        } else if (price > 0 && discountPercent > 0) {
            discountPrice = (price * discountPercent) / 100;
            purchasePrice = price - discountPrice;
        } else if (purchasePrice > 0 && discountPrice > 0) {
            price = purchasePrice + discountPrice;
            discountPercent = (discountPrice / price) * 100;
        } else if (purchasePrice > 0 && discountPercent > 0) {
            price = purchasePrice / (1 - (discountPercent / 100));
            discountPrice = price - purchasePrice;
        }

        row.find('input.price_field').val(price.toFixed(2));
        row.find('input.purchase_price').val(purchasePrice.toFixed(2));
        row.find('input.discount_price').val(discountPrice.toFixed(2));
        row.find('input.discount_percent').val(discountPercent.toFixed(2));
    }
</script>


<!-- Calculating the Price and discount of Distributor: end  -->


<!-- select2 drop downs: start  -->

<script>
        $(document).ready(function() {
        
            $('#color').select2({
                placeholder: "Farbe auswählen",
                allowClear: true
            
            });

             $('.percent').select2({
                placeholder: "% Rabbat",
                allowClear: true,
                tags: true
            
            });

            $('#measure_unit').select2({
                placeholder: "Mangeneinheit auswählen",
                allowClear: true
            
            });
            $('#article_group').select2();
            $('#discount_group').select2();
            $('#sub_article').select2();
            $('#category').select2();
            $('#roof_type').select2();
        }); 
    </script>

<!-- select2 drop downs: end  -->


<!-- saving the product in database: start  -->
 <script>
    $('#saveProductBtn').on('click', function (e) {
    e.preventDefault(); // Prevent the default form submission

    // Clear previous error messages
    $('.text-danger').text('');

    // Create a FormData object for handling file uploads and form submission
    var formData = new FormData($('#productForm')[0]);

    // Ensure proper handling of form data and prevent duplications
    $('#productForm').find('input, select, textarea').each(function () {
        var inputName = $(this).attr('name');
        var inputValue = $(this).val();

        // Only append to formData if the input has a value and is not already duplicated
        if (inputValue && !formData.has(inputName)) {
            formData.append(inputName, inputValue);
        }
    });

    // Make an AJAX POST request to store the product
    $.ajax({
        url: "{{ route('product.store') }}", // Your route to the 'store' method
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            toastr.success('Das Produkt wurde erfolgreich hinzugefügt!');
            window.location.href = '/product/';
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                var errors = xhr.responseJSON.errors;

                // Loop through the errors and display them in the form fields and using Toastr
                $.each(errors, function (field, messages) {
                    var fieldName = field.replace(/\./g, '_');
                    $('#' + fieldName + '-error').text(messages[0]);
                    messages.forEach(function (message) {
                        toastr.error(message);
                    });
                });
            } else {
                toastr.error('Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.');
            }
        }
    });
});
</script>

<!-- aving the product in database: end  -->

@endsection


