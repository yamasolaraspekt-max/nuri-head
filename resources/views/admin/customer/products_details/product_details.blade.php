@extends('admin.layouts.app')

@section('title') PHOTOVOLTAIK @endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/progress.css')}}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/phase.css')}}"> -->
<style>

    .dropdown-menu.show {
        display: block;
    }
    body {
        margin: 0;
    }

    h4 {
        font-size: 1rem !important;
    }

    h3 {

        font-size: 1rem !important;
    }

    .title {
       font-size: 17px !important;
        font-weight: bold !important;
    }

    .product_card {
        border-radius: 71px;
        background: #cfe09b!important;
    }

    #product_card_details {
        background: #95c11f!important;
        border-radius: 83px;
        color: white;
    }



    .products.selected {
        background: #cfe09b !important;
        color: white !important;
        border-radius: 71px;
    }

    .products.selected #product_card_details {
        background: #95c11f !important;
    }

    .products.selected .product_card {
        background: #cfe09b !important;

    }



    .products {
        cursor: pointer;
    }

    .sb-title {
        position: relative;
        top: -12px;
        font-family: Roboto, sans-serif;
        font-weight: 500;
    }

    .sb-title-icon {
        position: relative;
        top: -5px;
    }

    .card-container {
        display: flex;
        height: 500px;
        width: 600px;
    }

    .panel {
        background: white;
        width: 300px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-around;
    }

    .half-input-container {
        display: flex;
        justify-content: space-between;
    }

    .half-input {
        max-width: 120px;
    }

    .map {
        width: 300px;
    }

    h2 {
        margin: 0;
        font-family: Roboto, sans-serif;
    }

    input {
        height: 30px;
    }

    input {
        border: 0;
        border-bottom: 1px solid black;
        font-size: 14px;
        font-family: Roboto, sans-serif;
        font-style: normal;
        font-weight: normal;
    }

    input:focus::placeholder {
        color: white;
    }

    .star-rating {
        font-size: 2rem;
        cursor: pointer;
    }

    .star {
        color: #ccc;
    }

    .star.selected,
    .star.hovered {
        color: #9cc136;
    }

    .flex_me {
        display: flex !important;
        flex-wrap: nowrap;
        align-items: center;
    }

    .img-flag {
        width: 60px !important;
        top: 200px;
    }

    #roof {
        display: flex;
        flex-wrap: nowrap;
        justify-content: space-between;
        align-items: center;
    }

    #select2-selection__rendered span {
        display: flex !important;
        flex-wrap: nowrap !important;
        justify-content: space-between !important;
        align-items: center !important;
    }

    .select2-selection {
        border: 2px !important;
        width: 100% !important;
        background: #efeded !important;
        height: 70px !important;
    }

    .select2-container .select2-selection--single .select2-selection__arrow {
        display: none;
        /* Hides the arrow */
    }

    .custom-control-label::before,
    .custom-control-label::after {
        width: 1.5rem !important;
        height: 1.5rem !important;
        top: 0.03rem !important;
        border: 3px solid #73b1d4 !important;
        border-radius: 50% !important;
    }

    .custom-control-label {
        font-size: 16px !important;
    }

    .d-inline-block {
        width: 158px !important;
    }

    .list-unstyled {
        display: flex;
        flex-wrap: nowrap;
    }

    hr {
        border: 2px solid #73b1d4 !important;
    }
    .normal {
        border: 1px solid #d8d8d8 !important;
    }

    .tab-control {
        background: transparent !important;
        font-size: 24px !important;
        font-weight: bold !important;
        border-top: 3px solid #73b1d4;
        border-bottom: 3px solid #73b1d4;
        color: #95c11f !important;
    }
    .tab-control .active {
    color: #73b1d4 !important;
    background: transparent !important;
    font-size: 24px !important;
    font-weight: bold !important;
    }
    .titles {
        font-size: 23px !important;
        color: #73b1d4 !important;
        font-weight: bold !important;
        }
    @media (min-width: 766) {
    .right-border {
    border-right: 1px solid #d7d0d0;
    border-width: thin;
    }
    }

    label {
        line-height: 2 !important;
    }

    #accordion {
        font-size: 32px;
        color: #73b1d4;
        transform: rotate(90deg);
        position: static;
        float: right;
        margin-top: -24px;
    }

    .input-control {
        background-color: transparent !important;
        border:0 !important;
    }
    h4 {
        font-size: 1.2rem !important;
        font-weight: bold !important;
    }
    input {
        font-size: 15px !important;
        font-weight: 100 !important;
    }
    #backLable:hover {
        color: #94c11f !important;
            cursor: pointer;
    }


</style>
@endsection

@section('content')

<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">KUNDEN INFORMATION</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('customer_details') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ url('customer_product_details/'.$customer->id.'/'.$customer->postcode.'/'.$alternative->address_no)}}">PRODUCT</a>
                                </li>
                                <li class="breadcrumb-item active">{{ $article->article_group }}
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                <div class="form-group breadcrum-right">
                    <div class="dropdown">
                        <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle waves-effect waves-light"
                            type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                class="feather icon-settings"></i></button>
                        <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">Chat</a><a
                                class="dropdown-item" href="#">Email</a><a class="dropdown-item" href="#">Calendar</a></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body"> 
            <!-- information details -->
            <div class="col-12">
                <hr>
            </div>

            <section id="content-types">
                <div class="row match-height">

                        <div class="col-xl-2 col-xl-2 col-12"  >
                            <div class="card" style="box-shadow:none;">
                                <div class="card-header" style="background: #cfe09b;border-bottom: 10px solid #f8f8f8;">
                                    <h4 class="title primary mb-2" style="    font-size: 20px !important;">KUNDE</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body"> 
                                        <p class="card-text">
                                            {{ $customer->name }} {{ $customer->lastname }}  <br>
                                            {{ $customer->street }} <br>
                                            {{ $customer->postcode }}, {{ $customer->city }}</p>
                                        <p>
                                            <i class="feather icon-phone"></i> {{ $customer->phone }} <br>
                                            <i class="feather icon-mail"></i> {{ $customer->email }} <br>
                                        </p>
                                    </div>
                                </div>
                            </div> 
                        </div>

                        <div class="col-xl-2 col-xl-2 col-12">
                            <div class="card" style="box-shadow:none;">
                                <div class="card-header" style="background: #cfe09b;border-bottom: 10px solid #f8f8f8;">
                                    <h4 class="title primary mb-2" style="    font-size: 20px !important;">BAUVORHABEN</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body"> 
                                        <p class="card-text">
                                            @if($alternative)
                                                {{ $alternative->street }} <br>
                                                {{ $alternative->postcode }}, {{ $alternative->city }}
                                                @else
                                                {{ $customer->street }} <br>
                                                {{ $customer->postcode }}, {{ $customer->city }}</p>
                                            @endif 
                                    </div>
                                </div>
                            </div> 
                        </div>

                        <div class="col-xl-2 col-xl-2 col-12">
                            <div class="card" style="box-shadow:none;">
                                <div class="card-header" style="background: #cfe09b;border-bottom: 10px solid #f8f8f8;">
                                    <h4 class="title primary mb-2" style="    font-size: 20px !important;">WEITERES</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body"> 
                                        <p class="card-text">Anfrage von: </p>
                                        <p class="card-text">Besuch im Showroom</p>
                                        <p class="card-text">Verfasser:</p>
                                        <p class="card-text">{{ $customer->cname }} {{$customer->clastname}}</p>
                                           
                                    </div>
                                </div>
                            </div> 
                        </div>

                         
                        <div class="col-xl-6 col-xl-6 col-12 " style="  display: flex; flex-direction: column;">
                            <div class="col-12">
                                <article style="display: flex; align-items: center;">
                                    <div class="text-center bg-transparent products mt-1 mb-1 col-10" style="">
                                        <div class="card-content ">
                                            <div class="row product_card">
                                                <div class="col-md-2 col-2" id="product_card_image">
                                                    <img src="{{ asset('images/articles/'.$article->image) }}" alt="{{ $article->article_group }}"
                                                        style="width: 71px !important;" class="float-left  mt-2">
                                                </div>

                                                <div class="col-md-10 col-10" id="product_card_details">
                                                    <h2 class="card-title mt-1 mb-0 white title"> {{ $article->article_group }}</h2>
                                                    <p class="card-text"><a href="#" class="white">Projektdaten</a>
                                                        | <a href="#" class="white">Arbeitsschritte</a></p>
                                                    <p class="card-text white mb-1"> Aktualler Status: <span id="interested">Interesse</span> </p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                     
                                </article>
                                <article style="display:flex;justify-content: center; ">
                                    <a href="{{  URL::previous() }}"><label for="" class="title mb-2" style="font-size: 22px;" id="backLable" >zurück zur Übersicht</label></a>
                                    <br> 
                                </article>
                                <article style="display:flex;justify-content: center; ">
                                    @foreach ($article_icon as $ar_icon)
                                    <a type="button" class="btn btn-icon btn-icon  rounded-circle btn-primary mr-1 mb-1" id="inactive"
                                    style="height: 40px;  width: 40px; background:primary !important;">
                                    <span style="font-size: 10px;font-weight: bold; color:white; margin:0;font-family: sans-serif !important;">{{ $ar_icon->initial }}</span>
                                    </a>
                                    @endforeach 
                                </article>
                            </div>  
                        </div> 
                </div>
            </section>
            <!-- information details -->
            

            <section id="nav-justified">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="overflow-hidden">
                            <div class="card-content">
                                <div class="card-body">
                                    <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">

                                        <li class="nav-item tab-control">
                                            <a class="nav-link tab-control active" id="home-tab-justified" data-toggle="tab" href="#home-just" role="tab"
                                                aria-controls="home-just" aria-selected="true">CHECKLISTE</a>
                                        </li>
                                        <li class="nav-item tab-control">
                                            <a class="nav-link tab-control" id="profile-tab-justified" data-toggle="tab" href="#profile-just"
                                                role="tab" aria-controls="profile-just" aria-selected="true">ACTIVITIES</a>
                                        </li>
                                        <li class="nav-item tab-control">
                                            <a class="nav-link tab-control" id="messages-tab-justified" data-toggle="tab" href="#messages-just" role="tab"
                                                aria-controls="messages-just" aria-selected="true">PROJEKTMANAGEMENT</a>
                                        </li>

                                          <li class="nav-item tab-control">
                                            <a class="nav-link tab-control" id="task-tab-justified" data-toggle="tab" href="#task-just" role="tab"
                                                aria-controls="task-just" aria-selected="true">AUFGABENMANAGEMENT</a>
                                        </li>
                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content pt-1">
                                        <div class="tab-pane active" id="home-just" role="tabpanel"
                                            aria-labelledby="home-tab-justified"> 
                                           @php
                                               $checkProduct = DB::table('article_groups')->where('id', request()->product_id)->first();
                                           @endphp

                                            @if($checkProduct->article_group == "PHOTOVOLTAIK" || $checkProduct->initial=="PV")
                                            @include('admin.customer.products_details.checklist.pv.pv')
                                             @elseif($checkProduct->article_group == "WÄRMEPUMPE" || $checkProduct->initial=="WP")
                                            @include('admin.customer.products_details.checklist.wp.wp') 
                                            @endif
                                        </div>
                                        <div class="tab-pane" id="profile-just" role="tabpanel"
                                            aria-labelledby="profile-tab-justified">
                                            <p>
                                                Chocolate cake icing tiramisu liquorice toffee donut sweet roll cake. Cupcake
                                                dessert icing dragée
                                                dessert. Liquorice jujubes cake tart pie donut. Cotton candy candy canes lollipop
                                                liquorice chocolate
                                                marzipan muffin pie liquorice.
                                            </p>
                                        </div>
                                        <div class="tab-pane" id="messages-just" role="tabpanel"
                                            aria-labelledby="messages-tab-justified">
                                            @include('admin.customer.products_details.project_management.project')
                                        </div>
                                        <div class="tab-pane" id="task-just" role="tabpanel"
                                            aria-labelledby="task-tab-justified">
                                            @include('admin.customer.products_details.tasks.task')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>




@endsection


@push('scripts')
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/phase.js')}}"></script> 

<script>
    $(document).ready(function() {
        function initializeSelect2() {
            $('.tiles').select2({
                templateResult: formatOption,
                templateSelection: formatOption
            });
        }

        function formatOption(option) {
            if (!option.id) {
                return option.text;
            }

            var $option = $(
                '<div id="roof">' +
                '<h3>' + option.text + '</h3>' +
                '<img src="' + $(option.element).data('image') + '" class="img-flag" />' +
                '</div>'
            );
            return $option;
        }

        // Initialize the Select2
        initializeSelect2();
    });
</script>

<script>
    $(document).ready(function(){
        $("#addRow").click(function(){
            var row = `<div class="row align-items-center mb-3">
                            <div class="col-md-1">
                                <label for="category">Dachaufbauten</label>
                            </div>
                            <div class="col-md-2">
                                <select id="kabelfuhrung_durch" class="form-control">
                                    <option>Dachluke</option>
                                    <option>Antenne</option>
                                    <option>Stromleitung</option>
                                    <option>Gaube</option>
                                    <option>SAT-Schüssel</option>
                                    <option>Kamin</option>
                                    <option>Lüfter groß</option>
                                    <option>Dachfenster</option>
                                </select>
                            </div>

                            <div class="col-md-1">
                                <label for="category">geplante Aktion</label>
                            </div>
                            <div class="col-md-2">
                                <select id="geplante_aktion" class="form-control">
                                    <option>erneuert</option>
                                    <option>entfernt</option>
                                    <option>versetzt</option>
                                </select>
                            </div>

                            <div class="col-md-1">
                                    <label for="category">Notiz</label>
                                </div>

                                <div class="col-md-3">
                                    <textarea class="form-control" col="12" row="1" name="geplante_note"></textarea>
                                </div>

                            <div class="col-md-2">
                                <button type="button" class="btn btn-icon rounded-circle remove-row"><i class="feather icon-minus-circle danger" style="font-size: 34px;"></i></button>
                            </div>
                        </div>`;
            $("#rowsContainer").append(row);
        });

        // Delegate the click event for dynamically added rows
        $("#rowsContainer").on("click", ".remove-row", function(){
            $(this).closest('.row').remove();
        });
    });
</script>

<script>
    document.getElementById('accordion').addEventListener('click', function () {
            var longList = document.getElementById('longList');
            if (longList.style.display === 'none' || longList.style.display === '') {
                longList.style.display = 'block';
                this.classList.remove('fa-chevron-right');
                this.classList.add('fa-chevron-down');
            } else {
                longList.style.display = 'none';
                this.classList.remove('fa-chevron-down');
                this.classList.add('fa-chevron-right');
            }
        });
</script>

 
@endpush
