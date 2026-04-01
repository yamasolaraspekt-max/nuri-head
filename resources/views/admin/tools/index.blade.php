@extends('admin.layouts.app')
@section('title') BELEGUNGSTOOL @endsection
@section('style')
<!-- Optional: Include Three.js from a CDN -->

<style>
    :root {
        --primary-color: #569ad8;
        /* Updated primary brand color */
        --secondary-color: #9fbdd8;
        /* Updated secondary color */
        --success-color: #94c11c;
        /* Updated success color */
        --danger-color: #cfe09a;
        /* No change for warnings/errors */
        --warning-color: #ffc107;
        /* No change for caution */
        --info-color: #17a2b8;
        /* No change for general info */
        --light-color: #f8f9fa;
        /* No change for light elements */
        --dark-color: #343a40;
        /* No change for dark elements */
    }

    hr {
        border: none;
        height: 2px;
        background-color: var(--primary-color);
        /* Use the updated secondary color */
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

    #container {
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
    }


    #container_items {
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        overflow-x: auto;
        overflow-y: hidden;
        white-space: nowrap;
        width: 100%;
        max-height: 200px;
        gap: 21px;
        justify-content: center;
    }

    .nav-item {
        flex-shrink: 0;
        /* Prevent items from shrinking */
        margin-right: 10px;
        /* Space between items */
    }

    .menu-items {
        text-align: center;
        /* Center content inside the dropdown */
        overflow: hidden;
        /* Additional precaution to hide overflow inside items */
    }

    #sub_menu {
        text-align: center;
        border: 2px solid var(--primary-color);
        border-radius: 50%;
        width: 50px;
        height: 50px;
    }

    #sub_menu:hover {
        text-align: center;
        border: 5px solid var(--primary-color);
        border-radius: 50%;
        width: 60px;
        height: 60px;
    }

    canvas {
        width: 100% !important;
    }
</style>
@vite(['resources/js/pvtools/app.js'])
@endsection
@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-center col-md-12 col-12 mb-2" style="text-align: center;">
                <h4 class="content-header-title float-center mb-0"
                    style="font-weight: bold; color: var(--primary-color);">
                    BELEGUNGSTOOL
                </h4>

            </div>
        </div>
        <hr>
        <div class="content-body">
            <div class="navbar-wrapper">
                <div class="navbar-container content">
                    <ul class="nav navbar-nav" id="container">
                            <li class="dropdown dropdown-language nav-item" style="text-align: center;">
                                    <a class="dropdown-toggle nav-link" id="menu" href="#" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <div class="col-12 menu-items">
                                            <h6>DACH FÄCHEN </h6>
                                            <p>1. Süd-West Dach</p>

                                            <div class="avatar mr-0 avatar-lg" style="bottom: 20px;">
                                                <img src="../../../app-assets/images/portrait/small/avatar-s-20.jpg" alt="avtar img holder">
                                            </div>
                                        </div>

                                    </a>
                                </li>

                                <li class="dropdown dropdown-language nav-item" style="text-align: center;">
                                    <a class="dropdown-toggle nav-link" id="menu" href="#" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <div class="col-12 menu-items">
                                            <h6>DACH FÄCHEN </h6>
                                            <p>1. Süd-West Dach</p>

                                            <div class="avatar mr-0 avatar-lg" style="bottom: 20px;">
                                                <img src="../../../app-assets/images/portrait/small/avatar-s-20.jpg" alt="avtar img holder">
                                            </div>
                                        </div>

                                    </a>
                                </li>

                                <li class="dropdown dropdown-language nav-item" style="text-align: center;">
                                    <a class="dropdown-toggle nav-link" id="menu" href="#" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <div class="col-12 menu-items">
                                            <h6>DACH FÄCHEN </h6>
                                            <p>1. Süd-West Dach</p>

                                            <div class="avatar mr-0 avatar-lg" style="bottom: 20px;">
                                                <img src="../../../app-assets/images/portrait/small/avatar-s-20.jpg" alt="avtar img holder">
                                            </div>
                                        </div>

                                    </a>
                                </li>

                                <li class="dropdown dropdown-language nav-item" style="text-align: center;">
                                    <a class="dropdown-toggle nav-link" id="menu" href="#" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <div class="col-12 menu-items">
                                            <h6>DACH FÄCHEN </h6>
                                            <p>1. Süd-West Dach</p>

                                            <div class="avatar mr-0 avatar-lg" style="bottom: 20px;">
                                                <img src="../../../app-assets/images/portrait/small/avatar-s-20.jpg" alt="avtar img holder">
                                            </div>
                                        </div>

                                    </a>
                                </li>

                                <li class="dropdown dropdown-language nav-item" style="text-align: center;">
                                    <a class="dropdown-toggle nav-link" id="menu" href="#" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <div class="col-12 menu-items">
                                            <h6>DACH FÄCHEN </h6>
                                            <p>1. Süd-West Dach</p>

                                            <div class="avatar mr-0 avatar-lg" style="bottom: 20px;">
                                                <img src="../../../app-assets/images/portrait/small/avatar-s-20.jpg" alt="avtar img holder">
                                            </div>
                                        </div>

                                    </a>
                                </li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="navbar-wrapper">
                <div class="navbar-container content">
                    <ul class="nav navbar-nav" id="container_items">
                        @include('admin.tools.pages.sub_menu')

                    </ul>
                </div>
            </div>
            <hr>

            @include('admin.tools.pages.convas')
        </div>

    </div>
</div>
@endsection
@section('script')

<script src="https://unpkg.com/suncalc"></script>


@endsection