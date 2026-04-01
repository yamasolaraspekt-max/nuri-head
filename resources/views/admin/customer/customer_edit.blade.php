@extends('admin.layouts.app')

@section('title') KUNDEN UND OBJEKTDATEN @endsection

@section('style')
<!-- Include stylesheet -->

<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">


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
                            @foreach ($data as $title)
                                
                           
                            <h2 class="content-header-title float-left mb-0">KUNDEN INFORMATION</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="">{{ $title->name }} {{ $title->lastname }}</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">BEARBEITEN</a>
                                    </li>
                                </ol>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="content-body">
                <!-- Basic Horizontal form layout section start -->
                <section id="basic-horizontal-layouts">
                    <div class="row match-height">
                        <div class="col-md-6 col-12">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <form class="form form-horizontal" action=" {{ action('App\Http\Controllers\CustomerController@update')}}" method="post" >
                                            @csrf
                                            <div class="form-body">
                                            @foreach ($data as $cus)
                                                <div class="row">
                                               
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Title</span>
                                                            </div>
                                                           <div class="col-md-8">
                                                                <input type="text"  id="first-name" value="{{$cus->title }}"class="form-control" name="title" >
                                                                <input type="hidden" name="id" value="{{ $cus->id }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Firma</span>
                                                            </div>
                                                           <div class="col-md-8">
                                                                <input type="text"  id="first-name" class="form-control" value="{{$cus->firma }}" name="firma" >
                                                            </div>
                                                        </div>
                                                    </div>


                                                  
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Vorname</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text"  id="first-name" class="form-control" value="{{$cus->lastname }}" name="lastname" >
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Name</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text"  id="first-name" class="form-control" value="{{$cus->name }}" name="name">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Straße / Nr.</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text"  id="first-name" class="form-control" value="{{$cus->street }}" name="street">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>PLZ / Ort</span>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text"  id="first-name" class="form-control" value="{{$cus->postcode }}" name="postcode">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text"  id="first-name" class="form-control" value="{{$cus->city }}"name="city">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Tel</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="number"  id="contact-info" class="form-control" value="{{$cus->phone }}" name="phone">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>E-Mail</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="email"  id="contact-info" class="form-control" value="{{$cus->email }}" name="email" >
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>BV abweichende Adresse</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                   
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Straße und Nr.</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text"  id="contact-info" class="form-control" value="{{$cus->street2 }}" name="street2">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>PLZ und Ort</span>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text"  id="contact-info" class="form-control" value="{{$cus->postcode2 }}" name="postcode2" >
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text"  id="contact-info" class="form-control" value="{{$cus->city2 }}" name="city2" >
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Produkt</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <fieldset class="form-group">
                                                            <select class="select2-customize-result form-control" multiple="multiple" name="product_id[]" id="product"  >
                                                                @foreach ($products as $pro1 )
                                                                <option selected value="{{ $pro1->id }}">ausgewählt:{{ $pro1->product }}</option>
                                                                @endforeach

                                                                @foreach ($product as $pro)
                                                                <option value="{{ $pro->id }}">{{ $pro->product }}</option>
                                                                @endforeach
                                                                
                                                            </select>
                                                        </fieldset>
                                                        </div>
                                                    </div>
                                                    
                                                    
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Kontaktperson</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <fieldset class="form-group">
                                                            <select class="form-control" id="basicSelect" name="contact_person">
                                                                @foreach ($employee as $emp1)
                                                                    @if($cus->contact_person==$emp1->id)
                                                                     <option selected value="{{ $emp1->id }}">{{ $emp1->name }} {{ $emp1->lastname }}</option>
                                                                    @endif
                                                                @endforeach
                                                                @foreach ($employee as $emp)
                                                                <option value="{{ $emp->id }}">{{ $emp->name }} {{ $emp->lastname }}</option>
                                                                @endforeach
                                                                
                                                            </select>
                                                        </fieldset>
                                                        </div>
                                                    </div>
                                                 
                                                    @endforeach
                                            </div>
                                             
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-8">
                                                        <button type="submit" class="btn btn-primary" > SUBMIT </button>
                                                    </div>
                                                </div>
                                            </div>   
                                        </form>
                                    </div>

                                     
                         </div>
                        </div>
                    </div>
                       
                </section>
                <!-- // Basic Horizontal form layout section end -->
            </div>
        </div>
    </div>
    <!-- END: Content-->

@endsection

@section('script')
<script src="{{ asset('js/select2.min.js') }}"></script>


<script>
        $(document).ready(function() {
            $('#product').select2();
        });

        
    </script>
@endsection