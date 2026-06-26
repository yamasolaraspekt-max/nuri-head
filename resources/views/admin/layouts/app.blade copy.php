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
  
    @if(auth()->check())
        <meta name="user-id" content="{{ auth()->id() }}">
        <meta name="chat-user-id" content="{{ auth()->id() }}">
        <meta name="chat-user-name" content="{{ optional(auth()->user()->employee)->display_name }}">
        <meta name="employee-id" content="{{ auth()->user()->name }}">

    @endif

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
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/sidebar.css') }}"> -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/submenus.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/clock.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/right-sider.css') }}"> 
 
        <script src="https://unpkg.com/feather-icons"></script>

    <!-- Sidebar css  -->
     <style>
        .sidebar{
            width:315px;
            position:fixed;
            top:0;
            left:-315px;
            height:100vh;
            z-index:1000;
            display:flex;
            flex-direction:column;
            background:#ffffff;
            color:#1f2937;
            border-right:1px solid #e5e7eb;
            box-shadow:0 10px 30px rgba(15,23,42,.08);
            transition:left .28s ease, box-shadow .28s ease;
            overflow:hidden;
        }

        .sidebar.open{
            left:0;
        }

        .sidebar-logo{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            padding:18px 16px 14px;
            border-bottom:1px solid #e5e7eb;
            background:linear-gradient(180deg,#ffffff 0%, #f9fafb 100%);
            flex-shrink:0;
        }

        .sidebar-logo img{
            width:149px;
            max-width:100%;
            height:auto;
            display:block;
        }

        #menu-button,
        #pin-button{
            width:38px;
            height:38px;
            border:none;
            border-radius:10px !important;
            background:#f3f4f6;
            color:#74b2d4;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            font-size:18px;
            cursor:pointer;
            transition:all .2s ease;
        }

        #menu-button:hover,
        #pin-button:hover{
            background:#e3effb;
            color:#93c21c;
            transform:translateY(-1px);
        }

        .sidebar .sa-profile-menu,
        .sidebar-profile{
            padding:14px 16px;
            border-bottom:1px solid #e5e7eb;
            background:#ffffff;
            flex-shrink:0;
        }

        .sidebar .sa-trigger,
        .sidebar-profile > a,
        .sidebar-profile .dropdown-toggle{
            display:flex !important;
            align-items:center;
            gap:12px;
            width:100%;
            padding:10px 12px;
            border-radius:14px !important;
            background:#f9fafb;
            border:1px solid #eef2f7;
            text-decoration:none !important;
            transition:all .2s ease;
        }

        .sidebar .sa-trigger:hover,
        .sidebar-profile > a:hover,
        .sidebar-profile .dropdown-toggle:hover{
            background:#f4fae7;
            border-color:#d9ebb0;
        }

        .sidebar .sa-trigger img,
        .sidebar-profile img.round{
            width:44px;
            height:44px;
            border-radius:50%;
            object-fit:cover;
            border:2px solid #ffffff;
            box-shadow:0 3px 10px rgba(15,23,42,.10);
        }

        .sidebar .user-nav,
        .sidebar-profile .user-nav{
            display:flex;
            flex-direction:column;
            min-width:0;
        }

        .sidebar .user-nav .user-name,
        .sidebar-profile .user-nav .user-name{
            color:#111827;
            font-size:13px;
            font-weight:800;
            text-transform:uppercase;
            line-height:1.2;
            margin:0;
        }

        .sidebar .user-nav .user-status,
        .sidebar-profile .user-nav .user-status{
            color:#6b7280;
            font-size:11px;
            font-weight:600;
            text-transform:uppercase;
            line-height:1.2;
            margin-top:3px;
        }

        .sidebar-header{
            padding:10px 16px 6px;
            background:#ffffff;
            flex-shrink:0;
        }

        .sidebar-header a{
            display:flex;
            align-items:center;
            gap:10px;
            padding:11px 14px;
            border-radius:12px;
            background:#93c21c;
            color:#ffffff !important;
            text-decoration:none !important;
            font-weight:800;
            letter-spacing:.02em;
            box-shadow:0 8px 20px rgba(147,194,28,.22);
            transition:all .2s ease;
        }

        .sidebar-header a:hover{
            background:#7baa18;
            color:#ffffff !important;
            transform:translateY(-1px);
        }

        .sidebar .sidebar-content{
            flex:1 1 auto;
            overflow-y:auto;
            overflow-x:hidden;
            padding:10px 10px 16px;
            background:#ffffff;
            scrollbar-width:thin;
            scrollbar-color:#d1d5db transparent;
        }

        .sidebar .sidebar-content::-webkit-scrollbar{
            width:8px;
        }

        .sidebar .sidebar-content::-webkit-scrollbar-thumb{
            background:#d1d5db;
            border-radius:999px;
        }

        .sidebar ul{
            list-style:none;
            margin:0;
            padding:0;
        }

        .sidebar-content > ul > li{
            margin-bottom:4px;
        }

        .sidebar-content > ul > li > a,
        .sidebar .nav-has-submenu > a{
            display:flex;
            align-items:center;
            gap:10px;
            width:100%;
            padding:11px 14px;
            border-radius:12px !important;
            color:#1f2937 !important;
            font-size:13px;
            font-weight:700;
            text-decoration:none !important;
            transition:all .2s ease;
            position:relative;
        }

        .sidebar-content > ul > li > a:hover,
        .sidebar .nav-has-submenu > a:hover{
            background:#f9fafb;
            color:#93c21c !important;
        }

        .sidebar-content > ul > li.active > a,
        .sidebar .nav-has-submenu.active > a{
            background:#f4fae7;
            color:#93c21c !important;
            box-shadow:inset 0 0 0 1px #dcefb0;
        }

        .sidebar .nav-has-submenu > a::after{
            content:"";
            position:absolute;
            right:14px;
            top:50%;
            width:8px;
            height:8px;
            border-right:2px solid #9ca3af;
            border-bottom:2px solid #9ca3af;
            transform:translateY(-65%) rotate(45deg);
            transition:transform .2s ease, border-color .2s ease;
        }

        .sidebar .nav-has-submenu.active > a::after,
        .sidebar .nav-has-submenu.submenu-open > a::after{
            transform:translateY(-30%) rotate(-135deg);
            border-color:#93c21c;
        }

        .sidebar .nav-has-submenu > a:hover::after{
            border-color:#93c21c;
        }

        .sidebar .feather,
        .sidebar .fa{
            width:18px;
            min-width:18px;
            text-align:center;
            color:inherit !important;
            font-size:15px;
        }

        .nav-submenu{
            display:none;
            margin:4px 0 8px;
            padding:6px 0 2px 10px;
            border-left:2px solid #eef2f7;
            background:transparent;
        }

        .nav-submenu li{
            margin:0;
            padding:0;
        }

        .nav-submenu li a{
            display:flex;
            align-items:center;
            gap:9px;
            padding:9px 12px 9px 14px;
            margin:2px 0;
            border-radius:10px !important;
            font-size:12.5px;
            font-weight:600;
            color:#6b7280 !important;
            text-decoration:none !important;
            transition:all .2s ease;
            background:transparent;
        }

        .nav-submenu li a:hover{
            background:#f9fafb;
            color:#93c21c !important;
        }

        .nav-submenu li a.active,
        .nav-submenu li a.active-link{
            background:#eff6ff;
            color:#74b2d4 !important;
            box-shadow:inset 0 0 0 1px #dbeafe;
        }

        .nav-submenu .nav-submenu{
            margin-top:4px;
            margin-left:8px;
            padding-left:10px;
            border-left:2px dashed #e5e7eb;
        }

        .nav-submenu .nav-submenu li a{
            font-size:12px;
            padding:8px 10px 8px 12px;
        }

        .logout a{
            padding:0 0 20px 8px;
        }

        .color-content{
            padding:14px 16px 20px;
            border-top:1px solid #e5e7eb;
            background:#ffffff;
            flex-shrink:0;
        }

        .color-content .custom-switch{
            padding-left:0;
        }

        .color-content .custom-control-label{
            font-weight:700;
            color:#374151;
        }

        .sidebar-profile .dropdown-toggle:after{
            border:none !important;
            font-family:"feather";
            content:"\e842" !important;
            position:absolute;
            right:12px;
            top:50%;
            transform:translateY(-50%);
            color:#9ca3af;
            margin:0;
            padding:0;
        }

        .sidebar-profile .dropdown-menu,
        .sidebar .sa-menu{
            min-width:240px;
            background:#ffffff !important;
            border:1px solid #e5e7eb !important;
            border-radius:14px !important;
            box-shadow:0 16px 40px rgba(15,23,42,.14) !important;
            padding:8px !important;
        }

        .sidebar-profile .dropdown-menu a,
        .sidebar .sa-menu .sa-item{
            display:flex;
            align-items:center;
            gap:10px;
            padding:10px 12px;
            border-radius:10px;
            font-size:13px;
            font-weight:600;
            color:#1f2937 !important;
            text-decoration:none !important;
            transition:all .2s ease;
        }

        .sidebar-profile .dropdown-menu a:hover,
        .sidebar .sa-menu .sa-item:hover{
            background:#f9fafb !important;
            color:#93c21c !important;
        }

        /* Small section feeling */
        .sidebar-content > ul > li + li{
            margin-top:2px;
        }

        /* Dark mode */
        body.dark-mode .sidebar{
            background:#1f2d3d !important;
            color:#e5e7eb;
            border-right:1px solid rgba(255,255,255,.08);
            box-shadow:0 10px 30px rgba(0,0,0,.28);
        }

        body.dark-mode .sidebar-logo,
        body.dark-mode .sidebar-header,
        body.dark-mode .sidebar .sa-profile-menu,
        body.dark-mode .sidebar-profile,
        body.dark-mode .color-content,
        body.dark-mode .sidebar .sidebar-content{
            background:#1f2d3d !important;
            border-color:rgba(255,255,255,.08) !important;
        }

        body.dark-mode #menu-button,
        body.dark-mode #pin-button{
            background:#2b3c4f;
            color:#74b2d4;
        }

        body.dark-mode #menu-button:hover,
        body.dark-mode #pin-button:hover{
            background:#314b62;
            color:#93c21c;
        }

        body.dark-mode .sidebar .sa-trigger,
        body.dark-mode .sidebar-profile > a,
        body.dark-mode .sidebar-profile .dropdown-toggle{
            background:#2a3a4d;
            border-color:#314356;
        }

        body.dark-mode .sidebar .user-nav .user-name,
        body.dark-mode .sidebar-profile .user-nav .user-name{
            color:#ffffff;
        }

        body.dark-mode .sidebar .user-nav .user-status,
        body.dark-mode .sidebar-profile .user-nav .user-status{
            color:#9fb3c8;
        }

        body.dark-mode .sidebar-header a{
            background:#93c21c;
            color:#ffffff !important;
        }

        body.dark-mode .sidebar-content > ul > li > a,
        body.dark-mode .sidebar .nav-has-submenu > a{
            color:#e5e7eb !important;
        }

        body.dark-mode .sidebar-content > ul > li > a:hover,
        body.dark-mode .sidebar .nav-has-submenu > a:hover{
            background:#2b3c4f;
            color:#93c21c !important;
        }

        body.dark-mode .sidebar-content > ul > li.active > a,
        body.dark-mode .sidebar .nav-has-submenu.active > a{
            background:#2c4356;
            color:#93c21c !important;
            box-shadow:inset 0 0 0 1px rgba(147,194,28,.22);
        }

        body.dark-mode .nav-submenu{
            border-left-color:#314356;
        }

        body.dark-mode .nav-submenu li a{
            color:#aebdcb !important;
        }

        body.dark-mode .nav-submenu li a:hover{
            background:#2b3c4f;
            color:#93c21c !important;
        }

        body.dark-mode .nav-submenu li a.active,
        body.dark-mode .nav-submenu li a.active-link{
            background:#314b62;
            color:#74b2d4 !important;
            box-shadow:inset 0 0 0 1px rgba(116,178,212,.20);
        }

        body.dark-mode .nav-submenu .nav-submenu{
            border-left-color:#3a4d61;
        }

        body.dark-mode .sidebar-profile .dropdown-menu,
        body.dark-mode .sidebar .sa-menu{
            background:#243447 !important;
            border-color:#314356 !important;
        }

        body.dark-mode .sidebar-profile .dropdown-menu a,
        body.dark-mode .sidebar .sa-menu .sa-item{
            color:#e5e7eb !important;
        }

        body.dark-mode .sidebar-profile .dropdown-menu a:hover,
        body.dark-mode .sidebar .sa-menu .sa-item:hover{
            background:#2b3c4f !important;
            color:#93c21c !important;
        }

        body.dark-mode .color-content .custom-control-label{
            color:#dbe4ec;
        }

        /* Mobile */
        @media (max-width: 768px){
            .sidebar{
                width:290px;
                left:-290px;
            }

            .sidebar.open{
                left:0;
            }

            .sidebar-logo{
                padding:14px 12px 12px;
            }

            .sidebar .sa-profile-menu,
            .sidebar-profile,
            .sidebar-header,
            .color-content{
                padding-left:12px;
                padding-right:12px;
            }

            .sidebar .sidebar-content{
                padding:8px 8px 14px;
            }
        }
     </style>
 
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
        /* optional: thin scrollbars */
        * { scrollbar-width: thin; }
        *::-webkit-scrollbar { height: 8px; width: 8px; }
        *::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
    </style>

    <style>

        #notification_icon {
            display: flex !important;
            flex-direction: column !important;
            position: absolute !important;
            right: -34px;
            top: -11px;
            background: #2c3e4f;
        }

      
        @media (max-width: 768px) {
            .present_employee, .absent_employee, .nav-search {
                display: none !important;
            }
        }


        /* Make navbar items scroll horizontally when screen is narrow */
         


    </style>
    <style>
        #notification_icon li {
            transition: all 0.3s ease-in-out;
        }
    </style>

     <style>
        /* 1. Custom Scrollbar Styling (Optional but makes it look better) */
        #qs-sub-department::-webkit-scrollbar {
            width: 6px;
        }
        #qs-sub-department::-webkit-scrollbar-track {
            background: #f1f1f1; 
        }
        #qs-sub-department::-webkit-scrollbar-thumb {
            background: #888; 
            border-radius: 3px;
        }
        #qs-sub-department::-webkit-scrollbar-thumb:hover {
            background: #555; 
        }

        /* 2. Avatar Stack Styles */
        .dept-link {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding: 10px 15px !important;
            border-bottom: 1px solid #f0f0f0; /* Separator line */
        }
        .avatar-stack {
            display: flex;
            padding-left: 10px;
        }
        .avatar-stack img {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 2px solid #fff;
            margin-left: -8px;
            object-fit: cover;
            background: #eee;
        }
        .avatar-stack img:first-child {
            margin-left: 0;
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
         background-color: var(--app-bg) !important;
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

        button,
            input,
            .nav-link,
            select,
            .list-group-item {
                border-radius: 10px !important;
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
        border-radius: 16px;
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


    <style>
            /* Overlay */
        .search-input-overlay{
        position:fixed; inset:0; z-index:1050;
        background:rgba(255,255,255,0.95);
        display:flex; justify-content:flex-start; align-items:flex-start;
        padding:80px 30px 30px;
        }

        .btn-restore-item {
            border-radius = 20px !important;
        }

        /* Box */
        .search-box-wrapper{
        width:100%;
        margin-left:auto; margin-top:1%;
        background:#2c3e50; color:#ecf0f1;
        padding:16px 16px 10px; border-radius:8px;
        }

        /* Hint & help */
        .search-hint{
        background:rgba(255,255,255,.06);
        border:1px solid rgba(255,255,255,.12);
        border-radius:6px; padding:8px 10px; color:#ecf0f1;
        }
        .search-hint code{ background:rgba(255,255,255,.12); color:#fff; padding:1px 6px; border-radius:4px; }
        .search-help .card{ background:#34495e; border:1px solid rgba(255,255,255,.12); color:#ecf0f1; }
        .search-help kbd{ background:#111; color:#fff; padding:2px 6px; border-radius:4px; }

        /* Results */
        .search-results{
        max-height:400px; overflow-y:auto; list-style:none; padding:0; margin:0;
        }
        .search-results li{
        padding:10px 12px; border-bottom:1px solid rgba(255,255,255,.12);
        }
        .search-results li:hover{
        background:#cfe09b; cursor:pointer; color:#000;
        }
        .search-results li:hover *{ color:#000 !important; }

        /* Selected row class used by JS */
        .search-results li.bg-primary{ background:#007bff !important; }
        .search-results li.bg-primary *{ color:#fff !important; }

            /* Blur deleted rows in global search */
        .search-result-deleted {
            opacity: .45;
            filter: grayscale(.9);
        }

        /* Keep restore button “normal” on blurred rows */
        .search-result-deleted .btn-restore-item {
            opacity: 1;
            filter: none;
        }

        .btn-restore-item {
            border-radius:20px !important;
        }

    </style>

    <style>
        .chat-message.incoming .message-bubble {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .chat-message .avatar {
            border-radius: 50%;
        }
        #notification_icon li i {
            color: white !important;
        }

            #notification_icon li .badge.badge-up{
                
                top: 4px !important;
                right: 17px !important;
        }
    </style>
    
    <!-- notification  -->
    <style>
            /* Backdrop */
        .qs-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.35);
        opacity: 0;
        visibility: hidden;
        transition: .2s ease;
        z-index: 1040; /* above navbar */
        }

        /* Sider */
        .qs-sider {
        position: fixed;
        top: 0;
        right: -600px; /* hidden */
        width: 500px;
        max-width: 92vw;
        height: 100vh;
        background: #fff;
        box-shadow: -8px 0 24px rgba(0,0,0,.15);
        z-index: 1041;
        display: flex;
        flex-direction: column;
        transition: right .25s ease;
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
        }

        /* header */
        .qs-header {
        padding: 12px 16px;
        border-bottom: 1px solid #eee;
        }

        /* content scroll */
        .qs-content {
        padding: 12px 14px 18px;
        overflow-y: auto;
        }

        /* 4-per-row icon grid */
        .qs-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr)); /* ALWAYS 4 per row */
        gap: 10px;
        }

        .qs-tile {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 12px 8px;
        border-radius: 12px;
        background: #314b62;
        border: 1px solid #eef1f4;
        cursor: pointer;
        text-decoration: none !important;
        color: inherit;
        position: relative;
        transition: transform .12s ease, box-shadow .12s ease, background .12s ease;
        }
        .qs-tile:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,.06);
        background: #f0f6fb;
        }

        .qs-tile i {
        font-size: 20px;
        line-height: 1;
        margin-bottom: 6px;
        }
        .qs-tile span {
        font-size: .78rem;
        font-weight: 600;
        }

        /* small round badge in corner */
        .qs-badge {
        position: absolute;
        top: 6px;
        right: 8px;
        font-size: .65rem;
        padding: 2px 6px;
        border-radius: 10px;
        }

        /* Open state */
        body.qs-open {
        overflow: hidden;
        }
        body.qs-open #quickSider {
        right: 0;
        }
        body.qs-open #quickSiderBackdrop {
        opacity: 1;
        visibility: visible;
        }

        /* Dark mode friendly (optional) */
    
        .qs-sider { background: #2c3e50; color: #e7eaf0; }
        .qs-header { border-bottom-color: #2a2f36; }
        .qs-tile { background: #2c3e50; border-color: #262b33; }
        .qs-tile:hover { background: #20252c; box-shadow: none; }
    


        .quick_sider {
                list-style: none;
            font-size: 20px;
            position: absolute;
            top: 67px;
            right: -41px;
            background: rgb(44 62 80);
            width: 44px;
        }

        /* Z-indices (notification above quick sider) */
        :root{
        --z-qs-backdrop: 1040;
        --z-qs: 1041;
        --z-notif-backdrop: 1200;
        --z-notif: 1201;
        }

        /* Backdrop / Sider use variables */
        .qs-backdrop { z-index: var(--z-qs-backdrop); }
        .qs-sider { z-index: var(--z-qs); }

        /* If you have a notification sidebar/backdrop, ensure: */
        #notificationBackdrop { z-index: var(--z-notif-backdrop) !important; }
        #notificationSidebar  { z-index: var(--z-notif) !important; }

        /* Responsive grid (2/3/4 columns) */
        .qs-grid{
        display:grid;
        grid-template-columns: repeat(2, minmax(0,1fr));
        gap:10px;
        }
        @media (min-width: 576px){
        .qs-grid{ grid-template-columns: repeat(3, minmax(0,1fr)); }
        }
        @media (min-width: 992px){
        .qs-grid{ grid-template-columns: repeat(4, minmax(0,1fr)); }
        }

        /* Tiles */
        .qs-tile{
        display:flex; flex-direction:column; align-items:center; text-align:center;
        padding:12px 8px; border-radius:12px; background:#f7f9fb; border:1px solid #eef1f4;
        cursor:pointer; text-decoration:none!important; color:inherit; position:relative;
        transition:transform .12s ease, box-shadow .12s ease, background .12s ease;
        }
        .qs-tile:hover{ transform:translateY(-2px); box-shadow:0 6px 16px rgba(0,0,0,.06); background:#f0f6fb; }
        .qs-tile i{ font-size:20px; line-height:1; margin-bottom:6px; }
        .qs-tile span{ font-size:.78rem; font-weight:600; }

        /* Caret inside tiles that toggle submenus */
        .qs-caret{
        position:absolute; right:8px; top:8px; font-size:16px; opacity:.65;
        transition: transform .15s ease;
        }
        .qs-has-sub.open .qs-caret{ transform: rotate(180deg); }

        /* Inline submenu */
        .qs-sub{
        margin-top:6px; border-left:0; padding-left:10px;
        overflow:hidden; /* animate height smoothly */
        transition: height .2s ease, opacity .2s ease;
        }
        .qs-sub[hidden]{ display:block !important; height:0; opacity:0; }
        .qs-has-sub.open > .qs-sub{ height:auto; opacity:1; }

        .qs-sub-item{
        display:flex; align-items:center; gap:6px;
        padding:8px 10px; border-radius:8px; background:#f9fbfe; border:1px solid #eef1f4;
        font-size:.84rem; margin:6px 0; text-decoration:none!important; color:inherit;
        }
        .qs-sub-item:hover{ background:#eef6ff; }

        /* Scroll area stays scrollable without clipping popouts (we no longer need absolute dropdowns) */
        .qs-content{ padding:12px 14px 18px; overflow:auto; }

        /* Dark mode polish */
        
        .qs-sider { background:#2c3e50; color:#e7eaf0; }
        .qs-header { border-bottom-color:#2a2f36; }
        .qs-tile { background:#314b62; border-color:#2a3a4a; }
        .qs-tile:hover { background:#2b4257; box-shadow:none; }
        .qs-sub-item{ background:#2b4257; border-color:#2a3a4a; }
        .qs-sub-item:hover{ background:#37526a; }
        

        
    </style>
    
    <style>
            :root{
        --z-qs-backdrop: 1040;
        --z-qs: 1041;
        --z-notif-backdrop: 1200;
        --z-notif: 1201;
        }

        /* Backdrop */
        .notif-backdrop{
        position:fixed; inset:0;
        background:rgba(0,0,0,.35);
        opacity:0; visibility:hidden; transition:.2s ease;
        z-index:var(--z-notif-backdrop);
        }

        /* Sider */
        .notif-sider{
        position:fixed; top:0; right:-430px;
        width:400px; max-width:92vw; height:100vh;
        background:#fff; color:#1e293b;
        box-shadow:-10px 0 30px rgba(0,0,0,.15);
        border-top-left-radius:14px; border-bottom-left-radius:14px;
        display:flex; flex-direction:column;
        transition:right .25s ease, box-shadow .25s ease;
        z-index:var(--z-notif);
        }
        body.notif-open .notif-sider{ right:0; }
        body.notif-open .notif-backdrop{ opacity:1; visibility:visible; }

        /* Header */
        .notif-header{
        display:flex; align-items:center; justify-content:space-between;
        padding:12px 14px; border-bottom:1px solid #eef2f7;
        position:sticky; top:0; background:inherit; z-index:2;
        }

        /* Controls */
        .notif-controls{ padding:10px 14px; border-bottom:1px solid #eef2f7; }
        .notif-chips{ display:flex; flex-wrap:wrap; gap:6px; }
        .chip{
        border:1px solid #e5eaf1; background:#f7f9fc; color:#334155;
        padding:6px 10px; border-radius:999px; font-size:.85rem;
        }
        .chip.active{ background:#e9f2ff; border-color:#cfe2ff; }

        /* List */
        .notif-list{
        padding:10px 12px 14px;
        overflow:auto; flex:1 1 auto;
        background:linear-gradient(#fff, #fff) padding-box,
                    radial-gradient(circle at top right, #f0f7ff, transparent 50%) border-box;
        }

        /* Card */
        .notif-card{
        display:grid; grid-template-columns:40px 1fr auto; gap:10px;
        padding:10px; border:1px solid #eef2f7; border-radius:12px;
        background:#fff; box-shadow:0 1px 0 rgba(0,0,0,.03);
        margin-bottom:10px;
        }
        .notif-card.unread{ border-color:#cfe2ff; background:#f8fbff; }
        .notif-icon{
        width:40px; height:40px; border-radius:10px; display:grid; place-items:center;
        background:#eef2f7;
        }
        .notif-body h6{ margin:0; font-weight:700; font-size:0.95rem; color:#0f172a; }
        .notif-body p{ margin:4px 0 0; font-size:.88rem; color:#334155; }
        .notif-meta{ text-align:right; white-space:nowrap; font-size:.78rem; color:#64748b; }
        .notif-actions{ margin-top:8px; display:flex; gap:8px; }
        .notif-actions .btn-sm{ padding:3px 8px; font-size:.75rem; }

        /* Footer */
        .notif-footer{ padding:8px 14px; border-top:1px solid #eef2f7; }

        /* Dark mode */
        @media (prefers-color-scheme: dark) {
        .notif-sider{ background:#1c2836; color:#e7eef7; }
        .notif-header, .notif-controls, .notif-footer{ border-color:#223244; }
        .chip{ background:#253447; border-color:#2c4058; color:#dbe6f4; }
        .chip.active{ background:#2d4360; border-color:#3b5f87; }
        .notif-list{ background:#1c2836; }
        .notif-card{ background:#203044; border-color:#2a3c52; box-shadow:none; }
        .notif-card.unread{ background:#1f3550; border-color:#2e507b; }
        .notif-icon{ background:#2a3c52; }
        .notif-body h6{ color:#f2f7ff; }
        .notif-body p{ color:#cfe1ff; }
        .notif-meta{ color:#a4b9d4; }
        }

    </style>

    <style> 

        /* container */
        .sa-profile-menu { position: relative; }

        /* menu */
        .sa-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 6px);
        min-width: 240px;
        background: #111827; /* slate-900-ish */
        color: #e5e7eb;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 12px;
        padding: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        z-index: 9999;
        }
        .sa-menu[hidden] { display: none !important; }

        /* items */
        .sa-item {
        width: 100%;
        display: flex;
        align-items: center;
        gap: .5rem;
        padding: 10px 12px;
        border: 0;
        background: transparent;
        color: inherit;
        text-align: left;
        text-decoration: none;
        cursor: pointer;
        border-radius: 8px;
        }
        .sa-item:hover,
        .sa-item:focus {
        outline: none;
        background: rgba(255,255,255,0.06);
        }

        /* divider */
        .sa-divider {
        height: 1px;
        margin: 6px 8px;
        background: rgba(255,255,255,0.08);
        }

    </style>

    <!-- collaps anwesend  -->
    <style>
            /* Tiles (buttons) */
        .sa-tile {
        display:inline-flex; align-items:center; gap:.5rem;
        padding:.5rem .75rem; border-radius:.75rem !important; border:1px solid rgba(0,0,0,.08);
        background:#304b61; cursor:pointer; user-select:none;
        color:white;
        }
        .sa-tile:focus { outline:2px solid rgba(0,0,0,.2); outline-offset:2px; }
        .sa-text-present { color:#1d4ed8; } /* primary-ish */
        .sa-text-absent  { color:#dc2626; } /* danger-ish */

        /* Badges */
        .sa-badge { display:inline-block; padding:.125rem .5rem; border-radius:999px; font-size:.75rem; line-height:1; }
        .sa-badge-present {     background: #dbeafe;
            color: #1e40af;
            position: relative;
            top: -19px;
            left: -33px; }
        .sa-badge-absent  { background:#fee2e2; color:#991b1b;  position: relative;
            top: -19px;
            left: -33px;}

        /* Panels */
        .sa-collapse { overflow:hidden; transition:height .22s ease; }
        .sa-collapse[hidden] { display:none !important; }

        .sa-card { background:#fff; border:1px solid rgba(0,0,0,.08); border-radius:.75rem; }
        .sa-card-header { border-bottom:1px solid rgba(0,0,0,.06); border-top-left-radius:.75rem; border-top-right-radius:.75rem; }

        /* Inputs */
        .sa-input-group { display:flex; gap:.5rem; }
        .sa-input { flex:1; padding:.5rem .75rem; border:1px solid #e5e7eb; border-radius:.5rem; }
        .sa-btn { padding:.5rem .75rem; border:1px solid #e5e7eb; background:#f9fafb; border-radius:.5rem; cursor:pointer; }

        .sa-table { width:100%; }

    </style>
    <style>
        .search-results li[data-url][data-deleted="true"] {
        opacity: 0.6;
        filter: grayscale(60%);
        }
    </style>
    <style>
        /* ============================================================
        QUICK MENU (Hover Strip) — CLEAN + EASY TO HOVER + ON TOP
        ============================================================ */

        /* Prevent parent containers from clipping the dropdown */
        .header-navbar,
        .navbar-wrapper,
        .navbar-container,
        .navbar-collapse,
        .nav-right{
        overflow: visible !important;
        }

        /* Wrapper: stable stacking context + bigger hover area */
        .quick_sider_wrapper{
        position: relative;
        display: flex;
        align-items: center;
        height: 100%;
        padding: 10px 8px;              /* bigger hover hitbox */
        z-index: 12000;                 /* keep above navbar items */
        isolation: isolate;             /* new stacking context */
        }

        /* Panel container */
        .hover-quick-strip{
        position: absolute;
        top: calc(100% + 10px);         /* directly below QUICK MENU button */
        right: 0;

        width: 210px;                   /* space for icon + label */
        background: #2c3e50;
        border-radius: 18px;
        padding: 12px 10px;

        display: flex;
        flex-direction: column;
        gap: 8px;

        border: 1px solid rgba(255,255,255,.10);
        box-shadow: 0 10px 40px rgba(0,0,0,.25);

        /* hidden by default */
        opacity: 0;
        visibility: hidden;
        transform: translateY(-4px) scale(.98);
        pointer-events: none;

        transition: opacity .15s ease, transform .15s ease, visibility .15s ease;
        z-index: 13000;
        }

        /* Bridge: prevents "hard to select" (fills the gap between button & panel) */
        .hover-quick-strip::before{
        content: "";
        position: absolute;
        left: -16px;
        right: -16px;
        top: -24px;                     /* area above panel */
        height: 26px;
        background: transparent;
        }

        /* Show on hover / focus */
        .quick_sider_wrapper:hover .hover-quick-strip,
        .quick_sider_wrapper:focus-within .hover-quick-strip{
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
        pointer-events: auto;
        }

        /* Links: full-width rows (easy to click) */
        .hover-quick-strip .hqs-link{
        width: 100%;
        height: 40px;
        border-radius: 12px;

        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 10px;

        padding: 0 12px;
        text-decoration: none !important;

        /* WHITE TEXT (requested) */
        color: #fff !important;

        transition: background .15s ease, transform .15s ease;
        }

        /* Icons: keep white */
        .hover-quick-strip .hqs-link i{
        font-size: 18px;
        min-width: 18px;
        color: #fff !important;
        }

        /* Labels: white */
        .hover-quick-strip .hqs-text{
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
        color: #fff !important;
        }

        /* Hover */
        .hover-quick-strip .hqs-link:hover{
        background: rgba(255,255,255,.12);
        transform: translateX(2px);
        }

        /* Divider */
        .hover-quick-strip .hqs-divider{
        width: 100%;
        height: 1px;
        background: rgba(255,255,255,.10);
        margin: 4px 0;
        }

        /* Badge */
        .hover-quick-strip .hqs-badge{
        position: absolute;
        top: -2px;
        right: -2px;
        font-size: 9px;
        padding: 2px 4px;
        border-radius: 10px;
        border: 1px solid #2c3e50;
        }

        /* Danger hover: red */
        .hover-quick-strip .hqs-link.hqs-danger:hover{
        background: rgba(234,84,85,.18);
        }
        .hover-quick-strip .hqs-link.hqs-danger:hover,
        .hover-quick-strip .hqs-link.hqs-danger:hover i,
        .hover-quick-strip .hqs-link.hqs-danger:hover .hqs-text{
        color: #ea5455 !important;
        }

        /* Tooltips above everything */
        .tooltip,
        .popover{
        z-index: 20000 !important;
        }

        /* QUICK MENU button (nav link) default color */
        #openSiderBtn,
        #openSiderBtn i{
        color: #74b2d4 !important;
        }

        /* Hover: bold + keep same color */
        #openSiderBtn:hover{
        font-weight: 800 !important;
        color: #74b2d4 !important;
        }

        /* Hover icon too */
        #openSiderBtn:hover i{
        color: #74b2d4 !important;
        }

    </style>

    <style>
        /* Sleek Card Design */
        .activity-card {
            background: #ffffff;
            border: 1px solid #eef2f7;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 10px;
            display: grid;
            grid-template-columns: 36px 1fr auto;
            gap: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
            transition: all 0.3s ease;
        }
        
        .activity-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            transform: translateY(-2px);
        }

        .activity-icon-wrapper {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .activity-body h6 {
            font-size: 0.9rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 3px;
        }

        .activity-body p {
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 0;
            line-height: 1.4;
        }

        .activity-meta {
            text-align: right;
            font-size: 0.75rem;
            color: #adb5bd;
        }

        /* Flash Animation for NEW incoming events */
        @keyframes flashActivity {
            0% { background-color: var(--success-color); border-color: var(--success-color); }
            100% { background-color: #ffffff; border-color: #eef2f7; }
        }
        .flash-new {
            animation: flashActivity 2s ease-out;
        }

        /* Filter Section */
        .activity-filters {
            background: #f8f9fa;
            border-bottom: 1px solid #eef2f7;
        }
        
        .activity-filter-btn {
            transition: 0.2s;
        }
        .activity-filter-btn.active {
            background-color: #ffffff;
            color: var(--primary-color);
        }

        /* Select2 fixes for sidebar */
        .select2-container--default .select2-selection--single {
            border: 1px solid #dce1e7 !important;
            border-radius: 6px !important;
            height: 34px !important;
        }
    </style>
    
    <style>
        :root {
            --fb-chat-width: 320px;
            --fb-sidebar-width: 70px;
            --fb-header-height: 40px;
            --fb-primary: #569ad8;
            --fb-bg: #fff;
            --fb-border: #ddd;
            --fb-text: #333;
            --fb-incoming: #f1f0f0;
            --fb-outgoing: #e3f2fb;
        }

        /* Right Sidebar (The Strip) */
    #fb-right-sidebar {
            position: fixed;
            top: 88px;
            right: 0;
            bottom: 0;
            width: var(--fb-sidebar-width);
            background: #fff;
            border-left: 1px solid var(--fb-border);
            z-index: 900;            /* behind strip */
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 15px;
            overflow-y: auto;
            scrollbar-width: thin;
            transition: transform 0.3s ease-in-out;
            isolation: isolate;      /* prevents unexpected stacking interactions inside */
            }

            .activity-mode-tabs {
                display: flex;
                gap: 8px;
                padding: 12px 14px;
                background: #ffffff;
                border-bottom: 1px solid #e5e7eb;
            }

            .activity-mode-tab {
                flex: 1;
                border: 1px solid #e5e7eb;
                background: #f9fafb;
                color: #6b7280;
                border-radius: 12px;
                padding: 9px 10px;
                font-size: 13px;
                font-weight: 900;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 7px;
                cursor: pointer;
            }

            .activity-mode-tab.active {
                background: #93c21c;
                border-color: #93c21c;
                color: #ffffff;
                box-shadow: 0 8px 18px rgba(147, 194, 28, .22);
            }

            .activity-mode-tab:hover {
                border-color: #93c21c;
            }


        /* Mobile Responsive Logic */
        @media (max-width: 768px) {
            #fb-right-sidebar {
                transform: translateX(100%); /* Hidden by default on mobile */
                width: 80px; /* Slightly wider touch target on mobile */
                z-index: 9999; /* Top level on mobile */
                top: 60px; /* Adjust based on mobile header height */
                box-shadow: -2px 0 10px rgba(0,0,0,0.1);
            }

            /* Class to show sidebar via JS */
            #fb-right-sidebar.show-mobile {
                transform: translateX(0);
            }
        }

        .fb-contact-item {
            position: relative;
            width: 48px;
            height: 48px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .fb-contact-item:hover { transform: scale(1.05); }

        .fb-avatar {
            width: 30px; height: 30px; border-radius: 50%; object-fit: cover;  background: #eee;
            display: block; margin: 0 auto; /* Center image */
        }
        
        .fb-initials {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #74b2d4;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 10px;
            margin: 0 auto;
        }

        /* Online Status Dot */
        .fb-status-dot {
            position: absolute;
            bottom: 8px; /* Adjusted alignment */
            right: 8px;
            width: 10px;
            height: 10px;
            background: #ccc;
            border: 2px solid #fff;
            border-radius: 50%;
        }
        .fb-status-dot.online { background: #31a24c; }

        /* Unread Badge */
        .fb-avatar-badge {
            position: absolute; top: -5px; right: -5px; background: #e41e3f;
            color: #fff; font-size: 10px; padding: 1px 5px; border-radius: 10px;
            border: 1px solid #fff; font-weight: bold; z-index: 2;
        }

        /* Tooltip - Hide on mobile touch */
        .fb-contact-tooltip {
            position: absolute; right: 60px; top: 50%; transform: translateY(-50%);
            background: rgba(0,0,0,0.8); color: #fff; padding: 4px 8px;
            border-radius: 4px; font-size: 12px; white-space: nowrap;
            opacity: 0; pointer-events: none; transition: opacity 0.2s; z-index: 1050;
        }
        @media (min-width: 769px) {
            .fb-contact-item:hover .fb-contact-tooltip { opacity: 1; }
        }

        /* Bottom Chat Container */
        #fb-bottom-chats-container {
            position: fixed; bottom: 0; right: var(--fb-sidebar-width); left: 0;
            height: 0; z-index: 1050; display: flex; flex-direction: row-reverse;
            align-items: flex-end; padding-right: 15px; pointer-events: none;
        }

        /* Mobile Chat Window Adjustment */
        @media (max-width: 768px) {
            #fb-bottom-chats-container {
                right: 0; /* Full width on mobile */
                padding-right: 5px;
                padding-left: 5px;
            }
            .fb-chat-window {
                width: 100% !important; /* Full width chat on mobile */
                margin-right: 0 !important;
            }
        }

        /* Chat Window Styles */
        .fb-chat-window {
            width: var(--fb-chat-width); background: var(--fb-bg); border-radius: 8px 8px 0 0;
            box-shadow: 0 0 15px rgba(0,0,0,0.15); margin-right: 15px; display: flex;
            flex-direction: column; pointer-events: auto; border: 1px solid var(--fb-border);
            height: 400px; transition: transform 0.3s ease;
        }
        .fb-chat-window.minimized { height: var(--fb-header-height); overflow: hidden; transform: translateY(0); }
        .fb-chat-header { height: var(--fb-header-height); background: var(--fb-primary); color: #fff; padding: 0 10px; display: flex; align-items: center; justify-content: space-between; border-radius: 8px 8px 0 0; cursor: pointer; }
        .fb-chat-header.flash { animation: flash-header 1s infinite; }
        @keyframes flash-header { 0%, 100% { background-color: var(--fb-primary); } 50% { background-color: #e41e3f; } }
        .fb-chat-title { font-weight: 600; font-size: 13px; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .fb-chat-controls i { margin-left: 8px; cursor: pointer; opacity: 0.8; }
        .fb-chat-body { flex: 1; overflow-y: auto; padding: 10px; background: #f7f7f7; display: flex; flex-direction: column; gap: 8px; }
        .fb-chat-footer { padding: 8px; border-top: 1px solid #eee; background: #fff; display: flex; gap: 5px; align-items: center; }
        .fb-chat-input { flex: 1; border: 1px solid #ddd; border-radius: 15px; padding: 6px 12px; font-size: 13px; outline: none; }
        .fb-chat-btn { background: none; border: none; color: var(--fb-primary); cursor: pointer; padding: 5px; }

        /* Messages */
        .fb-msg { max-width: 75%; padding: 6px 10px; border-radius: 12px; font-size: 13px; position: relative; word-wrap: break-word; }
        .fb-msg.incoming { align-self: flex-start; background: var(--fb-incoming); color: #000; border-bottom-left-radius: 2px; }
        .fb-msg.outgoing { align-self: flex-end; background: var(--fb-outgoing); color: #333; border-bottom-right-radius: 2px; }
        .fb-msg-meta { font-size: 9px; color: #999; text-align: right; margin-top: 2px; }

        /* Dark Mode */
        body.dark-mode #fb-right-sidebar { background: #2c3e50; border-left: 1px solid #444; }
        body.dark-mode .fb-chat-window { background: #2c3e50; border-color: #444; }
        body.dark-mode .fb-chat-header { background: #1e1e1e; }
        body.dark-mode .fb-chat-body { background: #151515; }
        body.dark-mode .fb-chat-footer { background: #1e1e1e; border-top-color: #333; }
        body.dark-mode .fb-chat-input { background: #333; color: #fff; border-color: #555; }
        body.dark-mode .fb-msg.incoming { background: #333; color: #fff; }
        body.dark-mode .fb-msg.outgoing { background: #3b5066; color: #fff; }
    </style>

    <style>
        .fb-msg-avatar { display:none !important; }
        .fb-msg-row { gap:0 !important; } /* optional: remove leftover spacing */

        .sa-appointment-reminder-backdrop {
            position: fixed;
            inset: 0;
            z-index: 999999;
            background: rgba(15, 23, 42, .62);
            backdrop-filter: blur(7px);
            align-items: center;
            justify-content: center;
            padding: 22px;
        }

        .sa-appointment-reminder-modal {
            width: min(620px, 100%);
            background: #ffffff;
            border-radius: 26px;
            box-shadow: 0 30px 100px rgba(15, 23, 42, .35);
            overflow: hidden;
            animation: saAppointmentReminderIn .22s ease-out;
        }

        @keyframes saAppointmentReminderIn {
            from {
                opacity: 0;
                transform: translateY(18px) scale(.97);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .sa-appointment-reminder-head {
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            background: linear-gradient(135deg, #74b2d4, #93c21c);
            color: #ffffff;
        }

        .sa-appointment-reminder-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            background: rgba(255,255,255,.18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex: 0 0 auto;
        }

        .sa-appointment-reminder-head h4 {
            margin: 0;
            color: #ffffff;
            font-weight: 900;
            font-size: 20px;
        }

        .sa-appointment-reminder-head p {
            margin: 4px 0 0;
            color: rgba(255,255,255,.86);
            font-size: 13px;
            font-weight: 700;
        }

        .sa-appointment-reminder-close {
            margin-left: auto;
            width: 40px;
            height: 40px;
            border: 0;
            border-radius: 14px !important;
            background: rgba(255,255,255,.16);
            color: #ffffff;
            cursor: pointer;
        }

        .sa-appointment-reminder-close:hover {
            background: rgba(255,255,255,.25);
        }

        .sa-appointment-reminder-body {
            padding: 22px;
            background: #f8fafc;
        }

        .sa-appointment-reminder-alert {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #fed7aa;
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 14px;
        }

        .sa-appointment-reminder-body h3 {
            margin: 0 0 16px;
            color: #111827;
            font-size: 22px;
            font-weight: 900;
        }

        .sa-appointment-reminder-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .sa-appointment-reminder-box {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 14px;
        }

        .sa-appointment-reminder-box-full {
            grid-column: 1 / -1;
        }

        .sa-appointment-reminder-box label {
            display: block;
            margin: 0 0 6px;
            color: #6b7280;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .05em;
            font-weight: 900;
        }

        .sa-appointment-reminder-box div {
            color: #111827;
            font-size: 14px;
            font-weight: 800;
            line-height: 1.55;
            word-break: break-word;
        }

        .sa-appointment-reminder-foot {
            padding: 16px 20px;
            background: #ffffff;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .sa-appointment-reminder-foot .btn {
            font-weight: 900;
            border-radius: 13px !important;
            padding: 10px 15px;
        }

        @media(max-width: 640px) {
            .sa-appointment-reminder-grid {
                grid-template-columns: 1fr;
            }

            .sa-appointment-reminder-foot {
                flex-direction: column;
            }

            .sa-appointment-reminder-foot .btn {
                width: 100%;
            }
        }
    
    </style>

    <style>
    .sa-report-nav {
        position: relative;
        overflow: visible !important;
    }

    .sa-report-trigger {
        position: relative;
    }

    .sa-report-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        border-radius: 999px;
        background: #ef4444;
        color: #ffffff;
        font-size: 11px;
        font-weight: 900;
        line-height: 20px;
        text-align: center;
        box-shadow: 0 8px 18px rgba(239, 68, 68, .28);
        border: 2px solid #ffffff;
        z-index: 10;
    }

    .sa-report-dropdown {
        position: absolute;
        top: calc(100% + 14px);
        right: 0;
        width: 380px;
        max-width: calc(100vw - 30px);
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        box-shadow: 0 20px 60px rgba(15, 23, 42, .20);
        z-index: 16000;
        opacity: 0;
        visibility: hidden;
        transform: translateY(8px) scale(.98);
        transition: all .18s ease;
        overflow: hidden;
    }

    .sa-report-dropdown::before {
        content: "";
        position: absolute;
        top: -22px;
        left: 0;
        right: 0;
        height: 24px;
        background: transparent;
    }

    .sa-report-nav:hover .sa-report-dropdown,
    .sa-report-nav:focus-within .sa-report-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
    }

    .sa-report-drop-head {
        padding: 14px 16px;
        background: linear-gradient(135deg, #74b2d4, #93c21c);
        color: #ffffff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .sa-report-drop-head strong {
        display: block;
        font-size: 15px;
        font-weight: 900;
        color: #ffffff;
    }

    .sa-report-drop-head small {
        display: block;
        font-size: 11px;
        opacity: .85;
        color: #ffffff;
    }

    .sa-report-drop-head button {
        border: 1px solid rgba(255,255,255,.35);
        background: rgba(255,255,255,.14);
        color: #ffffff;
        border-radius: 10px;
        padding: 7px 9px;
        font-size: 11px;
        font-weight: 900;
        cursor: pointer;
    }

    .sa-report-drop-list {
        max-height: 390px;
        overflow-y: auto;
        padding: 10px;
        background: #f8fafc;
    }

    .sa-report-item {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 15px;
        padding: 12px;
        display: grid;
        grid-template-columns: 42px 1fr;
        gap: 11px;
        margin-bottom: 9px;
        cursor: pointer;
        transition: all .18s ease;
    }

    .sa-report-item:hover {
        border-color: #93c21c;
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, .08);
    }

    .sa-report-item.unread {
        background: #fbfff2;
        border-color: #dcefb0;
    }

    .sa-report-avatar {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        object-fit: cover;
        border: 1px solid #e5e7eb;
    }

    .sa-report-title {
        font-size: 13px;
        font-weight: 900;
        color: #111827;
        margin-bottom: 3px;
    }

    .sa-report-meta {
        font-size: 11px;
        color: #6b7280;
        font-weight: 700;
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        align-items: center;
    }

    .sa-report-text {
        font-size: 12px;
        color: #374151;
        line-height: 1.45;
        margin-top: 7px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .sa-report-empty {
        padding: 32px 16px;
        text-align: center;
        color: #6b7280;
        font-size: 13px;
        font-weight: 800;
    }

    .sa-report-drop-foot {
        padding: 10px 14px;
        background: #ffffff;
        border-top: 1px solid #e5e7eb;
        text-align: center;
    }

    .sa-report-drop-foot a {
        color: #74b2d4;
        font-size: 12px;
        font-weight: 900;
        text-decoration: none;
    }

    .sa-report-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 99999;
        background: rgba(15, 23, 42, .58);
        backdrop-filter: blur(5px);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 22px;
    }

    .sa-report-modal {
        width: min(720px, 100%);
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 24px 80px rgba(15, 23, 42, .28);
        overflow: hidden;
        animation: saReportModalIn .18s ease-out;
    }

    @keyframes saReportModalIn {
        from {
            opacity: 0;
            transform: translateY(14px) scale(.98);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .sa-report-modal-head {
        padding: 18px 20px;
        color: #ffffff;
        background: linear-gradient(135deg, #74b2d4, #93c21c);
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
    }

    .sa-report-modal-head h5 {
        margin: 0;
        color: #ffffff;
        font-weight: 900;
    }

    .sa-report-modal-head small {
        color: rgba(255,255,255,.82);
        font-weight: 700;
    }

    .sa-report-modal-close {
        width: 38px;
        height: 38px;
        border: 0;
        border-radius: 12px;
        background: rgba(255,255,255,.16);
        color: #ffffff;
        cursor: pointer;
    }

    .sa-report-modal-body {
        padding: 20px;
        background: #f8fafc;
    }

    .sa-report-modal-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 14px;
    }

    @media(max-width: 640px) {
        .sa-report-modal-grid {
            grid-template-columns: 1fr;
        }
    }

    .sa-report-modal-box {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 13px 14px;
    }

    .sa-report-modal-label {
        font-size: 11px;
        color: #6b7280;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 5px;
    }

    .sa-report-modal-value {
        font-size: 14px;
        color: #111827;
        font-weight: 800;
    }

    .sa-report-full-text {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 16px;
        color: #374151;
        font-size: 14px;
        line-height: 1.7;
        white-space: pre-wrap;
    }

    .sa-report-modal-foot {
        padding: 14px 20px;
        background: #ffffff;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
    }

    .sa-report-modal-foot button {
        background: #93c21c;
        color: #ffffff;
        border: 0;
        border-radius: 12px;
        padding: 10px 16px;
        font-weight: 900;
        cursor: pointer;
    }
</style>

<style>
    /* Default: Hide mobile toggle on desktop */
    .mobile-chat-toggle {
        display: none;
    }

    /* Mobile Styles */
    @media (max-width: 768px) {
        /* 1. Show the Floating Action Button */
        .mobile-chat-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            position: fixed;
            bottom: 20px; /* Adjust if it covers other elements */
            right: 20px;
            width: 50px;
            height: 50px;
            z-index: 1080; /* Above sidebar */
            font-size: 24px;
            padding: 0;
            border: 2px solid #fff;
        }

        /* 2. Adjust Sidebar Z-Index */
        #fb-right-sidebar {
            z-index: 1090 !important; /* Ensure it's on top of everything */
            width: 280px !important;
            padding-bottom: 80px; /* Space for bottom nav/footer */
        }

        /* 3. Fix Chat Window on Mobile */
        .fb-chat-window {
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            height: 80vh !important; /* Use 80% of viewport height */
            height: 80dvh !important; /* Dynamic viewport height for modern mobile browsers */
            margin-right: 0 !important;
            border-radius: 15px 15px 0 0;
            z-index: 2000 !important; /* Highest priority */
            display: flex !important;
            flex-direction: column;
        }

        /* Adjust chat body height to allow scrolling on mobile */
        .fb-chat-body {
            flex: 1;
            height: auto; 
            overflow-y: scroll;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Ensure input area isn't hidden by keyboard */
        .fb-chat-footer {
            padding-bottom: 20px; /* Add padding for iPhone home bar */
            background: #fff;
        }
    }
</style>

<style>
    /* --- MOBILE FIXES & CHAT STYLES --- */

    /* Default: Hide mobile-specific elements on Desktop */
    .mobile-chat-toggle, 
    .fb-sidebar-mobile-header, 
    .fb-contact-name {
        display: none !important;
    }

    /* === MOBILE VIEW ONLY (Max Width 768px) === */
    @media (max-width: 768px) {
        
        /* 1. HIDE DESKTOP HOVER STRIP */
        .hover-quick-strip {
            display: none !important;
        }

        /* 2. OVERLAY */
        #fb-mobile-overlay {
            z-index: 10000 !important; 
        }

        /* 3. SIDEBAR (The List) */
        #fb-right-sidebar {
            width: 280px !important; /* Wide enough for names */
            max-width: 85vw;
            background: #fff;
            padding-bottom: 80px; 
            z-index: 10001 !important; /* Above Overlay */
            box-shadow: 5px 0 15px rgba(0,0,0,0.2); 
            display: flex;
            flex-direction: column;
            align-items: stretch !important; /* Stretch items to full width */
        }

        /* 4. SHOW SIDEBAR HEADER (Close Button) */
        .fb-sidebar-mobile-header {
            display: flex !important;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
            margin-bottom: 10px;
            font-weight: bold;
            color: #333;
            background: #f8f9fa;
        }
        
        body.dark-mode .fb-sidebar-mobile-header {
            background: #2c3e50;
            color: #fff;
            border-bottom: 1px solid #444;
        }

        /* 5. CONTACT ITEMS (Layout for Name + Avatar) */
        .fb-contact-item {
            width: 100% !important; /* Full width */
            padding: 10px 15px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            height: auto !important; /* Auto height for text */
        }

        /* 6. SHOW NAMES */
        .fb-contact-name {
            display: block !important;
            margin-left: 12px;
            font-size: 14px;
            font-weight: 500;
            color: #333;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        body.dark-mode .fb-contact-name {
            color: #fff;
        }

        /* 7. Adjust Avatar/Initials positioning in the flex row */
        .fb-avatar, .fb-initials {
            margin: 0 !important; /* Remove auto margins */
            flex-shrink: 0;
        }

        /* 8. CHAT WINDOW */
        .fb-chat-window {
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            height: 85dvh !important; 
            margin-right: 0 !important;
            border-radius: 15px 15px 0 0;
            z-index: 10002 !important; 
            display: flex !important;
            flex-direction: column;
            border-top: 1px solid #ccc;
        }
        
        /* 9. Floating Toggle Button */
        .mobile-chat-toggle {
            display: flex !important;
            align-items: center;
            justify-content: center;
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            z-index: 1080;
            font-size: 24px;
            padding: 0;
            border: 2px solid #fff;
        }
    }
</style>
<style>
    /* -------------------------------------------------------
    * Chat message menu (3-dots) – robust positioning + z-index
    * ----------------------------------------------------- */

    /* Ensure chat windows create a stacking context */
    .fb-chat-window{
    position: relative;
    z-index: 1000;
    }

    /* Allow dropdowns to overflow outside bubbles */
    .fb-chat-body{
    overflow: auto;              /* keep your scroll */
    position: relative;
    }

    /* Each message bubble must be a positioning context */
    .fb-msg{
    position: relative;
    }

    /* Actions container (3 dots) */
    .fb-msg-actions{
    position:absolute;
    top:8px;
    right:10px;
    display:flex;
    gap:6px;
    opacity:0;
    pointer-events:none;
    transition: opacity .12s ease, transform .12s ease;
    transform: translateY(-1px);
    }

    /* Show on hover (desktop) */
    .fb-msg:hover .fb-msg-actions{
    opacity:1;
    pointer-events:auto;
    transform: translateY(0);
    }

    /* Always show on touch devices when bubble is tapped (JS can add .is-active if needed) */
    .fb-msg.is-active .fb-msg-actions{
    opacity:1;
    pointer-events:auto;
    transform: translateY(0);
    }

    /* 3-dot button */
    .fb-msg-action-btn{
    width:28px;
    height:28px;
    border-radius:999px;
    border:1px solid rgba(15,23,42,.10);
    background:#fff;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    box-shadow: 0 6px 18px rgba(15,23,42,.10);
    transition: transform .12s ease, box-shadow .12s ease, background .12s ease;
    }
    .fb-msg-action-btn:hover{
    background: rgba(15,23,42,.04);
    transform: translateY(-1px);
    box-shadow: 0 10px 24px rgba(15,23,42,.14);
    }
    .fb-msg-action-btn i{ font-size: 1.05rem; }

    /* Dropdown menu panel */
    .fb-msg-menu{
    position:absolute;
    top:40px;
    right:10px;

    min-width: 170px;
    padding: 6px;
    border-radius: 14px;

    background:#fff;
    border:1px solid rgba(15,23,42,.12);
    box-shadow: 0 18px 40px rgba(15,23,42,.18);

    z-index: 99999;              /* above header, body, etc */
    display:none;
    }

    /* Open state */
    .fb-msg-menu.is-open{ display:block; }

    /* Small arrow */
    .fb-msg-menu::before{
    content:"";
    position:absolute;
    top:-7px;
    right:16px;
    width:12px;
    height:12px;
    background:#fff;
    border-left:1px solid rgba(15,23,42,.12);
    border-top:1px solid rgba(15,23,42,.12);
    transform: rotate(45deg);
    }

    /* Menu items */
    .fb-msg-menu button{
    width:100%;
    border:0;
    background:transparent;
    text-align:left;
    cursor:pointer;

    padding: 10px 10px;
    border-radius: 10px;

    font-size: 13px;
    font-weight: 500;
    color: #0f172a;

    display:flex;
    align-items:center;
    gap:10px;
    }

    .fb-msg-menu button:hover{
    background: rgba(15,23,42,.06);
    }

    /* Danger action */
    .fb-msg-menu button.danger{
    color:#b91c1c;
    }
    .fb-msg-menu button.danger:hover{
    background: rgba(185,28,28,.08);
    }

    /* If your chat header overlaps, keep it below menus */
    .fb-chat-header{
    position: relative;
    z-index: 1000;
    }

    /* Safety: allow menus to render even if bubble has hidden overflow somewhere */
    .fb-msg,
    .fb-msg-row{
    overflow: visible !important;
    }

    /* Mobile: make hover irrelevant */
    @media (max-width: 768px){
    .fb-msg-actions{
        opacity:1;
        pointer-events:auto;
        transform:none;
    }
    .fb-msg-menu{
        right: 6px;
        min-width: 160px;
    }
    }
    </style>

    <style>
    .content-wrapper { margin-right: 0 !important; }

    /* desktop only */
    @media (min-width: 1024px) {
        .content-wrapper { margin-right: 39px !important; }
    }
    </style>

    <style>
    .nav-icon{
        width: 24px;
        height: 24px;
        display:inline-block;
        vertical-align:middle;

        stroke: currentColor;
        fill: none;
        stroke-width: 2.2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }
        .nav-link,
        .nav-link-search{
        display: inline-flex !important;
        align-items: center;
        list-style: none !important;
        }

    
    
</style>

<style>
    /* Custom Modern Multi-Select */
    .sa-ms-wrapper {
        position: relative;
        width: 100%;
        margin-bottom: 12px;
        font-family: Inter, system-ui, sans-serif;
    }
    
    .sa-ms-label {
        display: block;
        font-size: 11px;
        font-weight: 800;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-bottom: 6px;
    }

    .sa-ms-header {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
        min-height: 42px;
        padding: 6px 36px 6px 12px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        cursor: text;
        transition: all 0.2s ease;
        position: relative;
    }
    .sa-ms-header.active {
        border-color: #93c21c; /* var(--primary) */
        box-shadow: 0 0 0 3px #f4fae7; /* var(--primary-light) */
    }

    .sa-ms-caret {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        transition: transform 0.2s ease;
        pointer-events: none;
    }
    .sa-ms-header.active .sa-ms-caret {
        transform: translateY(-50%) rotate(180deg);
    }

    .sa-ms-search {
        border: none;
        outline: none;
        background: transparent;
        flex: 1 1 80px;
        min-width: 80px;
        font-size: 13px;
        color: #1f2937;
        padding: 4px 0;
    }
    .sa-ms-search::placeholder { color: #9ca3af; }

    /* Chips / Pills */
    .sa-ms-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f4fae7;
        color: #7baa18;
        border: 1px solid #cce891;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }
    .sa-ms-chip-close {
        cursor: pointer;
        color: #93c21c;
        font-size: 14px;
        line-height: 1;
        border-radius: 50%;
    }
    .sa-ms-chip-close:hover { color: #dc2626; }

    /* Dropdown Menu */
    .sa-ms-dropdown {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        box-shadow: 0 10px 25px -10px rgba(0,0,0,0.25);
        max-height: 250px;
        overflow-y: auto;
        z-index: 9999;
        display: none;
        padding: 6px;
    }
    .sa-ms-dropdown.show { display: block; }

    /* Custom Scrollbar for Dropdown */
    .sa-ms-dropdown::-webkit-scrollbar { width: 6px; }
    .sa-ms-dropdown::-webkit-scrollbar-track { background: #f9fafb; border-radius: 10px; }
    .sa-ms-dropdown::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
    .sa-ms-dropdown::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

    /* Options */
    .sa-ms-option {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.15s;
        margin-bottom: 2px;
    }
    .sa-ms-option:hover { background: #f3f4f6; }
    .sa-ms-option input[type="checkbox"] {
        cursor: pointer;
        width: 16px;
        height: 16px;
        accent-color: #93c21c;
    }
    .sa-ms-option-text {
        font-size: 13px;
        color: #374151;
        font-weight: 500;
        user-select: none;
    }
    .sa-ms-option.hidden { display: none; }

</style>

<style>
    /* --- OC Design System für Sidebar & Toasts --- */
    .oc-toast-wrap {
        position: fixed;
        right: 20px;
        bottom: 20px;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        gap: 10px;
        pointer-events: none;
    }
    .oc-toast {
        pointer-events: auto;
        min-width: 280px;
        max-width: 360px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        box-shadow: 0 10px 25px -10px rgba(0,0,0,0.25);
        padding: 12px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        animation: ocToastIn .3s cubic-bezier(.175,.885,.32,1.275) forwards;
        font-family: Inter, system-ui, sans-serif;
    }
    @keyframes ocToastIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    .oc-toast-ic {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
    }
    .oc-toast-ic.ok { background: #ecfdf5; color: #10b981; border: 1px solid #d1fae5; }
    .oc-toast-ic.bad { background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; }
    .oc-toast-ic.info { background: #eff6ff; color: #3b82f6; border: 1px solid #dbeafe; }
    
    .oc-toast-ttl { font-weight: 800; font-size: 13px; margin: 0; color: #111827; }
    .oc-toast-msg { font-size: 12px; color: #374151; margin: 4px 0 0 0; line-height: 1.4; }
    .oc-toast-x { margin-left: auto; background: transparent; border: none; cursor: pointer; color: #9ca3af; font-size: 18px; padding: 0; line-height: 1; }
    .oc-toast-x:hover { color: #111827; }

    /* Modern Cards inside the Sidebar */
    .oc-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: all 0.2s;
        font-family: Inter, system-ui, sans-serif;
    }
    .oc-card:hover { 
        border-color: #93c21c; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.06); 
    }
    .oc-badge {
        background: #f3f4f6;
        color: #4b5563;
        border: 1px solid #e5e7eb;
        padding: 4px 8px;
        border-radius: 8px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }
    .flash-new { animation: ocFlash 2.5s ease-out; }
    @keyframes ocFlash {
        0% { border-color: #10b981; box-shadow: 0 0 0 4px #ecfdf5; background: #f0fdf4; }
        100% { border-color: #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.04); background: #fff; }
    }
</style>

<style>
  :root{
    --app-bg:#f3f4f6;
    --card-bg:#ffffff;
    --text-main:#1f2937;
    --text-muted:#6b7280;
    --border:#e5e7eb;

    --primary:#93c21c;
    --primary-hover:#7baa18;
    --primary-light:#f4fae7;

    --blue:#74b2d4;
    --blue-light:#eff6ff;

    --success:#10b981;
    --success-light:#ecfdf5;

    --warning:#f59e0b;
    --warning-light:#fffbeb;

    --danger:#ef4444;
    --danger-hover:#dc2626;
    --danger-light:#fef2f2;

    --gray:#6b7280;
    --gray-light:#f3f4f6;

    --shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);
    --shadow:0 10px 25px -10px rgb(0 0 0 / .18), 0 4px 10px -4px rgb(0 0 0 / .10);

    --radius:14px;
    --radius-lg:16px;
    --radius-xl:18px;
    --transition:all .2s ease-in-out;
  }

  html, body{
    background:var(--app-bg) !important;
    color:var(--text-main);
  }

  body.dark-mode{
    background:var(--app-bg) !important;
  }

  *{
    scrollbar-width:thin;
    scrollbar-color:#d1d5db transparent;
  }

  *::-webkit-scrollbar{
    width:8px;
    height:8px;
  }

  *::-webkit-scrollbar-thumb{
    background:#d1d5db;
    border-radius:999px;
  }

  *::-webkit-scrollbar-track{
    background:transparent;
  }

  /* -------------------------
     GLOBAL CONTENT AREA
  ------------------------- */
  .content,
  .content-wrapper,
  .content-body,
  .app-content{
    background:transparent !important;
  }

  .content-header{
    margin-top:115px;
  }

  .content-wrapper{
    padding-bottom:1rem;
  }

  .header-navbar-shadow{
    background:transparent !important;
  }

  /* -------------------------
     TOP NAVBAR
  ------------------------- */
  .header-navbar.floating-nav{
    background:rgba(255,255,255,.94) !important;
    backdrop-filter:blur(10px);
    border:1px solid rgba(229,231,235,.95);
    box-shadow:var(--shadow-sm);
    border-radius:16px;
  }

  .header-navbar .navbar-container{
    background:transparent !important;
  }

  .header-navbar .nav-link,
  .header-navbar .menu,
  .header-navbar .dashboard-title,
  .header-navbar .dashboard-icon,
  .header-navbar .feather,
  .header-navbar .fa{
    transition:var(--transition);
  }

  .header-navbar .nav-link{
    color:var(--text-main) !important;
    border-radius:10px !important;
  }

  .header-navbar .nav-link:hover{
    background:#f9fafb;
  }

  .dashboard-icon{
    color:var(--blue) !important;
  }

  .menu.active_menu .dashboard-icon,
  .submenu a.active-link .dashboard-icon,
  .submenu a.active-link .feather{
    color:var(--primary) !important;
  }

  .horizontal_menu_item .menu{
    border-radius:12px;
    padding:8px 12px;
  }

  .horizontal_menu_item .menu:hover{
    background:#f9fafb;
  }

  .horizontal_menu_item .menu.active_menu{
    background:#ffffff00; 
  }

  .submenu{
    background:#fff;
    border:1px solid var(--border);
    border-radius:14px;
    box-shadow:var(--shadow);
    padding:8px;
  }

  .submenu ul{
    margin:0;
    padding:0;
    list-style:none;
  }

  .submenu li a{
    display:flex;
    align-items:center;
    gap:8px;
    padding:10px 12px;
    border-radius:10px;
    color:var(--text-main) !important;
    font-weight:600;
    transition:var(--transition);
  }

  .submenu li a:hover{
    background:#f9fafb;
    color:var(--primary) !important;
    text-decoration:none;
  }

  .submenu li a.danger:hover{
    background:var(--danger-light);
    color:var(--danger) !important;
  }

  /* -------------------------
     LEFT SIDEBAR
  ------------------------- */
  .sidebar{
    background:#fff !important;
    border-right:1px solid var(--border);
    box-shadow:var(--shadow-sm);
  }

  .sidebar-logo{
    border-bottom:1px solid var(--border);
    background:#fff;
  }

  .sidebar-header{
    border-bottom:1px solid var(--border);
    background:#fff;
  }

  .sidebar-header a{
    color:var(--text-main) !important;
    font-weight:800;
  }

  .sidebar-content{
    background:#fff;
  }

  .sidebar-content ul{
    margin:0;
    padding:0;
    list-style:none;
  }

  .sidebar-content > ul > li > a,
  .sidebar-content .nav-has-submenu > a{
    display:flex;
    align-items:center;
    gap:10px;
    padding:11px 14px;
    margin:3px 8px;
    border-radius:12px;
    color:var(--text-main) !important;
    font-weight:700;
    transition:var(--transition);
    text-decoration:none !important;
  }

  .sidebar-content > ul > li > a:hover,
  .sidebar-content .nav-has-submenu > a:hover{
    background:#f9fafb;
    color:var(--primary) !important;
  }

  .sidebar-content .nav-submenu{
    background:transparent;
    padding:2px 0 6px 0;
  }

  .sidebar-content .nav-submenu li a{
    display:flex;
    align-items:center;
    gap:8px;
    padding:9px 14px 9px 18px;
    margin:2px 8px 2px 16px;
    border-radius:10px;
    font-size:13px;
    color:var(--text-muted) !important;
    font-weight:600;
    transition:var(--transition);
    text-decoration:none !important;
  }

  .sidebar-content .nav-submenu li a:hover,
  .sidebar-content .nav-submenu li a.active,
  .sidebar-content .nav-submenu li a.active-link{
    background:var(--primary-light);
    color:var(--primary) !important;
  }

  .sidebar-content .nav-has-submenu.active > a{
    background:#f9fafb;
    color:var(--primary) !important;
  }

  .sidebar-content .feather,
  .sidebar-content .fa{
    color:inherit !important;
  }

  .color-content{
    border-top:1px solid var(--border);
    background:#fff;
  }

  /* -------------------------
     PROFILE DROPDOWN
  ------------------------- */
  .sa-menu{
    background:#fff !important;
    color:var(--text-main) !important;
    border:1px solid var(--border) !important;
    border-radius:14px !important;
    box-shadow:var(--shadow) !important;
  }

  .sa-item{
    color:var(--text-main) !important;
    border-radius:10px !important;
    font-weight:600;
  }

  .sa-item:hover,
  .sa-item:focus{
    background:#f9fafb !important;
    color:var(--primary) !important;
  }

  .sa-divider{
    background:var(--border) !important;
  }

  /* -------------------------
     QUICK SIDER + NOTIFICATION SIDERS
  ------------------------- */
  .qs-sider,
  .notif-sider,
  .activity-sider{
    background:#fff !important;
    color:var(--text-main) !important;
    border-left:1px solid var(--border);
    box-shadow:var(--shadow);
  }

  .qs-header,
  .notif-header,
  .activity-header{
    background:#fff !important;
    color:var(--text-main) !important;
    border-bottom:1px solid var(--border) !important;
  }

  .activity-header{
    background:linear-gradient(135deg,var(--primary),var(--blue)) !important;
    color:#fff !important;
  }

  .activity-header *{
    color:#fff !important;
  }

  .qs-content,
  .notif-list{
    background:#fff !important;
  }

  .qs-tile{
    background:#fff !important;
    border:1px solid var(--border) !important;
    color:var(--text-main) !important;
    border-radius:14px !important;
    box-shadow:var(--shadow-sm);
  }

  .qs-tile:hover{
    background:#f9fafb !important;
    border-color:var(--primary-light) !important;
    box-shadow:var(--shadow);
    transform:translateY(-2px);
  }

  .qs-tile i,
  .qs-tile span{
    color:var(--text-main) !important;
  }

  .qs-sub-item{
    background:#fff !important;
    border:1px solid var(--border) !important;
    color:var(--text-main) !important;
    border-radius:10px !important;
  }

  .qs-sub-item:hover{
    background:#f9fafb !important;
    color:var(--primary) !important;
  }

  .notif-card,
  .activity-card,
  .oc-card{
    background:#fff !important;
    border:1px solid var(--border) !important;
    border-radius:14px !important;
    box-shadow:var(--shadow-sm);
  }

  .notif-card.unread{
    background:#f8fbff !important;
    border-color:#dbeafe !important;
  }

  .notif-icon,
  .activity-icon-wrapper{
    background:var(--blue-light) !important;
    color:var(--blue) !important;
    border-radius:12px !important;
  }

  .chip{
    border:1px solid var(--border) !important;
    background:#f9fafb !important;
    color:var(--text-main) !important;
    border-radius:999px !important;
    font-weight:700;
  }

  .chip.active{
    background:var(--primary-light) !important;
    border-color:#dcefb0 !important;
    color:var(--primary) !important;
  }

  /* -------------------------
     SEARCH OVERLAY
  ------------------------- */
  .search-input-overlay{
    background:rgba(243,244,246,.96) !important;
    backdrop-filter:blur(6px);
  }

  .search-box-wrapper{
    background:#fff !important;
    color:var(--text-main) !important;
    border:1px solid var(--border);
    border-radius:18px;
    box-shadow:var(--shadow);
  }

  .search-hint{
    background:#f9fafb !important;
    color:var(--text-main) !important;
    border:1px solid var(--border) !important;
  }

  .search-results li{
    border-bottom:1px solid var(--border) !important;
    transition:var(--transition);
  }

  .search-results li:hover{
    background:var(--primary-light) !important;
    color:var(--text-main) !important;
  }

  .search-results li.bg-primary{
    background:var(--primary) !important;
  }

  .search-results li.bg-primary *{
    color:#fff !important;
  }

  /* -------------------------
     CHAT
  ------------------------- */
  #fb-right-sidebar{
    background:#fff !important;
    border-left:1px solid var(--border) !important;
    box-shadow:var(--shadow);
  }

  .fb-chat-window{
    background:#fff !important;
    border:1px solid var(--border) !important;
    box-shadow:var(--shadow);
    border-radius:16px 16px 0 0 !important;
  }

  .fb-chat-header{
    background:linear-gradient(135deg,var(--primary),var(--blue)) !important;
    color:#fff !important;
    border-radius:16px 16px 0 0 !important;
  }

  .fb-chat-body{
    background:#f9fafb !important;
  }

  .fb-chat-footer{
    background:#fff !important;
    border-top:1px solid var(--border) !important;
  }

  .fb-chat-input{
    border:1px solid var(--border) !important;
    border-radius:999px !important;
    background:#fff !important;
    color:var(--text-main) !important;
  }

  .fb-chat-input:focus{
    border-color:var(--primary) !important;
    box-shadow:0 0 0 3px var(--primary-light) !important;
  }

  .fb-msg.incoming{
    background:#f3f4f6 !important;
    color:var(--text-main) !important;
  }

  .fb-msg.outgoing{
    background:var(--blue-light) !important;
    color:var(--text-main) !important;
  }

  .fb-msg-menu{
    border:1px solid var(--border) !important;
    border-radius:12px !important;
    box-shadow:var(--shadow) !important;
  }

  /* -------------------------
     BUTTONS / INPUTS / FORMS
  ------------------------- */
  .btn,
  button{
    border-radius:10px !important;
    transition:var(--transition);
  }

  .btn-primary,
  .oc-btn{
    background:var(--primary) !important;
    border-color:var(--primary) !important;
    color:#fff !important;
    font-weight:800;
  }

  .btn-primary:hover,
  .oc-btn:hover{
    background:var(--primary-hover) !important;
    border-color:var(--primary-hover) !important;
  }

  .btn-outline-light,
  .btn-light,
  .btn-outline-secondary,
  .oc-btn-soft{
    background:#fff !important;
    border:1px solid var(--border) !important;
    color:var(--text-main) !important;
  }

  .btn-outline-light:hover,
  .btn-light:hover,
  .btn-outline-secondary:hover,
  .oc-btn-soft:hover{
    background:#f9fafb !important;
    color:var(--text-main) !important;
  }

  input,
  select,
  textarea,
  .form-control,
  .select2-selection,
  .oc-input,
  .oc-input-form,
  .oc-select{
    border-radius:10px !important;
    border:1px solid var(--border) !important;
    box-shadow:none !important;
  }

  input:focus,
  select:focus,
  textarea:focus,
  .form-control:focus,
  .oc-input:focus,
  .oc-input-form:focus,
  .oc-select:focus{
    border-color:var(--primary) !important;
    box-shadow:0 0 0 3px var(--primary-light) !important;
  }

  /* -------------------------
     CARDS / TABLES / MODALS
  ------------------------- */
  .card,
  .modal-content,
  .appointments-list-container,
  .report-modal-dialog,
  .customer-modal-dialog{
    background:#fff !important;
    border:1px solid var(--border) !important;
    border-radius:16px !important;
    box-shadow:var(--shadow);
  }

  .card-header,
  .modal-header{
    background:#fff !important;
    border-bottom:1px solid var(--border) !important;
  }

  .table,
  table{
    color:var(--text-main);
  }

  .table thead th,
  table thead th{
    background:#f9fafb;
    color:var(--text-muted);
    border-color:var(--border) !important;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.04em;
    font-size:12px;
  }

  .table td,
  .table th,
  table td,
  table th{
    border-color:var(--border) !important;
  }

  /* -------------------------
     BADGES
  ------------------------- */
  .badge-danger{
    background:var(--danger) !important;
  }

  .badge-warning{
    background:var(--warning) !important;
    color:#111827 !important;
  }

  .badge-success{
    background:var(--success) !important;
  }

  .badge-primary{
    background:var(--primary) !important;
  }

  .badge-info{
    background:var(--blue) !important;
  }

  /* -------------------------
     SMALL FIXES
  ------------------------- */
  .main-menu.menu-light .navigation > li.active > a{
    background:#fff !important;
    color:var(--primary) !important;
    font-weight:700 !important;
    border-radius:10px !important;
    box-shadow:none !important;
  }

  .active-link{
    color:var(--primary) !important;
  }

  .scroll-top{
    right:16px !important;
    left:auto !important;
    border-radius:999px !important;
  }

  .footer.footer-light{
    background:transparent !important;
  }

  .footer .copyright{
    background:var(--primary) !important;
    border-color:var(--primary) !important;
  }

  /* -------------------------
     MOBILE
  ------------------------- */
  @media (max-width: 768px){
    .header-navbar.floating-nav{
      margin:1rem .75rem 0 !important;
      width:calc(100% - 1.5rem) !important;
    }

    .content-header{
      margin-top:95px;
    }

    .qs-sider,
    .notif-sider,
    .activity-sider{
      border-radius:18px 0 0 18px !important;
    }
  }
</style>
<style>
    .activity-card-item {
        cursor: pointer;
    }

    .activity-card-item:hover {
        border-color: #93c21c !important;
    }

    #activityDetailPanel {
        max-height: 40vh;
        overflow: auto;
    }

    .fb-day-sep,
    .search-group-header {
        text-align: center;
        font-size: 11px;
        color: #6b7280;
        margin: 10px 0;
        font-weight: 700;
    }
</style>

<style>
    .activity-modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 99999;
    background: rgba(15, 23, 42, .55);
    backdrop-filter: blur(5px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 22px;
}

.activity-detail-modal {
    width: min(720px, 100%);
    max-height: 88vh;
    background: #ffffff;
    border-radius: 24px;
    box-shadow: 0 24px 80px rgba(15, 23, 42, .28);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    animation: activityModalIn .18s ease-out;
}

@keyframes activityModalIn {
    from {
        transform: translateY(16px) scale(.98);
        opacity: 0;
    }
    to {
        transform: translateY(0) scale(1);
        opacity: 1;
    }
}

.activity-detail-modal-header {
    padding: 18px 20px;
    color: #ffffff;
    background: linear-gradient(135deg, #74b2d4, #74b2d4);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.activity-detail-modal-header h5 {
    color: #ffffff;
    font-weight: 900;
}

.activity-detail-modal-header small {
    color: rgba(255,255,255,.75);
}

.activity-modal-icon {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    background: rgba(255,255,255,.14);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 18px;
}

.activity-modal-close {
    width: 38px;
    height: 38px;
    border: 0;
    border-radius: 12px;
    background: rgba(255,255,255,.14);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.activity-modal-close:hover {
    background: rgba(255,255,255,.22);
}

.activity-detail-modal-body {
    padding: 20px;
    overflow-y: auto;
    background: #f8fafc;
}

.activity-detail-modal-footer {
    padding: 14px 20px;
    background: #ffffff;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
}

.activity-detail-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
}

@media (max-width: 640px) {
    .activity-detail-grid {
        grid-template-columns: 1fr;
    }
}

.activity-detail-box {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 13px 14px;
}

.activity-detail-label {
    font-size: 11px;
    color: #6b7280;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: 5px;
}

.activity-detail-value {
    font-size: 14px;
    color: #111827;
    font-weight: 800;
    word-break: break-word;
}

.activity-detail-message {
    margin-top: 14px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    padding: 16px;
}

.activity-change-list {
    margin-top: 14px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    overflow: hidden;
}

.activity-change-head {
    padding: 13px 16px;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    font-weight: 900;
    color: #111827;
}

.activity-change-row {
    display: grid;
    grid-template-columns: 160px 1fr 1fr;
    gap: 10px;
    padding: 12px 16px;
    border-bottom: 1px solid #f3f4f6;
    align-items: center;
}

.activity-change-row:last-child {
    border-bottom: 0;
}

.activity-change-field {
    font-weight: 900;
    color: #374151;
}

.activity-change-pill {
    display: inline-flex;
    align-items: center;
    min-height: 30px;
    padding: 6px 10px;
    border-radius: 999px;
    background: #f3f4f6;
    color: #374151;
    font-size: 12px;
    font-weight: 800;
    word-break: break-word;
}

.activity-change-pill.to {
    background: #ecfdf5;
    color: #93c21c;
}

.activity-change-pill.from {
    background: #fff7ed;
    color: #c2410c;
}

@media (max-width: 640px) {
    .activity-change-row {
        grid-template-columns: 1fr;
    }
}
</style>

<style>
        /* Center navbar (desktop) */
    @media (min-width: 992px) {
        #navbar-mobile.navbar-collapse {
            display: flex !important;
            align-items: center;
            width: 100%;
            overflow: visible !important;
        }

        .bookmark-wrapper {
            width: 100%;
            min-width: 0;
            display: flex !important;
            align-items: center;
            overflow: visible !important;
        }

        .nav-left {
            flex: 0 0 auto;
            min-width: 42px;
            z-index: 20;
        }

        .nav-center {
            flex: 1 1 auto;
            min-width: 0;
            display: flex;
            justify-content: center;
            overflow-x: auto;
            overflow-y: visible;
            scrollbar-width: none;
            padding: 0 10px;
        }

        .nav-center::-webkit-scrollbar {
            display: none;
        }

        #container_new {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-wrap: nowrap !important;
            gap: .25rem;
            white-space: nowrap;
            min-width: max-content;
        }

        .nav-right {
            flex: 0 0 auto !important;
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            margin-left: 10px !important;
            min-width: max-content;
            white-space: nowrap;
            overflow: visible !important;
            position: relative;
            z-index: 50;
        }

        .nav-right > li {
            flex: 0 0 auto !important;
            display: flex !important;
            align-items: center !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .nav-right .nav-link {
            width: 38px;
            height: 38px;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .nav-right .quick_sider_wrapper .nav-link {
            width: auto;
            height: 38px;
        }
    }

    /* Quick strip: show name beside icon */
    .hover-quick-strip .hqs-link{
        display:flex;
        align-items:center;
        gap:.55rem;
        white-space:nowrap;
    }
    .hover-quick-strip .hqs-text{
        font-size:12px;
        font-weight:700;
        line-height:1;
        color:#3a3a3a;
    }


    @media (max-width: 991px) {
        .nav-right {
            display: flex !important;
            align-items: center !important;
            margin-left: auto !important;
            gap: 4px;
            position: relative;
            z-index: 100;
        }

        .nav-right > li.sa-navbar-action,
        .nav-right > li.dropdown-notification {
            display: flex !important;
        }

        .nav-right .quick_sider_wrapper span.font-small-3 {
            display: none !important;
        }

        .nav-right .nav-link {
            width: 38px;
            height: 38px;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .nav-center {
            display: none !important;
        }
    }
</style>

<style>
   /* REPORT DROPDOWN FIX */
.header-navbar,
.navbar-wrapper,
.navbar-container,
.navbar-collapse,
.bookmark-wrapper,
.nav-right,
.nav-center,
#container_new {
    overflow: visible !important;
}

.nav-center {
    position: relative;
    z-index: 1000;
}

.sa-report-nav {
    position: relative !important;
    overflow: visible !important;
    z-index: 20000 !important;
}

.sa-report-dropdown {
    position: absolute !important;
    top: calc(100% + 14px) !important;
    right: 0 !important;
    z-index: 30000 !important;
    display: block;
    pointer-events: none;
}

.sa-report-nav:hover .sa-report-dropdown,
.sa-report-nav:focus-within .sa-report-dropdown,
.sa-report-nav.is-open .sa-report-dropdown {
    opacity: 1 !important;
    visibility: visible !important;
    transform: translateY(0) scale(1) !important;
    pointer-events: auto !important;
} 
</style>

@vite(['resources/js/bootstrap.js', 'resources/js/notification.js', 'resources/js/chat.js'])
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body 

class="vertical-layout vertical-menu-modern 2-columns dark-mode navbar-floating footer-static  menu-collapsed pace-done" data-open="click" data-menu="vertical-menu-modern" id="verticalLayout" data-col="content-left-sidebar">

            <!-- BEGIN: Header-->
            <nav class="header-navbar navbar-expand-lg navbar navbar-with-menu floating-nav" style="background:#f1f1f1;">
                <div class="navbar-wrapper">
                    <div class="navbar-container content">
                    <div class="navbar-collapse" id="navbar-mobile">

                        {{-- FULL WIDTH ROW: LEFT (menu btn) | CENTER (main nav) | RIGHT (quick menu) --}}
                        <div class="bookmark-wrapper d-flex align-items-center w-100">

                        {{-- LEFT --}}
                        <div class="nav-left d-flex align-items-center">
                            <button id="menu-button" type="button" class="btn p-0">
                            <i class="feather icon-menu"></i>
                            </button>
                        </div>

                        {{-- CENTER --}}
                        <div class="nav-center">
                            <ul class="nav navbar-nav" id="container_new">

                            {{-- ANFRAGE --}}
                            <li class="horizontal_menu_item nav-item" id="menu1">
                                <a href="javascript:void(0);" aria-haspopup="true" aria-expanded="false"
                                class="menu {{ Str::contains(Route::currentRouteName(), 'quiry') ? 'active_menu' : '' }}">
                                <div class="menu-items">
                                    <i class="feather icon-inbox dashboard-icon"></i>
                                    <h6 class="dashboard-title">ANFRAGE</h6>
                                </div>
                                </a>

                                <div class="submenu {{ Str::contains(Route::currentRouteName(), 'quiry') ? 'show' : '' }}">
                                <ul>
                                    <li>
                                    <a href="{{ route('inquiry.create') }}"
                                        @if(Route::currentRouteName() == 'inquiry.create') style="color:#94c11b!important" @endif>
                                        <i class="feather icon-plus-circle"></i> ANLEGEN
                                    </a>
                                    </li>

                                  @php
                                        // Current employee id (you store it in user->name)
                                        $employeeId = (string) auth()->user()->name;

                                        // (1) UNVERIFIED INQUIRIES: not deleted, not published, not junk, and missing verify fields
                                        $unverifiedBase = DB::table('inquiries')
                                            ->whereNull('deleted_at')
                                            ->whereNotIn('status', ['Published', 'Junk'])
                                            ->where(function ($q) {
                                                $q->whereNull('verify_by')
                                                    ->orWhereNull('verify_date');
                                            });

                                        // (2) Kunde (lead inquiries)
                                        $anfrage_lead_count = (clone $unverifiedBase)
                                            ->where('pre_type', 'Kunde')
                                            ->count();

                                        // (3) Sonstige Anfrage (not Kunde)
                                        $anfrage_count = (clone $unverifiedBase)
                                            ->where(function ($q) {
                                                $q->whereNull('pre_type')
                                                    ->orWhere('pre_type', '!=', 'Kunde');
                                            })
                                            ->count();

                                        // (4) Junk
                                        $junk_anfrage = DB::table('inquiries')
                                            ->whereNull('deleted_at')
                                            ->where('status', 'Junk')
                                            ->count();

                                        // (5) Deleted
                                        $delete_anfrage = DB::table('inquiries')
                                            ->whereNotNull('deleted_at')
                                            ->count();

                                        // (6) My inquiries (not deleted, not junk, not published, not Kunde)
                                        $my_inquiries = $employeeId !== ''
                                            ? DB::table('inquiries')
                                                ->whereNull('deleted_at')
                                                ->where('contact_person', $employeeId)
                                                ->whereNotIn('status', ['Junk', 'Published'])
                                                ->where(function ($q) {
                                                    $q->whereNull('pre_type')
                                                        ->orWhere('pre_type', '!=', 'Kunde');
                                                })
                                                ->count()
                                            : 0;


                                        $verified_total = $summary['total'] ?? 0;
                                @endphp


                                    <li>
                                    <a href="{{ route('my.inquiry.view') }}"
                                        @if(Route::currentRouteName() == 'my.inquiry.view') style="color:#94c11b!important" @endif>
                                        <i class="feather icon-list"></i> MEINE
                                        <span style="margin:0 5px">|</span>
                                        <span style="color:#64b0e5">{{ $my_inquiries }}</span>
                                    </a>
                                    </li>

                                    <li>
                                    <a href="{{ route('inquiry.customer') }}"
                                        @if(Route::currentRouteName() == 'inquiry.customer') style="color:#94c11b!important" @endif>
                                        <i class="feather icon-users"></i> KUNDE ANFRAGE
                                        <span style="margin:0 5px">|</span>
                                        <span style="color:#64b0e5">{{ $anfrage_lead_count }}</span>
                                    </a>
                                    </li>

                                    <li>
                                    <a href="{{ route('inquiry.view') }}"
                                        @if(Route::currentRouteName() == 'inquiry.view') style="color:#94c11b!important" @endif>
                                        <i class="feather icon-list"></i> SONSTIGE ANFRAGE
                                        <span style="margin:0 5px">|</span>
                                        <span style="color:#64b0e5">{{ $anfrage_count }}</span>
                                    </a>
                                    </li>

                                    <li>
                                    <a href="{{ route('inquiry.published.list') }}"
                                        @if(Route::currentRouteName() == 'inquiry.published.list') style="color:#94c11b!important" @endif>
                                        <i class="feather icon-list"></i> VERIFIZIERTE LISTE
                                        <span style="margin:0 5px">|</span>
                                        <span style="color:#64b0e5">{{ $verified_total}}</span>
                                    </a>
                                    </li>

                                    <li>
                                    <a href="{{ url('inquiry_junklist') }}"
                                        @if(Route::currentRouteName() == 'inquiry.junk.list') style="color:#94c11b!important" @endif>
                                        <i class="feather icon-slash"></i> JUNK
                                        <span style="margin:0 5px">|</span>
                                        <span style="color:#64b0e5">{{ $junk_anfrage }}</span>
                                    </a>
                                    </li>

                                    <li>
                                    <a href="{{ url('inquiry_deleted_list') }}"
                                        @if(Route::currentRouteName() == 'inquiry.deleted.list') style="color:#94c11b!important" @endif
                                        class="danger">
                                        <i class="feather icon-trash"></i> GELÖSCHTE
                                        <span style="margin:0 5px">|</span>
                                        <span style="color:#64b0e5">{{ $delete_anfrage }}</span>
                                    </a>
                                    </li>
                                </ul>
                                </div>
                            </li>

                            {{-- KUNDE (LEADS) --}}
                            <li class="horizontal_menu_item nav-item" id="menu1">
                                <a href="javascript:void(0);" aria-haspopup="true" aria-expanded="false"
                                class="menu
                                    @php
                                            $previousUrl = url()->previous();
                                            $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
                                    @endphp
                                    @if(Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'lead'))
                                    active_menu
                                    @elseif(Str::contains(Route::currentRouteName(), 'lead') && Str::contains($previousRoute, 'lead'))
                                    active_menu
                                    @endif
                                ">
                                <div class="menu-items">
                                    <i class="feather icon-user dashboard-icon"></i>
                                    <h6 class="dashboard-title">KUNDEN</h6>
                                </div>
                                </a>

                                <div class="submenu
                                @php
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
$now = Carbon\Carbon::now();
$last48h = $now->subHours(48);
$lead_count = DB::table('new_leads')->whereNull('deleted_at')->count();
$lead_new = DB::table('new_leads')->whereNull('deleted_at')->where('created_at', '>=', $last48h)->count();
$junk_lead = DB::table('new_leads')->where('status', 'Junk')->whereNull('deleted_at')->count();
$delete_lead = DB::table('new_leads')->whereNotNull('deleted_at')->count();
$my_leads = DB::table('new_leads')->whereNull('deleted_at')->where('status', 'Lead')->where('contact_person', auth()->user()->name)->count();
$waiting_loops = DB::table('new_leads as nl')
    ->leftJoin('lead_product_lists as lpl', 'nl.id', '=', 'lpl.customer_id')
    ->whereNull('nl.deleted_at')->whereNull('lpl.employee_id')
    ->where('nl.status', 'Lead')->distinct('nl.id')->count();
                                    @endphp

                                    <li>
                                    <a href="{{ url('new_lead_create') }}" @if(Route::currentRouteName() == 'new.lead.create') style="color:#94c11b!important" @endif>
                                        <i class="feather icon-plus-circle"></i> ANLEGEN
                                    </a>
                                    </li>

                                    @php
$previousUrl = url()->previous();
try {
    $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
} catch (\Exception $e) {
    $previousRoute = null;
}
                                    @endphp

                                    <li>
                                    @php
$isActive = (Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'waiting.loop.leads'))
    || Str::contains(Route::currentRouteName(), 'waiting.loop.leads');
                                    @endphp
                                    <a href="{{ route('waiting.loop.leads') }}" class="{{ $isActive ? 'active-link' : '' }}" style="{{ $isActive ? 'color:#94c11b!important' : '' }}">
                                        @if($isActive)<i class="feather icon-arrow-right warning"></i>@endif
                                        <i class="feather icon-refresh-ccw"></i> WARTESCHLEIFE
                                        <span style="margin:0 5px">|</span><span style="color:#64b0e5">{{ $waiting_loops }}</span>
                                    </a>
                                    </li>

                                    <li>
                                    @php
$isActive = (Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'new.lead.view'))
    || Str::contains(Route::currentRouteName(), 'new.lead.view');
                                    @endphp
                                    <a href="{{ url('new_lead_view') }}" class="{{ $isActive ? 'active-link' : '' }}" style="{{ $isActive ? 'color:#94c11b!important' : '' }}">
                                        @if($isActive)<i class="feather icon-arrow-right warning"></i>@endif
                                        <i class="feather icon-list"></i> LISTE
                                        <span style="margin:0 5px">|</span><span style="color:#64b0e5">{{ $lead_count }}</span>
                                    </a>
                                    </li>

                                    <li>
                                    @php
$isActive = (Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'lead.junks'))
    || Str::contains(Route::currentRouteName(), 'lead.junks');
                                    @endphp
                                    <a href="{{ url('lead_junks') }}" class="{{ $isActive ? 'active-link' : '' }}" style="{{ $isActive ? 'color:#94c11b!important' : '' }}">
                                        @if($isActive)<i class="feather icon-arrow-right warning"></i>@endif
                                        <i class="feather icon-slash"></i> JUNK
                                        <span style="margin:0 5px">|</span><span style="color:#64b0e5">{{ $junk_lead }}</span>
                                    </a>
                                    </li>

                                    <li>
                                    @php
$isActive = (Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'deleted.leads'))
    || Str::contains(Route::currentRouteName(), 'deleted.leads');
                                    @endphp
                                    <a href="{{ url('deleted_leads') }}" class="{{ $isActive ? 'active-link' : '' }}" style="{{ $isActive ? 'color:#94c11b!important' : '' }}">
                                        @if($isActive)<i class="feather icon-arrow-right warning"></i>@endif
                                        <i class="feather icon-trash-2"></i> GELÖSCHTE
                                        <span style="margin:0 5px">|</span><span style="color:#64b0e5">{{ $delete_lead }}</span>
                                    </a>
                                    </li>
                                </ul>
                                </div>
                            </li>

                            {{-- ANGEBOTE --}}
                            <li class="horizontal_menu_item nav-item" id="menu4">
                                <a href="javascript:void(0);" aria-haspopup="true" aria-expanded="false"
                                class="menu
                                    @php $previousUrl = url()->previous();
$previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName(); @endphp
                                    @if(Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'offer')) active_menu
                                    @elseif(Str::contains(Route::currentRouteName(), 'offer')) active_menu @endif
                                ">
                                <div class="menu-items">
                                    <i class="feather icon-file-text dashboard-icon"></i>
                                    <h6 class="dashboard-title">VERKAUF</h6>
                                </div>
                                </a>

                                <div class="submenu
                                @php $previousUrl = url()->previous();
$previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName(); @endphp
                                @if(Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'offer')) show
                                @elseif(Str::contains(Route::currentRouteName(), 'offer')) show @endif">
                                <ul>

                                <li>
                                        @php
$currentRoute = Route::currentRouteName();

$isActive = Str::contains($currentRoute, 'offers.wizard.smart');
                                        @endphp

                                        <a href="{{ route('offers.wizard.smart') }}"
                                        class="{{ $isActive ? 'active-link' : '' }}"
                                        style="{{ $isActive ? 'color:#94c11b!important' : '' }}">
                                            @if($isActive)
                                                <i class="feather icon-arrow-right warning"></i>
                                            @endif

                                            <i class="feather icon-cpu"></i> SMART ANGEBOTS-WIZARD
                                        </a>
                                    </li>
                                    <li>
                                    @php
$previousUrl = url()->previous();
try {
    $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
} catch (\Exception $e) {
    $previousRoute = null;
}
$isActive = (Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'offer.view'))
    || Str::contains(Route::currentRouteName(), 'offer.view');
                                    @endphp
                                    <a href="{{ route('admin.offers.index') }}" class="{{ $isActive ? 'active-link' : '' }}" style="{{ $isActive ? 'color:#94c11b!important' : '' }}">
                                        @if($isActive)<i class="feather icon-arrow-right warning"></i>@endif
                                        <i class="feather icon-list"></i> ANGEBOT LISTE
                                       
                                    </a>
                                    </li>

                                     <li>
                                    @php
$previousUrl = url()->previous();
try {
    $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
} catch (\Exception $e) {
    $previousRoute = null;
}
$isActive = (Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'offer.view'))
    || Str::contains(Route::currentRouteName(), 'offer.view');
                                    @endphp
                                    <a href="{{ route('offers.wizard') }}" class="{{ $isActive ? 'active-link' : '' }}" style="{{ $isActive ? 'color:#94c11b!important' : '' }}">
                                        @if($isActive)<i class="feather icon-arrow-right warning"></i>@endif
                                        <i class="feather icon-list"></i> ANGEBOT VORLAGE
                                         
                                    </a>
                                    </li>
 
                                </ul>
                                </div>
                            </li>

                            {{-- AUFTRAG (DEALS) --}}
                            <li class="horizontal_menu_item nav-item" id="menu2">
                                <a href="javascript:void(0);" aria-haspopup="true" aria-expanded="false"
                                class="menu
                                    @php $previousUrl = url()->previous();
$previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName(); @endphp
                                    @if(Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'deal')) active_menu
                                    @elseif(Str::contains(Route::currentRouteName(), 'deal')) active_menu @endif
                                ">
                                <div class="menu-items">
                                    <i class="feather icon-briefcase dashboard-icon"></i>
                                    <h6 class="dashboard-title">AUFTRAG</h6>
                                </div>
                                </a>

                                @php
$deals = DB::table('deals')->select('id', 'status', 'deleted_at', 'status_msg')->get();
$deal_new = $deals->where('status_msg', 'new')->whereNotIn('status', ['Junk', 'confirm', 'complete'])->count();
$deal_confirm = $deals->where('status_msg', 'confirm')->count();
$deal_junk = $deals->where('status', 'Junk')->whereNull('deleted_at')->count();
$deal_all = $deals->whereNull('deleted_at')->whereNotIn('status', ['Junk'])->count();
$deal_delete = $deals->where('deleted_at', '!=', null)->count();
                                @endphp

                                <div class="submenu
                                @php $previousUrl = url()->previous();
$previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName(); @endphp
                                @if(Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'deal')) show
                                @elseif(Str::contains(Route::currentRouteName(), 'deal')) show @endif">
                                <ul>
                                    <li>
                                    @php
$previousUrl = url()->previous();
try {
    $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName();
} catch (\Exception $e) {
    $previousRoute = null;
}
$isActive = (Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'deal.all.list'))
    || Str::contains(Route::currentRouteName(), 'deal.all.list');
                                    @endphp
                                    <a href="{{ route('deal.details') }}" class="{{ $isActive ? 'active-link' : '' }}" style="{{ $isActive ? 'color:#94c11b!important' : '' }}">
                                        @if($isActive)<i class="feather icon-arrow-right warning"></i>@endif
                                        <i class="feather icon-layers"></i> ALLE | {{ $deal_all }}
                                    </a>
                                    </li>

                                    <li>
                                    <a href="{{ route('admin.invoices.index') }}">
                                        <i class="feather icon-file-text"></i> RECHNUNGEN 
                                    </a>
                                    </li>

                                    <li>
                                    @php
$isActive = (Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'deal.junk.list'))
    || Str::contains(Route::currentRouteName(), 'deal.junk.list');
                                    @endphp
                                    <a href="{{ route('deal.junk.list') }}" class="{{ $isActive ? 'active-link' : '' }}" style="{{ $isActive ? 'color:#94c11b!important' : '' }}">
                                        @if($isActive)<i class="feather icon-arrow-right warning"></i>@endif
                                        <i class="feather icon-slash"></i> JUNK | {{ $deal_junk }}
                                    </a>
                                    </li>

                                    <li>
                                    @php
$isActive = (Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'deal.delete.list'))
    || Str::contains(Route::currentRouteName(), 'deal.delete.list');
                                    @endphp
                                    <a href="{{ route('deal.delete.list') }}" class="{{ $isActive ? 'active-link' : '' }}" style="{{ $isActive ? 'color:#94c11b!important' : '' }}">
                                        @if($isActive)<i class="feather icon-arrow-right warning"></i>@endif
                                        <i class="feather icon-trash"></i> GELÖSCHTE | {{ $deal_delete }}
                                    </a>
                                    </li>
                                </ul>
                                </div>
                            </li>

                            {{-- MONTAGE (PROJECTS) --}}
                            <li class="horizontal_menu_item nav-item" id="menu3">
                                <a href="javascript:void(0);" aria-haspopup="true" aria-expanded="false"
                                class="menu
                                    @php $previousUrl = url()->previous();
$previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName(); @endphp
                                    @if(Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'project')) active_menu
                                    @elseif(Str::contains(Route::currentRouteName(), 'project') && Str::contains($previousRoute, 'project')) active_menu @endif
                                ">
                                <div class="menu-items">
                                    <i class="feather icon-settings dashboard-icon"></i>
                                    <h6 class="dashboard-title">MONTAGE</h6>
                                </div>
                                </a>

                                <div class="submenu {{ Str::contains(Route::currentRouteName(), 'project') ? 'show' : '' }}">
                                <ul>
                                    <li>
                                    <a href="{{ route('planner.index') }}" @if(Route::currentRouteName() == 'project.create.new') style="color:#94c11b!important" @endif>
                                        <i class="feather icon-plus-circle"></i> PLANNUNG
                                    </a>
                                    </li> 
                                    <li>
                                    <a href="{{ route('planner.list') }}" @if(Route::currentRouteName() == 'project.detailss') style="color:#94c11b!important" @endif>
                                        <i class="feather icon-list"></i> ÜBERSICHT
                                    </a>
                                    </li> 
                                </ul>
                                </div>
                            </li>
 

                            @php
                            $open_ticket = DB::table('problems')
                                ->whereNull('deleted_at')
                                ->whereNotIn('status', ['end', 'beendet'])
                                ->count();
                            @endphp

                            {{-- TICKET --}}
                            <li class="horizontal_menu_item nav-item" id="menu8">
                                <a href="javascript:void(0);" aria-haspopup="true" aria-expanded="false"
                                class="menu
                                    @php $previousUrl = url()->previous();
                                    $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName(); @endphp
                                    @if(Str::contains(Route::currentRouteName(), 'new.lead.profile') && Str::contains($previousRoute, 'ticket')) active_menu
                                    @elseif(Str::contains(Route::currentRouteName(), 'ticket') && Str::contains($previousRoute, 'ticket')) active_menu @endif
                                ">
                                <div class="menu-items">
                                    <i class="feather icon-life-buoy dashboard-icon"></i>
                                    <h6 class="dashboard-title">TICKET</h6>
                                </div>
                                </a>

                                <div class="submenu">
                                <ul>
                                    <li>
                                    <a href="{{ url('/error') }}">
                                        <i class="feather icon-alert-triangle"></i> FEHLER UND FEHLERHEFT
                                    </a>
                                    </li>
                                    <li>
                                    <a href="{{ url('problem_create') }}">
                                        <i class="feather icon-tag"></i> ANLEGEN
                                    </a>
                                    </li>
                                    <li>
                                    <a href="{{ url('problem_view') }}">
                                        <i class="feather icon-tool"></i> LISTE | {{ $open_ticket }}
                                    </a>
                                    </li>
                                </ul>
                                </div>
                            </li>

                            {{-- PROZESS --}}
                            <li class="horizontal_menu_item nav-item ml-2" id="menu11">
                                <a href="{{ url('lead/kanban') }}" aria-haspopup="true" aria-expanded="false" class="menu">
                                <div class="menu-items">
                                    <i class="feather icon-activity dashboard-icon"></i>
                                    <h6 class="dashboard-title">ÜBERSICHT</h6>
                                </div>
                                </a>
                            </li>

                            <li class="horizontal_menu_item nav-item ml-2 sa-report-nav" id="reportNotifyMenu">
                                <a href="javascript:void(0);" class="menu sa-report-trigger" id="reportNotifyTrigger">
                                    <div class="menu-items position-relative">
                                        <i class="feather icon-clipboard dashboard-icon"></i>

                                        <span id="reportNotifyBadge" class="sa-report-badge" style="display:none;">0</span>

                                        <h6 class="dashboard-title">REPORTS</h6>
                                    </div>
                                </a>

                                <div id="reportNotifyDropdown" class="sa-report-dropdown">
                                    <div class="sa-report-drop-head">
                                        <div>
                                            <strong>Neue Berichte</strong>
                                            <small>Aktuelle Meldungen</small>
                                        </div>

                                        <button type="button" id="reportMarkAllReadBtn">
                                            Alle gelesen
                                        </button>
                                    </div>

                                    <div id="reportNotifyList" class="sa-report-drop-list">
                                        <div class="sa-report-empty">Keine neuen Berichte.</div>
                                    </div>

                                    <div class="sa-report-drop-foot">
                                        <a href="{{ route('admin.report.index') }}">
                                            Alle Reports öffnen
                                        </a>
                                    </div>
                                </div>
                            </li>
 

                            </ul>

                            <style>
                            .dashboard-icon { font-size:22px; margin-right:8px; line-height:1; color:#74b2d4; }
                            .menu.active_menu .dashboard-icon,
                            .submenu a.active-link .feather { color:#94c11b; }
                            .dashboard-title { margin-bottom:0; }
                            </style>
                        </div>
                        
                        
                            <ul class="nav-right d-flex align-items-center list-unstyled mb-0 ml-auto">
                                <li class="nav-item mx-1 sa-navbar-action">
                                    <a class="nav-link nav-link-search d-flex align-items-center justify-content-center p-1" 
                                    href="javascript:void(0);" data-toggle="tooltip" title="Suche">
                                        <i class="feather icon-search font-medium-4 text-secondary"></i>
                                    </a>
                                </li>

                                <li class="nav-item mx-1 sa-navbar-action">
                                    <a class="nav-link d-flex align-items-center justify-content-center position-relative p-1" 
                                    href="javascript:void(0);" onclick="toggleActivitySidebar()" title="Live-Aktivitäten">
                                        <i class="feather icon-activity font-medium-4 text-primary"></i>
                                        <span id="activityBadge" 
                                            class="badge badge-pill badge-danger badge-up shadow-sm"
                                            style="display:none; position:absolute; top: 0; right: 0; font-size: 0.7rem; padding: 0.3em 0.5em;">
                                            0
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item dropdown dropdown-notification mx-1">
                                    <a class="nav-link d-flex align-items-center justify-content-center position-relative p-1" 
                                    href="javascript:void(0);" data-open="notifications" title="Benachrichtigungen">
                                        <i class="feather icon-bell font-medium-4 text-secondary"></i>
                                        <span id="navbarNotificationBadge" 
                                            class="badge badge-pill badge-danger badge-up shadow-sm"
                                            style="display:none; position:absolute; top: 0; right: 0; font-size: 0.7rem; padding: 0.3em 0.5em;">
                                            0
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item quick_sider_wrapper ml-1">
                                    <a href="javascript:void(0);" 
                                    class="nav-link quick-menu-trigger d-flex align-items-center shadow-sm border rounded-pill px-2 py-1" 
                                    id="openSiderBtn">
                                        <i class="feather icon-grid mr-50"></i>
                                        <span class="font-small-3 font-weight-bolder text-uppercase ls-1">Quick Menu</span>
                                    </a>

                                    <div class="hover-quick-strip shadow-lg border-0 rounded-lg"> 
                                        
                                        <a href="javascript:void(0);" class="hqs-link" id="openInnerSiderBtn">
                                            <i class="feather icon-grid"></i><span class="hqs-text">Alle Apps</span>
                                        </a>
                                        <a href="{{ url('/') }}" class="hqs-link">
                                            <i class="feather icon-home"></i><span class="hqs-text">Dashboard</span>
                                        </a>
                                        <a href="{{ url('admin/chat') }}" class="hqs-link">
                                            <i class="feather icon-message-square"></i><span class="hqs-text">Nachrichten</span>
                                        </a>
                                        <a href="{{ url('tasks/calendar/personal') }}" class="hqs-link">
                                            <i class="feather icon-calendar"></i><span class="hqs-text">Kalender</span>
                                        </a>
                                        <a href="{{ url('admin/todo/personal?tab=my') }}" class="hqs-link">
                                            <i class="feather icon-check-square"></i><span class="hqs-text">Aufgaben</span>
                                        </a>

                                         <a href="{{ url('customer/appointments') }}" class="hqs-link">
                                            <i class="feather icon-calendar"></i><span class="hqs-text">Termin</span>
                                        </a>
                                        
                                        <div class="hqs-divider my-1 border-top"></div>
                                        
                                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="hqs-link hqs-danger pb-2">
                                            <i class="feather icon-power"></i><span class="hqs-text">Logout</span>
                                        </a>
                                    </div>
                                </li>
                            </ul>
                                
                        </div>
                        {{-- /bookmark-wrapper --}}

                    </div>
                    </div>
                </div>
            </nav>

             

            <!-- END: Header-->   
            <!-- BEGIN: Content--> 
            <div class="sidebar" id="sidebar" data-color="">
                <div class="sidebar-logo">
                    <img src="{{ asset('logo/solar.svg')}}" alt="">
                        <button id="pin-button"><i class="feather icon-edit-1"></i></button>
                </div>
                <div class="sa-profile-menu" data-sa-menu="profile">
                    <a
                        href="#"
                        class="sa-trigger nav-link"
                        role="button"
                        aria-haspopup="true"
                        aria-expanded="false"
                        aria-controls="saProfileMenu-{{ auth()->user()->name }}"
                        data-sa-toggle
                        style="justify-items:center;     justify-self: center;">
                        <span>
                        <img
                            class="round"
                            src="{{ asset('images/user/' . auth()->user()->image)}}"
                            style="border-radius:50%; box-shadow:0 0;"
                            alt="avatar"
                            height="40"
                            width="40"
                        >
                        </span>

                        <div class="user-nav d-sm-flex d-none" style="    display: flex ; flex-direction: column;  ">
                        <span class="user-name text-bold-600">
                                                    {{
                            DB::table('users')
                                ->join('employees', 'employees.id', '=', 'users.name')
                                ->where('users.name', auth()->user()->name)
                                ->select('employees.name')
                                ->pluck('name')
                                ->first()
                            }}
                        </span>
                        <span class="user-status"><small>verfügbar</small></span>
                        </div>
                    </a>

                    <div class="sa-menu sa-menu-right" id="saProfileMenu-{{ auth()->user()->name }}" role="menu" hidden>
                        <!-- STATUS -->
                        <button type="button" class="sa-item" data-sa-status="aktiv">
                        <i class="feather icon-user-check"></i> Aktiv
                        </button>
                        <button type="button" class="sa-item" data-sa-status="abwesend">
                        <i class="feather icon-user-x"></i> Abwesend
                        </button>
                        <button type="button" class="sa-item" data-sa-status="mittagspause">
                        <i class="feather icon-pause"></i> Mittagspause
                        </button>

                        <div class="sa-divider" role="separator"></div>

                        <!-- LINKS -->
                        <a class="sa-item" href="{{ url('/employee_profile/' . auth()->user()->name) }}" role="menuitem">
                        <i class="feather icon-user"></i> Mein Profil
                        </a>
                        <a class="sa-item" href="{{ url('/employee_notifications/' . auth()->user()->name) }}" role="menuitem">
                        <i class="feather icon-bell"></i> Meine Anträge
                        </a>

                        <a class="sa-item" href="{{ url('admin/employees/' . auth()->user()->name . '/time-management')}}" role="menuitem">
                            <i class="feather icon-clock"></i> Arbeitzeit
                        </a>
                        <a class="sa-item" href="{{ url('/user') }}" role="menuitem">
                        <i class="feather icon-user"></i> Profil bearbeiten
                        </a>
                    </div>
                </div>  
                <div class="sidebar-header">
                    <a href="{{ url('/') }}"><span><i class="feather icon-home"></i> Dashboard</span> </a>
                </div> 
                <div class="sidebar-content">
                    <ul> 
                        <li class="nav-has-submenu" data-name="Email" id="email-menu"  >
                            <a href="javascript:void(0);">
                                <i class="fa fa-envelope"></i> Kommunikation
                            </a>
                            <ul class="nav-submenu">
                                <li><a href="{{ url('/email_view') }}"><i class="fa fa-envelope-open"></i> Email</a></li> 
                                <li><a href="{{ route('lead.email.inbox') }}"><i class="fa fa-envelope-open"></i> Lead Emails</a></li> 

                                <li class="nav-has-submenu">
                                        <a href="javascript:void(0);">
                                            <i class="feather icon-settings"></i> Email Konfigurator
                                        </a>
                                        <ul class="nav-submenu">
                                            <li><a href="{{ url('/email_configuration') }}"><i class="feather icon-chevron-right"></i> Personal Email</a></li>
                                            <li><a href="{{ route('lead-email-accounts.index') }}"><i class="feather icon-chevron-right"></i> Lead Emails</a></li>
                                            <li><a href="{{ route('lead.email.domain.filters.index') }}"><i class="feather icon-chevron-right"></i> Email Filters</a></li>
                                        </ul>
                                    </li>
                                <li><a href="{{ route('chats.view', auth()->user()->name) }}"><i class="fa fa-cogs"></i> Chat</a></li>
                            </ul>
                        </li> 
                                <!-- Main Partner Menu -->
                        <li class="nav-has-submenu" data-name="Partner" id="partner-menu" style="display:none;">
                            <a href="javascript:void(0);">
                                <i class="fa fa-building"></i> Kontakte
                            </a>
                            <ul class="nav-submenu">
                                <li><a href="{{ route('inquiry.create') }}"><i class="fa fa-plus"></i> Kontakt Anlegen</a></li>
                                <li><a href="{{ route('all.contacts') }}"><i class="fa fa-lists"></i> Kontaktlisten</a></li>
                                <li><a href="{{ route('brand.index') }}"><i class="fa fa-industry"></i> Hersteller</a></li>
                                <li><a href="{{ route('distributors.index') }}"><i class="fa fa-truck"></i> Lieferant</a></li>
                                <li><a href="{{ route('external.info') }}"><i class="fa fa-users"></i> Zeitarbeitfirma</a></li>
                                <li><a href="{{ route('brand.sub.contractor') }}"><i class="fa fa-tools"></i> Nach Unternehmer</a></li>
                                <li><a href="{{ route('brand.architect') }}"><i class="fa fa-pencil-ruler"></i> Architekten</a></li>
                                <li><a href="{{ route('brand.bank') }}"><i class="fa fa-university"></i> Bank</a></li>
                                <li><a href="{{ route('brand.insurance') }}"><i class="fa fa-shield-alt"></i> Versicherung</a></li> 
                            </ul>
                        </li>


                        <li class="nav-has-submenu" data-name="Inquiry" id="inquiry-menu">
                            <a href="javascript:void(0);">
                                <i class="feather icon-user-check"></i> Anfragen
                            </a>
                            <ul class="nav-submenu">
                                <li><a href="{{ route('inquiry.create') }}"><i class="fa fa-plus-circle"></i> Anlegen</a></li>
                                <li><a href="{{ route('my.inquiry.view') }}"><i class="fa fa-list-ol"></i> Meine</a></li>
                                <li><a href="{{ route('inquiry.view') }}"><i class="fa fa-list"></i> Liste</a></li>
                                <li><a href="{{ url('inquiry_junklist') }}"><i class="feather icon-slash"></i> Junk</a></li>
                                <li><a href="{{ url('inquiry_deleted_list') }}"><i class="feather icon-trash"></i> Gelöschte</a></li> 
                                <li>
                                    <a href="{{ route('fusion.forms.index') }}">
                                        <i class="feather icon-globe"></i> Website Leads
                                    </a>
                                </li>
                            </ul>
                        </li> 

                        <li class="nav-has-submenu" data-name="Projects" id="project_menus">
                            <a href="javascript:void(0);">
                                <i class="feather icon-folder"></i> Projecte
                            </a>
                            <ul class="nav-submenu"> 

                                <!-- Leads -->
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

                                        {{-- Heizkörperkonfiguration --}}
                                        <li class="{{ request()->routeIs('radiator.config.*') ? 'active' : '' }}">
                                            <a href="{{ route('radiator.config.view') }}">
                                                <i class="fa fa-thermometer-half"></i> Heizkörperkonfiguration
                                            </a>
                                        </li>

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
                                        <li><a href="{{ url('personal/task/' . auth()->user()->name)}}"><i class="fa fa-exclamation-circle"></i> Aufgabenerteilung</a></li>
                                        <li><a href="{{ url('task_todo/' . auth()->user()->name)}}"><i class="fa fa-tools"></i> Projekt Aufgaben</a></li>
                                        <li><a href="{{ url('tasks/calendar/personal/')}}"><i class="fa fa-thumbs-down"></i> Meine Kalender</a></li>
                                        <li><a href="{{ url('customer/appointments')}}"><i class="fa fa-ambulance"></i> Terminliste</a></li>
                                        <li><a href="{{ url('lead/overview')}}"><i class="fa fa-wrench"></i> Prozess</a></li>
                                        <li><a href="{{ url('lead/kanban')}}"><i class="fa fa-wrench"></i> Lead Übersicht </a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>



                        <li class="nav-has-submenu" data-name="Service" id="service-menu" style="display:none;">
                            <a href="javascript:void(0);">
                                <i class="feather icon-tag"></i> Service
                            </a>
                            <ul class="nav-submenu">

                                <li>
                                    <a href="{{ url('error') }}">
                                        <i class="fa fa-exclamation-circle"></i> Fehler & Felherhelft
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('problem_create') }}">
                                        <i class="fa fa-plus"></i> Ticket Anlegen
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('problem_view') }}">
                                        <i class="fa fa-list"></i> Liste
                                    </a>
                                </li>

                            

                            </ul>
                        </li>

                        <li class="nav-has-submenu" >
                            <a href="javascript:void(0);">
                                <i class="feather icon-tag"></i> Wartungs
                            </a>
                            <ul class="nav-submenu">

                                <li>
                                    <a href="{{ url('admin/maintenance/contracts') }}">
                                        <i class="fa fa-folder-open"></i> Wartungsverträge
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('admin.maintenance_checklists.index') }}#new-checklist">
                                        <i class="fa fa-plus-circle"></i> Checkliste
                                    </a>
                                </li>

                            </ul>
                        </li> 

                        <li class="nav-has-submenu">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-tasks"></i> To-Dos
                                </a>
                                <ul class="nav-submenu">
                                    <li><a href="{{ route('note.category.view') }}"><i class="fa fa-folder"></i> Kategorie</a></li>
                                    <li><a href="{{ route('notes.details') }}"><i class="fa fa-list-alt"></i> To-Do's Details</a></li> 
                                </ul>
                        </li> 



                        <!-- Main Employee Menu -->
                        <li class="nav-has-submenu" data-name="Employee" id="employee-menu" style="display:none;">
                            <a href="javascript:void(0);">
                                <i class="feather icon-users"></i> Personal
                            </a>
                            <ul class="nav-submenu">

                                <!-- Mitarbeiter Details -->
                                <li class="nav-has-submenu">
                                    <a href="javascript:void(0);">
                                        <i class="feather icon-copy"></i> Mitarbeiter Details
                                    </a>
                                    <ul class="nav-submenu">
                                        <li><a href="{{ route('emp.create') }}"><i class="fa fa-user-plus"></i> Mitarbeiter registrieren</a></li>
                                        <li><a href="{{ route('emp.info') }}"><i class="fa fa-user"></i> Mitarbeiterliste</a></li>
                                        <li><a href="{{ route('time_management.slots') }}"><i class="feather icon-clock"></i>Arbeitzeit</a></li>
                                    </ul>
                                </li>

                                <li class="nav-has-submenu {{ request()->routeIs('teams.*') ? 'open' : '' }}">
                                    <a href="javascript:void(0);" class="{{ request()->routeIs('teams.*') ? 'active' : '' }}">
                                        <i class="fa fa-users"></i> Teams
                                    </a>
                                    <ul class="nav-submenu">
                                        <li class="{{ request()->routeIs('teams.index') ? 'active' : '' }}">
                                            <a href="{{ route('teams.index') }}">
                                                <i class="fa fa-layer-group"></i> Alle Teams
                                            </a>
                                        </li> 
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

                                        {{-- ✅ NEW: Attendance Analytics --}}
                                        <li><a href="{{ route('admin.attendance.analytics') }}"><i class="fa-solid fa-user-check"></i> Anwesenheit</a></li>
                                    </ul>
                                </li>


                                <!-- Gehaltsmanagement -->
                                                                        @if(
    DB::table('user_rolls')
        ->where('user_rolls.user_id', '=', auth()->user()->name)
        ->where('user_rolls.item_id', '=', 'Super')
        ->where('user_rolls.is_add', '=', 'on')
        ->first()
)
                                        <li class="nav-has-submenu">
                                            <a href="javascript:void(0);">
                                                <i class="fa fa-money-bill-wave"></i> Gehaltsmanagement
                                            </a>
                                            <ul class="nav-submenu">
                                                <li><a href="{{ route('salary.index') }}"><i class="fa fa-calculator"></i> Lohn-Vollkosten</a></li>
                                            </ul>
                                        </li>
                                        @endif

                        
                                <!-- Bewerber -->
                                    <li>
                                        <a href="{{ route('admin.overdue-center.reports.index') }}">
                                            <i class="fa fa-user-tie"></i> Report
                                        </a>
                                        </li>


                                <!-- Abteilung & Berufsbezeichnung -->
                                

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
                                        <li><a href="{{ route('public-holidays.index') }}"><i class="fa fa-toggle-off"></i> Gesetzliche Feiertage</a></li>
                                        <li><a href="{{ route('holiday.info') }}"><i class="fa fa-toggle-off"></i> Feiertage</a></li>
                                        <li><a href="{{ route('leave.day.info') }}"><i class="fa fa-toggle-off"></i> Urlaubstage</a></li>
                                    </ul>
                                </li>

                                <!-- Steuereinstellung -->
                                <li><a href="{{ route('tax.info') }}"><i class="fa fa-percentage"></i> Steuereinstellung</a></li>
                            </ul>
                        </li>
                

                        <li class="nav-has-submenu" data-name="Organization" id="organization-menu" style="display:none;">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-tasks"></i> Organisation
                                </a>
                                <ul class="nav-submenu"> 
                                    <li><a href="{{ route('department.info') }}"><i class="fa fa-building"></i> Abteilung</a></li>
                                    <li><a href="{{ route('position.index') }}"><i class="fa fa-briefcase"></i> Position</a></li>
                                    <li><a href="{{ route('department.organize') }}"><i class="fa fa-project-diagram"></i> Organisationsstruktur</a></li>
                                </ul>
                        </li> 

                    
                        <!-- Main Product Menu -->
                        <li class="nav-has-submenu" data-name="Product" id="product-menu" style="display:none;">
                            <a href="javascript:void(0);">
                                <i class="feather icon-box"></i> Artikelverwaltung
                            </a>
                            <ul class="nav-submenu">

                                <!-- Product Section -->
                                <li class="nav-has-submenu">
                                    <a href="javascript:void(0);">
                                        <i class="feather icon-box"></i> Artikel
                                    </a>
                                    <ul class="nav-submenu">
                                        <li><a href="{{ route('product.create') }}"><i class="feather icon-plus"></i> Artikel Anlegen</a></li> 
                                        <li><a href="{{ route('product.info') }}"><i class="feather icon-file"></i> Artikel liste</a></li> 
                                    
                                        <li>
                                            <a href="{{ route('product.favorites.index') }}">
                                                <i class="feather icon-star"></i> Favoritenliste  
                                            </a>
                                        </li>

                                        <li>
                                            <a href="{{ route('stamp.lists.index') }}">
                                                <i class="feather icon-award"></i> Stempel-Favoriten
                                            </a>
                                        </li>


                                        <li>
                                            <a href="{{ route('admin.products.difference') }}">
                                                <i class="feather icon-layers"></i> Produkt­vergleich
                                            </a>
                                        </li>


                                        <!-- Product Settings (Submenu) -->
                                        <li class="nav-has-submenu">
                                            <a href="javascript:void(0);">
                                                <i class="feather icon-settings"></i> Produkteinstellungen
                                            </a>
                                            <ul class="nav-submenu">
                                                <li><a href="{{ route('measure.info') }}"><i class="feather icon-sliders"></i> Einheit</a></li>
                                                <li><a href="{{ route('discount_group.info') }}"><i class="fa fa-percent"></i> Rabatt-Gruppe</a></li>
                                                <li><a href="{{ route('article_group.index') }}"><i class="fa fa-layer-group"></i> Artikel-Gruppe</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>

                                <li >
                                    <a href="{{ route('ids.search.form') }}">
                                        <i class="fa fa-arrow-circle-up"></i> GC Online
                                    </a> 
                                </li>

                                 <li class="{{ Str::startsWith(Route::currentRouteName(), 'admin.master_sets.') ? 'active' : '' }}" data-name="Product" id="organization-menu" style="display:none;">
                                    <a href="{{ route('admin.master_sets.index') }}">
                                        <i class="fa fa-cart-plus"></i> Master Set  
                                    </a> 
                                </li>



                                <!-- Outgoing Request -->
                                <li>
                                    <a href="{{ route('request.out.details') }}">
                                        <i class="fa fa-arrow-circle-up"></i> Ausgehend anfordern
                                    </a> 
                                </li>

                                <!-- Purchase Request -->
                                <li>
                                    <a href="{{ route('purchase.request') }}">
                                        <i class="fa fa-shopping-cart"></i> Kaufanfrage
                                    </a> 
                                </li>

                            </ul>
                        </li> 

                        <li class="nav-has-submenu" data-name="Product" id="organization-menu" style="display:none;">
                            <a href="javascript:void(0);">
                                <i class="fa fa-truck"></i> Lager
                            </a>
                            <ul class="nav-submenu">
                                <li >
                                    <a href="{{ route('inventory.index') }}">
                                        <i class="fa fa-warehouse"></i> Inventar
                                    </a>
                                    
                                    <a href="{{ route('delivery-notes.index') }}">
                                        <i class="fa fa-file-invoice"></i> Lieferschein
                                    </a>
                                </li>
 
 
                                <li class="nav-has-submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-qrcode"></i> Vermögensbestand
                                    </a>
                                    <ul class="nav-submenu"> 
                                        <li><a href="{{ route('handover.details.asset') }}"><i class="fa fa-exchange-alt"></i> Assets</a></li> 

                                        <li><a href="{{ route('handover.details') }}"><i class="fa fa-exchange-alt"></i> Gegenstände übergeben</a></li>  
                                    </ul>
                                </li>
                            </ul>
                        </li> 

                      
                        
                        <!-- Main Controlling Menu -->
                        <li class="nav-has-submenu" data-name="Finance" id="finance-menu" style="display:none;">
                            <a href="javascript:void(0);">
                                <i class="feather icon-file"></i> Finanzen
                            </a>
                            <ul class="nav-submenu">
                                <li><a href="{{ route('beg-fundings.index') }}"><i class="fa fa-file-invoice"></i> Förderung</a></li>
                                 <li><a href="{{ route('branch.expense') }}"><i class="fa fa-money-check-alt"></i> Spesenarten für Filialen</a></li>
                                <li><a href="{{ route('assets.installment.show') }}"><i class="fa fa-credit-card"></i> Ratenzahlung</a></li>
                            </ul>
                        </li>

                        <!-- Main Controlling Menu -->
                        <li class="nav-has-submenu" data-name="Product" id="finance-menu" style="display:none;">
                            <a href="javascript:void(0);">
                                <i class="fa fa-car"></i> Anlagen
                            </a>
                            <ul class="nav-submenu">
                                <li><a href="{{ route('machine.inventory') }}"><i class="fa fa-car"></i> Auto & Machine</a></li> 
                            </ul>
                        </li>


                        <li>
                            <a href="{{ route('branch.info') }}">
                                <i class="fa fa-map-pin"></i> Standorte
                            </a>
                        </li>
                    
                        <!-- Main Benutzerverwaltung Menu -->
                        <li class="nav-has-submenu" data-name="Users" id="user-menu" style="display:none;">
                            <a href="javascript:void(0);">
                                <i class="feather icon-user"></i> Benutzerverwaltung
                            </a>
                            <ul class="nav-submenu">
                                <li><a href="{{ url('/admin_user') }}"><i class="fa fa-user-shield"></i> Admins</a></li>
                                <li><a href="{{ url('/limit_user') }}"><i class="fa fa-user-lock"></i> Limited</a></li>
                                <li><a href="{{ route('user-rolls.index') }}"><i class="fa fa-user-cog"></i> User Rolle</a></li>
                                <li><a href="{{ url('/user') }}"><i class="fa fa-user-circle"></i> User Profile</a></li>
                            </ul>
                        </li> 

                        <li class="nav-has-submenu">
                            <a href="javascript:void(0);">
                                <i class="fa fa-tasks"></i> Konfigurationen
                            </a>
                            <ul class="nav-submenu">  

                                    <li class="nav-has-submenu">
                                        <a href="javascript:void(0);">
                                            <i class="fa fa-money-bill-wave"></i> Projekt-Checkliste
                                        </a>
                                        <ul class="nav-submenu">
                                            <li><a href="{{ route('task_phase.index') }}"><i class="fa fa-clock"></i> Arbeitsschritte</a></li> 
                                            <li><a href="{{ route('stages.index') }}"><i class="fa fa-clock"></i> Stage</a></li> 
                                         </ul>
                                    </li>

                                    <li class="nav-has-submenu">
                                        <a href="javascript:void(0);">
                                            <i class="fa fa-gg"></i> Pipelines
                                        </a>
                                        <ul class="nav-submenu">
                                            <li><a href="{{ route('product.position.view') }}"><i class="fa fa-users"></i> Mitarbeitervorschläge</a></li>  
                                            <li><a href="{{ route('product.formula.index') }}"><i class="feather icon-layout"></i> Produktformeln</a></li>  
                                        </ul>
                                    </li>
                            </ul>
                        </li> 

                        <li>
                            <a href="{{ route('admin.chat.learnings.index') }}">
                                <i class="feather icon-book-open"></i> LEARNING CHAT TOPICS
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('system.feedback.view') }}">
                                <i class="feather icon-info"></i> FEEDBACK
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('knowledge.base') }}">
                                <i class="fa fa-question-circle"></i> Hilfe
                            </a>
                        
                            <a href="{{ route('admin.system-warning.index') }}">
                                <i class="fa fa-exclamation-triangle"></i> Systemhinweis
                            </a>
                        
                            @php
                                $canDeleteGarbage = \Illuminate\Support\Facades\DB::table('user_rolls')
                                    ->where('user_id', auth()->user()->name ?? null)
                                    ->where('item_id', 'Administrator')
                                    ->where(function ($query) {
                                        $query->where('is_delete', 1)
                                            ->orWhere('is_delete', '1')
                                            ->orWhere('is_delete', 'on')
                                            ->orWhere('is_delete', true);
                                    })
                                    ->exists();
                            @endphp
                            
                                @if($canDeleteGarbage)
                                    <a href="{{ route('admin.garbage.index') }}">
                                        <i class="fa fa-trash"></i> Papierkorb reinigen
                                    </a>
                                @endif
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

            <div id="activityBackdrop" class="notif-backdrop" onclick="toggleActivitySidebar()"></div>

            <aside id="activitySidebar" class="notif-sider activity-sider" style="right:-430px;" aria-hidden="true">
                <!-- Header -->
                <header class="notif-header activity-header bg-primary text-white" style="display:flex; align-items:center; justify-content:space-between;">
                    <div class="d-flex align-items-center">
                        <div class="mr-2 d-flex align-items-center justify-content-center"
                            style="width:36px; height:36px; border-radius:10px; background:rgba(255,255,255,.15);">
                            <i class="feather icon-activity text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 text-white" style="font-weight:800;">Live-Aktivitäten</h5>
                            <small class="text-white" style="opacity:.85;">Verlauf, Suche, Filter und Details</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-light mr-1 activity-filter-btn"
                            onclick="toggleActivityFilters()"
                            title="Filter"
                            style="border-radius:10px;"
                        >
                            <i class="feather icon-filter"></i>
                        </button>

                        <button
                            type="button"
                            class="btn btn-sm btn-dark text-white"
                            onclick="toggleActivitySidebar()"
                            title="Schließen"
                            style="border-radius:10px;"
                        >
                            <i class="feather icon-x"></i>
                        </button>
                    </div>
                </header>

                <!-- Search -->
                <section class="p-3" style="background:#ffffff; border-bottom:1px solid #e5e7eb;">
                    <div class="d-flex align-items-center" style="gap:8px;">
                        <div class="input-group" style="margin-bottom:0;">
                            <input
                                type="text"
                                id="activitySearchInput"
                                class="form-control"
                                placeholder="Aktivitäten suchen..."
                                autocomplete="off"
                                style="border-radius:10px 0 0 10px !important;"
                            >
                            <div class="input-group-append">
                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    id="activitySearchBtn"
                                    style="border-radius:0 10px 10px 0 !important;"
                                >
                                    <i class="feather icon-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2 d-flex align-items-center justify-content-between">
                        <small class="text-muted">
                            Suche nach Kunde, Produkt, Mitarbeiter oder Aktivitätstext
                        </small>
                        <button
                            type="button"
                            class="btn btn-sm btn-light"
                            id="activitySearchResetBtn"
                            style="border-radius:10px;"
                        >
                            <i class="feather icon-rotate-ccw mr-50"></i> Zurücksetzen
                        </button>
                    </div>
                </section>

                <!-- Filter panel -->
               <div
                    id="activityFilterSection"
                    class="activity-filters p-3"
                    style="display:none; background:#f9fafb; border-bottom:1px solid #e5e7eb;"
                >
                    <div
                        class="mb-3"
                        style="padding:10px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:12px;"
                    >
                        <p class="small text-muted mb-0" style="line-height:1.5;">
                            Wähle aus, wessen Aktivitäten du sehen möchtest. Lässt du ein Feld leer, werden alle Einträge angezeigt.
                        </p>
                    </div>

                    <div class="sa-ms-wrapper" id="custom-select-customers">
                        <label class="sa-ms-label">Kunde filtern</label>
                        <div class="sa-ms-header">
                            <input type="text" class="sa-ms-search" placeholder="Kunden wählen...">
                            <i class="feather icon-chevron-down sa-ms-caret"></i>
                        </div>
                        <div class="sa-ms-dropdown">
                            @foreach(\App\Models\NewLeads::whereNull('deleted_at')->get() as $c)
                                <label class="sa-ms-option">
                                    <input type="checkbox" value="{{ $c->id }}" data-label="{{ $c->lastname }} {{ $c->name }}">
                                    <span class="sa-ms-option-text">
                                        {{ $c->lastname }} {{ $c->name }} {{ $c->firma ? '(' . $c->firma . ')' : '' }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="sa-ms-wrapper" id="custom-select-employees">
                        <label class="sa-ms-label">Mitarbeiter filtern</label>
                        <div class="sa-ms-header">
                            <input type="text" class="sa-ms-search" placeholder="Mitarbeiter wählen...">
                            <i class="feather icon-chevron-down sa-ms-caret"></i>
                        </div>
                        <div class="sa-ms-dropdown">
                            @foreach(\App\Models\Employee::where('status', 'Active')->get() as $e)
                                <label class="sa-ms-option">
                                    <input type="checkbox" value="{{ $e->id }}" data-label="{{ $e->name }} {{ $e->lastname }}">
                                    <span class="sa-ms-option-text">{{ $e->name }} {{ $e->lastname }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="sa-ms-wrapper" id="custom-select-products">
                        <label class="sa-ms-label">Produkt filtern</label>
                        <div class="sa-ms-header">
                            <input type="text" class="sa-ms-search" placeholder="Produkte wählen...">
                            <i class="feather icon-chevron-down sa-ms-caret"></i>
                        </div>
                        <div class="sa-ms-dropdown">
                            @foreach(\App\Models\ArticleGroup::all() as $p)
                                <label class="sa-ms-option">
                                    <input type="checkbox" value="{{ $p->id }}" data-label="{{ $p->article_group }}">
                                    <span class="sa-ms-option-text">{{ $p->article_group }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="sa-ms-wrapper mb-0" id="custom-select-types">
                        <label class="sa-ms-label">Typ filtern</label>
                        <div class="sa-ms-header">
                            <input type="text" class="sa-ms-search" placeholder="Typen wählen...">
                            <i class="feather icon-chevron-down sa-ms-caret"></i>
                        </div>
                        <div class="sa-ms-dropdown">
                            <label class="sa-ms-option">
                                <input type="checkbox" value="notes" data-label="Notizen">
                                <span class="sa-ms-option-text">Notizen</span>
                            </label>

                            <label class="sa-ms-option">
                                <input type="checkbox" value="process" data-label="Prozess">
                                <span class="sa-ms-option-text">Prozess</span>
                            </label>

                            <label class="sa-ms-option">
                                <input type="checkbox" value="ticket" data-label="Ticket">
                                <span class="sa-ms-option-text">Ticket</span>
                            </label>

                            <label class="sa-ms-option">
                                <input type="checkbox" value="appointment" data-label="Termin">
                                <span class="sa-ms-option-text">Termin</span>
                            </label>

                            <label class="sa-ms-option">
                                <input type="checkbox" value="customer" data-label="Kunde">
                                <span class="sa-ms-option-text">Kunde</span>
                            </label>

                            <label class="sa-ms-option">
                                <input type="checkbox" value="address" data-label="Objekt / Adresse">
                                <span class="sa-ms-option-text">Objekt / Adresse</span>
                            </label>

                            <label class="sa-ms-option">
                                <input type="checkbox" value="general" data-label="Allgemein">
                                <span class="sa-ms-option-text">Allgemein</span>
                            </label>
                        </div>
                    </div>

                    <div
                        class="mt-3"
                        style="padding:12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:12px;"
                    >
                        <label class="mb-0 d-flex align-items-center justify-content-between" style="cursor:pointer; gap:12px;">
                            <div>
                                <div style="font-weight:800; color:#111827;">Ton stummschalten</div>
                                <small class="text-muted">Keine Audio-Benachrichtigung bei neuen Live-Aktivitäten</small>
                            </div>
                            <input type="checkbox" id="activityMuteToggle" style="transform:scale(1.2);">
                        </label>
                    </div>

                    <div class="d-flex mt-3" style="gap:8px;">
                        <button
                            type="button"
                            class="btn btn-block"
                            style="background:#93c21c; color:#fff; font-weight:800; border-radius:10px;"
                            onclick="saveActivityFilters(event)"
                        >
                            <i class="feather icon-save mr-50"></i> Filter anwenden
                        </button>

                        <button
                            type="button"
                            class="btn btn-light"
                            id="clearActivityFiltersBtn"
                            style="font-weight:700; border-radius:10px; white-space:nowrap;"
                        >
                            <i class="feather icon-x-circle mr-50"></i> Leeren
                        </button>
                    </div>
                </div>

                <!-- Activity list -->

                <section class="activity-mode-tabs">
                    <button type="button"
                            class="activity-mode-tab active"
                            data-activity-mode="active">
                        <i class="feather icon-inbox"></i>
                        Aktuell
                    </button>

                    <button type="button"
                            class="activity-mode-tab"
                            data-activity-mode="archive">
                        <i class="feather icon-archive"></i>
                        Archiv
                    </button>
                </section>
                <section
                    id="activityList"
                    class="notif-list activity-list-scroll"
                    style="flex:1 1 auto; overflow-y:auto; background:#f8fafc;"
                >
                    <div
                        id="emptyActivity"
                        class="text-center text-muted mt-3"
                        style="padding:30px 18px;"
                    >
                        <div
                            class="mx-auto mb-2 d-flex align-items-center justify-content-center"
                            style="width:56px; height:56px; border-radius:16px; background:#eef2f7;"
                        >
                            <i class="feather icon-radio" style="font-size:1.6rem; opacity:.6;"></i>
                        </div>
                        <div style="font-weight:700; color:#6b7280;">Warte auf neue Aktivitäten...</div>
                        <small class="d-block mt-1">Sobald neue Einträge kommen, erscheinen sie hier.</small>
                    </div>
                </section>

                <!-- Pagination -->
                <section
                    id="activityPagination"
                    class="p-3"
                    style="background:#ffffff; border-top:1px solid #e5e7eb; border-bottom:1px solid #e5e7eb;"
                >
                    <div class="d-flex align-items-center justify-content-between" style="gap:10px;">
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary"
                            id="activityPrevBtn"
                            style="border-radius:10px;"
                        >
                            <i class="feather icon-chevron-left mr-50"></i> Zurück
                        </button>

                        <div class="text-center">
                            <div id="activityPageInfo" style="font-size:12px; font-weight:800; color:#374151;">Seite 1</div>
                            <small id="activityPageMeta" class="text-muted">20 Einträge pro Seite</small>
                        </div>

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary"
                            id="activityNextBtn"
                            style="border-radius:10px;"
                        >
                            Weiter <i class="feather icon-chevron-right ml-50"></i>
                        </button>
                    </div>
                </section>

                <!-- Detail panel -->
                
            </aside>

            <div id="activityDetailModalBackdrop" class="activity-modal-backdrop" style="display:none;">
                <div class="activity-detail-modal" role="dialog" aria-modal="true" aria-labelledby="activityDetailModalTitle">
                    <div class="activity-detail-modal-header">
                        <div class="d-flex align-items-center" style="gap:12px;">
                            <div id="activityDetailModalIcon" class="activity-modal-icon">
                                <i class="feather icon-activity"></i>
                            </div>

                            <div>
                                <h5 id="activityDetailModalTitle" class="mb-0">Aktivitätsdetails</h5>
                                <small id="activityDetailModalSubTitle">Vollständige Informationen zum Eintrag</small>
                            </div>
                        </div>

                        <button type="button" class="activity-modal-close" onclick="closeActivityDetailModal()">
                            <i class="feather icon-x"></i>
                        </button>
                    </div>

                    <div id="activityDetailModalBody" class="activity-detail-modal-body">
                        <!-- JS inserts detail content here -->
                    </div>

                    <div class="activity-detail-modal-footer">
                        <button type="button" class="btn btn-light" onclick="closeActivityDetailModal()" style="border-radius:12px; font-weight:800;">
                            Schließen
                        </button>
                    </div>
                </div>
            </div>
             <!-- Quick Sider -->
            <div id="quickSiderBackdrop" class="qs-backdrop"></div> 
            <aside id="quickSider" class="qs-sider">
                <div class="qs-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                    <i class="feather icon-command mr-2"></i>
                    <h5 class="mb-0 white">Schnellzugriff</h5>
                    </div>
                    <button class="btn btn-sm btn-danger text-dark" id="closeSiderBtn" aria-label="Close"><i class="feather icon-x"></i></button>
                </div>

                <div class="qs-content">
                    <!-- 4-per-row icon grid -->
                    <div class="qs-grid">
                    <!-- Keep your original classes so existing JS keeps working -->
                    <a class="qs-tile menu_view_icon">
                        <i class="feather icon-command"></i>
                        <span>Menü</span>
                    </a>

                    <a class="qs-tile dashboard_view_icon">
                        <i class="feather icon-home"></i>
                        <span>Dashboard</span>
                    </a>

                    <a class="qs-tile nav-link-search">
                        <i class="feather icon-search"></i>
                        <span>Suche</span>
                    </a>

                    <a class="qs-tile nav-link-time">
                        <i class="feather icon-play"></i>
                        <span>Zeiterfassung</span>
                    </a>

                    <a class="qs-tile message_view_icon position-relative">
                        <i class="feather icon-message-square"></i>
                        <span>Nachrichten</span>
                        <span class="badge badge-danger unread-message-count qs-badge" style="display:none;">0</span>
                    </a>

                    <a class="qs-tile calendar_view_icon">
                        <i class="feather icon-calendar"></i>
                        <span>Kalender</span>
                    </a>

                    <a class="qs-tile watch_view_icon" href="{{ route('employee.capacity.view') }}">
                        <i class="feather icon-watch"></i>
                        <span>Kapazität</span>
                    </a>

                    <a class="qs-tile map_view_icon" href="{{ route('lead.reference') }}">
                        <i class="feather icon-map-pin"></i>
                        <span>Karte</span>
                    </a>


                <a class="qs-tile map_view_icon" href="{{ route('ai.chats') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:26px;"
                            fill="none" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round"
                            class="w-5 h-5 inline-block align-middle me-2">
                            <!-- antenna -->
                            <circle cx="12" cy="2.5" r="1.3"/>
                            <path d="M12 3.8v1.8"/>
                            <!-- head & ears -->
                            <rect x="5" y="6" width="14" height="12" rx="3"/>
                            <rect x="3" y="9" width="2" height="6" rx="1"/>
                            <rect x="19" y="9" width="2" height="6" rx="1"/>
                            <!-- eyes -->
                            <circle cx="9" cy="12" r="1.5" fill="currentColor" stroke="none"/>
                            <circle cx="15" cy="12" r="1.5" fill="currentColor" stroke="none"/>
                            <!-- mouth -->
                            <rect x="9" y="15" width="6" height="2" rx="1"/>
                        </svg>
                        <span>KI Chat</span>
                        </a>

                        <button
                            type="button"
                            class="sa-tile sa-tile-present"
                            data-sa-collapse-target="#sa-present-panel"
                            data-sa-collapse-group="emp-status"
                            aria-expanded="false"
                            aria-controls="sa-present-panel"
                            >
                            <i class="feather icon-user sa-text-present"></i>
                            <span>Anwesend</span>
                            <span class="sa-badge sa-badge-present sa-active-count">0</span>
                    </button>

                    <a class="qs-tile map_view_icon" href="{{ route('breaking-news.index') }}">
                        <i class="feather icon-alert-triangle"></i>
                        <span>Breaking News</span>
                    </a>


                    <button
                            type="button"
                            class="sa-tile sa-tile-absent"
                            data-sa-collapse-target="#sa-absent-panel"
                            data-sa-collapse-group="emp-status"
                            aria-expanded="false"
                            aria-controls="sa-absent-panel"
                            >
                            <i class="feather icon-user sa-text-absent"></i>
                            <span>Abwesend</span>
                            <span class="sa-badge sa-badge-absent sa-inactive-count">0</span>
                    </button>

                    <a class="qs-tile" onclick="toggleSidebar(event)">
                        <i class="feather icon-bell"></i>
                        <span>Benachr.</span>
                        <span id="notificationBellBadge" class="badge badge-danger qs-badge" style="display:none;">0</span>
                    </a>


                    <a class="qs-tile nav-log-off" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="feather icon-power"></i>
                        <span>Logout</span>
                    </a>
                    </div>

                    <!-- Collapsible sections for employees -->
                    <div class="sa-collapse mt-3" id="sa-present-panel" hidden>
                        <div class="sa-card sa-card-body p-2">
                            <div class="sa-card-header m-0 p-2">
                            <h6 class="mb-1">ANWESENHEIT</h6>
                            <small class="sa-employee-status-count">
                                <span class="mr-2">anwesend: <span id="sa-active-emp">0</span></span>
                                <span class="mr-2">abwesend: <span id="sa-inactive-emp">0</span></span>
                                <span class="mr-2">krank: <span id="sa-sick-emp">0</span></span>
                                <span>Urlaub: <span id="sa-holiday-emp">0</span></span>
                            </small>
                            </div>

                            <fieldset class="mt-2">
                            <div class="sa-input-group">
                                <input type="text" class="sa-input sa-emp-search" placeholder="Name, Nachname" data-scope="present">
                                <button class="sa-btn sa-search-btn" type="button"><i class="feather icon-search"></i></button>
                            </div>
                            </fieldset>

                            <table class="sa-table sa-active-employee-list mb-0 mt-2"></table>
                        </div>
                        </div>

                        <!-- Absent panel -->
                        <div class="sa-collapse mt-2" id="sa-absent-panel" hidden>
                        <div class="sa-card sa-card-body p-2">
                            <div class="sa-card-header m-0 p-2" style="background:#ea5555; color:#fff;">
                            <h6 class="mb-0">ABWESENHEIT</h6>
                            </div>

                            <fieldset class="mt-2">
                            <div class="sa-input-group">
                                <input type="text" class="sa-input sa-emp-search" placeholder="Name, Nachname" data-scope="absent">
                                <button class="sa-btn sa-search-btn" type="button"><i class="feather icon-search"></i></button>
                            </div>
                            </fieldset>

                            <table class="sa-table sa-inactive-employee-list mb-0 mt-2"></table>
                        </div>
                        </div>

                    <!-- Hidden search overlay kept for compatibility -->
                        <div class="search-input-overlay d-none">
                            <div class="search-box-wrapper">
                                <!-- Top row: Input + buttons -->
                                <div class="d-flex align-items-start gap-2">
                                    <div class="input-group flex-grow-1">
                                        <input id="globalSearchInput"
                                            class="form-control"
                                            type="text"
                                            placeholder="Suche… (z. B. “Torsten Kunde, Lieferant”, “app kunde”)"
                                            autocomplete="off" />
                                        <div class="input-group-append">
                                            <button id="globalSearchGo" type="button" class="btn btn-primary">
                                                <i class="feather icon-search"></i>
                                            </button>
                                            <button type="button" class="btn btn-light close-search">
                                                <i class="feather icon-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button id="toggleSearchHelp" class="btn btn-outline-light ml-2">
                                        <i class="feather icon-help-circle"></i> Hilfe
                                    </button>
                                </div>

                                <!-- Quick hint row -->
                                <div class="search-hint mt-2">
                                    <strong>Hinweis:</strong>
                                    Verwenden Sie Filter per Komma:
                                    <code>Kunde</code>, <code>Lieferant</code>, <code>Hersteller</code>,
                                    <code>Mitarbeiter</code>, <code>Anfrage</code>.<br>
                                    Beispiele:
                                    <code>Torsten Kunde, Lieferant</code>,
                                    <code>Müller Mitarbeiter</code>.<br>
                                    <strong>App-Ansicht:</strong>
                                    Mit <code>app</code> öffnen Sie die App-Übersicht –
                                    z. B. <code>app kunde</code>, <code>app projekt</code> für gefilterte Bereiche.<br>
                                    <strong>Gelöschte Einträge:</strong>
                                    Grau/unscharf dargestellte Zeilen sind gelöscht. Über den Button
                                    <code>Wiederherstellen</code> können Sie sie direkt hier zurückholen.<br>
                                    Pfeile ↑/↓ zum Navigieren, <kbd>Enter</kbd> zum Öffnen, <kbd>Esc</kbd> schließen.
                                </div>
                                <div class="small text-muted">
                                    <span class="badge badge-light border mr-1"><i class="feather icon-command"></i> Ctrl + K</span> öffnen
                                    <span class="badge badge-light border"><i class="feather icon-x-square"></i> Esc</span> schließen
                                </div>

                                <!-- Collapsible help -->
                                <div id="searchHelpPanel" class="search-help mt-2" style="display:none;">
                                    <div class="card card-body p-2">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="mb-1">Tastatur</h6>
                                                <ul class="mb-2 small pl-3">
                                                    <li><kbd>↑</kbd>/<kbd>↓</kbd> – Einträge wählen</li>
                                                    <li><kbd>Enter</kbd> – Ausgewählten Eintrag öffnen</li>
                                                    <li><kbd>Esc</kbd> – Suche schließen</li>
                                                </ul>

                                                <h6 class="mb-1">Spezialbefehle</h6>
                                                <ul class="mb-2 small pl-3">
                                                    <li><code>app</code> – App-Übersicht mit allen Bereichen öffnen</li>
                                                    <li><code>app kunde</code> – Apps/Bereiche rund um Kunden anzeigen</li>
                                                    <li><code>app projekt</code> – Projekt-/Aufgabenbereiche anzeigen</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="mb-1">Filter</h6>
                                                <ul class="mb-2 small pl-3">
                                                    <li><code>… Kunde</code> – nur Kunden</li>
                                                    <li><code>… Lieferant</code> – nur Lieferanten</li>
                                                    <li><code>… Hersteller</code> – nur Hersteller</li>
                                                    <li><code>… Mitarbeiter</code> – nur Mitarbeiter</li>
                                                    <li><code>… Anfrage</code> – nur Anfragen</li>
                                                    <li>Mehrere per Komma: <code>… Kunde, Lieferant</code></li>
                                                </ul>

                                                <h6 class="mb-1">Gelöschte Einträge</h6>
                                                <ul class="mb-2 small pl-3">
                                                    <li>Gelöschte Datensätze werden grau/unscharf angezeigt und mit
                                                        <span class="badge badge-danger">Gelöscht</span> markiert.</li>
                                                    <li>Über den Button <code>Wiederherstellen</code> können Sie den Datensatz direkt
                                                        reaktivieren, ohne die Suche zu schließen.</li>
                                                    <li>Je nach Typ wird in der passenden Tabelle wiederhergestellt
                                                        (<code>Kunde</code> → <code>new_leads</code>,
                                                        <code>Hersteller/Merk</code> → <code>brands</code>,
                                                        <code>Lieferant</code> → <code>distributors</code>).</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Results -->
                                <ul class="search-results mt-2"></ul>
                            </div>
                        </div>


                    <!-- Clock panel kept for compatibility -->
                    <div class="clock mb-2" id="clock-section" style="display:none">
                    <div class="card-content">
                        <div class="card-body text-center d-flex p-0">
                        <div class="d-flex mr-2 ml-2">
                            <span class="start_container">
                            <i class="fa fa-play" id="start_clock"></i>
                            <i class="feather icon-pause blinking-icon" id="pause_clock" data-start-id="" style="display:none"></i>
                            <p class="font-medium-2">START</p>
                            </span>
                        </div>
                        <div class="col d-flex">
                            <div class="button">
                            <p class="font-weight-bold font-medium-2 mb-0" id="clock"></p>
                            <small id="employee_start_time"> -</small>
                            <small id="employee_end_time">00:00</small>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>

                </div><!-- /qs-content -->

                    <div class="qs-content"> 
                        <div class="qs-grid">
                            <!-- TICKET (default = Liste) -->
                            <div class="qs-has-sub">
                                <button type="button" class="qs-tile qs-toggle" aria-expanded="false" aria-controls="qs-sub-ticket"
                                    style="display: flex;
        
                                        display: flex;
                                        flex-direction: column;
                                        align-items: center;
                                        text-align: center;
                                        padding: 16px 36px;
                                        border-radius: 12px !important;
                                        background: #314b62;
                                        border: 1px solid #4d4f52;
                                        cursor: pointer;
                                        text-decoration: none !important;
                                        color: inherit;
                                        position: relative;
                                        transition: transform .12s ease, box-shadow .12s ease, background .12s ease;
                                        ">
                                    <i class="feather icon-alert-triangle"></i>
                                    <span>Ticket</span>
                                    <i class="feather icon-chevron-down qs-caret"></i>
                                </button>
                                <div id="qs-sub-ticket" class="qs-sub" hidden style="  position: absolute;   z-index: 2000;  ">
                                    <a class="qs-sub-item" href="{{ url('/error') }}">
                                    <i class="feather icon-alert-triangle mr-1"></i> Fehler &amp; Fehlerheft
                                    </a>
                                    <a class="qs-sub-item" href="{{ url('problem_create') }}">
                                    <i class="fa fa-ticket mr-1"></i> Anlegen
                                    </a>
                                    <a class="qs-sub-item" href="{{ url('problem_view') }}">
                                    <i class="fa fa-wrench mr-1"></i> Liste
                                    </a>
                                </div>
                            </div>
    
                            <!-- AUFGABEN -->
                            <a class="qs-tile" href="{{ url('admin/todo/personal?tab=my') }}">
                                <i class="feather icon-check-square"></i>
                                <span>Aufgaben</span>
                            </a>

                            <!-- TERMINE -->
                            <a class="qs-tile calendar_view_icon" href="{{ url('customer/appointments') }}">
                                <i class="feather icon-calendar"></i>
                                <span>Termine</span>
                            </a>

                            <!-- PROZESS (default = Übersicht) -->
                            <div class="qs-has-sub">
                                <button type="button" class="qs-tile qs-toggle" aria-expanded="false" aria-controls="qs-sub-prozess"
                                style="display: flex;
        
                                        display: flex;
                                        flex-direction: column;
                                        align-items: center;
                                        text-align: center;
                                        padding: 16px 36px;
                                        border-radius: 12px !important;
                                        background: #314b62;
                                        border: 1px solid #4d4f52;
                                        cursor: pointer;
                                        text-decoration: none !important;
                                        color: inherit;
                                        position: relative;
                                        transition: transform .12s ease, box-shadow .12s ease, background .12s ease;
                                        ">
                                    <i class="feather icon-sliders"></i>
                                    <span>Prozess</span>
                                    <i class="feather icon-chevron-down qs-caret"></i>
                                </button>
                                <div id="qs-sub-prozess" class="qs-sub" hidden  style="  position: absolute;   z-index: 2000;  ">
                                    <a class="qs-sub-item" href="{{ url('lead/overview') }}">
                                    <i class="feather icon-calendar mr-1"></i> Prozess
                                    </a>
                                    <a class="qs-sub-item" href="{{ url('lead/kanban') }}">
                                    <i class="feather icon-check-square mr-1"></i> Lead Übersicht
                                    </a>
                                </div>
                            </div>

                            <!-- KONTAKTE -->
                            <a class="qs-tile" href="{{ url('/all-contacts') }}">
                                <i class="feather icon-users"></i>
                                <span>Kontakte</span>
                            </a> 
                            <div class="qs-has-sub"
                                id="qs-department-wrapper"
                                data-url="{{ route('quick.departments') }}">
                                <button type="button"
                                        class="qs-tile qs-toggle"
                                        aria-expanded="false"
                                        aria-controls="qs-sub-department"
                                        style="display: flex;
                                            flex-direction: column;
                                            align-items: center;
                                            text-align: center;
                                            padding: 16px 36px;
                                            border-radius: 12px !important;
                                            background: #314b62;
                                            border: 1px solid #4d4f52;
                                            cursor: pointer;
                                            text-decoration: none !important;
                                            color: inherit;
                                            position: relative;
                                            transition: transform .12s ease, box-shadow .12s ease, background .12s ease;">
                                    <i class="feather icon-layers"></i>
                                    <span>Abteilung</span>
                                    <i class="feather icon-chevron-down qs-caret"></i>
                                </button>

                                <div id="qs-sub-department"
                                    class="qs-sub"
                                    hidden
                                    style="
                                        position: absolute;
                                        z-index: 2000;
                                        min-width: 280px;
                                        max-height: 350px;
                                        overflow-y: auto;
                                        background: transparent;
                                        border-radius: 8px;
                                    ">
                                    <div class="p-3 text-center text-muted small js-dept-loading">
                                        Abteilungen werden geladen ...
                                    </div>

                                    <div class="js-dept-error p-2 text-center text-danger small" style="display:none;"></div>

                                    <div class="js-dept-list"></div>
                                </div>
                            </div>

                            
                        </div>
    
                        <div class="settings">
                            
                        </div>
                </div><!-- /qs-content -->

            
            </aside>

            <div id="fb-right-sidebar"></div> 
            <div id="fb-bottom-chats-container"></div> 
             <!-- Keep your toast + logout form in the page -->
                <div id="notifToast" class="toast position-fixed bottom-0 right-0 m-3" data-delay="5000" style="z-index:9999;">
                <div class="toast-header">
                    <strong class="mr-auto">📢 Neue Benachrichtigung</strong>
                    <small>gerade eben</small>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">&times;</button>
                </div>
                <div class="toast-body" id="notifToastBody">...</div>
                </div>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
 
            <!-- Notification Backdrop -->
                <div id="notificationBackdrop" class="notif-backdrop" onclick="closeNotification()"></div>

                <!-- Notification Slider -->
                <aside id="notificationSidebar" class="notif-sider" aria-hidden="true" aria-label="Benachrichtigungen">
                    <header class="notif-header">
                        <div class="d-flex align-items-center">
                        <i class="feather icon-bell mr-2"></i>
                        <h5 class="mb-0">Benachrichtigungen</h5>
                        <span id="notifUnreadBadge" class="ml-2 badge badge-danger d-none"></span>
                        </div>
                        <div class="d-flex align-items-center">
                        <button id="markAllReadBtn" class="btn btn-sm btn-outline-secondary mr-2">Alle gelesen</button>
                        <button class="btn btn-sm btn-dark" onclick="closeNotification()" aria-label="Schließen">
                            <i class="feather icon-x"></i>
                        </button>
                        </div>
                    </header>

                    <div class="notif-controls">
                        <div class="input-group">
                        <input id="notifSearch" type="text" class="form-control" placeholder="Suchen…">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="feather icon-search"></i></span>
                        </div>
                        </div>

                        <div id="notifFilters" class="notif-chips mt-2">
                        <!-- Chips are clickable filters -->
                        <button data-type="all" class="chip active">Alle</button>
                        <button data-type="unread" class="chip">Ungelesen</button>
                        <button data-type="lead" class="chip">Lead</button>
                        <button data-type="inquiry" class="chip">Anfrage</button>
                        <button data-type="appointment" class="chip">Termin</button>
                        <button data-type="task" class="chip">Aufgabe</button>
                        <button data-type="ticket" class="chip">Ticket</button>
                        <button data-type="offer" class="chip">Angebot</button>
                        <button data-type="employee" class="chip">Mitarb.</button>
                        <button data-type="other" class="chip">Sonstiges</button>
                        </div>
                    </div>

                    <section id="notifList" class="notif-list" aria-live="polite" aria-busy="false">
                        <!-- Cards inserted here -->
                    </section>

                    <footer class="notif-footer">
                        <button id="loadMoreBtn" class="btn btn-light btn-block">Mehr laden</button>
                    </footer>
                </aside>

 
               @yield('content')

            

                <div id="chat-messages-container" class="d-none"></div>


                <div id="chatToast" class="toast position-fixed top-3 right-0 m-3" data-delay="6000" style="z-index: 9999;">
                    <div class="toast-header bg-primary">
                        <img src="{{asset('images/gender/users.png')}}" id="chatToastImage" class="rounded mr-2" width="30" height="30" />
                        <strong class="mr-auto" id="chatToastSender">Neue Nachricht</strong>
                        <small class="text-muted"></small>
                        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">&times;</button>
                    </div>
                    <div class="toast-body" id="chatToastBody">
                        ...
                    </div>
                </div> 

                <a href="#" data-open="notifications"> 
                    <span id="notificationBellBadge" class="badge badge-danger d-none"></span>
                </a> 
 
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
        
                @if(auth()->check() && (request()->is('/') || request()->is('home')))
                    @include('admin.layouts.partial.due-today-modal')

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const modal     = document.getElementById('dueTodayModal');
                            const progress  = document.getElementById('dueProgressBar');
                            const container = document.getElementById('dueTodayList');

                            function openDueModal() {
                                if (!modal) return;
                                modal.classList.add('is-open');
                                modal.setAttribute('aria-hidden', 'false');
                                document.documentElement.style.overflow = 'hidden';
                            }

                            function closeDueModal() {
                                if (!modal) return;
                                modal.classList.remove('is-open');
                                modal.setAttribute('aria-hidden', 'true');
                                document.documentElement.style.overflow = '';
                            }

                            // Close triggers: backdrop, close button, ESC
                            modal?.addEventListener('click', function (e) {
                                    const closeTrigger = e.target.closest('[data-due-close]');
                                    if (closeTrigger) {
                                        e.preventDefault();
                                        closeDueModal();
                                    }
                                });

                            document.addEventListener('keydown', function (e) {
                                if (e.key === 'Escape' && modal?.classList.contains('is-open')) {
                                    closeDueModal();
                                }
                            });

                            modal?.addEventListener('click', function (e) {
                                const closeTrigger = e.target.closest('[data-due-close]');
                                if (closeTrigger) {
                                    e.preventDefault();
                                    closeDueModal();
                                }
                            });
                            checkAndShowDueToday();
                            setInterval(checkAndShowDueToday, 5 * 60 * 1000); // every 5 minutes

                            function checkAndShowDueToday() {
                                const lastShown = localStorage.getItem('dueTodayLastShown');
                                const now       = Date.now();

                                // Only show if more than 30 minutes passed
                                if (lastShown && now - parseInt(lastShown, 10) < 30 * 60 * 1000) {
                                    return;
                                }

                                fetch('/my/due-today')
                                    .then(res => res.json())
                                    .then(data => {
                                        if (!data || !data.items || data.items.length === 0) return;

                                        const today = new Date();
                                        const overdueItems = data.items.filter(item => {
                                            if (!item.due_date) return false;
                                            const dueDate  = new Date(item.due_date);
                                            const diffDays = Math.floor((today - dueDate) / (1000 * 60 * 60 * 24));
                                            return diffDays > 3;
                                        });

                                        if (overdueItems.length > 0 || (data.percent || 0) > 0) {
                                            renderDueTodayModal(data);
                                            openDueModal();
                                            localStorage.setItem('dueTodayLastShown', Date.now().toString());
                                        }
                                    })
                                    .catch(err => {
                                        console.error('Failed to load due-today:', err);
                                    });
                            }

                            function renderDueTodayModal(data) {
                                if (!container || !progress) return;

                                const today   = new Date();
                                const percent = Math.round(data.percent || 0);

                                // Progress bar
                                progress.innerHTML = `
                                    <div class="due-progress">
                                        <div class="due-progress-track">
                                            <div class="due-progress-bar" style="width: ${percent}%;"></div>
                                        </div>
                                        <span class="due-progress-label">
                                            ${percent}% deiner heutigen Aufgaben sind erledigt.
                                        </span>
                                    </div>
                                `;

                                container.innerHTML = '';

                                data.items.forEach(item => {
                                    const dueDate   = item.due_date ? new Date(item.due_date) : null;
                                    const diffDays  = dueDate ? Math.floor((today - dueDate) / (1000 * 60 * 60 * 24)) : 0;
                                    const isOld     = dueDate && diffDays > 3;
                                    const safeTitle = item.title || 'Ohne Titel';

                                    let typeClass = 'primary';
                                    switch (item.type) {
                                        case 'personal_task': typeClass = 'info'; break;
                                        case 'appointment':   typeClass = 'warning'; break;
                                        case 'problem':       typeClass = 'danger'; break;
                                        case 'ticket_task':   typeClass = 'secondary'; break;
                                    }

                                    const borderColor = {
                                        info:      '#79b2d4',
                                        warning:   '#f97316',
                                        danger:    '#ef4444',
                                        secondary: '#6b7280',
                                        primary:   '#95c120'
                                    }[typeClass];

                                    const pillClass = {
                                        info:      'due-pill-info',
                                        warning:   'due-pill-warning',
                                        danger:    'due-pill-danger',
                                        secondary: 'due-pill-secondary',
                                        primary:   'due-pill-primary'
                                    }[typeClass];

                                    const wrapper = document.createElement('article');
                                    wrapper.className = 'due-card';
                                    wrapper.innerHTML = `
                                        <div class="due-card-border" style="background: ${borderColor};"></div>
                                        <div class="due-card-content">
                                            <h6 class="due-card-title" title="${safeTitle.replace(/"/g, '&quot;')}">
                                                ${safeTitle}
                                            </h6>
                                            <div class="due-card-meta">
                                                <span class="due-pill ${pillClass}">
                                                    ${item.label || 'Task'}
                                                </span>
                                                ${isOld ? `
                                                    <span class="due-pill due-pill-overdue">
                                                        ⚠️ Überfällig (${diffDays} Tage)
                                                    </span>
                                                ` : ''}
                                            </div>
                                            ${item.description ? `
                                                <p class="due-card-text">${item.description}</p>
                                            ` : ''}
                                            ${item.due_date ? `
                                                <div class="due-card-date">
                                                    Fällig: ${new Date(item.due_date).toLocaleDateString()}
                                                </div>
                                            ` : ''}
                                        </div>
                                    `;

                                    container.appendChild(wrapper);
                                });
                            }
                        });
                    </script>
                @endif
 
    
    <!-- END: Content-->
        <button
            type="button"
            class="mobile-chat-toggle btn btn-primary shadow-lg"
            onclick="toggleChatSidebar()"
            style="
                z-index:2;
                position:fixed;
                right:16px;
                bottom:16px;

                border-radius:999px;
                padding:.75rem 1.1rem;

                display:flex;
                align-items:center;
                justify-content:center;
                gap:.6rem;

                min-width:140px;            /* wider button */
                height:52px;                /* consistent height */ 
            "
            >
            <i class="feather icon-message-circle" style="font-size:20px; line-height:1;"></i>
            <span style="font-weight:800; font-size:1rem; line-height:1;">Chat</span>

            <!-- badge: visually clean, inline (not floating) -->
            <span
                id="mobile-chat-badge"
                class="badge badge-light"
                style="
                display:none;
                margin-left:.15rem;

                background:rgba(255,255,255,.92);
                color:#0f172a;

                border:1px solid rgba(255,255,255,.65);
                border-radius:999px;

                min-width:26px;
                height:22px;
                padding:0 8px;

                font-weight:900;
                font-size:12px;
                line-height:22px;
                text-align:center;
                "
            >0</span>
            </button>



    <div id="fb-mobile-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5);" onclick="toggleChatSidebar()"></div>

   
    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    @include('admin.layouts.partial.system-warning-modal')



    <div id="saReportModalBackdrop" class="sa-report-modal-backdrop">
        <div class="sa-report-modal">
            <div class="sa-report-modal-head">
                <div>
                    <h5 id="saReportModalTitle">Bericht</h5>
                    <small id="saReportModalSub">Details</small>
                </div>

                <button type="button" class="sa-report-modal-close" onclick="closeSaReportModal()">
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <div class="sa-report-modal-body">
                <div class="sa-report-modal-grid">
                    <div class="sa-report-modal-box">
                        <div class="sa-report-modal-label">Gemeldet von</div>
                        <div class="sa-report-modal-value" id="saReportModalEmployee">—</div>
                    </div>

                    <div class="sa-report-modal-box">
                        <div class="sa-report-modal-label">Typ</div>
                        <div class="sa-report-modal-value" id="saReportModalType">—</div>
                    </div>

                    <div class="sa-report-modal-box">
                        <div class="sa-report-modal-label">Objekt</div>
                        <div class="sa-report-modal-value" id="saReportModalTarget">—</div>
                    </div>

                    <div class="sa-report-modal-box">
                        <div class="sa-report-modal-label">Zeitpunkt</div>
                        <div class="sa-report-modal-value" id="saReportModalTime">—</div>
                    </div>
                </div>

                <div class="sa-report-modal-label">Bericht</div>
                <div class="sa-report-full-text" id="saReportModalReport">—</div>
            </div>

            <div class="sa-report-modal-foot">
                <button type="button" onclick="closeSaReportModal()">Schließen</button>
            </div>
        </div>
    </div>

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
    <!-- <script src="{{ asset('js/clock.js')}}"></script>  -->
 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

      

    <script>window.userId = {{ auth()->id() }};</script>
    <audio id="notificationSound" src="{{ asset('notification/notification.mp3') }}" preload="auto"></audio>
    <audio id="chatNotificationSound" src="{{ asset('notification/notification.mp3') }}" preload="auto"></audio>
    <audio id="activityNotificationSound" src="{{ asset('notification/short.mp3') }}" preload="auto"></audio>
    <audio id="appointmentReminderSound" src="{{ asset('notification/short.mp3') }}" preload="auto"></audio>
    <script>
        const empImage = "{{ asset('images/employee/') }}"
    </script>

    <script>
        window.userId = {{ auth()->user()->id }};
          window.userName = @json(optional(auth()->user())->name);
        window.userImage = @json(optional(auth()->user())->avatar_url);

        window.addEventListener('DOMContentLoaded', function () {
            initializeChatNotifications(window.userId);
        });
    </script> 
    <!-- Running the lead function  -->
    <script>
        function syncFusionEntries() {
            $.ajax({
                url: '/fusion/webhook/ajax',
                method: 'POST',
                headers: {
                    'X-Fusion-Token': '{{ config('services.fusion_forms.token') }}'
                },
                success: function (res) {
                    console.log(`✅ Synced ${res.count} entries`);
                },
                error: function (err) {
                    console.error('❌ Sync failed', err.responseJSON || err);
                }
            });
        }

        // Run immediately
        syncFusionEntries();

        // Repeat every 2 minutes
        setInterval(syncFusionEntries, 120000); // 2 minutes
    </script>  
    <!-- Globale Search :start  --> 
    <script>
            $(document).ready(function () {
            // Show/hide help
            $('#toggleSearchHelp').on('click', function(){
                $('#searchHelpPanel').slideToggle(150);
            });

            // Click the search button → reuse your keyup handler
            $('#globalSearchGo').on('click', function(){
                $('#globalSearchInput').trigger('keyup');
            });

            // Prevent form submit on Enter in the input (your list handles Enter)
            $('#globalSearchInput').on('keypress', function(e){
                if (e.key === 'Enter') e.preventDefault();
            });
            });
    </script>
    <script>
    (function ($) {
        'use strict';

        const SEARCH_URL = "{{ route('contacts.global.search') }}";
        const CSRF_TOKEN = "{{ csrf_token() }}";
        const DEFAULT_AVATAR = "{{ asset('images/gender/male.png') }}";

        const ROUTES = {
            restoreCustomer: "{{ route('global.restore.customer') }}",
            restoreBrand: "{{ route('global.restore.brand') }}",
            restoreDistributor: "{{ route('global.restore.distributor') }}",
            createInquiry: "{{ route('inquiry.create') }}",
            createCustomer: "{{ route('new.lead.create') }}",
            createDistributor: "{{ route('distributors.index') }}",
            createBrand: "{{ route('brand.index') }}"
        };

        const TYPE_BADGE_COLORS = {
            'Kunde': 'info',
            'Hersteller': 'primary',
            'Lieferant': 'warning',
            'Anfrage': 'secondary',
            'Mitarbeiter': 'success',
            'Aufgabe': 'danger',
            'Termin': 'primary',
            'Ticket': 'primary'
        };

        const TYPE_RESTORE_ROUTES = {
            'Kunde': ROUTES.restoreCustomer,
            'Hersteller': ROUTES.restoreBrand,
            'Merk': ROUTES.restoreBrand,
            'Lieferant': ROUTES.restoreDistributor
        };

        let appEntries = [];
        let selectedIndex = -1;
        let searchRequest = null;

        const state = {
            $input: null,
            $overlay: null,
            $results: null,
            $helpPanel: null
        };

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function safeText(value, fallback = '—') {
            const text = String(value ?? '').trim();
            return text !== '' ? escapeHtml(text) : fallback;
        }

        function getBadgeColor(type) {
            return TYPE_BADGE_COLORS[type] || 'light';
        }

        function getRestoreEndpoint(type) {
            return TYPE_RESTORE_ROUTES[type] || null;
        }

        function refreshFeather() {
            if (typeof feather !== 'undefined' && feather && typeof feather.replace === 'function') {
                feather.replace();
            }
        }

        function resetSelection() {
            selectedIndex = -1;
            state.$results.find('li[data-url]').removeClass('bg-primary text-white');
        }

        function highlightSelection() {
            const $items = state.$results.find('li[data-url]');
            $items.removeClass('bg-primary text-white');

            if (selectedIndex >= 0 && selectedIndex < $items.length) {
                $items.eq(selectedIndex).addClass('bg-primary text-white');
            }
        }

        function buildAppEntries() {
            appEntries = [];

            $('.sidebar-content > ul > li.nav-has-submenu').each(function () {
                const $section = $(this);

                let sectionTitle = $.trim(
                    $section
                        .children('a')
                        .clone()
                        .children('i, .feather, .fa, .badge')
                        .remove()
                        .end()
                        .text()
                );

                if (!sectionTitle) {
                    sectionTitle = 'Allgemein';
                }

                $section.find('ul.nav-submenu a').each(function () {
                    const $link = $(this);
                    const href = $link.attr('href');

                    if (!href || href === 'javascript:void(0);' || href === '#') {
                        return;
                    }

                    const label = $.trim(
                        $link
                            .clone()
                            .children('i, .feather, .fa, .badge')
                            .remove()
                            .end()
                            .text()
                    );

                    appEntries.push({
                        section: sectionTitle,
                        label: label || href,
                        url: href
                    });
                });
            });
        }

        function groupBy(items, key) {
            return items.reduce(function (groups, item) {
                const groupKey = item[key] || 'Sonstiges';
                if (!groups[groupKey]) {
                    groups[groupKey] = [];
                }
                groups[groupKey].push(item);
                return groups;
            }, {});
        }

        function renderEmptyApps(message) {
            state.$results.html(`
                <li class="p-3 text-muted">
                    ${escapeHtml(message)}
                </li>
            `);
            refreshFeather();
        }

        function renderAppLauncher(filterTerm = '') {
            state.$results.empty();

            if (!appEntries.length) {
                renderEmptyApps('Keine Apps gefunden.');
                return;
            }

            const normalized = String(filterTerm || '').trim().toLowerCase();

            const filtered = !normalized
                ? appEntries
                : appEntries.filter(function (entry) {
                    return entry.label.toLowerCase().includes(normalized) ||
                        entry.section.toLowerCase().includes(normalized);
                });

            if (!filtered.length) {
                renderEmptyApps(`Keine passenden Bereiche für "${filterTerm}" gefunden.`);
                return;
            }

            const grouped = groupBy(filtered, 'section');

            let html = `
                <li class="p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong class="text-white">Bereiche &amp; Apps</strong><br>
                            <small class="text-muted">
                                Tippe <code>app</code> oder <code>app &lt;Name&gt;</code>, z. B. <code>app kunde</code>.
                            </small>
                        </div>
                    </div>
                </li>
            `;

            Object.keys(grouped).forEach(function (section) {
                html += `
                    <li class="search-group-header text-uppercase text-muted font-weight-bold py-1">
                        ${escapeHtml(section)}
                    </li>
                `;

                grouped[section].forEach(function (entry) {
                    html += `
                        <li class="app-launcher-item" data-url="${escapeHtml(entry.url)}">
                            <div class="search-result-card p-1">
                                <button type="button" class="btn btn-outline-light btn-block text-left">
                                    <div class="d-flex flex-column">
                                        <span class="font-weight-bold text-white">${escapeHtml(entry.label)}</span>
                                        <small class="text-muted">${escapeHtml(section)}</small>
                                    </div>
                                </button>
                            </div>
                        </li>
                    `;
                });
            });

            state.$results.html(html);
            refreshFeather();
        }

        function renderNoResults() {
            state.$results.html(`
                <li class="p-3 text-center">
                    <div class="mb-2 text-muted">
                        <i class="feather icon-alert-circle mr-50"></i> Kein Ergebnis gefunden.
                    </div>
                    <hr class="my-2">
                    <strong class="d-block text-muted mb-2 text-left small">NEU ANLEGEN:</strong>
                    <div class="row">
                        <div class="col-6 mb-2">
                            <a href="${ROUTES.createInquiry}" class="btn btn-outline-secondary btn-sm btn-block">
                                <i class="feather icon-file-plus"></i> Anfrage
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="${ROUTES.createCustomer}" class="btn btn-outline-info btn-sm btn-block">
                                <i class="feather icon-user-plus"></i> Kunde
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="${ROUTES.createDistributor}" class="btn btn-outline-warning btn-sm btn-block">
                                <i class="feather icon-truck"></i> Lieferant
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="${ROUTES.createBrand}" class="btn btn-outline-primary btn-sm btn-block">
                                <i class="feather icon-briefcase"></i> Hersteller
                            </a>
                        </div>
                    </div>
                </li>
            `);

            refreshFeather();
        }

        function normalizeIconName(iconClass) {
            const parts = String(iconClass || '').trim().split(/\s+/);
            return parts[1] || 'user';
        }

        function normalizePriorityLabel(priority) {
            const value = String(priority || '').trim().toLowerCase();

            switch (value) {
                case 'very high':
                case 'sehr hoch':
                    return 'Sehr hoch';
                case 'high':
                case 'hoch':
                    return 'Hoch';
                case 'normal':
                    return 'Normal';
                default:
                    return priority ? String(priority).trim() : '';
            }
        }

        function buildParticipantsHtml(participants) {
            if (!Array.isArray(participants) || !participants.length) {
                return '';
            }

            return `
                <div class="mt-50 d-flex align-items-center flex-wrap">
                    ${participants.map(function (participant) {
                        const avatar = participant?.avatar ? escapeHtml(participant.avatar) : DEFAULT_AVATAR;
                        const name = safeText(participant?.name, '');
                        return `
                            <div class="mr-25" title="${name}">
                                <img
                                    src="${avatar}"
                                    alt="${name}"
                                    class="rounded-circle border border-white"
                                    width="28"
                                    height="28"
                                >
                            </div>
                        `;
                    }).join('')}
                </div>
            `;
        }

        function buildMetaLine(icon, text) {
            if (!text) {
                return '';
            }

            return `
                <br>
                <small class="text-muted">
                    <i class="feather icon-${icon} white"></i> ${escapeHtml(text)}
                </small>
            `;
        }

        function buildContactItem(contact, type) {
            const displayName = `${contact?.name ?? ''} ${contact?.lastname ?? ''}`.trim() || `#${contact?.id ?? ''}`;
            const url = contact?.url || '#';
            const avatar = contact?.avatar || DEFAULT_AVATAR;
            const iconName = normalizeIconName(contact?.icon || '');
            const phone = safeText(contact?.phone);
            const email = safeText(contact?.email);
            const address = safeText(contact?.address);
            const assignedBy = contact?.assigned_by_name ? String(contact.assigned_by_name).trim() : '';
            const assignedEmployees = contact?.assigned_employees ? String(contact.assigned_employees).trim() : '';
            const priorityLabel = normalizePriorityLabel(contact?.priority);
            const startDate = contact?.start_date_label ? String(contact.start_date_label).trim() : '';
            const endDate = contact?.end_date_label ? String(contact.end_date_label).trim() : '';
            const dateText = startDate || endDate ? `${startDate}${endDate ? ' → ' + endDate : ''}` : '';
            const participants = Array.isArray(contact?.participants) ? contact.participants : [];
            const isDeleted = contact?.deleted_at !== null && contact?.deleted_at !== undefined;
            const isVerified = contact?.verified_label === 'Verifiziert';

            return `
                <li
                    class="${isDeleted ? 'search-result-deleted' : ''}"
                    data-url="${escapeHtml(url)}"
                    data-type="${escapeHtml(type)}"
                    data-id="${escapeHtml(contact?.id ?? '')}"
                    data-deleted="${isDeleted ? '1' : '0'}"
                >
                    <div class="search-result-card p-1">
                        <div class="d-flex align-items-center">
                            <img
                                src="${escapeHtml(avatar)}"
                                alt="avatar"
                                class="rounded-circle mr-1"
                                width="40"
                                height="40"
                            >
                            <div class="ml-1">
                                <p class="mb-0 font-weight-bold d-flex align-items-center" >
                                    <i data-feather="${escapeHtml(iconName)}" class="mr-50 text-primary"></i>
                                    ${escapeHtml(displayName)}
                                    <span class="badge badge-pill badge-${getBadgeColor(type)} ml-1">${escapeHtml(type)}</span>
                                    ${isVerified ? '<span class="badge badge-pill badge-success ml-1">Verifiziert</span>' : ''}
                                    ${isDeleted ? '<span class="badge badge-pill badge-danger ml-1">Gelöscht</span>' : ''}
                                    ${isDeleted ? `
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-light ml-1 btn-restore-item"
                                            data-id="${escapeHtml(contact?.id ?? '')}"
                                            data-type="${escapeHtml(type)}"
                                        >
                                            Wiederherstellen
                                        </button>
                                    ` : ''}
                                </p>

                                <small class="text-muted">
                                    <i class="feather icon-phone"></i> ${phone} |
                                    <i class="feather icon-mail"></i> ${email}
                                </small>

                                ${buildMetaLine('map-pin', address !== '—' ? address : '')}
                                ${buildMetaLine('user', assignedBy ? `Von: ${assignedBy}` : '')}
                                ${buildMetaLine('calendar', dateText)}
                                ${buildMetaLine('bell', priorityLabel)}
                                ${buildMetaLine('users', assignedEmployees)}
                                ${buildParticipantsHtml(participants)}
                            </div>
                        </div>
                    </div>
                </li>
            `;
        }

        function renderSearchResults(data) {
            state.$results.empty();

            if (!Array.isArray(data) || !data.length) {
                renderNoResults();
                return;
            }

            const grouped = groupBy(data, 'type');
            let html = '';

            Object.entries(grouped).forEach(function ([type, items]) {
                html += `
                    <li class="search-group-header text-uppercase text-muted font-weight-bold py-1">
                        ${escapeHtml(type)}
                    </li>
                `;

                items.forEach(function (item) {
                    html += buildContactItem(item, type);
                });
            });

            state.$results.html(html);
            refreshFeather();
        }

        function performSearch(query) {
            if (searchRequest && typeof searchRequest.abort === 'function') {
                searchRequest.abort();
            }

            searchRequest = $.ajax({
                url: SEARCH_URL,
                method: 'GET',
                data: { q: query }
            })
            .done(function (data) {
                renderSearchResults(data);
            })
            .fail(function (xhr, status) {
                if (status === 'abort') {
                    return;
                }

                state.$results.html(`
                    <li class="p-3 text-danger">
                        Suche konnte nicht geladen werden.
                    </li>
                `);
            })
            .always(function () {
                searchRequest = null;
                resetSelection();
            });
        }

        function openOverlay() {
            state.$overlay.fadeIn().removeClass('d-none');
            state.$input.val('').focus();
            state.$results.empty();
            resetSelection();
        }

        function closeOverlay() {
            state.$overlay.fadeOut();
            resetSelection();
        }

        function handleKeyboardNavigation(e) {
            const $items = state.$results.find('li[data-url]');

            if (!$items.length) {
                return;
            }

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndex = (selectedIndex + 1) % $items.length;
                highlightSelection();
                return;
            }

            if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndex = (selectedIndex - 1 + $items.length) % $items.length;
                highlightSelection();
                return;
            }

            if (e.key === 'Enter') {
                e.preventDefault();

                const $selected = selectedIndex >= 0 ? $items.eq(selectedIndex) : $items.first();
                const url = $selected.data('url');

                if (url) {
                    window.location.href = url;
                }
            }
        }

        function handleInputSearch(e) {
            if (['ArrowDown', 'ArrowUp', 'Enter'].includes(e.key)) {
                return;
            }

            const query = String(state.$input.val() || '');
            const normalized = query.trim().toLowerCase();

            resetSelection();

            if (normalized === 'app' || normalized.startsWith('app ')) {
                const filterTerm = normalized.length > 3 ? normalized.slice(3).trim() : '';
                renderAppLauncher(filterTerm);
                return;
            }

            if (query.trim().length < 2) {
                state.$results.empty();
                return;
            }

            performSearch(query);
        }

        function restoreItem($button) {
            const id = $button.data('id');
            const type = $button.data('type');
            const url = getRestoreEndpoint(type);

            if (!url) {
                alert('Kein Restore-Endpunkt für diesen Typ: ' + type);
                return;
            }

            $button.prop('disabled', true).text('Wird wiederhergestellt…');

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    id: id,
                    _token: CSRF_TOKEN
                }
            })
            .done(function () {
                const $item = $button.closest('li[data-url]');
                $item.attr('data-deleted', '0').removeClass('search-result-deleted');
                $item.find('.badge-danger').remove();
                $button.remove();
            })
            .fail(function () {
                alert('Konnte Eintrag nicht wiederherstellen.');
                $button.prop('disabled', false).text('Wiederherstellen');
            });
        }

        function bindEvents() {
            $('.nav-link-search').on('click', function () {
                openOverlay();
            });

            $('.close-search').on('click', function () {
                closeOverlay();
            });

            $('#toggleSearchHelp').on('click', function () {
                state.$helpPanel.slideToggle(150);
            });

            state.$input.on('keydown', handleKeyboardNavigation);
            state.$input.on('keyup', handleInputSearch);

            $(document).on('click', '.search-results li[data-url]', function () {
                const url = $(this).data('url');
                if (url) {
                    window.location.href = url;
                }
            });

            $(document).on('click', '.app-launcher-item', function (e) {
                e.stopPropagation();
                const url = $(this).data('url');
                if (url) {
                    window.location.href = url;
                }
            });

            $(document).on('click', '.btn-restore-item', function (e) {
                e.stopPropagation();
                restoreItem($(this));
            });

            $(document).on('keydown', function (e) {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                    e.preventDefault();

                    if (state.$overlay.is(':hidden')) {
                        openOverlay();
                    } else {
                        state.$input.focus();
                    }

                    return;
                }

                if (e.key === 'Escape' && state.$overlay.is(':visible')) {
                    e.preventDefault();
                    closeOverlay();
                }
            });
        }

        $(document).ready(function () {
            state.$input = $('#globalSearchInput');
            state.$overlay = $('.search-input-overlay');
            state.$results = $('.search-results');
            state.$helpPanel = $('#searchHelpPanel');

            buildAppEntries();
            bindEvents();
        });

    })(jQuery);
    </script> 
    <!-- Globale Search :end  -->
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

    
<script>
    document.querySelector('.calendar_view_icon').addEventListener('click', function() {
        window.location.href = '/tasks/calendar/personal';
    });

      document.querySelector('.message_view_icon').addEventListener('click', function() {
        window.location.href = '/admin/chat';
    });
    document.querySelector('.watch_view_icon').addEventListener('click', function() {
        window.location.href = '/capacity/index';
    });

    document.querySelector('.map_view_icon').addEventListener('click', function() {
        window.location.href = '/lead/reference';
    });

    document.querySelector('.dashboard_view_icon').addEventListener('click', function() {
        window.location.href = '/';
    });
</script>
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
    const pinButton  = document.getElementById("pin-button");
    const sidebar    = document.getElementById("sidebar");
    const layout     = document.querySelector(".vertical-layout");

    // IMPORTANT: fallback to your page container if .app-content is missing
    const appContent =
        document.querySelector(".app-content") ||
        document.getElementById("appContent") ||
        document.getElementById("app-container");

    const submenuItems = document.querySelectorAll(".nav-has-submenu > a");
    const navbar = document.querySelector(".header-navbar.floating-nav");

    // If core layout pieces are missing, don't run this script on this page
    if (!sidebar || !layout) return;

    let isPinned = false;

    function setContentMargin(px) {
        if (appContent) appContent.style.marginLeft = px;
    }

    function updateNavbarWidth() {
        if (!navbar) return;
        if (layout.classList.contains("menu-expanded")) {
        navbar.style.width = "calc(100vw - (100vw - 100%) - calc(2.2rem * 2) - 260px)";
        } else {
        navbar.style.width = "calc(100vw - (100vw - 100%) - calc(2.2rem * 2) - 10px)";
        }
    }

    // Toggle Sidebar on Menu Button Click
    if (menuButton) {
        menuButton.addEventListener("click", function () {
        if (!isPinned) {
            sidebar.classList.toggle("open");
            layout.classList.toggle("menu-expanded");
            layout.classList.toggle("menu-collapsed");
            updateNavbarWidth();
        }
        });
    }

    // Toggle Sidebar Pinning
    if (pinButton) {
        pinButton.addEventListener("click", function () {
        isPinned = !isPinned;

        if (isPinned) {
            layout.classList.add("menu-expanded");
            layout.classList.remove("menu-collapsed");
            sidebar.classList.add("open");
            setContentMargin("260px");
        } else {
            layout.classList.remove("menu-expanded");
            layout.classList.add("menu-collapsed");
            sidebar.classList.remove("open");
            setContentMargin("10px");
        }
        updateNavbarWidth();
        });
    }

    // Collapse Sidebar when Clicking Outside (If Not Pinned)
    document.addEventListener("click", function (event) {
        if (
        !isPinned &&
        !sidebar.contains(event.target) &&
        (!menuButton || !menuButton.contains(event.target)) &&
        (!pinButton  || !pinButton.contains(event.target))
        ) {
        layout.classList.remove("menu-expanded");
        layout.classList.add("menu-collapsed");
        sidebar.classList.remove("open");
        setContentMargin("10px");
        updateNavbarWidth();
        }
    });

    // Accordion-style Submenu Toggle
    submenuItems.forEach(item => {
        item.addEventListener("click", function (event) {
        event.preventDefault();
        const parent = this.closest("li.nav-has-submenu");
        const submenu = parent ? parent.querySelector(":scope > .nav-submenu") : null;
        if (!parent) return;

        const siblings = Array.from(parent.parentElement?.children || []).filter(el =>
            el !== parent && el.classList.contains("nav-has-submenu")
        );

        siblings.forEach(sib => {
            sib.classList.remove("active");
            const sibSub = sib.querySelector(":scope > .nav-submenu");
            if (sibSub) sibSub.style.display = "none";
        });

        if (submenu && submenu.style.display === "block") {
            submenu.style.display = "none";
            parent.classList.remove("active");
        } else if (submenu) {
            submenu.style.display = "block";
            parent.classList.add("active");
        }
        });
    });

    function checkScreenSize() {
        if (window.innerWidth < 768) {
        isPinned = false;
        sidebar.classList.remove("open");
        layout.classList.remove("menu-expanded");
        layout.classList.add("menu-collapsed");
        setContentMargin("5px");
        updateNavbarWidth();
        }
    }

    window.addEventListener("resize", checkScreenSize);
    checkScreenSize();
    updateNavbarWidth();
    });
</script> 
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
    $(document).ready(function () {
  function fetchEmployees(query = '') {
    // Active
    $.ajax({
      url: "{{ route('get.employees.leave.status') }}",
      method: 'GET',
      data: { search: query },
      dataType: 'json',
      success: function (data) {
        renderEmployeeList(data, ".sa-active-employee-list");
        $(".sa-active-count").text(data.length);
        $("#sa-active-emp").text(data.length);
        countEmployeeStatus(data);
      },
      error: function (xhr) {
        console.error("❌ Error fetching active employees:", xhr.responseText);
      }
    });

    // Inactive
    $.ajax({
      url: "{{ route('get.employees.leave.status.inactive') }}",
      method: 'GET',
      data: { search: query },
      dataType: 'json',
      success: function (data) {
        renderEmployeeList(data, ".sa-inactive-employee-list");
        $(".sa-inactive-count").text(data.length);
        $("#sa-inactive-emp").text(data.length);
      },
      error: function (xhr) {
        console.error("❌ Error fetching inactive employees:", xhr.responseText);
      }
    });
  }

  function countEmployeeStatus(employees) {
    let sickCount = 0, holidayCount = 0;
    employees.forEach(employee => {
      if (employee.status === 'Sick') sickCount++;
      if (employee.status === 'Holiday') holidayCount++;
    });
    $("#sa-sick-emp").text(sickCount);
    $("#sa-holiday-emp").text(holidayCount);
  }

  function renderEmployeeList(employees, listSelector) {
    let html = '';
    employees.forEach(employee => {
      let imagePath = employee.image 
        ? `{{ asset('images/employee/') }}/${employee.image}`
        : `{{ asset('images/gender/male.png') }}`;

      let statusClass = getStatusClass(employee.status);
      let leaveButton = getActionButton(employee.id, "leave");
      let sickButton  = getActionButton(employee.id, "sick");

      html += `
        <tr>
          <td class="pt-0 pb-0">
            <div class="avatar mr-1">
              <img src="${imagePath}" alt="${employee.name} ${employee.lastname}" width="32" height="32">
              <span class="${statusClass}"></span>
            </div>
          </td>
          <td class="pt-0 pb-0">
            <a href="{{ url('employee_profile/') }}/${employee.id}" class="view_profile">
              <span style="font-weight:bold;color:#505050;">${employee.name} ${employee.lastname}</span>
            </a>
            <p>${employee.status_msg ?? ''}</p>
            <div class="d-flex">
              ${leaveButton}
              ${sickButton}
            </div>
          </td>
        </tr>`;
    });

    $(listSelector).html(html);
  }

  function getStatusClass(status) {
    switch (status) {
      case 'Active':  return 'avatar-status-online';
      case 'Holiday':
      case 'Sick':    return 'avatar-status-away';
      default:        return 'avatar-status-busy';
    }
  }

  function getActionButton(empId, type) {
    if ("{{ auth()->user()->name }}" === "Admin") {
      return `<p class="mr-1">
        <a href="#" data-id="${empId}" class="${type}_creating">
          ${type === 'leave' ? 'Urlaub' : 'Krankmeldung'}
        </a>
      </p>`;
    }
    return '';
  }

  // Bind to both search inputs
  $(document).on('input', '.sa-emp-search', function () {
    fetchEmployees($(this).val());
  });

  // Optional: clicking the search icon triggers fetch using its sibling input
  $(document).on('click', '.sa-search-btn', function(){
    const v = $(this).closest('.sa-input-group').find('.sa-emp-search').val() || '';
    fetchEmployees(v);
  });

  // Initial load
  fetchEmployees();

  // Sick modal
  $(document).on('click', '.sick_creating', function(e) {
    e.preventDefault();
    let empId = $(this).data('id');
    let modal = $('#sickModal');
    modal.modal('show');
    modal.find('input[name="emp_id"]').val(empId);
  });

  // Leave modal
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
      .then(r => r.json())
      .then(data => {
        modal.querySelector(".leave_day").value = data.total_leave_days;
        modal.querySelector(".remaining_day").value = data.remaining_days;
        modal.querySelector(".last_year_remainings").value = data.last_year_remainings;
      })
      .catch(err => console.error("❌ Error fetching leave data:", err));
  }

  $('#new_leave_modal form').on('submit', function(e) {
    e.preventDefault();
    let formData = $(this).serialize();

    $.ajax({
      url: $(this).attr('action'),
      method: 'POST',
      data: formData,
      success: function() {
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

<script>
    (function(){
  const TRIGGER_ATTR = 'data-sa-collapse-target';
  const GROUP_ATTR   = 'data-sa-collapse-group';

  function openPanel(panel, trigger, group){
    // Close others in same group
    if (group) {
      document.querySelectorAll(`.sa-collapse[${GROUP_ATTR}="${group}"]`).forEach(p=>{
        if (p !== panel && !p.hidden) closePanel(p, document.querySelector(`[${TRIGGER_ATTR}="#${p.id}"]`));
      });
    }

    panel.hidden = false;
    panel.setAttribute(GROUP_ATTR, group || '');
    panel.style.height = '0px';
    // force reflow
    panel.getBoundingClientRect();
    panel.style.height = panel.scrollHeight + 'px';
    trigger?.setAttribute('aria-expanded','true');

    const onEnd = (e)=>{
      if (e.target !== panel) return;
      panel.style.height = '';
      panel.removeEventListener('transitionend', onEnd);
    };
    panel.addEventListener('transitionend', onEnd);
  }

  function closePanel(panel, trigger){
    panel.style.height = panel.scrollHeight + 'px';
    // next frame, collapse to 0
    requestAnimationFrame(()=>{ panel.style.height = '0px'; });
    const onEnd = (e)=>{
      if (e.target !== panel) return;
      panel.hidden = true;
      panel.style.height = '';
      trigger?.setAttribute('aria-expanded','false');
      panel.removeEventListener('transitionend', onEnd);
    };
    panel.addEventListener('transitionend', onEnd);
  }

  document.addEventListener('click', (e)=>{
    const trigger = e.target.closest(`[${TRIGGER_ATTR}]`);
    if (!trigger) return;

    const selector = trigger.getAttribute(TRIGGER_ATTR);
    const group    = trigger.getAttribute(GROUP_ATTR) || '';
    const panel    = document.querySelector(selector);
    if (!panel) return;

    const isOpen = !panel.hidden;
    isOpen ? closePanel(panel, trigger) : openPanel(panel, trigger, group);
  });
})();
</script> 
<script>
  (function () {
    const openBtn = document.getElementById('openSiderBtn');       // The main navbar icon
    const openInnerBtn = document.getElementById('openInnerSiderBtn'); // The new icon inside the strip
    const closeBtn = document.getElementById('closeSiderBtn');
    const sider = document.getElementById('quickSider');
    const backdrop = document.getElementById('quickSiderBackdrop');

    function openSider() { document.body.classList.add('qs-open'); }
    function closeSider() { document.body.classList.remove('qs-open'); }

    // Attach open function to BOTH buttons
    openBtn && openBtn.addEventListener('click', openSider);
    openInnerBtn && openInnerBtn.addEventListener('click', openSider);

    closeBtn && closeBtn.addEventListener('click', closeSider);
    backdrop && backdrop.addEventListener('click', closeSider);

    // Close on ESC
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeSider();
    });
  })();
</script>

<script>
  // Inline submenu toggles
  (function(){
    const toggles = document.querySelectorAll('.qs-toggle');

    toggles.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const wrap = btn.closest('.qs-has-sub');
        const sub  = document.getElementById(btn.getAttribute('aria-controls'));
        const isOpen = wrap.classList.contains('open');

        // close others (optional — keeps UI tidy)
        document.querySelectorAll('.qs-has-sub.open').forEach(w => {
          if (w !== wrap){
            w.classList.remove('open');
            const s = w.querySelector('.qs-sub');
            if (s){ s.hidden = true; w.querySelector('.qs-toggle')?.setAttribute('aria-expanded','false'); }
          }
        });

        // toggle current
        wrap.classList.toggle('open', !isOpen);
        sub.hidden = isOpen;
        btn.setAttribute('aria-expanded', String(!isOpen));
      });
    });

    // Click-outside to close open submenus (only inside the sider)
    document.addEventListener('click', (e) => {
      const inSider = e.target.closest('#quickSider');
      const onToggle = e.target.closest('.qs-toggle');
      if (!inSider || onToggle) return;
      document.querySelectorAll('.qs-has-sub.open').forEach(w => {
        w.classList.remove('open');
        const s = w.querySelector('.qs-sub');
        if (s){ s.hidden = true; w.querySelector('.qs-toggle')?.setAttribute('aria-expanded','false'); }
      });
    });
  })();

  // Make notification overlay reliably stack above the quick sider

</script>
 
<script>
    (function(){
  // Toggle one, close others
  function closeAll(){
    document.querySelectorAll('.qs-has-sub.is-open').forEach(el=>{
      el.classList.remove('is-open');
      const btn = el.querySelector('.qs-caret');
      if (btn) btn.setAttribute('aria-expanded','false');
    });
  }

  document.addEventListener('click', (e)=>{
    const caret = e.target.closest('.qs-caret');
    const inSub  = e.target.closest('.qs-submenu');
    const inWrap = e.target.closest('.qs-has-sub');

    // Click on caret → toggle
    if (caret) {
      e.preventDefault();
      e.stopPropagation();
      const wrap = caret.closest('.qs-has-sub');
      const open = wrap.classList.contains('is-open');
      closeAll();
      if (!open) {
        wrap.classList.add('is-open');
        caret.setAttribute('aria-expanded','true');
      }
      return;
    }

    // Click inside submenu → let links work; close after navigation
    if (inSub) return;

    // Click anywhere else → close
    if (!inWrap) closeAll();
  });

  // ESC to close
  document.addEventListener('keydown', (e)=>{
    if (e.key === 'Escape') closeAll();
  });
})();
</script>

<!-- Notification script :start -->
 <script type="module">
/**
 * Notifications Slider – FULL SCRIPT (Open + Mark Read + Open Relevant Page)
 * Requires:
 * - Laravel Echo configured (window.Echo)
 * - Toastr optional
 * - Routes:
 *    GET  {{ route('get.notification.list') }}
 *    POST /notification/mark-as-read/{id}
 *    POST /notification/mark-all-read
 *
 * Notification payload should include:
 *  data: { url?: string, lead_id?:, inquiry_id?:, task_id?:, appointment_id?:, ticket_id?:, offer_id?: ... }
 */
(function () {
  'use strict';

  // -----------------------------
  // Config
  // -----------------------------
  const userId  = {{ auth()->id() }};
  const listUrl = "{{ route('get.notification.list') }}";

  const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  const api = {
    markRead:    (id) => `{{ url('/notification/mark-as-read') }}/${id}`,
    markAllRead: `{{ url('/notification/mark-all-read') }}`,
  };

  // -----------------------------
  // State
  // -----------------------------
  let cache = [];               // flat list of notifications
  let currentFilter = 'all';
  let showUnreadOnly = false;

  // -----------------------------
  // DOM Helpers
  // -----------------------------
  const $  = (sel, el = document) => el.querySelector(sel);
  const $$ = (sel, el = document) => Array.from(el.querySelectorAll(sel));

  // -----------------------------
  // Utils
  // -----------------------------
  function escapeHtml(s) {
    return String(s ?? '').replace(/[&<>"']/g, (m) => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[m]));
  }

  function timeAgo(dateStr) {
    const d = new Date(dateStr);
    const s = Math.max(0, Math.floor((Date.now() - d.getTime()) / 1000));
    const mins = Math.floor(s / 60), hrs = Math.floor(mins / 60), days = Math.floor(hrs / 24);
    if (s < 60) return `${s}s`;
    if (mins < 60) return `${mins}m`;
    if (hrs < 24) return `${hrs}h`;
    return `${days}d`;
  }

  function iconFor(type) {
    const map = {
      lead: 'user-plus',
      inquiry: 'help-circle',
      responsible_change: 'shuffle',
      appointment: 'calendar',
      ticket: 'alert-triangle',
      employee: 'user',
      task: 'check-square',
      project_task: 'sliders',
      offer: 'file-text',
      other: 'bell',
    };
    return map[type] || 'bell';
  }

  function toastNotify(payload) {
    const title = payload.title || 'Benachrichtigung';
    const msg   = payload.message || '';
    if (window.toastr) {
      toastr.options = { positionClass: 'toast-bottom-right', timeOut: 6000, progressBar: true };
      toastr.info(msg, title);
      return;
    }
    const t = document.createElement('div');
    t.style.cssText = 'position:fixed;right:16px;bottom:16px;background:#111827;color:#fff;padding:10px 12px;border-radius:10px;z-index:2000;max-width:320px;box-shadow:0 10px 20px rgba(0,0,0,.2)';
    t.innerHTML = `<strong style="display:block;margin-bottom:4px">${escapeHtml(title)}</strong><div>${escapeHtml(msg)}</div>`;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 4000);
  }

  async function postJson(url, body = {}) {
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      },
      body: JSON.stringify(body),
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json().catch(() => ({}));
  }

  // -----------------------------
  // Badge
  // -----------------------------
  function badgeUpdate() {
    const unread = cache.filter(n => !n.read_at).length;

    const sidebarBadge = $('#notifUnreadBadge');
    if (sidebarBadge) {
      if (unread > 0) {
        sidebarBadge.textContent = unread;
        sidebarBadge.classList.remove('d-none');
      } else {
        sidebarBadge.classList.add('d-none');
      }
    }

    const navbarBadge = $('#navbarNotificationBadge');
    if (navbarBadge) {
      if (unread > 0) {
        navbarBadge.textContent = unread;
        navbarBadge.style.display = 'inline-block';
      } else {
        navbarBadge.style.display = 'none';
      }
    }
  }

  // -----------------------------
  // Resolve URL to open
  // IMPORTANT: Adjust fallback URL patterns to your app routes.
  // Best: store "url" inside notifications.data when creating notifications.
  // -----------------------------
function resolveNotificationUrl(n) {
  const d = n?.data || {};

  // ✅ Preferred: backend provides direct URL
  const direct = d.url || d.link || d.href || d.redirect || d.route_url;
  if (direct) return direct;

  // Normalize common id keys (many systems store generic entity ids)
  const id =
    d.task_id || d.taskId || d.task?.id ||
    d.appointment_id || d.appointmentId || d.appointment?.id ||
    d.lead_id || d.leadId || d.lead?.id ||
    d.inquiry_id || d.inquiryId || d.inquiry?.id ||
    d.ticket_id || d.ticketId || d.ticket?.id ||
    d.offer_id || d.offerId || d.offer?.id ||
    d.model_id || d.entity_id || d.id;

  // Normalize type
  const type =
    (n.type || d.type || d.entity_type || d.model || '').toString().toLowerCase();

  // Fallback URL patterns (ADJUST to your real routes)
  if (type.includes('task') && id) return `{{ url('/tasks') }}/${id}`;
  if (type.includes('appointment') && id) return `{{ url('/appointments') }}/${id}`;
  if (type.includes('lead') && id) return `{{ url('/new_lead_profile') }}/${id}`;
  if (type.includes('inquiry') && id) return `{{ url('/inquiry_show') }}/${id}`;
  if (type.includes('ticket') && id) return `{{ url('/problem/profile') }}/${id}`;
  if (type.includes('offer') && id) return `{{ url('/offers') }}/${id}`;

  return null;
}

  // -----------------------------
  // Render
  // -----------------------------
  function render() {
    const list = $('#notifList');
    if (!list) return;

    list.setAttribute('aria-busy', 'true');
    list.innerHTML = '';

    const q = ($('#notifSearch')?.value || '').toLowerCase();
    const type = currentFilter;
    const unreadOnly = showUnreadOnly || (type === 'unread');

    const items = cache.filter(n => {
      const matchesType = type === 'all' || type === 'unread' || n.type === type;
      const matchesUnread = !unreadOnly || !n.read_at;
      const matchesSearch = !q || (String(n.title || '').toLowerCase().includes(q) || String(n.message || '').toLowerCase().includes(q));
      return matchesType && matchesUnread && matchesSearch;
    });

    if (items.length === 0) {
      list.innerHTML = `<div class="text-center text-muted mt-3">Keine Benachrichtigungen</div>`;
      list.setAttribute('aria-busy', 'false');
      return;
    }

    for (const n of items) {
      const card = document.createElement('article');
      card.className = `notif-card ${n.read_at ? '' : 'unread'}`;
      card.dataset.id = n.id;

      const canOpen = !!resolveNotificationUrl(n);

      card.innerHTML = `
        <div class="notif-icon">
          <i class="feather icon-${iconFor(n.type)}"></i>
        </div>
        <div class="notif-body">
          <h6>${escapeHtml(n.title || 'Benachrichtigung')}</h6>
          <p>${escapeHtml(n.message || '')}</p>
          <div class="notif-actions">
            <button class="btn btn-sm btn-outline-primary" data-action="open" ${canOpen ? '' : 'disabled'}>Öffnen</button>
            <button class="btn btn-sm btn-outline-secondary" data-action="read">${n.read_at ? 'Gelesen' : 'Als gelesen'}</button>
            <button class="btn btn-sm btn-outline-danger" data-action="remove">Entfernen</button>
          </div>
        </div>
        <div class="notif-meta">
          <div class="text-uppercase small">${escapeHtml(n.type ?? 'other')}</div>
          <div title="${escapeHtml(n.performed_at)}">${timeAgo(n.performed_at)}</div>
        </div>
      `;
      list.appendChild(card);
    }

    list.setAttribute('aria-busy', 'false');
  }

  // -----------------------------
  // Data Load
  // -----------------------------
  async function loadInitial() {
    const url = new URL(listUrl, window.location.origin);
    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
    if (!res.ok) throw new Error(`Failed to load notifications (${res.status})`);

    const groups = await res.json();

    cache = [];
    Object.keys(groups || {}).forEach(type => {
      (groups[type] || []).forEach(n => cache.push(n));
    });

    // newest first
    cache.sort((a, b) => new Date(b.performed_at) - new Date(a.performed_at));

    badgeUpdate();
    render();
  }

  // -----------------------------
  // Actions
  // -----------------------------
  async function markRead(id) {
    const n = cache.find(x => String(x.id) === String(id));
    if (!n) return;

    // optimistic UI
    const prev = n.read_at;
    n.read_at = n.read_at || new Date().toISOString();
    badgeUpdate();
    render();

    // persist
    try {
      await postJson(api.markRead(id));
    } catch (err) {
      // rollback
      n.read_at = prev || null;
      badgeUpdate();
      render();
      console.error('markRead failed', err);
    }
  }

  function removeItem(id) {
    cache = cache.filter(x => String(x.id) !== String(id));
    badgeUpdate();
    render();
  }

  async function openItem(id) {
    const n = cache.find(x => String(x.id) === String(id));
    if (!n) return;

    await markRead(id);

    const url = resolveNotificationUrl(n);
    if (!url) return;

    window.closeNotification?.();
    window.location.href = url;
  }

  async function markAllRead() {
    // optimistic
    const prev = cache.map(n => ({ id: n.id, read_at: n.read_at }));
    cache.forEach(n => n.read_at = n.read_at || new Date().toISOString());
    badgeUpdate();
    render();

    try {
      await postJson(api.markAllRead);
    } catch (err) {
      // rollback
      prev.forEach(p => {
        const n = cache.find(x => String(x.id) === String(p.id));
        if (n) n.read_at = p.read_at;
      });
      badgeUpdate();
      render();
      console.error('markAllRead failed', err);
    }
  }

  // -----------------------------
  // Events
  // -----------------------------
  $('#notifFilters')?.addEventListener('click', (e) => {
    const btn = e.target.closest('.chip');
    if (!btn) return;
    $$('#notifFilters .chip').forEach(c => c.classList.remove('active'));
    btn.classList.add('active');

    const t = btn.dataset.type;
    currentFilter = t === 'unread' ? 'unread' : t;
    showUnreadOnly = t === 'unread';
    render();
  });

  $('#notifSearch')?.addEventListener('input', () => render());

  $('#loadMoreBtn')?.addEventListener('click', () => {
    // no pagination yet => refresh
    loadInitial().catch(console.error);
  });

  $('#markAllReadBtn')?.addEventListener('click', () => {
    markAllRead().catch(console.error);
  });

  $('#notifList')?.addEventListener('click', (e) => {
    const btn = e.target.closest('button[data-action]');
    if (!btn) return;

    const card = e.target.closest('.notif-card');
    const id = card?.dataset.id;
    if (!id) return;

    const action = btn.dataset.action;
    if (action === 'read')   markRead(id).catch(console.error);
    if (action === 'remove') removeItem(id);
    if (action === 'open')   openItem(id).catch(console.error);
  });

  // -----------------------------
  // Open/Close API (matches your HTML onclick="closeNotification()")
  // -----------------------------
  window.openNotificationSlider = function () {
    document.body.classList.remove('qs-open');
    document.body.classList.add('notif-open');
    loadInitial().catch(console.error);
  };

  window.closeNotification = function () {
    document.body.classList.remove('notif-open');
  };

  // Optional: open via any bell button
  document.querySelectorAll('[data-open="notifications"]').forEach(b => {
    b.addEventListener('click', (e) => {
      e.preventDefault();
      window.openNotificationSlider();
    });
  });

  // -----------------------------
  // Realtime (Laravel Echo)
  // -----------------------------
  if (window.Echo?.private) {
    window.Echo.private(`App.Models.User.${userId}`)
      .notification((payload) => {
        toastNotify(payload);

        const normalized = {
          id: payload.id || (crypto?.randomUUID ? crypto.randomUUID() : String(Date.now())),
          type: payload.type || 'other',
          title: payload.title || '',
          message: payload.message || '',
          performed_at: payload.performed_at || new Date().toISOString(),
          read_at: null,
          data: payload.data || {}, // ✅ keep ids/url
        };

        cache.unshift(normalized);
        cache.sort((a, b) => new Date(b.performed_at) - new Date(a.performed_at));

        badgeUpdate();
        render();
      });
  }

  // Initial load (so badge correct even before opening)
  loadInitial().catch(console.error);

})();
</script>

<!-- Notification script :end -->
  
<script>
  function toggleSidebar(e){
    e && e.preventDefault();
    document.body.classList.remove('qs-open');

    const sidebar = document.getElementById("notificationSidebar");
    const backdrop = document.getElementById("notificationBackdrop");
    if (!sidebar || !backdrop) return;

    const isOpen = sidebar.dataset.open === '1';

    if (isOpen){
      sidebar.style.right = '-430px';
      backdrop.style.opacity = '0';
      backdrop.style.visibility = 'hidden';
      sidebar.dataset.open = '0';
    } else {
      // ensure element is visible
      sidebar.style.right = '0';
      backdrop.style.opacity = '1';
      backdrop.style.visibility = 'visible';
      sidebar.dataset.open = '1';
      if (typeof loadInitial === 'function') loadInitial();
    }
  }

  function closeNotification(){
    const s = document.getElementById("notificationSidebar");
    const b = document.getElementById("notificationBackdrop");
    if (s){ s.style.right = '-430px'; s.dataset.open = '0'; }
    if (b){ b.style.opacity = '0'; b.style.visibility = 'hidden'; }
  }
</script>

<script>
(function () {
  'use strict';

  const ROOT_ATTR   = 'data-sa-menu';
  const TOGGLE_ATTR = 'data-sa-toggle';
  const STATUS_ATTR = 'data-sa-status';

  /** Track one open menu at a time */
  let openContext = null;

  document.querySelectorAll(`[${ROOT_ATTR}]`).forEach(initSaMenu);

  function initSaMenu(root) {
    const toggle = root.querySelector(`[${TOGGLE_ATTR}]`);
    const menu   = root.querySelector('.sa-menu');
    const items  = Array.from(menu.querySelectorAll('.sa-item'));
    const statusLabel = root.querySelector('.user-status small');

    // a11y
    items.forEach(el => {
      el.setAttribute('tabindex', '-1');
      el.setAttribute('role', 'menuitem');
    });

    let isOpen = false;
    let focusIndex = -1;

    const open = () => {
      if (isOpen) return;
      // close other open menu
      if (openContext && openContext.close) openContext.close();

      isOpen = true;
      menu.hidden = false;
      toggle.setAttribute('aria-expanded', 'true');
      focusIndex = 0;
      requestAnimationFrame(() => items[0]?.focus());

      document.addEventListener('click', onDocClick, true);
      document.addEventListener('keydown', onKeydown, true);

      openContext = { close };
    };

    const close = () => {
      if (!isOpen) return;
      isOpen = false;
      menu.hidden = true;
      toggle.setAttribute('aria-expanded', 'false');
      focusIndex = -1;

      document.removeEventListener('click', onDocClick, true);
      document.removeEventListener('keydown', onKeydown, true);

      // return focus to trigger
      try { toggle.focus({preventScroll:true}); } catch(_){}
      if (openContext && openContext.close === close) openContext = null;
    };

    toggle.addEventListener('click', (e) => {
      e.preventDefault();
      isOpen ? close() : open();
    });

    function onDocClick(e) {
      if (!root.contains(e.target)) close();
    }

    function onKeydown(e) {
      if (!isOpen) return;

      if (e.key === 'Escape') {
        e.preventDefault();
        close();
        return;
      }

      if (['ArrowDown','ArrowUp','Home','End'].includes(e.key)) {
        e.preventDefault();
        if (focusIndex === -1) focusIndex = 0;
        if (e.key === 'ArrowDown') focusIndex = Math.min(items.length - 1, focusIndex + 1);
        if (e.key === 'ArrowUp')   focusIndex = Math.max(0, focusIndex - 1);
        if (e.key === 'Home')      focusIndex = 0;
        if (e.key === 'End')       focusIndex = items.length - 1;
        items[focusIndex]?.focus();
      }
    }

    // handle status changes
    menu.addEventListener('click', (e) => {
      const btn = e.target.closest(`[${STATUS_ATTR}]`);
      if (!btn) return; // links navigate as usual

      e.preventDefault();
      const status = String(btn.getAttribute(STATUS_ATTR) || '').toLowerCase();

      if (statusLabel) {
        const map = { aktiv: 'Aktiv', abwesend: 'Abwesend', mittagspause: 'Mittagspause' };
        statusLabel.textContent = map[status] ?? status;
      }

      // Optional: persist (CSRF meta required)
      const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
      if (csrf) {
        fetch('/user/status', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json',
          },
          body: JSON.stringify({ status }),
        }).catch(() => {});
      }

      // dispatch a custom event for app-level listeners
      root.dispatchEvent(new CustomEvent('sa:statusChanged', { detail: { status } }));

      close();
    });
  }
})();
</script>

<script>
(function () {
    'use strict';

    const wrapper = document.getElementById('qs-department-wrapper');
    if (!wrapper) return;

    const btn        = wrapper.querySelector('.qs-toggle');
    const panel      = document.getElementById('qs-sub-department');
    const loadingEl  = panel.querySelector('.js-dept-loading');
    const listEl     = panel.querySelector('.js-dept-list');
    const errorEl    = panel.querySelector('.js-dept-error');
    const url        = wrapper.dataset.url;

    let isLoaded = false;
    let isOpen   = false;

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function togglePanel() {
        isOpen = !isOpen;
        panel.hidden = !isOpen;
        btn.setAttribute('aria-expanded', String(isOpen));

        if (isOpen && !isLoaded) {
            loadDepartments();
        }
    }

    btn.addEventListener('click', function (e) {
        e.preventDefault();
        togglePanel();
    });

    function loadDepartments() {
        if (!url) return;
        loadingEl.style.display = 'block';
        errorEl.style.display   = 'none';
        listEl.innerHTML        = '';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(function (res) {
            if (!res.ok) {
                throw new Error('HTTP ' + res.status);
            }
            return res.json();
        })
        .then(function (items) {
            isLoaded = true;
            loadingEl.style.display = 'none';
            renderDepartments(items);
        })
        .catch(function (err) {
            console.error('Department load failed:', err);
            loadingEl.style.display = 'none';
            errorEl.textContent = 'Abteilungen konnten nicht geladen werden.';
            errorEl.style.display = 'block';
        });
    }

    function renderDepartments(items) {
        listEl.innerHTML = '';

        if (!items || !items.length) {
            listEl.innerHTML =
                '<div class="p-3 text-center text-muted small">Keine Abteilungen gefunden</div>';
            return;
        }

        items.forEach(function (dept) {
            const a = document.createElement('a');
            a.className = 'qs-sub-item dept-link';
            a.href = dept.url;

            const staffHtml = (dept.staff || []).slice(0, 5).map(function (emp) {
                return '<img src="' + emp.avatar + '" alt="' + escapeHtml(emp.name) + '">';
            }).join('');

            const moreHtml = (dept.more_count && dept.more_count > 0)
                ? '<span style="font-size: 9px; background: #ccc; color:#333; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #fff; margin-left: -8px;">+' +
                    dept.more_count + '</span>'
                : '';

            a.innerHTML =
                '<span class="text-truncate" style="max-width: 130px; font-weight: 600;">' +
                    escapeHtml(dept.name) +
                '</span>' +
                '<div class="avatar-stack">' +
                    staffHtml +
                    moreHtml +
                '</div>';

            listEl.appendChild(a);
        });
    }
})();
</script>

<!-- Mini Chat Script  -->
<script>
(() => {
  'use strict';

  /* ---------------------------------------------
   * DOM helpers
   * ------------------------------------------- */
  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

  const escapeHtml = (s) =>
    String(s ?? '')
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');

  // linkify already-escaped text (so we don't inject html)
  const linkifyEscaped = (escaped) => {
    const re = /((https?:\/\/|www\.)[^\s<]+)/gi;
    return String(escaped).replace(re, (m) => {
      const href = m.startsWith('http') ? m : `https://${m}`;
      return `<a href="${href}" target="_blank" rel="noopener noreferrer">${m}</a>`;
    });
  };

  const pad2 = (n) => String(n).padStart(2, '0');
  const isoDay = (d) => `${d.getFullYear()}-${pad2(d.getMonth() + 1)}-${pad2(d.getDate())}`;

  /* ---------------------------------------------
   * Resolve current user (auth user id + employee display name from meta)
   * ------------------------------------------- */
  const me = {
    id: Number($('meta[name="chat-user-id"]')?.content || 0),
    name: ($('meta[name="chat-user-name"]')?.content || '').trim() || 'Ich',
  };
  if (!me.id) return;

  const csrf = $('meta[name="csrf-token"]')?.content || '';
  const sidebarEl   = $('#fb-right-sidebar');
  const containerEl = $('#fb-bottom-chats-container');
  const overlayEl   = $('#fb-mobile-overlay');
  const badgeEl     = $('#mobile-chat-badge');

  const MAX_VISIBLE_CHATS = 3;

  /* ---------------------------------------------
   * Routes (override via window.fbChatRoutes)
   * ------------------------------------------- */
  const routes = Object.assign({
    employees: '/chat/employees',

    fetchUser:  (id) => `/chat/fetch/${id}`,
    fetchGroup: (id) => `/chat/group/fetch/${id}`,

    markReadUser:  (id) => `/chat/mark-read/${id}`,
    markReadGroup: (id) => `/chat/group/mark-read/${id}`,

    send: '/chat/send',

    update: (id) => `/chat/message/${id}`,
    delete: (id) => `/chat/message/${id}`,
  }, window.fbChatRoutes || {});

  const state = {
    contacts: [],          // mixed: users + groups
    activeChats: [],       // opened windows
    recorders: new Map(),  // key -> {rec, stream}
    editing: new Map(),    // key -> {msgId, originalText}
  };

  /* ---------------------------------------------
   * CSS for message actions/menu + fixed z-index
   * ------------------------------------------- */
  (() => {
    const css = `
      /* keep menu above everything inside the chat window */
      .fb-msg { position: relative; }
      .fb-msg-actions{
        position:absolute;
        top:6px;
        right:8px;
        display:flex;
        gap:6px;
        opacity:0;
        pointer-events:none;
        transition: opacity .12s ease;
        z-index: 6;
      }
      .fb-msg.outgoing:hover .fb-msg-actions,
      .fb-msg.incoming:hover .fb-msg-actions{
        opacity:1;
        pointer-events:auto;
      }
      .fb-msg-action-btn{
        width:26px;height:26px;
        border-radius:999px;
        border:1px solid rgba(0,0,0,.08);
        background:#fff;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        cursor:pointer;
      }
      .fb-msg-menu{
        position:absolute;
        top:34px;
        right:8px;
        min-width:160px;
        background:#fff;
        border:1px solid rgba(0,0,0,.10);
        border-radius:12px;
        box-shadow:0 10px 26px rgba(0,0,0,.14);
        padding:6px;
        display:none;
        z-index: 99999; /* important */
      }
      .fb-msg-menu.is-open{ display:block; }
      .fb-msg-menu button{
        width:100%;
        border:0;
        background:transparent;
        padding:10px 10px;
        text-align:left;
        border-radius:10px;
        cursor:pointer;
        font-size:13px;
      }
      .fb-msg-menu button:hover{ background:rgba(0,0,0,.05); }
      .fb-msg-menu button.danger{ color:#b91c1c; }

      .fb-chat-btn.is-recording { background: rgba(239,68,68,.12); border-color: rgba(239,68,68,.35); }
      .fb-chat-input.is-editing { outline: 2px solid rgba(59,130,246,.35); }

      /* prevent parent overflow from clipping menu */
      .fb-chat-body { overflow: auto; }
      .fb-msg-row { overflow: visible; }
      .fb-msg { overflow: visible; }
    `;
    const style = document.createElement('style');
    style.type = 'text/css';
    style.appendChild(document.createTextNode(css));
    document.head.appendChild(style);
  })();

  /* ---------------------------------------------
   * Global compatibility
   * ------------------------------------------- */
  window.toggleChatSidebar = () => toggleSidebar();
  window.fbClose = (key) => closeChat(key);
  window.fbSend  = (key, inputEl) => sendText(key, inputEl);

  /* ---------------------------------------------
   * Sidebar: mobile open/close + unread badge
   * ------------------------------------------- */
  function toggleSidebar(force) {
    if (!sidebarEl) return;

    const show = (typeof force === 'boolean')
      ? force
      : !sidebarEl.classList.contains('show-mobile');

    sidebarEl.classList.toggle('show-mobile', show);
    if (overlayEl) overlayEl.style.display = show ? 'block' : 'none';
  }

  function updateMobileBadge() {
    if (!badgeEl) return;
    const totalUnread = (state.contacts || []).reduce((sum, c) => sum + (c.unread || 0), 0);
    badgeEl.innerText = String(totalUnread);
    badgeEl.style.display = totalUnread > 0 ? 'block' : 'none';
  }

  /* ---------------------------------------------
   * Contacts
   * ------------------------------------------- */
  async function loadContacts() {
    try {
      const res = await fetch(routes.employees, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      });
      const data = await safeJson(res);

      const users = (data.employees || []).map((u) => ({
        ...u,
        type: 'user',
        key: `u-${u.id}`,
        displayName: `${u.name ?? ''} ${u.lastname ?? ''}`.trim() || `#${u.id}`,
        online: !!u.online,
        unread: Number(u.unread || 0),
      }));

      const groups = (data.groups || []).map((g) => ({
        ...g,
        type: 'group',
        key: `g-${g.id}`,
        displayName: (g.name || 'Gruppe').trim(),
        unread: Number(g.unread || 0),
      }));

      state.contacts = [...users, ...groups];
      renderSidebar();
    } catch (e) {
      console.error('Failed to load contacts', e);
    }
  }

  function renderSidebar() {
    if (!sidebarEl) return;

    sidebarEl.innerHTML = '';

    const mobileHeader = document.createElement('div');
    mobileHeader.className = 'fb-sidebar-mobile-header';
    mobileHeader.innerHTML = `
      <span>Kontakte</span>
      <button type="button" class="btn btn-sm btn-icon btn-flat-secondary" data-fb-close-sidebar>
        <i class="feather icon-x" style="font-size:1.2rem;"></i>
      </button>
    `;
    sidebarEl.appendChild(mobileHeader);
    mobileHeader.querySelector('[data-fb-close-sidebar]')?.addEventListener('click', () => toggleSidebar(false));

    const users = state.contacts
      .filter((c) => c.type === 'user')
      .sort((a, b) => (b.unread || 0) - (a.unread || 0) || (b.online ? 1 : 0) - (a.online ? 1 : 0));

    const groups = state.contacts
      .filter((c) => c.type === 'group')
      .sort((a, b) => (b.unread || 0) - (a.unread || 0));

    sidebarEl.appendChild(renderSection('Mitarbeiter', users));
    sidebarEl.appendChild(renderSection('Gruppen', groups));

    updateMobileBadge();
  }

  function renderSection(title, items) {
    const wrap = document.createElement('div');
    wrap.className = 'fb-section';

    const header = document.createElement('div');
    header.className = 'fb-section-title';
    header.textContent = '';
    wrap.appendChild(header);

    const list = document.createElement('div');
    list.className = 'fb-section-list';

    items.forEach((c) => {
      const item = document.createElement('div');
      item.className = 'fb-contact-item';
      item.dataset.key = c.key;
      item.title = c.displayName;

      item.addEventListener('click', () => {
        openChatWindow(c);
        if (window.innerWidth < 768) toggleSidebar(false);
      });

      const avatar = buildAvatarHtml(c.image || c.avatar, c.displayName);

      const statusHtml = (c.type === 'user')
        ? `<div class="fb-status-dot ${c.online ? 'online' : ''}"></div>`
        : '';

      const badgeHtml = (c.unread > 0)
        ? `<div class="fb-avatar-badge">${c.unread}</div>`
        : '';

      const nameHtml = `<span class="fb-contact-name">${escapeHtml(c.displayName)}</span>`;
      const tooltipHtml = `<div class="fb-contact-tooltip">${escapeHtml(c.displayName)}</div>`;

      item.innerHTML = `${avatar}${nameHtml}${statusHtml}${badgeHtml}${tooltipHtml}`;
      list.appendChild(item);
    });

    wrap.appendChild(list);
    return wrap;
  }

  function buildAvatarHtml(image, displayName) {
    const initials = (displayName || '').trim().slice(0, 2).toUpperCase() || '??';
    let imgHtml = '';
    if (image) {
      const src = String(image);
      const finalSrc = src.startsWith('http') ? src : `/images/employee/${src}`;
      imgHtml = `<img src="${finalSrc}" class="fb-avatar" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">`;
    }
    const initialsHtml = `<div class="fb-initials" ${imgHtml ? 'style="display:none"' : ''}>${escapeHtml(initials)}</div>`;
    return `${imgHtml}${initialsHtml}`;
  }

  /* ---------------------------------------------
   * Window management
   * ------------------------------------------- */
  function openChatWindow(contact) {
    const existing = state.activeChats.find((c) => c.key === contact.key);
    if (existing) {
      const win = $(`#chat-win-${contact.key}`);
      if (win) {
        win.classList.remove('minimized');
        win.querySelector('input.fb-chat-input')?.focus();
        scrollToBottom(contact.key);
      }
      return;
    }

    state.activeChats.push(contact);

    if (state.activeChats.length > MAX_VISIBLE_CHATS) {
      const removed = state.activeChats.shift();
      stopRecorderIfAny(removed.key);
      state.editing.delete(removed.key);
      $(`#chat-win-${removed.key}`)?.remove();
    }

    renderChatWindow(contact);
    contact.unread = 0;
    renderSidebar();
  }

  function closeChat(key) {
    stopRecorderIfAny(key);
    state.editing.delete(key);
    state.activeChats = state.activeChats.filter((c) => c.key !== key);
    $(`#chat-win-${key}`)?.remove();
  }

  function renderChatWindow(contact) {
    if (!containerEl) return;

    const win = document.createElement('div');
    win.className = 'fb-chat-window';
    win.id = `chat-win-${contact.key}`;

    win.innerHTML = `
      <div class="fb-chat-header" data-fb-toggle-min>
        <div class="fb-chat-title">${escapeHtml(contact.displayName)}</div>
        <div class="fb-chat-controls">
          <i class="feather icon-minus" title="Minimieren"></i>
          <i class="feather icon-x" data-fb-close title="Schließen"></i>
        </div>
      </div>

      <div class="fb-chat-body" id="chat-body-${contact.key}">
        <div class="text-center text-muted mt-4"><small>Lade Nachrichten...</small></div>
      </div>

      <div class="fb-chat-footer">
        <input type="file" class="fb-file-input" id="fb-file-${contact.key}" style="display:none" />
        <button type="button" class="fb-chat-btn" data-fb-attach title="Datei / Bild">
          <i class="feather icon-paperclip"></i>
        </button>
        <button type="button" class="fb-chat-btn" data-fb-mic title="Sprachnachricht">
          <i class="feather icon-mic"></i>
        </button>
        <input type="text" class="fb-chat-input" placeholder="Nachricht..." />
        <button type="button" class="fb-chat-btn" data-fb-send title="Senden">
          <i class="feather icon-send"></i>
        </button>
      </div>
    `;
    containerEl.appendChild(win);

    // header controls
    win.querySelector('[data-fb-toggle-min]')?.addEventListener('click', (e) => {
      if (e.target.closest('[data-fb-close]')) return;
      win.classList.toggle('minimized');
    });
    win.querySelector('[data-fb-close]')?.addEventListener('click', (e) => {
      e.stopPropagation();
      closeChat(contact.key);
    });

    // footer controls
    const input    = win.querySelector('input.fb-chat-input');
    const attachBtn= win.querySelector('[data-fb-attach]');
    const micBtn   = win.querySelector('[data-fb-mic]');
    const sendBtn  = win.querySelector('[data-fb-send]');
    const fileInput= win.querySelector(`#fb-file-${CSS.escape(contact.key)}`);

    input?.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') sendText(contact.key, input);
      if (e.key === 'Escape') cancelEdit(contact.key);
    });
 

    // --- NEW: Handle pasting images directly into the chat input ---
    input?.addEventListener('paste', (e) => {
      const items = (e.clipboardData || window.clipboardData).items;
      for (let i = 0; i < items.length; i++) {
        if (items[i].type.indexOf('image') !== -1) {
          // It's an image! Get the file and send it
          const file = items[i].getAsFile();
          if (file) {
            e.preventDefault(); // Prevent default text pasting
            
            // Optional: Give the pasted image a custom name
            const newFile = new File([file], `pasted-image-${Date.now()}.png`, { type: file.type });
            
            sendFile(contact.key, newFile); 
          }
        }
      }
    });
    // ---------------------------------------------------------------

    sendBtn?.addEventListener('click', () => sendText(contact.key, input));
    attachBtn?.addEventListener('click', () => fileInput?.click());

    fileInput?.addEventListener('change', () => {
      const file = fileInput.files?.[0];
      fileInput.value = '';
      if (file) sendFile(contact.key, file);
    });

    micBtn?.addEventListener('click', () => toggleVoiceRecording(contact.key, micBtn));

    // close menus on outside click within this window
    win.addEventListener('click', (e) => {
      if (!e.target.closest('.fb-msg-menu') && !e.target.closest('[data-fb-msg-menu-btn]')) {
        closeAllMenus(contact.key);
      }
    });

    // message actions delegation
    const body = win.querySelector(`#chat-body-${CSS.escape(contact.key)}`);
    body?.addEventListener('click', (e) => onBodyClick(contact.key, contact, e));

    try { window.feather?.replace?.(); } catch (_) {}

    fetchMessages(contact);
  }

  /* ---------------------------------------------
   * Messages (fetch + render + day grouping)
   * ------------------------------------------- */
  async function fetchMessages(contact) {
    const body = $(`#chat-body-${contact.key}`);
    if (!body) return;

    const url = contact.type === 'group'
      ? routes.fetchGroup(contact.id)
      : routes.fetchUser(contact.id);

    try {
      const res = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        credentials: 'same-origin',
      });
      const msgs = await safeJson(res);

      body.innerHTML = '';
      const sorted = (Array.isArray(msgs) ? msgs : []).slice().sort(
        (a, b) => new Date(a.created_at) - new Date(b.created_at)
      );

      renderMessages(contact, sorted);
      scrollToBottom(contact.key);

      const readUrl = contact.type === 'group'
        ? routes.markReadGroup(contact.id)
        : routes.markReadUser(contact.id);

      fetch(readUrl, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      }).catch(() => {});
    } catch (e) {
      console.error(e);
      body.innerHTML = '<div class="text-center text-danger mt-4"><small>Fehler.</small></div>';
    }
  }

  function renderMessages(contact, msgs) {
    const body = $(`#chat-body-${contact.key}`);
    if (!body) return;

    let lastDay = null;

    msgs.forEach((m) => {
      const d = new Date(m.created_at || Date.now());
      const day = isoDay(d);

      if (day !== lastDay) {
        lastDay = day;
        const sep = document.createElement('div');
        sep.className = 'fb-day-sep';
        sep.innerHTML = `<span>${escapeHtml(d.toLocaleDateString())}</span>`;
        body.appendChild(sep);
      }

      upsertMessage(contact, m);
    });
  }

  function getMessageId(msg) {
    return msg?.id ?? msg?.message_id ?? msg?.uuid ?? msg?.uid ?? null;
  }

  function upsertMessage(contact, msg) {
    const key = contact.key;
    const body = $(`#chat-body-${key}`);
    if (!body) return;

    const mid = getMessageId(msg);
    if (mid) {
      const existing = body.querySelector(`[data-msg-id="${CSS.escape(String(mid))}"]`);
      if (existing) {
        const textEl = existing.querySelector('.fb-msg-text');
        const attachWrap = existing.querySelector('[data-fb-attach-wrap]');
        const attachment = getAttachment(msg);

        if (attachWrap) attachWrap.innerHTML = attachment ? renderAttachmentHtml(attachment) : '';

        const text = String(msg.message || '').trim();
        if (textEl) {
          textEl.innerHTML = text
            ? linkifyEscaped(escapeHtml(text))
            : `<i class="italic text-sm text-gray-500">Diese Nachricht wurde gelöscht</i>`;
        }

        // also update sender label if payload changed
        const senderEl = existing.querySelector('.fb-msg-sender');
        if (senderEl) senderEl.textContent = getSenderName(contact, msg, false);

        return;
      }
    }

    appendMessage(contact, msg);
  }

  function appendMessage(contact, msg) {
    const body = $(`#chat-body-${contact.key}`);
    if (!body) return;

    const senderId = Number(msg.from_user_id || msg.from_user?.id || msg.user_id || msg.sender_id || 0);
    const isMe = senderId === me.id;

    const senderName = getSenderName(contact, msg, isMe);
    const avatarUrl  = getSenderAvatar(contact, msg, isMe);
    const attachment = getAttachment(msg);
    const mid        = getMessageId(msg);

    const wrap = document.createElement('div');
    wrap.className = `fb-msg-row ${isMe ? 'is-me' : 'is-them'}`;
    if (mid) wrap.dataset.msgId = String(mid);

    const avatarHtml = (!isMe)
      ? `<div class="fb-msg-avatar">${buildAvatarHtml(avatarUrl, senderName)}</div>`
      : `<div class="fb-msg-avatar" style="visibility:hidden"></div>`;

    const bubble = document.createElement('div');
    bubble.className = `fb-msg ${isMe ? 'outgoing' : 'incoming'}`;

    const time = new Date(msg.created_at || Date.now())
      .toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    const text = String(msg.message || '').trim();

    const contentHtml = (() => {
      if (attachment) {
        let html = `<div data-fb-attach-wrap>${renderAttachmentHtml(attachment)}</div>`;
        html += text ? `<div class="fb-msg-text">${linkifyEscaped(escapeHtml(text))}</div>` : `<div class="fb-msg-text"></div>`;
        return html;
      }
      return `<div class="fb-msg-text">${text ? linkifyEscaped(escapeHtml(text)) : `<i class="italic text-sm text-gray-500">Diese Nachricht wurde gelöscht</i>`}</div>`;
    })();

    const actionsHtml = (isMe && mid)
      ? `
        <div class="fb-msg-actions">
          <button type="button" class="fb-msg-action-btn" data-fb-msg-menu-btn title="Optionen">
            <i class="feather icon-more-horizontal"></i>
          </button>
        </div>
        <div class="fb-msg-menu" data-fb-msg-menu>
          <button type="button" data-fb-edit>Bearbeiten</button>
          <button type="button" class="danger" data-fb-delete>Löschen</button>
        </div>
      `
      : '';

    bubble.innerHTML = `
      <div class="fb-msg-sender">${escapeHtml(senderName)}</div>
      ${contentHtml}
      <div class="fb-msg-meta">${escapeHtml(time)}</div>
      ${actionsHtml}
    `;

    wrap.innerHTML = avatarHtml;
    wrap.appendChild(bubble);
    body.appendChild(wrap);

    try { window.feather?.replace?.(); } catch (_) {}
  }

  function renderAttachmentHtml(attachment) {
    if (attachment.kind === 'image') {
      return `
        <a href="${attachment.url}" target="_blank" rel="noopener noreferrer" class="fb-attach fb-attach-image">
          <img src="${attachment.url}" alt="${escapeHtml(attachment.name || 'Bild')}" loading="lazy">
        </a>
      `;
    }
    if (attachment.kind === 'voice') {
      return `
        <div class="fb-attach fb-attach-voice">
          <audio controls preload="none" src="${attachment.url}"></audio>
        </div>
      `;
    }
    return `
      <a class="fb-attach fb-attach-file" href="${attachment.url}" target="_blank" rel="noopener noreferrer">
        <i class="feather icon-file"></i>
        <span>${escapeHtml(attachment.name || 'Datei öffnen')}</span>
      </a>
    `;
  }

  /* ---------------------------------------------
   * Sender resolve (fix: group messages show USER name, not group name)
   * ------------------------------------------- */
  function resolveUserFromContacts(uid) {
    const id = Number(uid || 0);
    if (!id) return null;
    return state.contacts.find((c) => c.type === 'user' && Number(c.id) === id) || null;
  }
  function resolveUserDisplayName(uid) {
    return resolveUserFromContacts(uid)?.displayName || null;
  }
  function resolveUserAvatar(uid) {
    const u = resolveUserFromContacts(uid);
    return u?.image || u?.avatar || null;
  }

  function getSenderName(contact, msg, isMe) {
    if (isMe) return me.name;

    const uid = Number(
      msg.from_user_id ||
      msg.from_user?.id ||
      msg.user_id ||
      msg.sender_id ||
      msg.sender_user_id ||
      0
    );

    const emp = msg.from_employee || msg.employee || msg.sender_employee || null;
    const u   = msg.from_user || msg.user || msg.sender_user || null;

    const empName =
      emp?.display_name ||
      (emp?.name || emp?.lastname ? `${emp?.name ?? ''} ${emp?.lastname ?? ''}`.trim() : null);

    const userEmployeeName =
      u?.employee?.display_name ||
      (u?.employee?.name || u?.employee?.lastname
        ? `${u.employee?.name ?? ''} ${u.employee?.lastname ?? ''}`.trim()
        : null);

    const userDirectName =
      u?.display_name ||
      u?.full_name ||
      (u?.name && u?.lastname ? `${u.name} ${u.lastname}`.trim() : null) ||
      (String(u?.name || '').trim() || null);

    const contactsName = resolveUserDisplayName(uid);

    // IMPORTANT: group should NOT fallback to contact.displayName (that's the group name)
    if (contact.type === 'group') {
      return empName || userEmployeeName || userDirectName || contactsName || msg.sender_name || 'Unbekannt';
    }

    return empName || userEmployeeName || userDirectName || contactsName || msg.sender_name || contact.displayName || 'Unbekannt';
  }

  function getSenderAvatar(contact, msg, isMe) {
    if (isMe) return null;

    const uid = Number(
      msg.from_user_id ||
      msg.from_user?.id ||
      msg.user_id ||
      msg.sender_id ||
      msg.sender_user_id ||
      0
    );

    const emp = msg.from_employee || msg.employee || msg.sender_employee || null;
    const u   = msg.from_user || msg.user || msg.sender_user || null;

    const img =
      emp?.image ||
      u?.employee?.image ||
      u?.avatar ||
      u?.image ||
      msg.sender_avatar ||
      resolveUserAvatar(uid) ||
      null;

    if (img) return img;
    return (contact.type === 'user') ? (contact.image || contact.avatar || null) : null;
  }

  /* ---------------------------------------------
   * Attachments
   * ------------------------------------------- */
  function toPublicUrl(pathOrUrl) {
    if (!pathOrUrl) return '';
    const v = String(pathOrUrl);

    if (/^https?:\/\//i.test(v)) return v;
    if (v.startsWith('/')) return v;

    if (v.startsWith('storage/')) return `/${v}`;
    return `/storage/${v}`;
  }

  function getAttachment(msg) {
    const type = String(msg.type || msg.message_type || '').toLowerCase();
    const mime = String(msg.mime || msg.file_mime || msg.mimetype || '').toLowerCase();

    const url =
      msg.file_url ||
      msg.media_url ||
      msg.voice_url ||
      msg.image_url ||
      msg.attachment_url ||
      msg.url ||
      msg.file_path ||
      msg.path ||
      msg.file;

    const name =
      msg.file_name ||
      msg.original_name ||
      msg.filename ||
      msg.name;

    const resolved = toPublicUrl(url);
    if (!resolved) return null;

    const isImage =
      type === 'image' ||
      mime.startsWith('image/') ||
      /\.(png|jpe?g|gif|webp|bmp|svg)$/i.test(resolved);

    const isVoice =
      type === 'voice' ||
      type === 'audio' ||
      mime.startsWith('audio/') ||
      /\.(webm|mp3|wav|m4a|ogg)$/i.test(resolved);

    if (isImage) return { kind: 'image', url: resolved, name, mime };
    if (isVoice) return { kind: 'voice', url: resolved, name: name || 'Sprachnachricht', mime };
    if (type === 'file' || url) return { kind: 'file', url: resolved, name: name || 'Datei', mime };

    return null;
  }

  function scrollToBottom(key) {
    const body = $(`#chat-body-${key}`);
    if (body) body.scrollTop = body.scrollHeight;
  }

  /* ---------------------------------------------
   * Message actions: menu / edit / delete
   * ------------------------------------------- */
  function closeAllMenus(key) {
    const body = $(`#chat-body-${key}`);
    if (!body) return;
    body.querySelectorAll('[data-fb-msg-menu].is-open').forEach((m) => m.classList.remove('is-open'));
  }

  function onBodyClick(key, contact, e) {
    const row = e.target.closest('.fb-msg-row');
    if (!row) return;

    const mid = row.dataset.msgId;
    if (!mid) return;

    if (e.target.closest('[data-fb-msg-menu-btn]')) {
      const menu = row.querySelector('[data-fb-msg-menu]');
      if (!menu) return;
      const isOpen = menu.classList.contains('is-open');
      closeAllMenus(key);
      menu.classList.toggle('is-open', !isOpen);
      return;
    }

    if (e.target.closest('[data-fb-edit]')) {
      e.preventDefault();
      closeAllMenus(key);
      beginEdit(key, mid, row);
      return;
    }

    if (e.target.closest('[data-fb-delete]')) {
      e.preventDefault();
      closeAllMenus(key);
      deleteMessage(key, mid);
      return;
    }
  }

  function beginEdit(key, msgId, rowEl) {
    const input = $(`#chat-win-${key} input.fb-chat-input`);
    if (!input) return;

    const textEl = rowEl.querySelector('.fb-msg-text');
    const currentText = textEl ? textEl.textContent.trim() : '';

    state.editing.set(key, { msgId: String(msgId), originalText: currentText });

    input.value = currentText;
    input.classList.add('is-editing');
    input.focus();
    input.placeholder = 'Bearbeiten… (Enter = speichern, Esc = abbrechen)';
  }

  function cancelEdit(key) {
    const input = $(`#chat-win-${key} input.fb-chat-input`);
    if (!input) return;

    if (!state.editing.has(key)) return;

    state.editing.delete(key);
    input.classList.remove('is-editing');
    input.placeholder = 'Nachricht...';
    input.value = '';
  }

  async function updateMessageOnServer(msgId, newText) {
    const url = typeof routes.update === 'function' ? routes.update(msgId) : String(routes.update);

    try {
      const res = await fetch(url, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
        credentials: 'same-origin',
        body: JSON.stringify({ message: newText }),
      });
      if (res.ok) return await safeJson(res);
    } catch (_) {}

    const res2 = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      },
      credentials: 'same-origin',
      body: JSON.stringify({ _method: 'PUT', message: newText }),
    });
    return await safeJson(res2);
  }

  async function deleteMessageOnServer(msgId) {
    const url = typeof routes.delete === 'function' ? routes.delete(msgId) : String(routes.delete);

    try {
      const res = await fetch(url, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
        credentials: 'same-origin',
      });
      if (res.ok) return await safeJson(res);
    } catch (_) {}

    const res2 = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      },
      credentials: 'same-origin',
      body: JSON.stringify({ _method: 'DELETE' }),
    });
    return await safeJson(res2);
  }

  async function deleteMessage(key, msgId) {
    const body = $(`#chat-body-${key}`);
    const row = body?.querySelector(`[data-msg-id="${CSS.escape(String(msgId))}"]`);
    if (!row) return;

    const textEl = row.querySelector('.fb-msg-text');
    if (textEl) textEl.innerHTML = `<i class="italic text-sm text-gray-500">Diese Nachricht wurde gelöscht</i>`;

    try { await deleteMessageOnServer(msgId); }
    catch (e) { console.error(e); }
  }

  /* ---------------------------------------------
   * Send text (NEW or EDIT) + send file/voice
   * ------------------------------------------- */
  async function sendText(key, inputEl) {
    const input = inputEl || $(`#chat-win-${key} input.fb-chat-input`);
    if (!input) return;

    const text = input.value.trim();
    if (!text) return;

    const contact = state.activeChats.find((c) => c.key === key);
    if (!contact) return;

    const edit = state.editing.get(key);
    if (edit?.msgId) {
      input.value = '';
      input.classList.remove('is-editing');
      input.placeholder = 'Nachricht...';
      state.editing.delete(key);

      try {
        const data = await updateMessageOnServer(edit.msgId, text);
        const updated = data?.message || data?.data || data || null;
        if (updated) {
          upsertMessage(contact, updated);
          scrollToBottom(key);
        } else {
          const body = $(`#chat-body-${key}`);
          const row = body?.querySelector(`[data-msg-id="${CSS.escape(String(edit.msgId))}"]`);
          const el = row?.querySelector('.fb-msg-text');
          if (el) el.innerHTML = linkifyEscaped(escapeHtml(text));
        }
      } catch (e) {
        console.error(e);
      }
      return;
    }

    input.value = '';

    const payload = {
      message: text,
      type: 'text',
      to_user_id: contact.type === 'user' ? contact.id : null,
      group_id:  contact.type === 'group' ? contact.id : null,
    };

    try {
      const res = await fetch(routes.send, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
        credentials: 'same-origin',
        body: JSON.stringify(payload),
      });
      const data = await safeJson(res);
      if (data.message) {
        upsertMessage(contact, data.message);
        scrollToBottom(key);
      }
    } catch (e) {
      console.error(e);
    }
  }

  async function sendFile(key, file) {
    const contact = state.activeChats.find((c) => c.key === key);
    if (!contact) return;

    const fd = new FormData();
    fd.append('file', file);
    fd.append('type', file.type?.startsWith('image/') ? 'image' : 'file');
    fd.append('message', '');
    if (contact.type === 'user')  fd.append('to_user_id', String(contact.id));
    if (contact.type === 'group') fd.append('group_id',  String(contact.id));

    try {
      const res = await fetch(routes.send, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        credentials: 'same-origin',
        body: fd,
      });
      const data = await safeJson(res);
      if (data.message) {
        upsertMessage(contact, data.message);
        scrollToBottom(key);
      }
    } catch (e) {
      console.error(e);
    }
  }

  async function sendVoice(key, blob) {
    const contact = state.activeChats.find((c) => c.key === key);
    if (!contact) return;

    const file = new File([blob], `voice-${Date.now()}.webm`, { type: blob.type || 'audio/webm' });

    const fd = new FormData();
    fd.append('file', file);
    fd.append('voice', file);
    fd.append('audio', file);

    fd.append('type', 'voice');
    fd.append('message', '');
    if (contact.type === 'user')  fd.append('to_user_id', String(contact.id));
    if (contact.type === 'group') fd.append('group_id',  String(contact.id));

    try {
      const res = await fetch(routes.send, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        credentials: 'same-origin',
        body: fd,
      });
      const data = await safeJson(res);
      if (data.message) {
        upsertMessage(contact, data.message);
        scrollToBottom(key);
      }
    } catch (e) {
      console.error(e);
    }
  }

  /* ---------------------------------------------
   * Voice recording
   * ------------------------------------------- */
  async function toggleVoiceRecording(key, micBtn) {
    if (!('MediaRecorder' in window) || !navigator.mediaDevices?.getUserMedia) {
      alert('Sprachnachrichten sind in diesem Browser nicht verfügbar.');
      return;
    }

    if (state.recorders.has(key)) {
      stopRecorderIfAny(key);
      micBtn?.classList.remove('is-recording');
      const icon = micBtn?.querySelector('i');
      icon?.classList.remove('icon-square');
      icon?.classList.add('icon-mic');
      return;
    }

    try {
      const stream = await navigator.mediaDevices.getUserMedia({ audio: true });

      const preferred = [
        'audio/webm;codecs=opus',
        'audio/webm',
        'audio/ogg;codecs=opus',
      ].find((t) => window.MediaRecorder?.isTypeSupported?.(t));

      const rec = new MediaRecorder(stream, preferred ? { mimeType: preferred } : undefined);

      const chunks = [];
      rec.ondataavailable = (e) => { if (e.data?.size) chunks.push(e.data); };

      rec.onstop = () => {
        stream.getTracks().forEach((t) => t.stop());
        const blob = new Blob(chunks, { type: rec.mimeType || 'audio/webm' });
        if (blob.size) sendVoice(key, blob);
      };

      state.recorders.set(key, { rec, stream });

      micBtn?.classList.add('is-recording');
      const icon = micBtn?.querySelector('i');
      icon?.classList.remove('icon-mic');
      icon?.classList.add('icon-square');

      rec.start();
    } catch (e) {
      console.error(e);
    }
  }

  function stopRecorderIfAny(key) {
    const r = state.recorders.get(key);
    if (!r) return;

    try { if (r.rec && r.rec.state !== 'inactive') r.rec.stop(); } catch (_) {}
    try { r.stream?.getTracks()?.forEach((t) => t.stop()); } catch (_) {}

    state.recorders.delete(key);
  }

  /* ---------------------------------------------
   * Realtime (Echo)
   * ------------------------------------------- */
  function initEchoListeners() {
    if (!window.Echo) {
      setTimeout(initEchoListeners, 500);
      return;
    }

    window.Echo.private(`chat.user.${me.id}`)
      .listen('.message-sent', (e) => {
        const isGroup = !!e.group_id;

        let key = null;
        if (isGroup) {
          key = `g-${e.group_id}`;
        } else {
          const fromId = Number(e.from_user_id || e.from_user?.id || 0);
          const toId   = Number(e.to_user_id   || e.to_user?.id   || 0);
          const partner = (fromId === me.id) ? toId : fromId;
          if (partner) key = `u-${partner}`;
        }
        if (!key) return;

        const open = state.activeChats.find((c) => c.key === key);

        if (open) {
          upsertMessage(open, e);
          scrollToBottom(key);

          const win = $(`#chat-win-${key}`);
          if (win?.classList.contains('minimized')) {
            const h = win.querySelector('.fb-chat-header');
            h?.classList.add('flash');
            setTimeout(() => h?.classList.remove('flash'), 3000);
          }
        } else {
          const contact = state.contacts.find((c) => c.key === key);
          if (contact) {
            contact.unread = (contact.unread || 0) + 1;
            renderSidebar();
          }
        }
      });

    window.Echo.join('online')
      .here((users) => (users || []).forEach((u) => updateStatus(u.id, true)))
      .joining((u) => updateStatus(u.id, true))
      .leaving((u) => updateStatus(u.id, false));
  }

  function updateStatus(uid, status) {
    const c = state.contacts.find((x) => x.type === 'user' && Number(x.id) === Number(uid));
    if (c) {
      c.online = !!status;
      renderSidebar();
    }
  }

  /* ---------------------------------------------
   * Safe JSON helper
   * ------------------------------------------- */
  async function safeJson(res) {
    const ct = (res.headers.get('content-type') || '').toLowerCase();
    const text = await res.text();

    if (!res.ok) throw new Error(`HTTP ${res.status}: ${text.slice(0, 250)}`);
    if (!ct.includes('application/json')) throw new Error(`Expected JSON, got "${ct}". Body: ${text.slice(0, 250)}`);

    return JSON.parse(text || 'null');
  }

  /* ---------------------------------------------
   * Start
   * ------------------------------------------- */
  document.addEventListener('click', (e) => {
    if (e.target.closest('.fb-msg-menu') || e.target.closest('[data-fb-msg-menu-btn]')) return;
    state.activeChats.forEach((c) => closeAllMenus(c.key));
  });

  if (overlayEl) overlayEl.addEventListener('click', () => toggleSidebar(false));
  loadContacts();
  initEchoListeners();
})();
</script>


@php
$activityFilter = auth()->user()->activityFilter;

$activityFilterPayload = [
    'customers' => $activityFilter->customer_ids ?? [],
    'employees' => $activityFilter->employee_ids ?? [],
    'products' => $activityFilter->product_ids ?? [],
    'is_muted' => (bool) ($activityFilter->is_muted ?? false),
    'types' => $activityFilter->notification_types ?? [],
];
@endphp

<script type="module">
    window.userActivityFilters = @json($activityFilterPayload);

    window.activityState = {
        page: 1,
        per_page: 20,
        q: '',
        mode: 'active', // active | archive
        pagination: {
            current_page: 1,
            has_more_pages: false,
            next_page_url: null,
            prev_page_url: null
        }
    };

    let msCustomers = null;
    let msEmployees = null;
    let msProducts = null;
    let msTypes = null;

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
    function csrfToken() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    function refreshFeatherIcons() {
        if (window.feather) {
            window.feather.replace();
        }
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getCurrentActivityRequestData() {
        return {
            customers: msCustomers?.getValues?.() || [],
            employees: msEmployees?.getValues?.() || [],
            products: msProducts?.getValues?.() || [],
            types: msTypes?.getValues?.() || [],
            q: window.activityState.q || '',
            page: window.activityState.page || 1,
            per_page: window.activityState.per_page || 20,
            mode: window.activityState.mode || 'active'
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Toast
    |--------------------------------------------------------------------------
    */
    window.showActivityToast = function(title, msg, kind = 'info') {
        let wrap = document.getElementById('oc-toast-wrap');

        if (!wrap) {
            wrap = document.createElement('div');
            wrap.id = 'oc-toast-wrap';
            wrap.className = 'oc-toast-wrap';
            document.body.appendChild(wrap);
        }

        const icons = {
            ok: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5"/></svg>`,
            bad: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`,
            info: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`
        };

        const el = document.createElement('div');
        el.className = 'oc-toast';

        el.innerHTML = `
            <div class="oc-toast-ic ${escapeHtml(kind)}">${icons[kind] || icons.info}</div>
            <div style="flex:1;">
                <p class="oc-toast-ttl">${escapeHtml(title)}</p>
                <p class="oc-toast-msg">${msg}</p>
            </div>
            <button type="button" class="oc-toast-x" onclick="this.parentElement.remove()">×</button>
        `;

        wrap.appendChild(el);

        setTimeout(() => {
            try {
                el.remove();
            } catch (e) {}
        }, 6000);
    };

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    */
    window.toggleActivitySidebar = function() {
        const sidebar = document.getElementById('activitySidebar');
        const backdrop = document.getElementById('activityBackdrop');
        const badge = document.getElementById('activityBadge');

        if (!sidebar || !backdrop) return;

        if (sidebar.style.right === '0px') {
            sidebar.style.right = '-430px';
            backdrop.style.opacity = '0';
            backdrop.style.visibility = 'hidden';
        } else {
            sidebar.style.right = '0px';
            backdrop.style.opacity = '1';
            backdrop.style.visibility = 'visible';

            if (badge) {
                badge.style.display = 'none';
                badge.innerText = '0';
            }

            fetchHistoricalActivities();
        }
    };

    window.toggleActivityFilters = function() {
        $('#activityFilterSection').slideToggle(250);
        $('.activity-filter-btn').toggleClass('active');
    };

    /*
    |--------------------------------------------------------------------------
    | Custom Multi Select
    |--------------------------------------------------------------------------
    */
    class CustomMultiSelect {
        constructor(wrapperId, initialValues = []) {
            this.wrapper = document.getElementById(wrapperId);

            if (!this.wrapper) {
                this.header = null;
                this.search = null;
                this.dropdown = null;
                this.options = [];
                this.checkboxes = [];
                return;
            }

            this.header = this.wrapper.querySelector('.sa-ms-header');
            this.search = this.wrapper.querySelector('.sa-ms-search');
            this.dropdown = this.wrapper.querySelector('.sa-ms-dropdown');
            this.options = Array.from(this.wrapper.querySelectorAll('.sa-ms-option'));
            this.checkboxes = Array.from(this.wrapper.querySelectorAll('input[type="checkbox"]'));

            this.initEvents();

            if (Array.isArray(initialValues) && initialValues.length > 0) {
                this.setValues(initialValues);
            } else {
                this.renderChips();
            }
        }

        initEvents() {
            if (!this.header || !this.search || !this.dropdown) return;

            this.header.addEventListener('click', (e) => {
                if (e.target.classList.contains('sa-ms-chip-close')) return;

                const isOpen = this.dropdown.classList.contains('show');

                document.querySelectorAll('.sa-ms-dropdown').forEach(d => d.classList.remove('show'));
                document.querySelectorAll('.sa-ms-header').forEach(h => h.classList.remove('active'));

                if (!isOpen) {
                    this.dropdown.classList.add('show');
                    this.header.classList.add('active');
                    this.search.focus();
                }
            });

            this.search.addEventListener('input', (e) => {
                const term = String(e.target.value || '').toLowerCase();

                this.options.forEach(opt => {
                    const text = String(opt.querySelector('.sa-ms-option-text')?.innerText || '').toLowerCase();
                    opt.classList.toggle('hidden', !text.includes(term));
                });
            });

            this.checkboxes.forEach(cb => {
                cb.addEventListener('change', () => {
                    this.renderChips();

                    if (this.search) {
                        this.search.value = '';
                        this.search.focus();
                    }

                    this.options.forEach(o => o.classList.remove('hidden'));
                });
            });

            document.addEventListener('click', (e) => {
                if (!this.wrapper.contains(e.target)) {
                    this.dropdown.classList.remove('show');
                    this.header.classList.remove('active');

                    if (this.search) {
                        this.search.value = '';
                    }

                    this.options.forEach(o => o.classList.remove('hidden'));
                }
            });
        }

        setValues(valuesArr = []) {
            const normalized = valuesArr.map(v => String(v));

            this.checkboxes.forEach(cb => {
                cb.checked = normalized.includes(String(cb.value));
            });

            this.renderChips();
        }

        getValues() {
            return this.checkboxes
                .filter(cb => cb.checked)
                .map(cb => cb.value);
        }

        clear() {
            this.checkboxes.forEach(cb => {
                cb.checked = false;
            });

            if (this.search) {
                this.search.value = '';
            }

            this.options.forEach(o => o.classList.remove('hidden'));
            this.renderChips();
        }

        renderChips() {
            if (!this.header || !this.search) return;

            this.header.querySelectorAll('.sa-ms-chip').forEach(chip => chip.remove());

            let checkedCount = 0;

            this.checkboxes.forEach(cb => {
                if (!cb.checked) return;

                checkedCount++;

                const label = cb.getAttribute('data-label') || cb.value;
                const chip = document.createElement('span');
                chip.className = 'sa-ms-chip';
                chip.innerHTML = `${escapeHtml(label)} <span class="sa-ms-chip-close" data-val="${escapeHtml(cb.value)}">&times;</span>`;

                const closeBtn = chip.querySelector('.sa-ms-chip-close');

                if (closeBtn) {
                    closeBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        cb.checked = false;
                        this.renderChips();
                    });
                }

                this.header.insertBefore(chip, this.search);
            });

            this.search.placeholder = checkedCount > 0
                ? ''
                : (this.search.getAttribute('data-orig-placeholder') || 'Wählen...');
        }
    }

    function initMultiSelects() {
        document.querySelectorAll('.sa-ms-search').forEach(input => {
            input.setAttribute('data-orig-placeholder', input.placeholder);
        });

        msCustomers = new CustomMultiSelect('custom-select-customers', window.userActivityFilters.customers || []);
        msEmployees = new CustomMultiSelect('custom-select-employees', window.userActivityFilters.employees || []);
        msProducts = new CustomMultiSelect('custom-select-products', window.userActivityFilters.products || []);
        msTypes = new CustomMultiSelect('custom-select-types', window.userActivityFilters.types || []);

        $('#activityMuteToggle').prop('checked', !!window.userActivityFilters.is_muted);
    }

    /*
    |--------------------------------------------------------------------------
    | Activity Type / Icons
    |--------------------------------------------------------------------------
    */
    function getActivityType(data) {
        if (data?.notification_type) {
            return String(data.notification_type).toLowerCase();
        }

        const model = String(data?.model || data?.model_de || '').toLowerCase();

        if (model.includes('notiz')) return 'notes';
        if (model.includes('prozess')) return 'process';
        if (model.includes('ticket')) return 'ticket';
        if (model.includes('termin')) return 'appointment';
        if (model.includes('kunde')) return 'customer';
        if (model.includes('objekt')) return 'address';

        return 'general';
    }

    function getActivityIconAndKind(data) {
        let icon = 'edit-2';
        let kind = 'info';

        if (data?.action === 'created') {
            icon = 'plus';
            kind = 'ok';
        } else if (data?.action === 'deleted') {
            icon = 'trash-2';
            kind = 'bad';
        }

        return { icon, kind };
    }

    /*
    |--------------------------------------------------------------------------
    | Render Activity Card
    |--------------------------------------------------------------------------
    */
    function renderActivityCard(data, isLive = false) {
        const cName = data.customer_name || 'Unbekannter Kunde';
        const pName = data.product_name || 'Allgemein';
        const mName = data.model_de || 'System';
        const detail = data.detail_text || '';
        const creator = data.creator_name || 'System';
        const typeLabel = data.notification_type_label || data.notification_type || 'Allgemein';

        const { icon, kind } = getActivityIconAndKind(data);
        const safeData = JSON.stringify(data).replace(/'/g, '&apos;');

        const isArchiveMode = window.activityState?.mode === 'archive';

        const actionButtonHtml = isArchiveMode
            ? `
                <button type="button"
                        class="btn btn-sm btn-light activity-unread-btn"
                        data-id="${escapeHtml(data.id)}"
                        title="Zurück in Aktuell"
                        style="border-radius:10px; font-size:11px; font-weight:800; padding:5px 9px;">
                    <i class="feather icon-rotate-ccw"></i>
                </button>
            `
            : `
                <button type="button"
                        class="btn btn-sm btn-light activity-read-btn"
                        data-id="${escapeHtml(data.id)}"
                        title="Als gelesen markieren"
                        style="border-radius:10px; font-size:11px; font-weight:800; padding:5px 9px;">
                    <i class="feather icon-check"></i>
                </button>
            `;

        return `
            <article class="oc-card ${isLive ? 'flash-new' : ''} activity-card-item"
                     data-activity='${safeData}'>
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div class="oc-toast-ic ${kind}" style="width:32px; height:32px; font-size:14px;">
                            <i class="feather icon-${icon}"></i>
                        </div>

                        <div>
                            <h6 style="margin:0; font-size:13px; font-weight:800; color:#111827;">
                                ${escapeHtml(cName)}
                            </h6>
                            <div style="font-size:11px; color:#6b7280; margin-top:2px;">
                                ${escapeHtml(pName)}
                            </div>
                        </div>
                    </div>

                    <div style="display:flex; gap:6px; align-items:center; flex-wrap:wrap; justify-content:flex-end;">
                        <span class="oc-badge">${escapeHtml(mName)}</span>
                        <span class="oc-badge" style="background:#eef6ff; color:#1d4ed8;">
                            ${escapeHtml(typeLabel)}
                        </span>
                    </div>
                </div>

                <p style="font-size:13px; color:#374151; margin:0 0 12px 0; line-height:1.5; font-weight:500;">
                    ${escapeHtml(detail)}
                </p>

                <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #f3f4f6; padding-top:10px;">
                    <div style="font-size:11px; color:#6b7280; font-weight:600; display:flex; align-items:center; gap:4px;">
                        <i class="feather icon-user" style="font-size:13px;"></i>
                        ${escapeHtml(creator)}
                    </div>

                    <div style="display:flex; align-items:center; gap:8px;">
                        ${actionButtonHtml}

                        <div style="font-size:11px; color:#9ca3af; font-weight:600;">
                            ${escapeHtml(data.date || data.time || 'Jetzt')}
                        </div>
                    </div>
                </div>
            </article>
        `;
    }

    /*
    |--------------------------------------------------------------------------
    | Archive / Restore
    |--------------------------------------------------------------------------
    */
    function animateRemoveCard(cardEl, direction = 'right') {
        if (!cardEl) return;

        cardEl.style.transition = 'all .2s ease';
        cardEl.style.opacity = '0';
        cardEl.style.transform = direction === 'left'
            ? 'translateX(-20px)'
            : 'translateX(20px)';

        setTimeout(() => {
            cardEl.remove();

            if ($('#activityList .activity-card-item').length === 0) {
                fetchHistoricalActivities();
            }
        }, 220);
    }

    function markActivityAsRead(activityId, cardEl) {
        if (!activityId) return;

        $.ajax({
            url: `/api/live-activities/${activityId}/read`,
            method: 'POST',
            data: {
                _token: csrfToken()
            },
            success: function(response) {
                if (!response?.success) {
                    window.showActivityToast('Fehler', 'Aktivität konnte nicht archiviert werden.', 'bad');
                    return;
                }

                animateRemoveCard(cardEl, 'right');
                window.showActivityToast('Archiviert', 'Aktivität wurde als gelesen markiert.', 'ok');
            },
            error: function(xhr) {
                console.error(xhr.responseText || xhr);
                window.showActivityToast('Fehler', 'Aktivität konnte nicht archiviert werden.', 'bad');
            }
        });
    }

    function markActivityAsUnread(activityId, cardEl) {
        if (!activityId) return;

        $.ajax({
            url: `/api/live-activities/${activityId}/read`,
            method: 'DELETE',
            data: {
                _token: csrfToken()
            },
            success: function(response) {
                if (!response?.success) {
                    window.showActivityToast('Fehler', 'Aktivität konnte nicht wiederhergestellt werden.', 'bad');
                    return;
                }

                animateRemoveCard(cardEl, 'left');
                window.showActivityToast('Wiederhergestellt', 'Aktivität wurde zurück in Aktuell verschoben.', 'ok');
            },
            error: function(xhr) {
                console.error(xhr.responseText || xhr);
                window.showActivityToast('Fehler', 'Aktivität konnte nicht wiederhergestellt werden.', 'bad');
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Detail Modal
    |--------------------------------------------------------------------------
    */
    function translateActivityValue(field, value) {
        const val = String(value ?? '');

        const stageDE = {
            open: 'Offen',
            lead: 'Lead',
            offer: 'Verkauf',
            deal: 'Auftrag',
            project: 'Montage',
            completed: 'Abschluss',
            archive: 'Archiv',
            junk: 'Junk',
            cancel: 'Abgebrochen',
            reject: 'Junk',
            todo: 'Zu erledigen',
            done: 'Erledigt',
        };

        const workStatusDE = {
            playing: 'Läuft',
            paused: 'Pausiert',
            stopped: 'Gestoppt',
        };

        const statusDE = {
            open: 'Offen',
            todo: 'Zu erledigen',
            done: 'Erledigt',
            cancel: 'Abgebrochen',
            canceled: 'Abgebrochen',
            cancelled: 'Abgebrochen',
            active: 'Aktiv',
            inactive: 'Inaktiv',
            pending: 'Ausstehend',
            processing: 'In Bearbeitung',
            completed: 'Abgeschlossen',
        };

        const fieldLower = String(field || '').toLowerCase();

        if (fieldLower === 'stage') {
            return stageDE[val.toLowerCase()] || val || '-';
        }

        if (fieldLower === 'work_status') {
            return workStatusDE[val.toLowerCase()] || val || '-';
        }

        if (fieldLower === 'status') {
            return statusDE[val.toLowerCase()] || val || '-';
        }

        return val || '-';
    }

    function translateActivityField(field) {
        const labels = {
            status: 'Status',
            stage: 'Phase',
            work_status: 'Arbeitsstatus',
            description: 'Beschreibung',
            note: 'Notiz',
            title: 'Titel',
            name: 'Name',
            lastname: 'Nachname',
            firma: 'Firma',
            phone: 'Telefon',
            email: 'E-Mail',
            address: 'Adresse',
            city: 'Stadt',
            zip: 'PLZ',
            appointment_date: 'Termin',
            start_date: 'Startdatum',
            end_date: 'Enddatum',
            priority: 'Priorität',
        };

        return labels[field] || String(field || '-').replaceAll('_', ' ');
    }

    function renderActivityChanges(changes) {
        if (!changes || typeof changes !== 'object') {
            return `
                <div class="activity-change-list">
                    <div class="activity-change-head">Änderungen</div>
                    <div class="p-3 text-muted">Keine Änderungsdetails vorhanden.</div>
                </div>
            `;
        }

        let rows = '';

        Object.entries(changes).forEach(([field, value]) => {
            if (field === 'attributes' && value && typeof value === 'object') {
                Object.entries(value).forEach(([attrField, attrValue]) => {
                    rows += `
                        <div class="activity-change-row">
                            <div class="activity-change-field">${escapeHtml(translateActivityField(attrField))}</div>
                            <div>
                                <span class="activity-change-pill from">-</span>
                            </div>
                            <div>
                                <span class="activity-change-pill to">${escapeHtml(translateActivityValue(attrField, attrValue))}</span>
                            </div>
                        </div>
                    `;
                });

                return;
            }

            if (value && typeof value === 'object' && ('from' in value || 'to' in value)) {
                rows += `
                    <div class="activity-change-row">
                        <div class="activity-change-field">${escapeHtml(translateActivityField(field))}</div>
                        <div>
                            <span class="activity-change-pill from">${escapeHtml(translateActivityValue(field, value.from))}</span>
                        </div>
                        <div>
                            <span class="activity-change-pill to">${escapeHtml(translateActivityValue(field, value.to))}</span>
                        </div>
                    </div>
                `;

                return;
            }

            rows += `
                <div class="activity-change-row">
                    <div class="activity-change-field">${escapeHtml(translateActivityField(field))}</div>
                    <div>
                        <span class="activity-change-pill from">-</span>
                    </div>
                    <div>
                        <span class="activity-change-pill to">${escapeHtml(translateActivityValue(field, value))}</span>
                    </div>
                </div>
            `;
        });

        if (!rows) {
            rows = `<div class="p-3 text-muted">Keine Änderungsdetails vorhanden.</div>`;
        }

        return `
            <div class="activity-change-list">
                <div class="activity-change-head">
                    <i class="feather icon-repeat mr-50"></i> Änderungen
                </div>
                ${rows}
            </div>
        `;
    }

    window.closeActivityDetailModal = function() {
        const backdrop = document.getElementById('activityDetailModalBackdrop');
        const body = document.getElementById('activityDetailModalBody');

        if (backdrop) {
            backdrop.style.display = 'none';
        }

        if (body) {
            body.innerHTML = '';
        }
    };

    function renderActivityDetail(data) {
        const backdrop = document.getElementById('activityDetailModalBackdrop');
        const body = document.getElementById('activityDetailModalBody');
        const title = document.getElementById('activityDetailModalTitle');
        const subtitle = document.getElementById('activityDetailModalSubTitle');
        const iconWrap = document.getElementById('activityDetailModalIcon');

        if (!backdrop || !body) return;

        const { icon } = getActivityIconAndKind(data);

        if (title) {
            title.innerText = `${data.model_de || 'Aktivität'} ${data.action_de || data.action || ''}`;
        }

        if (subtitle) {
            subtitle.innerText = `${data.customer_name || 'Unbekannter Kunde'} · ${data.date || data.time || 'Jetzt'}`;
        }

        if (iconWrap) {
            iconWrap.innerHTML = `<i class="feather icon-${icon}"></i>`;
        }

        body.innerHTML = `
            <div class="activity-detail-grid">
                <div class="activity-detail-box">
                    <div class="activity-detail-label">Kunde</div>
                    <div class="activity-detail-value">${escapeHtml(data.customer_name || '-')}</div>
                </div>

                <div class="activity-detail-box">
                    <div class="activity-detail-label">Produkt</div>
                    <div class="activity-detail-value">${escapeHtml(data.product_name || '-')}</div>
                </div>

                <div class="activity-detail-box">
                    <div class="activity-detail-label">Modul</div>
                    <div class="activity-detail-value">${escapeHtml(data.model_de || data.model || '-')}</div>
                </div>

                <div class="activity-detail-box">
                    <div class="activity-detail-label">Aktion</div>
                    <div class="activity-detail-value">${escapeHtml(data.action_de || data.action || '-')}</div>
                </div>

                <div class="activity-detail-box">
                    <div class="activity-detail-label">Mitarbeiter</div>
                    <div class="activity-detail-value">${escapeHtml(data.creator_name || '-')}</div>
                </div>

                <div class="activity-detail-box">
                    <div class="activity-detail-label">Zeitpunkt</div>
                    <div class="activity-detail-value">${escapeHtml(data.date || data.time || '-')}</div>
                </div>
            </div>

            <div class="activity-detail-message">
                <div class="activity-detail-label">Beschreibung</div>
                <div style="font-size:14px; color:#374151; line-height:1.6; font-weight:600;">
                    ${escapeHtml(data.detail_text || '-')}
                </div>
            </div>

            ${renderActivityChanges(data.changes)}
        `;

        backdrop.style.display = 'flex';
        refreshFeatherIcons();
    }

    /*
    |--------------------------------------------------------------------------
    | Fetch / Pagination
    |--------------------------------------------------------------------------
    */
    function updatePaginationUI(meta = {}) {
        window.activityState.pagination = meta || {};

        $('#activityPageInfo').text(`Seite ${meta.current_page || 1}`);
        $('#activityPrevBtn').prop('disabled', !meta.prev_page_url);
        $('#activityNextBtn').prop('disabled', !meta.has_more_pages);
        $('#activityPageMeta').text(`${window.activityState.per_page} Einträge pro Seite`);
    }

    function fetchHistoricalActivities() {
        const list = $('#activityList');
        const modeLabel = window.activityState.mode === 'archive' ? 'Archiv' : 'Aktuell';

        list.html(`
            <div class="text-center mt-4">
                <i class="feather icon-loader spinner" style="font-size:24px;"></i>
                <div class="mt-2 text-muted" style="font-size:12px;">${modeLabel} wird geladen...</div>
            </div>
        `);

        refreshFeatherIcons();

        $.ajax({
            url: '/api/live-activities/recent',
            method: 'GET',
            data: getCurrentActivityRequestData(),
            success: function(response) {
                list.empty();

                const rows = response?.data || [];

                if (!rows.length) {
                    const emptyText = window.activityState.mode === 'archive'
                        ? 'Keine archivierten Aktivitäten gefunden.'
                        : 'Keine aktuellen Aktivitäten gefunden.';

                    list.html(`
                        <div id="emptyActivity" class="text-center text-muted mt-3" style="padding:30px 18px;">
                            <i class="feather ${window.activityState.mode === 'archive' ? 'icon-archive' : 'icon-radio'}"
                               style="font-size:2rem; opacity:.5;"></i>
                            <br>
                            <div class="mt-2" style="font-weight:800;">${emptyText}</div>
                        </div>
                    `);

                    updatePaginationUI(response?.pagination || {
                        current_page: 1,
                        has_more_pages: false
                    });

                    refreshFeatherIcons();
                    return;
                }

                rows.forEach(row => {
                    list.append(renderActivityCard(row, false));
                });

                updatePaginationUI(response?.pagination || {
                    current_page: 1,
                    has_more_pages: false
                });

                refreshFeatherIcons();
            },
            error: function(xhr) {
                console.error(xhr.responseText || xhr);
                list.html('<div class="text-danger text-center mt-3">Fehler beim Laden der Aktivitäten.</div>');
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Live Activity
    |--------------------------------------------------------------------------
    */
    function passesActivityFilter(data) {
        const prefs = window.userActivityFilters || {};

        const customers = (prefs.customers || []).map(String);
        const employees = (prefs.employees || []).map(String);
        const products = (prefs.products || []).map(String);
        const types = (prefs.types || []).map(String);

        const matchCustomer = customers.length === 0 || customers.includes(String(data.customer_id));
        const matchEmployee = employees.length === 0 || employees.includes(String(data.employee_id));
        const matchProduct = products.length === 0 || products.includes(String(data.product_id));
        const matchType = types.length === 0 || types.includes(getActivityType(data));

        return matchCustomer && matchEmployee && matchProduct && matchType;
    }

    window.processNewActivity = function(e, isLive = true) {
        const data = e?.logData || e;

        if (!data) return;
        if (isLive && !passesActivityFilter(data)) return;

        const sound = document.getElementById('activityNotificationSound');
        const isMuted = !!window.userActivityFilters?.is_muted;

        if (!isMuted && sound) {
            sound.currentTime = 0;
            sound.play().catch(() => {});
        }

        window.showActivityToast(
            `${data.model_de || 'System'} ${data.action_de || data.action || ''}`,
            `<b>${escapeHtml(data.customer_name || 'Unbekannter Kunde')}</b><br>${escapeHtml(data.detail_text || '')}`,
            data.action === 'deleted' ? 'bad' : (data.action === 'created' ? 'ok' : 'info')
        );

        if (window.activityState.mode === 'active') {
            $('#activityList').prepend(renderActivityCard(data, true));
        }

        const sidebar = document.getElementById('activitySidebar');

        if (sidebar && sidebar.style.right !== '0px') {
            const badge = document.getElementById('activityBadge');

            if (badge) {
                const count = parseInt(badge.innerText || '0', 10);
                badge.innerText = String(count + 1);
                badge.style.display = 'inline-block';
            }
        }

        refreshFeatherIcons();
    };

    /*
    |--------------------------------------------------------------------------
    | Search / Filters
    |--------------------------------------------------------------------------
    */
    function resetActivitySearch() {
        $('#activitySearchInput').val('');
        window.activityState.page = 1;
        window.activityState.q = '';
        fetchHistoricalActivities();
    }

    function clearActivityFilters() {
        msCustomers?.clear?.();
        msEmployees?.clear?.();
        msProducts?.clear?.();
        msTypes?.clear?.();
        $('#activityMuteToggle').prop('checked', false);
    }

    window.saveActivityFilters = function(event) {
        const btn = $(event.currentTarget);
        const originalHtml = btn.html();

        btn.html('<i class="feather icon-loader spinner"></i> Speichert...');
        refreshFeatherIcons();

        const filters = {
            customers: msCustomers?.getValues?.() || [],
            employees: msEmployees?.getValues?.() || [],
            products: msProducts?.getValues?.() || [],
            types: msTypes?.getValues?.() || [],
            is_muted: $('#activityMuteToggle').is(':checked') ? 1 : 0,
            _token: csrfToken()
        };

        $.post('/api/live-activities/save-filters', filters, function() {
            window.userActivityFilters = {
                customers: filters.customers,
                employees: filters.employees,
                products: filters.products,
                types: filters.types,
                is_muted: !!Number(filters.is_muted)
            };

            window.activityState.page = 1;
            fetchHistoricalActivities();

            btn.html('<i class="feather icon-check"></i> Filter angewendet');
            refreshFeatherIcons();

            setTimeout(() => {
                btn.html(originalHtml || '<i class="feather icon-save"></i> Filter anwenden');
                refreshFeatherIcons();
            }, 2000);

            document.querySelectorAll('.sa-ms-dropdown').forEach(d => d.classList.remove('show'));
            document.querySelectorAll('.sa-ms-header').forEach(h => h.classList.remove('active'));
        }).fail(function(xhr) {
            console.error(xhr.responseText || xhr);
            window.showActivityToast('Fehler', 'Filter konnten nicht gespeichert werden.', 'bad');

            btn.html(originalHtml || '<i class="feather icon-save"></i> Filter anwenden');
            refreshFeatherIcons();
        });
    };

    /*
    |--------------------------------------------------------------------------
    | Event Bindings
    |--------------------------------------------------------------------------
    */
    function bindActivityModeTabs() {
        $(document).on('click', '.activity-mode-tab', function() {
            const mode = $(this).data('activity-mode') || 'active';

            window.activityState.mode = mode;
            window.activityState.page = 1;

            $('.activity-mode-tab').removeClass('active');
            $(this).addClass('active');

            fetchHistoricalActivities();
        });
    }

    function bindSearchEvents() {
        $('#activitySearchBtn').on('click', function() {
            window.activityState.page = 1;
            window.activityState.q = $('#activitySearchInput').val().trim();
            fetchHistoricalActivities();
        });

        $('#activitySearchInput').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();

                window.activityState.page = 1;
                window.activityState.q = $(this).val().trim();

                fetchHistoricalActivities();
            }
        });

        $('#activitySearchResetBtn').on('click', function() {
            resetActivitySearch();
        });
    }

    function bindPaginationEvents() {
        $('#activityPrevBtn').on('click', function() {
            if (window.activityState.pagination.prev_page_url) {
                window.activityState.page = Math.max(1, window.activityState.page - 1);
                fetchHistoricalActivities();
            }
        });

        $('#activityNextBtn').on('click', function() {
            if (window.activityState.pagination.has_more_pages) {
                window.activityState.page += 1;
                fetchHistoricalActivities();
            }
        });
    }

    function bindDetailEvents() {
        $(document).on('click', '.activity-read-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const activityId = $(this).data('id');
            const card = this.closest('.activity-card-item');

            markActivityAsRead(activityId, card);
        });

        $(document).on('click', '.activity-unread-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const activityId = $(this).data('id');
            const card = this.closest('.activity-card-item');

            markActivityAsUnread(activityId, card);
        });

        $(document).on('click', '.activity-card-item', function() {
            const raw = $(this).attr('data-activity');

            if (!raw) return;

            try {
                const data = JSON.parse(raw.replace(/&apos;/g, "'"));
                renderActivityDetail(data);
            } catch (error) {
                console.error('Aktivitätsdaten konnten nicht gelesen werden.', error);
            }
        });

        $('#closeActivityDetailBtn').on('click', function() {
            $('#activityDetailPanel').hide();
            $('#activityDetailContent').empty();
        });

        $(document).on('click', '#activityDetailModalBackdrop', function(e) {
            if (e.target.id === 'activityDetailModalBackdrop') {
                closeActivityDetailModal();
            }
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                closeActivityDetailModal();
            }
        });
    }

    function bindFilterEvents() {
        $('#clearActivityFiltersBtn').on('click', function() {
            clearActivityFilters();
        });
    }

    function bindEchoEvents() {
        if (!window.Echo) return;

        window.Echo.private('company-activities')
            .listen('.activity.created', (e) => {
                window.processNewActivity(e, true);
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Init
    |--------------------------------------------------------------------------
    */
    $(document).ready(function() {
        initMultiSelects();

        bindSearchEvents();
        bindPaginationEvents();
        bindDetailEvents();
        bindFilterEvents();
        bindActivityModeTabs();
        bindEchoEvents();

        fetchHistoricalActivities();
        refreshFeatherIcons();
    });
</script>
<style>
    @keyframes flashActivity {
        0% { background-color: var(--danger-color); }
        100% { background-color: #fff; }
    }
    .bg-light-success { background-color: rgba(40, 199, 111, 0.12); }
    .bg-light-danger { background-color: rgba(234, 84, 85, 0.12); }
    .bg-light-primary { background-color: rgba(115, 103, 240, 0.12); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const resetBtn = document.getElementById('activitySearchResetBtn');
    const clearFiltersBtn = document.getElementById('clearActivityFiltersBtn');

    resetBtn?.addEventListener('click', function () {
        const input = document.getElementById('activitySearchInput');
        if (input) input.value = '';
        if (window.activityState) {
            window.activityState.q = '';
            window.activityState.page = 1;
        }
        if (typeof fetchHistoricalActivities === 'function') {
            fetchHistoricalActivities();
        }
    });

    clearFiltersBtn?.addEventListener('click', function () {
        document.querySelectorAll('#activityFilterSection input[type="checkbox"]').forEach(cb => {
            cb.checked = false;
        });

        document.querySelectorAll('#activityFilterSection .sa-ms-chip').forEach(chip => chip.remove());
        document.querySelectorAll('#activityFilterSection .sa-ms-search').forEach(input => {
            input.value = '';
            input.placeholder = input.getAttribute('data-orig-placeholder') || input.placeholder;
        });

        if (window.msCustomers?.renderChips) window.msCustomers.renderChips();
        if (window.msEmployees?.renderChips) window.msEmployees.renderChips();
        if (window.msProducts?.renderChips) window.msProducts.renderChips();
    });
});
</script>

<script type="module">
(function () {
    'use strict';

    const routes = {
        list: @json(route('admin.overdue.reports.notifications')),
        read: (id) => @json(url('/admin/overdue-report-notifications')) + '/' + id + '/read',
        readAll: @json(route('admin.overdue.reports.notifications.readAll')),
    };

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

    const dom = {
        badge: document.getElementById('reportNotifyBadge'),
        list: document.getElementById('reportNotifyList'),
        markAll: document.getElementById('reportMarkAllReadBtn'),
        sound: document.getElementById('notificationSound') || document.getElementById('activityNotificationSound'),

        modalBackdrop: document.getElementById('saReportModalBackdrop'),
        modalTitle: document.getElementById('saReportModalTitle'),
        modalSub: document.getElementById('saReportModalSub'),
        modalEmployee: document.getElementById('saReportModalEmployee'),
        modalType: document.getElementById('saReportModalType'),
        modalTarget: document.getElementById('saReportModalTarget'),
        modalTime: document.getElementById('saReportModalTime'),
        modalReport: document.getElementById('saReportModalReport'),
    };

    let reports = [];

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function typeLabel(type) {
        const map = {
            inquiry: 'Anfrage',
            task: 'Aufgabe',
            appointment: 'Termin',
            ticket: 'Ticket',
            lead: 'Lead',
        };

        return map[type] || type || 'Bericht';
    }

    function updateBadge(count) {
        if (!dom.badge) return;

        count = Number(count || 0);

        if (count > 0) {
            dom.badge.innerText = count > 99 ? '99+' : String(count);
            dom.badge.style.display = 'inline-block';
        } else {
            dom.badge.innerText = '0';
            dom.badge.style.display = 'none';
        }
    }

    function renderList() {
        if (!dom.list) return;

        if (!reports.length) {
            dom.list.innerHTML = `<div class="sa-report-empty">Keine neuen Berichte.</div>`;
            updateBadge(0);
            return;
        }

        const unreadCount = reports.filter(x => x.is_unread).length;
        updateBadge(unreadCount);

        dom.list.innerHTML = reports.map(item => {
            return `
                <article class="sa-report-item ${item.is_unread ? 'unread' : ''}" data-report-id="${escapeHtml(item.id)}">
                    <img src="${escapeHtml(item.employee_image || '')}" class="sa-report-avatar" alt="">

                    <div>
                        <div class="sa-report-title">
                            ${escapeHtml(item.employee || 'Unbekannt')} hat berichtet
                        </div>

                        <div class="sa-report-meta">
                            <span>${escapeHtml(typeLabel(item.type))}</span>
                            <span>•</span>
                            <span>${escapeHtml(item.title || ('#' + item.target_id))}</span>
                            <span>•</span>
                            <span>${escapeHtml(item.created_human || '')}</span>
                        </div>

                        <div class="sa-report-text">
                            ${escapeHtml(item.report || '')}
                        </div>
                    </div>
                </article>
            `;
        }).join('');

        if (window.feather) {
            window.feather.replace();
        }
    }

    async function fetchReports() {
        try {
            const res = await fetch(routes.list, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!res.ok) throw new Error('HTTP ' + res.status);

            const data = await res.json();

            reports = data.items || [];
            renderList();
        } catch (e) {
            console.error('Report notifications failed:', e);
        }
    }

    async function markRead(id) {
        try {
            await fetch(routes.read(id), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                },
                credentials: 'same-origin',
            });

            reports = reports.map(item => {
                if (String(item.id) === String(id)) {
                    item.is_unread = false;
                    item.read_at = new Date().toISOString();
                }

                return item;
            });

            renderList();
        } catch (e) {
            console.error('Mark report read failed:', e);
        }
    }

    async function markAllRead() {
        try {
            await fetch(routes.readAll, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                },
                credentials: 'same-origin',
            });

            reports = reports.map(item => {
                item.is_unread = false;
                item.read_at = new Date().toISOString();
                return item;
            });

            renderList();
        } catch (e) {
            console.error('Mark all reports read failed:', e);
        }
    }

    window.openSaReportModal = function(item) {
        if (!item || !dom.modalBackdrop) return;

        dom.modalTitle.innerText = 'Bericht';
        dom.modalSub.innerText = `${item.employee || 'Unbekannt'} · ${item.created_human || ''}`;
        dom.modalEmployee.innerText = item.employee || 'Unbekannt';
        dom.modalType.innerText = typeLabel(item.type);
        dom.modalTarget.innerText = item.title || ('#' + item.target_id);
        dom.modalTime.innerText = item.created_at || '—';
        dom.modalReport.innerText = item.report || '—';

        dom.modalBackdrop.style.display = 'flex';

        if (window.feather) {
            window.feather.replace();
        }
    };

    window.closeSaReportModal = function() {
        if (dom.modalBackdrop) {
            dom.modalBackdrop.style.display = 'none';
        }
    };

    dom.list?.addEventListener('click', function(e) {
        const card = e.target.closest('.sa-report-item');
        if (!card) return;

        const id = card.dataset.reportId;
        const item = reports.find(x => String(x.id) === String(id));

        if (!item) return;

        window.openSaReportModal(item);
        markRead(id);
    });

    dom.markAll?.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        markAllRead();
    });

    dom.modalBackdrop?.addEventListener('click', function(e) {
        if (e.target === dom.modalBackdrop) {
            window.closeSaReportModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.closeSaReportModal();
        }
    });

    function pushLiveReport(payload) {
        const item = {
            id: payload.id,
            type: payload.type,
            target_id: payload.target_id,
            title: payload.title || 'Neuer Bericht',
            report: payload.report || '',
            employee_id: payload.employee_id,
            employee: payload.employee || 'Unbekannt',
            employee_image: payload.employee_image || @json(asset('images/gender/male.png')),
            created_at: payload.created_at,
            created_human: payload.created_human || 'gerade eben',
            read_at: null,
            is_unread: true,
        };

        reports.unshift(item);
        reports = reports.slice(0, 15);

        renderList();

        if (dom.sound) {
            dom.sound.currentTime = 0;
            dom.sound.play().catch(() => {});
        }
    }

    if (window.Echo) {
        window.Echo.channel('overdue-reports')
            .listen('.overdue.report.created', function(payload) {
                pushLiveReport(payload);
            });
    }

    fetchReports();

    setInterval(fetchReports, 60000);
})();
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const reportMenu = document.getElementById('reportNotifyMenu');
    const reportTrigger = document.getElementById('reportNotifyTrigger');
    const reportDropdown = document.getElementById('reportNotifyDropdown');

    if (!reportMenu || !reportTrigger || !reportDropdown) return;

    reportTrigger.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        reportMenu.classList.toggle('is-open');
    });

    reportDropdown.addEventListener('click', function (e) {
        e.stopPropagation();
    });

    document.addEventListener('click', function () {
        reportMenu.classList.remove('is-open');
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            reportMenu.classList.remove('is-open');
        }
    });
});
</script>

@if(auth()->check())
<div id="mainAppointmentReminderBackdrop" class="sa-appointment-reminder-backdrop" style="display:none;">
    <div class="sa-appointment-reminder-modal" role="dialog" aria-modal="true">
        <div class="sa-appointment-reminder-head">
            <div class="sa-appointment-reminder-icon">
                <i class="feather icon-calendar"></i>
            </div>

            <div>
                <h4>Termin-Erinnerung</h4>
                <p>Dein Termin beginnt in wenigen Minuten.</p>
            </div>

            <button type="button" class="sa-appointment-reminder-close" id="closeAppointmentReminderBtn">
                <i class="feather icon-x"></i>
            </button>
        </div>

        <div class="sa-appointment-reminder-body">
            <div class="sa-appointment-reminder-alert">
                <i class="feather icon-clock"></i>
                <span id="appointmentReminderTime">—</span>
            </div>

            <h3 id="appointmentReminderTitle">Termin</h3>

            <div class="sa-appointment-reminder-grid">
                <div class="sa-appointment-reminder-box">
                    <label>Kunde</label>
                    <div id="appointmentReminderCustomer">—</div>
                </div>

                <div class="sa-appointment-reminder-box">
                    <label>Typ</label>
                    <div id="appointmentReminderType">—</div>
                </div>

                <div class="sa-appointment-reminder-box sa-appointment-reminder-box-full">
                    <label>Adresse</label>
                    <div id="appointmentReminderAddress">—</div>
                </div>

                <div class="sa-appointment-reminder-box sa-appointment-reminder-box-full">
                    <label>Beschreibung / Notiz</label>
                    <div id="appointmentReminderNote">—</div>
                </div>
            </div>
        </div>

        <div class="sa-appointment-reminder-foot">
            <button type="button" class="btn btn-light" id="appointmentReminderLaterBtn">
                Später erinnern
            </button>

            <a href="#" class="btn btn-primary" id="appointmentReminderOpenBtn">
                Termin öffnen
            </a>

            <button type="button" class="btn btn-success" id="appointmentReminderDoneBtn">
                Verstanden
            </button>
        </div>
    </div>
</div>
@endif

@if(auth()->check())
<script type="module">
(function () {
    'use strict';

    const employeeId = document.querySelector('meta[name="employee-id"]')?.content;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    if (!employeeId) return;

    const routes = {
        upcoming: @json(route('main-appointments.reminders.upcoming')),
        seen: (id) => @json(url('/main-appointments')) + '/' + id + '/reminders/seen',
    };

    const dom = {
        backdrop: document.getElementById('mainAppointmentReminderBackdrop'),
        closeBtn: document.getElementById('closeAppointmentReminderBtn'),
        laterBtn: document.getElementById('appointmentReminderLaterBtn'),
        doneBtn: document.getElementById('appointmentReminderDoneBtn'),
        openBtn: document.getElementById('appointmentReminderOpenBtn'),

        title: document.getElementById('appointmentReminderTitle'),
        customer: document.getElementById('appointmentReminderCustomer'),
        address: document.getElementById('appointmentReminderAddress'),
        note: document.getElementById('appointmentReminderNote'),
        type: document.getElementById('appointmentReminderType'),
        time: document.getElementById('appointmentReminderTime'),

        sound: document.getElementById('appointmentReminderSound'),
    };

    let currentAppointment = null;
    let snoozedAppointments = JSON.parse(localStorage.getItem('saAppointmentReminderSnoozed') || '{}');

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function isSnoozed(appointmentId) {
        const until = Number(snoozedAppointments[String(appointmentId)] || 0);
        return until && Date.now() < until;
    }

    function snooze(appointmentId) {
        snoozedAppointments[String(appointmentId)] = Date.now() + (3 * 60 * 1000);
        localStorage.setItem('saAppointmentReminderSnoozed', JSON.stringify(snoozedAppointments));
    }

    function playSound() {
        if (!dom.sound) return;

        dom.sound.currentTime = 0;
        dom.sound.play().catch(() => {
            // Browser may block sound before first user interaction.
        });
    }

    function formatTime(item) {
        const start = item.start_time || '';
        const end = item.end_time || '';

        if (start && end) {
            return `${start} - ${end}`;
        }

        if (start) {
            return start;
        }

        return 'In 10 Minuten';
    }

    function openModal(item) {
        if (!dom.backdrop || !item?.id) return;

        if (isSnoozed(item.id)) return;

        currentAppointment = item;

        dom.title.innerHTML = escapeHtml(item.title || 'Termin');
        dom.customer.innerHTML = escapeHtml(item.customer_name || 'Unbekannter Kunde');
        dom.address.innerHTML = escapeHtml(item.address || 'Keine Adresse hinterlegt');
        dom.note.innerHTML = escapeHtml(item.note || 'Keine Notiz vorhanden');
        dom.type.innerHTML = escapeHtml(item.appointment_type || item.execution_type || 'Termin');
        dom.time.innerHTML = escapeHtml(formatTime(item));

        if (dom.openBtn) {
            dom.openBtn.href = item.url || '#';
        }

        dom.backdrop.style.display = 'flex';
        document.documentElement.style.overflow = 'hidden';

        playSound();

        if (window.feather) {
            window.feather.replace();
        }
    }

    function closeModal() {
        if (!dom.backdrop) return;

        dom.backdrop.style.display = 'none';
        document.documentElement.style.overflow = '';
    }

    async function markSeen() {
        if (!currentAppointment?.id) {
            closeModal();
            return;
        }

        try {
            await fetch(routes.seen(currentAppointment.id), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });
        } catch (error) {
            console.error('Appointment reminder seen failed:', error);
        }

        closeModal();
    }

    async function checkUpcomingAppointment() {
        try {
            const res = await fetch(routes.upcoming, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!res.ok) return;

            const data = await res.json();

            if (data?.has_reminder && data?.appointment) {
                openModal(data.appointment);
            }
        } catch (error) {
            console.error('Appointment reminder check failed:', error);
        }
    }

    dom.closeBtn?.addEventListener('click', function () {
        closeModal();
    });

    dom.doneBtn?.addEventListener('click', function () {
        markSeen();
    });

    dom.laterBtn?.addEventListener('click', function () {
        if (currentAppointment?.id) {
            snooze(currentAppointment.id);
        }

        closeModal();
    });

    dom.backdrop?.addEventListener('click', function (e) {
        if (e.target === dom.backdrop) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    if (window.Echo) {
        window.Echo.private(`employee-appointment.${employeeId}`)
            .listen('.main.appointment.reminder', function (payload) {
                openModal(payload);
            });
    }

    // Fallback: check every minute.
    checkUpcomingAppointment();
    setInterval(checkUpcomingAppointment, 30000);
})();
</script>
@endif


@stack('scripts')
@yield('script')


</body>

</html>