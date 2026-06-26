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
    <link rel="stylesheet" type="text/css" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/submenus.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/clock.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/right-sider.css') }}"> 
 
        <script src="https://unpkg.com/feather-icons"></script>

 
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

        .app-content .content {
            margin-right: 10px !important;
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
       margin-right: 10px !important;
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
        margin-left:auto; 
        margin-top:1%;
        background:#74b2d4 !important; 
        color:#ecf0f1 !important;
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
   /* Wrapper positioning */
.quick_sider_wrapper {
    position: relative;
    display: flex;
    align-items: center;
    height: 100%; /* Ensure wrapper matches navbar height */
}

/* The Strip Container */
.hover-quick-strip {
    position: absolute;
    top: 80%; /* Move it up slightly so it overlaps the navbar a bit */
    right: -10px; 
    width: 50px;
    background: #2c3e50;
    border-radius: 30px;
    padding: 15px 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.1);
    
    /* Hidden State */
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px) scale(0.95);
    pointer-events: none;
    
    transition: all 0.2s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    z-index: 1050;
}

/* THE BRIDGE: This invisible block fills the gap between the icon and the menu */
.hover-quick-strip::before {
    content: '';
    position: absolute;
    top: -30px; /* Extend invisible area upwards to catch the mouse */
    left: 0;
    width: 100%;
    height: 30px;
    background: transparent; /* Invisible */
}

/* THE HOVER LOGIC */
.quick_sider_wrapper:hover .hover-quick-strip {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
    pointer-events: auto;
}

/* Links inside the strip */
.hqs-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    color: rgba(255, 255, 255, 0.6);
    background: transparent;
    transition: all 0.2s ease;
    text-decoration: none !important;
    position: relative; /* For badges */
    z-index: 2; /* Ensure links sit above the bridge */
}

.hqs-link i {
    font-size: 18px;
}

.hqs-link:hover {
    background: rgba(255, 255, 255, 0.15);
    color: #fff;
    transform: scale(1.1);
}

.hqs-link.hqs-danger:hover {
    background: rgba(234, 84, 85, 0.2);
    color: #ea5455;
}

.hqs-divider {
    width: 20px;
    height: 1px;
    background: rgba(255, 255, 255, 0.1);
    margin: 2px 0;
}

.hqs-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    font-size: 9px;
    padding: 2px 4px;
    border-radius: 10px;
    border: 1px solid #2c3e50;
}
</style>

  
@vite(['resources/js/bootstrap.js', 'resources/js/notification.js','resources/js/chat.js'])
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
                                        {{-- ANFRAGE --}}
                                        <li class="horizontal_menu_item nav-item" id="menu1">
                                            <a href="javascript:void(0);" aria-haspopup="true" aria-expanded="false"
                                            class="menu {{ Str::contains(Route::currentRouteName(), 'quiry') ? 'active_menu' : '' }}"
                                            >
                                                <div class="menu-items">
                                                    <i class="feather icon-inbox dashboard-icon"></i>
                                                    <h6 class="dashboard-title">ANFRAGE</h6>
                                                </div>
                                            </a>

                                            <div class="submenu {{ Str::contains(Route::currentRouteName(), 'quiry') ? 'show' : '' }}">
                                                <ul>

                                                    {{-- ============================
                                                        CREATE NEW INQUIRY
                                                    ============================= --}}
                                                    <li>
                                                        <a href="{{ route('inquiry.create') }}"
                                                        @if(Route::currentRouteName()=='inquiry.create') style="color:#94c11b!important" @endif>
                                                            <i class="feather icon-plus-circle"></i> ANLEGEN
                                                        </a>
                                                    </li>

                                                    @php
                                                        // (1) UNVERIFIED INQUIRIES (all types except Published & not deleted)
                                                        $unverifiedBase = DB::table('inquiries')
                                                            ->whereNull('deleted_at')
                                                            ->where(function($q) {
                                                                $q->whereNull('verify_by')
                                                                ->orWhereNull('verify_date')
                                                                ->orWhere('status','!=','Published');
                                                            });

                                                        // (2) Kunde (lead inquiries)
                                                        $anfrage_lead_count = (clone $unverifiedBase)
                                                            ->where('pre_type','Kunde')
                                                            ->count();

                                                        // (3) Sonstige Anfrage (not Kunde)
                                                        $anfrage_count = (clone $unverifiedBase)
                                                            ->where(function($q){
                                                                $q->whereNull('pre_type')
                                                                ->orWhere('pre_type','!=','Kunde');
                                                            })
                                                            ->count();

                                                        // (4) Junk
                                                        $junk_anfrage = DB::table('inquiries')
                                                            ->where('status','Junk')
                                                            ->whereNull('deleted_at')
                                                            ->count();

                                                        // (5) Deleted
                                                        $delete_anfrage = DB::table('inquiries')
                                                            ->whereNotNull('deleted_at')
                                                            ->count();

                                                        // (6) My inquiries (using employee id or fallback to user id)
                                                        $employeeId = auth()->user()->name;

                                                        $my_inquiries = $employeeId
                                                            ? DB::table('inquiries')
                                                                ->where('contact_person',$employeeId)
                                                                ->whereNull('deleted_at')
                                                                ->whereNotIn('status',['Junk','Published'])
                                                                ->where(function($q){
                                                                    $q->whereNull('pre_type')
                                                                    ->orWhere('pre_type','!=','Kunde');
                                                                })
                                                                ->count()
                                                            : 0;

                                                        // (7) Summary for verified list
                                                        $verified_total = $summary['total'] ?? 0;
                                                    @endphp


                                                    {{-- ============================
                                                        MY INQUIRIES
                                                    ============================= --}}
                                                    <li>
                                                        <a href="{{ route('my.inquiry.view') }}"
                                                        @if(Route::currentRouteName()=='my.inquiry.view') style="color:#94c11b!important" @endif>
                                                            <i class="feather icon-list"></i> MEINE
                                                            <span style="margin:0 5px">|</span>
                                                            <span style="color:#64b0e5">{{ $my_inquiries }}</span>
                                                        </a>
                                                    </li>


                                                    {{-- ============================
                                                        LEAD INQUIRIES
                                                    ============================= --}}
                                                    <li>
                                                        <a href="{{ route('inquiry.customer') }}"
                                                        @if(Route::currentRouteName()=='inquiry.customer') style="color:#94c11b!important" @endif>
                                                            <i class="feather icon-users"></i> KUNDE ANFRAGE
                                                            <span style="margin:0 5px">|</span>
                                                            <span style="color:#64b0e5">{{ $anfrage_lead_count }}</span>
                                                        </a>
                                                    </li>


                                                    {{-- ============================
                                                        OTHER INQUIRIES
                                                    ============================= --}}
                                                    <li>
                                                        <a href="{{ route('inquiry.view') }}"
                                                        @if(Route::currentRouteName()=='inquiry.view') style="color:#94c11b!important" @endif>
                                                            <i class="feather icon-list"></i> SONSTIGE ANFRAGE
                                                            <span style="margin:0 5px">|</span>
                                                            <span style="color:#64b0e5">{{ $anfrage_count }}</span>
                                                        </a>
                                                    </li>


                                                    {{-- ============================
                                                        VERIFIED (PUBLISHED) LIST
                                                    ============================= --}}
                                                    <li>
                                                        <a href="{{ route('inquiry.published.list') }}"
                                                        @if(Route::currentRouteName()=='inquiry.published.list') style="color:#94c11b!important" @endif>
                                                            <i class="feather icon-list"></i> VERIFIZIERTE LISTE
                                                            <span style="margin:0 5px">|</span> 
                                                            <span style="color:#64b0e5">{{ $verified_total}}</span>
                                                        </a>
                                                    </li>


                                                    {{-- ============================
                                                        JUNK
                                                    ============================= --}}
                                                    <li>
                                                        <a href="{{ url('inquiry_junklist') }}"
                                                        @if(Route::currentRouteName()=='inquiry.junk.list') style="color:#94c11b!important" @endif>
                                                            <i class="feather icon-slash"></i> JUNK
                                                            <span style="margin:0 5px">|</span>
                                                            <span style="color:#64b0e5">{{ $junk_anfrage }}</span>
                                                        </a>
                                                    </li>


                                                    {{-- ============================
                                                        DELETED
                                                    ============================= --}}
                                                    <li>
                                                        <a href="{{ url('inquiry_deleted_list') }}"
                                                        @if(Route::currentRouteName()=='inquiry.deleted.list') style="color:#94c11b!important" @endif
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
                                                @if(Str::contains(Route::currentRouteName(),'new.lead.profile') && Str::contains($previousRoute,'lead'))
                                                    active_menu
                                                @elseif(Str::contains(Route::currentRouteName(),'lead') && Str::contains($previousRoute,'lead'))
                                                    active_menu
                                                @endif"
                                            >
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
                                                @if(Str::contains(Route::currentRouteName(),'new.lead.profile') && Str::contains($previousRoute,'lead'))
                                                    show
                                                @elseif(Str::contains(Route::currentRouteName(),'lead'))
                                                    show
                                                @endif">
                                                <ul>
                                                    @php
                                                        $now = Carbon\Carbon::now();
                                                        $last48h = $now->subHours(48);
                                                        $lead_count = DB::table('new_leads')->whereNull('deleted_at')->count();
                                                        $lead_new = DB::table('new_leads')->whereNull('deleted_at')->where('created_at','>=',$last48h)->count();
                                                        $junk_lead = DB::table('new_leads')->where('status','Junk')->whereNull('deleted_at')->count();
                                                        $delete_lead = DB::table('new_leads')->whereNotNull('deleted_at')->count();
                                                        $my_leads = DB::table('new_leads')->whereNull('deleted_at')->where('status','Lead')->where('contact_person',auth()->user()->name)->count();
                                                        $waiting_loops = DB::table('new_leads as nl')
                                                            ->leftJoin('lead_product_lists as lpl','nl.id','=','lpl.customer_id')
                                                            ->whereNull('nl.deleted_at')->whereNull('lpl.employee_id')
                                                            ->where('nl.status','Lead')->distinct('nl.id')->count();
                                                    @endphp

                                                    <li>
                                                        <a href="{{ url('new_lead_create') }}" @if(Route::currentRouteName() == 'new.lead.create') style="color:#94c11b!important" @endif>
                                                            <i class="feather icon-plus-circle"></i> ANLEGEN
                                                        </a>
                                                    </li>

                                                    @php
                                                        // helper to calc "active" per-link
                                                        $previousUrl = url()->previous();
                                                        try { $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName(); }
                                                        catch (\Exception $e) { $previousRoute = null; }
                                                    @endphp
 
 

                                                    <li>
                                                        @php
                                                            $isActive = (Str::contains(Route::currentRouteName(),'new.lead.profile') && Str::contains($previousRoute,'waiting.loop.leads'))
                                                                    || Str::contains(Route::currentRouteName(),'waiting.loop.leads');
                                                        @endphp
                                                        <a href="{{ route('waiting.loop.leads') }}" class="{{ $isActive ? 'active-link' : '' }}" style="{{ $isActive ? 'color:#94c11b!important' : '' }}">
                                                            @if($isActive)<i class="feather icon-arrow-right warning"></i>@endif
                                                            <i class="feather icon-refresh-ccw"></i> WARTESCHLEIFE
                                                            <span style="margin:0 5px">|</span><span style="color:#64b0e5">{{ $waiting_loops }}</span>
                                                        </a>
                                                    </li>

                                                    <li>
                                                        @php
                                                            $isActive = (Str::contains(Route::currentRouteName(),'new.lead.profile') && Str::contains($previousRoute,'new.lead.view'))
                                                                    || Str::contains(Route::currentRouteName(),'new.lead.view');
                                                        @endphp
                                                        <a href="{{ url('new_lead_view') }}" class="{{ $isActive ? 'active-link' : '' }}" style="{{ $isActive ? 'color:#94c11b!important' : '' }}">
                                                            @if($isActive)<i class="feather icon-arrow-right warning"></i>@endif
                                                            <i class="feather icon-list"></i> LISTE
                                                            <span style="margin:0 5px">|</span><span style="color:#64b0e5">{{ $lead_count }}</span>
                                                        </a>
                                                    </li>

                                                    <li>
                                                        @php
                                                            $isActive = (Str::contains(Route::currentRouteName(),'new.lead.profile') && Str::contains($previousRoute,'lead.junks'))
                                                                    || Str::contains(Route::currentRouteName(),'lead.junks');
                                                        @endphp
                                                        <a href="{{ url('lead_junks') }}" class="{{ $isActive ? 'active-link' : '' }}" style="{{ $isActive ? 'color:#94c11b!important' : '' }}">
                                                            @if($isActive)<i class="feather icon-arrow-right warning"></i>@endif
                                                            <i class="feather icon-slash"></i> JUNK
                                                            <span style="margin:0 5px">|</span><span style="color:#64b0e5">{{ $junk_lead }}</span>
                                                        </a>
                                                    </li>

                                                    <li>
                                                        @php
                                                            $isActive = (Str::contains(Route::currentRouteName(),'new.lead.profile') && Str::contains($previousRoute,'deleted.leads'))
                                                                    || Str::contains(Route::currentRouteName(),'deleted.leads');
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
                                                @php $previousUrl = url()->previous(); $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName(); @endphp
                                                @if(Str::contains(Route::currentRouteName(),'new.lead.profile') && Str::contains($previousRoute,'offer')) active_menu
                                                @elseif(Str::contains(Route::currentRouteName(),'offer')) active_menu @endif"
                                            >
                                                <div class="menu-items">
                                                    <i class="feather icon-file-text dashboard-icon"></i>
                                                    <h6 class="dashboard-title">VERKAUF</h6>
                                                </div>
                                            </a>

                                            <div class="submenu
                                                @php $previousUrl = url()->previous(); $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName(); @endphp
                                                @if(Str::contains(Route::currentRouteName(),'new.lead.profile') && Str::contains($previousRoute,'offer')) show
                                                @elseif(Str::contains(Route::currentRouteName(),'offer')) show @endif">
                                                <ul>
                                                    <li>
                                                        @php
                                                            $previousUrl = url()->previous();
                                                            try { $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName(); } catch (\Exception $e) { $previousRoute = null; }
                                                            $isActive = (Str::contains(Route::currentRouteName(),'new.lead.profile') && Str::contains($previousRoute,'offer.view'))
                                                                    || Str::contains(Route::currentRouteName(),'offer.view');
                                                        @endphp
                                                        <a href="{{ route('offer.view') }}" class="{{ $isActive ? 'active-link' : '' }}" style="{{ $isActive ? 'color:#94c11b!important' : '' }}">
                                                            @if($isActive)<i class="feather icon-arrow-right warning"></i>@endif
                                                            <i class="feather icon-list"></i> LISTE
                                                            <span style="margin:0 5px">|</span><span style="color:#64b0e5">0</span>
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a href="{{ url('new_lead_view') }}">
                                                            <i class="feather icon-trash"></i> GELÖSCHTE
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>

                                        {{-- AUFTRAG (DEALS) --}}
                                        <li class="horizontal_menu_item nav-item" id="menu2">
                                            <a href="javascript:void(0);" aria-haspopup="true" aria-expanded="false"
                                            class="menu
                                                @php $previousUrl = url()->previous(); $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName(); @endphp
                                                @if(Str::contains(Route::currentRouteName(),'new.lead.profile') && Str::contains($previousRoute,'deal')) active_menu
                                                @elseif(Str::contains(Route::currentRouteName(),'deal')) active_menu @endif"
                                            >
                                                <div class="menu-items">
                                                    <i class="feather icon-briefcase dashboard-icon"></i>
                                                    <h6 class="dashboard-title">AUFTRAG</h6>
                                                </div>
                                            </a>

                                            @php
                                                $deals = DB::table('deals')->select('id','status','deleted_at','status_msg')->get();
                                                $deal_new = $deals->where('status_msg','new')->whereNotIn('status',['Junk','confirm','complete'])->count();
                                                $deal_confirm = $deals->where('status_msg','confirm')->count();
                                                $deal_junk = $deals->where('status','Junk')->whereNull('deleted_at')->count();
                                                $deal_all = $deals->whereNull('deleted_at')->whereNotIn('status',['Junk'])->count();
                                                $deal_delete = $deals->where('deleted_at','!=',null)->count();
                                            @endphp

                                            <div class="submenu
                                                @php $previousUrl = url()->previous(); $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName(); @endphp
                                                @if(Str::contains(Route::currentRouteName(),'new.lead.profile') && Str::contains($previousRoute,'deal')) show
                                                @elseif(Str::contains(Route::currentRouteName(),'deal')) show @endif">
                                                <ul>
                                                    <li>
                                                        @php
                                                            $previousUrl = url()->previous();
                                                            try { $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName(); } catch (\Exception $e) { $previousRoute = null; }
                                                            $isActive = (Str::contains(Route::currentRouteName(),'new.lead.profile') && Str::contains($previousRoute,'deal.all.list'))
                                                                    || Str::contains(Route::currentRouteName(),'deal.all.list');
                                                        @endphp
                                                        <a href="{{ route('deal.details') }}" class="{{ $isActive ? 'active-link' : '' }}" style="{{ $isActive ? 'color:#94c11b!important' : '' }}">
                                                            @if($isActive)<i class="feather icon-arrow-right warning"></i>@endif
                                                            <i class="feather icon-layers"></i> ALLE | {{ $deal_all }}
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a href="{{ route('deal.invoice') }}">
                                                            <i class="feather icon-file-text"></i> RECHNUNGEN |
                                                        </a>
                                                    </li>

                                                    <li>
                                                        @php
                                                            $isActive = (Str::contains(Route::currentRouteName(),'new.lead.profile') && Str::contains($previousRoute,'deal.junk.list'))
                                                                    || Str::contains(Route::currentRouteName(),'deal.junk.list');
                                                        @endphp
                                                        <a href="{{ route('deal.junk.list') }}" class="{{ $isActive ? 'active-link' : '' }}" style="{{ $isActive ? 'color:#94c11b!important' : '' }}">
                                                            @if($isActive)<i class="feather icon-arrow-right warning"></i>@endif
                                                            <i class="feather icon-slash"></i> JUNK | {{ $deal_junk }}
                                                        </a>
                                                    </li>

                                                    <li>
                                                        @php
                                                            $isActive = (Str::contains(Route::currentRouteName(),'new.lead.profile') && Str::contains($previousRoute,'deal.delete.list'))
                                                                    || Str::contains(Route::currentRouteName(),'deal.delete.list');
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
                                                @php $previousUrl = url()->previous(); $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName(); @endphp
                                                @if(Str::contains(Route::currentRouteName(),'new.lead.profile') && Str::contains($previousRoute,'project')) active_menu
                                                @elseif(Str::contains(Route::currentRouteName(),'project') && Str::contains($previousRoute,'project')) active_menu @endif"
                                            >
                                                <div class="menu-items">
                                                    <i class="feather icon-settings dashboard-icon"></i>
                                                    <h6 class="dashboard-title">MONTAGE</h6>
                                                </div>
                                            </a>

                                            <div class="submenu {{ Str::contains(Route::currentRouteName(), 'project') ? 'show' : '' }}">
                                                <ul>
                                                    <li>
                                                        <a href="{{ route('project.create.new') }}" @if(Route::currentRouteName() == 'project.create.new') style="color:#94c11b!important" @endif>
                                                            <i class="feather icon-plus-circle"></i> ANLEGEN
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('my.project.details') }}" @if(Route::currentRouteName() == 'my.project.details') style="color:#94c11b!important" @endif>
                                                            <i class="feather icon-user"></i> MEINE
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('project.details') }}" @if(Route::currentRouteName() == 'project.detailss') style="color:#94c11b!important" @endif>
                                                            <i class="feather icon-list"></i> LISTE
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('project.delete.lists') }}" @if(Route::currentRouteName() == 'project.delete.lists') style="color:#94c11b!important" @endif>
                                                            <i class="feather icon-trash"></i> GELÖSCHTE
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>

                                        {{-- ABSCHLUSS (duplicate block kept, iconized) --}}
                                        <li class="horizontal_menu_item nav-item" id="menu4">
                                            <a href="javascript:void(0);" aria-haspopup="true" aria-expanded="false"
                                            class="menu
                                                @php $previousUrl = url()->previous(); $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName(); @endphp
                                                @if(Str::contains(Route::currentRouteName(),'new.lead.profile') && Str::contains($previousRoute,'project')) active_menu
                                                @elseif(Str::contains(Route::currentRouteName(),'project') && Str::contains($previousRoute,'project')) active_menu @endif"
                                            >
                                                <div class="menu-items">
                                                    <i class="feather icon-check-square dashboard-icon"></i>
                                                    <h6 class="dashboard-title">ABSCHLUSS</h6>
                                                </div>
                                            </a>

                                            <div class="submenu {{ Str::contains(Route::currentRouteName(), 'project') ? 'show' : '' }}">
                                                <ul>
                                                    <li>
                                                        <a href="{{ route('project.create.new') }}" @if(Route::currentRouteName() == 'project.create.new') style="color:#94c11b!important" @endif>
                                                            <i class="feather icon-plus-circle"></i> ANLEGEN
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('my.project.details') }}" @if(Route::currentRouteName() == 'my.project.details') style="color:#94c11b!important" @endif>
                                                            <i class="feather icon-user"></i> MEINE
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('project.details') }}" @if(Route::currentRouteName() == 'project.detailss') style="color:#94c11b!important" @endif>
                                                            <i class="feather icon-list"></i> LISTE
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('project.delete.lists') }}" @if(Route::currentRouteName() == 'project.delete.lists') style="color:#94c11b!important" @endif>
                                                            <i class="feather icon-trash"></i> GELÖSCHTE
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
                                                @php $previousUrl = url()->previous(); $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl))->getName(); @endphp
                                                @if(Str::contains(Route::currentRouteName(),'new.lead.profile') && Str::contains($previousRoute,'ticket')) active_menu
                                                @elseif(Str::contains(Route::currentRouteName(),'ticket') && Str::contains($previousRoute,'ticket')) active_menu @endif"
                                            >
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
                                            <a href="{{ url('lead/kanban') }}" aria-haspopup="true" aria-expanded="false" class="menu" >
                                                <div class="menu-items">
                                                    <i class="feather icon-activity dashboard-icon"></i>
                                                    <h6 class="dashboard-title">KUNDEN ÜBERSICHT</h6>
                                                </div>
                                            </a> 
                                            
                                        </li>

                                    </ul>
    
                                    <style>
                                    .dashboard-icon { font-size: 22px; margin-right: 8px; line-height: 1; color: #74b2d4}
                                    .menu.active_menu .dashboard-icon,
                                    .submenu a.active-link .feather { color: #94c11b; }
                                    .dashboard-title { margin-bottom:0; }
                                    </style>

                            </div> 
                                                        
                             <li class="nav-item quick_sider_wrapper"> 
                                    <a href="javascript:void(0);" class="nav-link" id="openSiderBtn" title="Quick Panel">
                                        <i class="ficon feather icon-grid"></i>
                                    </a>

                                    <div class="hover-quick-strip">
                                        
                                        <a href="javascript:void(0);" class="hqs-link" id="openInnerSiderBtn" title="Alle Apps öffnen" data-toggle="tooltip" data-placement="left">
                                            <i class="feather icon-grid"></i>
                                        </a>
                                        <a href="{{ url('/')}}" class="hqs-link" title="Dashboard" data-toggle="tooltip" data-placement="left">
                                            <i class="feather icon-home"></i>
                                        </a>

                                        <a href="javascript:void(0);" class="hqs-link nav-link-search" title="Suche" data-toggle="tooltip" data-placement="left">
                                            <i class="feather icon-search"></i>
                                        </a>

                                        <a href="{{ url('admin/chat')}}" class="hqs-link position-relative" title="Nachrichten" data-toggle="tooltip" data-placement="left">
                                            <i class="feather icon-message-square"></i> 
                                        </a>

                                        <a href="{{ url('tasks/calendar/personal')}}" class="hqs-link" title="Kalender" data-toggle="tooltip" data-placement="left">
                                            <i class="feather icon-calendar"></i>
                                        </a>

                                        <a href="{{ url('admin/todo/personal?tab=my') }}" class="hqs-link" title="Aufgaben" data-toggle="tooltip" data-placement="left">
                                            <i class="feather icon-check-square"></i>
                                        </a>

                                        <div class="hqs-divider"></div>

                                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="hqs-link hqs-danger" title="Logout" data-toggle="tooltip" data-placement="left">
                                            <i class="feather icon-power"></i>
                                        </a>

                                    </div>
                                </li>

                                <li>

                                </li>
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
                            src="{{ asset('images/user/'.auth()->user()->image)}}"
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
                        <a class="sa-item" href="{{ url('/employee_profile/'.auth()->user()->name) }}" role="menuitem">
                        <i class="feather icon-user"></i> Mein Profil
                        </a>
                        <a class="sa-item" href="{{ url('/employee_notifications/'.auth()->user()->name) }}" role="menuitem">
                        <i class="feather icon-bell"></i> Meine Anträge
                        </a>

                        <a class="sa-item" href="{{ url('admin/employees/'.auth()->user()->name.'/time-management')}}" role="menuitem">
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
                                <li><a href="{{ route('brand.info') }}"><i class="fa fa-industry"></i> Hersteller</a></li>
                                <li><a href="{{ route('distributors.index') }}"><i class="fa fa-truck"></i> Lieferant</a></li>
                                <li><a href="{{ route('external.info') }}"><i class="fa fa-users"></i> Zeitarbeitfirma</a></li>
                                <li><a href="{{ route('brand.sub.contractor') }}"><i class="fa fa-tools"></i> Nach Unternehmer</a></li>
                                <li><a href="{{ route('brand.architect') }}"><i class="fa fa-pencil-ruler"></i> Architekten</a></li>
                                <li><a href="{{ route('brand.bank') }}"><i class="fa fa-university"></i> Bank</a></li>
                                <li><a href="{{ route('brand.insurance') }}"><i class="fa fa-shield-alt"></i> Versicherung</a></li>
                                <li><a href="{{ route('brand.others') }}"><i class="fa fa-ellipsis-h"></i> Sonstiges</a></li>
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
                                        <li><a href="{{ route('offer.view') }}"><i class="fa fa-plus-circle"></i> Neue</a></li>
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
                                        <li><a href="{{ route('project.create.new') }}"><i class="fa fa-folder-plus"></i> ANLEGEN</a></li>
                                        <li><a href="{{ route('my.project.details') }}"><i class="fa fa-user"></i> LISTE</a></li> 
                                        <li><a href="{{ route('project.delete.lists') }}"><i class="feather icon-trash"></i> GELÖSCHT</a></li>
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
                                    </ul>
                                </li>

                                <!-- Gehaltsmanagement -->
                                    @if(DB::table('user_rolls')
                                        ->where('user_rolls.user_id', '=', auth()->user()->name)
                                        ->where('user_rolls.item_id', '=', 'Super')
                                        ->where('user_rolls.is_add', '=', 'on')
                                        ->first())
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
                                <li><a href="#"><i class="fa fa-user-tie"></i> Bewerber</a></li>

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
                                    <li><a href="{{ route('position.info') }}"><i class="fa fa-briefcase"></i> Position</a></li>
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
                

                        <li class="nav-has-submenu" data-name="Product" id="organization-menu" style="display:none;">
                            <a href="javascript:void(0);">
                                <i class="fa fa-truck"></i> Lager
                            </a>
                            <ul class="nav-submenu">
                                <li class="nav-has-submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-warehouse"></i> Inventar
                                    </a>
                                    <ul class="nav-submenu">
                                        <li><a href="{{ route('product.create.inventory') }}"><i class="fa fa-box-open"></i> Bestandsregistrierung</a></li>
                                        <li><a href="{{ route('product.inventory.search') }}"><i class="feather icon-search"></i> Inventarsuche</a></li>
                                    </ul>
                                </li>


                                    <li class="nav-has-submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-warehouse"></i> Produktlieferung
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


                                    <li class="nav-has-submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-qrcode"></i> Vermögensbestand
                                    </a>
                                    <ul class="nav-submenu">
                                            <li><a href="{{ route('assets.inventory') }}"><i class="fa fa-database"></i> Dateneingabe</a></li>
                                        <li><a href="{{ route('handover.details') }}"><i class="fa fa-exchange-alt"></i> Gegenstände übergeben</a></li>
                                        <li><a href="{{ route('product.inventory.search') }}"><i class="feather icon-search"></i> Inventarsuche</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li> 

                        <li class="nav-has-submenu" data-name="Product" id="organization-menu" style="display:none;">
                            <a href="javascript:void(0);">
                                <i class="fa fa-cart-plus"></i> Einkauf
                            </a>
                            <ul class="nav-submenu">
                                <li class="nav-has-submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-warehouse"></i> Keine Daten
                                    </a>
                                    <ul class="nav-submenu">
                                        <li><a  ><i class="fa fa-box-open"></i> Keine Daten</a></li> 
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
                                <li><a href="{{ route('invoice.info') }}"><i class="fa fa-file-invoice"></i> Rechnung Details</a></li>
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
                                <li><a href="{{ url('/user_roll') }}"><i class="fa fa-user-cog"></i> User Rolle</a></li>
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
                                            <i class="fa fa-money-bill-wave"></i> Sets
                                        </a>
                                        <ul class="nav-submenu">
                                            <li><a href="{{ route('article.group.set') }}"><i class="fa fa-clock"></i> Master Set</a></li>
                                            <li><a href="{{ route('group.set.view') }}"><i class="feather icon-map-pin"></i> Group Set</a></li>
                                        </ul>
                                    </li>

                                    <li class="nav-has-submenu">
                                        <a href="javascript:void(0);">
                                            <i class="fa fa-money-bill-wave"></i> Projekt-Checkliste
                                        </a>
                                        <ul class="nav-submenu">
                                            <li><a href="{{ route('task_phase.index') }}"><i class="fa fa-clock"></i> Arbeitsschritte</a></li> 
                                            <li><a href="{{ route('stages.index') }}"><i class="fa fa-clock"></i> Stage</a></li> 
                                            <li><a href="{{ route('checklists.index') }}"><i class="fa fa-clock"></i> Checkliste</a></li> 
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
                            <div class="search-box-wrapper" style="background:#1f2d3d !important;">
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

                @if (Route::currentRouteName() != 'chat.index')
                    @vite(['resources/js/mini_chat.js'])

                    {{-- Launcher --}}
                    <button id="chatLauncher"
                            class="mini-chat-launcher btn btn-primary d-flex align-items-center position-fixed"
                            aria-controls="chatWindow" aria-expanded="false" type="button">
                        <span class="mini-chat-launcher-icon me-2">
                            <i class="feather icon-message-circle"></i>
                        </span>
                        <span class="mini-chat-launcher-label d-none d-sm-inline">Chat</span>
                        <span id="launcherBadge"
                            class="mini-chat-launcher-badge badge rounded-pill bg-danger text-white d-none">0</span>
                    </button>

                    {{-- Chat Window --}}
                    <section id="chatWindow"
                            class="mini-chat-window d-none"
                            role="dialog"
                            aria-modal="true"
                            aria-labelledby="chatTitle">

                        <style>
                            :root {
                                    --fb-chat-width: 320px;
                                    --fb-sidebar-width: 70px;
                                    --fb-header-height: 45px;
                                    --fb-primary: #569ad8;
                                    --fb-bg: #fff;
                                    --fb-border: #ddd;
                                    --fb-text: #333;
                                    --fb-incoming: #f1f0f0;
                                    --fb-outgoing: #e3f2fb;
                                }

                                /* --- Right Sidebar (Contact Strip) --- */
                                #fb-right-sidebar {
                                    position: fixed;
                                    top: 110px; /* Adjust based on your navbar height */
                                    right: 0;
                                    bottom: 0;
                                    width: var(--fb-sidebar-width);
                                    background: #fff;
                                    border-left: 1px solid var(--fb-border);
                                    z-index: 1045;
                                    display: flex;
                                    flex-direction: column;
                                    align-items: center;
                                    padding-top: 10px;
                                    overflow-y: auto;
                                    scrollbar-width: thin;
                                    box-shadow: -2px 0 5px rgba(0,0,0,0.05);
                                }

                                .fb-contact-item {
                                    position: relative;
                                    width: 45px;
                                    height: 45px;
                                    margin-bottom: 15px;
                                    cursor: pointer;
                                    transition: transform 0.2s;
                                }

                                .fb-contact-item:hover {
                                    transform: scale(1.1);
                                }

                                .fb-avatar {
                                    width: 100%;
                                    height: 100%;
                                    border-radius: 50%;
                                    object-fit: cover;
                                    border: 2px solid #fff;
                                    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                                    background: #eee;
                                }

                                .fb-initials {
                                    width: 100%;
                                    height: 100%;
                                    border-radius: 50%;
                                    background: var(--fb-primary);
                                    color: #fff;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-weight: bold;
                                    font-size: 14px;
                                    border: 2px solid #fff;
                                    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                                }

                                /* Online Status Dot */
                                .fb-status-dot {
                                    position: absolute;
                                    bottom: 0;
                                    right: 0;
                                    width: 12px;
                                    height: 12px;
                                    background: #ccc;
                                    border: 2px solid #fff;
                                    border-radius: 50%;
                                }
                                .fb-status-dot.online { background: #31a24c; }

                                /* Unread Badge on Avatar */
                                .fb-avatar-badge {
                                    position: absolute;
                                    top: -5px;
                                    right: -5px;
                                    background: #e41e3f;
                                    color: #fff;
                                    font-size: 10px;
                                    padding: 2px 5px;
                                    border-radius: 10px;
                                    border: 1px solid #fff;
                                    font-weight: bold;
                                }

                                /* Tooltip for Name on Hover */
                                .fb-contact-tooltip {
                                    position: absolute;
                                    right: 60px;
                                    top: 50%;
                                    transform: translateY(-50%);
                                    background: rgba(0,0,0,0.8);
                                    color: #fff;
                                    padding: 4px 8px;
                                    border-radius: 4px;
                                    font-size: 12px;
                                    white-space: nowrap;
                                    opacity: 0;
                                    pointer-events: none;
                                    transition: opacity 0.2s;
                                    z-index: 1050;
                                }
                                .fb-contact-item:hover .fb-contact-tooltip {
                                    opacity: 1;
                                }

                                /* --- Bottom Chat Container --- */
                                #fb-bottom-chats-container {
                                    position: fixed;
                                    bottom: 0;
                                    right: var(--fb-sidebar-width);
                                    left: 0;
                                    height: 0; /* Let children overflow upwards */
                                    z-index: 1050;
                                    display: flex;
                                    flex-direction: row-reverse; /* Newest chats on the right */
                                    align-items: flex-end;
                                    padding-right: 15px;
                                    pointer-events: none; /* Allow clicks to pass through empty space */
                                }

                                /* --- Individual Chat Window --- */
                                .fb-chat-window {
                                    width: var(--fb-chat-width);
                                    background: var(--fb-bg);
                                    border-radius: 8px 8px 0 0;
                                    box-shadow: 0 0 15px rgba(0,0,0,0.15);
                                    margin-right: 15px;
                                    display: flex;
                                    flex-direction: column;
                                    pointer-events: auto; /* Re-enable clicks */
                                    transition: transform 0.3s ease;
                                    border: 1px solid var(--fb-border);
                                    max-height: 450px;
                                    height: 450px;
                                }

                                .fb-chat-window.minimized {
                                    height: var(--fb-header-height);
                                    overflow: hidden;
                                    transform: translateY(0);
                                }

                                .fb-chat-header {
                                    height: var(--fb-header-height);
                                    background: var(--fb-primary);
                                    color: #fff;
                                    padding: 0 10px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: space-between;
                                    border-radius: 8px 8px 0 0;
                                    cursor: pointer;
                                    flex-shrink: 0;
                                }

                                .fb-chat-header.flash {
                                    animation: flash-header 1s infinite;
                                }

                                @keyframes flash-header {
                                    0% { background-color: var(--fb-primary); }
                                    50% { background-color: #e41e3f; }
                                    100% { background-color: var(--fb-primary); }
                                }

                                .fb-chat-title {
                                    font-weight: 600;
                                    font-size: 14px;
                                    white-space: nowrap;
                                    overflow: hidden;
                                    text-overflow: ellipsis;
                                    max-width: 200px;
                                }

                                .fb-chat-controls i {
                                    margin-left: 8px;
                                    cursor: pointer;
                                    opacity: 0.8;
                                }
                                .fb-chat-controls i:hover { opacity: 1; }

                                .fb-chat-body {
                                    flex: 1;
                                    overflow-y: auto;
                                    padding: 10px;
                                    background: #f7f7f7;
                                    display: flex;
                                    flex-direction: column;
                                    gap: 8px;
                                }

                                .fb-chat-footer {
                                    padding: 8px;
                                    border-top: 1px solid #eee;
                                    background: #fff;
                                    display: flex;
                                    gap: 5px;
                                    align-items: center;
                                }

                                .fb-chat-input {
                                    flex: 1;
                                    border: 1px solid #ddd;
                                    border-radius: 20px;
                                    padding: 6px 12px;
                                    font-size: 13px;
                                    outline: none;
                                }

                                .fb-chat-btn {
                                    background: none;
                                    border: none;
                                    color: var(--fb-primary);
                                    cursor: pointer;
                                    padding: 5px;
                                }

                                /* Messages */
                                .fb-msg {
                                    max-width: 80%;
                                    padding: 8px 12px;
                                    border-radius: 12px;
                                    font-size: 13px;
                                    line-height: 1.4;
                                    position: relative;
                                    word-wrap: break-word;
                                }

                                .fb-msg.incoming {
                                    align-self: flex-start;
                                    background: var(--fb-incoming);
                                    color: #000;
                                    border-bottom-left-radius: 2px;
                                }

                                .fb-msg.outgoing {
                                    align-self: flex-end;
                                    background: var(--fb-outgoing);
                                    color: #333;
                                    border-bottom-right-radius: 2px;
                                }

                                .fb-msg-meta {
                                    font-size: 10px;
                                    color: #999;
                                    text-align: right;
                                    margin-top: 2px;
                                }

                                /* Dark Mode Overrides */
                                body.dark-mode #fb-right-sidebar {
                                    background: #2c3e50;
                                    border-left: 1px solid #444;
                                }
                                body.dark-mode .fb-chat-window {
                                    background: #2c3e50;
                                    border-color: #444;
                                }
                                body.dark-mode .fb-chat-header {
                                    background: #1e1e1e;
                                }
                                body.dark-mode .fb-chat-body {
                                    background: #151515;
                                }
                                body.dark-mode .fb-chat-footer {
                                    background: #1e1e1e;
                                    border-top-color: #333;
                                }
                                body.dark-mode .fb-chat-input {
                                    background: #333;
                                    color: #fff;
                                    border-color: #555;
                                }
                                body.dark-mode .fb-msg.incoming {
                                    background: #333;
                                    color: #fff;
                                }
                                body.dark-mode .fb-msg.outgoing {
                                    background: #3b5066;
                                    color: #fff;
                                }
                        </style>

                        {{-- Header --}}
                        <header class="mini-chat-header">
                            <div class="mini-chat-header-left">
                                <div class="mini-chat-header-icon">
                                    <i class="feather icon-message-square"></i>
                                </div>
                                <div class="mini-chat-header-text">
                                    <div id="chatTitle" class="mini-chat-header-title">Nachrichten</div>
                                    <div id="miniChatHint" class="mini-chat-header-sub">Kontakt oder Gruppe wählen</div>
                                </div>
                            </div>
                            <div class="mini-chat-header-right">
                                <button id="btnUsers" type="button" class="mini-chat-header-btn d-inline d-sm-none"
                                        title="Kontakte">
                                    <i class="feather icon-users"></i>
                                </button>
                                <button id="btnMinimize" type="button" class="mini-chat-header-btn" title="Minimieren">
                                    <i class="feather icon-x"></i>
                                </button>
                            </div>
                        </header>

                        {{-- Body --}}
                        <div class="mini-chat-body">
                            <div class="mini-chat-body-backdrop" id="miniChatBackdrop"></div>

                            {{-- Sidebar: users + groups --}}
                            <aside class="mini-chat-sidebar" id="miniChatSidebar">
                                <div class="mini-chat-sidebar-search">
                                    <div class="mini-chat-sidebar-search-wrap">
                                        <span class="mini-chat-sidebar-search-icon">
                                            <i class="feather icon-search"></i>
                                        </span>
                                        <input id="userSearch" type="text" class="form-control form-control-sm"
                                            placeholder="Suchen…">
                                    </div>
                                </div>
                                <ul id="userList" class="mini-chat-sidebar-list">
                                    {{-- filled by JS --}}
                                </ul>
                                <div class="mini-chat-sidebar-footer">
                                    <span class="d-flex align-items-center">
                                        <span class="mini-chat-online-dot"></span>
                                        <span>Online</span>
                                    </span>
                                    <span id="onlineCount">0</span>
                                </div>
                            </aside>

                            {{-- Conversation --}}
                            <main class="mini-chat-main">
                                <div class="mini-chat-main-header">
                                    <div class="mini-chat-main-avatar" id="convAvatar">–</div>
                                    <div>
                                        <div id="convName" class="mini-chat-main-title">Kein Chat ausgewählt</div>
                                        <div id="convStatus" class="mini-chat-main-sub">Wähle einen Kontakt</div>
                                    </div>
                                    <div class="mini-chat-main-badges" id="convBadges">
                                        {{-- tag pills if needed --}}
                                    </div>
                                </div>

                                <div id="typingIndicator" class="mini-chat-typing d-none"></div>

                                <div id="messages" class="mini-chat-messages">
                                    <div class="mini-chat-empty">
                                        Wählen Sie links einen Kontakt oder eine Gruppe.
                                    </div>
                                </div>

                                <div id="replyBanner" class="mini-chat-reply-banner">
                                    <div class="mini-chat-reply-text" id="replyText"></div>
                                    <button id="replyClose" type="button" class="mini-chat-reply-close">&times;</button>
                                </div>

                                <div class="mini-chat-composer">
                                    <div class="mini-chat-composer-row">
                                        <div class="mini-chat-composer-actions">
                                            <button id="btnAttach" type="button" class="mini-chat-icon-btn" title="Datei anhängen">
                                                <i class="feather icon-paperclip"></i>
                                            </button>
                                            <button id="btnVoice" type="button" class="mini-chat-icon-btn" title="Sprachnachricht">
                                                <i class="feather icon-mic"></i>
                                            </button>
                                        </div>

                                        <input id="msgInput" type="text" class="mini-chat-input"
                                            placeholder="Nachricht schreiben…" disabled>

                                        <button id="btnSend" type="button"
                                                class="mini-chat-icon-btn mini-chat-icon-btn--primary" disabled
                                                title="Senden">
                                            <i class="feather icon-send"></i>
                                        </button>

                                        <input id="fileInput" type="file" class="d-none" multiple>
                                    </div>
                                </div>
                            </main>
                        </div>
                    </section>
                @endif


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
    <!-- <script src="{{ asset('js/clock.js')}}"></script>  -->
 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

      

    <script>window.userId = {{ auth()->id() }};</script>
    <audio id="notificationSound" src="{{ asset('notification/notification.mp3') }}" preload="auto"></audio>
    <audio id="chatNotificationSound" src="{{ asset('notification/notification.mp3') }}" preload="auto"></audio>
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
        // -----------------------------------------
        // Helper: Badge colors by type
        // -----------------------------------------
        function getBadgeColor(type) {
            switch (type) {
                case 'Kunde':      return 'info';
                case 'Hersteller': return 'primary';
                case 'Lieferant':  return 'warning';
                case 'Anfrage':    return 'secondary';
                case 'Mitarbeiter':return 'success';
                case 'Aufgabe':    return 'danger';
                case 'Termin':     return 'primary';
                case 'Ticket':     return 'primary';
                default:           return 'light';
            }
        }

        // -----------------------------------------
        // Restore endpoint per type
        // -----------------------------------------
        function getRestoreEndpoint(type) {
            switch (type) {
                case 'Kunde':
                    // soft-deleted in new_leads table
                    return "{{ route('global.restore.customer') }}";
                case 'Hersteller':
                case 'Merk':
                    // soft-deleted in brands table
                    return "{{ route('global.restore.brand') }}";
                case 'Lieferant':
                    // soft-deleted in distributors table
                    return "{{ route('global.restore.distributor') }}";
                default:
                    return null;
            }
        }

        // -----------------------------------------
        // App-Launcher: build from sidebar
        // -----------------------------------------
        let appEntries = [];

        function buildAppEntries() {
            appEntries = [];

            // All top-level sections in sidebar
            $('.sidebar-content > ul > li.nav-has-submenu').each(function () {
                const $section = $(this);

                // Section headline (e.g. Kommunikation, Projekte, Personal, …)
                let sectionTitle = $.trim(
                    $section.children('a').clone().children('i,.feather,.fa,.badge').remove().end().text()
                );
                if (!sectionTitle) sectionTitle = 'Allgemein';

                // Real links in this section
                $section.find('ul.nav-submenu a').each(function () {
                    const $a   = $(this);
                    const href = $a.attr('href');

                    if (!href || href === 'javascript:void(0);') return;

                    const label = $.trim(
                        $a.clone().children('i,.feather,.fa,.badge').remove().end().text()
                    );

                    appEntries.push({
                        section: sectionTitle,
                        label:   label || href,
                        url:     href
                    });
                });
            });
        }

        function renderAppLauncher($results, filterTerm = '') {
            $results.empty();

            if (!appEntries.length) {
                $results.append(`
                    <li class="p-3 text-muted">
                        Keine Apps gefunden.
                    </li>
                `);
                feather.replace();
                return;
            }

            const term = (filterTerm || '').toLowerCase();
            let filtered = appEntries;

            if (term) {
                filtered = appEntries.filter(app =>
                    app.label.toLowerCase().includes(term) ||
                    app.section.toLowerCase().includes(term)
                );
            }

            if (!filtered.length) {
                $results.append(`
                    <li class="p-3 text-muted">
                        Keine passenden Bereiche für "${filterTerm}" gefunden.
                    </li>
                `);
                feather.replace();
                return;
            }

            // Intro
            $results.append(`
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
            `);

            // Group by section
            const grouped = {};
            filtered.forEach(app => {
                if (!grouped[app.section]) grouped[app.section] = [];
                grouped[app.section].push(app);
            });

            Object.keys(grouped).forEach(section => {
                $results.append(`
                    <li class="search-group-header text-uppercase text-muted font-weight-bold py-1">
                        ${section}
                    </li>
                `);

                grouped[section].forEach(app => {
                    $results.append(`
                        <li class="app-launcher-item" data-url="${app.url}">
                            <div class="search-result-card p-1">
                                <button type="button"
                                        class="btn btn-outline-light btn-block text-left">
                                    <div class="d-flex flex-column">
                                        <span class="font-weight-bold text-white">${app.label}</span>
                                        <small class="text-muted">${section}</small>
                                    </div>
                                </button>
                            </div>
                        </li>
                    `);
                });
            });

            feather.replace();
        }

        // -----------------------------------------
        // Global Search Overlay
        // -----------------------------------------
        $(document).ready(function () {
            const $input   = $('#globalSearchInput');
            const $overlay = $('.search-input-overlay');
            const $results = $('.search-results');
            let selectedIndex = -1;

            // Build app-entries from sidebar once
            buildAppEntries();

            // Open search overlay
            $('.nav-link-search').on('click', function () {
                $overlay.fadeIn().removeClass('d-none');
                $input.val('').focus();
                $results.empty();
                selectedIndex = -1;
            });

            // Close overlay via button
            $('.close-search').on('click', function () {
                $overlay.fadeOut();
                selectedIndex = -1;
            });

            // Close overlay via ESC
            $(document).on('keydown', function (e) {
                if (e.key === 'Escape') {
                    $overlay.fadeOut();
                    selectedIndex = -1;
                }
            });

            // Toggle help panel
            $('#toggleSearchHelp').on('click', function () {
                $('#searchHelpPanel').slideToggle(150);
            });

            // Keyboard navigation inside list (ArrowUp / ArrowDown / Enter)
            $input.on('keydown', function (e) {
                const $items = $results.find('li[data-url]');

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if ($items.length === 0) return;
                    selectedIndex = (selectedIndex + 1) % $items.length;
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if ($items.length === 0) return;
                    selectedIndex = (selectedIndex - 1 + $items.length) % $items.length;
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    const $selected = $items.eq(selectedIndex);
                    if ($selected.length) {
                        const url = $selected.data('url');
                        if (url) window.location.href = url;
                    }
                }

                $items.removeClass('bg-primary text-white');
                if (selectedIndex >= 0 && selectedIndex < $items.length) {
                    $items.eq(selectedIndex).addClass('bg-primary text-white');
                }
            });

            // Live search logic (incl. special "app" mode)
            $input.on('keyup', function (e) {
                if (['ArrowDown', 'ArrowUp', 'Enter'].includes(e.key)) return;

                const q       = $(this).val();
                const raw     = q.trim().toLowerCase();
                selectedIndex = -1;

                // -----------------------------------------
                // SPECIAL MODE: "app" / "app <filter>"
                // -----------------------------------------
                if (raw === 'app' || raw.startsWith('app ')) {
                    const filterTerm = raw.length > 3 ? raw.slice(3).trim() : '';
                    renderAppLauncher($results, filterTerm);
                    return;
                }

                // -----------------------------------------
                // Normal backend search
                // -----------------------------------------
                if (q.length >= 2) {
                    $.get("{{ route('contacts.global.search') }}", { q: q }, function (data) {
                        $results.empty();

                        // No results → show create buttons
                        if (!data || data.length === 0) {
                            $results.append(`
                                <li class="p-3 text-center">
                                    <div class="mb-2 text-muted">
                                        <i class="feather icon-alert-circle mr-50"></i> Kein Ergebnis gefunden.
                                    </div>
                                    <hr class="my-2">
                                    <strong class="d-block text-muted mb-2 text-left small">NEU ANLEGEN:</strong>
                                    <div class="row">
                                        <div class="col-6 mb-2">
                                            <a href="{{ route('inquiry.create') }}" class="btn btn-outline-secondary btn-sm btn-block">
                                                <i class="feather icon-file-plus"></i> Anfrage
                                            </a>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <a href="{{ route('new.lead.create') }}" class="btn btn-outline-info btn-sm btn-block">
                                                <i class="feather icon-user-plus"></i> Kunde
                                            </a>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <a href="{{ route('distributors.index') }}" class="btn btn-outline-warning btn-sm btn-block">
                                                <i class="feather icon-truck"></i> Lieferant
                                            </a>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <a href="{{ route('brand.info') }}" class="btn btn-outline-primary btn-sm btn-block">
                                                <i class="feather icon-briefcase"></i> Hersteller
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            `);
                            feather.replace();
                            return;
                        }

                        // Results found → group by type
                        const grouped = {};
                        data.forEach(item => {
                            if (!grouped[item.type]) grouped[item.type] = [];
                            grouped[item.type].push(item);
                        });

                        for (const [type, items] of Object.entries(grouped)) {
                            $results.append(`
                                <li class="search-group-header text-uppercase text-muted font-weight-bold py-1">
                                    ${type}
                                </li>
                            `);

                            items.forEach(contact => {
                                const display = `${contact.name ?? ''} ${contact.lastname ?? ''}`.trim() || `#${contact.id}`;
                                const url     = contact.url;
                                const avatar  = contact.avatar ?? "{{ asset('images/gender/male.png') }}";
                                const iconCls = contact.icon || '';
                                const iconName = iconCls.split(' ')[1] || 'user';
                                const phone   = contact.phone ?? '—';
                                const email   = contact.email ?? '—';

                                const assigned = contact.assigned_by_name
                                    ? `<br><small class="text-muted">
                                            <i class="feather icon-user white"></i> Von: ${contact.assigned_by_name}
                                    </small>`
                                    : '';

                                const dates = (contact.start_date_label || contact.end_date_label)
                                    ? `<br><small class="text-muted">
                                            <i class="feather icon-calendar white"></i>
                                            ${(contact.start_date_label ?? '')}${contact.end_date_label ? ' → ' + contact.end_date_label : ''}
                                    </small>`
                                    : '';

                                const priorityText = (contact.priority || '').toLowerCase();
                                let priorityLabel = '';
                                switch (priorityText) {
                                    case 'very high':
                                    case 'sehr hoch':
                                        priorityLabel = 'Sehr hoch';
                                        break;
                                    case 'high':
                                    case 'hoch':
                                        priorityLabel = 'Hoch';
                                        break;
                                    case 'normal':
                                        priorityLabel = 'Normal';
                                        break;
                                    default:
                                        priorityLabel = contact.priority ?? '';
                                        break;
                                }

                                const priority = priorityLabel
                                    ? `<br><small class="text-muted">
                                            <i class="feather icon-bell white"></i> ${priorityLabel}
                                    </small>`
                                    : '';

                                const attendeesText = contact.assigned_employees
                                    ? `<br><small class="text-muted">
                                            <i class="feather icon-users white"></i> ${contact.assigned_employees}
                                    </small>`
                                    : '';

                                const participants = Array.isArray(contact.participants) ? contact.participants : [];

                                const participantRow = participants.length
                                    ? `
                                        <div class="mt-50 d-flex align-items-center flex-wrap">
                                            ${participants.map(p => `
                                                <div class="mr-25" title="${(p.name || '').replace(/"/g, '&quot;')}">
                                                    <img src="${p.avatar}"
                                                        alt="${p.name || ''}"
                                                        class="rounded-circle border border-white"
                                                        width="28" height="28">
                                                </div>
                                            `).join('')}
                                        </div>
                                    `
                                    : '';

                                const isDeleted = contact.deleted_at !== null && contact.deleted_at !== undefined;

                                const deletedBadge = isDeleted
                                    ? `<span class="badge badge-pill badge-danger ml-1">Gelöscht</span>`
                                    : '';

                                const restoreButton = isDeleted
                                    ? `<button type="button"
                                            class="btn btn-sm btn-outline-light ml-1 btn-restore-item"
                                            data-id="${contact.id}"
                                            data-type="${type}">
                                        Wiederherstellen
                                    </button>`
                                    : '';

                                const isVerified = contact.verified_label && contact.verified_label === 'Verifiziert';
                                const verifiedBadge = isVerified
                                    ? `<span class="badge badge-pill badge-success ml-1">Verifiziert</span>`
                                    : '';

                                const liDeletedClass = isDeleted ? 'search-result-deleted' : '';

                                $results.append(`
                                    <li class="${liDeletedClass}"
                                        data-url="${url}"
                                        data-type="${type}"
                                        data-id="${contact.id}"
                                        data-deleted="${isDeleted ? 1 : 0}">
                                        <div class="search-result-card p-1">
                                            <div class="d-flex align-items-center">
                                                <img src="${avatar}" alt="avatar"
                                                    class="rounded-circle mr-1"
                                                    width="40" height="40">
                                                <div class="ml-1">
                                                    <p class="mb-0 font-weight-bold d-flex align-items-center" >
                                                        <i data-feather="${iconName}" class="mr-50 text-primary"></i>
                                                        ${display}
                                                        <span class="badge badge-pill badge-${getBadgeColor(type)} ml-1">
                                                            ${type}
                                                        </span>
                                                        ${verifiedBadge}
                                                        ${deletedBadge}
                                                        ${restoreButton}
                                                    </p>

                                                    <small class="text-muted">
                                                        <i class="feather icon-phone "></i> ${phone} |
                                                        <i class="feather icon-mail "></i> ${email}
                                                    </small>
                                                    ${assigned}
                                                    ${dates}
                                                    ${priority}
                                                    ${attendeesText}
                                                    ${participantRow}
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                `);
                            });
                        }

                        feather.replace();
                    });
                } else {
                    $results.empty();
                }
            });

            // Click on normal result item → open URL
            $(document).on('click', '.search-results li[data-url]', function () {
                const url = $(this).data('url');
                if (url) window.location.href = url;
            });

            // Click on app-launcher item
            $(document).on('click', '.app-launcher-item', function (e) {
                e.stopPropagation();
                const url = $(this).data('url');
                if (url) window.location.href = url;
            });

            // Restore soft-deleted entries (Kunde / Merk/Hersteller / Lieferant) from search
            $(document).on('click', '.btn-restore-item', function (e) {
                e.stopPropagation(); // do not trigger li click

                const $btn  = $(this);
                const id    = $btn.data('id');
                const type  = $btn.data('type');
                const url   = getRestoreEndpoint(type);

                if (!url) {
                    alert('Kein Restore-Endpunkt für diesen Typ: ' + type);
                    return;
                }

                $btn.prop('disabled', true).text('Wird wiederhergestellt…');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        id: id,
                        _token: '{{ csrf_token() }}'
                    }
                }).done(function () {
                    // Update UI only; do not close overlay
                    const $li = $btn.closest('li[data-url]');
                    $li.attr('data-deleted', '0')
                    .removeClass('search-result-deleted');
                    $li.find('.badge-danger').remove(); // remove "Gelöscht"
                    $btn.remove();                       // remove restore button
                }).fail(function () {
                    alert('Konnte Eintrag nicht wiederherstellen.');
                    $btn.prop('disabled', false).text('Wiederherstellen');
                });
            });
        });

        // Keyboard Shortcut: Ctrl + K (or Cmd + K on Mac) to open search
        $(document).on('keydown', function(e) {
                    const $overlay = $('.search-input-overlay');

                    // 1. OPEN: Ctrl + K (or Cmd + K)
                    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                        e.preventDefault(); 
                        if ($overlay.is(':hidden')) {
                            $('.nav-link-search').first().click(); 
                        }
                    }

                    // 2. CLOSE: Escape
                    if (e.key === 'Escape') {
                        if ($overlay.is(':visible')) {
                            e.preventDefault();
                            // Trigger the existing close button logic
                            $('.close-search').first().click();
                        }
                    }
                });
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

 
 
  <script type="module">
 
// ---- Helpers ----
const userId = {{ auth()->id() }};
const listUrl = "{{ route('get.notification.list') }}"; // your endpoint
let cache = []; // flat list for search/filter
let currentFilter = 'all';
let showUnreadOnly = false;
let nextPageSince = null; // optional pagination hook if you add it later

const $ = sel => document.querySelector(sel);
const $$ = sel => Array.from(document.querySelectorAll(sel));

function timeAgo(dateStr){
  const d = new Date(dateStr);
  const s = Math.floor((Date.now() - d.getTime())/1000);
  const mins = Math.floor(s/60), hrs = Math.floor(mins/60), days = Math.floor(hrs/24);
  if (s < 60) return `${s}s`;
  if (mins < 60) return `${mins}m`;
  if (hrs < 24) return `${hrs}h`;
  return `${days}d`;
}

function iconFor(type){
  const map = {
    lead: 'user-plus', inquiry: 'help-circle', responsible_change: 'shuffle',
    appointment: 'calendar', ticket:'alert-triangle', employee:'user',
    task:'check-square', project_task:'sliders', offer:'file-text', other:'bell'
  };
  return map[type] || 'bell';
}

function badgeUpdate(){
  const unread = cache.filter(n => !n.read_at).length;
  const badge = $('#notifUnreadBadge');
  if (!badge) return;
  if (unread > 0){ badge.textContent = unread; badge.classList.remove('d-none'); }
  else { badge.classList.add('d-none'); }
}

function toastNotify(payload){
  const title = payload.title || 'Benachrichtigung';
  const msg   = payload.message || '';
  if (window.toastr){
    toastr.options = { positionClass: 'toast-bottom-right', timeOut: 6000, progressBar: true };
    toastr.info(msg, title);
  } else {
    // Minimal fallback
    const t = document.createElement('div');
    t.style.cssText = 'position:fixed;right:16px;bottom:16px;background:#111827;color:#fff;padding:10px 12px;border-radius:10px;z-index:2000;max-width:320px;box-shadow:0 10px 20px rgba(0,0,0,.2)';
    t.innerHTML = `<strong style="display:block;margin-bottom:4px">${title}</strong><div>${msg}</div>`;
    document.body.appendChild(t);
    setTimeout(()=> t.remove(), 4000);
  }
}

// Render
function render(){
  const list = $('#notifList');
  if (!list) return;
  list.setAttribute('aria-busy','true');
  list.innerHTML = '';

  const q = ($('#notifSearch')?.value || '').toLowerCase();
  const type = currentFilter;
  const unreadOnly = showUnreadOnly || (type === 'unread');

  const items = cache.filter(n => {
    const matchesType = type === 'all' || type === 'unread' || n.type === type;
    const matchesUnread = !unreadOnly || !n.read_at;
    const matchesSearch = !q || (n.title?.toLowerCase().includes(q) || n.message?.toLowerCase().includes(q));
    return matchesType && matchesUnread && matchesSearch;
  });

  if (items.length === 0){
    list.innerHTML = `<div class="text-center text-muted mt-3">Keine Benachrichtigungen</div>`;
    list.setAttribute('aria-busy','false');
    return;
  }

  for (const n of items){
    const card = document.createElement('article');
    card.className = `notif-card ${n.read_at ? '' : 'unread'}`;
    card.dataset.id = n.id;

    card.innerHTML = `
      <div class="notif-icon">
        <i class="feather icon-${iconFor(n.type)}"></i>
      </div>
      <div class="notif-body">
        <h6>${escapeHtml(n.title || 'Benachrichtigung')}</h6>
        <p>${escapeHtml(n.message || '')}</p>
        <div class="notif-actions">
          <button class="btn btn-sm btn-outline-primary" data-action="open">Öffnen</button>
          <button class="btn btn-sm btn-outline-secondary" data-action="read">${n.read_at ? 'Gelesen' : 'Als gelesen'}</button>
          <button class="btn btn-sm btn-outline-danger" data-action="remove">Entfernen</button>
        </div>
      </div>
      <div class="notif-meta">
        <div class="text-uppercase small">${n.type ?? 'other'}</div>
        <div title="${n.performed_at}">${timeAgo(n.performed_at)}</div>
      </div>
    `;
    list.appendChild(card);
  }

  list.setAttribute('aria-busy','false');
}

function escapeHtml(s){
  return (s || '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
}

// Fetch from your existing endpoint
async function loadInitial(){
  const url = new URL(listUrl, window.location.origin);
  // if you want unread-only initial: url.searchParams.set('unread','1');
  const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
  const groups = await res.json();

  cache = [];
  Object.keys(groups).forEach(type => {
    groups[type].forEach(n => cache.push(n));
  });

  // newest first
  cache.sort((a,b) => new Date(b.performed_at) - new Date(a.performed_at));

  badgeUpdate();
  render();
}

// Mark one as read (simple, client-side visual; wire backend if you want persistence)
function markRead(id){
  const n = cache.find(x => x.id === id);
  if (!n) return;
  n.read_at = n.read_at || new Date().toISOString();
  badgeUpdate();
  render();
}

// Remove from UI (optional)
function removeItem(id){
  cache = cache.filter(x => x.id !== id);
  badgeUpdate();
  render();
}

// Controls
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
$('#notifSearch')?.addEventListener('input', render);
$('#loadMoreBtn')?.addEventListener('click', () => {
  // Optional: implement pagination with a since cursor on your endpoint
  // For now, just re-fetch:
  loadInitial();
});

// Card action buttons
$('#notifList')?.addEventListener('click', (e) => {
  const btn = e.target.closest('button[data-action]');
  if (!btn) return;
  const action = btn.dataset.action;
  const card = e.target.closest('.notif-card');
  const id = card?.dataset.id;

  if (!id) return;

  if (action === 'read') markRead(id);
  if (action === 'remove') removeItem(id);
  if (action === 'open'){
    // Route user wherever makes sense per type; here we just mark read
    markRead(id);
  }
});

// “Mark all read”
$('#markAllReadBtn')?.addEventListener('click', () => {
  cache.forEach(n => n.read_at = n.read_at || new Date().toISOString());
  badgeUpdate();
  render();
});

// ---- Realtime: subscribe to notifications for this user ----
window.Echo.private(`App.Models.User.${userId}`)
  .notification((payload) => {
    // payload contains ['type','title','message','performed_at', 'id' => notification uuid]
    toastNotify(payload);

    // Push into cache at top and re-render
    const normalized = {
      id: payload.id || crypto.randomUUID(),
      type: payload.type || 'other',
      title: payload.title || '',
      message: payload.message || '',
      performed_at: payload.performed_at || new Date().toISOString(),
      read_at: null,
    };
    cache.unshift(normalized);
    badgeUpdate();
    render();
  });

// ---- Open/Close API (works with your existing toggleSidebar if you want) ----
window.openNotificationSlider = function(){
  document.body.classList.remove('qs-open'); // ensure quick sider closed
  document.body.classList.add('notif-open');
  loadInitial(); // refresh on open
};
window.closeSidebar = function(){
  document.body.classList.remove('notif-open');
};

// optional: open via any bell button you have
document.querySelectorAll('[data-open="notifications"]').forEach(b => {
  b.addEventListener('click', (e) => { e.preventDefault(); openNotificationSlider(); });
});

// Initial load (so badge is correct even before opening)
loadInitial();
</script>

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

 
 <!-- Chat  -->

 <script>
    (function () {
    // -------------------------------------------------------------------------
    // Configuration & State
    // -------------------------------------------------------------------------
    const me = {
        id: Number(document.body.dataset.userId || window.userId || 0),
        name: (document.body.dataset.userName || window.userName || "").trim(),
    };

    if (!me.id) return; // Exit if not logged in

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const sidebarEl = document.getElementById('fb-right-sidebar');
    const containerEl = document.getElementById('fb-bottom-chats-container');

    // State
    let contacts = []; 
    let activeChats = []; // Array of chat objects { key, type, id, name... }
    const MAX_VISIBLE_CHATS = 3; // Prevent clutter

    // -------------------------------------------------------------------------
    // 1. Sidebar Logic (Contacts)
    // -------------------------------------------------------------------------
    async function loadContacts() {
        try {
            const res = await fetch("/chat/employees", { headers: { "X-Requested-With": "XMLHttpRequest" } });
            const data = await res.json();
            
            // Merge users and groups for simplicity
            const users = (data.employees || []).map(u => ({
                ...u, type: 'user', key: `u-${u.id}`, 
                displayName: `${u.name} ${u.lastname}`.trim()
            }));
            
            const groups = (data.groups || []).map(g => ({
                ...g, type: 'group', key: `g-${g.id}`,
                displayName: g.name || 'Gruppe'
            }));

            contacts = [...users, ...groups].sort((a,b) => {
                // Sort by unread count first, then online status
                return (b.unread || 0) - (a.unread || 0) || (b.online ? 1 : 0) - (a.online ? 1 : 0);
            });

            renderSidebar();
        } catch (e) {
            console.error("Failed to load contacts", e);
        }
    }

    function renderSidebar() {
        sidebarEl.innerHTML = ''; // Clear

        contacts.forEach(c => {
            const item = document.createElement('div');
            item.className = 'fb-contact-item';
            item.dataset.key = c.key;
            item.onclick = () => openChatWindow(c);

            // Avatar Logic
            let avatarHtml = '';
            if (c.image || c.avatar) {
                const src = c.image || c.avatar; // Adjust path logic if needed
                const finalSrc = src.startsWith('http') ? src : `/images/employee/${src}`; // Example path
                avatarHtml = `<img src="${finalSrc}" class="fb-avatar" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">`;
            }
            
            // Initials Fallback
            const initials = c.displayName.substring(0, 2).toUpperCase();
            const initialsHtml = `<div class="fb-initials" ${c.image ? 'style="display:none"' : ''}>${initials}</div>`;

            // Status Dot
            const statusClass = c.online ? 'online' : 'offline';
            const statusHtml = c.type === 'user' ? `<div class="fb-status-dot ${statusClass}"></div>` : '';

            // Unread Badge
            const badgeHtml = (c.unread > 0) ? `<div class="fb-avatar-badge">${c.unread}</div>` : '';

            // Tooltip
            const tooltipHtml = `<div class="fb-contact-tooltip">${c.displayName}</div>`;

            item.innerHTML = `${avatarHtml}${initialsHtml}${statusHtml}${badgeHtml}${tooltipHtml}`;
            sidebarEl.appendChild(item);
        });
    }

    // -------------------------------------------------------------------------
    // 2. Chat Window Management
    // -------------------------------------------------------------------------
    function openChatWindow(contact) {
        // Check if already open
        const existing = activeChats.find(c => c.key === contact.key);
        
        if (existing) {
            // If active, just ensure it's visible and focus input
            const win = document.getElementById(`chat-win-${contact.key}`);
            if (win) {
                win.classList.remove('minimized');
                win.querySelector('input')?.focus();
            }
            return;
        }

        // Add to active list
        activeChats.push(contact);
        
        // Remove oldest if limit reached
        if (activeChats.length > MAX_VISIBLE_CHATS) {
            const removed = activeChats.shift();
            removeChatDOM(removed.key);
        }

        renderChatWindow(contact);
    }

    function removeChatDOM(key) {
        const el = document.getElementById(`chat-win-${key}`);
        if (el) el.remove();
    }

    function closeChat(key) {
        activeChats = activeChats.filter(c => c.key !== key);
        removeChatDOM(key);
    }

    function toggleMinimize(key) {
        const win = document.getElementById(`chat-win-${key}`);
        if (win) win.classList.toggle('minimized');
    }

    function renderChatWindow(contact) {
        const win = document.createElement('div');
        win.className = 'fb-chat-window';
        win.id = `chat-win-${contact.key}`;
        
        win.innerHTML = `
            <div class="fb-chat-header" onclick="window.fbToggle('${contact.key}')">
                <div class="fb-chat-title">${contact.displayName}</div>
                <div class="fb-chat-controls">
                    <i class="feather icon-minus" title="Minimieren"></i>
                    <i class="feather icon-x" onclick="event.stopPropagation(); window.fbClose('${contact.key}')" title="Schließen"></i>
                </div>
            </div>
            <div class="fb-chat-body" id="chat-body-${contact.key}">
                <div class="text-center text-muted mt-4"><small>Lade Nachrichten...</small></div>
            </div>
            <div class="fb-chat-footer">
                <button class="fb-chat-btn"><i class="feather icon-paperclip"></i></button>
                <input type="text" class="fb-chat-input" placeholder="Nachricht..." 
                       onkeydown="if(event.key==='Enter') window.fbSend('${contact.key}', this)">
                <button class="fb-chat-btn"><i class="feather icon-send"></i></button>
            </div>
        `;

        containerEl.appendChild(win);
        fetchMessages(contact);
    }

    // -------------------------------------------------------------------------
    // 3. Messaging Logic
    // -------------------------------------------------------------------------
    async function fetchMessages(contact) {
        const body = document.getElementById(`chat-body-${contact.key}`);
        const url = contact.type === 'group' 
            ? `/chat/group/fetch/${contact.id}` 
            : `/chat/fetch/${contact.id}`;

        try {
            const res = await fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } });
            const msgs = await res.json();
            
            body.innerHTML = ''; // Clear loading
            
            if (msgs.length === 0) {
                body.innerHTML = '<div class="text-center text-muted mt-4"><small>Keine Nachrichten.</small></div>';
            }

            // Sort and render
            msgs.sort((a,b) => new Date(a.created_at) - new Date(b.created_at))
                .forEach(m => appendMessage(contact.key, m));
            
            scrollToBottom(contact.key);
            markAsRead(contact);

        } catch (e) {
            body.innerHTML = '<div class="text-center text-danger mt-4"><small>Fehler beim Laden.</small></div>';
        }
    }

    function appendMessage(key, msg) {
        const body = document.getElementById(`chat-body-${key}`);
        if (!body) return;

        const isMe = Number(msg.from_user_id) === me.id;
        const div = document.createElement('div');
        div.className = `fb-msg ${isMe ? 'outgoing' : 'incoming'}`;
        
        // Basic text content (you can expand this for images/files similar to mini_chat.js)
        div.innerHTML = `
            <div>${msg.message || '<i>Anhang</i>'}</div>
            <div class="fb-msg-meta">${new Date(msg.created_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}</div>
        `;

        body.appendChild(div);
        scrollToBottom(key);
    }

    function scrollToBottom(key) {
        const body = document.getElementById(`chat-body-${key}`);
        if (body) body.scrollTop = body.scrollHeight;
    }

    window.fbSend = async function(key, input) {
        const text = input.value.trim();
        if (!text) return;

        const contact = activeChats.find(c => c.key === key);
        if (!contact) return;

        input.value = ''; // Clear immediately

        const payload = {
            message: text,
            type: "text",
            to_user_id: contact.type === 'user' ? contact.id : null,
            group_id: contact.type === 'group' ? contact.id : null,
        };

        try {
            const res = await fetch("/chat/send", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf,
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: JSON.stringify(payload),
            });
            const data = await res.json();
            if (data.message) {
                appendMessage(key, data.message);
            }
        } catch (e) {
            console.error("Send failed", e);
        }
    };

    function markAsRead(contact) {
        const url = contact.type === 'group' 
            ? `/chat/group/mark-read/${contact.id}` 
            : `/chat/mark-read/${contact.id}`;
        
        fetch(url, { 
            method: "POST", 
            headers: { "X-CSRF-TOKEN": csrf, "X-Requested-With": "XMLHttpRequest" } 
        }).catch(() => {});
        
        // Reset local badge
        contact.unread = 0;
        renderSidebar();
    }

    // -------------------------------------------------------------------------
    // 4. Global Helpers & Echo
    // -------------------------------------------------------------------------
    window.fbClose = closeChat;
    window.fbToggle = toggleMinimize;

    // Real-time listener
    if (window.Echo) {
        window.Echo.private(`chat.user.${me.id}`)
            .listen(".message-sent", (e) => {
                // Determine chat key
                const isGroup = !!e.group_id;
                const key = isGroup ? `g-${e.group_id}` : `u-${e.from_user_id}`;
                
                // 1. Is window open?
                const isOpen = activeChats.find(c => c.key === key);
                
                if (isOpen) {
                    appendMessage(key, e);
                    // Flash header
                    const header = document.querySelector(`#chat-win-${key} .fb-chat-header`);
                    if(header) {
                        header.classList.add('flash');
                        setTimeout(() => header.classList.remove('flash'), 3000);
                    }
                } else {
                    // 2. Update contact unread count
                    const contact = contacts.find(c => c.key === key);
                    if (contact) {
                        contact.unread = (contact.unread || 0) + 1;
                        renderSidebar(); // Update sidebar badge
                    }
                }
            });
            
        // Presence (Online Status)
        window.Echo.join("online")
            .here(users => {
                users.forEach(u => updateStatus(u.id, true));
            })
            .joining(u => updateStatus(u.id, true))
            .leaving(u => updateStatus(u.id, false));
    }

    function updateStatus(userId, isOnline) {
        const c = contacts.find(x => x.type === 'user' && x.id === userId);
        if (c) {
            c.online = isOnline;
            renderSidebar();
        }
    }

    // Init
    loadContacts();

})();
 </script>

 

@stack('scripts')
@yield('script')


</body>

</html>