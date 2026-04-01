@extends('admin.layouts.app')

@section('title') PV System Data Overview @endsection
@section('style')
<!-- Include stylesheet -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

<style>
    body {
        margin: 0;
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
        height: 30px;
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

    .category p {
    margin-bottom: 10px; /* Adds space between paragraphs */
    font-size: 14px; /* Sets the font size */
    }
    .category strong {
    color: #0056b3; /* Changes the color of the category titles */
    font-size: 16px; /* Sets a larger font size for the titles */
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
                        <h2 class="content-header-title float-left mb-0">PV System Data Overview</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('customer_details') }}">Kunden</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#">Neu</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="content-body">
            <!-- Basic Horizontal form layout section start -->
            <section id="basic-horizontal-layouts">
                <div class="row match-height">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>BREITENGRAD</th>
                                                            <td>{{ $customer->lat }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>LÄNGENGRAD</th>
                                                            <td>{{ $customer->lon }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>HÖHE ÜBER NN</th>
                                                            <td>{{ $customer->elevation }} Meters</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>


                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        
                    <div class="col-md-6">
                        <section id="nav-filled">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card overflow-hidden">
                                        <div class="card-header">
                                            <h4 class="card-title">Filled</h4>
                                        </div>
                                        <div class="card-content">
                                            <div class="card-body"> 
                                                <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" id="home-tab-fill" data-toggle="tab"
                                                            href="#home-fill" role="tab" aria-controls="home-fill"
                                                            aria-selected="true">Monthly Data</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="profile-tab-fill" data-toggle="tab"
                                                            href="#profile-fill" role="tab" aria-controls="profile-fill"
                                                            aria-selected="false">Total Annual Data</a>
                                                    </li>
                                                </ul>

                                                <!-- Tab panes -->
                                                <div class="tab-content pt-1">
                                                    <div class="tab-pane active" id="home-fill" role="tabpanel"
                                                        aria-labelledby="home-tab-fill">
                                                        <table class="table table-hover mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Month</th>
                                                                    <th>Tägliche Energieproduktion (kWh/Tag)</th>
                                                                    <th>Average Monthly Energy Production (kWh/month)</th>
                                                                    <th>Average Daily Global Irradiation (kWh/m²/day)</th>
                                                                    <th>Average Monthly Global Irradiation (kWh/m²/month)</th>
                                                                    <th>Monthly Energy Production Standard Deviation (kWh)</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                               
                                                                  @foreach($data as $values)
                                                                        <tr>
                                                                            <td>{{ $values['month'] }}</td> <!-- Month -->
                                                                            <td>{{ $values['E_d'] }}</td> <!-- Average Daily Energy Production -->
                                                                            <td>{{ $values['E_m'] }}</td> <!-- Average Monthly Energy Production -->
                                                                            <td>{{ $values['H(i)_d'] }}</td> <!-- Average Daily Global Irradiation -->
                                                                            <td>{{ $values['H(i)_m'] }}</td> <!-- Average Monthly Global Irradiation -->
                                                                            <td>{{ $values['SD_m'] }}</td> <!-- Monthly Energy Production Standard Deviation -->
                                                                        </tr>
                                                                    @endforeach
                                                                 
                                                            </tbody>
                                                        </table>

                                                    </div>
                                                    <div class="tab-pane" id="profile-fill" role="tabpanel"
                                                        aria-labelledby="profile-tab-fill">
                                                         
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                     

                     
            <!-- // Basic Horizontal form layout section end --> 

        </div>
    </div>
</div>
<!-- END: Content-->

@endsection

@section('script')


<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/popover/popover.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


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
    $(document).ready(function(){
                                                        $('.category').popover({
                                                            html: true
                                                        });
                                                    });
</script>




@endsection