@extends('admin.layouts.app')
@section('style')
<style>
   
       
        :root {
        --level-1: #8fc73e;
        --level-2: #74b2d4;
        --level-3: #b0d5f2;
        --level-4: #ececec;
        --black: black;
        }

        * {
        padding: 0;
        margin: 0;
        box-sizing: border-box;
        }

        ol {
        list-style: none;
        }

        body {
        margin: 50px 0 100px;
        text-align: center;
        font-family: "Inter", sans-serif;
        }

        .container {
        max-width: 1000px;
        padding: 0 10px;
        margin: 0 auto;
        }

        .rectangle {
        position: relative;
        padding: 20px; 
        background-color:rgb(255, 255, 255); 
        text-align: center;
        margin: 10px;  
        }


        /* LEVEL-1 STYLES
        –––––––––––––––––––––––––––––––––––––––––––––––––– */
        .level-1 {
        width: 50%;
        margin: 0 auto 40px;
        border-left:12px solid var(--level-1);
        }

        .level-1::before {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        width: 2px;
        height: 20px;
        background: var(--black);
        }


        /* LEVEL-2 STYLES
        –––––––––––––––––––––––––––––––––––––––––––––––––– */
        .level-2-wrapper {
        position: relative;
        display: grid;
        grid-template-columns: repeat(7, 7fr);
        }

        .level-2-wrapper::before {
        content: "";
        position: absolute;
        top: -20px;
        left: 0;
        width: 100%;
        height: 2px;
        background: var(--black);
        }

        .level-2-wrapper::after {
        display: none;
        content: "";
        position: absolute;
        left: -20px;
        bottom: -20px;
        width: calc(100% + 20px);
        height: 2px;
        background: var(--black);
        }

        .level-2-wrapper li {
        position: relative;
        }

        .level-2-wrapper > li::before {
        content: "";
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        width: 2px;
        height: 20px;
        background: var(--black);
        }

        .level-2 {
        width: 70%;
        margin: 0 auto 40px;
        background: var(--level-2);
        }

        .level-2::before {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        width: 2px;
        height: 20px;
        background: var(--black);
        }

        .level-2::after {
        display: none;
        content: "";
        position: absolute;
        top: 50%;
        left: 0%;
        transform: translate(-100%, -50%);
        width: 20px;
        height: 2px;
        background: var(--black);
        }


        /* LEVEL-3 STYLES
        –––––––––––––––––––––––––––––––––––––––––––––––––– */
        .level-3-wrapper {
        position: relative;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-column-gap: 20px;
        width: 90%;
        margin: 0 auto;
        }

        .level-3-wrapper::before {
        content: "";
        position: absolute;
        top: -20px;
        left: calc(25% - 5px);
        width: calc(50% + 10px);
        height: 2px;
        background: var(--black);
        }

        .level-3-wrapper > li::before {
        content: "";
        position: absolute;
        top: 0;
        left: 50%;
        transform: translate(-50%, -100%);
        width: 2px;
        height: 20px;
        background: var(--black);
        }

        .level-3 {
        margin-bottom: 20px;
        background: var(--level-3);
        }


        /* LEVEL-4 STYLES
        –––––––––––––––––––––––––––––––––––––––––––––––––– */
        .level-4-wrapper {
        position: relative;
        width: 80%;
        margin-left: auto;
        }

        .level-4-wrapper::before {
        content: "";
        position: absolute;
        top: -20px;
        left: -20px;
        width: 2px;
        height: calc(100% + 20px);
        background: var(--black);
        }

        .level-4-wrapper li + li {
        margin-top: 20px;
        }

        .level-4 {
        font-weight: normal;
        background: var(--level-4);
        }

        .level-4::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 0%;
        transform: translate(-100%, -50%);
        width: 20px;
        height: 2px;
        background: var(--black);
        }


        /* MQ STYLES
        –––––––––––––––––––––––––––––––––––––––––––––––––– */
        @media screen and (max-width: 700px) {
        .rectangle {
            padding: 20px 10px;
        }

        .level-1,
        .level-2 {
            width: 100%;
        }

        .level-1 {
            margin-bottom: 20px;
        }

        .level-1::before,
        .level-2-wrapper > li::before {
            display: none;
        }
        
        .level-2-wrapper,
        .level-2-wrapper::after,
        .level-2::after {
            display: block;
        }

        .level-2-wrapper {
            width: 90%;
            margin-left: 10%;
        }

        .level-2-wrapper::before {
            left: -20px;
            width: 2px;
            height: calc(100% + 40px);
        }

        .level-2-wrapper > li:not(:first-child) {
            margin-top: 50px;
        }
        }


        .plus-button {
               position: relative;
            left: 103%;
            width: 20px;
            background: white;
            bottom: 34px;
        }

        .plus-button:hover {
            background:#d6d1d1;
            color:white !important;
        }
        .plus-button i:hover { 
            color:white !important;
        }

        .plus-button i{ 
            font-size: 18px; 
            color: black; 

        }

        .name {
            font-size:16px; 
        }

        .hide-button {
            width: 50px;
            position: absolute;
            left: 45%;
            bottom: -9%;
            background:white;
           
        }

        .hide-button:hover { 
            background:#d6d1d1;
            color:white !important; 
           
        }
 
        .hide-button i{ 
            font-size: 18px; 
            color: black;

        }


        .hide-button i:hover{  
            color: white;

        }

        .contact {
            border-top: 1px solid;
            width: 289px;
            align-self: center;
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
                <h2 class="content-header-title float-left mb-0">AUFTRÄGE</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active"><a href="{{ url('/employee_dashboard') }}">Neue</a></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body"> 
            <div class="container"> 
                <div class="card parent level-1 rectangle">
                    <div class="name">Stella Payne Diaz</div>
                    <div class="role">CEO</div> 
                    <div class="contact"> 
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
                    </div>
                    <div class="hide-button">
                        <i class="feather icon-chevron-down"></i>
                    </div>
                    <div class="plus-button">
                        <i class="feather icon-plus"></i>
                    </div>
                </div>
                <ol class="level-2-wrapper">
                    <li>
                    <h2 class="level-2 rectangle">Director A</h2>
                    <ol class="level-3-wrapper">
                        <li>
                        <h3 class="level-3 rectangle">Manager A</h3>
                        <ol class="level-4-wrapper">
                            <li>
                            <h4 class="level-4 rectangle">Person A</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person B</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person C</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person D</h4>
                            </li>
                        </ol>
                        </li>
                        <li>
                        <h3 class="level-3 rectangle">Manager B</h3>
                        <ol class="level-4-wrapper">
                            <li>
                            <h4 class="level-4 rectangle">Person A</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person B</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person C</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person D</h4>
                            </li>
                        </ol>
                        </li>
                    </ol>
                    </li>
                    <li>
                    <h2 class="level-2 rectangle">Director B</h2>
                    <ol class="level-3-wrapper">
                        <li>
                        <h3 class="level-3 rectangle">Manager C</h3>
                        <ol class="level-4-wrapper">
                            <li>
                            <h4 class="level-4 rectangle">Person A</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person B</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person C</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person D</h4>
                            </li> 
                            <li>
                                <h4 class="level-4 rectangle">Ramin </h4>
                            </li>
                        </ol>
                        </li>
                        <li>
                        <h3 class="level-3 rectangle">Manager D</h3>
                        <ol class="level-4-wrapper">
                            <li>
                            <h4 class="level-4 rectangle">Person A</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person B</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person C</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person D</h4>
                            </li>
                        </ol>
                        </li>
                    </ol>
                    <li>
                    <h2 class="level-2 rectangle">Director C</h2>
                    <ol class="level-3-wrapper">
                        <li>
                        <h3 class="level-3 rectangle">Manager C</h3>
                        <ol class="level-4-wrapper">
                            <li>
                            <h4 class="level-4 rectangle">Person A</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person B</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person C</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person D</h4>
                            </li> 
                            <li>
                                <h4 class="level-4 rectangle">Ramin </h4>
                            </li>
                        </ol>
                        </li>
                        <li>
                        <h3 class="level-3 rectangle">Manager D</h3>
                        <ol class="level-4-wrapper">
                            <li>
                            <h4 class="level-4 rectangle">Person A</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person B</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person C</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person D</h4>
                            </li>
                        </ol>
                        </li>
                    </ol>
                    </li>

                    <li>
                    <h2 class="level-2 rectangle">Director C</h2>
                    <ol class="level-3-wrapper">
                        <li>
                        <h3 class="level-3 rectangle">Manager C</h3>
                        <ol class="level-4-wrapper">
                            <li>
                            <h4 class="level-4 rectangle">Person A</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person B</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person C</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person D</h4>
                            </li> 
                            <li>
                                <h4 class="level-4 rectangle">Ramin </h4>
                            </li>
                        </ol>
                        </li>
                        <li>
                        <h3 class="level-3 rectangle">Manager D</h3>
                        <ol class="level-4-wrapper">
                            <li>
                            <h4 class="level-4 rectangle">Person A</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person B</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person C</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person D</h4>
                            </li>
                        </ol>
                        </li>
                    </ol>
                    </li>

                    <li>
                    <h2 class="level-2 rectangle">Director C</h2>
                    <ol class="level-3-wrapper">
                        <li>
                        <h3 class="level-3 rectangle">Manager C</h3>
                        <ol class="level-4-wrapper">
                            <li>
                            <h4 class="level-4 rectangle">Person A</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person B</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person C</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person D</h4>
                            </li> 
                            <li>
                                <h4 class="level-4 rectangle">Ramin </h4>
                            </li>
                        </ol>
                        </li>
                        <li>
                        <h3 class="level-3 rectangle">Manager D</h3>
                        <ol class="level-4-wrapper">
                            <li>
                            <h4 class="level-4 rectangle">Person A</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person B</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person C</h4>
                            </li>
                            <li>
                            <h4 class="level-4 rectangle">Person D</h4>
                            </li>
                        </ol>
                        </li>
                    </ol>
                    </li>
                </ol>
                </div>

            
        </div>
    </div>
</div>
 

    @endsection