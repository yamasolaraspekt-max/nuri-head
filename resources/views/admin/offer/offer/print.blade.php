@extends('admin.layouts.app')
@section('title') Angebot @endsection
@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/invoice.css')}}">
<style>
    @media print {
        .new-page {
            page-break-before: always;
            /* Always start a new page before this element */
        }
    }

    #cover {
    width: 100%; /* Full width */
    height: auto; /* Maintain aspect ratio */
    }
    
    /* Print-specific styling */
    @media print { 
    #cover {
    margin: 0;
    width: 100%;
    height: 100%;
    max-height: 100%;
    object-fit:fill; /* Ensures the image covers the full area without distortion */
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
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Angebot</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Angebot</a>
                                    </li>
                                    <li class="breadcrumb-item active">Drucken
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrum-right">
                        <div class="dropdown">
                            <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="feather icon-settings"></i></button>
                            <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">Chat</a><a class="dropdown-item" href="#">Email</a><a class="dropdown-item" href="#">Calendar</a></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- invoice functionality start -->
                <section class="invoice-print mb-1">
                    <div class="row">

                        <fieldset class="col-12 col-md-5 mb-1 mb-md-0">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Email" aria-describedby="button-addon2">
                                <div class="input-group-append" id="button-addon2">
                                    <button class="btn btn-outline-primary" type="button">Sende Angebot</button>
                                </div>
                            </div>
                        </fieldset>
                        <div class="col-12 col-md-7 d-flex flex-column flex-md-row justify-content-end">
                            <button class="btn btn-primary btn-print mb-1 mb-md-0"> <i class="feather icon-file-text"></i> Drucken</button>
                            <button class="btn btn-outline-primary  ml-0 ml-md-1"> <i class="feather icon-download"></i> Download</button>
                        </div>
                    </div>
                </section>
                <!-- invoice functionality end -->
                
                <!-- invoice page -->
                <section class="card invoice-page">
                    <div id="invoice-template" class="card-body">
                            <!-- Cover Letter -->
                            <div class="col-xl-12">
                                <div class="card" > 
                                    <div class="card-content">
                                        <img  src="{{ asset('images/offer/cover/1715601510.jpg') }}" id="cover" style=""> 
                                    </div> 
                                </div>
                            </div>

                            <div class="new-page"></div>
                            <!-- End of Cover Letter -->


                        <!-- Invoice Company Details -->
                        <div id="invoice-company-details" class="row">
                            <div class="col-sm-12 col-12 text-right pt-1" style="padding-bottom: 11px;margin-top: 10px;margin-bottom: -16px;border-bottom: 3px solid #95c11c;">
                                <div class="media pt-1" style="    float: right;">
                                    <img src="{{ asset('logo/logo.png')}}" alt="company logo" />
                                </div>
                            </div>

                            <div class="col-sm-12 col-12 text-right pt-1" style="padding-bottom: 26px;margin-top: 1;margin-bottom: 17px;color:#95c11c;">
                                <div class="media pt-1" style="    float: right;">
                                 <h3 style=" color:#95c11c;     font-weight: bold;">WE LOVE THE SUN.</h3>
                                </div>
                            </div>
                        
                        
                        </div>
                        <!--/ Invoice Company Details -->

                        <!-- Invoice Recipient Details -->
                        <div id="invoice-customer-details" class="row pt-2" style="margin-left: 20px !important; margin-right: 20px !important;margin-bottom: 99px;">
                            <div class="col-sm-6 col-12 text-left">
                                <h5></h5>
                                <div class="recipient-info my-2">
                                    <p>Herr</p>
                                    <p>Peter Stark</p>
                                    <p>8577 West West Drive</p>
                                    <p>Holbrook, NY</p>
                                    <p>90001</p>
                                </div>
                                <div class="recipient-contact pb-2">
                                    <p>
                                        <i class="feather icon-mail"></i>
                                        peter@mail.com
                                    </p>
                                    <p>
                                        <i class="feather icon-phone"></i>
                                        +91 988 888 8888
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6 col-12 text-right">
                                <h5 style="color: #95c11c;">Ihr Ansprechpartner</h5>
                                <div class="company-info my-2">
                                    <p>Yama Nuri</p>
                                    
                                </div>
                                <div class="company-contact">
                                    <p>
                                        <i class="feather icon-mail"></i>
                                        hello@solar-aspekt.com
                                    </p>
                                    <p>
                                        <i class="feather icon-phone"></i>
                                        +91 999 999 9999
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!--/ Invoice Recipient Details -->
                        
                        {{-- Subject Details: --}}
                        <div id="invoice-items-details" class="pt-1 invoice-items-table" style="margin-left: 131px !important;margin-right: 131px !important;margin-bottom: 163px;">
                            <div class="row" >
                                 @include('admin.offer.print.subject')
                            </div>
                        </div>
                        {{-- Subject Details: Ending --}}

                        <div class="new-page"></div>

                        {{-- Product Details: Start --}}
                        <div id="invoice-items-details" class="pt-1 invoice-items-table"
                            style="margin-left: 131px !important;margin-right: 131px !important;margin-bottom: 163px;">
                            <div class="row">
                                @include('admin.offer.print.products')
                            </div>
                        </div>
                            {{-- Product Details: Ending --}}
                        <div class="new-page"></div>


                        <!-- Invoice Items Details -->
                        <div id="invoice-items-details" class="pt-1 invoice-items-table">
                            <div class="row">
                                <div class="table-responsive col-12">
                                    <table class="table table-borderless">
                                        <thead>
                                            <tr>
                                                <th>TASK DESCRIPTION</th>
                                                <th>HOURS</th>
                                                <th>RATE</th>
                                                <th>AMOUNT</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Website Redesign</td>
                                                <td>60</td>
                                                <td>15 USD</td>
                                                <td>90000 USD</td>
                                            </tr>
                                            <tr>
                                                <td>Newsletter template design</td>
                                                <td>20</td>
                                                <td>12 USD</td>
                                                <td>24000 USD</td>
                                            </tr>
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
                                                    <th>SUBTOTAL</th>
                                                    <td>114000 USD</td>
                                                </tr>
                                                <tr>
                                                    <th>DISCOUNT (5%)</th>
                                                    <td>5700 USD</td>
                                                </tr>
                                                <tr>
                                                    <th>TOTAL</th>
                                                    <td>108300 USD</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Footer -->
                        <div id="invoice-footer" class="text-right pt-3">
                            <p>Transfer the amounts to the business amount below. Please include invoice number on your check.
                                <p class="bank-details mb-0">
                                    <span class="mr-4">BANK: <strong>FTSBUS33</strong></span>
                                    <span>IBAN: <strong>G882-1111-2222-3333</strong></span>
                                </p>
                        </div>
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

        <!-- BEGIN: Vendor JS-->
        <script src="{{ asset('app-assets/vendors/js/vendors.min.js')}}"></script>
        <!-- BEGIN Vendor JS-->
    
        <!-- BEGIN: Page Vendor JS-->
        <script src="{{ asset('app-assets/vendors/js/ui/jquery.sticky.js')}}"></script>
        <!-- END: Page Vendor JS-->
    
    
        <!-- BEGIN: Page JS-->
        <script src="{{ asset('app-assets/js/scripts/pages/invoice.js')}}"></script>
        <!-- END: Page JS-->
    @endsection
