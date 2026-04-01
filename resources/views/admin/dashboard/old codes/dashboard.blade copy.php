@extends('admin.layouts.app')

@section('title') Employee Dashboard @endsection
@section('style')
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
        width: 170px;
        height: 170px;
        background: #569ad8;
        border: 10px solid #9fbdd8;
        border-radius: 50%;
        place-content: center;
        color: white;
        position: relative;
    }

    #menu:hover {
        background: var(--success-color);
        border: 10px solid var(--danger-color);
    }

    #menu>.menu-items>h6 {
        font-weight: bold;
        color: white;
        font-size: 16px;
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
        top: 19px;
        text-align: center;
    }

    #container_new {
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        overflow-x: auto;
        overflow-y: hidden;
        white-space: nowrap;
        width: 100%;
        max-height: 200px;
        gap: 21px;
        justify-content: space-evenly; 
         margin-top: 102px !important;
        ::-webkit-scrollbar {
        display: none;
       
}
    }

    #container_items {
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        /* overflow-x: auto; */
        /* overflow-y: hidden; */
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

    .menu-items {
        text-align: center;
        overflow: hidden;
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
        overflow: hidden;
        z-index: 10000;
        border-radius: 6px;
        width: 100%; 
    }

    #sub_menu {
        text-align: center;
          width: 50px;
    height: 50px;
    }

    #sub_menu:hover {
        text-align: center;
    
    
        width: 60px;
        height: 60px;
    }

    canvas {
        width: 100% !important;
    }

    .badge {
        padding: 10px;
        font-size: 10px;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        font-weight: 800;
    }

    .item_lists {
            font-size: 26px;
    margin-right: 15px;
    margin-top: -1px;
    }
    .fc-daygrid-day-number{
        padding: 4px 8px !important;
    }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0"> MEIN BEREICH</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">DASHBOARD</a></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body"> 
            <section id="dashboard-analytics">
                    <div class="row">
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <div class="card bg-primary pt-2 text-white">
                                <div class="card-content" id="banner">
                                    <div class="card-body text-center">
                                        <!-- <img src="{{ asset('app-assets/images/elements/decore-left.png')}}" class="img-left" alt="card-img-left">
                                        <img src="{{ asset('app-assets/images/elements/decore-right.png')}}" class="img-right" alt="card-img-right"> -->
                                        <div class="avatar avatar-xl bg-primary shadow mt-0">
                                            <div class="avatar-content">
                                                <span><img class="round"
                                                        src="{{ asset('images/user/'.auth()->user()->image)}}"
                                                        alt="avatar" height="40" width="40"></span>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <h1 class="mb-2 text-white">Hallo
                                                {{ DB::table('users')->join('employees', 'employees.id', '=', 'users.name' )->where('users.name', '=', auth()->user()->name)->select('employees.name')->pluck('name')->first() }}
                                                {{ DB::table('users')->join('employees', 'employees.id', '=', 'users.name' )->where('users.name', '=', auth()->user()->name)->select('employees.lastname')->pluck('lastname')->first() }}
                                            </h1>
                                            <p class="m-auto w-75">Your login date is : {{ \Carbon\Carbon::now() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                       <div class="col-lg-4 col-md-6 col-12">
                            <div class="card" style="background:#b0d5f2">
                                <div class="card-header d-flex flex-column align-items-start pb-0">
                                    @if(isset($weather['main']))
                                    <h2 class="text-bold-700 mt-1 mb-25">
                                        <span>
                                            <img src="http://openweathermap.org/img/w/{{ $weather['weather'][0]['icon'] }}.png" 
                                                alt="Weather Icon" style=" width: 110px;">
                                        </span>
                                       <span style="color:white;font-size: 50px;"> {{ $weather['main']['temp'] }} °C</span>
                                    </h2>

                                    <h6 class="mb-3">
                                        <span class="mr-2 warning" style="font-size: 20px;"><i class="feather icon-chevron-down "></i> {{ $weather['main']['temp_min'] }} °C</span>
                                        <span class="primary" style="font-size: 20px;"><i class="feather icon-chevron-up "></i> {{ $weather['main']['temp_max'] }} °C</span>
                                    </h6> 
                                    @else
                                    <h2 class="text-bold-700 mt-1 mb-25">
                                        <p>Unable to fetch weather data</p>
                                    </h2>
                                    @endif
                                </div>
                                <div class="card-content">
                                    <div id="orders-received-chart"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card text-center" style="height:215px">
                                <div class="card-content">
                                    <div class="card-body mt-2">
                                        <div class="avatar bg-rgba-info p-50 m-0 mb-1">
                                            <div class="avatar-content">
                                                <i class="feather icon-clock text-info font-medium-5"></i>
                                            </div>
                                        </div>
                                        <h2 class="text-bold-700">STARTEN</h2>
                                        <p class="mb-0 line-ellipsis">Clock in: {{ \Carbon\Carbon::parse(now())->isoFormat('HH:MM:SS')}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <section id="statistics-card">
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
                                <div class="card-header d-flex align-items-start pb-0">
                                    <div>
                                        <h2 class="text-bold-700 mb-0">86%</h2>
                                        <p>CPU Usage</p>
                                    </div>
                                    <div class="avatar bg-rgba-primary p-50 m-0">
                                        <div class="avatar-content">
                                            <i class="feather icon-cpu text-primary font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-start pb-0">
                                    <div>
                                        <h2 class="text-bold-700 mb-0">1.2gb</h2>
                                        <p>Memory Usage</p>
                                    </div>
                                    <div class="avatar bg-rgba-success p-50 m-0">
                                        <div class="avatar-content">
                                            <i class="feather icon-server text-success font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-start pb-0">
                                    <div>
                                        <h2 class="text-bold-700 mb-0">0.1%</h2>
                                        <p>Downtime Ratio</p>
                                    </div>
                                    <div class="avatar bg-rgba-danger p-50 m-0">
                                        <div class="avatar-content">
                                            <i class="feather icon-activity text-danger font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-start pb-0">
                                    <div>
                                        <h2 class="text-bold-700 mb-0">13</h2>
                                        <p>Issues Found</p>
                                    </div>
                                    <div class="avatar bg-rgba-warning p-50 m-0">
                                        <div class="avatar-content">
                                            <i class="feather icon-alert-octagon text-warning font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>  
                </section>
                
                <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="mb-0">Dispatched Orders</h4>
                                </div>
                                <div class="card-content">
                                    <div class="table-responsive mt-1">
                                        <table class="table table-hover-animation mb-0">
                                            <thead>
                                                <tr>
                                                    <th>ORDER</th>
                                                    <th>STATUS</th>
                                                    <th>OPERATORS</th>
                                                    <th>LOCATION</th>
                                                    <th>DISTANCE</th>
                                                    <th>START DATE</th>
                                                    <th>EST DEL. DT</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>#879985</td>
                                                    <td><i class="fa fa-circle font-small-3 text-success mr-50"></i>Moving</td>
                                                    <td class="p-1">
                                                        <ul class="list-unstyled users-list m-0  d-flex align-items-center">
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Vinnie Mostowy" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-5.jpg" alt="Avatar" height="30" width="30">
                                                            </li>
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Elicia Rieske" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-7.jpg" alt="Avatar" height="30" width="30">
                                                            </li>
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Julee Rossignol" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-10.jpg" alt="Avatar" height="30" width="30">
                                                            </li>
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Darcey Nooner" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-8.jpg" alt="Avatar" height="30" width="30">
                                                            </li>
                                                        </ul>
                                                    </td>
                                                    <td>Anniston, Alabama</td>
                                                    <td>
                                                        <span>130 km</span>
                                                        <div class="progress progress-bar-success mt-1 mb-0">
                                                            <div class="progress-bar" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </td>
                                                    <td>14:58 26/07/2018</td>
                                                    <td>28/07/2018</td>
                                                </tr>
                                                <tr>
                                                    <td>#156897</td>
                                                    <td><i class="fa fa-circle font-small-3 text-warning mr-50"></i>Pending</td>
                                                    <td class="p-1">
                                                        <ul class="list-unstyled users-list m-0  d-flex align-items-center">
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Trina Lynes" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-1.jpg" alt="Avatar" height="30" width="30">
                                                            </li>
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Lilian Nenez" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-2.jpg" alt="Avatar" height="30" width="30">
                                                            </li>
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Alberto Glotzbach" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-3.jpg" alt="Avatar" height="30" width="30">
                                                            </li>
                                                        </ul>
                                                    </td>
                                                    <td>Cordova, Alaska</td>
                                                    <td>
                                                        <span>234 km</span>
                                                        <div class="progress progress-bar-warning mt-1 mb-0">
                                                            <div class="progress-bar" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </td>
                                                    <td>14:58 26/07/2018</td>
                                                    <td>28/07/2018</td>
                                                </tr>
                                                <tr>
                                                    <td>#568975</td>
                                                    <td><i class="fa fa-circle font-small-3 text-success mr-50"></i>Moving</td>
                                                    <td class="p-1">
                                                        <ul class="list-unstyled users-list m-0  d-flex align-items-center">
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Lai Lewandowski" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-6.jpg" alt="Avatar" height="30" width="30">
                                                            </li>
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Elicia Rieske" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-7.jpg" alt="Avatar" height="30" width="30">
                                                            </li>
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Darcey Nooner" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-8.jpg" alt="Avatar" height="30" width="30">
                                                            </li>
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Julee Rossignol" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-10.jpg" alt="Avatar" height="30" width="30">
                                                            </li>
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Jeffrey Gerondale" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-9.jpg" alt="Avatar" height="30" width="30">
                                                            </li>
                                                        </ul>
                                                    </td>
                                                    <td>Florence, Alabama</td>
                                                    <td>
                                                        <span>168 km</span>
                                                        <div class="progress progress-bar-success mt-1 mb-0">
                                                            <div class="progress-bar" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </td>
                                                    <td>14:58 26/07/2018</td>
                                                    <td>28/07/2018</td>
                                                </tr>
                                                <tr>
                                                    <td>#245689</td>
                                                    <td><i class="fa fa-circle font-small-3 text-danger mr-50"></i>Canceled</td>
                                                    <td class="p-1">
                                                        <ul class="list-unstyled users-list m-0  d-flex align-items-center">
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Vinnie Mostowy" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-5.jpg" alt="Avatar" height="30" width="30">
                                                            </li>
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Elicia Rieske" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-7.jpg" alt="Avatar" height="30" width="30">
                                                            </li>
                                                        </ul>
                                                    </td>
                                                    <td>Clifton, Arizona</td>
                                                    <td>
                                                        <span>125 km</span>
                                                        <div class="progress progress-bar-danger mt-1 mb-0">
                                                            <div class="progress-bar" role="progressbar" style="width: 95%" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </td>
                                                    <td>14:58 26/07/2018</td>
                                                    <td>28/07/2018</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                       
                    <div class="row">
                        <!-- permissions start -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header border-bottom mx-2 px-0">
                                    <h6 class="border-bottom py-1 mb-0 font-medium-2"><i
                                            class="feather icon-lock mr-50 "></i>Permission
                                    </h6>
                                </div>
                                <div class="card-body px-75">
                                    <div class="table-responsive users-view-permission">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th>Module</th>
                                                    <th>Read</th>
                                                    <th>Update</th>
                                                    <th>Create</th>
                                                    <th>Delete</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($premission as $pre)
                                                @if($pre->user_id==auth()->user()->id)
                                                <tr>
                                                    <td>{{ $pre->item_id}}</td>
                                                    <td>
                                                        @if($pre->is_read=='on')
                                                        <div class="custom-control custom-checkbox ml-50"><input
                                                                type="checkbox" id="users-checkbox1"
                                                                class="custom-control-input" disabled checked>
                                                            <label class="custom-control-label"
                                                                for="users-checkbox1"></label>
                                                        </div>
                                                        @else
                                                        <div class="custom-control custom-checkbox ml-50"><input
                                                                type="checkbox" id="users-checkbox1"
                                                                class="custom-control-input" disabled unchecked>
                                                            <label class="custom-control-label"
                                                                for="users-checkbox1"></label>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($pre->is_update=='on')
                                                        <div class="custom-control custom-checkbox ml-50"><input
                                                                type="checkbox" id="users-checkbox1"
                                                                class="custom-control-input" disabled checked>
                                                            <label class="custom-control-label"
                                                                for="users-checkbox1"></label>
                                                        </div>
                                                        @else
                                                        <div class="custom-control custom-checkbox ml-50"><input
                                                                type="checkbox" id="users-checkbox1"
                                                                class="custom-control-input" disabled unchecked>
                                                            <label class="custom-control-label"
                                                                for="users-checkbox1"></label>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($pre->is_add=='on')
                                                        <div class="custom-control custom-checkbox ml-50"><input
                                                                type="checkbox" id="users-checkbox1"
                                                                class="custom-control-input" disabled checked>
                                                            <label class="custom-control-label"
                                                                for="users-checkbox1"></label>
                                                        </div>
                                                        @else
                                                        <div class="custom-control custom-checkbox ml-50"><input
                                                                type="checkbox" id="users-checkbox1"
                                                                class="custom-control-input" disabled unchecked>
                                                            <label class="custom-control-label"
                                                                for="users-checkbox1"></label>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($pre->is_delete=='on')
                                                        <div class="custom-control custom-checkbox ml-50"><input
                                                                type="checkbox" id="users-checkbox1"
                                                                class="custom-control-input" disabled checked>
                                                            <label class="custom-control-label"
                                                                for="users-checkbox1"></label>
                                                        </div>
                                                        @else
                                                        <div class="custom-control custom-checkbox ml-50"><input
                                                                type="checkbox" id="users-checkbox1"
                                                                class="custom-control-input" disabled unchecked>
                                                            <label class="custom-control-label"
                                                                for="users-checkbox1"></label>
                                                        </div>
                                                        @endif
                                                    </td>

                                                </tr>
                                                @endif
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- permissions end -->
                    </div>
                </section>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const menus = document.querySelectorAll('.menu');
    menus.forEach(menu => {
        const submenu = menu.querySelector('.submenu');
        menu.addEventListener("click", function() {
            const isDisplayed = submenu.style.display === "block";
            document.querySelectorAll('.submenu').forEach(sub => sub.style.display = "none");
            submenu.style.display = isDisplayed ? "none" : "block";
        });
    });

    document.addEventListener("click", function(event) {
        if (!event.target.closest('.menu')) {
            document.querySelectorAll('.submenu').forEach(sub => sub.style.display = "none");
        }
    });
});
</script>
@endsection
