@extends('admin.layouts.app')
@section('title') LabensLauf @endsection
@section('style')
 <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 20px;
        }
        .sidebar {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .sidebar h2 {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        .main-content {
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            margin-top: 20px;
        }
        .section-title {
            font-size: 1.25rem;
            border-bottom: 2px solid #000000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .contact-info, .education, .skills, .references {
            margin-bottom: 20px;
        }
    </style>
@endsection
@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">MITARBEITERPROFIL</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">HOME</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ url('/emp') }}">MITARBEITER</a>
                                </li>
                                <li class="breadcrumb-item active">LABENSLAUF</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="container"> 
                <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <div class="sidebar text-center">
                    <img src="{{ asset('images/employee/'.$data->image) }}" alt="Profile Picture" class="profile-img">
                    <h2>{{ $data->name }} {{$data->lastname}}</h2> 
                    <div class="contact-info">
                        <h2>Contact Me</h2>
                        <p><strong>Phone:</strong> {{ $data->phone }}</p>
                        <p><strong>Email:</strong> {{ $data->email}}</p>  
                    </div>
                    <div class="education">
                        <h2>Bildungshintergrund</h2>
                        @foreach ($qualifications as $quali)
                        <p><strong>{{$quali->degree}}({{ $quali->major }})</strong><br>{{ $quali->institution }}<br>{{ $quali->q_start_year }} - {{ $quali->q_end_year }}</p>  
                        @endforeach
                    </div>
                    <div class="skills">
                        <h2>Fähigkeiten</h2>
                        <ul class="list-unstyled">
                            @foreach ($skills as $skill)
                            <li> 
                                <span class="chip-text"> {{ $skill->article_group }}</span> 
                                @php
                                    $result = $skill->result + 70
                                @endphp
                                <div class="progress progress-bar-danger progress-md">
                                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="1" aria-valuemin="1" aria-valuemax="30" style="width:{{$result}}% ;"></div>
                                </div>
                            </li>  
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="main-content">
                    <div class="section">
                        <div class="section-title">Arbeitserfahrung bei {{$data->branch}}</div>
                        @foreach ($positions as $position)
                        <p><strong>{{$position->position}}</strong> {{ $data->contract_date }} <br><em>{{ $data->branch }}</em>
                        <br>
                        {!! $position->job_description !!}
                        </p>
                        
                        @endforeach
                      
                    </div>
                    <div class="section">
                        <div class="section-title">Sprachen</div>
                        <div class="references">
                           <ul class="list-unstyled d-flex">
                                @foreach ($languages as $language)
                                <li>
                                   <div class="chip mr-1">
                                            <div class="chip-body">
                                                <span class="chip-text"> {{ $language->language }}</span>
                                            </div>
                                        </div>
                                </li>
                                @endforeach
                            </ul>
                                    </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
            </div>
            
        </div>
    </div>
</div>
@endsection