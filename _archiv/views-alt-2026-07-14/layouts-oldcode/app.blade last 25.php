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
    <meta name="author" content="SOLAR ASPEKT">
    <title>Solar Aspekt - @yield('title')</title>
    <link rel="apple-touch-icon" href="{{ asset('app-assets/images/ico/apple-icon-120.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('app-assets/images/ico/favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css')}}"> 
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

html body {
    background-color: #f1f1f1 !important;
}
.scroll-top {
    float:right !important;
    right:0 !important;
    left: 0 !important;
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

.active_menu {
    background: var(--success-color) !important;
    border: 10px solid var(--danger-color) !important;
    color: white !important;
    font-weight: bold;

}
.active_menu .menu-items h6 {
    color:white !important;
}

.active_menu .menu-items .red-icon {
    filter: brightness(0) invert(1);

}
 

#menu {
    width: 20vw;
    height: 20vw;
    max-width: 150px;
    max-height: 150px;
    background: #b0d5f2;
    border: 10px solid #d9eaf9;
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

 

#menu:hover .red-icon {
   filter: brightness(0) invert(1);
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
    margin-top: 70px !important;
    margin-bottom: 36px !important;
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
    background: #f1f1f1;
    border-bottom: 1px solid #73b1d4;
    border-top: 1px solid #73b1d4;
    padding-top: 8px;
    left: 50%;
    /* z-index: 500; */
    border-radius: 6px;
    width: 100%;
    max-height: 300px;
    overflow-y: auto; 
    transition: opacity 0.3s ease, visibility 0.3s ease;
    opacity: 0;
    visibility: hidden;
}

.submenu.show {
    display: block;
    opacity: 1;
    visibility: visible;
}

.submenu ul {
    height: 28px !important;
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

.red-icon {
    filter: invert(62%) sepia(20%) saturate(1350%) hue-rotate(164deg) brightness(87%) contrast(92%);
}
.mobile_menu{
    width: 282px !important;
}
</style>

<style>
    .dashboard-image {
    width: 100%; /* Default for larger screens */
    max-width: 400px; /* Set a reasonable max width */
    height: auto; /* Maintain aspect ratio */
}

/* Tablet Screens (Width < 1024px) */
@media (max-width: 1024px) {
    .dashboard-image {
        max-width: 250px; /* Smaller size for tablets */
    }
}

/* Mobile Screens (Width < 768px) */
@media (max-width: 768px) {
    .dashboard-image {
        max-width: 150px; /* Smaller size for mobile */
    }
}

/* Very Small Screens (Low-Resolution < 480px) */
@media (max-width: 480px) {
    .dashboard-image {
        max-width: 38px; /* Even smaller for low-res devices */
    }
    .dashboard-title{
        font-size:9px !important;
    }
}

/* If width becomes 20px, force a minimum width */
@media (max-width: 320px) {
    .dashboard-image {
        max-width: 80px;
        min-width: 50px; /* Ensure it does not shrink too much */
    }
}

@media (max-width: 480px) {
    #mobile_menu {
        display: block !important; /* Even smaller for low-res devices */
    } 
}

button {
    border-radius: 0 !important;
}
input {
       border-radius: 0 !important;
}
.card {
        border-radius: 0 !important;
}
.nav-link {
        border-radius: 0 !important;

}
.header-navbar {
        border-radius: 0 !important;
    

}
.header-navbar.navbar-shadow {
    box-shadow: 0 0 !important;
}
.header-navbar-shadow {
    display:none !important;
}
.submenu {
        border-radius: 0 !important;

}
.submenu ul li a {
    color: #73b1d4 !important;
}
.submenu ul li a:hover {
    color:  #8fc73e !important;
}
select {
        border-radius: 0 !important;

}
.list-group-item {
        border-radius: 0 !important;

}

#dashboard_table tr td {
    padding-top: 4px !important;
    padding-bottom: 0 !important;
}
.main-menu.menu-light .navigation > li.active > a {
    background: white; 
    color: #8fc73e;
    font-weight: 400;
    border-radius: 4px;
    box-shadow: 0 0 ;
}
    .header-navbar.floating-nav {
            margin: 0.0rem 3.1rem 0;
            height:35px !important;
    }

    .line-clock {

    width: 104%;
    height: 42px;
    border-left: 2px solid #d5d5d5;

    }
    .active-link {
    color: #94c11b !important;
}
</style>

<style>
.employee_lists_menu{
   
 display: flex !important;
    align-items: flex-start !important;
    flex-direction: column !important;

}
.employee_list {

    list-style:none;

}
.emp_list {
    display:flex;
    align-items:center;
}
</style>
 
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->
{{-- @vite('resources/js/app.js') --}}

<body class="vertical-layout vertical-menu-modern 2-columns  navbar-floating footer-static  " data-layout="dark-layout" data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">

    <!-- BEGIN: Header-->
    <nav class="header-navbar navbar-expand-lg navbar navbar-with-menu floating-nav navbar-light  ">
        <div class="navbar-wrapper">
            <div class="navbar-container content">
                <div class="navbar-collapse" id="navbar-mobile">
                    <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                        <ul class="nav navbar-nav">
                            <li class="nav-item mobile-menu d-xl-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ficon feather icon-menu"></i></a></li>
                        </ul>
                     <ul class="nav navbar-nav bookmark-icons d-none d-lg-flex" id="menu-top-left">
                    <li class="dropdown nav-item m-0" data-menu="dropdown">
                        <a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown">
                            <span data-i18n="Others">AUFGABEN</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ url('personal/task/'.auth()->user()->name) }}">
                                <i class="feather icon-menu"></i> AUFGABENERTEILUNG
                            </a></li>
                            <li>
                                <a class="dropdown-item" href="{{ url('task_todo/'.auth()->user()->name) }}">
                                    <i class="feather icon-menu"></i> PROJEKT AUFGABEN
                                </a>
                            </li>
                      
                          
                        </ul>
                    </li>
                    <li class="dropdown nav-item m-0" data-menu="dropdown">
                        <a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown">
                            <span  >KALENDER</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ url('tasks/calendar/personal') }}"><i class="feather icon-menu"></i> MEINE KALENDER</a></li> 
                            <li><a class="dropdown-item" href="{{ url('appointments') }}"><i class="feather icon-menu"></i> TERMINLISTE</a></li>
                        </ul>
                    </li>

             
                    <li class="dropdown dropdown-notification nav-item">
                        <a class="nav-link nav-link-label m-0" href="#" data-toggle="dropdown">
                            ANWESENHEIT
                        </a>
                         
                        <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                            <li class="dropdown-menu-header">
                                <div class="dropdown-header m-0 p-2">
                                    <h3 class="white mb-1">Mitarbeiterliste:</h3>
                                    <small class="employee_status_count">
                                        <span class="notification-title mr-1 active_emp">anwesend: <span id="active_emp">0</span></span>
                                        <span class="notification-title mr-1 inactive_emp">abwesend: <span id="inactive_emp">0</span></span>
                                        <span class="notification-title mr-1 sick_emp">krank: <span id="sick_emp">0</span></span>
                                        <span class="notification-title holiday_emp">Urlaub: <span id="holiday_emp">0</span></span>
                                    </small> 
                                </div>
                           
                            </li>
                            <li>
                                 <fieldset>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="employee_search" placeholder="Name, Nachname">
                                        <div class="input-group-append" id="button-addon2">
                                            <button class="btn btn-primary waves-effect waves-light" type="button"><i class="feather icon-search"></i></button>
                                        </div>
                                    </div>
                                </fieldset>
                            </li>
                     

                            <table class="table employee_list">

                            </table> 
                        </ul> 
                    </li> 

                    <li class="dropdown nav-item m-0" data-menu="dropdown">
                        <a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown">
                            <span  >PROZESS</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ url('lead/overview') }}"><i class="feather icon-menu"></i> PROZESS</a></li> 
                            <li><a class="dropdown-item" href="{{ url('lead/kanban') }}"><i class="feather icon-menu"></i> LEAD ÜBERSICHT</a></li>
                        </ul>
                    </li>

                    <li class="dropdown nav-item m-0">
                        <a class="dropdown-toggle nav-link" href="#">
                            <span >ALLE KONTAKTE</span>
                        </a>
                    </li>

                


                </ul>
       <!-- Mobile and Tablet Dropdown -->  
                    <li class="dropdown nav-item" data-menu="dropdown" style="list-style: none; display:none;" id="mobile_menu">
                        <a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown"><i class="feather icon-more-horizontal"></i><span data-i18n="Others">SUB NAV</span></a>
                        <ul class="dropdown-menu mobile_menu "> 
                            <li data-menu="">
                                <a class="dropdown-item" href="{{ url('personal/task/'.auth()->user()->name) }}" data-toggle="dropdown"  >
                                <i class="feather icon-folder"></i>PAUFGABENERTEILUNG
                                </a>
                            </li> 
                            <li data-menu="">
                                <a class="dropdown-item" href="{{ url('personal/task/'.auth()->user()->name) }}" data-toggle="dropdown"  >
                                <i class="feather icon-folder"></i>PROJEKT AUFGABEN
                                </a>
                            </li> 

                            <li data-menu="">
                                <a class="dropdown-item" href="{{ url('tasks/calendar/personal') }}" data-toggle="dropdown"  >
                                <i class="feather icon-calendar"></i>MEINE KALENDER
                                </a>
                            </li> 

                            <li data-menu="">
                                <a class="dropdown-item" href="{{ url('appointments') }}" data-toggle="dropdown"  >
                                <i class="feather icon-folder"></i>TERMINLISTE
                                </a>
                            </li> 
                            
 
                            <li data-menu="">
                                <a class="dropdown-item" href="" data-toggle="dropdown"  >
                                <i class="feather icon-folder"></i>ANWESENHEITS
                                </a>
                            </li>
                        </ul>
                    </li>
 
                    </div>
                    <ul class="nav navbar-nav float-right" style="margin-top:1px;"> 
                        <li class="nav-item d-none d-lg-block"> 
                          
                            <div class="clock" id="clock-container"> 
                                <div class="card-content">
                                    <div class="card-body text-center d-flex  p-0"  > 
                                        <div class="line-clock"> 
                                        </div>
                                        <div class="d-flex mr-2 ml-2"> 
                                            <span class=""><i class="fa fa-play primary" style="font-size: 27px; margin-right:10px;"></i></span>
                                            <p class="font-medium-2">   
                                                START
                                            </p>   
                                        </div>
                                        <div class="col-md-6 d-flex"> 
                                            <div class="button ">
                                                <p class="font-weight-bold font-medium-2 mb-0" id="clock"></p> 
                                                    <small>7:30 -</small>
                                                    <small>16:00</small> 
                                            </div> 
                                        </div>
                                        <div class="line-clock mr-1">
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item d-none d-lg-block p-0"><a class="nav-link nav-link-expand p-0"><i class="ficon feather icon-maximize"></i></a></li>
              
                        <li class="dropdown dropdown-notification nav-item">
                            <a class="nav-link nav-link-label p-0   " href="#" data-toggle="dropdown">
                                <i class="ficon feather icon-bell"></i>
                                <span class="badge badge-pill badge-primary badge-up"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                                <li class="dropdown-menu-header">
                                    <div class="dropdown-header m-0 p-2">
                                        <h3 class="white">New</h3>
                                        <span class="notification-title">App Notifications</span>
                                    </div>
                                </li>
                                <li class="scrollable-container media-list"> 
                                    <!-- Notification 2 -->
                                     <a class="d-flex justify-content-between" href="javascript:void(0)">
                                        <div class="media d-flex align-items-start"> 
                                            <div class="media-body"> 
                                                <div class="accordion" id="inquiryNotification">
                                                     <div id="inquiryNotification" class="accordion"></div>

                                                </div>
                                            </div> 
                                        </div>
                                    </a> 
                                </li>
                                <li class="dropdown-menu-footer">
                                    <a class="dropdown-item p-1 text-center" href="javascript:void(0)">Read all notifications</a>
                                </li>
                            </ul>
                        </li>

                        
                        
                         <li class="dropdown dropdown-user nav-item"> 
                                <a  class="dropdown-toggle nav-link dropdown-user-link  p-0" href="#" data-toggle="dropdown">
                                        <div class="user-nav d-sm-flex d-none"><span class="user-name text-bold-600">{{
                                                DB::table('users')->join('employees', 'employees.id', '=', 'users.name'
                                                )->where('users.name', '=',
                                                auth()->user()->name)->select('employees.name')->pluck('name')->first()
                                                }}</span>
                                            <span class="user-status">verfügbar</span>
                                        </div>
                                        <span><img class="round" src="{{ asset('images/user/'.auth()->user()->image)}}" style="    border-radius: 50%; box-shadow:0 0;"   alt="avatar" height="40" width="40"></span>
                                 </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item"  href="{{url('/user')}}">  <i class="feather icon-user-check"></i> Aktiv</a>
                                    <a class="dropdown-item"  href="{{url('/user')}}">  <i class="feather icon-user-x"></i> abwesend</a>
                                    <a class="dropdown-item"  href="{{url('/user')}}">  <i class="feather icon-pause"></i> Mittagspause</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item"  href="{{url('/employee_profile/'.auth()->user()->name)}}">  <i class="feather icon-user"></i> Mein Profile</a>
                                    <a class="dropdown-item"  href="{{url('/employee_notifications/'.auth()->user()->name)}}">  <i class="feather icon-bell"></i> Mein Anträge</a>
                                    <a class="dropdown-item"  href="{{url('/user')}}">  <i class="feather icon-user"></i> Profil bearbeiten</a> 
                                    <div class="dropdown-divider"></div> 
                                    <a class="dropdown-item" class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                            document.getElementById('logout-form').submit();">

                                        <i class="feather icon-power"></i>
                                        Ausloggen

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
    <div class="main-menu menu-fixed menu-light menu-accordion " data-scroll-to-active="true">
        <div class="navbar-header main-nav">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mr-auto"><a class="navbar-brand" href="{{ url('/') }}">
                       
                    </a>
                </li>
                <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                    <i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i>
                    <i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary" data-ticon="icon-disc"></i></a>
                </li>
            </ul>
        </div>
         <img id="toggle-picture" src="{{ asset('logo/solar.svg')}}" alt="Navbar Picture" class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary"style="width: 158px;position: absolute;top: 20px;left: 26px;"  data-ticon="icon-disc">
        

        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                 <li class=""><a href="{{ url('/home') }}"><i class="feather icon-home"></i><span class="menu-item" >DASHBOARD</span></a> </li> 
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

                     
 
                    <li class="nav-item">
                        <a href="#"><i class="fa fa-building"></i>Geschäftspartner</a>
                        <ul class="menu-content">
                            <li><a href="{{ route('brand.info') }}"><i class="feather icon-circle"></i>Hersteller</a></li>
                            <li><a href="{{ route('distributor.info') }}"><i class="feather icon-circle"></i>Lieferant</a></li>
                            <li><a href="{{ route('external.info') }}"><i class="feather icon-circle"></i>Zeitarbeitfirma</a></li>
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
                                <li class="has-sub is-shown"><a href="#"><i class="feather icon-home"></i><span class="menu-item" data-i18n="Second Level">Abteilung & Berufsbezeichnung</span></a>
                                    <ul class="menu-content">
                                        <li><a href="{{ route('department.info') }}"><i class="feather icon-circle"></i>Abteilung</a>
                                        </li>
                                        <li><a href="{{ route('position.info') }}"><i class="feather icon-circle"></i>Position</a>
                                        </li>
                                         <li><a href="{{ route('department.organize') }}"><i class="feather icon-circle"></i>Organisationsstruktur</a>
                                        </li>
                                        
                                    </ul>
                                </li>
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
                        <a href="#"><i class="feather icon-user"></i>Benutzerverwaltung</a>
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

                      
                    <li class="nav-item">
                        <a href="#"><i class="fa fa-book"></i>PERSÖNLICHE TO-DOS</a>
                        <ul class="menu-content">
                            <li><a href="{{ route('note.category.view') }}"><i class="feather icon-folder"></i>Kategorie</a></li>
                            <li><a href="{{ route('notes.details') }}"><i class="feather icon-file-text"></i>Todo's Details</a></li> 
                        </ul>
                    </li>

                    <li><a href="{{ route('chats.view', auth()->user()->name) }}"><i class="feather icon-message-circle"></i>CHAT</a></li>
                    <li><a href="{{ route('system.feedback.view') }}"><i class="feather icon-info"></i> FEEDBACK</a></li>
                    <li><a href="{{ route('knowledge.base') }}"><i class="fa fa-question-circle"></i>  HILFE CENTER</a></li>


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
                                    <a id="menu" href="javascript:void(0);" aria-haspopup="true" aria-expanded="false" class="menu {{ Str::contains(Route::currentRouteName(), 'quiry') ? 'active_menu' : '' }}">
                                        <div class="menu-items">
                                            <img src="{{ asset('images/dashboard/icon_speedometer.svg') }}" alt="Gauge Icon" class="dashboard-image red-icon" style="width:82px !important">

                                            <h6 class="dashboard-title">ANFRAGE</h6>
                                        </div>
                                    </a>
                                    <div class="submenu {{ Str::contains(Route::currentRouteName(), 'quiry') ? 'show' : '' }}">
                                        <ul>
                                            <li>
                                                <a href="{{ route('inquiry.create') }}" @if(Route::currentRouteName() == 'inquiry.create') style="color: #94c11b !important" @endif >
                                                    <i class="fa fa-plus-circle"></i> ANLEGEN
                                                </a>
                                            </li> 
                                                 @php
                                                    $anfrage_count = DB::table('inquiries')->where('status', 'Unpublished')->count();
                                                    $junk_anfrage = DB::table('inquiries')->where('status', 'junk')->count();
                                                    $delete_anfrage = DB::table('inquiries')->whereNotNull('deleted_at')->count();
                                                    $my_inquiries = DB::table('inquiries')->where('contact_person', auth()->user()->name)->where('status', '!=', 'Published')->count();
                                                @endphp
                                            <li>
                                                <a href="{{ route('my.inquiry.view') }}" @if(Route::currentRouteName() == 'my.inquiry.view') style="color: #94c11b !important" @endif >
                                                    <i class="fa fa-list-ol"></i> MEINE <span style="margin-right:5px; margin-left:5px" >|</span> <span style="color:#64b0e5"   >  {{$my_inquiries}}</span> 
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('inquiry.view') }}" @if(Route::currentRouteName() == 'inquiry.view') style="color: #94c11b !important" @endif >
                                                    <i class="fa fa-list-ol"></i> LISTE <span style="margin-right:5px; margin-left:5px" >|</span> <span style="color:#64b0e5">{{$anfrage_count}}</span>
                                            
       
                                                    <p style="justify-self: center;"></p>
                                                </a>
                                            </li>  
                                            <li>
                                                <a href="{{ url('inquiry_junklist') }}" @if(Route::currentRouteName() == 'inquiry.junk.list') style="color: #94c11b !important" @endif>
                                                    <i class="feather icon-slash"></i> JUNK  <span style="margin-right:5px; margin-left:5px" >|</span> <span style="color:#64b0e5">{{$junk_anfrage}}</span>
                                                   
                                                </a>
                                            </li>
                                      
                                              <li>
                                                <a href="{{ url('inquiry_deleted_list') }}" class="danger"  @if(Route::currentRouteName() == 'inquiry.deleted.list') style="color: #94c11b !important" @endif>
                                                    <i class="feather icon-trash"></i> GELÖSCHTE  <span style="margin-right:5px; margin-left:5px" >|</span> <span style="color:#64b0e5">{{$delete_anfrage}}</span> 

                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>

                                <!-- LEADS -->
                                <li class="horizontal_menu_item nav-item" id="menu1">
                                    <a id="menu" href="javascript:void(0);" aria-haspopup="true" aria-expanded="false" class="menu 
                                        @php
                                            $previousUrl = url()->previous(); 
                                            $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                        @endphp

                                        @if(Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'lead'))
                                            active_menu
                                        @elseif(Str::contains(Route::currentRouteName(), 'lead') && Str::contains($previousRoute, 'lead'))
                                            active_menu
                                        @endif">
                                        <div class="menu-items">
                                            <img src="{{ asset('images/dashboard/icon_roket.svg') }}" alt="Gauge Icon" class="dashboard-image red-icon" style="width:82px !important"> 
                                            <h6 class="dashboard-title">LEADS</h6>
                                        </div>
                                    </a>
                                    <div class="submenu  @php
                                            $previousUrl = url()->previous(); 
                                            $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                                @endphp

                                                @if(Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'lead'))
                                                  show
                                                @elseif(Str::contains(Route::currentRouteName(), 'lead'))
                                                   show
                                                @endif">
                                        <ul>
                                          @php
                                                $lead_count = DB::table('new_leads')->where('status', 'Published')->count();
                                                $lead_new = DB::table('lead_alternative_adds')->where('stage', 'new')->count();
                                                $junk_lead = DB::table('new_leads')->where('status', 'junk')->count();
                                                $delete_lead = DB::table('new_leads')->whereNotNull('deleted_at')->count();
                                                $my_leads = DB::table('new_leads')->where('contact_person', auth()->user()->name)->count();
                                                $waiting_loops = DB::table('new_lead_responsibilities')->where('status', '!=', 'accept')->count();
                                            @endphp

                                            <li>
                                                <a href="{{ url('new_lead_create') }}" @if(Route::currentRouteName() == 'new.lead.create') style="color: #94c11b !important" @endif>
                                                    <i class="fa fa-plus-circle"></i> ANLEGEN
                                                </a>
                                            </li>
                                       
                                            <li> 
                                                 @php
                                                    $previousUrl = url()->previous();
                                                    $previousRoute = null;

                                                    try {
                                                        $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                                    } catch (\Exception $e) {
                                                        // Avoid errors if no previous route exists
                                                        $previousRoute = null;
                                                    }

                                                    // Define if the link should be highlighted
                                                    $isActive = (Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'new.leads')) ||
                                                                (Str::contains(Route::currentRouteName(), 'new.leads')  );
                                                @endphp

                                                <a href="{{ url('new_leads') }}" 
                                                    class="{{ $isActive ? 'active-link' : '' }}" 
                                                    style="{{ $isActive ? 'color: #94c11b !important;' : '' }}">

                                                    @if($isActive)
                                                        <i class="fa fa-arrow-right warning"></i>
                                                    @endif

                                                    <i class="feather icon-plus"></i> NEUE
                                                    <span style="margin-right:5px; margin-left:5px">|</span> 
                                                    <span style="color:#64b0e5">{{ $lead_new }}</span>    

                                                </a>
                                            </li>
                                            <li> 
                                                 @php
                                                    $previousUrl = url()->previous();
                                                    $previousRoute = null;

                                                    try {
                                                        $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                                    } catch (\Exception $e) {
                                                        // Avoid errors if no previous route exists
                                                        $previousRoute = null;
                                                    }

                                                    // Define if the link should be highlighted
                                                    $isActive = (Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'my.leads')) ||
                                                                (Str::contains(Route::currentRouteName(), 'my.leads')  );
                                                @endphp

                                                <a href="{{ url('my_leads') }}" 
                                                    class="{{ $isActive ? 'active-link' : '' }}" 
                                                    style="{{ $isActive ? 'color: #94c11b !important;' : '' }}">

                                                    @if($isActive)
                                                        <i class="fa fa-arrow-right warning"></i>
                                                    @endif

                                                    <i class="feather icon-user"></i> MEINE
                                                    <span style="margin-right:5px; margin-left:5px">|</span> 
                                                    <span style="color:#64b0e5">{{ $my_leads }}</span>    

                                                </a>
                                            </li>
                                            <li> 
                                                 @php
                                                    $previousUrl = url()->previous();
                                                    $previousRoute = null;

                                                    try {
                                                        $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                                    } catch (\Exception $e) {
                                                        // Avoid errors if no previous route exists
                                                        $previousRoute = null;
                                                    }

                                                    // Define if the link should be highlighted
                                                    $isActive = (Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'waiting.loop.leads')) ||
                                                                (Str::contains(Route::currentRouteName(), 'waiting.loop.leads')  );
                                                @endphp

                                                <a href="{{ route('waiting.loop.leads') }}" 
                                                    class="{{ $isActive ? 'active-link' : '' }}" 
                                                    style="{{ $isActive ? 'color: #94c11b !important;' : '' }}">

                                                    @if($isActive)
                                                        <i class="fa fa-arrow-right warning"></i>
                                                    @endif

                                                    <i class="feather icon-refresh-ccw"></i> WARTESCHLEIFE
                                                    <span style="margin-right:5px; margin-left:5px">|</span> 
                                                    <span style="color:#64b0e5">{{ $waiting_loops }}</span>    

                                                </a>
                                            </li>
                                            <li> 
                                                 @php
                                                    $previousUrl = url()->previous();
                                                    $previousRoute = null;

                                                    try {
                                                        $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                                    } catch (\Exception $e) {
                                                        // Avoid errors if no previous route exists
                                                        $previousRoute = null;
                                                    }

                                                    // Define if the link should be highlighted
                                                    $isActive = (Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'new.lead.view')) ||
                                                                (Str::contains(Route::currentRouteName(), 'new.lead.view')  );
                                                @endphp

                                                <a href="{{ url('new_lead_view') }}" 
                                                    class="{{ $isActive ? 'active-link' : '' }}" 
                                                    style="{{ $isActive ? 'color: #94c11b !important;' : '' }}">

                                                    @if($isActive)
                                                        <i class="fa fa-arrow-right warning"></i>
                                                    @endif

                                                    <i class="fa fa-list-ol"></i> LISTE
                                                    <span style="margin-right:5px; margin-left:5px">|</span> 
                                                    <span style="color:#64b0e5">{{ $lead_count }}</span>    

                                                </a>
                                            </li> 
                                        
                                            <li>
                                        @php
                                            $previousUrl = url()->previous();
                                            $previousRoute = null;

                                            try {
                                                $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                            } catch (\Exception $e) {
                                                // Avoid errors if no previous route exists
                                                $previousRoute = null;
                                            }

                                            // Define if the link should be highlighted
                                            $isActive = (Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'lead.junks')) ||
                                                        (Str::contains(Route::currentRouteName(), 'lead.junks')  );
                                        @endphp

                                        <a href="{{ url('lead_junks') }}" 
                                            class="{{ $isActive ? 'active-link' : '' }}" 
                                            style="{{ $isActive ? 'color: #94c11b !important;' : '' }}">

                                            @if($isActive)
                                                <i class="fa fa-arrow-right warning"></i>
                                            @endif

                                            <i class="feather icon-slash"></i> JUNK
                                            <span style="margin-right:5px; margin-left:5px">|</span> 
                                            <span style="color:#64b0e5">{{ $junk_lead }}</span>    

                                        </a>

                                            </li> 
                                            <li>

                                            @php
                                                $previousUrl = url()->previous();
                                                $previousRoute = null;

                                                try {
                                                    $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                                } catch (\Exception $e) {
                                                    // Avoid errors if no previous route exists
                                                    $previousRoute = null;
                                                }

                                                // Define if the link should be highlighted
                                                $isActive = (Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'deleted.leads')) ||
                                                            (Str::contains(Route::currentRouteName(), 'deleted.leads')  );
                                            @endphp

                                            <a href="{{ url('deleted_leads') }}" 
                                                class="{{ $isActive ? 'active-link' : '' }}" 
                                                style="{{ $isActive ? 'color: #94c11b !important;' : '' }}">

                                                @if($isActive)
                                                    <i class="fa fa-arrow-right warning"></i>
                                                @endif

                                                <i class="feather icon-trash-2"></i> GELÖSCHTE
                                                <span style="margin-right:5px; margin-left:5px">|</span> 
                                                <span style="color:#64b0e5">{{ $delete_lead }}</span>    

                                            </a>
                                                
                                            </li>
                                        </ul>
                                    </div>
                                </li>

                                <!-- PLANUNG -->
                                <li class="horizontal_menu_item nav-item" id="menu2">
                                    <a id="menu" href="javascript:void(0);" aria-haspopup="true" aria-expanded="false" class="menu 
                                        @php
                                            $previousUrl = url()->previous(); 
                                            $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                        @endphp

                                        @if(Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'plan'))
                                            active_menu
                                        @elseif(Str::contains(Route::currentRouteName(), 'plan') )
                                            active_menu
                                        @endif">
                                        <div class="menu-items">
                                            <img src="{{ asset('images/dashboard/icon_gears.svg') }}" alt="Gauge Icon" class="dashboard-image red-icon" style="width:82px !important"> 
                                            <h6 class="dashboard-title">PLANUNG</h6>
                                        </div>
                                    </a>
                                    <div class="submenu 
                                            @php
                                                $previousUrl = url()->previous(); 
                                                $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                            @endphp

                                            @if(Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'plan'))
                                                show
                                            @elseif(Str::contains(Route::currentRouteName(), 'plan'))
                                                show
                                            @endif">
                                        <ul>
                                            <li>
                                                 @php
                                                    $previousUrl = url()->previous();
                                                    $previousRoute = null;

                                                    try {
                                                        $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                                    } catch (\Exception $e) {
                                                        // Avoid errors if no previous route exists
                                                        $previousRoute = null;
                                                    }

                                                    // Define if the link should be highlighted
                                                    $isActive = (Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'plan.details')) ||
                                                                (Str::contains(Route::currentRouteName(), 'plan.details')  );
                                                @endphp

                                                <a href="{{ route('plan.details') }}" 
                                                    class="{{ $isActive ? 'active-link' : '' }}" 
                                                    style="{{ $isActive ? 'color: #94c11b !important;' : '' }}">

                                                    @if($isActive)
                                                        <i class="fa fa-arrow-right warning"></i>
                                                    @endif

                                                    <i class="feather icon-plus"></i> NEUE
                                                    <span style="margin-right:5px; margin-left:5px">|</span> 
                                                    <span style="color:#64b0e5">0</span>    

                                                </a>
                                                     
                                            </li>
                                            <li>
                                                <a href="">
                                                    <i class="feather icon-refresh-ccw"></i> OFFENE 
                                                </a>
                                            </li>
                                            <li>
                                                <a href="">
                                                    <i class="fa fa-list-ol"></i> FERTIGE 
                                                </a>
                                            </li>
                                            <li>
                                                <a href="">
                                                    <i class="feather icon-trash-2"></i> ALLE 
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>

                                <!-- ANGEBOTE -->
                                <li class="horizontal_menu_item nav-item" id="menu4">
                                    <a id="menu" href="javascript:void(0);" aria-haspopup="true" aria-expanded="false" class="menu 
                                           @php
                                                $previousUrl = url()->previous(); 
                                                $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                            @endphp

                                            @if(Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'offer'))
                                                active_menu
                                            @elseif(Str::contains(Route::currentRouteName(), 'offer') )
                                                active_menu
                                            @endif">
                                        <div class="menu-items">
                                            <img src="{{ asset('images/dashboard/icon_document.svg') }}" alt="Gauge Icon" class="dashboard-image red-icon" style="width:82px !important"> 
                                            <h6 class="dashboard-title">ANGEBOTE</h6>
                                        </div>
                                    </a>
                                    <div class="submenu 
                                             @php
                                                $previousUrl = url()->previous(); 
                                                $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                            @endphp

                                            @if(Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'offer'))
                                                show
                                            @elseif(Str::contains(Route::currentRouteName(), 'offer'))
                                                show
                                            @endif">
                                        <ul>
                                            <li>
                                                 

                                                @php
                                                    $previousUrl = url()->previous();
                                                    $previousRoute = null;

                                                    try {
                                                        $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                                    } catch (\Exception $e) {
                                                        // Avoid errors if no previous route exists
                                                        $previousRoute = null;
                                                    }

                                                    // Define if the link should be highlighted
                                                    $isActive = (Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'offer.details')) ||
                                                                (Str::contains(Route::currentRouteName(), 'offer.details')  );
                                                @endphp

                                                <a href="{{ route('offer.details') }}" 
                                                    class="{{ $isActive ? 'active-link' : '' }}" 
                                                    style="{{ $isActive ? 'color: #94c11b !important;' : '' }}">

                                                    @if($isActive)
                                                        <i class="fa fa-arrow-right warning"></i>
                                                    @endif

                                                    <i class="feather icon-plus"></i> NEUE
                                                    <span style="margin-right:5px; margin-left:5px">|</span> 
                                                    <span style="color:#64b0e5">0</span>    

                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('my_leads') }}">
                                                    <i class="feather icon-refresh-ccw"></i> OFFENE  
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('new_lead_view') }}">
                                                    <i class="fa fa-list-ol"></i> FERTIGE  
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('new_lead_view') }}">
                                                    <i class="fa fa-list-ol"></i> BESCHPRECHUNG  
                                                </a>
                                            </li>

                                            <li>
                                                <a href="{{ url('new_lead_view') }}">
                                                    <i class="fa fa-list-ol"></i> ANGEBOT 
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('lead_junks') }}">
                                                    <i class="feather icon-trash-2"></i> ALLE 
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>

                                <!-- AUFTRAGE -->
                                <li class="horizontal_menu_item nav-item" id="menu2">
                                    <a id="menu" href="javascript:void(0);" aria-haspopup="true" aria-expanded="false" class="menu 
                                           @php
                                                $previousUrl = url()->previous(); 
                                                $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                            @endphp

                                            @if(Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'deal'))
                                                active_menu
                                            @elseif(Str::contains(Route::currentRouteName(), 'deal') )
                                                active_menu
                                            @endif">
                                        <div class="menu-items">
                                            <img src="{{ asset('images/dashboard/icon_target.svg') }}" alt="Gauge Icon" class="dashboard-image red-icon" style="width:82px !important"> 
                                            <h6 class="dashboard-title">AUFTRÄGE</h6>
                                        </div>
                                    </a>
                                     @php
                                            $deals = DB::table('deals')->select('id', 'status', 'deleted_at', 'status_msg')->get();
                                            $deal_new = $deals->where('status_msg', 'new')->whereNotIn('status', ['Junk', 'confirm', 'complete'])->count();
                                            $deal_confirm = $deals->where('status_msg', 'confirm')->count();
                                            $deal_junk = $deals->where('status', 'Junk')->whereNull('deleted_at')->count();
                                            $deal_all = $deals->whereNull('deleted_at')->whereNotIn('status', ['Junk'])->count();
                                            $deal_delete = $deals->where('deleted_at', '!=', Null)->count(); 
                                        @endphp
                                    <div class="submenu 
                                            @php
                                                $previousUrl = url()->previous(); 
                                                $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                            @endphp

                                            @if(Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'deal'))
                                                show
                                            @elseif(Str::contains(Route::currentRouteName(), 'deal'))
                                                show
                                            @endif">
                                        <ul>
                                            <li> 
                                                @php
                                                    $previousUrl = url()->previous();
                                                    $previousRoute = null;

                                                    try {
                                                        $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                                    } catch (\Exception $e) {
                                                        // Avoid errors if no previous route exists
                                                        $previousRoute = null;
                                                    }

                                                    // Define if the link should be highlighted
                                                    $isActive = (Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'deal.details')) ||
                                                                (Str::contains(Route::currentRouteName(), 'deal.details')  );
                                                @endphp

                                                <a href="{{ route('deal.details') }}" 
                                                    class="{{ $isActive ? 'active-link' : '' }}" 
                                                    style="{{ $isActive ? 'color: #94c11b !important;' : '' }}">

                                                    @if($isActive)
                                                        <i class="fa fa-arrow-right warning"></i>
                                                    @endif

                                                     <i class="fa fa-plus-circle"></i> NEUE | {{ $deal_new }}

                                                </a>

                                                 
                                            </li>  
                                            <li>
                                                <a href="">
                                                    <i class="fa fa-list-ol"></i> BESTÄTIGTE | {{ $deal_confirm }}
                                                </a>
                                            </li>
                                            <li> 
                                                 @php
                                                    $previousUrl = url()->previous();
                                                    $previousRoute = null;

                                                    try {
                                                        $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                                    } catch (\Exception $e) {
                                                        // Avoid errors if no previous route exists
                                                        $previousRoute = null;
                                                    }

                                                    // Define if the link should be highlighted
                                                    $isActive = (Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'deal.all.list')) ||
                                                                (Str::contains(Route::currentRouteName(), 'deal.all.list')  );
                                                @endphp

                                                <a href="{{ route('deal.all.list') }}" 
                                                    class="{{ $isActive ? 'active-link' : '' }}" 
                                                    style="{{ $isActive ? 'color: #94c11b !important;' : '' }}">

                                                    @if($isActive)
                                                        <i class="fa fa-arrow-right warning"></i>
                                                    @endif

                                                    <i class="feather icon-trash-2"></i> ALLE | {{ $deal_all }}

                                                </a>
                                            </li>

                                            <li> 
                                                 @php
                                                    $previousUrl = url()->previous();
                                                    $previousRoute = null;

                                                    try {
                                                        $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                                    } catch (\Exception $e) {
                                                        // Avoid errors if no previous route exists
                                                        $previousRoute = null;
                                                    }

                                                    // Define if the link should be highlighted
                                                    $isActive = (Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'deal.junk.list')) ||
                                                                (Str::contains(Route::currentRouteName(), 'deal.junk.list')  );
                                                @endphp

                                                <a href="{{ route('deal.junk.list') }}" 
                                                    class="{{ $isActive ? 'active-link' : '' }}" 
                                                    style="{{ $isActive ? 'color: #94c11b !important;' : '' }}">

                                                    @if($isActive)
                                                        <i class="fa fa-arrow-right warning"></i>
                                                    @endif

                                                    <i class="feather icon-slash"></i> JUNK | {{ $deal_junk }}

                                                </a>
                                            </li>

                                            <li> 

                                                 @php
                                                    $previousUrl = url()->previous();
                                                    $previousRoute = null;

                                                    try {
                                                        $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                                    } catch (\Exception $e) {
                                                        // Avoid errors if no previous route exists
                                                        $previousRoute = null;
                                                    }

                                                    // Define if the link should be highlighted
                                                    $isActive = (Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'deal.delete.list')) ||
                                                                (Str::contains(Route::currentRouteName(), 'deal.delete.list')  );
                                                @endphp

                                                <a href="{{ route('deal.delete.list') }}" 
                                                    class="{{ $isActive ? 'active-link' : '' }}" 
                                                    style="{{ $isActive ? 'color: #94c11b !important;' : '' }}">

                                                    @if($isActive)
                                                        <i class="fa fa-arrow-right warning"></i>
                                                    @endif

                                                    <i class="feather icon-trash"></i>  GELÖSCHTE | {{ $deal_delete }}

                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>

                                <!-- PROJEKTE -->
                                <li class="horizontal_menu_item nav-item" id="menu3">
                                    <a id="menu" href="javascript:void(0);" aria-haspopup="true" aria-expanded="false" class="menu 
                                            @php
                                                $previousUrl = url()->previous(); 
                                                $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                            @endphp

                                            @if(Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'project'))
                                                active_menu
                                            @elseif(Str::contains(Route::currentRouteName(), 'project') && Str::contains($previousRoute, 'project'))
                                                active_menu
                                            @endif">
                                        <div class="menu-items">
                                            <img src="{{ asset('images/dashboard/icon_memoboard.svg') }}" alt="Gauge Icon" class="dashboard-image red-icon" style="width:82px !important"> 
                                            <h6 class="dashboard-title">PROJEKTE</h6>
                                        </div>
                                    </a>

                                    <div class="submenu {{ Str::contains(Route::currentRouteName(), 'project') ? 'show' : '' }}"> 
                                        <ul> 
                                             
                                            <li>
                                                <a href="{{ route('project.details') }}" @if(Route::currentRouteName() == 'project.details') style="color: #94c11b !important" @endif>
                                                    <i class="fa fa-list-ol"></i> NEUES  <!--OPEN AND NEW PROJECT -->
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('my.project.details') }}" @if(Route::currentRouteName() == 'my.project.details') style="color: #94c11b !important" @endif>
                                                    <i class="fa fa-list-ol"></i> MEINE 
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('project.lists') }}" @if(Route::currentRouteName() == 'project.lists') style="color: #94c11b !important" @endif>
                                                    <i class="fa fa-list-ol"></i>LISTE
                                                </a>
                                            </li> 
                                            <li>
                                                <a href="{{ route('project.junk.lists') }}" @if(Route::currentRouteName() == 'project.junk.lists') style="color: #94c11b !important" @endif>
                                                    <i class="fa fa-power-off"></i> JUNK 
                                                </a>
                                            </li>
                                             <li>
                                                <a href="{{ route('project.delete.lists') }}" @if(Route::currentRouteName() == 'project.delete.lists') style="color: #94c11b !important" @endif>
                                                    <i class="feather icon-trash"></i> GELÖSCHTE
                                                </a>
                                            </li>
                                        </ul> 
                                    </div>
                                </li> 
                                <!-- AUFGABEN -->   

                                <!-- WARTUNGEN -->
                                <li class="horizontal_menu_item nav-item" id="menu8">
                                    <a id="menu" href="javascript:void(0);" aria-haspopup="true" aria-expanded="false" class="menu  
                                            @php
                                                $previousUrl = url()->previous(); 
                                                $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                            @endphp

                                            @if(Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'ticket'))
                                                active_menu
                                            @elseif(Str::contains(Route::currentRouteName(), 'ticket') && Str::contains($previousRoute, 'ticket'))
                                                active_menu
                                            @endif">
                                        <div class="menu-items">
                                            <img src="{{ asset('images/dashboard/icon_speaker.svg') }}" alt="Gauge Icon" class="dashboard-image red-icon" style="width:82px !important"> 
                                            <h6 class="dashboard-title">TICKET</h6>
                                        </div>
                                    </a>
                                    <div class="submenu">
                                        <ul>
                                            <li>
                                                <a href="">
                                                    <i class="fa fa-wrench"></i>STÖRUNG
                                                </a>
                                            </li>
                                            <li>
                                                <a href="">
                                                    <i class="fa fa-wrench"></i> WARTUNG
                                                </a>
                                            </li>

                                             <li>
                                                <a href="">
                                                    <i class="fa fa-wrench"></i> REKLAMATION
                                                </a>
                                            </li>

                                             <li>
                                                <a href="">
                                                    <i class="fa fa-wrench"></i> NOTDIENST
                                                </a>
                                            </li>
                                              <li>
                                                <a href="">
                                                    <i class="fa fa-wrench"></i> REPARATUR
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


        <div class="modal fade" id="new_leave_modal" tabindex="-1" role="dialog" data-emp-id="">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Urlaubsanfrage</h4>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form class="form-horizontal" method="POST" action="{{ route('leave.store') }}">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="active_tab" value="leave">
                            <input type="hidden" name="emp_id" value="">

                            <div class="form-group">
                                <label>Jahr</label>
                                <select name="year" id="yearSelect" class="form-control">
                                    <option value="">Select Year</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Ab Datum</label>
                                <input type="date" class="form-control leave_start_date" name="start_date">
                            </div>

                            <div class="form-group">
                                <label>Bis Datum</label>
                                <input type="date" class="form-control leave_end_date" name="end_date">
                            </div>

                            <div class="form-group">
                                <label>Erlaubter Feiertag</label>
                                <input type="number" class="form-control leave_day" name="leave_day">
                            </div>

                            <div class="form-group">
                                <label>Verbleibende Tage</label>
                                <input type="number" class="form-control remaining_day" name="remaining_day">
                            </div>

                            <div class="form-group">
                                <label>Letztes Jahr übrig</label>
                                <input type="number" class="form-control last_year_remainings" name="last_year_remainings" readonly>
                            </div>

                            <div class="form-group">
                                <label>Dauer (Tage)</label>
                                <input type="number" class="form-control leave_duration" name="duration">
                                <label class="duration_label" style="color:red; display:none;">Die Dauer überschreitet die zulässigen Urlaubstage</label>
                            </div>

                            <div class="form-group">
                                <label>Grund</label>
                                <select class="form-control" name="reason">
                                    <option value="Persönlicher Urlaub" selected>Persönlicher Urlaub</option>
                                    <option value="Jahresurlaub">Jahresurlaub</option> 
                                    <option value="Elternzeit">Elternzeit</option>
                                    <option value="Trauerurlaub">Trauerurlaub</option> 
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Beschreibung</label>
                                <textarea name="description" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary save_button">Speichern</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Abbrechen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="sickModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Krankmeldung</h5>
                        <button class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                       <form id="sickForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="sick_id">
                            <input type="hidden" id="emp_id" value="" name="emp_id"> 

                            <div class="form-group">
                                <label>Startdatum</label>
                                <input type="date" id="start_date" name="start_date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Enddatum</label>
                                <input type="date" id="end_date" name="end_date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Gesamte Tage</label>
                                <input type="number" id="total_days" name="total_days" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Gesamtzeit</label>
                                <input type="number" id="total_hours" name="total_hours" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Krankmeldung (PDF, JPG, PNG)</label>
                                <input type="file" name="document" class="form-control" accept=".pdf,.jpg,.png">
                            </div> 
                             
                            <div class="form-group">
                                <label>Bechreibung</label>
                                <textarea id="status_msg" name="status_msg" class="form-control"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">speichern</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">abbrechen</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


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
                            <p>Ihre Zeit läuft in <span id="countdown" class="btn-danger">60</span> Sekunden ab.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary waves-effect waves-light" onclick="refreshPage();">
                                Ja
                            </button>
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
                class="float-md-left d-block d-md-inline-block mt-25 ">COPYRIGHT &copy; {{ $year }}<a
                    class="text-bold-800 grey darken-2" href="" target="_blank">Solar Aspekt,</a>All rights
                Reserved</span>
            <button class="btn btn-primary btn-icon scroll-top copyright "   type="button"><i
                    class="feather icon-arrow-up"></i></button>
        </p>
    </footer>
    <!-- END: Footer-->
    <!-- Active menu  -->
    <script>
   // Select all menu items
        const menus = document.querySelectorAll('.nav-item .menu');

        // Function to remove active_menu from all menus
        function clearActiveMenus() {
            menus.forEach(menu => menu.classList.remove('active_menu'));
        }

        // Add click event to each menu item
        menus.forEach(menuItem => {
            menuItem.addEventListener('click', function(event) {
                // Prevent the event from bubbling up and closing immediately
                event.stopPropagation();

                // Clear previous active_menu classes
                clearActiveMenus();

                // Set the current menu as active
                this.classList.add('active_menu');
            });
        });

        // Remove active_menu when clicking outside the menu
        // document.addEventListener('click', function() {
        //     clearActiveMenus();
        // });

    </script>
 <script>
    document.addEventListener("DOMContentLoaded", function () {
        const menus = document.querySelectorAll('.horizontal_menu_item');
        const menuLinks = document.querySelectorAll('.nav-item .menu');

        // Function to clear all active menus and hide submenus
        function clearAllMenus() {
            menuLinks.forEach(menu => menu.classList.remove('active_menu'));
            document.querySelectorAll('.submenu').forEach(submenu => submenu.classList.remove('show'));
        }

        // Toggle active menu and submenu visibility
        menus.forEach(menu => {
            const submenu = menu.querySelector('.submenu');
            const menuLink = menu.querySelector('.menu');

            menuLink.addEventListener("click", function (event) {
                event.stopPropagation(); // Prevent bubbling

                // Clear previous active menus and hide submenus
                clearAllMenus();

                // Activate the current menu and toggle submenu visibility
                menuLink.classList.add('active_menu');
                if (submenu) submenu.classList.toggle('show');
            });
        });

        // Close all menus and submenus when clicking outside
        // document.addEventListener("click", function () {
        //     clearAllMenus();
        // });

        // Prevent clicks inside the submenu from closing it
        document.querySelectorAll('.submenu').forEach(submenu => {
            submenu.addEventListener("click", function (event) {
                event.stopPropagation(); // Prevent submenu from closing
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

 
    <!-- BEGIN: Toastr-->
    <script src="{{ asset('app-assets/js/scripts/extensions/toastr.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/modal/components-modal.js') }}"></script> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script> 
    <script src="{{ asset('js/checkHoliday.js')}}"></script>
    <script src="{{ asset('js/checkEndHoliday.js')}}"></script>

    <!-- END: Theme JS-->
    <script src="{{ asset('js/select2.min.js')}}"></script>
    @yield('script')


    <!--Notification System: start-->
    
    <script>
        $(document).on('click', '.dropdown-menu', function (e) {
        e.stopPropagation(); // Prevent dropdown from closing on click
    }); 
    </script>
    <!--Notification System: start-->

    <script>
        function refreshPage() {
        location.reload();
    }
    </script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const timeout = {{ config('session.lifetime') * 60 }};
        let lastActivity = parseInt('{{ session("last_activity_time", time()) }}');
        const timeoutWarning = timeout - 60; // Show modal 60 seconds before session expires

        let timeoutId; // ID of the logout timer
        let intervalId; // Interval for pinging the server

        function resetActivityTimer() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(function () {
                let currentTime = Math.floor(Date.now() / 1000);
                let elapsed = currentTime - lastActivity;

                if (elapsed >= timeoutWarning) {
                    showTimeoutModal(60); // 60 seconds countdown
                }
            }, (timeout - 60) * 1000); // Reset the timer to trigger modal before session expires
        }

        document.addEventListener('mousemove', function () {
            lastActivity = Math.floor(Date.now() / 1000); // Update lastActivity to current time
            resetActivityTimer(); // Reset the logout timer
            keepSessionAlive(); // Ping server to keep session alive
        }, false);

        resetActivityTimer();

        function showTimeoutModal(duration) {
            const modal = $('#timeout');
            const countdown = $('#countdown');
            let counter = duration;
            const interval = setInterval(function () {
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

        window.refreshPage = function () {
            lastActivity = Math.floor(Date.now() / 1000); // Reset last activity time
            resetActivityTimer(); // Reset the timeout timer
            keepSessionAlive(); // Ping server to keep session alive
            $('#timeout').modal('hide'); // Hide the modal
        };

        function keepSessionAlive() {
            fetch('/keep-session-alive', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ keepAlive: true })
            });
        }

        function logoutUser() {
            fetch('/logout', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', // Include CSRF token for Laravel security
                    'Content-Type': 'application/json'
                }
            }).then(() => {
                window.location.href = '/login'; // Redirect to login page after logout
            }).catch(error => console.error('Logout failed:', error));
        }


        // Periodically ping the server to keep the session alive
        intervalId = setInterval(function () {
            keepSessionAlive();
        }, (timeoutWarning * 1000) / 2); // Ping every half of the timeoutWarning period
    });
</script>


<script>
    function updateClock() {
        const clockElement = document.getElementById('clock');
        const clockContainer = document.getElementById('clock-container');
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        clockElement.textContent = `${hours}:${minutes} Uhr`;

        // Blink the clock container every minute
        if (now.getSeconds() === 0) {
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

    <script>
         const userName = {{ auth()->user()->name }}
            $(document).ready(function () {
    function fetchUnreadNotifications() {
        $.ajax({
            url: '/notifications/inquiry/' + encodeURIComponent(userName), // Fetch unread notifications
            method: 'GET',
            success: function (response) {
                const notifications = response.data;
                const notificationCount = notifications.length;

                // Update the badge with the count of unread notifications
                $('#notification-count').text(notificationCount);

                const collapseContainer = $('#collapse1');
                collapseContainer.empty(); // Clear existing notifications

                if (notificationCount === 0) {
                    collapseContainer.append('<p class="text-muted p-2">No unread notifications.</p>');
                    return;
                }

                // Populate notifications in the collapsible section
                notifications.forEach(notification => {
                    const notificationHtml = `
                        <div class="notification-item p-2" style="cursor: pointer;" data-id="${notification.id}" data-url="/inquiry_show/${notification.lead_id}">
                            
                            <p>${notification.message}</p>
                            <small>
                                <time>${notification.performed_at}</time>
                                <a href="javascript:void(0)" class="mark-read text-muted" data-id="${notification.id}">Mark as Read</a>
                            </small>
                        </div>
                        <hr>
                    `;
                    collapseContainer.append(notificationHtml);
                });
            },
            error: function (xhr, status, error) {
                console.error('Error fetching unread notifications:', xhr.responseText || error);
            },
        });
    }

    // Mark notification as read
    $(document).on('click', '.mark-read', function (e) {
        e.stopPropagation(); // Prevent triggering parent click event
        const notificationId = $(this).data('id');

        $.ajax({
            url: '/notifications/' + encodeURIComponent(notificationId) + '/mark-read', // Mark notification as read
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function () {
                console.log('Notification marked as read.');
                fetchUnreadNotifications(); // Refresh unread notifications
            },
            error: function (xhr, status, error) {
                console.error('Error marking notification as read:', xhr.responseText || error);
            },
        });
    });

    // Redirect to notification link and mark as read
    $(document).on('click', '.notification-item', function () {
        const url = $(this).data('url'); // Get the URL from the notification item's data attribute
        const notificationId = $(this).data('id'); // Get the notification ID

        if (url && notificationId) {
            // Mark the notification as read before redirecting
            $.ajax({
                url: '/notifications/' + encodeURIComponent(notificationId) + '/mark-read', // Mark notification as read
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                success: function () {
                    console.log('Notification marked as read.');
                    window.location.href = url; // Redirect after marking as read
                },
                error: function (xhr, status, error) {
                    console.error('Error marking notification as read:', xhr.responseText || error);
                    // Redirect even if marking as read fails
                    window.location.href = url;
                },
            });
        }
    });

    // Initial setup for the collapsible card
    $('#inquiryNotification').html(`
        <div class="cards" id="inquiry_card">
            <div class="card-headers p-0" id="heading1">
                <button class="btn btn-link collapsible-trigger" data-toggle="collapse" data-target="#collapse1" aria-expanded="false" aria-controls="collapse1">
                    <table>
                        <td>
                            <h6 class="primary media-heading">
                                <img src="/images/dashboard/icon_speedometer.svg" alt="Anfrage" style="width: 33px;">
                                Neue Anfrage
                            </h6>
                        </td>
                        <td>
                            <div class="badge badge-warning ml-2" id="notification-count">0</div>
                        </td>
                    </table>
                </button>
            </div>
            <div id="collapse1" class="collapse" aria-labelledby="heading1" data-parent="#inquiryNotification">
                <!-- Notifications will load dynamically here -->
            </div>
        </div>
    `);

    // Fetch notifications on page load
    fetchUnreadNotifications();
});

    </script>


<!-- Lead Notificaiton : start  -->
 <script> 
    $(document).ready(function () {
        function fetchUnreadNotifications() {
            $.ajax({
                url: '/notifications/lead/' + encodeURIComponent(userName), // Updated route
                method: 'GET',
                success: function (response) {
                    const notifications = response.data;
                    const notificationCount = notifications.length;

                    // Update the badge with the count of unread notifications
                    $('#notification-count').text(notificationCount);

                    const collapseContainer = $('#collapse1');
                    collapseContainer.empty(); // Clear existing notifications

                    if (notificationCount === 0) {
                        collapseContainer.append('<p class="text-muted p-2">No unread notifications.</p>');
                        return;
                    }

                    // Populate notifications in the collapsible section
                    notifications.forEach(notification => {
                        const notificationHtml = `
                            <div class="notification-item p-2" style="cursor: pointer;" data-id="${notification.id}" data-url="/lead_show/${notification.lead_id}">
                                <p>${notification.message}</p>
                                <small>
                                    <time>${notification.performed_at}</time>
                                    <a href="javascript:void(0)" class="mark-read text-muted" data-id="${notification.id}">Mark as Read</a>
                                </small>
                            </div>
                            <hr>
                        `;
                        collapseContainer.append(notificationHtml);
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching unread notifications:', xhr.responseText || error);
                },
            });
        }

        // Mark notification as read
        $(document).on('click', '.mark-read', function (e) {
            e.stopPropagation(); // Prevent triggering parent click event
            const notificationId = $(this).data('id');

            $.ajax({
                url: '/lead/notifications/' + encodeURIComponent(notificationId) + '/mark-read', // Mark notification as read
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                success: function () {
                    console.log('Notification marked as read.');
                    fetchUnreadNotifications(); // Refresh unread notifications
                },
                error: function (xhr, status, error) {
                    console.error('Error marking notification as read:', xhr.responseText || error);
                },
            });
        });

        // Redirect to notification link and mark as read
        $(document).on('click', '.notification-item', function () {
            const url = $(this).data('url'); // Get the URL from the notification item's data attribute
            const notificationId = $(this).data('id'); // Get the notification ID

            if (url && notificationId) {
                // Mark the notification as read before redirecting
                $.ajax({
                    url: '/lead/notifications/' + encodeURIComponent(notificationId) + '/mark-read', // Mark notification as read
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function () {
                        console.log('Notification marked as read.');
                        window.location.href = url; // Redirect after marking as read
                    },
                    error: function (xhr, status, error) {
                        console.error('Error marking notification as read:', xhr.responseText || error);
                        // Redirect even if marking as read fails
                        window.location.href = url;
                    },
                });
            }
        });

        // Initialize collapsible card for notifications
        $('#leadNotification').html(`
            <div class="cards" id="lead_card">
                <div class="card-headers p-0" id="heading1">
                    <button class="btn btn-link collapsible-trigger" data-toggle="collapse" data-target="#collapse1" aria-expanded="false" aria-controls="collapse1">
                        <table>
                            <td>
                                <h6 class="primary media-heading">
                                    <img src="/images/dashboard/icon_speedometer.svg" alt="Lead" style="width: 33px;">
                                    Neue Leads
                                </h6>
                            </td>
                            <td>
                                <div class="badge badge-warning ml-2" id="notification-count">0</div>
                            </td>
                        </table>
                    </button>
                </div>
                <div id="collapse1" class="collapse" aria-labelledby="heading1" data-parent="#leadNotification">
                    <!-- Notifications will load dynamically here -->
                </div>
            </div>
        `);

        // Fetch notifications on page load
        fetchUnreadNotifications();
    });


    function renderNeighborTable(neighbors) {
        if (!neighbors || neighbors.length === 0) {
            Swal.fire({
                icon: "info",
                title: "Keine Nachbarn gefunden",
                text: "Es wurden keine Nachbarn in der Nähe gefunden.",
            });
            return;
        }

        let tableRows = neighbors.map(neighbor => `
            <tr>
                <td>${neighbor.name} ${neighbor.lastname}</td>
                <td>${neighbor.street}</td>
                <td>${neighbor.postcode}</td>
                <td>${neighbor.city}</td>
                <td>${neighbor.distance.toFixed(2)} km</td>
                <td><a href="/new_lead_profile/${neighbor.id}" class="btn btn-primary btn-sm" target="_blank">Profil ansehen</a></td>
            </tr>
        `).join("");

        const tableHtml = `
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Street</th>
                        <th>Postcode</th>
                        <th>City</th>
                        <th>Distance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableRows}
                </tbody>
            </table>
        `;

        Swal.fire({
            title: "Nachbarn gefunden",
            html: tableHtml,
            width: "80%",
            showCloseButton: true,
        });
    }

</script>
<!-- Lead Notifictaion : End  -->
 
<!-- Reminder Alert of note  -->
<script>
    $(document).ready(function() {
        let reminderCount = 0; // Track the number of active reminders

     
        // Function to fetch reminders
        function checkPersonalNotesReminders() {
            console.log('Fetching reminders...');
            $.ajax({
                url: 'due-personal-notes',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('auth_token'), // Ensure auth token is sent
                    'Accept': 'application/json'
                },
                success: function(response) {
 
                    // Ensure data is an array
                    let personalNotes = Array.isArray(response.personal_notes) ? response.personal_notes : [];
                    let appointments = Array.isArray(response.appointments) ? response.appointments : [];

                    let allReminders = [...personalNotes, ...appointments];

                    if (allReminders.length > 0) {
                        allReminders.forEach(function(note) {
                            console.log('Processing reminder:', note);

                            // Play alarm sound
                            var alarmSound = document.getElementById('alarm-sound');
                            if (alarmSound) {
                                alarmSound.play().catch(function(error) {
                                    console.error('Error playing sound:', error);
                                });
                            }

                            // Display SweetAlert with Repeat and Complete buttons
                            Swal.fire({
                                title: note.title || 'Reminder',
                                text: note.note || 'You have a reminder.',
                                icon: 'info',
                                showCancelButton: true,
                                confirmButtonText: 'Complete',
                                cancelButtonText: 'Repeat',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    updateReminderStatus(note.id, 'complete');
                                } else {
                                    updateReminderStatus(note.id, 'repeat');
                                }
                            });
                        });
                    } else {
                        console.log('No reminders due.');
                    }
                },

                error: function(xhr, status, error) {
                    console.error('Error fetching reminders:', error);
                }
            });
        }

        // Function to update reminder status
        function updateReminderStatus(reminderId, action) {
            $.ajax({
                url: `reminder/${reminderId}/status`,
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('auth_token'), // Ensure auth token is sent
                    'Accept': 'application/json'
                },
                data: {
                    action: action,
                    _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token if needed
                },
                success: function(response) {
                    console.log('Reminder status updated:', response);
                },
                error: function(xhr, status, error) {
                    console.error('Error updating reminder status:', xhr.responseText);
                }
            });
        }

        // Fetch reminders every 60 seconds
        setInterval(checkPersonalNotesReminders, 60000);

        // Initial fetch
        checkPersonalNotesReminders();

        

        // Fetch reminders every 60 seconds
        setInterval(checkPersonalNotesReminders, 60000);
    });
</script>


<!-- Personal Note: repeate function :start  -->
<script>
    $(document).ready(function () {
        const repeatTranslations = {
            "": "Häufigkeit auswählen",
            "minute": "Minütlich",
            "hourly": "Stündlich",
            "daily": "Täglich",
            "weekly": "Wöchentlich",
            "monthly": "Monatlich",
            "quarterly": "Vierteljährlich",
            "yearly": "Jährlich"
        };

        function processRepeatingNotes() {
            $.ajax({
                url: "{{ route('notes.processRepeatingNotes') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}" // Include CSRF token
                },
                success: function (response) {
                    const { duplicated_notes, repeated_note_details } = response;

                    // If no notes were duplicated, do nothing
                    if (duplicated_notes === 0) {
                        console.log('No repeating notes to process.');
                        return;
                    }

                    // Create an HTML table to display the repeated note details
                    let noteDetailsTable = `
                        <table class="table table-bordered" style="width: 100%; font-size: 14px;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Titel</th>
                                    <th>Nächste Frist</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    repeated_note_details.forEach(note => {
                        noteDetailsTable += `
                            <tr>
                                <td>${note.id}</td>
                                <td>${note.title}</td>
                                <td>${note.next_deadline}</td>
                            </tr>
                        `;
                    });

                    noteDetailsTable += `
                            </tbody>
                        </table>
                    `;

                    // Show SweetAlert with the "Stop" button
                    Swal.fire({
                        title: 'Wiederholte Notizen verarbeitet',
                        html: `
                            <p>${duplicated_notes} Notizen wurden erfolgreich verarbeitet.</p>
                            ${noteDetailsTable}
                        `,
                        icon: 'success',
                        width: '800px',
                        showDenyButton: true,
                        confirmButtonText: 'OK',
                        denyButtonText: 'Stop Repeating',
                    }).then((result) => {
                        if (result.isDenied) {
                            // Trigger stop repeat functionality if "Stop Repeating" is clicked
                            stopRepeatingForAll(repeated_note_details);
                        }
                    });

                    loadNotes(); // Reload the notes table to reflect changes
                },
                error: function () {
                    Swal.fire('Error', 'Failed to process repeating notes. Please try again.', 'error');
                }
            });
        }


        // Function to stop repeating for all notes
        function stopRepeatingForAll(repeatedNotes) {
            const noteIds = repeatedNotes.map(note => note.id); // Extract IDs of repeated notes

            $.ajax({
                url: "{{ route('notes.stopRepeatingForAll') }}",
                method: "PUT",
                data: {
                    _token: "{{ csrf_token() }}",
                    note_ids: noteIds
                },
                success: function () {
                    Swal.fire('Erfolgreich!', 'Die Wiederholung für alle Notizen wurde gestoppt.', 'success');
                    loadNotes(); // Reload notes to reflect changes
                },
                error: function () {
                    Swal.fire('Fehler', 'Die Wiederholung konnte nicht gestoppt werden.', 'error');
                }
            });
        }


        function loadNotes() {
            $.ajax({
                url: "{{ route('notes') }}",
                method: "GET",
                success: function (response) {
                    const noteTableBody = $('#note_table tbody');
                    noteTableBody.empty();

                    response.notes.forEach((note, index) => {
                        noteTableBody.append(`
                            <tr data-id="${note.id}" style="cursor: grab; border-bottom: 10px solid #f8f8f8; border-left: 10px solid ${note.color}; background:white;">
                                <td style="text-align: center;">${index + 1}</td>
                                <td class="title-field" data-id="${note.id}" data-field="title">${note.title}</td>
                                <td class="note-field" data-id="${note.id}" data-field="note">${note.note}</td>
                                <td class="change-date" data-id="${note.id}">${note.deadline || 'Kein Fälligkeitsdatum'}</td>
                                <td class="change-time" data-id="${note.id}">${note.end_time || 'Keine Endzeit'}</td>
                                <td class="updateCategoryModal" data-id="${note.id}">${note.category_name || 'Standard'}</td>
                                <td>
                                    ${note.reminder_date || note.reminder_time ? `
                                        <small class="no-reminder-icon" data-id="${note.id}" data-toggle="tooltip" title="Erinnerung: ${note.reminder_date || ''} ${note.reminder_time || ''}">
                                            <i class="feather icon-bell primary"></i> ${note.reminder_date || ''} ${note.reminder_time || ''}
                                        </small>` : 'Keine Erinnerung'}
                                </td>
                                <td>
                                    ${note.repeat ? `
                                        <small class="no-reminder-icon" data-id="${note.id}">
                                            <i class="feather icon-refresh-ccw primary"></i> ${repeatTranslations[note.repeat] || note.repeat} 
                                        </small>
                                        <button class="btn btn-sm btn-danger stop-repeating" data-id="${note.id}">Stop</button>
                                    ` : 'Keine Wiederholung'}
                                </td>
                                <td>
                                    <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1 waves-effect waves-light delete_note" data-id="${note.id}">
                                        <i class="feather icon-trash"></i>  
                                    </button>
                                    <button type="button" class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1 waves-effect waves-lightnote-color" data-id="${note.id}">
                                        <i class="feather icon-aperture" ></i> 
                                    </button> 
                                    <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1 waves-effect waves-light note-settings" data-id="${note.id}">
                                        <i class="feather icon-edit"></i> 
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                },
                error: function () {
                    Swal.fire('Fehler', 'Notizen konnten nicht geladen werden. Bitte versuche es erneut.', 'error');
                }
            });
        }

        // Stop repeating button functionality
        

        // Automatically call processRepeatingNotes every 5 minutes
        setInterval(function () {
            console.log('Processing repeating notes...');
            processRepeatingNotes();
        }, 60000);  
    });
</script>

 


<!-- Repeating the personal Task:  -->
  <script>
    $(document).ready(function () {
        const repeatTranslations = {
            "": "Häufigkeit auswählen",
            "minute": "Minütlich",
            "hourly": "Stündlich",
            "daily": "Täglich",
            "weekly": "Wöchentlich",
            "monthly": "Monatlich",
            "quarterly": "Vierteljährlich",
            "yearly": "Jährlich"
        };

        // Fetch repeatable personal tasks and load them into the table
        function loadRepeatableTasks() {
            console.log('Fetching repeatable tasks...');
            $.ajax({
                url: "{{ route('personal.task.repeat.list') }}", // Use the repeat_list route
                method: "GET",
               success: function (response) {
                    console.log('Repeatable tasks fetched successfully:', response);

                    if (response.length === 0) {
                        console.warn('No repeatable tasks found.');
                        return; // Exit if no tasks are found
                    }

                    const taskTableBody = $('#task_table tbody');
                    taskTableBody.empty();

                    response.forEach((task, index) => {
                        console.log(`Task ${index + 1}:`, task);
                        taskTableBody.append(`
                            <tr data-id="${task.id}" style="cursor: grab; border-bottom: 10px solid #f8f8f8; border-left: 10px solid ${task.color}; background:white;">
                                <td style="text-align: center;">${index + 1}</td>
                                <td>${task.task_title}</td>
                                <td>${task.description || 'Keine Beschreibung'}</td>
                                <td>${task.reminder_date || 'Kein Fälligkeitsdatum'}</td>
                                <td>${task.reminder_time || 'Keine Zeit'}</td>
                                <td>
                                    ${task.repeat ? `
                                        <small>
                                            <i class="feather icon-refresh-ccw primary"></i> ${repeatTranslations[task.repeat] || task.repeat}
                                        </small>
                                        <button class="btn btn-sm btn-danger cancel-repeat" data-id="${task.id}">Stop</button>
                                    ` : 'Keine Wiederholung'}
                                </td>
                                <td>
                                    <button type="button" class="btn btn-icon btn-danger delete-task" data-id="${task.id}">
                                        <i class="feather icon-trash"></i>
                                    </button>
                                    <button type="button" class="btn btn-icon btn-primary edit-task" data-id="${task.id}">
                                        <i class="feather icon-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching repeatable tasks:', error, xhr.responseText);
                }
            });
        }

        // Handle cancel repeat button
        $(document).on('click', '.cancel-repeat', function () {
            const taskId = $(this).data('id');
            console.log(`Cancel repeat for task ID: ${taskId}`);

            $.ajax({
                url: "{{ route('tasks.stopRepeatingForAll') }}", // Replace with your backend stop route
                method: "PUT",
                data: {
                    _token: "{{ csrf_token() }}",
                    task_ids: [taskId]
                },
                success: function () {
                    console.log(`Repeat canceled for task ID: ${taskId}`);
                    loadRepeatableTasks(); // Reload repeatable tasks
                },
                error: function (xhr, status, error) {
                    console.error('Error stopping repeat:', error, xhr.responseText);
                }
            });
        });

        // Automatically fetch repeatable tasks every minute
        setInterval(function () {
            console.log('Checking and fetching repeatable tasks...');
            loadRepeatableTasks();
        }, 60000); // 1 minute

        // Initial load of repeatable tasks on page load
        console.log('Initial load of repeatable tasks...');
        loadRepeatableTasks();
    });
</script>



<!-- Getting the employee Answenheit and the function for saving Leave  -->
    
<script>
    $(document).ready(function() {
        // Function to fetch employee data
       function fetchEmployees(query = '') {
            $.ajax({
                url: "{{ route('get.employees.leave.status') }}",
                method: 'GET',
                data: { search: query },  // Pass search parameter
                dataType: 'json',
                success: function(data) {
                    console.log("✅ API Response:", data); // Debugging
                    renderEmployeeList(data);
                },
                error: function(xhr, status, error) {
                    console.error("❌ Error fetching employees:", error);
                }
            });
        }


        // Function to render the employee list
      function renderEmployeeList(employees) {
            let employeeList = '';
            
            employees.forEach(employee => {
                let imagePath = employee.image 
                    ? `{{ asset('images/employee/') }}/${employee.image}`
                    : `{{ asset('images/gender/male.png') }}`;

                let status = '';
                if (employee.status === 'Active') {
                    status = '<span class="avatar-status-online"></span>';
                } else if (employee.status === 'Holiday' || employee.status === 'Sick') {
                    status = '<span class="avatar-status-away"></span>';
                } else {
                    status = '<span class="avatar-status-busy"></span>';
                }

                checkUserPermission("{{ auth()->user()->name }}", "Admin", function(hasPermission) {
                    let leaveButton = hasPermission 
                        ? `<p class="mr-1"><a href="#" data-id="${employee.id}" class="leave_creating">Urlaub</a></p>` 
                        : '';

                    let sickButton = hasPermission 
                        ? `<p><a href="#" data-id="${employee.id}" class="sick_creating">Krankmeldung</a></p>` 
                        : '';

                    employeeList += `  
                        <tr>
                            <td class="pt-0 pb-0">
                                <div class="avatar mr-1">
                                    <img src="${imagePath}" alt="${employee.name} ${employee.lastname}" width="32" height="32"> 
                                    ${status}
                                </div>
                            </td>
                            <td  class="pt-0 pb-0">
                                <a href="{{ url('employee_profile/')}}/${employee.id}" class="view_profile">
                                    <span style="font-weight: bold; color:#505050;">${employee.name} ${employee.lastname}</span>
                                </a> 
                                <p>${employee.status_msg ?? ''}</p>
                                <div class="d-flex"> 
                                    ${leaveButton}
                                    ${sickButton}
                                </div>
                            </td> 
                        </tr>`;
                    
                    $('.employee_list').html(employeeList);
                });
            });
        }

        
        function checkUserPermission(userId, roll, callback) {
            $.ajax({
                url: `/has_permission/${userId}/${roll}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    callback(response.hasPermission); // ✅ Pass result to callback
                },
                error: function(xhr) {
                    console.error("❌ Error checking permission:", xhr.responseText);
                    callback(false); // Assume no permission if an error occurs
                }
            });
        }

        // Populate year dropdown
        function populateYearDropdown() {
            const yearSelect = document.getElementById("yearSelect");
            const currentYear = new Date().getFullYear();
            yearSelect.innerHTML = ""; 

            for (let i = currentYear - 5; i <= currentYear + 1; i++) {
                let option = document.createElement("option");
                option.value = i;
                option.textContent = i;
                yearSelect.appendChild(option);
            }
            yearSelect.value = currentYear;
        }

        // Calculate working days (excluding weekends)
        function calculateWorkingDays(startDate, endDate) {
            let start = new Date(startDate);
            let end = new Date(endDate);
            let count = 0;

            while (start <= end) {
                let dayOfWeek = start.getDay();
                if (dayOfWeek !== 0 && dayOfWeek !== 6) { 
                    count++;
                }
                start.setDate(start.getDate() + 1);
            }
            return count;
        }

        // Update leave duration and validate remaining leave days
        function updateDurationAndRemainingDays(modal) {
            if (!modal) return console.error("❌ Modal not found.");

            const startDate = modal.querySelector(".leave_start_date").value;
            const endDate = modal.querySelector(".leave_end_date").value;
            const remainingDays = parseInt(modal.querySelector(".remaining_day").value) || 0;

            if (startDate && endDate) {
                const workingDays = calculateWorkingDays(startDate, endDate);
                modal.querySelector(".leave_duration").value = workingDays;

                console.log("🚀 Requested Leave:", workingDays, "Remaining Days:", remainingDays);

                if (workingDays > remainingDays) {
                    modal.querySelector(".duration_label").style.display = "block";
                    modal.querySelector(".save_button").style.display = "none";

                    Swal.fire({
                        title: "Achtung!",
                        text: "Sie haben mehr Urlaubstage als erlaubt beantragt. Zusätzliche Tage werden von Ihrem Gehalt abgezogen!",
                        icon: "warning",
                        confirmButtonText: "Verstanden"
                    });
                } else {
                    modal.querySelector(".duration_label").style.display = "none";
                    modal.querySelector(".save_button").style.display = "block";
                }
            }
        }

        // Fetch remaining leave days for employee
        function fetchRemainingLeaveDays(empId, year, modal) {
            if (!empId) return console.error("❌ Employee ID is missing.");

            console.log(`📡 Fetching leave days for empId: ${empId}, Year: ${year}`);

            fetch(`/employee/remaining/days/${empId}?year=${year}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) return console.error("❌ Error:", data.error);

                    console.log("✅ Leave Data:", data);

                    modal.querySelector(".leave_day").value = data.total_leave_days;
                    modal.querySelector(".remaining_day").value = data.remaining_days;
                    modal.querySelector(".last_year_remainings").value = data.last_year_remainings;
                })
                .catch(error => console.error("❌ Error fetching leave data:", error));
        }

        


        // Fetch employees on page load
        fetchEmployees();

        // Search employees
        $('#employee_search').on('keyup', function() {
            fetchEmployees($(this).val());
        });


        // Open leave modal and set employee ID
        $(document).on('click', '.sick_creating', function(e) {
            e.preventDefault();
            let empId = $(this).data('id');
            let modal = $('#sickModal');

            modal.modal('show');
            modal.find('input[name="emp_id"]').val(empId);
 
       
        });

        function calculateDaysAndHours() {
            let startDate = new Date($('#start_date').val());
            let endDate = new Date($('#end_date').val()); 

            let totalDays = 0;
            let totalHours = 0;

            if (!isNaN(startDate) && !isNaN(endDate)) {
                // Count only weekdays (Monday-Friday)
                let currentDate = new Date(startDate);
                while (currentDate <= endDate) {
                    let dayOfWeek = currentDate.getDay(); // 0 = Sunday, 6 = Saturday
                    if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                        totalDays++;
                    }
                    currentDate.setDate(currentDate.getDate() + 1);
                }

                totalHours = totalDays * 24; // Default 24 hours per day if no time provided

            
            }

            $('#total_days').val(totalDays);
            $('#total_hours').val(totalHours);
        }

        $('#start_date, #end_date').on('change', calculateDaysAndHours);

        $('#sickForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);
            formData.append("emp_id", $('#emp_id').val()); // ✅ Ensure emp_id is included

            let url = $('#sick_id').val() ? `/employee-sick/update/${$('#sick_id').val()}` : "/employee-sick/store";

            $.ajax({
                url: url,
                method: "POST",
                data: formData,
                processData: false,  // ✅ Prevent jQuery from processing data
                contentType: false,  // ✅ Ensure multipart/form-data is sent
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // ✅ CSRF Token
                },
                success: function (response) {
                    alert(response.success);
                    location.reload();
                },
                error: function (xhr) {
                    alert("Error: " + xhr.responseJSON.message);
                }
            });
        });

        // Open leave modal and set employee ID
      $(document).on('click', '.leave_creating', function(e) {
        e.preventDefault();
        let empId = $(this).data('id');
        let modal = $('#new_leave_modal');

        modal.modal('show');
        modal.find('input[name="emp_id"]').val(empId);

        let year = $("#yearSelect").val();

        // ✅ Ensure fetching is only for leaveModal
        if (modal.is(':visible')) { 
            fetchRemainingLeaveDays(empId, year, modal[0]);
        }
    });


        // Handle year dropdown change
        $("#yearSelect").on("change", function() {
            let modal = $("#new_leave_modal")[0];
            let empId = modal.querySelector('input[name="emp_id"]').value;
            fetchRemainingLeaveDays(empId, this.value, modal);
        });

        // Handle date changes in modal
        $(".modal").on("change", ".leave_start_date, .leave_end_date", function() {
            updateDurationAndRemainingDays(this.closest(".modal"));
        });

        // AJAX form submission for leave request
        $('#new_leave_modal form').on('submit', function(e) {
            e.preventDefault();
            let formData = $(this).serialize();

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                success: function(response) {
                    alert('Leave request submitted successfully');
                    $('#new_leave_modal').modal('hide');
                    fetchEmployees();
                },
                error: function() {
                    alert('Error submitting leave request');
                }
            });
        });

        populateYearDropdown();
    });
</script>


<!-- Counting Employee Status  -->
 <script>
    function fetchEmployeeStatus() {
        fetch('/employee-status') // Update the API route if necessary
            .then(response => response.json())
            .then(data => {
                document.getElementById('active_emp').textContent = data.active_emp;
                document.getElementById('inactive_emp').textContent = data.inactive_emp;
                document.getElementById('sick_emp').textContent = data.sick_count;
                document.getElementById('holiday_emp').textContent = data.holiday_count;
            })
            .catch(error => console.error('Error fetching employee status:', error));
    }

    // Auto-refresh every 30 seconds
    setInterval(fetchEmployeeStatus, 30000);
    fetchEmployeeStatus();

 </script>

@stack('scripts')



</body>

</html>