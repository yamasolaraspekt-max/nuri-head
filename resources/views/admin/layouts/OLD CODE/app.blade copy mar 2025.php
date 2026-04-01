<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Solar Aspekt">
    <meta name="keywords" content="Photovoltaik, WP, PV, Solar, Warmpumpe, Heizung, Heiztechnique,">
    <meta name="author" content="Solar Aspekt">
    <title>SA-DESK - @yield('title')</title>
    <link rel="apple-touch-icon" href="{{ asset('logo/logo_half.png')}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('logo/logo_half.png')}}">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300,500,600" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    


    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/katex.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/monokai-sublime.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css') }}">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/colors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/dark-layout.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/semi-dark-layout.css') }}">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-gradient.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/app-email.css') }}">
    <!-- END: Page CSS-->
 

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/submenus.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/clock.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/right-sider.css') }}">
    <!-- END: Custom CSS-->
    <style>
        .main-menu .navbar-header .navbar-brand .brand-logo {
            background: url("{{ asset('logo/logo_half.png') }}") !important;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }
        .content-header {
            margin-top:115px;
        }
    </style>

    <style>

        #notification_icon {
            display: flex !important;
            flex-direction: column !important;
            position: absolute !important;
            right: -13px;
            top: 54px;
        }

        @media (max-width: 768px) {
            .present_employee, .absent_employee, .nav-search {
                display: none !important;
            }
        }

    </style>

     @yield('style')
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
    --dark-color: #343a30;
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

body .vertical-layout .vertical-menu-modern .menu-collapsed .app-content{
    margin-left: 10px !important;
 
}

.app-content{
       margin-right: 60px !important;
}
.content-header-right {
        margin-left: -51px !important;
}

</style>

 

<style>
    .dashboard-image {
    width: 100%; /* Default for larger screens */
    max-width: 300px; /* Set a reasonable max width */
    height: auto; /* Maintain aspect ratio */
}
 
 
canvas {
    width: auto !important;
    height: auto;
    max-width: 100%;
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
    font-weight: 300;
    border-radius: 4px;
    box-shadow: 0 0 ;
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
.header-navbar-shadow {
    background:transparent !important;
}
</style>

<style>
        
        
      
   #errorPreviewBox img {
        max-width: 100%;
        height: auto;
    }
    .custom-error-tooltip {
        cursor: pointer;
        transition: 0.2s ease;
    }

    .custom-error-tooltip:hover {
        opacity: 0.9;
    }
    #errorPreviewBox::-webkit-scrollbar {
        width: 8px;
    }
    #errorPreviewBox::-webkit-scrollbar-thumb {
        background-color: rgba(0,0,0,0.2);
        border-radius: 4px;
    }


   @media (min-width: 1000px) and (max-width: 1200px) {
        html body .content {
            margin-left: 10px ; 
        } 
    }

    @media (min-width: 1201px) {
        html body .content {
            margin-left: 50px; /* Set your desired margin-left after 1200px */
        } 
    }

    /* //Navagation of top with sidebar  */
    /* Default Navbar (Sidebar Closed) */
    .header-navbar.floating-nav {
        margin: 1.3rem 2.2rem 0;
        border-radius: 0.5rem;
        position: fixed;
        width: calc(100vw - (100vw - 100%) - calc(2.2rem * 2) - 10px);
        z-index: 12;
        right: 0;
        transition: width 0.3s ease-in-out;
    }

    /* Navbar when Sidebar is Open */
    .sidebar-open .header-navbar.floating-nav {
        width: calc(100vw - (100vw - 100%) - calc(2.2rem * 2) - 260px);
    }

    .inactive_employees_ui .show::before {
        background: #ea5555;
        border-color: #ea5555;
    }

    .dropdown-notification .dropdown-menu .dropdown-menu-right::before {
       background: #ea5555;
        border-color: #ea5555;
    }

    </style>
  

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern 2-columns dark-mode navbar-floating footer-static  menu-collapsed pace-done" data-open="click" data-menu="vertical-menu-modern" id="verticalLayout" data-col="content-left-sidebar">

        <!-- BEGIN: Header-->
        <nav class="header-navbar navbar-expand-lg navbar navbar-with-menu floating-nav  " style="background: #f1f1f1;">
            <div class="navbar-wrapper">
                <div class="navbar-container content">
                    <div class="navbar-collapse" id="navbar-mobile">
                
                        <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">   
                            <button id="menu-button">
                                <i class="feather icon-menu"></i>
                            </button>
                            <ul class="nav navbar-nav" id="container_new"> 
                                <!-- ANFRAGE --> 
                                <li class="horizontal_menu_item nav-item" id="menu1">
                                    <a id="menu" href="javascript:void(0);" aria-haspopup="true" aria-expanded="false" class="menu {{ Str::contains(Route::currentRouteName(), 'quiry') ? 'active_menu' : '' }}" 
                                    onclick="toggleMenu(this, 'anfrage_image', 'anfrage_active.png', 'anfrage.png')">
                                        <div class="menu-items">
                                            <img   
                                            alt="Gauge Icon" class="dashboard-image red-icon" 
                                            src="{{ asset('images/dashboard/anfrage.png') }}" 

                                            id="anfrage_image" >

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
                                        @endif"
                                            onclick="toggleMenu(this, 'lead_image', 'lead_active.png', 'lead.png')">
                                        <div class="menu-items">
                                            <img src="{{asset('images/dashboard/lead.png')}}" alt="Gauge Icon" class="dashboard-image red-icon" id="lead_image" > 
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
                                            @endif"
                                        onclick="toggleMenu(this, 'offer_image', 'offer_active.png', 'offer.png')">
                                            
                                        <div class="menu-items">
                                            <img src="{{asset('images/dashboard/offer.png')}}" alt="Gauge Icon" class="dashboard-image red-icon" id="offer_image" > 
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
                                            @endif"
                                            onclick="toggleMenu(this, 'deal_image', 'deal_active.png', 'deal.png')">
                                        <div class="menu-items">
                                            <img src="{{ asset('images/dashboard/deal.png') }}" alt="Gauge Icon"  id="deal_image" class="dashboard-image red-icon" > 
                                            <h6 class="dashboard-title">DEALS</h6>
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
                                            @endif"
                                            onclick="toggleMenu(this, 'project_image', 'project_active.png', 'project.png')">

                                        <div class="menu-items">
                                            <img src="{{asset('images/dashboard/project.png')}}" alt="Gauge Icon" class="dashboard-image red-icon" id="project_image" > 
                                            <h6 class="dashboard-title">AUSFÜHRUNG</h6>
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
                                            @endif"
                                            onclick="toggleMenu(this, 'ticket_image', 'ticket_active.png', 'ticket.png')">
                                        <div class="menu-items">
                                            <img src="{{asset('images/dashboard/ticket.png')}}" alt="Gauge Icon" id="ticket_image" class="dashboard-image red-icon" > 
                                            <h6 class="dashboard-title">KUNDENDIENST</h6>
                                        </div>
                                    </a>
                                    <div class="submenu">
                                        <ul>
                                            <li>
                                                <a href="{{url('/error')}}">
                                                    <i class="feather icon-alert-triangle"></i> FEHLER UND FEHLERHEFT
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('problem_create')}}">
                                                    <i class="fa fa-ticket"></i> Anlegen
                                                </a>
                                            </li>

                                            <li>
                                                <a  href="{{ url('problem_view')}}">
                                                    <i class="fa fa-wrench"></i> Liste
                                                </a>
                                            </li>

                                                
                                        </ul>
                                    </div>
                                </li> 

                                <li class="horizontal_menu_item nav-item ml-2" id="menu9">
                                    <a id="menu" href="javascript:void(0);" aria-haspopup="true" aria-expanded="false" class="menu  
                                            @php
                                                $previousUrl = url()->previous(); 
                                                $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                            @endphp

                                            @if(Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'ticket'))
                                                active_menu
                                            @elseif(Str::contains(Route::currentRouteName(), 'ticket') && Str::contains($previousRoute, 'ticket'))
                                                active_menu
                                            @endif"
                                            >
                                        <div class="menu-items">
                                            <h6 class="dashboard-title">AUFGABEN & KALENDER</h6>
                                        </div>
                                    </a>
                                    <div class="submenu">
                                        <ul>
                                            <li>
                                                <a href="{{ url('personal/task/'.auth()->user()->name) }}">
                                                    <i class="feather icon-check-square"></i>AUFGABENERTEILUNG
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('task_todo/'.auth()->user()->name) }}">
                                                    <i class="feather icon-check-square"></i>  PROJEKT AUFGABEN
                                                </a>
                                            </li>
                                            
                                             <li>
                                                <a href="{{ url('tasks/calendar/personal') }}">
                                                    <i class="feather icon-calendar"></i>MEINE KALENDER
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('appointments') }}">
                                                    <i class="feather icon-check-square"></i>  TERMINLISTE
                                                </a>
                                            </li> 

                                             <li>
                                                <a href="{{ url('lead/overview') }}">
                                                    <i class="feather icon-calendar"></i>PROZESS
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('lead/kanban') }}">
                                                    <i class="feather icon-check-square"></i>  LEAD ÜBERSICHT
                                                </a>
                                            </li> 
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        
                        </div> 
                 
                        <ul class="nav navbar-nav float-right" id="notification_icon">
                           
                            <li class="nav-item nav-search"><a class="nav-link nav-link-search"><i class="ficon feather icon-search"></i></a>
                                <div class="search-input">
                                    <div class="search-input-icon"><i class="feather icon-search primary"></i></div>
                                    <input class="input" type="text" placeholder="Explore Vuexy..." tabindex="-1" data-search="template-list">
                                    <div class="search-input-close"><i class="feather icon-x"></i></div>
                                    <ul class="search-list search-list-main"></ul>
                                </div>
                            </li> 
                            <li class="nav-item nav-time"><a class="nav-link nav-link-time"><i class="ficon feather icon-play clock_section_play"></i></a>
                                    <div class="clock mb-2" id="clock-section" style="display:none"> 
                                        <div class="card-content">
                                            <div class="card-body text-center d-flex  p-0"  > 
                                                <div class="d-flex mr-2 ml-2"> 
                                                    <span class="start_container">
                                                        <i class="fa fa-play" id="start_clock"   ></i> 
                                                        <i class="feather icon-pause  blinking-icon" id="pause_clock" data-start-id=""style="display:none"></i> 
                                                        <p class="font-medium-2">START</p> 
                                                    </span>  
                                                </div>
                                                <div class="col-md-6 d-flex"> 
                                                    <div class="button ">
                                                        <p class="font-weight-bold font-medium-2 mb-0" id="clock"></p> 
                                                            <small id="employee_start_time"> -</small>
                                                            <small id="employee_end_time">00:00</small> 
                                                    </div> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </li>

                            <li class="nav-item nav-log-off">
                                <a href="{{ url('tasks/calendar/personal') }}" class="nav-link nav-link-log-off" >
                                    <i class="ficon feather icon-calendar"></i>
                                </a>
                            </li>
                         
                            <li class="dropdown dropdown-notification nav-item present_employee">
                                <a class="nav-link nav-link-label m-0" href="#" data-toggle="dropdown">
                                    <i class="ficon feather icon-user primary"></i><span class="badge badge-pill badge-primary badge-up active_employee_count">0</span>
                                </a>
                                
                                <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                                    <li class="dropdown-menu-header">
                                        <div class="dropdown-header m-0 p-2">
                                            <h3 class="white mb-1">ANWESENHEIT</h3>
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
                                    <table class="table active_employee_list">

                                    </table> 
                                </ul> 
                            </li> 
                            <li class="dropdown dropdown-notification nav-item absent_employee">
                                <a class="nav-link nav-link-label m-0" href="#" data-toggle="dropdown">
                                    <i class="ficon feather icon-user danger"></i><span class="badge badge-pill badge-danger badge-up inactive_employee_count">0</span>
                                </a>
                                
                                <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right inactive_employees_ui">
                                    <li class="dropdown-menu-header">
                                        <div class="dropdown-header m-0 p-2" style="background: #ea5555;">
                                            <h3 class="white mb-1">ABWESENHEIT </h3> 
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
                            

                                    <table class="table employee_list_inactive">

                                    </table> 
                                </ul> 
                            </li> 
                            <li class="dropdown dropdown-notification nav-item"><a class="nav-link nav-link-label" href="#" data-toggle="dropdown"><i class="ficon feather icon-bell"></i><span class="badge badge-pill badge-primary badge-up">5</span></a>
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
                           <li class="nav-item nav-log-off">
                                <a href="#" class="nav-link nav-link-log-off" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="ficon feather icon-power"></i>
                                </a>
                            </li>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form> 
    
                        </ul>
                    </div>
                </div>
            </div>
        </nav> 
        <ul class="main-search-list-defaultlist d-none">
            <li class="d-flex align-items-center"><a class="pb-25" href="#">
                    <h6 class="text-primary mb-0">Files</h6>
                </a></li>
            <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100" href="#">
                    <div class="d-flex">
                        <div class="mr-50"><img src="../../../app-assets/images/icons/xls.png" alt="png" height="32"></div>
                        <div class="search-data">
                            <p class="search-data-title mb-0">Two new item submitted</p><small class="text-muted">Marketing Manager</small>
                        </div>
                    </div><small class="search-data-size mr-50 text-muted">&apos;17kb</small>
                </a></li>
            <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100" href="#">
                    <div class="d-flex">
                        <div class="mr-50"><img src="../../../app-assets/images/icons/jpg.png" alt="png" height="32"></div>
                        <div class="search-data">
                            <p class="search-data-title mb-0">52 JPG file Generated</p><small class="text-muted">FontEnd Developer</small>
                        </div>
                    </div><small class="search-data-size mr-50 text-muted">&apos;11kb</small>
                </a></li>
            <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100" href="#">
                    <div class="d-flex">
                        <div class="mr-50"><img src="../../../app-assets/images/icons/pdf.png" alt="png" height="32"></div>
                        <div class="search-data">
                            <p class="search-data-title mb-0">25 PDF File Uploaded</p><small class="text-muted">Digital Marketing Manager</small>
                        </div>
                    </div><small class="search-data-size mr-50 text-muted">&apos;150kb</small>
                </a></li>
            <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100" href="#">
                    <div class="d-flex">
                        <div class="mr-50"><img src="../../../app-assets/images/icons/doc.png" alt="png" height="32"></div>
                        <div class="search-data">
                            <p class="search-data-title mb-0">Anna_Strong.doc</p><small class="text-muted">Web Designer</small>
                        </div>
                    </div><small class="search-data-size mr-50 text-muted">&apos;256kb</small>
                </a></li>
            <li class="d-flex align-items-center"><a class="pb-25" href="#">
                    <h6 class="text-primary mb-0">Members</h6>
                </a></li>
            <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="#">
                    <div class="d-flex align-items-center">
                        <div class="avatar mr-50"><img src="../../../app-assets/images/portrait/small/avatar-s-8.jpg" alt="png" height="32"></div>
                        <div class="search-data">
                            <p class="search-data-title mb-0">John Doe</p><small class="text-muted">UI designer</small>
                        </div>
                    </div>
                </a></li>
            <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="#">
                    <div class="d-flex align-items-center">
                        <div class="avatar mr-50"><img src="../../../app-assets/images/portrait/small/avatar-s-1.jpg" alt="png" height="32"></div>
                        <div class="search-data">
                            <p class="search-data-title mb-0">Michal Clark</p><small class="text-muted">FontEnd Developer</small>
                        </div>
                    </div>
                </a></li>
            <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="#">
                    <div class="d-flex align-items-center">
                        <div class="avatar mr-50"><img src="../../../app-assets/images/portrait/small/avatar-s-14.jpg" alt="png" height="32"></div>
                        <div class="search-data">
                            <p class="search-data-title mb-0">Milena Gibson</p><small class="text-muted">Digital Marketing Manager</small>
                        </div>
                    </div>
                </a></li>
            <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="#">
                    <div class="d-flex align-items-center">
                        <div class="avatar mr-50"><img src="../../../app-assets/images/portrait/small/avatar-s-6.jpg" alt="png" height="32"></div>
                        <div class="search-data">
                            <p class="search-data-title mb-0">Anna Strong</p><small class="text-muted">Web Designer</small>
                        </div>
                    </div>
                </a></li>
        </ul>
        <ul class="main-search-list-defaultlist-other-list d-none">
            <li class="auto-suggestion d-flex align-items-center justify-content-between cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100 py-50">
                    <div class="d-flex justify-content-start"><span class="mr-75 feather icon-alert-circle"></span><span>No results found.</span></div>
                </a></li>
        </ul>
        <!-- END: Header-->  
 
    <!-- BEGIN: Content--> 
        <div class="sidebar" id="sidebar" data-color="">
            <div class="sidebar-logo">
                <img src="{{ asset('logo/solar.svg')}}" alt="">
                    <button id="pin-button"><i class="feather icon-edit-1"></i></button>
            </div>
            <div class="sidebar-profile"> 
                <a  class="dropdown-toggle nav-link dropdown-user-link  " href="#" data-toggle="dropdown" style="    justify-items: center;">
                    <span><img class="round" src="{{ asset('images/user/'.auth()->user()->image)}}" style="    border-radius: 50%; box-shadow:0 0;"   alt="avatar" height="40" width="40"></span> 
                    <div class="user-nav d-sm-flex d-none"><span class="user-name text-bold-600">{{
                            DB::table('users')->join('employees', 'employees.id', '=', 'users.name'
                            )->where('users.name', '=',
                            auth()->user()->name)->select('employees.name')->pluck('name')->first()
                            }}</span>
                        <span class="user-status"><small>verfügbar</small></span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item"  href="{{url('/user')}}">  <i class="feather icon-user-check"></i> Aktiv</a>
                    <a class="dropdown-item"  href="{{url('/user')}}">  <i class="feather icon-user-x"></i> abwesend</a>
                    <a class="dropdown-item"  href="{{url('/user')}}">  <i class="feather icon-pause"></i> Mittagspause</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item"  href="{{url('/employee_profile/'.auth()->user()->name)}}">  <i class="feather icon-user"></i> Mein Profile</a>
                    <a class="dropdown-item"  href="{{url('/employee_notifications/'.auth()->user()->name)}}">  <i class="feather icon-bell"></i> Mein Anträge</a>
                    <a class="dropdown-item"  href="{{url('/user')}}">  <i class="feather icon-user"></i> Profil bearbeiten</a>  
                </div>
            
            </div>
            
            <div class="sidebar-header">
                <a href="{{ url('/') }}"><span><i class="feather icon-home"></i> DASHBOARD</span> </a>
            </div>

            <div class="sidebar-content">
                    <ul>
                    <li class="nav-has-submenu" id="project_menus">
                        <a href="javascript:void(0);">
                            <i class="feather icon-folder"></i> PROJEKTMANAGEMENT
                        </a>
                        <ul class="nav-submenu">
                            <!-- Anfrage -->
                            <li class="nav-has-submenu">
                                <a href="javascript:void(0);">
                                    <i class="feather icon-file-text"></i> Anfrage
                                </a>
                                <ul class="nav-submenu">
                                    <li><a href="{{ route('inquiry.create') }}"><i class="fa fa-plus-circle"></i> Anlegen</a></li>
                                    <li><a href="{{ route('my.inquiry.view') }}"><i class="fa fa-list-ol"></i> Meine</a></li>
                                    <li><a href="{{ route('inquiry.view') }}"><i class="fa fa-list"></i> Liste</a></li>
                                    <li><a href="{{ url('inquiry_junklist') }}"><i class="feather icon-slash"></i> Junk</a></li>
                                    <li><a href="{{ url('inquiry_deleted_list') }}"><i class="feather icon-trash"></i> Gelöschte</a></li>
                                </ul>
                            </li>

                            <!-- Leads -->
                            <li class="nav-has-submenu">
                                <a href="javascript:void(0);">
                                    <i class="feather icon-users"></i> Leads
                                </a>
                                <ul class="nav-submenu">
                                    <li><a href="{{ url('new_lead_create') }}"><i class="fa fa-plus-circle"></i> Anlegen</a></li>
                                    <li><a href="{{ url('new_leads') }}"><i class="fa fa-check-circle"></i> Neue</a></li>
                                    <li><a href="{{ url('my_leads') }}"><i class="fa fa-user"></i> Meine</a></li>
                                    <li><a href="{{ route('waiting.loop.leads') }}"><i class="feather icon-refresh-ccw"></i> Warteschleife</a></li>
                                    <li><a href="{{ url('new_lead_view') }}"><i class="fa fa-list"></i> Liste</a></li>
                                    <li><a href="{{ url('lead_junks') }}"><i class="feather icon-slash"></i> Junk</a></li>
                                    <li><a href="{{ url('deleted_leads') }}"><i class="feather icon-trash"></i> Gelöschte</a></li>
                                </ul>
                            </li>

                            <!-- Angebote -->
                            <li class="nav-has-submenu">
                                <a href="javascript:void(0);">
                                    <i class="feather icon-clipboard"></i> Angebote
                                </a>
                                <ul class="nav-submenu">
                                    <li><a href="{{ route('offer.details') }}"><i class="fa fa-plus-circle"></i> Neue</a></li>
                                    <li><a href="{{ url('my_leads') }}"><i class="feather icon-check-circle"></i> Offene</a></li>
                                    <li><a href="{{ url('new_lead_view') }}"><i class="fa fa-list-ol"></i> Fertige</a></li>
                                    <li><a href="{{ url('new_lead_view') }}"><i class="fa fa-comments"></i> Besprechung</a></li>
                                    <li><a href="{{ url('new_lead_view') }}"><i class="fa fa-file"></i> Angebot</a></li>
                                    <li><a href="{{ url('lead_junks') }}"><i class="feather icon-folder"></i> Alle</a></li>
                                </ul>
                            </li>

                            <!-- Deals -->
                            <li class="nav-has-submenu">
                                <a href="javascript:void(0);">
                                    <i class="feather icon-briefcase"></i> Deals
                                </a>
                                <ul class="nav-submenu">
                                    <li><a href="{{ route('deal.details') }}"><i class="fa fa-plus-circle"></i> Neue</a></li>
                                    <li><a href="#"><i class="fa fa-check-circle"></i> Bestätigte</a></li>
                                    <li><a href="{{ route('deal.all.list') }}"><i class="fa fa-list"></i> Alle</a></li>
                                    <li><a href="{{ route('deal.junk.list') }}"><i class="feather icon-slash"></i> Junk</a></li>
                                    <li><a href="{{ route('deal.delete.list') }}"><i class="feather icon-trash"></i> Gelöschte</a></li>
                                </ul>
                            </li>

                            <!-- Ausführung -->
                            <li class="nav-has-submenu">
                                <a href="javascript:void(0);">
                                    <i class="feather icon-tool"></i> Ausführung
                                </a>
                                <ul class="nav-submenu">
                                    <li><a href="{{ route('project.details') }}"><i class="fa fa-folder-plus"></i> Neues</a></li>
                                    <li><a href="{{ route('my.project.details') }}"><i class="fa fa-user"></i> Meine</a></li>
                                    <li><a href="{{ route('project.lists') }}"><i class="fa fa-list"></i> Liste</a></li>
                                    <li><a href="{{ route('project.junk.lists') }}"><i class="feather icon-slash"></i> Junk</a></li>
                                    <li><a href="{{ route('project.delete.lists') }}"><i class="feather icon-trash"></i> Gelöschte</a></li>
                                </ul>
                            </li>

                            <!-- Ticket -->
                            <li class="nav-has-submenu">
                                <a href="javascript:void(0);">
                                    <i class="feather icon-alert-circle"></i> Ticket
                                </a>
                                <ul class="nav-submenu">
                                    <li><a href="#"><i class="fa fa-exclamation-circle"></i> Störung</a></li>
                                    <li><a href="#"><i class="fa fa-tools"></i> Wartung</a></li>
                                    <li><a href="#"><i class="fa fa-thumbs-down"></i> Reklamation</a></li>
                                    <li><a href="#"><i class="fa fa-ambulance"></i> Notdienst</a></li>
                                    <li><a href="#"><i class="fa fa-wrench"></i> Reparatur</a></li>
                                </ul>
                            </li>

                            <li class="nav-has-submenu">
                                <a href="javascript:void(0);">
                                    <i class="feather icon-alert-circle"></i> Aufgaben & Kalender
                                </a>
                                <ul class="nav-submenu">
                                    <li><a href="{{ url('personal/task/'.auth()->user()->name )}}"><i class="fa fa-exclamation-circle"></i> Aufgabenerteilung</a></li>
                                    <li><a href="{{ url('task_todo/'.auth()->user()->name )}}"><i class="fa fa-tools"></i> Projekt Aufgaben</a></li>
                                    <li><a href="{{ url('tasks/calendar/personal/')}}"><i class="fa fa-thumbs-down"></i> Meine Kalender</a></li>
                                    <li><a href="{{ url('appointments')}}"><i class="fa fa-ambulance"></i> Terminliste</a></li>
                                    <li><a href="{{ url('lead/overview')}}"><i class="fa fa-wrench"></i> Prozess</a></li>
                                    <li><a href="{{ url('lead/kanban')}}"><i class="fa fa-wrench"></i> Lead Übersicht </a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                
                    <!-- Main Product Menu -->
                    <li class="nav-has-submenu" data-name="Product" id="product-menu" style="display:none;">
                        <a href="javascript:void(0);">
                            <i class="fa fa-box"></i> PRODUKT
                        </a>
                        <ul class="nav-submenu">

                            <!-- Product Section -->
                            <li class="nav-has-submenu">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-cube"></i> Produkt
                                </a>
                                <ul class="nav-submenu">
                                    <li><a href="{{ route('product.info') }}"><i class="feather icon-info"></i> Produkt Details</a></li>
                                    <li><a href="{{ route('tiles.view') }}"><i class="feather icon-home"></i> Dacheindeckung</a></li> 
                                    <li><a href="{{ route('distributor.distinct') }}"><i class="feather icon-shuffle"></i> Produkt unterscheiden</a></li>

                                    <!-- Product Settings (Submenu) -->
                                    <li class="nav-has-submenu">
                                        <a href="javascript:void(0);">
                                            <i class="feather icon-settings"></i> Produkteinstellungen
                                        </a>
                                        <ul class="nav-submenu">
                                            <li><a href="{{ route('measure.info') }}"><i class="feather icon-sliders"></i> Einheit</a></li>
                                            <li><a href="{{ route('discount_group.info') }}"><i class="fa fa-percent"></i> Rabatt-Gruppe</a></li>
                                            <li><a href="{{ route('article_group.info') }}"><i class="fa fa-layer-group"></i> Artikel-Gruppe</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>

                            <!-- Inventory Section -->
                            <li class="nav-has-submenu">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-warehouse"></i> Inventar
                                </a>
                                <ul class="nav-submenu">
                                    <li><a href="{{ route('product.create.inventory') }}"><i class="fa fa-box-open"></i> Bestandsregistrierung</a></li>
                                    <li><a href="{{ route('product.inventory.search') }}"><i class="feather icon-search"></i> Inventarsuche</a></li>
                                </ul>
                            </li>

                            <!-- Product Delivery -->
                            <li class="nav-has-submenu">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-truck"></i> Produktlieferung
                                </a>
                                <ul class="nav-submenu">
                                    <li>
                                        <a href="{{ route('delivery.note') }}">
                                            <i class="fa fa-file-invoice"></i> Lieferschein <span class="badge badge-warning">1</span>
                                        </a>
                                    </li>
                                    <li><a href="{{ route('product.inventory.search') }}"><i class="feather icon-search"></i> Inventarsuche</a></li>
                                </ul>
                            </li>

                            <!-- Asset Inventory -->
                            <li class="nav-has-submenu">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-qrcode"></i> Vermögensbestand
                                </a>
                                <ul class="nav-submenu">
                                    <li><a href="{{ route('assets.inventory') }}"><i class="fa fa-database"></i> Dateneingabe</a></li>
                                    <li><a href="{{ route('machine.inventory') }}"><i class="fa fa-car"></i> Auto & Machine</a></li>
                                    <li><a href="{{ route('handover.details') }}"><i class="fa fa-exchange-alt"></i> Gegenstände übergeben</a></li>
                                    <li><a href="{{ route('product.inventory.search') }}"><i class="feather icon-search"></i> Inventarsuche</a></li>
                                </ul>
                            </li>

                            <!-- Outgoing Request -->
                            <li class="nav-has-submenu">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-arrow-circle-up"></i> Ausgehend anfordern
                                </a>
                                <ul class="nav-submenu">
                                    <li><a href="{{ route('request.out.create') }}"><i class="feather icon-file-text"></i> Anfrageformular</a></li>
                                    <li><a href="{{ route('request.out.details') }}"><i class="feather icon-list"></i> Anfragedetails</a></li>
                                </ul>
                            </li>

                            <!-- Purchase Request -->
                            <li class="nav-has-submenu">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-shopping-cart"></i> Kaufanfrage
                                </a>
                                <ul class="nav-submenu">
                                    <li><a href="{{ route('purchase.request') }}"><i class="fa fa-file-invoice-dollar"></i> Einzelheiten zur Kaufanfrage</a></li>
                                    <li><a href="{{ route('purchase.request.create') }}"><i class="fa fa-plus-circle"></i> Neue Kaufanfrage</a></li>
                                </ul>
                            </li>

                        </ul>
                    </li>
            
        
                    <!-- Main Partner Menu -->
                    <li class="nav-has-submenu" data-name="Partner" id="partner-menu" style="display:none;">
                        <a href="javascript:void(0);">
                            <i class="fa fa-building"></i> Konaktverwaltung
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="{{ route('brand.info') }}"><i class="fa fa-industry"></i> Hersteller</a></li>
                            <li><a href="{{ route('distributor.info') }}"><i class="fa fa-truck"></i> Lieferant</a></li>
                            <li><a href="{{ route('external.info') }}"><i class="fa fa-users"></i> Zeitarbeitfirma</a></li>
                            <li><a href="{{ route('brand.sub.contractor') }}"><i class="fa fa-tools"></i> Nach Unternehmer</a></li>
                            <li><a href="{{ route('brand.architect') }}"><i class="fa fa-pencil-ruler"></i> Architekten</a></li>
                            <li><a href="{{ route('brand.bank') }}"><i class="fa fa-university"></i> Bank</a></li>
                            <li><a href="{{ route('brand.insurance') }}"><i class="fa fa-shield-alt"></i> Versicherung</a></li>
                            <li><a href="{{ route('brand.others') }}"><i class="fa fa-ellipsis-h"></i> Sonstiges</a></li>
                        </ul>
                    </li>
                

                    <!-- Main Employee Menu -->
                    <li class="nav-has-submenu" data-name="Employee" id="employee-menu" style="display:none;">
                        <a href="javascript:void(0);">
                            <i class="feather icon-users"></i> PERSONALWESEN
                        </a>
                        <ul class="nav-submenu">

                            <!-- Mitarbeiter Details -->
                            <li class="nav-has-submenu">
                                <a href="javascript:void(0);">
                                    <i class="feather icon-copy"></i> Mitarbeiter Details
                                </a>
                                <ul class="nav-submenu">
                                    <li><a href="{{ route('emp.create') }}"><i class="fa fa-user-plus"></i> Mitarbeiter registrieren</a></li>
                                    <li><a href="{{ route('emp.info') }}"><i class="fa fa-user"></i> Mitarbeiter</a></li>
                                </ul>
                            </li>
 
                            <!-- Tagesbericht -->
                            <li class="nav-has-submenu">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-money-bill-wave"></i> Tagesbericht
                                </a>
                                <ul class="nav-submenu">
                                    <li><a href="{{ route('employee.daily.plan') }}"><i class="fa fa-clock"></i> Bericht</a></li>
                                    <li><a href="{{ route('work.place.index') }}"><i class="feather icon-map-pin"></i> Arbeitsplatz</a></li>
                                </ul>
                            </li>

                            <!-- Gehaltsmanagement -->
                            <li class="nav-has-submenu">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-money-bill-wave"></i> Gehaltsmanagement
                                </a>
                                <ul class="nav-submenu">
                                    <li><a href="{{ route('salary.info') }}"><i class="fa fa-calculator"></i> Lohn-Vollkosten</a></li>
                                </ul>
                            </li>

                            <!-- Firmen Gruppe -->
                            <li><a href="{{ route('branch.info') }}"><i class="fa fa-building"></i> Firmen Gruppe</a></li>

                            <!-- Bewerber -->
                            <li><a href="#"><i class="fa fa-user-tie"></i> Bewerber</a></li>

                            <!-- Abteilung & Berufsbezeichnung -->
                            <li class="nav-has-submenu">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-sitemap"></i> Abteilung & Berufsbezeichnung
                                </a>
                                <ul class="nav-submenu">
                                    <li><a href="{{ route('department.info') }}"><i class="fa fa-building"></i> Abteilung</a></li>
                                    <li><a href="{{ route('position.info') }}"><i class="fa fa-briefcase"></i> Position</a></li>
                                    <li><a href="{{ route('department.organize') }}"><i class="fa fa-project-diagram"></i> Organisationsstruktur</a></li>
                                </ul>
                            </li>

                            <!-- Vertragstyp -->
                            <li><a href="{{ route('contract.type.info') }}"><i class="fa fa-file-signature"></i> Vertragstyp</a></li>

                            <!-- Sprachen -->
                            <li><a href="{{ route('language.info') }}"><i class="fa fa-language"></i> Sprachen</a></li>

                            <!-- Land -->
                            <li><a href="{{ route('country.info') }}"><i class="fa fa-globe"></i> Land</a></li>

                            <!-- Feiertagseinstellungen -->
                            <li class="nav-has-submenu">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-calendar-alt"></i> Feiertagseinstellungen
                                </a>
                                <ul class="nav-submenu">
                                    <li><a href="{{ route('holiday.info') }}"><i class="fa fa-toggle-off"></i> Feiertage</a></li>
                                    <li><a href="{{ route('leave.day.info') }}"><i class="fa fa-toggle-off"></i> Urlaubstage</a></li>
                                </ul>
                            </li>

                            <!-- Steuereinstellung -->
                            <li><a href="{{ route('tax.info') }}"><i class="fa fa-percentage"></i> Steuereinstellung</a></li>
                        </ul>
                    </li>
                    
                    <!-- Main Controlling Menu -->
                    <li class="nav-has-submenu" data-name="Finance" id="finance-menu" style="display:none;">
                        <a href="javascript:void(0);">
                            <i class="feather icon-file"></i> CONTROLLING
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="{{ route('invoice.info') }}"><i class="fa fa-file-invoice"></i> Rechnung Details</a></li>
                            <li><a href="{{ route('branch.expense') }}"><i class="fa fa-money-check-alt"></i> Spesenarten für Filialen</a></li>
                            <li><a href="{{ route('assets.installment.show') }}"><i class="fa fa-credit-card"></i> Ratenzahlung</a></li>
                        </ul>
                    </li>
                
                    <!-- Main Benutzerverwaltung Menu -->
                    <li class="nav-has-submenu" data-name="Users" id="user-menu" style="display:none;">
                        <a href="javascript:void(0);">
                            <i class="feather icon-user"></i> BENUTZERVERWALTUNG
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="{{ url('/admin_user') }}"><i class="fa fa-user-shield"></i> Admins</a></li>
                            <li><a href="{{ url('/limit_user') }}"><i class="fa fa-user-lock"></i> Limited</a></li>
                            <li><a href="{{ url('/user_roll') }}"><i class="fa fa-user-cog"></i> User Rolle</a></li>
                            <li><a href="{{ url('/user') }}"><i class="fa fa-user-circle"></i> User Profile</a></li>
                        </ul>
                    </li>
                

                    <li class="nav-has-submenu" data-name="Email" id="email-menu" style="display:none;">
                        <a href="javascript:void(0);">
                            <i class="fa fa-envelope"></i> EMAIL
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="{{ url('/email_view') }}"><i class="fa fa-envelope-open"></i> Email</a></li>
                            <li><a href="{{ url('/email_configuration') }}"><i class="fa fa-cogs"></i> Email Konfigurator</a></li>
                        </ul>
                    </li>


                    <li class="nav-has-submenu">
                    <a href="javascript:void(0);">
                        <i class="fa fa-tasks"></i> TO-DOS
                    </a>
                    <ul class="nav-submenu">
                        <li><a href="{{ route('note.category.view') }}"><i class="fa fa-folder"></i> Kategorie</a></li>
                        <li><a href="{{ route('notes.details') }}"><i class="fa fa-list-alt"></i> To-Do's Details</a></li> 
                    </ul>
                    </li> 

                    <li >
                        <a href="{{ route('chats.view', auth()->user()->name) }}">
                            <i class="feather icon-message-circle"></i> CHAT
                        </a>
                    </li>




                    <li class="nav-has-submenu">
                        <a href="javascript:void(0);">
                            <i class="fa fa-tasks"></i> KONFIGURATION
                        </a>
                        <ul class="nav-submenu">
                                <li class="nav-has-submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-money-bill-wave"></i> SETS
                                    </a>
                                    <ul class="nav-submenu">
                                        <li><a href="{{ route('article.group.set') }}"><i class="fa fa-clock"></i> Master Set</a></li>
                                        <li><a href="{{ route('offer.cover.view') }}"><i class="feather icon-map-pin"></i> Group Set</a></li>
                                    </ul>
                                </li>

                                 <li class="nav-has-submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-money-bill-wave"></i> CHECKLISTE
                                    </a>
                                    <ul class="nav-submenu">
                                        <li><a href="{{ route('task.phase') }}"><i class="fa fa-clock"></i> Arbeitsschritte</a></li> 
                                        <li><a href="{{ route('checklists.index') }}"><i class="fa fa-clock"></i> Checkliste</a></li> 
                                    </ul>
                                </li>
                        </ul>
                    </li> 
                    <li>
                        <a href="{{ route('system.feedback.view') }}">
                            <i class="feather icon-info"></i> FEEDBACK
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('knowledge.base') }}">
                            <i class="fa fa-question-circle"></i> HILFE CENTER
                        </a>
                    </li>




        

                </ul> 
            </div>

            <div class="color-content">
                <div class="custom-control custom-switch switch-lg custom-switch-primary">
                    <input type="checkbox" class="custom-control-input" id="color-switch">
                    <label class="custom-control-label" for="color-switch">
                        <span class="switch-text-left">LIGHT</span>
                        <span class="switch-text-right">DARK</span>
                    </label>
                </div>
            </div>
            
            <div class="logout_app" style="display:none">  
                    @csrf
            </div>


        </div>

 
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

 
        <div class="modal fade text-left" id="plan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel17">Heutiger Arbeitsplan</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form id="employee_planer_form">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="employee_id" value="{{auth()->user()->name}}">
                            <input type="hidden" name="work_place_id" value="">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Titel</th>
                                            <th>Start Time</th>
                                            <th>Zweck</th>
                                            <th>Aktion</th>
                                        </tr>
                                    </thead>
                                    <tbody class="daily_plan_table">
                                        
                                        <tr>
                                            <td>Appointment with Customer</td>
                                            <td>7:00 Am</td>
                                            <td>Max Muller</td>
                                            <td>
                                                <button type="button" class="btn bg-gradient-primary mr-1 mb-1 waves-effect waves-light"
                                                data-id=""
                                                data-appointment-id=""
                                                data-task-id=""
                                                data-customer-id=""
                                                data-alternative-id=""
                                                data-service=""

                                                ><i class="feather icon-play"></i> Starten</button>
                                            </td>
                                        </tr>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal">Abbrechen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
 
     <!-- Floating Error Preview Box -->
      <div id="errorPreviewBox" style="
            position: absolute;
            z-index: 99999;
            display: none;
            width: 600px;
            max-height: 400px;
            overflow: auto;
            background: white;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            padding: 15px;
            font-size: 14px;
            line-height: 1.6;
            word-break: break-word;
            scrollbar-gutter: stable;
            overscroll-behavior: contain;
        ">
        </div>





 

 
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

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
    <script src="{{ asset('js/clock.js')}}"></script> 

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Clock visiable in right sider :start -->
    <script>
        $(document).ready(function() {
            $('.clock_section_play').on('click', function(e) {
                e.preventDefault(); // prevent default anchor behavior
                $('#clock-section').slideToggle(); // toggle with animation
            });
        });
    </script>
    <!-- Clock visiable in right sider :end -->


    <!-- Menu js  -->
     <script>
        function toggleProjectMenu() {
            document.getElementById("projectmenu").classList.toggle("show");
        }

        function toggleProjectSubMenu(submenuId) {
            document.getElementById(submenuId).classList.toggle("show");
        }

        // Close dropdown when clicking outside
        document.addEventListener("click", function (event) {
            const dropdown = document.getElementById("projectmenuDrop");
            const menu = document.getElementById("projectmenu");

            if (dropdown && menu && !dropdown.contains(event.target)) {
                menu.classList.remove("show");
            }
        });
    </script>

<!-- 
    <script>
    async function checkAndStartReport() {
        const response = await fetch('/daily-reports/get/time');
        const data = await response.json();

        // If no report yet for today
        if (!data || !data.start_time) {
            const workplaces = await fetch('/get/work-places'); // Make sure this route exists
            const workplacesData = await workplaces.json();

            let optionsHtml = '';
            workplacesData.forEach(place => {
                optionsHtml += `<option value="${place.id}">${place.place_name}</option>`;
            });

            let clockInterval;
            const { value: workPlaceId } = await Swal.fire({
                title: '<b>Start Your Work</b>',
                html: `
                    <div id="clock" style="font-size: 24px; margin-bottom: 10px;"></div>
                    <select id="work_place_id" class="swal2-select">
                        ${optionsHtml}
                    </select>
                `,
                focusConfirm: false,
                preConfirm: () => {
                    const selectedPlace = document.getElementById('work_place_id').value;
                    if (!selectedPlace) {
                        Swal.showValidationMessage('Please select a workplace');
                        return false;
                    }
                    return selectedPlace;
                },
                didOpen: () => {
                    const clockEl = document.getElementById('clock');
                    clockInterval = setInterval(() => {
                        const now = new Date();
                        clockEl.innerHTML = now.toLocaleTimeString();
                    }, 1000);
                },
                willClose: () => {
                    clearInterval(clockInterval);
                },
                confirmButtonText: 'Start'
            });

            if (workPlaceId) {
                navigator.geolocation.getCurrentPosition(async (position) => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;

                    try {
                        const ipRes = await fetch('https://api.ipify.org?format=json');
                        const ip = (await ipRes.json()).ip;

                        const res = await fetch('/daily-reports/start', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                work_place_id: workPlaceId,
                                lat: lat,
                                lon: lon,
                                ip: ip
                            })
                        });

                        const result = await res.json();

                        if (result.status === 'success') {
                            Swal.fire({
                                icon: result.work_status === 'overtime' ? 'warning' : 'success',
                                title: result.work_status === 'overtime' ? 'Overtime Started' : 'Work Started',
                                text: result.message
                            });
                        } else {
                            Swal.fire('Error', 'Could not start work. Try again.', 'error');
                        }
                    } catch (e) {
                        console.error(e);
                        Swal.fire('Error', 'Failed to start work.', 'error');
                    }
                }, (error) => {
                    Swal.fire('Location Error', 'Please allow location access!', 'error');
                });
            }
        }
    }

    // Automatically trigger on page load, or call this from a button
    document.addEventListener('DOMContentLoaded', checkAndStartReport);
</script>  -->
 
    <!-- time tracking scripts  -->
 <script>
$(document).ready(function () {
   

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // 🔄 Initialize Status
    loadCurrentStatus();

    // 📍 LOAD CURRENT STATUS FUNCTION
    function loadCurrentStatus() {
        $.get('/daily-reports/get/time', function (data) {
            console.log('STATUS:', data);

            if (data && data.status === 'started') {
                $('#employee_start_time').text(data.start_time ?? '-');
                $('#employee_end_time').text(data.end_time ?? '00:00');
                $('#pause_clock').attr('data-start-id', data.id).show();
                $('#start_clock').hide();
            } else if (data && data.status === 'paused') {
                $('#employee_start_time').text(data.start_time ?? '-');
                $('#employee_end_time').text(data.end_time ?? '00:00');
                $('#pause_clock').attr('data-start-id', data.id).show();
                $('#start_clock').hide();
            } else {
                $('#employee_start_time').text('-');
                $('#employee_end_time').text('00:00');
                $('#start_clock').show();
                $('#pause_clock').hide();
            }
        });
    }

    // 🟢 START CLOCK → WORKPLACE SELECTION
    $('#start_clock').on('click', async function () {
        const workPlaces = await $.get('/get/work-places');
        if (!workPlaces.length) {
            Swal.fire('Fehler', 'Keine Arbeitsplätze verfügbar.', 'error');
            return;
        }

        let options = '';
        workPlaces.forEach(place => {
            options += `<option value="${place.id}">${place.place_name} (${place.type})</option>`;
        });

        const result = await Swal.fire({
            title: 'Arbeitsplatz wählen',
            html: `<select id="work_place_select" class="swal2-input">${options}</select>`,
            confirmButtonText: 'Weiter',
            showCancelButton: true,
            preConfirm: () => {
                return document.getElementById('work_place_select').value;
            }
        });

        if (!result.isConfirmed) return;
        const work_place_id = result.value;

        navigator.geolocation.getCurrentPosition(async function (position) {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            const response = await $.post('/daily-reports/start', {
                work_place_id,
                lat,
                lon
            });

            if (response.status === 'success') {
                const now = new Date();
                $('#employee_start_time').text(now.toLocaleTimeString());
                $('#employee_end_time').text("00:00");
                $('#pause_clock').attr('data-start-id', response.id).attr('data-work-place-id', work_place_id).show();
                $('#start_clock').hide();

                loadDailyPlan(response.employee_id, work_place_id);
            }
        }, function () {
            Swal.fire('Fehler', 'Bitte Standortfreigabe erlauben.', 'error');
        });
    });

    // 📅 LOAD DAILY PLAN MODAL
    function loadDailyPlan(employee_id, work_place_id) {
        $.get(`/employee/get/daily/plan/${employee_id}`, function (plan) {
            const tbody = $('.daily_plan_table').empty();

            if (!plan.events || !plan.events.length) {
                tbody.append(`<tr><td colspan="4">Keine Aufgaben gefunden.</td></tr>`);
            } else {
                plan.events.forEach(event => {
                    tbody.append(`
                        <tr>
                            <td>${event.title ?? 'Termin'}</td>
                            <td>${event.start_time}</td>
                            <td>${event.lastname}</td>
                            <td>
                                <button type="button" class="btn bg-gradient-primary start-task-btn"
                                    data-employee-id="${plan.employee_id}"
                                    data-title="${event.title ?? 'Termin'}"
                                    data-task-id="${event.task_id ?? ''}"
                                    data-appointment-id="${event.type === 'appointment' ? event.id : ''}"
                                    data-emp-personal-id="${event.emp_personal_id ?? ''}"
                                    data-customer-id="${event.customer_id ?? ''}"
                                    data-alternative-id="${event.alternative_id ?? ''}"
                                    data-service="${event.service ?? ''}"
                                    data-work-place-id="${work_place_id}">
                                    <i class="feather icon-play"></i> Starten
                                </button>
                            </td>
                        </tr>
                    `);
                });
            }

            setTimeout(() => $('#plan').modal('show'), 200);
        });
    }

    // ▶️ START SPECIFIC TASK
    $(document).on('click', '.start-task-btn', function (e) {
        e.preventDefault(); // ✅ Prevent page reload

        const data = {
            employee_id: $(this).data('employee-id'),
            work_place_id: $(this).data('work-place-id'),
            task_id: $(this).data('task-id'),
            appointment_id: $(this).data('appointment-id'),
            emp_personal_id: $(this).data('emp-personal-id'),
            customer_id: $(this).data('customer-id'),
            alternative_id: $(this).data('alternative-id'),
            service: $(this).data('service'),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        navigator.geolocation.getCurrentPosition(function (position) {
            data.lat = position.coords.latitude;
            data.lon = position.coords.longitude;

            $.post('/daily-reports/start', data, function (res) {
                if (res.status === 'success') {
                    $('#plan').modal('hide');
                    $('#start_clock').hide();
                    $('#pause_clock').attr('data-start-id', res.id).show();

                    const now = new Date();
                    $('#employee_start_time').text(now.toLocaleTimeString());
                    $('#employee_end_time').text("00:00");
                }
            });
        }, function () {
            Swal.fire('Fehler', 'Bitte Standortfreigabe erlauben.', 'error');
        });
    });

    // ⏸️ PAUSE CLOCK
    $('#pause_clock').on('click', function () {
        const reportId = $(this).attr('data-start-id');
        const workPlaceId = $(this).attr('data-work-place-id');

        if (!reportId) {
            Swal.fire('Fehler', 'Report-ID fehlt. Bitte zuerst starten.', 'error');
            return;
        }

        Swal.fire({
            title: 'Pausengrund wählen',
            input: 'select',
            inputOptions: {
                'Afternoon Pause': 'Nachmittagspause',
                'Finish Work': 'Arbeit beenden',
                'Traffic Pause': 'Verkehrspause'
            },
            inputPlaceholder: 'Grund wählen',
            showCancelButton: true
        }).then((result) => {
            if (!result.isConfirmed) return;

            const reason = result.value;

            navigator.geolocation.getCurrentPosition(function (position) {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;

                $.post('/daily-reports/end', {
                    id: reportId,
                    lat,
                    lon,
                    reason,
                    work_place_id: workPlaceId
                }, function (res) {
                    if (res.status === 'success') {
                        const now = new Date();
                        $('#employee_end_time').text(now.toLocaleTimeString());
                        $('#pause_clock').hide();
                        $('#start_clock').show();
                        Swal.fire('Beendet', `Grund: ${reason}`, 'success');
                    }
                });
            });
        });
    });
});
</script>







<!-- Side menu and side profile scripts : start  -->
 
    <script>
        function toggleDropdown() {
            var menu = document.getElementById("mobile_menu");
            if (menu.style.display === "block") {
                menu.style.display = "none";
            } else {
                menu.style.display = "block";
            }
        }
    </script>
    
 <script>
    document.addEventListener("DOMContentLoaded", function () {
    const menuButton = document.getElementById("menu-button");
    const pinButton = document.getElementById("pin-button");
    const sidebar = document.getElementById("sidebar");
    const layout = document.querySelector(".vertical-layout");
    const appContent = document.querySelector(".app-content");
    const submenuItems = document.querySelectorAll(".nav-has-submenu > a");
    const navbar = document.querySelector(".header-navbar.floating-nav");
    let isPinned = false; // Track if sidebar is pinned

    /** Function to update navbar width */
    function updateNavbarWidth() {
        if (layout.classList.contains("menu-expanded")) {
            navbar.style.width = "calc(100vw - (100vw - 100%) - calc(2.2rem * 2) - 260px)";
        } else {
            navbar.style.width = "calc(100vw - (100vw - 100%) - calc(2.2rem * 2) - 10px)";
        }
    }

    /** Toggle Sidebar on Menu Button Click */
    menuButton.addEventListener("click", function () {
        if (!isPinned) {
            sidebar.classList.toggle("open");
            layout.classList.toggle("menu-expanded");
            layout.classList.toggle("menu-collapsed");
            updateNavbarWidth();
        }
    });

    /** Toggle Sidebar Pinning */
    pinButton.addEventListener("click", function () {
        isPinned = !isPinned; // Toggle pin state

        if (isPinned) {
            layout.classList.add("menu-expanded");
            layout.classList.remove("menu-collapsed");
            sidebar.classList.add("open");
            appContent.style.marginLeft = "260px";
        } else {
            layout.classList.remove("menu-expanded");
            layout.classList.add("menu-collapsed");
            sidebar.classList.remove("open");
            appContent.style.marginLeft = "10px";
        }
        updateNavbarWidth();
    });

    /** Collapse Sidebar when Clicking Outside (If Not Pinned) */
    document.addEventListener("click", function (event) {
        if (
            !isPinned && // Only collapse if sidebar is NOT pinned
            !sidebar.contains(event.target) &&
            !menuButton.contains(event.target) &&
            !pinButton.contains(event.target)
        ) {
            layout.classList.remove("menu-expanded");
            layout.classList.add("menu-collapsed");
            sidebar.classList.remove("open");
            appContent.style.marginLeft = "10px";
            updateNavbarWidth();
        }
    });

    /** Accordion-style Submenu Toggle */
    submenuItems.forEach(item => {
        item.addEventListener("click", function (event) {
            event.preventDefault();
            let parent = this.closest("li.nav-has-submenu");
            let submenu = parent.querySelector(":scope > .nav-submenu");

            // Only close siblings, not nested submenus
            let siblings = Array.from(parent.parentElement.children).filter(el =>
                el !== parent && el.classList.contains("nav-has-submenu")
            );

            siblings.forEach(sib => {
                sib.classList.remove("active");
                let sibSub = sib.querySelector(":scope > .nav-submenu");
                if (sibSub) sibSub.style.display = "none";
            });

            // Toggle the clicked submenu
            if (submenu && submenu.style.display === "block") {
                submenu.style.display = "none";
                parent.classList.remove("active");
            } else if (submenu) {
                submenu.style.display = "block";
                parent.classList.add("active");
            }
        });
    });

    

    /** Automatically Collapse Sidebar on Mobile */
    function checkScreenSize() {
        if (window.innerWidth < 768) { // Mobile & tablet screens
            isPinned = false; // Ensure it is not pinned on small screens
            sidebar.classList.remove("open");
            layout.classList.remove("menu-expanded");
            layout.classList.add("menu-collapsed");
            appContent.style.marginLeft = "15px";
            updateNavbarWidth();
        }
    }

    /** Listen for Window Resize */
    window.addEventListener("resize", checkScreenSize);
    checkScreenSize();
    updateNavbarWidth(); // Ensure correct navbar width on page load
});

 </script>

 
<!-- Side menu and side profile scripts : end  -->
<!-- Color Change Start  -->

<script>
    const colorSwitch = document.getElementById('color-switch');

    // If dark mode is default, keep the switch unchecked
    colorSwitch.checked = false;

    colorSwitch.addEventListener('change', function () {
        if (this.checked) {
            // Switch to light mode
            document.body.classList.remove('dark-mode');
        } else {
            // Switch back to dark mode
            document.body.classList.add('dark-mode');
        }
    });
</script>

<!-- Color Change Eend  -->


<!-- check user premisttion start -->
 <script>
$(document).ready(function () {
    // Loop through all elements with data-name attribute
    $('[data-name]').each(function () {
        const $menuItem = $(this);
        const roleName = $menuItem.data('name');
        const userId = {{ auth()->user()->name }}; // or pass via meta tag if not available here


        $.ajax({
            url: `/has_permission/${userId}/${roleName}`,
            type: 'GET',
            success: function (response) {
                if (response.hasPermission) {
                    $menuItem.show(); // Show the menu item if permission is granted
                } else {
                    $menuItem.remove(); // Or hide/remove if not allowed
                }
            },
            error: function () {
                console.error(`Error checking permission for: ${roleName}`);
            }
        });
    });
});
</script>

<!-- check user premisttion end -->

<script>
    function toggleMenu(selectedMenu, imageId, activeImage, inactiveImage) {
        // Remove active state from all menus
        document.querySelectorAll('.menu').forEach(menu => {
            menu.classList.remove("active_menu");
            let img = menu.querySelector("img");
            if (img) {
                let defaultImage = img.getAttribute("data-inactive"); // Retrieve the inactive state image
                img.src = defaultImage;
            }
        });

        // Add active state to clicked menu
        selectedMenu.classList.add("active_menu");
        let selectedImg = document.getElementById(imageId);
        if (selectedImg) {
            selectedImg.src = "{{ asset('images/dashboard/') }}/" + activeImage; // Set active image
        }
    }

    // Store inactive images in data attributes for reference
    document.querySelectorAll('.menu img').forEach(img => {
        img.setAttribute("data-inactive", img.src);
    });
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
    $(document).ready(function () {
    function fetchEmployees(query = '') {
        // Fetch active employees
        $.ajax({
            url: "{{ route('get.employees.leave.status') }}",
            method: 'GET',
            data: { search: query },
            dataType: 'json',
            success: function (data) {
                renderEmployeeList(data, ".active_employee_list"); // ✅ Render active employees
                $(".active_employee_count").text(data.length); // ✅ Update active employee badge count
                $("#active_emp").text(data.length); // ✅ Update active count in dropdown
                countEmployeeStatus(data); // ✅ Count holiday & sick employees
            },
            error: function (xhr) {
                console.error("❌ Error fetching active employees:", xhr.responseText);
            }
        });

        // Fetch inactive employees
        $.ajax({
            url: "{{ route('get.employees.leave.status.inactive') }}",
            method: 'GET',
            data: { search: query },
            dataType: 'json',
            success: function (data) {
                renderEmployeeList(data, ".employee_list_inactive"); // ✅ Render inactive employees
                $(".inactive_employee_count").text(data.length); // ✅ Update inactive employee badge count
                $("#inactive_emp").text(data.length); // ✅ Update inactive count in dropdown
            },
            error: function (xhr) {
                console.error("❌ Error fetching inactive employees:", xhr.responseText);
            }
        });
    }

    function countEmployeeStatus(employees) {
        let sickCount = 0;
        let holidayCount = 0;

        employees.forEach(employee => {
            if (employee.status === 'Sick') sickCount++;
            if (employee.status === 'Holiday') holidayCount++;
        });

        $("#sick_emp").text(sickCount);
        $("#holiday_emp").text(holidayCount);
    }

    function renderEmployeeList(employees, listClass) {
        let employeeList = '';

        employees.forEach(employee => {
            let imagePath = employee.image 
                ? `{{ asset('images/employee/') }}/${employee.image}`
                : `{{ asset('images/gender/male.png') }}`;

            let statusClass = getStatusClass(employee.status);
            let leaveButton = getActionButton(employee.id, "leave");
            let sickButton = getActionButton(employee.id, "sick");

            employeeList += `
                <tr>
                    <td class="pt-0 pb-0">
                        <div class="avatar mr-1">
                            <img src="${imagePath}" alt="${employee.name} ${employee.lastname}" width="32" height="32"> 
                            <span class="${statusClass}"></span>
                        </div>
                    </td>
                    <td class="pt-0 pb-0">
                        <a href="{{ url('employee_profile/') }}/${employee.id}" class="view_profile">
                            <span style="font-weight: bold; color:#505050;">${employee.name} ${employee.lastname}</span>
                        </a> 
                        <p>${employee.status_msg ?? ''}</p>
                        <div class="d-flex">
                            ${leaveButton}
                            ${sickButton}
                        </div>
                    </td> 
                </tr>`;
        });

        $(listClass).html(employeeList);
    }

    function getStatusClass(status) {
        switch (status) {
            case 'Active': return 'avatar-status-online';
            case 'Holiday': 
            case 'Sick': return 'avatar-status-away';
            default: return 'avatar-status-busy';
        }
    }

    function getActionButton(empId, type) {
        if ("{{ auth()->user()->name }}" === "Admin") {
            return `<p class="mr-1"><a href="#" data-id="${empId}" class="${type}_creating">${type === 'leave' ? 'Urlaub' : 'Krankmeldung'}</a></p>`;
        }
        return '';
    }

    $('#employee_search').on('keyup', function () {
        fetchEmployees($(this).val());
    });

    fetchEmployees(); // ✅ Initial load to update counts

    // Sick leave modal handler
    $(document).on('click', '.sick_creating', function(e) {
        e.preventDefault();
        let empId = $(this).data('id');
        let modal = $('#sickModal');

        modal.modal('show');
        modal.find('input[name="emp_id"]').val(empId);
    });

    // Leave modal handler
    $(document).on('click', '.leave_creating', function(e) {
        e.preventDefault();
        let empId = $(this).data('id');
        let modal = $('#new_leave_modal');

        modal.modal('show');
        modal.find('input[name="emp_id"]').val(empId);
        let year = $("#yearSelect").val();

        if (modal.is(':visible')) { 
            fetchRemainingLeaveDays(empId, year, modal[0]);
        }
    });

    $("#yearSelect").on("change", function() {
        let modal = $("#new_leave_modal")[0];
        let empId = modal.querySelector('input[name="emp_id"]').value;
        fetchRemainingLeaveDays(empId, this.value, modal);
    });

    function fetchRemainingLeaveDays(empId, year, modal) {
        if (!empId) return console.error("❌ Employee ID is missing.");
        fetch(`/employee/remaining/days/${empId}?year=${year}`)
            .then(response => response.json())
            .then(data => {
                modal.querySelector(".leave_day").value = data.total_leave_days;
                modal.querySelector(".remaining_day").value = data.remaining_days;
                modal.querySelector(".last_year_remainings").value = data.last_year_remainings;
            })
            .catch(error => console.error("❌ Error fetching leave data:", error));
    }

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
});


</script>


<!-- Counting Employee Status  -->
<script>
    function fetchEmployeeStatus() {
        fetch('/employee-status')
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
@yield('script')


</body>

</html>