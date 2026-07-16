@extends('admin.layouts.app')
@section('title') Ducken @endsection
@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/invoice.css ')}}">
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
                            <h2 class="content-header-title float-left mb-0">Aushändigen</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::previous()}}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Artikel</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
       
            </div>
            <div class="content-body">
                <!-- invoice functionality start -->
                <section class="invoice-print mb-1">
                    <div class="row">
                        <div class="col-12 col-md-7 d-flex flex-column flex-md-row justify-content-right">
                            <button class="btn btn-primary btn-print mb-1 mb-md-0"> <i class="feather icon-file-text"></i> Print</button>
                            <button class="btn btn-outline-primary  ml-0 ml-md-1"> <i class="feather icon-download"></i> Download</button>
                        </div>
                    </div>
                </section>
                <!-- invoice functionality end -->
                <!-- invoice page -->
               
                <section class="card invoice-page" style="    margin-left: 20px;">
                    <div id="invoice-template" class="card-body">
                        <!-- Invoice Company Details -->
                        <div id="invoice-company-details" class="row">
                            <div class="col-sm-6 col-12 text-left pt-1">
                                @if($data->branch=="Solar Aspekt")
                                <div class="media pt-1">
                                    <img src="{{ asset('logo/logo.png') }}" alt="Solar Aspekt" />
                                </div>
                                @else
                                <div class="media pt-1">
                                    <img src="{{ asset('logo/werk.png') }}" alt="Werk Studio" />
                                </div>
                                @endif

                            </div>
                            <div class="col-sm-6 col-12 text-right">
                                <h1>Formular zur Objektübergabe</h1>
                                <div class="invoice-details mt-2">
                                    <h6 class="mt-2"> DATUM</h6>
                                    <p>{{ \Carbon\Carbon::today()->isoFormat('DD.MM.YYYY') }}</p>
                                </div>
                            </div>
                        </div>
                        <!--/ Invoice Company Details -->

                        <!-- Invoice Recipient Details -->
                        <div id="invoice-customer-details" class="row pt-3">
                            <div class="col-sm-6 col-12 text-left">
                                <h5>Empfänger/in</h5>
                                <div class="recipient-info my-2">
                                    <p>{{ $data->hand_to_name }} {{ $data->hand_to_lastname }}</p>
                                </div>
                                <div class="recipient-contact pb-2">
                                    <p>
                                        <i class="feather icon-mail"></i>
                                        {{ $data->hand_to_email }}
                                    </p>
                                    <p>
                                        <i class="feather icon-phone"></i>
                                        {{ $data->hand_to_phone }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6 col-12 text-right">
                                <h5>Verantwortlich</h5>
                                <div class="company-info my-2">
                                    <p>{{ $data->hand_from_name }} {{ $data->hand_from_lastname }}</p>
                                </div>
                                <div class="company-contact">
                                    <p>
                                        <i class="feather icon-mail"></i>
                                       {{ $data->hand_from_email}}
                                    </p>
                                    <p>
                                        <i class="feather icon-phone"></i>
                                        {{ $data->hand_from_phone }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!--/ Invoice Recipient Details -->

                        <!-- Invoice Items Details -->
                        <div id="invoice-items-details" class="pt-1 invoice-items-table">
                            <div class="row">
                                <div class="table-responsive col-12">
                                    <table class="table " style="border:1px; border-style:solid; border-color:#1f2937;">
                                        <thead>
                                            <tr>
                                                <th style="border:1px; border-style:solid; border-color:#1f2937;">ARTIKEL</th>
                                                <th style="border:1px; border-style:solid; border-color:#1f2937;">MENGE</th>
                                                <th style="border:1px; border-style:solid; border-color:#1f2937;">VERWENDUNGSZWECK</th>
                                                <th style="border:1px; border-style:solid; border-color:#1f2937;">INFO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                @foreach ($handovers as $handover )
                                                @if($handover->handover_id == $data->handover_id)
                                                <td style="border:1px; border-style:solid; border-color:#1f2937;">{{ $handover->item }}</td>
                                                <td style="border:1px; border-style:solid; border-color:#1f2937;">{{ $handover->quantity }}</td>
                                                <td style="border:1px; border-style:solid; border-color:#1f2937;">{!! $handover->purpose   !!}</td>
                                                <td style="border:1px; border-style:solid; border-color:#1f2937;">{{ $handover->remark }}</td>
                                            </tr>
                                            <tr>
                                                <td>{{ $handover->description }}</td>
                                                
                                            </tr>
                                            @endif
                                                @endforeach
                                               
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div id="invoice-total-details" class="invoice-total-table">
                            <div class="row">
                                <div class="col-7 offset-5"> 
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th>Artikel-Barcode </th>
                                                    <td> <p style="margin-bottom: 0px;" > {!! DNS1D::getBarcodeHTML("$data->handover_id", 'C39',2,25,'black') !!}</p></td>
                                                </tr>
                                            
                                              
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th>
                                                        <td>Unterschrift: </br> Datum:</td>
                                                    </th>
                                                    <td>{{ $data->hand_to_name }} {{ $data->hand_to_lastname }}</td>

                                                    <th>
                                                        <td>Unterschrift: </br> Datum:</td>
                                                    </th>
                                                    <td>{{ $data->hand_from_name }} {{ $data->hand_from_lastname }}</td>
                                                </tr>
                                                
                                              
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Footer -->
                        
                        <!--/ Invoice Footer -->

                    </div>
                </section>
                <!-- invoice page end -->

            </div>
        </div>
    </div>
    <!-- END: Content-->
@endsection

@section('script')
<script src="{{ asset('app-assets/js/scripts/pages/invoice.js') }}"></script>
@endsection