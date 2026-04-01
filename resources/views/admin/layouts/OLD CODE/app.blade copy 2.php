<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description"
        content="Solar Aspekt CRM (Customer Relation Management), Ticket System, Lead Management and Solar Management....">
    <meta name="keywords" content="Solar Aspekt, Solar CRM, CMS Solar Aspekt">
    <meta name="author" content="PIXINVENT">
    <title>Solar Aspekt - @yield('title')</title>
    <link rel="apple-touch-icon" href="{{ asset('app-assets/images/ico/apple-icon-120.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('app-assets/images/ico/favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/charts/apexcharts.css')}}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/extensions/tether-theme-arrows.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/tether.min.css')}}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/extensions/shepherd-theme-default.css')}}">
    <!-- END: Vendor CSS-->
    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/katex.min.css')}}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/editors/quill/monokai-sublime.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css')}}">
    <!-- END: Vendor CSS-->
    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap-extended.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/colors.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/components.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/dark-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/semi-dark-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/animate/animate.css')}}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/menu/menu-types/horizontal-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-gradient.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/dashboard-analytics.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/card-analytics.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/tour/tour.css')}}">

    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/extensions/toastr.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/menu/menu-types/vertical-menu.css')}}">

    <!-- END: Page CSS-->
    @yield('style')
 
    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css')}}">
    <!-- END: Custom CSS-->
    @stack('style')
    <style>
     :root {
    --primary-color: #569ad8;
    --secondary-color: #9fbdd8;
    --success-color: #94c11c;
    --danger-color: #cfe09a;
    --warning-color: #ffc107;
    --info-color: #17a2b8;
    --light-color: #f8f9fa;
    --dark-color: #343a40;
}

hr {
    border: none;
    height: 2px;
    background-color: var(--primary-color);
}

.card-container {
    display: flex;
    flex-wrap: nowrap;
    overflow-x: auto;
    padding: 10px;
    gap: 10px;
}

#menu {
    width: 20vw;
    height: 20vw;
    max-width: 150px;
    max-height: 150px;
    background: #b0d5f2;
    border: 5px solid #d9eaf9;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    position: relative;
    transition: all 0.3s ease;
}

#menu:hover {
    background: var(--success-color);
    border: 10px solid var(--danger-color);
    color: white;
}

#menu:hover h6 {
    color: white;
}
.menu-items>h6 {
    color: #74b2d4;
    font-size: 14px;
    font-weight: bolder; 
    text-wrap: balance;
}
 

#menu>.menu-items>p {
    color: white;
    font-size: 10px;
    text-wrap: balance;
}

.menu-items {
    padding: 19px;
    border-radius: 50%;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

#container_new {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    overflow-x: auto;
    white-space: nowrap;
    width: 100%;
    max-height: 200px;
    gap: 21px;
    justify-content: space-evenly;
    margin-top: 134px !important;
}

#container_new::-webkit-scrollbar {
    display: none;
}

#container_items {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    white-space: nowrap;
    width: 100%;
    max-height: 342px;
    gap: 160px;
    justify-content: center;
}

.nav-item {
    flex-shrink: 0;
    margin-right: 10px;
}

.submenu {
    display: none;
    position: absolute;
    top: 114%;
    transform: translateX(-50%);
    background: #f5f5f5;
    border-bottom: 1px solid var(--primary-color);
    padding: 10px;
    left: 50%;
    z-index: 10000;
    border-radius: 6px;
    width: 100%;
    max-height: 300px;
    overflow-y: auto;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: opacity 0.3s ease, visibility 0.3s ease;
    opacity: 0;
    visibility: hidden;
}

.submenu.show {
    display: block;
    opacity: 1;
    visibility: visible;
}

#sub_menu {
    text-align: center;
    width: 10vw;
    height: 10vw;
    max-width: 50px;
    max-height: 50px;
    transition: all 0.3s ease;
}

#sub_menu:hover {
    width: 12vw;
    height: 12vw;
    max-width: 60px;
    max-height: 60px;
}

canvas {
    width: auto !important;
    height: auto;
    max-width: 100%;
}

.item_lists {
    font-size: 26px;
    margin-right: 15px;
    margin-top: -1px;
}
.submenu ul {
    list-style: none; 
    display: flex;
    justify-content: space-evenly;

}

.content-wrapper {
    margin-top: 0.2rem !important;
}

</style>

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->
{{-- @vite('resources/js/app.js') --}}

<body class="vertical-layout vertical-menu-modern 2-columns  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">

    <!-- BEGIN: Header-->
    <nav class="header-navbar navbar-expand-lg navbar navbar-with-menu floating-nav navbar-light navbar-shadow">
        <div class="navbar-wrapper">
            <div class="navbar-container content">
                <div class="navbar-collapse" id="navbar-mobile">
                    <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                        <ul class="nav navbar-nav">
                            <li class="nav-item mobile-menu d-xl-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ficon feather icon-menu"></i></a></li>
                        </ul>
                        <ul class="nav navbar-nav bookmark-icons">
                            <!-- li.nav-item.mobile-menu.d-xl-none.mr-auto-->
                            <!--   a.nav-link.nav-menu-main.menu-toggle.hidden-xs(href='#')-->
                            <!--     i.ficon.feather.icon-menu-->  
                            <li class="nav-item d-none d-lg-block">
                                 <div class="btn-group dropup dropdown-icon-wrapper mt-1  d-lg-block"  > 
                                    <button type="button" class="btn  dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        AUFGABEN
                                    </button>
                                    <div class="dropdown-menu" x-placement="top-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(79px, -7px, 0px);">
                                       <a href="{{ url('task_todo/'.auth()->user()->name ) }}">
                                            <span class="dropdown-item">
                                               MEINE AUFGABEN
                                            </span>
                                        </a>
                                        <span class="dropdown-item">
                                              ALL AUFGABEN
                                        </span>
                                         
                                        <span class="dropdown-item">
                                             ÜBERTRAGENE AUFGABEN
                                        </span>
                                         
                                         
                                    </div>
                                </div> 
                            </li>
                            <li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown"><i class="feather icon-more-horizontal"></i><span data-i18n="Others"> AUFGABEN</span></a>
                                <ul class="dropdown-menu">
                                    <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown" data-i18n="Menu Levels"><i class="feather icon-menu"></i>Menu Levels</a>
                                        <ul class="dropdown-menu">
                                            <li data-menu=""><a class="dropdown-item" href="#" data-toggle="dropdown" data-i18n="Second Level"><i class="feather icon-circle"></i>Second Level</a>
                                            </li>
                                            <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown" data-i18n="Second Level"><i class="feather icon-circle"></i>Second Level</a>
                                                <ul class="dropdown-menu">
                                                    <li data-menu=""><a class="dropdown-item" href="#" data-toggle="dropdown" data-i18n="Third Level"><i class="feather icon-circle"></i>Third Level</a>
                                                    </li>
                                                    <li data-menu=""><a class="dropdown-item" href="#" data-toggle="dropdown" data-i18n="Third Level"><i class="feather icon-circle"></i>Third Level</a>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="disabled" data-menu=""><a class="dropdown-item" href="" data-toggle="dropdown" data-i18n="Disabled Menu"><i class="feather icon-eye-off"></i>Disabled Menu</a>
                                    </li>
                                    <li data-menu=""><a class="dropdown-item" href="https://pixinvent.com/demo/vuexy-html-bootstrap-admin-template/documentation" data-toggle="dropdown" data-i18n="Documentation"><i class="feather icon-folder"></i>Documentation</a>
                                    </li>
                                    <li data-menu=""><a class="dropdown-item" href="https://pixinvent.ticksy.com/" data-toggle="dropdown" data-i18n="Raise Support"><i class="feather icon-life-buoy"></i>Raise Support</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item d-none d-lg-block">
                                 <div class="btn-group dropup dropdown-icon-wrapper mt-1"  > 
                                    <button type="button" class="btn  dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                     KALENDER
                                    </button>
                                    <div class="dropdown-menu" x-placement="left-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(79px, -7px, 0px);">
                                        <span class="dropdown-item">
                                            <i class="ficon feather icon-calender"></i> MEINE KALENDER
                                        </span>
                                        <span class="dropdown-item">
                                            <i class="ficon feather icon-calender"></i> FIRMEN KALENDER
                                        </span>
                                         
                                        <span class="dropdown-item">
                                            <i class="ficon feather icon-calender"></i> KUNDE KALENDER
                                        </span>
                                         
                                         
                                    </div>
                                </div> 
                            </li>
                            <li class="nav-item d-none d-lg-block">
                                 <div class="btn-group mt-1"  > 
                                    <a type="button" class="btn  waves-effect waves-light"  aria-haspopup="true" aria-expanded="false" href="{{ url('chats/'.auth()->user()->name) }}">
                                       CHAT
                                    </a>
                                     
                                </div> 
                            </li>

                            <li class="nav-item d-none d-lg-block">
                                 <div class="btn-group dropup dropdown-icon-wrapper mt-1"  > 
                                    <button type="button" class="btn  dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       ANWESENHEIT
                                    </button>
                                    <div class="dropdown-menu" x-placement="left-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(79px, -7px, 0px);">
                                        <span class="dropdown-item">
                                            <i class="ficon feather icon-calender"></i> MEINE KALENDER
                                        </span>
                                        <span class="dropdown-item">
                                            <i class="ficon feather icon-calender"></i> FIRMEN KALENDER
                                        </span>
                                         
                                        <span class="dropdown-item">
                                            <i class="ficon feather icon-calender"></i> KUNDE KALENDER
                                        </span>
                                         
                                         
                                    </div>
                                </div> 
                            </li>

                            <li class="nav-item d-none d-lg-block">
                                <a >
                                 <div class="btn-group mt-1"  > 
                                    <a type="button" class="btn  waves-effect waves-light" href="{{ url('employee_profile/'.auth()->user()->name) }}"  aria-haspopup="true" aria-expanded="false">
                                         MEIN PROFIL 
                                    </a>
                                </div> 
                            </li>
                        </ul>
               
                    </div>
                    <ul class="nav navbar-nav float-right"> 
                        <li class="nav-item d-none d-lg-block">
                            <div class="clock" id="clock-container"> 
                                <div class="card-content">
                                    <div class="card-body text-center mx-auto" style="border-left: 1px solid #cdcdcd; border-right: 1px solid #cdcdcd;"> 
                                        <div class="d-flex justify-content-between">
                                            <div class="start mr-1">
                                                <p class="font-medium-2">START</p>
                                                <span class="" style="display: flex !important; flex-direction: column;">
                                                    <small>08:15 Uhr</small>
                                                    <small>16:00 Uhr</small>
                                                </span> 
                                            </div>
                                            <div class="button">
                                                <p class="font-weight-bold font-medium-2 mb-0" id="clock">07:15 Std.</p>
                                                <span class=""><i class="fa fa-play primary" style="font-size: 27px;"></i></span>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </li>
                        <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-expand"><i class="ficon feather icon-maximize"></i></a></li>
                        <li class="nav-item nav-search"><a class="nav-link nav-link-search"><i class="ficon feather icon-search"></i></a>
                            <div class="search-input">
                                <div class="search-input-icon"><i class="feather icon-search primary"></i></div>
                                <input class="input" type="text" placeholder="Explore Vuexy..." tabindex="-1" data-search="template-list">
                                <div class="search-input-close"><i class="feather icon-x"></i></div>
                                <ul class="search-list search-list-main"></ul>
                            </div>
                        </li>
                        <li class="dropdown dropdown-notification nav-item">
                            <a class="nav-link nav-link-label" href="#" data-toggle="dropdown">
                                <i class="ficon feather icon-bell"></i><span class="badge badge-pill badge-primary badge-up">5</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                                <li class="dropdown-menu-header">
                                    <div class="dropdown-header m-0 p-2">
                                        <h3 class="white">5 New</h3><span class="notification-title">App Notifications</span>
                                    </div>
                                </li>
                                <li class="scrollable-container media-list"><a class="d-flex justify-content-between" href="javascript:void(0)">
                                        <div class="media d-flex align-items-start">
                                            <div class="media-left"><i class="feather icon-plus-square font-medium-5 primary"></i></div>
                                            <div class="media-body">
                                                <h6 class="primary media-heading">You have new order!</h6><small class="notification-text"> Are your going to meet me tonight?</small>
                                            </div><small>
                                                <time class="media-meta" datetime="2015-06-11T18:29:20+08:00">9 hours ago</time></small>
                                        </div>
                                    </a><a class="d-flex justify-content-between" href="javascript:void(0)">
                                        <div class="media d-flex align-items-start">
                                            <div class="media-left"><i class="feather icon-download-cloud font-medium-5 success"></i></div>
                                            <div class="media-body">
                                                <h6 class="success media-heading red darken-1">99% Server load</h6><small class="notification-text">You got new order of goods.</small>
                                            </div><small>
                                                <time class="media-meta" datetime="2015-06-11T18:29:20+08:00">5 hour ago</time></small>
                                        </div>
                                    </a><a class="d-flex justify-content-between" href="javascript:void(0)">
                                        <div class="media d-flex align-items-start">
                                            <div class="media-left"><i class="feather icon-alert-triangle font-medium-5 danger"></i></div>
                                            <div class="media-body">
                                                <h6 class="danger media-heading yellow darken-3">Warning notifixation</h6><small class="notification-text">Server have 99% CPU usage.</small>
                                            </div><small>
                                                <time class="media-meta" datetime="2015-06-11T18:29:20+08:00">Today</time></small>
                                        </div>
                                    </a><a class="d-flex justify-content-between" href="javascript:void(0)">
                                        <div class="media d-flex align-items-start">
                                            <div class="media-left"><i class="feather icon-check-circle font-medium-5 info"></i></div>
                                            <div class="media-body">
                                                <h6 class="info media-heading">Complete the task</h6><small class="notification-text">Cake sesame snaps cupcake</small>
                                            </div><small>
                                                <time class="media-meta" datetime="2015-06-11T18:29:20+08:00">Last week</time></small>
                                        </div>
                                    </a><a class="d-flex justify-content-between" href="javascript:void(0)">
                                        <div class="media d-flex align-items-start">
                                            <div class="media-left"><i class="feather icon-file font-medium-5 warning"></i></div>
                                            <div class="media-body">
                                                <h6 class="warning media-heading">Generate monthly report</h6><small class="notification-text">Chocolate cake oat cake tiramisu marzipan</small>
                                            </div><small>
                                                <time class="media-meta" datetime="2015-06-11T18:29:20+08:00">Last month</time></small>
                                        </div>
                                    </a></li>
                                <li class="dropdown-menu-footer"><a class="dropdown-item p-1 text-center" href="javascript:void(0)">Read all notifications</a></li>
                            </ul>
                        </li>
                         <li class="dropdown dropdown-user nav-item"><a
                                class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                                <div class="user-nav d-sm-flex d-none"><span class="user-name text-bold-600">{{
                                        DB::table('users')->join('employees', 'employees.id', '=', 'users.name'
                                        )->where('users.name', '=',
                                        auth()->user()->name)->select('employees.name')->pluck('name')->first()
                                        }}</span>
                                    <span class="user-status">verfügbar</span>
                                </div>
                                <span><img class="round" src="{{ asset('images/user/'.auth()->user()->image)}}"
                                        alt="avatar" height="80" width="80"></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item"
                                    href="{{url('/user')}}"><i class="feather icon-user"></i> Edit Profile</a>

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">

                                    <i class="feather icon-power"></i>
                                    {{ __('Logout') }}

                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav> 
    <!-- END: Header-->


    <!-- BEGIN: Main Menu-->
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mr-auto"><a class="navbar-brand" href="{{ url('/') }}">
                        <h4 class="brand-text mb-0"><SMALl>SOLAR-ASPEKT</SMALl></h4>
                    </a></li>
                <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                    <i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i>
                    <i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary" data-ticon="icon-disc"></i></a>
                </li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                 <li class="active"><a href="{{ url('/home') }}"><i class="feather icon-home"></i><span class="menu-item" d >HOME</span></a> </li> 
                 <li class="navigation-header"><span>Apps</span></li>
                    <li class="nav-item">
                        <a href="#"><i class="feather icon-shopping-cart"></i><span class="menu-title">PROJEKTMANAGEMENT</span></a>
                        <ul class="menu-content">  
                            <li class="">
                                <a href="#"><i class="feather icon-folder"></i>SERVICE</a> 
                            </li>
                            <li class="has-sub">
                                <a href="#"><i class="feather icon-folder"></i>TICKETSYSTEM</a>
                                <ul class="menu-content">
                                    <li class="">
                                        <a href="#"><i class="feather icon-user"></i>REPARATUR</a> 
                                    </li>
                                     <li class="">
                                        <a href="#"><i class="feather icon-user"></i>SERVICE</a> 
                                    </li>
                                     <li class="">
                                        <a href="#"><i class="feather icon-user"></i>NOTEDIESTE</a> 
                                    </li> 
                                </ul>
                            </li>
                            <li class="">
                                <a href="#"><i class="feather icon-folder"></i>REGIEARBEITEN</a> 
                            </li>
                            <li class="">
                                <a href="#"><i class="feather icon-folder"></i>WARTUNG</a> 
                            </li>
                            <li class="">
                                <a href="#"><i class="feather icon-folder"></i>KONFIGURATION</a> 
                            </li>
                           
                            <li class="has-sub">
                                <a href="#"><i class="feather icon-folder"></i>ANGEBOT-EINSTELLUNG</a>
                                <ul class="menu-content">
                                    <li class="has-sub">
                                        <a href="#"><i class="feather icon-user"></i>Sets</a>
                                        <ul class="menu-content">
                                            <li><a href="{{ route('article.group.set') }}"><i class="feather icon-circle"></i>Master Set</a></li>
                                            <li><a href="{{ route('group.set.view') }}"><i class="feather icon-circle"></i>Group Set</a></li>
                                        </ul>
                                    </li>
                                    <li class="has-sub">
                                        <a href="#"><i class="feather icon-user"></i>Die Angebotsparagraphen</a>
                                        <ul class="menu-content">
                                            <li><a href="{{ route('offer.greeting.view') }}"><i class="feather icon-circle"></i>Grüße/Betreff</a></li>
                                            <li><a href="{{ route('offer.cover.view') }}"><i class="feather icon-circle"></i>Titelseite</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li> 
                            <li><a href="{{ url('/invoice') }}"><i class="feather icon-file"></i>Rechnung Details</a></li>
                            <li class="has-sub">
                                <a href="#"><i class="fa fa-ticket"></i>Ticket System</a>
                                <ul class="menu-content">
                                    <li><a href="{{ route('problem.create') }}"><i class="fa fa-plus-square-o"></i>Neues Ticket</a></li>
                                    <li><a href="{{ route('problem.view') }}"><i class="feather icon-menu"></i>Tickets Details</a></li>
                                    <li><a href="{{ route('error.info') }}"><i class="feather icon-info"></i>Problemfall</a></li>
                                </ul>
                            </li>
                            <li class="has-sub">
                                <a href="#"><i class="feather icon-settings"></i>Konfiguration</a>
                                <ul class="menu-content">
                                    <li class="has-sub">
                                        <a href="#"><i class="feather icon-paperclip"></i>Aufgaben</a>
                                        <ul class="menu-content">
                                            <li><a href="{{ route('product.position.view') }}"><i class="fa fa-th-list"></i>Aufgabenzuweisung - Produkt</a></li>
                                            <li class="has-sub">
                                                <a href="#"><i class="feather icon-user"></i>Produkt-Task-Konfiguration</a>
                                                <ul class="menu-content">
                                                    <li><a href="{{ route('task.phase') }}"><i class="feather icon-list"></i>Arbeitsschritte Details</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="has-sub">
                                        <a href="#"><i class="feather icon-check-square"></i>Checkliste</a>
                                        <ul class="menu-content">
                                            <li><a href="{{ route('checklist.new') }}"><i class="feather icon-plus-circle"></i>Neue Checkliste</a></li>
                                            <li><a href="{{ route('product.position.view') }}"><i class="feather icon-list"></i>Checkliste-details</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                   
                    @if(DB::table('user_rolls')
                        ->where('user_rolls.user_id', '=', auth()->user()->name)
                        ->where('user_rolls.item_id', '=', 'Product')
                        ->where('user_rolls.is_read', '=', 'on')
                        ->first())

                    <li class="navigation-header"><span>PRODUKT</span></li>
                    <li class="nav-item">
                        <a href="#"><i class="fa fa-product-hunt"></i>Produkt</a>
                        <ul class="menu-content">
                            <li class="has-sub">
                                <a href="#"><i class="fa fa-product-hunt"></i>Produkt</a>
                                <ul class="menu-content">
                                    <li><a href="{{ route('product.info') }}"><i class="feather icon-circle"></i>Produkt Details</a></li>
                                    <li><a href="{{ route('tiles.view') }}"><i class="feather icon-circle"></i>Dacheindeckung</a></li> 
                                    <li><a href="{{ route('distributor.distinct') }}"><i class="feather icon-circle"></i>Produkt unterscheiden</a></li>
                                    <li class="has-sub">
                                        <a href="#"><i class="feather icon-user"></i>Produkteinstellungen</a>
                                        <ul class="menu-content">
                                            <li><a href="{{ route('measure.info') }}"><i class="feather icon-circle"></i>Einheit</a></li>
                                            <li><a href="{{ route('discount_group.info') }}"><i class="feather icon-circle"></i>Rabbat-Gruppe</a></li>
                                            <li><a href="{{ route('article_group.info') }}"><i class="feather icon-circle"></i>Artikel-Gruppe</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="has-sub">
                                <a href="#"><i class="fa fa-qrcode"></i>Inventar</a>
                                <ul class="menu-content">
                                    <li><a href="{{ route('product.create.inventory') }}"><i class="feather icon-circle"></i>Bestandsregistrierung</a></li>
                                    <li><a href="{{ route('product.inventory.search') }}"><i class="feather icon-circle"></i>Inventarsuche</a></li>
                                </ul>
                            </li>
                            <li class="has-sub">
                                <a href="#"><i class="fa fa-truck"></i>Produktlieferung</a>
                                <ul class="menu-content">
                                    <li><a href="{{ route('delivery.note') }}"><i class="feather icon-circle"></i>Lieferschein <div class="badge badge-warning">1</div></a></li>
                                    <li><a href="{{ route('product.inventory.search') }}"><i class="feather icon-circle"></i>Inventarsuche</a></li>
                                </ul>
                            </li>
                            <li class="has-sub">
                                <a href="#"><i class="fa fa-qrcode"></i>Vermögensbestand</a>
                                <ul class="menu-content">
                                    <li><a href="{{ route('assets.inventory') }}"><i class="feather icon-circle"></i>Dateneingabe</a></li>
                                    <li><a href="{{ route('machine.inventory') }}"><i class="feather icon-circle"></i>Auto & Machine</a></li>
                                    <li><a href="{{ route('handover.details') }}"><i class="feather icon-circle"></i>Gegenstände übergeben</a></li>
                                    <li><a href="{{ route('product.inventory.search') }}"><i class="feather icon-circle"></i>Inventarsuche</a></li>
                                </ul>
                            </li>
                            <li class="has-sub">
                                <a href="#"><i class="fa fa-outdent"></i>Ausgehend anfordern</a>
                                <ul class="menu-content">
                                    <li><a href="{{ route('request.out.create') }}"><i class="feather icon-circle"></i>Anfrageformular</a></li>
                                    <li><a href="{{ route('request.out.details') }}"><i class="feather icon-circle"></i>Anfragedetails</a></li>
                                </ul>
                            </li>
                            <li class="has-sub">
                                <a href="#"><i class="fa fa-shopping-cart"></i>Kaufanfrage</a>
                                <ul class="menu-content">
                                    <li><a href="{{ route('purchase.request') }}"><i class="feather icon-circle"></i>Einzelheiten zur Kaufanfrage</a></li>
                                    <li><a href="{{ route('purchase.request.create') }}"><i class="feather icon-circle"></i>Nuen Kaufanfrage</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    @endif

                    @if(DB::table('user_rolls')
                        ->where('user_rolls.user_id', '=', auth()->user()->name)
                        ->where('user_rolls.item_id', '=', 'Partner')
                        ->where('user_rolls.is_read', '=', 'on')
                        ->first())

                    <li class="navigation-header"><span>Partner</span></li> 
                    <li><a href="{{ route('brand.info') }}"><i class="fa fa-building-o"></i>Hersteller</a></li>
                    <li><a href="{{ route('distributor.info') }}"><i class="feather icon-circle"></i>Lieferant</a></li>
                    <li class="nav-item">
                        <a href="#"><i class="fa fa-building"></i>Geschäftspartner</a>
                        <ul class="menu-content">
                            <li><a href="{{ route('brand.sub.contractor') }}"><i class="feather icon-circle"></i>Nach Unternehmer</a></li>
                            <li><a href="{{ route('brand.architect') }}"><i class="feather icon-circle"></i>Archtikten</a></li>
                            <li><a href="{{ route('brand.bank') }}"><i class="feather icon-circle"></i>Bank</a></li>
                            <li><a href="{{ route('brand.insurance') }}"><i class="feather icon-circle"></i>Versicherung</a></li>
                            <li><a href="{{ route('brand.others') }}"><i class="feather icon-circle"></i>Sonstiges</a></li>

                        </ul>
                    </li>
                    @endif

                <li class="navigation-header"><span>PERSONALWESEN</span></li>
                        <li class="nav-item">
                            <a href="#"><i class="feather icon-users"></i>Personal</a>
                            <ul class="menu-content">
                                <li class="has-sub">
                                    <a href="#"><i class="feather icon-copy"></i>Mitarbeiter Details</a>
                                    <ul class="menu-content">
                                        <li><a href="{{ route('emp.create') }}"><i class="feather icon-circle"></i>Mitarbeiter registrieren</a></li>
                                        <li><a href="{{ route('emp.info') }}"><i class="feather icon-circle"></i>Mitarbeiter</a></li>
                                    </ul>
                                </li>
                        
                                <li class="has-sub">
                                    <a href="#"><i class="feather icon-copy"></i>Gehaltsmanagement</a>
                                    <ul class="menu-content">
                                        <li><a href="{{ route('salary.info') }}"><i class="feather icon-circle"></i>Lohn-Vollkosten</a></li>
                                    </ul>
                                </li>
                                <li><a href="{{ route('branch.info') }}"><i class="feather icon-box"></i>Firmen Gruppe</a></li> 
                                <li><a href=" "><i class="feather icon-circle"></i>Bewerber</a></li>
                                <li><a href="{{ route('department.info') }}"><i class="feather icon-package"></i>Abteilung & Berufsbezeichnung</a></li>
                                <li><a href="{{ route('contract.type.info') }}"><i class="feather icon-check-circle"></i>Vertragstyp</a></li>
                                <li><a href="{{ route('language.info') }}"><i class="fa fa-language"></i>Sprachen</a></li>
                                <li><a href="{{ route('country.info') }}"><i class="fa fa-language"></i>Land</a></li>
                                <li class="has-sub">
                                    <a href="#"><i class="feather icon-copy"></i>Feiertagseinstellungen</a>
                                    <ul class="menu-content">
                                        <li><a href="{{ route('holiday.info') }}"><i class="fa fa-toggle-off"></i>Frietags</a></li>
                                        <li><a href="{{ route('leave.day.info') }}"><i class="fa fa-toggle-off"></i>Urlaubstage</a></li>
                                    </ul>
                                </li>
                                <li><a href="{{ route('tax.info') }}"><i class="feather icon-grid"></i>Steuereinstellung</a></li>
                            </ul>
                        </li>



                    @if(DB::table('user_rolls')
                        ->where('user_rolls.user_id', '=', auth()->user()->name)
                        ->where('user_rolls.item_id', '=', 'Invoice')
                        ->where('user_rolls.is_read', '=', 'on')
                        ->first())

                    <li class="navigation-header"><span>Controlling</span></li>
                    <li class="nav-item">
                        <a href="#"><i class="feather icon-file"></i>Controlling</a>
                        <ul class="menu-content">
                            <li><a href="{{ route('invoice.info') }}"><i class="feather icon-file"></i>Rechnung Details</a></li>
                            <li><a href="{{ route('branch.expense') }}"><i class="feather icon-file"></i>Spesenarten für Filialen</a></li>
                            <li><a href="{{ route('assets.installment.show') }}"><i class="feather icon-file"></i>Ratenzahlung</a></li>
                        </ul>
                    </li>
                    @endif


                   <li class="navigation-header"><span>Benutzerverwaltung</span></li>
                    <li class="nav-item">
                        <a href="#"><i class="feather icon-settings"></i>Benutzerverwaltung</a>
                        <ul class="menu-content">
                            <li><a href="{{ url('/admin_user') }}"><i class="feather icon-folder"></i>Admins</a></li>
                            <li><a href="{{ url('/limit_user') }}"><i class="feather icon-folder"></i>Limited</a></li>
                            <li><a href="{{ url('/user_roll') }}"><i class="fa fa-chain-broken"></i>User Roll</a></li>
                            <li><a href="{{ url('/user') }}"><i class="fa fa-user-circle"></i>User Profile</a></li>
                        </ul>
                    </li>

 
                    <li class="nav-item">
                        <a href="#"><i class="feather icon-settings"></i>TOOLS</a>
                        <ul class="menu-content">
                            <li><a href="{{ url('/temp_view') }}"><i class="feather icon-folder"></i>Normaußentemperatur</a></li>
                            <li><a href="{{ url('/customer_phase_manage') }}"><i class="fa fa-th"></i>Phasenmanagement</a></li>
                            <li><a href="{{ url('/radiator_config_view') }}"><i class="feather icon-settings"></i>Heizkörperkonfiguration</a></li>
                            <li class="has-sub">
                                <a href="#"><i class="feather icon-mail"></i><strong>Email</strong></a>
                                <ul class="menu-content">
                                    <li><a href="{{ url('/email_view') }}"><i class="feather icon-circle"></i>Email</a></li>
                                    <li><a href="{{ url('/email_configuration') }}"><i class="feather icon-circle"></i>Email Konfigurator</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li><a href="{{ route('chats.view', auth()->user()->name) }}"><i class="feather icon-message-circle"></i>CHAT</a></li>
                    <li><a href="{{ route('system.feedback.view') }}"><i class="feather icon-info"></i>FEEDBACK</a></li>


            </ul>
        </div> 
    </div> 
 <!-- BEGIN: Circle Menu-->
    <nav>
        <div class="navbar-header">
            <div class="row">
                <div class="col-12">
                    <div class="navbar-wrapper">
                        <div class="navbar-container content">
                            <ul class="nav navbar-nav" id="container_new">

                             <!-- ANFRAGE -->
                                <li class="horizontal_menu_item nav-item" id="menu1">
                                    <a id="menu" href="javascript:void(0);" aria-haspopup="true" aria-expanded="false">
                                        <div class="menu-items">
                                            <h6>ANFRAGE</h6>
                                        </div>
                                    </a>
                                    <div class="submenu">
                                        <ul>
                                            <li>
                                                <a href="{{ route('inquiry.create') }}">
                                                    <i class="fa fa-plus-circle"></i> ANFRAGE ANLEGEN
                                                </a>
                                            </li> 
                                            <li>
                                                <a href="{{ route('inquiry.view') }}">
                                                    <i class="fa fa-list-ol"></i> ANFRAGELISTE
                                                </a>
                                            </li>  
                                            <li>
                                                <a href="{{ url('inquiry_junklist') }}">
                                                    <i class="feather icon-slash"></i> JUNK ANFRAGE
                                                </a>
                                            </li>
                                             <li>
                                                <a href="{{ url('inquiry_type') }}">
                                                    <i class="feather icon-settings"></i> ANFRAGE KONTAKTS ART
                                                </a>
                                            </li>

                                              <li>
                                                <a href="{{ url('inquiry_deleted_list') }}" class="danger">
                                                    <i class="feather icon-trash"></i> GELÖSCHTE ANFRAGEN
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>

                                <!-- LEADS -->
                                <li class="horizontal_menu_item nav-item" id="menu1">
                                    <a id="menu" href="javascript:void(0);" aria-haspopup="true" aria-expanded="false">
                                        <div class="menu-items">
                                            <h6>LEADS</h6>
                                        </div>
                                    </a>
                                    <div class="submenu">
                                        <ul>
                                            <li>
                                                <a href="{{ url('new_lead_create') }}">
                                                    <i class="fa fa-plus-circle"></i> LEADS ANLEGEN
                                                </a>
                                            </li>
                                       
                                            <li>
                                                <a href="{{ url('new_leads') }}" style="text-align: center;">
                                                    <i class="feather icon-refresh-ccw"></i> NEUE LEADS 
                                                    <p class="new" style="color:#56a9d7;"></p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('my_leads') }}">
                                                    <i class="feather icon-refresh-ccw"></i> MEINE LEADS
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('new_lead_view') }}">
                                                    <i class="fa fa-list-ol"></i> ALLE LEADS
                                                </a>
                                            </li> 
                                        
                                            <li>
                                                <a href="{{ url('lead_junks') }}">
                                                    <i class="feather  icon-slash"></i> JUNK LEADS
                                                </a>
                                            </li>

                                            <li>
                                                <a href="{{ url('deleted_leads') }}">
                                                    <i class="feather icon-trash-2"></i> GELÖSCHTE LEADS
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>

                                <!-- PLANUNG -->
                                <li class="horizontal_menu_item nav-item" id="menu2">
                                    <a id="menu" href="javascript:void(0);" aria-haspopup="true" aria-expanded="false">
                                        <div class="menu-items">
                                            <h6>PLANUNG</h6>
                                        </div>
                                    </a>
                                    <div class="submenu">
                                        <ul>
                                            <li>
                                                <a href="{{ route('plan.details') }}">
                                                    <i class="fa fa-plus-circle"></i> NEUE PLANUNG
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('my_leads') }}">
                                                    <i class="feather icon-refresh-ccw"></i> OFFENE PLANUNG
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('new_lead_view') }}">
                                                    <i class="fa fa-list-ol"></i> FERTIGE PLANUNG
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('lead_junks') }}">
                                                    <i class="feather icon-trash-2"></i> ALLE PLANUNGEN
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>

                                <!-- ANGEBOTE -->
                                <li class="horizontal_menu_item nav-item" id="menu4">
                                    <a id="menu" href="javascript:void(0);" aria-haspopup="true" aria-expanded="false">
                                        <div class="menu-items">
                                            <h6>ANGEBOTE</h6>
                                        </div>
                                    </a>
                                    <div class="submenu">
                                        <ul>
                                            <li>
                                                <a href="{{ url('offer_view') }}">
                                                    <i class="fa fa-plus-circle"></i> NEUE ANGEBOTE <br><small><code>Unopened Offers</code></small>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('my_leads') }}">
                                                    <i class="feather icon-refresh-ccw"></i> OFFENE ANGEBOTE <br><small><code>The processed Offers</code></small>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('new_lead_view') }}">
                                                    <i class="fa fa-list-ol"></i> FERTIGE ANGEBOTE <br><small><code>Ready Offers</code></small>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('new_lead_view') }}">
                                                    <i class="fa fa-list-ol"></i> ANGEBOTS BESCHPRECHUNG <br><small><code>Termin Kalender</code></small>
                                                </a>
                                            </li>

                                            <li>
                                                <a href="{{ url('new_lead_view') }}">
                                                    <i class="fa fa-list-ol"></i> ANGEBOT STATUS
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('lead_junks') }}">
                                                    <i class="feather icon-trash-2"></i> ALLE ANGEBOTE
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>

                                <!-- AUFTRAGE -->
                                <li class="horizontal_menu_item nav-item" id="menu2">
                                    <a id="menu" href="javascript:void(0);" aria-haspopup="true" aria-expanded="false">
                                        <div class="menu-items">
                                            <h6>AUFTRÄGE</h6>
                                        </div>
                                    </a>
                                    <div class="submenu">
                                        <ul>
                                            <li>
                                                <a href="{{ route('plan.details') }}">
                                                    <i class="fa fa-plus-circle"></i> NEUE AUFTRÄGE
                                                </a>
                                            </li> 
                                            <li>
                                                <a href="{{ url('my_leads') }}">
                                                    <i class="feather icon-refresh-ccw"></i> UNBESTÄTIGTE AUFTRÄGE
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('new_lead_view') }}">
                                                    <i class="fa fa-list-ol"></i> BESTÄTIGTE AUFTRÄGE
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('lead_junks') }}">
                                                    <i class="feather icon-trash-2"></i> ALLE AUFTRÄGE
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>

                                <!-- PROJEKTE -->
                                <li class="horizontal_menu_item nav-item" id="menu3">
                                    <a id="menu" href="javascript:void(0);" aria-haspopup="true" aria-expanded="false">
                                        <div class="menu-items">
                                            <h6>PROJEKTE</h6>
                                        </div>
                                    </a>

                                    <div class="submenu">
                                        <ul>
                                            <li style="border-bottom: 1px solid #dad7d7;margin-bottom: 20px;">  <h3 class="bold primary">PROJEKTIERUNG</h3> </li>
                                        </ul>
                                        <ul> 
                                            <li>
                                                <a href="{{ route('new.lead.create') }}">
                                                    <i class="fa fa-plus-circle"></i> PROJEKT ANLEGEN
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('customer_details') }}">
                                                    <i class="fa fa-list-ol"></i> NEUES PROJEKT <!--OPEN AND NEW PROJECT -->
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('my_customer/'.auth()->user()->name) }}">
                                                    <i class="fa fa-list-ol"></i> MEINE PROJEKT
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('customer_details') }}">
                                                    <i class="fa fa-list-ol"></i> PROJEKT LISTE
                                                </a>
                                            </li> 
                                            <li>
                                                <a href="">
                                                    <i class="feather icon-trash-2"></i> JUNK PROJEKT
                                                </a>
                                            </li>
                                        </ul>
                                            <hr>
                                        <ul>
                                            <li style="border-bottom: 1px solid #dad7d7;margin-bottom: 20px;margin-top: 34px;">  <h3 class="bold primary">AUFGABEN</h3> </li>
                                        </ul>
                                        <ul>
                                            <li>
                                                <a href="">
                                                    <i class="fa fa-plus-circle"></i> NEUE AUFGABEN
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('task_todo/'.auth()->user()->name) }}">
                                                    <i class="feather icon-check-square"></i> MEINE AUFGABEN
                                                </a>
                                            </li>
                                            <li>
                                                <a href="">
                                                    <i class="feather icon-corner-down-right"></i> ÜBERTRAGENE AUFGABEN
                                                </a>
                                            </li>
                                            <li>
                                                <a href="">
                                                    <i class="feather icon-check-circle"></i> ALLE AUFGABEN
                                                </a>
                                            </li>
                                        </ul> 
                                        <hr> 
                                        <ul>
                                            <li style="border-bottom: 1px solid #dad7d7;margin-bottom: 20px;margin-top: 34px;">  <h3 class="bold primary">KALENDER</h3> </li>
                                        </ul>

                                        <ul>
                                            <li>
                                                <a href="">
                                                    <i class="fa fa-calendar"></i> MEINE KALENDER
                                                </a>
                                            </li>
                                            <li>
                                                <a href="">
                                                    <i class="fa fa-calendar"></i> FIRMEN KALENDER
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('appointment_view') }}">
                                                    <i class="fa fa-calendar"></i> KUNDE KALENDER
                                                </a>
                                            </li>
                                        </ul> 
                                            <hr>
                                        <ul>
                                            <li style="border-bottom: 1px solid #dad7d7;margin-bottom: 20px;margin-top: 34px;">  <h3 class="bold primary">TICKET</h3> </li>
                                        </ul>

                                        <ul>
                                            <li>
                                                <a href="">
                                                    <i class="fa fa-ticket"></i> NEUES TICKET
                                                </a>
                                            </li>
                                            <li>
                                                <a href="">
                                                    <i class="fa fa-ticket"></i> OFFENE TICKET
                                                </a>
                                            </li>
                                            <li>
                                                <a href="">
                                                    <i class="fa fa-ticket"></i> FERTIGE TICKET
                                                </a>
                                            </li>
                                            <li>
                                                <a href="">
                                                    <i class="fa fa-ticket"></i> ALLE TICKETS
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li> 
                                <!-- AUFGABEN -->  


                            

                                <!-- WARTUNGEN -->
                            <li class="horizontal_menu_item nav-item" id="menu8">
                                    <a id="menu" href="javascript:void(0);" aria-haspopup="true" aria-expanded="false">
                                        <div class="menu-items">
                                            <h6>SERVICE</h6>
                                        </div>
                                    </a>
                                    <div class="submenu">
                                        <ul>
                                            <li>
                                                <a href="">
                                                    <i class="fa fa-wrench"></i> NEUE WARTUNG
                                                </a>
                                            </li>
                                            <li>
                                                <a href="">
                                                    <i class="fa fa-wrench"></i> ALLE WARTUNGEN
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li> 

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
 <!-- END: Circle Menu-->



    
           
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    
    <!-- Dashboard Analytics Start -->
    <section id="dashboard-analytics"> 
        @yield('content')  
        <div class="modal fade text-left" id="timeout" tabindex="-1" data-backdrop="false" role="dialog"
            aria-labelledby="myModalLabel4" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger white">
                        <h4 class="modal-title" id="myModalLabel4">Session Timeout Warning</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h1><i class="fa fa-smile-o"></i> Bist du noch da?</h1>
                        <p>Ihre Zeit läuft in <span id="countdown" class=" btn-danger">60</span> Sekunden ab.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-light"
                            onclick="refreshPage();">Ja</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Dashboard Analytics end -->

    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    @php
    $year = \Carbon\Carbon::parse(now())->format('Y');
    @endphp
    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">
        <p class="clearfix blue-grey lighten-2 mb-0"><span
                class="float-md-left d-block d-md-inline-block mt-25">fasd &copy; {{ $year }}<a
                    class="text-bold-800 grey darken-2" href="" target="_blank">Solar Aspekt,</a>All rights
                Reserved</span>
            <button class="btn btn-primary btn-icon scroll-top" type="button"><i
                    class="feather icon-arrow-up"></i></button>
        </p>
    </footer>
    <!-- END: Footer-->
 <script>
     document.addEventListener("DOMContentLoaded", function () {
    const menus = document.querySelectorAll('.horizontal_menu_item');

    menus.forEach(menu => {
        const submenu = menu.querySelector('.submenu');

        menu.addEventListener("click", function (event) {
            event.stopPropagation(); // Prevent bubbling to the document click

            // Toggle the current submenu visibility
            if (submenu) {
                const isVisible = submenu.classList.contains('show'); 
                submenu.classList.toggle('show', !isVisible);  // Show or hide based on the current state 
            }

            // Close other submenus
            menus.forEach(otherMenu => {
                const otherSubmenu = otherMenu.querySelector('.submenu');
                if (otherMenu !== menu && otherSubmenu) { 
                    otherSubmenu.classList.remove('show');
                }
            });
        });
    });

    // Close all submenus when clicking outside
    document.addEventListener("click", function () { 
        document.querySelectorAll('.submenu').forEach(submenu => {
            submenu.classList.remove('show'); 
        });
    });

    // Prevent clicks inside the submenu from closing it
    document.querySelectorAll('.submenu').forEach(submenu => {
        submenu.addEventListener("click", function (event) { 
            event.stopPropagation(); // Prevent click from closing the submenu
        });
    });
});


 </script>

    <!-- BEGIN: Vendor JS-->
    <script src="{{ asset('app-assets/vendors/js/vendors.min.js')}}"></script>
    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ asset('app-assets/vendors/js/editors/quill/katex.min.js')}}"></script>
    <script src="{{ asset('app-assets/vendors/js/editors/quill/highlight.min.js')}}"></script>
    <script src="{{ asset('app-assets/vendors/js/editors/quill/quill.min.js')}}"></script>
    <!-- END: Page Vendor JS-->

    <script src="{{ asset('app-assets/vendors/js/extensions/tether.min.js')}}"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/shepherd.min.js')}}"></script>
    <!-- BEGIN: Theme JS-->


    <script src="{{asset('app-assets/js/core/app-menu.js')}}"></script>
    <script src="{{asset('app-assets/js/core/app.js')}}"></script>
    <script src="{{asset('app-assets/js/scripts/components.js')}}"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
 
    <!-- END: Theme JS-->


    <!-- BEGIN: Page JS-->
    <script src="{{asset('app-assets/js/scripts/pages/dashboard-analytics.js')}}"></script>
    <!-- END: Page JS-->
    <!-- BEGIN: Toastr-->
    <script src="{{ asset('app-assets/js/scripts/extensions/toastr.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/modal/components-modal.js') }}"></script> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('app-assets/js/scripts/pages/dashboard-analytics.js')}}"></script>

    <!-- END: Theme JS-->
    <script src="{{ asset('js/select2.min.js')}}"></script>
    @yield('script')
    

    <script>
        function refreshPage() {
        location.reload();
    }
    </script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const timeout = {{ config('session.lifetime') * 60 }};
        let lastActivity = parseInt('{{ session("last_activity_time", time()) }}');
        let timeoutWarning = timeout - 60; // Show modal 60 seconds before session expires
    
        let timeoutId; // ID of the logout timer

        function resetActivityTimer() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(function() {
                let currentTime = Math.floor(Date.now() / 1000);
                let elapsed = currentTime - lastActivity;
    
                if (elapsed >= timeoutWarning) {
                    showTimeoutModal(60); // 60 seconds countdown
                }
            }, timeout * 1000); // Reset the timer for the session lifetime
        }
    
        document.addEventListener('mousemove', function() {
            lastActivity = Math.floor(Date.now() / 1000); // Update lastActivity to current time
            resetActivityTimer(); // Reset the logout timer
        }, false);
    
        resetActivityTimer();
    
        function showTimeoutModal(duration) {
            var modal = $('#timeout');
            var countdown = $('#countdown');
            var counter = duration;
            var interval = setInterval(function() {
                countdown.text(counter);
                counter--;
                if (counter < 0) {
                    clearInterval(interval);
                    modal.modal('hide');
                    logoutUser(); // Perform logout
                }
            }, 1000);
    
            modal.modal('show');
        }

        window.refreshPage = function() {
            lastActivity = Math.floor(Date.now() / 1000); // Reset last activity time
            resetActivityTimer(); // Reset the timeout timer
            $('#timeout').modal('hide'); // Hide the modal
        };

        function logoutUser() {
            window.location.href = '/login_out'; // Adjust this URL to your application's logout route
        }
    });
</script>


<script>
    function updateClock() {
        const clockElement = document.getElementById('clock');
        const clockContainer = document.getElementById('clock-container');
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        clockElement.textContent = `${hours}:${minutes}:${seconds} Std.`;

        // Blink the clock container every minute
        if (seconds === "00") {
            clockContainer.style.opacity = 0;
            setTimeout(() => {
                clockContainer.style.opacity = 1;
            }, 500); // Blink for 500ms
        }
    }

    // Update the clock every second
    setInterval(updateClock, 1000);

    // Initialize clock immediately
    updateClock();
</script>
@stack('scripts')

</body>

</html>