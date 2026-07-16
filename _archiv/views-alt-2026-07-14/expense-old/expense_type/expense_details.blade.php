@extends('admin.layouts.app')
@section('title') Ausgabenarten @stop
@section('style')

<style>
    
    .slide {
        position: fixed;
        top: 143px;
        right: -100%; /* Start hidden off the right side */
        background: #f5f5f5;
        width: 61%;
        transition: right 0.5s ease; /* Smooth animation */
        z-index: 1000;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .slide.show {
        right: 9px; /* Slide in from the right */
    }

    
    .close-slider { 
        background: #de1313;
        color: white;
        border: 0;
        height: 56px;
        border-radius: 10px 0 0 10px;
    }

    .slide-panel {
    position: fixed;
        top: 143px;
        right: -100%; /* Start hidden off the right side */
        background: #f5f5f5;
        width: 61%;
        transition: right 0.5s ease; /* Smooth animation */
        z-index: 1000;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .slide-panel.show {
        right: 9px; /* Slide in from the right */
    }

    .close {
        position: relative;
        float: left;
    }

    #close_slider_save {
        background: #de1313;
        color: white;
        border: 0;
        height: 56px;
        border-radius: 10px 0 0 10px;
    }


    .select2-selection--single {
            background: transparent !important;
        height: 81px !important;
    }
    .select2-results__option {
        height:106px !important;
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
                            <h2 class="content-header-title float-left mb-0">Filialkosten</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a>
                                    </li> 
                                    <li class="breadcrumb-item "> {{ $data->branch }}
                                    </li>
                                      <li class="breadcrumb-item active"> {{ request()->year }}
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
                  @php
                    $active = old('active_tab', 'account-pill-general');
                @endphp
            <div class="content-body">
                <!-- account setting page start -->
                <section id="page-account-settings">
                    <div class="row">
                        <!-- left menu section -->
                        <div class="col-md-3 mb-2 mb-md-0">
                            <ul class="nav nav-pills flex-column mt-md-0 mt-1">
                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75 active" id="account-pill-general" data-toggle="pill" href="#account-vertical-general" aria-expanded="true">
                                        <i class="feather icon-command mr-50 font-medium-3"></i>
                                        Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75 {{ $active == 'rent' ? 'active' : '' }}" id="rentObject" data-toggle="pill" href="#account-vertical-rent" aria-expanded="true">
                                        <i class="fa fa-home mr-50 font-medium-3"></i>
                                        Mietobjekte
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75" id="account-pill-password" data-toggle="pill" href="#account-vertical-password" aria-expanded="false">
                                        <i class="feather icon-users mr-50 font-medium-3"></i>
                                        Personalkosten
                                    </a>
                                </li> 
                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75" id="account-pill-social" data-toggle="pill" href="#account-vertical-social" aria-expanded="false">
                                        <i class="fa-car mr-50 font-medium-3"></i>
                                        Fahrzeugkosten
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75" id="account-pill-connections" data-toggle="pill" href="#account-vertical-connections" aria-expanded="false">
                                        <i class="fa fa-joomla mr-50 font-medium-3"></i>
                                       Versicherungen
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75" id="account-pill-notifications" data-toggle="pill" href="#account-vertical-notifications" aria-expanded="false">
                                        <i class="fa fa-calculator mr-50 font-medium-3"></i>
                                        Kreditverbindlichkeiten
                                    </a>
                                </li> 

                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75" id="account-pill-notifications" data-toggle="pill" href="#account-vertical-notifications" aria-expanded="false">
                                        <i class="feather icon-message-circle mr-50 font-medium-3"></i>
                                        Abschreibungen
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75" id="account-pill-notifications" data-toggle="pill" href="#account-vertical-notifications" aria-expanded="false">
                                        <i class="fa fa-line-chart mr-50 font-medium-3"></i>
                                        Verwaltungskosten
                                    </a>
                                </li>

                                  <li class="nav-item">
                                    <a class="nav-link d-flex py-75" id="account-pill-notifications" data-toggle="pill" href="#account-vertical-notifications" aria-expanded="false">
                                        <i class="fa fa-handshake-o mr-50 font-medium-3"></i>
                                        Rechts- und Beratungskosten
                                    </a>
                                </li>
                                 <li class="nav-item">
                                    <a class="nav-link d-flex py-75" id="account-pill-notifications" data-toggle="pill" href="#account-vertical-notifications" aria-expanded="false">
                                        <i class="fa fa-edge mr-50 font-medium-3"></i>
                                        Marketing und Werbekosten
                                    </a>
                                </li>
                                 <li class="nav-item">
                                    <a class="nav-link d-flex py-75" id="account-pill-notifications" data-toggle="pill" href="#account-vertical-notifications" aria-expanded="false">
                                        <i class="fa fa-server mr-50 font-medium-3"></i>
                                        IT- und Kommunikationskosten
                                    </a>
                                </li>
                                 <li class="nav-item">
                                    <a class="nav-link d-flex py-75" id="account-pill-notifications" data-toggle="pill" href="#account-vertical-notifications" aria-expanded="false">
                                        <i class="fa fa-leanpub mr-50 font-medium-3"></i>
                                        Fortbildung und Schulungskosten
                                    </a>
                                </li>
                                 <li class="nav-item">
                                    <a class="nav-link d-flex py-75" id="account-pill-notifications" data-toggle="pill" href="#account-vertical-notifications" aria-expanded="false">
                                        <i class="fa fa-gg-circle mr-50 font-medium-3"></i>
                                        Mitgliedschaften und Gebühren
                                    </a>
                                </li>
                                 <li class="nav-item">
                                    <a class="nav-link d-flex py-75" id="account-pill-notifications" data-toggle="pill" href="#account-vertical-notifications" aria-expanded="false">
                                        <i class="fa fa-paint-brush mr-50 font-medium-3"></i>
                                       Reinigung und Wartung
                                    </a>
                                </li>
 
                            </ul>
                        </div>
                        <!-- right content section -->
                        <div class="col-md-9">
                            <div class="cards">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <div role="tabpanel" class="tab-pane active" id="account-vertical-general" aria-labelledby="account-pill-general" aria-expanded="true">
                                        
                                                    <div class="row">
                                                        <div class="col-xl-2 col-md-4 col-sm-6">
                                                            <div class="card text-center">
                                                                <div class="card-content">
                                                                    <div class="card-body">
                                                                        <div class="avatar bg-rgba-info p-50 m-0 mb-1">
                                                                            <div class="avatar-content">
                                                                                <i class="feather icon-eye text-info font-medium-5"></i>
                                                                            </div>
                                                                        </div>
                                                                        <h2 class="text-bold-700">36.9k</h2>
                                                                        <p class="mb-0 line-ellipsis">Views</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-2 col-md-4 col-sm-6">
                                                            <div class="card text-center">
                                                                <div class="card-content">
                                                                    <div class="card-body">
                                                                        <div class="avatar bg-rgba-warning p-50 m-0 mb-1">
                                                                            <div class="avatar-content">
                                                                                <i class="feather icon-message-square text-warning font-medium-5"></i>
                                                                            </div>
                                                                        </div>
                                                                        <h2 class="text-bold-700">12k</h2>
                                                                        <p class="mb-0 line-ellipsis">Comments</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-2 col-md-4 col-sm-6">
                                                            <div class="card text-center">
                                                                <div class="card-content">
                                                                    <div class="card-body">
                                                                        <div class="avatar bg-rgba-danger p-50 m-0 mb-1">
                                                                            <div class="avatar-content">
                                                                                <i class="feather icon-shopping-bag text-danger font-medium-5"></i>
                                                                            </div>
                                                                        </div>
                                                                        <h2 class="text-bold-700">97.8k</h2>
                                                                        <p class="mb-0 line-ellipsis">Orders</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-2 col-md-4 col-sm-6">
                                                            <div class="card text-center">
                                                                <div class="card-content">
                                                                    <div class="card-body">
                                                                        <div class="avatar bg-rgba-primary p-50 m-0 mb-1">
                                                                            <div class="avatar-content">
                                                                                <i class="feather icon-heart text-primary font-medium-5"></i>
                                                                            </div>
                                                                        </div>
                                                                        <h2 class="text-bold-700">26.8</h2>
                                                                        <p class="mb-0 line-ellipsis">Bookmarks</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-2 col-md-4 col-sm-6">
                                                            <div class="card text-center">
                                                                <div class="card-content">
                                                                    <div class="card-body">
                                                                        <div class="avatar bg-rgba-success p-50 m-0 mb-1">
                                                                            <div class="avatar-content">
                                                                                <i class="feather icon-award text-success font-medium-5"></i>
                                                                            </div>
                                                                        </div>
                                                                        <h2 class="text-bold-700">689</h2>
                                                                        <p class="mb-0 line-ellipsis">Reviews</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-2 col-md-4 col-sm-6">
                                                            <div class="card text-center">
                                                                <div class="card-content">
                                                                    <div class="card-body">
                                                                        <div class="avatar bg-rgba-danger p-50 m-0 mb-1">
                                                                            <div class="avatar-content">
                                                                                <i class="feather icon-truck text-danger font-medium-5"></i>
                                                                            </div>
                                                                        </div>
                                                                        <h2 class="text-bold-700">2.1k</h2>
                                                                        <p class="mb-0 line-ellipsis">Returns</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-lg-3 col-sm-6 col-12">
                                                            <div class="card">
                                                                <div class="card-header d-flex flex-column align-items-start pb-0">
                                                                    <div class="avatar bg-rgba-primary p-50 m-0">
                                                                        <div class="avatar-content">
                                                                            <i class="feather icon-users text-primary font-medium-5"></i>
                                                                        </div>
                                                                    </div>
                                                                    <h2 class="text-bold-700 mt-1">92.6k</h2>
                                                                    <p class="mb-0">Subscribers Gained</p>
                                                                </div>
                                                                <div class="card-content" style="position: relative;">
                                                                    <div id="line-area-chart-1" style="min-height: 100px;"><div id="apexchartsq33e3iye" class="apexcharts-canvas apexchartsq33e3iye light" style="width: 406px; height: 100px;"><svg id="SvgjsSvg1006" width="406" height="100" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)" style="background: transparent;"><g id="SvgjsG1008" class="apexcharts-inner apexcharts-graphical" transform="translate(0, 0)"><defs id="SvgjsDefs1007"><clipPath id="gridRectMaskq33e3iye"><rect id="SvgjsRect1012" width="408.5" height="102.5" x="-1.25" y="-1.25" rx="0" ry="0" fill="#ffffff" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0"></rect></clipPath><clipPath id="gridRectMarkerMaskq33e3iye"><rect id="SvgjsRect1013" width="408" height="102" x="-1" y="-1" rx="0" ry="0" fill="#ffffff" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0"></rect></clipPath><linearGradient id="SvgjsLinearGradient1019" x1="0" y1="0" x2="0" y2="1"><stop id="SvgjsStop1020" stop-opacity="0.7" stop-color="rgba(115,103,240,0.7)" offset="0"></stop><stop id="SvgjsStop1021" stop-opacity="0.5" stop-color="rgba(241,240,254,0.5)" offset="0.8"></stop><stop id="SvgjsStop1022" stop-opacity="0.5" stop-color="rgba(241,240,254,0.5)" offset="1"></stop></linearGradient></defs><line id="SvgjsLine1011" x1="0" y1="0" x2="0" y2="100" stroke="#b6b6b6" stroke-dasharray="3" class="apexcharts-xcrosshairs" x="0" y="0" width="1" height="100" fill="#b1b9c4" filter="none" fill-opacity="0.9" stroke-width="1"></line><g id="SvgjsG1025" class="apexcharts-xaxis" transform="translate(0, 0)"><g id="SvgjsG1026" class="apexcharts-xaxis-texts-g" transform="translate(0, -4)"></g></g><g id="SvgjsG1029" class="apexcharts-grid"><line id="SvgjsLine1031" x1="0" y1="100" x2="406" y2="100" stroke="transparent" stroke-dasharray="0"></line><line id="SvgjsLine1030" x1="0" y1="1" x2="0" y2="100" stroke="transparent" stroke-dasharray="0"></line></g><g id="SvgjsG1015" class="apexcharts-area-series apexcharts-plot-series"><g id="SvgjsG1016" class="apexcharts-series" seriesName="Subscribers" data:longestSeries="true" rel="1" data:realIndex="0"><path id="SvgjsPath1023" d="M0 100L0 77.77777777777777C23.683333333333334 77.77777777777777 43.983333333333334 51.111111111111114 67.66666666666667 51.111111111111114C91.35000000000001 51.111111111111114 111.65 60 135.33333333333334 60C159.01666666666668 60 179.31666666666666 24.444444444444443 203 24.444444444444443C226.68333333333334 24.444444444444443 246.98333333333335 55.55555555555556 270.6666666666667 55.55555555555556C294.35 55.55555555555556 314.65000000000003 6.666666666666657 338.33333333333337 6.666666666666657C362.0166666666667 6.666666666666657 382.31666666666666 17.777777777777786 406 17.777777777777786C406 17.777777777777786 406 17.777777777777786 406 100M406 17.777777777777786C406 17.777777777777786 406 17.777777777777786 406 17.777777777777786 " fill="url(#SvgjsLinearGradient1019)" fill-opacity="1" stroke-opacity="1" stroke-linecap="butt" stroke-width="0" stroke-dasharray="0" class="apexcharts-area" index="0" clip-path="url(#gridRectMaskq33e3iye)" pathTo="M 0 100L 0 77.77777777777777C 23.683333333333334 77.77777777777777 43.983333333333334 51.111111111111114 67.66666666666667 51.111111111111114C 91.35000000000001 51.111111111111114 111.65 60 135.33333333333334 60C 159.01666666666668 60 179.31666666666666 24.444444444444443 203 24.444444444444443C 226.68333333333334 24.444444444444443 246.98333333333335 55.55555555555556 270.6666666666667 55.55555555555556C 294.35 55.55555555555556 314.65000000000003 6.666666666666657 338.33333333333337 6.666666666666657C 362.0166666666667 6.666666666666657 382.31666666666666 17.777777777777786 406 17.777777777777786C 406 17.777777777777786 406 17.777777777777786 406 100M 406 17.777777777777786z" pathFrom="M -1 140L -1 140L 67.66666666666667 140L 135.33333333333334 140L 203 140L 270.6666666666667 140L 338.33333333333337 140L 406 140"></path><path id="SvgjsPath1024" d="M0 77.77777777777777C23.683333333333334 77.77777777777777 43.983333333333334 51.111111111111114 67.66666666666667 51.111111111111114C91.35000000000001 51.111111111111114 111.65 60 135.33333333333334 60C159.01666666666668 60 179.31666666666666 24.444444444444443 203 24.444444444444443C226.68333333333334 24.444444444444443 246.98333333333335 55.55555555555556 270.6666666666667 55.55555555555556C294.35 55.55555555555556 314.65000000000003 6.666666666666657 338.33333333333337 6.666666666666657C362.0166666666667 6.666666666666657 382.31666666666666 17.777777777777786 406 17.777777777777786C406 17.777777777777786 406 17.777777777777786 406 17.777777777777786 " fill="none" fill-opacity="1" stroke="#7367f0" stroke-opacity="1" stroke-linecap="butt" stroke-width="2.5" stroke-dasharray="0" class="apexcharts-area" index="0" clip-path="url(#gridRectMaskq33e3iye)" pathTo="M 0 77.77777777777777C 23.683333333333334 77.77777777777777 43.983333333333334 51.111111111111114 67.66666666666667 51.111111111111114C 91.35000000000001 51.111111111111114 111.65 60 135.33333333333334 60C 159.01666666666668 60 179.31666666666666 24.444444444444443 203 24.444444444444443C 226.68333333333334 24.444444444444443 246.98333333333335 55.55555555555556 270.6666666666667 55.55555555555556C 294.35 55.55555555555556 314.65000000000003 6.666666666666657 338.33333333333337 6.666666666666657C 362.0166666666667 6.666666666666657 382.31666666666666 17.777777777777786 406 17.777777777777786" pathFrom="M -1 140L -1 140L 67.66666666666667 140L 135.33333333333334 140L 203 140L 270.6666666666667 140L 338.33333333333337 140L 406 140"></path><g id="SvgjsG1017" class="apexcharts-series-markers-wrap"><g class="apexcharts-series-markers"><circle id="SvgjsCircle1037" r="0" cx="0" cy="77.77777777777777" class="apexcharts-marker wbj61c1m6 no-pointer-events" stroke="#ffffff" fill="#7367f0" fill-opacity="1" stroke-width="2" stroke-opacity="0.9" default-marker-size="0"></circle></g></g><g id="SvgjsG1018" class="apexcharts-datalabels"></g></g></g><line id="SvgjsLine1032" x1="0" y1="0" x2="406" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="1" class="apexcharts-ycrosshairs"></line><line id="SvgjsLine1033" x1="0" y1="0" x2="406" y2="0" stroke-dasharray="0" stroke-width="0" class="apexcharts-ycrosshairs-hidden"></line><g id="SvgjsG1034" class="apexcharts-yaxis-annotations"></g><g id="SvgjsG1035" class="apexcharts-xaxis-annotations"></g><g id="SvgjsG1036" class="apexcharts-point-annotations"></g></g><rect id="SvgjsRect1010" width="0" height="0" x="0" y="0" rx="0" ry="0" fill="#fefefe" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0"></rect><g id="SvgjsG1027" class="apexcharts-yaxis" rel="0" transform="translate(-21, 0)"><g id="SvgjsG1028" class="apexcharts-yaxis-texts-g"></g></g></svg><div class="apexcharts-legend"></div><div class="apexcharts-tooltip light" style="left: 11px; top: 65.6094px;"><div class="apexcharts-tooltip-series-group active" style="display: flex;"><span class="apexcharts-tooltip-marker" style="background-color: rgb(115, 103, 240);"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-label">Subscribers: </span><span class="apexcharts-tooltip-text-value">28</span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div></div></div></div>
                                                                <div class="resize-triggers"><div class="expand-trigger"><div style="width: 407px; height: 101px;"></div></div><div class="contract-trigger"></div></div></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-sm-6 col-12">
                                                            <div class="card">
                                                                <div class="card-header d-flex flex-column align-items-start pb-0">
                                                                    <div class="avatar bg-rgba-success p-50 m-0">
                                                                        <div class="avatar-content">
                                                                            <i class="feather icon-credit-card text-success font-medium-5"></i>
                                                                        </div>
                                                                    </div>
                                                                    <h2 class="text-bold-700 mt-1">97.5k</h2>
                                                                    <p class="mb-0">Revenue Generated</p>
                                                                </div>
                                                                <div class="card-content" style="position: relative;">
                                                                    <div id="line-area-chart-2" style="min-height: 100px;"><div id="apexchartsjazq1xae" class="apexcharts-canvas apexchartsjazq1xae light" style="width: 406px; height: 100px;"><svg id="SvgjsSvg1038" width="406" height="100" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)" style="background: transparent;"><g id="SvgjsG1040" class="apexcharts-inner apexcharts-graphical" transform="translate(0, 0)"><defs id="SvgjsDefs1039"><clipPath id="gridRectMaskjazq1xae"><rect id="SvgjsRect1044" width="408.5" height="102.5" x="-1.25" y="-1.25" rx="0" ry="0" fill="#ffffff" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0"></rect></clipPath><clipPath id="gridRectMarkerMaskjazq1xae"><rect id="SvgjsRect1045" width="408" height="102" x="-1" y="-1" rx="0" ry="0" fill="#ffffff" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0"></rect></clipPath><linearGradient id="SvgjsLinearGradient1051" x1="0" y1="0" x2="0" y2="1"><stop id="SvgjsStop1052" stop-opacity="0.7" stop-color="rgba(40,199,111,0.7)" offset="0"></stop><stop id="SvgjsStop1053" stop-opacity="0.5" stop-color="rgba(234,249,241,0.5)" offset="0.8"></stop><stop id="SvgjsStop1054" stop-opacity="0.5" stop-color="rgba(234,249,241,0.5)" offset="1"></stop></linearGradient></defs><line id="SvgjsLine1043" x1="202.5" y1="0" x2="202.5" y2="100" stroke="#b6b6b6" stroke-dasharray="3" class="apexcharts-xcrosshairs" x="202.5" y="0" width="1" height="100" fill="#b1b9c4" filter="none" fill-opacity="0.9" stroke-width="1"></line><g id="SvgjsG1057" class="apexcharts-xaxis" transform="translate(0, 0)"><g id="SvgjsG1058" class="apexcharts-xaxis-texts-g" transform="translate(0, -4)"></g></g><g id="SvgjsG1061" class="apexcharts-grid"><line id="SvgjsLine1063" x1="0" y1="100" x2="406" y2="100" stroke="transparent" stroke-dasharray="0"></line><line id="SvgjsLine1062" x1="0" y1="1" x2="0" y2="100" stroke="transparent" stroke-dasharray="0"></line></g><g id="SvgjsG1047" class="apexcharts-area-series apexcharts-plot-series"><g id="SvgjsG1048" class="apexcharts-series" seriesName="Revenue" data:longestSeries="true" rel="1" data:realIndex="0"><path id="SvgjsPath1055" d="M0 100L0 60C23.683333333333334 60 43.983333333333334 90 67.66666666666667 90C91.35000000000001 90 111.65 40 135.33333333333334 40C159.01666666666668 40 179.31666666666666 80 203 80C226.68333333333334 80 246.98333333333335 60 270.6666666666667 60C294.35 60 314.65000000000003 80 338.33333333333337 80C362.0166666666667 80 382.31666666666666 20 406 20C406 20 406 20 406 100M406 20C406 20 406 20 406 20 " fill="url(#SvgjsLinearGradient1051)" fill-opacity="1" stroke-opacity="1" stroke-linecap="butt" stroke-width="0" stroke-dasharray="0" class="apexcharts-area" index="0" clip-path="url(#gridRectMaskjazq1xae)" pathTo="M 0 100L 0 60C 23.683333333333334 60 43.983333333333334 90 67.66666666666667 90C 91.35000000000001 90 111.65 40 135.33333333333334 40C 159.01666666666668 40 179.31666666666666 80 203 80C 226.68333333333334 80 246.98333333333335 60 270.6666666666667 60C 294.35 60 314.65000000000003 80 338.33333333333337 80C 362.0166666666667 80 382.31666666666666 20 406 20C 406 20 406 20 406 100M 406 20z" pathFrom="M -1 200L -1 200L 67.66666666666667 200L 135.33333333333334 200L 203 200L 270.6666666666667 200L 338.33333333333337 200L 406 200"></path><path id="SvgjsPath1056" d="M0 60C23.683333333333334 60 43.983333333333334 90 67.66666666666667 90C91.35000000000001 90 111.65 40 135.33333333333334 40C159.01666666666668 40 179.31666666666666 80 203 80C226.68333333333334 80 246.98333333333335 60 270.6666666666667 60C294.35 60 314.65000000000003 80 338.33333333333337 80C362.0166666666667 80 382.31666666666666 20 406 20C406 20 406 20 406 20 " fill="none" fill-opacity="1" stroke="#28c76f" stroke-opacity="1" stroke-linecap="butt" stroke-width="2.5" stroke-dasharray="0" class="apexcharts-area" index="0" clip-path="url(#gridRectMaskjazq1xae)" pathTo="M 0 60C 23.683333333333334 60 43.983333333333334 90 67.66666666666667 90C 91.35000000000001 90 111.65 40 135.33333333333334 40C 159.01666666666668 40 179.31666666666666 80 203 80C 226.68333333333334 80 246.98333333333335 60 270.6666666666667 60C 294.35 60 314.65000000000003 80 338.33333333333337 80C 362.0166666666667 80 382.31666666666666 20 406 20" pathFrom="M -1 200L -1 200L 67.66666666666667 200L 135.33333333333334 200L 203 200L 270.6666666666667 200L 338.33333333333337 200L 406 200"></path><g id="SvgjsG1049" class="apexcharts-series-markers-wrap"><g class="apexcharts-series-markers"><circle id="SvgjsCircle1069" r="0" cx="203" cy="80" class="apexcharts-marker wu0dt8dkrl no-pointer-events" stroke="#ffffff" fill="#28c76f" fill-opacity="1" stroke-width="2" stroke-opacity="0.9" default-marker-size="0"></circle></g></g><g id="SvgjsG1050" class="apexcharts-datalabels"></g></g></g><line id="SvgjsLine1064" x1="0" y1="0" x2="406" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="1" class="apexcharts-ycrosshairs"></line><line id="SvgjsLine1065" x1="0" y1="0" x2="406" y2="0" stroke-dasharray="0" stroke-width="0" class="apexcharts-ycrosshairs-hidden"></line><g id="SvgjsG1066" class="apexcharts-yaxis-annotations"></g><g id="SvgjsG1067" class="apexcharts-xaxis-annotations"></g><g id="SvgjsG1068" class="apexcharts-point-annotations"></g></g><rect id="SvgjsRect1042" width="0" height="0" x="0" y="0" rx="0" ry="0" fill="#fefefe" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0"></rect><g id="SvgjsG1059" class="apexcharts-yaxis" rel="0" transform="translate(-21, 0)"><g id="SvgjsG1060" class="apexcharts-yaxis-texts-g"></g></g></svg><div class="apexcharts-legend"></div><div class="apexcharts-tooltip light" style="left: 67.5781px; top: 65.6094px;"><div class="apexcharts-tooltip-series-group active" style="display: flex;"><span class="apexcharts-tooltip-marker" style="background-color: rgb(40, 199, 111);"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-label">Revenue: </span><span class="apexcharts-tooltip-text-value">300</span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div></div></div></div>
                                                                <div class="resize-triggers"><div class="expand-trigger"><div style="width: 407px; height: 101px;"></div></div><div class="contract-trigger"></div></div></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-sm-6 col-12">
                                                            <div class="card">
                                                                <div class="card-header d-flex flex-column align-items-start pb-0">
                                                                    <div class="avatar bg-rgba-danger p-50 m-0">
                                                                        <div class="avatar-content">
                                                                            <i class="feather icon-shopping-cart text-danger font-medium-5"></i>
                                                                        </div>
                                                                    </div>
                                                                    <h2 class="text-bold-700 mt-1">36%</h2>
                                                                    <p class="mb-0">Quarterly Sales</p>
                                                                </div>
                                                                <div class="card-content" style="position: relative;">
                                                                    <div id="line-area-chart-3" style="min-height: 100px;"><div id="apexchartsq1tu963m" class="apexcharts-canvas apexchartsq1tu963m light" style="width: 406px; height: 100px;"><svg id="SvgjsSvg1070" width="406" height="100" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)" style="background: transparent;"><g id="SvgjsG1072" class="apexcharts-inner apexcharts-graphical" transform="translate(0, 0)"><defs id="SvgjsDefs1071"><clipPath id="gridRectMaskq1tu963m"><rect id="SvgjsRect1076" width="408.5" height="102.5" x="-1.25" y="-1.25" rx="0" ry="0" fill="#ffffff" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0"></rect></clipPath><clipPath id="gridRectMarkerMaskq1tu963m"><rect id="SvgjsRect1077" width="408" height="102" x="-1" y="-1" rx="0" ry="0" fill="#ffffff" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0"></rect></clipPath><linearGradient id="SvgjsLinearGradient1083" x1="0" y1="0" x2="0" y2="1"><stop id="SvgjsStop1084" stop-opacity="0.7" stop-color="rgba(234,84,85,0.7)" offset="0"></stop><stop id="SvgjsStop1085" stop-opacity="0.5" stop-color="rgba(253,238,238,0.5)" offset="0.8"></stop><stop id="SvgjsStop1086" stop-opacity="0.5" stop-color="rgba(253,238,238,0.5)" offset="1"></stop></linearGradient></defs><line id="SvgjsLine1075" x1="0" y1="0" x2="0" y2="100" stroke="#b6b6b6" stroke-dasharray="3" class="apexcharts-xcrosshairs" x="0" y="0" width="1" height="100" fill="#b1b9c4" filter="none" fill-opacity="0.9" stroke-width="1"></line><g id="SvgjsG1089" class="apexcharts-xaxis" transform="translate(0, 0)"><g id="SvgjsG1090" class="apexcharts-xaxis-texts-g" transform="translate(0, -4)"></g></g><g id="SvgjsG1093" class="apexcharts-grid"><line id="SvgjsLine1095" x1="0" y1="100" x2="406" y2="100" stroke="transparent" stroke-dasharray="0"></line><line id="SvgjsLine1094" x1="0" y1="1" x2="0" y2="100" stroke="transparent" stroke-dasharray="0"></line></g><g id="SvgjsG1079" class="apexcharts-area-series apexcharts-plot-series"><g id="SvgjsG1080" class="apexcharts-series" seriesName="Sales" data:longestSeries="true" rel="1" data:realIndex="0"><path id="SvgjsPath1087" d="M0 100L0 53.33333333333333C28.419999999999998 53.33333333333333 52.78 20 81.2 20C109.62 20 133.98000000000002 73.33333333333333 162.4 73.33333333333333C190.82 73.33333333333333 215.18 40 243.6 40C272.02 40 296.38 100 324.8 100C353.22 100 377.58 13.333333333333329 406 13.333333333333329C406 13.333333333333329 406 13.333333333333329 406 100M406 13.333333333333329C406 13.333333333333329 406 13.333333333333329 406 13.333333333333329 " fill="url(#SvgjsLinearGradient1083)" fill-opacity="1" stroke-opacity="1" stroke-linecap="butt" stroke-width="0" stroke-dasharray="0" class="apexcharts-area" index="0" clip-path="url(#gridRectMaskq1tu963m)" pathTo="M 0 100L 0 53.33333333333333C 28.419999999999998 53.33333333333333 52.78 20 81.2 20C 109.62 20 133.98000000000002 73.33333333333333 162.4 73.33333333333333C 190.82 73.33333333333333 215.18 40 243.6 40C 272.02 40 296.38 100 324.8 100C 353.22 100 377.58 13.333333333333329 406 13.333333333333329C 406 13.333333333333329 406 13.333333333333329 406 100M 406 13.333333333333329z" pathFrom="M -1 120L -1 120L 81.2 120L 162.4 120L 243.6 120L 324.8 120L 406 120"></path><path id="SvgjsPath1088" d="M0 53.33333333333333C28.419999999999998 53.33333333333333 52.78 20 81.2 20C109.62 20 133.98000000000002 73.33333333333333 162.4 73.33333333333333C190.82 73.33333333333333 215.18 40 243.6 40C272.02 40 296.38 100 324.8 100C353.22 100 377.58 13.333333333333329 406 13.333333333333329C406 13.333333333333329 406 13.333333333333329 406 13.333333333333329 " fill="none" fill-opacity="1" stroke="#ea5455" stroke-opacity="1" stroke-linecap="butt" stroke-width="2.5" stroke-dasharray="0" class="apexcharts-area" index="0" clip-path="url(#gridRectMaskq1tu963m)" pathTo="M 0 53.33333333333333C 28.419999999999998 53.33333333333333 52.78 20 81.2 20C 109.62 20 133.98000000000002 73.33333333333333 162.4 73.33333333333333C 190.82 73.33333333333333 215.18 40 243.6 40C 272.02 40 296.38 100 324.8 100C 353.22 100 377.58 13.333333333333329 406 13.333333333333329" pathFrom="M -1 120L -1 120L 81.2 120L 162.4 120L 243.6 120L 324.8 120L 406 120"></path><g id="SvgjsG1081" class="apexcharts-series-markers-wrap"><g class="apexcharts-series-markers"><circle id="SvgjsCircle1101" r="0" cx="0" cy="0" class="apexcharts-marker wp63psan5h no-pointer-events" stroke="#ffffff" fill="#ea5455" fill-opacity="1" stroke-width="2" stroke-opacity="0.9" default-marker-size="0"></circle></g></g><g id="SvgjsG1082" class="apexcharts-datalabels"></g></g></g><line id="SvgjsLine1096" x1="0" y1="0" x2="406" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="1" class="apexcharts-ycrosshairs"></line><line id="SvgjsLine1097" x1="0" y1="0" x2="406" y2="0" stroke-dasharray="0" stroke-width="0" class="apexcharts-ycrosshairs-hidden"></line><g id="SvgjsG1098" class="apexcharts-yaxis-annotations"></g><g id="SvgjsG1099" class="apexcharts-xaxis-annotations"></g><g id="SvgjsG1100" class="apexcharts-point-annotations"></g></g><rect id="SvgjsRect1074" width="0" height="0" x="0" y="0" rx="0" ry="0" fill="#fefefe" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0"></rect><g id="SvgjsG1091" class="apexcharts-yaxis" rel="0" transform="translate(-21, 0)"><g id="SvgjsG1092" class="apexcharts-yaxis-texts-g"></g></g></svg><div class="apexcharts-legend"></div><div class="apexcharts-tooltip light"><div class="apexcharts-tooltip-series-group"><span class="apexcharts-tooltip-marker" style="background-color: rgb(234, 84, 85);"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-label"></span><span class="apexcharts-tooltip-text-value"></span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div></div></div></div>
                                                                <div class="resize-triggers"><div class="expand-trigger"><div style="width: 407px; height: 101px;"></div></div><div class="contract-trigger"></div></div></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-sm-6 col-12">
                                                            <div class="card">
                                                                <div class="card-header d-flex flex-column align-items-start pb-0">
                                                                    <div class="avatar bg-rgba-warning p-50 m-0">
                                                                        <div class="avatar-content">
                                                                            <i class="feather icon-package text-warning font-medium-5"></i>
                                                                        </div>
                                                                    </div>
                                                                    <h2 class="text-bold-700 mt-1">97.5K</h2>
                                                                    <p class="mb-0">Orders Received</p>
                                                                </div>
                                                                <div class="card-content" style="position: relative;">
                                                                    <div id="line-area-chart-4" style="min-height: 100px;"><div id="apexcharts5wqojz4q" class="apexcharts-canvas apexcharts5wqojz4q light" style="width: 406px; height: 100px;"><svg id="SvgjsSvg1102" width="406" height="100" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)" style="background: transparent;"><g id="SvgjsG1104" class="apexcharts-inner apexcharts-graphical" transform="translate(0, 0)"><defs id="SvgjsDefs1103"><clipPath id="gridRectMask5wqojz4q"><rect id="SvgjsRect1108" width="408.5" height="102.5" x="-1.25" y="-1.25" rx="0" ry="0" fill="#ffffff" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0"></rect></clipPath><clipPath id="gridRectMarkerMask5wqojz4q"><rect id="SvgjsRect1109" width="408" height="102" x="-1" y="-1" rx="0" ry="0" fill="#ffffff" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0"></rect></clipPath><linearGradient id="SvgjsLinearGradient1115" x1="0" y1="0" x2="0" y2="1"><stop id="SvgjsStop1116" stop-opacity="0.7" stop-color="rgba(255,159,67,0.7)" offset="0"></stop><stop id="SvgjsStop1117" stop-opacity="0.5" stop-color="rgba(255,245,236,0.5)" offset="0.8"></stop><stop id="SvgjsStop1118" stop-opacity="0.5" stop-color="rgba(255,245,236,0.5)" offset="1"></stop></linearGradient></defs><line id="SvgjsLine1107" x1="0" y1="0" x2="0" y2="100" stroke="#b6b6b6" stroke-dasharray="3" class="apexcharts-xcrosshairs" x="0" y="0" width="1" height="100" fill="#b1b9c4" filter="none" fill-opacity="0.9" stroke-width="1"></line><g id="SvgjsG1121" class="apexcharts-xaxis" transform="translate(0, 0)"><g id="SvgjsG1122" class="apexcharts-xaxis-texts-g" transform="translate(0, -4)"></g></g><g id="SvgjsG1125" class="apexcharts-grid"><line id="SvgjsLine1127" x1="0" y1="100" x2="406" y2="100" stroke="transparent" stroke-dasharray="0"></line><line id="SvgjsLine1126" x1="0" y1="1" x2="0" y2="100" stroke="transparent" stroke-dasharray="0"></line></g><g id="SvgjsG1111" class="apexcharts-area-series apexcharts-plot-series"><g id="SvgjsG1112" class="apexcharts-series" seriesName="Orders" data:longestSeries="true" rel="1" data:realIndex="0"><path id="SvgjsPath1119" d="M0 100L0 60C23.683333333333334 60 43.983333333333334 10 67.66666666666667 10C91.35000000000001 10 111.65 80 135.33333333333334 80C159.01666666666668 80 179.31666666666666 10 203 10C226.68333333333334 10 246.98333333333335 90 270.6666666666667 90C294.35 90 314.65000000000003 40 338.33333333333337 40C362.0166666666667 40 382.31666666666666 80 406 80C406 80 406 80 406 100M406 80C406 80 406 80 406 80 " fill="url(#SvgjsLinearGradient1115)" fill-opacity="1" stroke-opacity="1" stroke-linecap="butt" stroke-width="0" stroke-dasharray="0" class="apexcharts-area" index="0" clip-path="url(#gridRectMask5wqojz4q)" pathTo="M 0 100L 0 60C 23.683333333333334 60 43.983333333333334 10 67.66666666666667 10C 91.35000000000001 10 111.65 80 135.33333333333334 80C 159.01666666666668 80 179.31666666666666 10 203 10C 226.68333333333334 10 246.98333333333335 90 270.6666666666667 90C 294.35 90 314.65000000000003 40 338.33333333333337 40C 362.0166666666667 40 382.31666666666666 80 406 80C 406 80 406 80 406 100M 406 80z" pathFrom="M -1 160L -1 160L 67.66666666666667 160L 135.33333333333334 160L 203 160L 270.6666666666667 160L 338.33333333333337 160L 406 160"></path><path id="SvgjsPath1120" d="M0 60C23.683333333333334 60 43.983333333333334 10 67.66666666666667 10C91.35000000000001 10 111.65 80 135.33333333333334 80C159.01666666666668 80 179.31666666666666 10 203 10C226.68333333333334 10 246.98333333333335 90 270.6666666666667 90C294.35 90 314.65000000000003 40 338.33333333333337 40C362.0166666666667 40 382.31666666666666 80 406 80C406 80 406 80 406 80 " fill="none" fill-opacity="1" stroke="#ff9f43" stroke-opacity="1" stroke-linecap="butt" stroke-width="2.5" stroke-dasharray="0" class="apexcharts-area" index="0" clip-path="url(#gridRectMask5wqojz4q)" pathTo="M 0 60C 23.683333333333334 60 43.983333333333334 10 67.66666666666667 10C 91.35000000000001 10 111.65 80 135.33333333333334 80C 159.01666666666668 80 179.31666666666666 10 203 10C 226.68333333333334 10 246.98333333333335 90 270.6666666666667 90C 294.35 90 314.65000000000003 40 338.33333333333337 40C 362.0166666666667 40 382.31666666666666 80 406 80" pathFrom="M -1 160L -1 160L 67.66666666666667 160L 135.33333333333334 160L 203 160L 270.6666666666667 160L 338.33333333333337 160L 406 160"></path><g id="SvgjsG1113" class="apexcharts-series-markers-wrap"><g class="apexcharts-series-markers"><circle id="SvgjsCircle1133" r="0" cx="0" cy="0" class="apexcharts-marker w2jqqtluu no-pointer-events" stroke="#ffffff" fill="#ff9f43" fill-opacity="1" stroke-width="2" stroke-opacity="0.9" default-marker-size="0"></circle></g></g><g id="SvgjsG1114" class="apexcharts-datalabels"></g></g></g><line id="SvgjsLine1128" x1="0" y1="0" x2="406" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="1" class="apexcharts-ycrosshairs"></line><line id="SvgjsLine1129" x1="0" y1="0" x2="406" y2="0" stroke-dasharray="0" stroke-width="0" class="apexcharts-ycrosshairs-hidden"></line><g id="SvgjsG1130" class="apexcharts-yaxis-annotations"></g><g id="SvgjsG1131" class="apexcharts-xaxis-annotations"></g><g id="SvgjsG1132" class="apexcharts-point-annotations"></g></g><rect id="SvgjsRect1106" width="0" height="0" x="0" y="0" rx="0" ry="0" fill="#fefefe" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0"></rect><g id="SvgjsG1123" class="apexcharts-yaxis" rel="0" transform="translate(-21, 0)"><g id="SvgjsG1124" class="apexcharts-yaxis-texts-g"></g></g></svg><div class="apexcharts-legend"></div><div class="apexcharts-tooltip light"><div class="apexcharts-tooltip-series-group"><span class="apexcharts-tooltip-marker" style="background-color: rgb(255, 159, 67);"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-label"></span><span class="apexcharts-tooltip-text-value"></span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div></div></div></div>
                                                                <div class="resize-triggers"><div class="expand-trigger"><div style="width: 407px; height: 101px;"></div></div><div class="contract-trigger"></div></div></div>
                                                            </div>
                                                        </div>
                                                    </div> 
                                              
                                            </div>
                                            <div class="tab-pane fade " id="account-vertical-rent" role="tabpanel" aria-labelledby="rentObject" aria-expanded="false">
                                                @include('admin.expense.expense_type.tabs.rent')
                                            </div>
                                            <div class="tab-pane fade" id="account-vertical-info" role="tabpanel" aria-labelledby="account-pill-info" aria-expanded="false">
                                                <form novalidate>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="accountTextarea">Bio</label>
                                                                <textarea class="form-control" id="accountTextarea" rows="3" placeholder="Your Bio data here..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <div class="controls">
                                                                    <label for="account-birth-date">Birth date</label>
                                                                    <input type="text" class="form-control birthdate-picker" required placeholder="Birth date" id="account-birth-date" data-validation-required-message="This birthdate field is required">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="accountSelect">Country</label>
                                                                <select class="form-control" id="accountSelect">
                                                                    <option>USA</option>
                                                                    <option>India</option>
                                                                    <option>Canada</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="languageselect2">Languages</label>
                                                                <select class="form-control" id="languageselect2" multiple="multiple">
                                                                    <option value="English" selected>English</option>
                                                                    <option value="Spanish">Spanish</option>
                                                                    <option value="French">French</option>
                                                                    <option value="Russian">Russian</option>
                                                                    <option value="German">German</option>
                                                                    <option value="Arabic" selected>Arabic</option>
                                                                    <option value="Sanskrit">Sanskrit</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <div class="controls">
                                                                    <label for="account-phone">Phone</label>
                                                                    <input type="text" class="form-control" id="account-phone" required placeholder="Phone number" value="(+656) 254 2568" data-validation-required-message="This phone number field is required">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="account-website">Website</label>
                                                                <input type="text" class="form-control" id="account-website" placeholder="Website address">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="musicselect2">Favourite Music</label>
                                                                <select class="form-control" id="musicselect2" multiple="multiple">
                                                                    <option value="Rock">Rock</option>
                                                                    <option value="Jazz" selected>Jazz</option>
                                                                    <option value="Disco">Disco</option>
                                                                    <option value="Pop">Pop</option>
                                                                    <option value="Techno">Techno</option>
                                                                    <option value="Folk" selected>Folk</option>
                                                                    <option value="Hip hop">Hip hop</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="moviesselect2">Favourite movies</label>
                                                                <select class="form-control" id="moviesselect2" multiple="multiple">
                                                                    <option value="The Dark Knight" selected>The Dark Knight
                                                                    </option>
                                                                    <option value="Harry Potter" selected>Harry Potter</option>
                                                                    <option value="Airplane!">Airplane!</option>
                                                                    <option value="Perl Harbour">Perl Harbour</option>
                                                                    <option value="Spider Man">Spider Man</option>
                                                                    <option value="Iron Man" selected>Iron Man</option>
                                                                    <option value="Avatar">Avatar</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 d-flex flex-sm-row flex-column justify-content-end">
                                                            <button type="submit" class="btn btn-primary mr-sm-1 mb-1 mb-sm-0">Save
                                                                changes</button>
                                                            <button type="reset" class="btn btn-outline-warning">Cancel</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="tab-pane fade " id="account-vertical-social" role="tabpanel" aria-labelledby="account-pill-social" aria-expanded="false">
                                                <form>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="account-twitter">Twitter</label>
                                                                <input type="text" id="account-twitter" class="form-control" placeholder="Add link" value="https://www.twitter.com">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="account-facebook">Facebook</label>
                                                                <input type="text" id="account-facebook" class="form-control" placeholder="Add link">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="account-google">Google+</label>
                                                                <input type="text" id="account-google" class="form-control" placeholder="Add link">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="account-linkedin">LinkedIn</label>
                                                                <input type="text" id="account-linkedin" class="form-control" placeholder="Add link" value="https://www.linkedin.com">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="account-instagram">Instagram</label>
                                                                <input type="text" id="account-instagram" class="form-control" placeholder="Add link">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="account-quora">Quora</label>
                                                                <input type="text" id="account-quora" class="form-control" placeholder="Add link">
                                                            </div>
                                                        </div>
                                                        <div class="col-12 d-flex flex-sm-row flex-column justify-content-end">
                                                            <button type="submit" class="btn btn-primary mr-sm-1 mb-1 mb-sm-0">Save
                                                                changes</button>
                                                            <button type="reset" class="btn btn-outline-warning">Cancel</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="tab-pane fade" id="account-vertical-connections" role="tabpanel" aria-labelledby="account-pill-connections" aria-expanded="false">
                                                <div class="row">
                                                    <div class="col-12 mb-3">
                                                        <a href="javascript: void(0);" class="btn btn-info">Connect to
                                                            <strong>Twitter</strong></a>
                                                    </div>
                                                    <div class="col-12 mb-3">
                                                        <button class=" btn btn-sm btn-secondary float-right">edit</button>
                                                        <h6>You are connected to facebook.</h6>
                                                        <span>Johndoe@gmail.com</span>
                                                    </div>
                                                    <div class="col-12 mb-3">
                                                        <a href="javascript: void(0);" class="btn btn-danger">Connect to
                                                            <strong>Google</strong>
                                                        </a>
                                                    </div>
                                                    <div class="col-12 mb-2">
                                                        <button class=" btn btn-sm btn-secondary float-right">edit</button>
                                                        <h6>You are connected to Instagram.</h6>
                                                        <span>Johndoe@gmail.com</span>
                                                    </div>
                                                    <div class="col-12 d-flex flex-sm-row flex-column justify-content-end">
                                                        <button type="submit" class="btn btn-primary mr-sm-1 mb-1 mb-sm-0">Save
                                                            changes</button>
                                                        <button type="reset" class="btn btn-outline-warning">Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="account-vertical-notifications" role="tabpanel" aria-labelledby="account-pill-notifications" aria-expanded="false">
                                                <div class="row">
                                                    <h6 class="m-1">Activity</h6>
                                                    <div class="col-12 mb-1">
                                                        <div class="custom-control custom-switch custom-control-inline">
                                                            <input type="checkbox" class="custom-control-input" checked id="accountSwitch1">
                                                            <label class="custom-control-label mr-1" for="accountSwitch1"></label>
                                                            <span class="switch-label w-100">Email me when someone comments
                                                                onmy
                                                                article</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mb-1">
                                                        <div class="custom-control custom-switch custom-control-inline">
                                                            <input type="checkbox" class="custom-control-input" checked id="accountSwitch2">
                                                            <label class="custom-control-label mr-1" for="accountSwitch2"></label>
                                                            <span class="switch-label w-100">Email me when someone answers on
                                                                my
                                                                form</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mb-1">
                                                        <div class="custom-control custom-switch custom-control-inline">
                                                            <input type="checkbox" class="custom-control-input" id="accountSwitch3">
                                                            <label class="custom-control-label mr-1" for="accountSwitch3"></label>
                                                            <span class="switch-label w-100">Email me hen someone follows
                                                                me</span>
                                                        </div>
                                                    </div>
                                                    <h6 class="m-1">Application</h6>
                                                    <div class="col-12 mb-1">
                                                        <div class="custom-control custom-switch custom-control-inline">
                                                            <input type="checkbox" class="custom-control-input" checked id="accountSwitch4">
                                                            <label class="custom-control-label mr-1" for="accountSwitch4"></label>
                                                            <span class="switch-label w-100">News and announcements</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mb-1">
                                                        <div class="custom-control custom-switch custom-control-inline">
                                                            <input type="checkbox" class="custom-control-input" id="accountSwitch5">
                                                            <label class="custom-control-label mr-1" for="accountSwitch5"></label>
                                                            <span class="switch-label w-100">Weekly product updates</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mb-1">
                                                        <div class="custom-control custom-switch custom-control-inline">
                                                            <input type="checkbox" class="custom-control-input" checked id="accountSwitch6">
                                                            <label class="custom-control-label mr-1" for="accountSwitch6"></label>
                                                            <span class="switch-label w-100">Weekly blog digest</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 d-flex flex-sm-row flex-column justify-content-end">
                                                        <button type="submit" class="btn btn-primary mr-sm-1 mb-1 mb-sm-0">Save
                                                            changes</button>
                                                        <button type="reset" class="btn btn-outline-warning">Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
 
                         
                    </div>
                </section>
                <!-- account setting page end -->

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


 <!-- Updating new Rent Object: start  -->
    <script>
        $(document).ready(function() {
            $('#branchRentForm').on('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                var formData = new FormData(this); // Get form data

                $.ajax({
                    url: $(this).attr('action'), // Form action URL
                    type: 'POST', // HTTP method
                    data: formData, // Form data
                    processData: false, // Tell jQuery not to process data
                    contentType: false, // Tell jQuery not to set contentType
                    success: function(response) {
                        if (response.success) {
                            // Show Toastr success notification
                            toastr.success(response.message);

                            // Optionally, reload the page or update part of the page after a delay
                            setTimeout(function() {
                                location.reload();
                            }, 2000); // 2 seconds delay before reloading the page
                        } else {
                            // Show Toastr error notification if the request fails
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        // Show a Toastr error notification if the AJAX request fails
                        toastr.error('An error occurred while processing your request.');
                    }
                });
            });
        });
    </script> 
    <!-- Updating new Rent Object: end  -->


    <script>
        $(document).ready(function() {
            $('.rent-btn').on('click', function() {
                var id = $(this).data('id');

                // Hide all sliders
                $('.slide-panel').removeClass('show').css('display', 'none');

                // Show the selected slider with animation
                $('#rentSlide' + id).css('display', 'block');
                setTimeout(function() {
                    $('#rentSlide' + id).addClass('show');
                }, 10); // Small delay to ensure the transition is applied
            });

            // Add close functionality to each slider
            $('.rentClose').on('click', function() {
                var id = $(this).data('id');

                // Hide the specific slider with animation
                $('#rentSlide' + id).removeClass('show');
                setTimeout(function() {
                    $('#rentSlide' + id).css('display', 'none');
                }, 500); // Match the CSS transition duration
            });
        }); 
        </script>



@endsection