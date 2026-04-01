@extends('admin.layouts.app')
@section('title') Foto @endsection
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
                            <h2 class="content-header-title float-left mb-0">Problem Foto</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">Basic Card
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="content-body">
                <div class="alert alert-primary">
                    <i class="feather icon-info mr-1"></i> <a   type="button" href="{{ url('/problem_view')}}" >Zurück</a>
                </div>

                @foreach ($data as $item)
                    
                
                        <div class="col-xl-12 col-md-6 col-sm-12">
                            <div class="card collapse-icon accordion-icon-rotate">
                                <div class="card-body">
                                    <div class="accordion" id="accordionExample" data-toggle-hover="true">
                                        <div class="collapse-margin">
                                            <div class="card-header collapsed" id="headingOne" data-toggle="collapse" role="button" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                                <span class="lead collapse-title collapsed">
                                                    KUNDEN INFORMATION
                                                </span>
                                            </div>

                                            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample" style="">
                                                <div class="card-body">
                                                    <h2>{{$item->name}} {{$item->lastname}}</h2>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="collapse-margin">
                                            <div class="card-header" id="headingTwo" data-toggle="collapse" role="button" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                <span class="lead collapse-title collapsed">
                                                    PROBLEM
                                                </span>
                                            </div>
                                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                                                <div class="card-body">
                                                    {!! $item->problem !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="collapse-margin">
                                            <div class="card-header" id="headingThree" data-toggle="collapse" role="button" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                <span class="lead collapse-title">
                                                PRODUKT & FEHLER
                                                </span>
                                            </div>
                                            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                                                <div class="card-body">
                                                    <h2>{{ $item->product}}</h2>
                                                    @foreach ($error as $proE)
                                                        @if($proE->problem_id==$item->id)
                                                        <div class="badge badge-pill badge-glow badge-warning mr-1 mb-1">{{ $proE->problem_types }}</div>
                                                        @endif
                                                    @endforeach
                                                    </div>
                                            </div>
                                        </div>
                                        <div class="collapse-margin">
                                            <div class="card-header collapsed" id="headingFour" data-toggle="collapse" role="button" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                                <span class="lead collapse-title">
                                                LÖSUNG
                                                </span>
                                            </div>
                                            <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordionExample" style="">
                                                <div class="card-body">
                                                {!! $item->solution !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="collapse-margin">
                                            <div class="card-header collapsed" id="headingFour" data-toggle="collapse" role="button" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                                <span class="lead collapse-title">
                                                VERANTWORTLICH
                                                </span>
                                            </div>
                                            <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordionExample" style="">
                                                <div class="card-body">
                                               
                                                    <ul class="list-unstyled users-list m-0  d-flex align-items-center">
                                                    @foreach ($responsible as $resp)
                                                        @if($resp->problem_id==$item->id)
                                                        <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="{{ $resp->rname }} {{ $resp->rlastname }}" class="avatar pull-up">
                                                            <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$resp->rimage)}}" alt="Avatar" height="100px" width="100px">
                                                        </li>
                                                    @endif
                                                    @endforeach
                                                        
                                                        </ul>
                                                 </div>
                                            </div>
                                        </div>

                                        <div class="collapse-margin">
                                            <div class="card-header collapsed" id="headingFour" data-toggle="collapse" role="button" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                                <span class="lead collapse-title">
                                                STATUS
                                                </span>
                                            </div>
                                            <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordionExample" style="">
                                                <div class="card-body">
                                               
                                                @if($item->status=="offen")
                                                        <div class="badge badge-pill badge-glow badge-danger mr-1 mb-1">Offen</div>
                                                        @elseif($item->status=="in Klärung")
                                                        <div class="badge badge-pill badge-glow badge-warning mr-1 mb-1">in Klärung</div>
                                                        <div class="badge badge-pill badge-glow badge-warning mr-1 mb-1">{{ $item->progress_user }}</div>
                                                        @elseif($item->status=="beendet")
                                                        <div class="badge badge-pill badge-glow badge-success mr-1 mb-1">beendet</div>
                                                        @endif
                                                 </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                <!-- Content types section start -->
                <section id="content-types">
                    <div class="row match-height">

                  
                    @foreach (explode('|',$images->image_id ) as $imagep)
                    <div class="col-6 ">
                            <div class="card">
                                <div class="card-header mb-1">
                                    <h4 class="card-title"><strong></strong></h4>
                                </div>
                                <div class="card-content">
                                    <img class="img-fluid" src="{{ URL::to($imagep) }}" alt="" width="100%">
                                    <div class="card-body">
                                        <p class="card-text"></p>
                                    </div>
                                </div>
                             
                            </div>
                        </div>
                        @endforeach
                    </div>
                  
                   
                </section>
                <!-- Content types section end -->

                

            </div>
        </div>
    </div>
    <!-- END: Content-->
@endsection
