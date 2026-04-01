@extends('admin.layouts.app')

@section('title') Project @endsection

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
                            <h2 class="content-header-title float-left mb-0">KUNDEN</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('customer_product_show') }}">Dokumentation</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">{{ $customer->name }} {{ $customer->lastname }}</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
        
            </div>
            <style>
             
            

            </style>
      
            <div class="content-body">
                <div id="user-profile">
      
                    <section id="profile-info">
                        <div class="row">
                            <div class="col-lg-3 col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>KUNDEN INFO</h4>
                                        <i class="feather icon-more-horizontal cursor-pointer"></i>
                                    </div>
                                    <div class="card-body">
                                        <p>{{ $customer->name }} {{ $customer->lastname }}</p>
                                        @if($customer->firma)
                                        <div class="mt-1">
                                            <h6 class="mb-0">Firma:</h6>
                                            <p>{{ $customer->firma }}</p>
                                        </div>
                                        @endif
                                       
                                        <div class="mt-1">
                                            <h6 class="mb-0">Adress</h6>
                                            <p class="mb-0">Primary</p>
                                            <p>{{ $customer->street }} {{ $customer->postcode }}</p>
                                            <p>{{ $customer->city }}</p>
                                            @if($customer->street2)
                                            <p class="mb-0">BV abweichende Adresse  </p>
                                            <p>{{ $customer->street2 }} {{ $customer->postcode2 }}</p>
                                            <p>{{ $customer->city2 }}</p>
                                            @endif
                                        </div>
                                        <div class="mt-1">
                                            <h6 class="mb-0">Email:</h6>
                                            <p>{{ $customer->email }}</p>
                                        </div>
                                        <div class="mt-1">
                                            <h6 class="mb-0">Telefon:</h6>
                                            <p>{{ $customer->phone }}</p>
                                        </div>
                                        <div class="mt-1">
                                            <button type="button" class="btn btn-sm btn-icon btn-primary mr-25 p-25"><i class="feather icon-facebook"></i></button>
                                            <button type="button" class="btn btn-sm btn-icon btn-primary mr-25 p-25"><i class="feather icon-twitter"></i></button>
                                            <button type="button" class="btn btn-sm btn-icon btn-primary p-25"><i class="feather icon-instagram"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">PRODUKT</h4>
                                    </div>
                                    <div class="card-body suggested-block">
                                        @foreach ($product as $pro)
                                        
                                        <div class="d-flex justify-content-start align-items-center mb-1">
                                       
                                            <div class="user-page-info">
                                                <p><i class="feather icon-package "></i> {{ $pro->article_group }} ({{ $pro->product_count }})</p>
                                            </div>
                                          
                                        </div>
                                        @endforeach
                                  
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        <h4>ERSTELLER</h4>
                                    </div>
                                    <div class="card-body">
                                        @foreach ($first_contact as $responsible)

                                        <div class="twitter-feed">
                                            <div class="d-flex justify-content-start align-items-center mb-1">
                                                <div class="avatar mr-50">
                                                    <img src="{{ asset('images/employee/'.$responsible->eimage) }}" alt="{{ $responsible->ename }}" height="35" width="35">
                                                </div>
                                                <div class="user-page-info">
                                                    <p class="text-bold-600 mb-0">{{ $responsible->ename }} {{ $responsible->elastname }}</p>
                                                    <small>@ {{ substr($responsible->ename, 0, 1) }}{{ substr($responsible->elastname, 0, 1) }} </small>
                                                    <div class="badge badge-primary badge-pill badge-sm p-0">
                                                        <i class="feather icon-check font-small-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="mb-0">Position: ------</p>
                                            <small>12 Dec 2018</small>
                                        </div>
                                   
                                        @endforeach
                                        
                                </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                              
                                <div class="card">
                                    <div class="card-body">
                                        <section id="nav-justified">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="card overflow-hidden">
                                                 
                                                        <div class="card-content">
                                                            <div class="card-body">
                                                                <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" id="home-tab-justified" data-toggle="tab" href="#home-just" role="tab" aria-controls="home-just" aria-selected="false">Beratung</a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" id="profile-tab-justified" data-toggle="tab" href="#profile-just" role="tab" aria-controls="profile-just" aria-selected="false">Planung</a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link active" id="messages-tab-justified" data-toggle="tab" href="#messages-just" role="tab" aria-controls="messages-just" aria-selected="true">Kalkulation</a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" id="settings-tab-justified" data-toggle="tab" href="#settings-just" role="tab" aria-controls="settings-just" aria-selected="false">Angebot</a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" id="project-tab-justified" data-toggle="tab" href="#project_tab" role="tab" aria-controls="settings-just" aria-selected="false">Projektierung</a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" id="montage-tab-justified" data-toggle="tab" href="#montage_tab" role="tab" aria-controls="settings-just" aria-selected="false">Montage</a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" id="fertig-tab-justified" data-toggle="tab" href="#fertig_tab" role="tab" aria-controls="settings-just" aria-selected="false">Fertigstellung</a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" id="abschluss-tab-justified" data-toggle="tab" href="#abschluss_tab" role="tab" aria-controls="settings-just" aria-selected="false">Abschluss</a>
                                                                    </li>
                                                                </ul>
                        
                                                                <!-- Tab panes -->
                                                                <div class="tab-content pt-1">
                                                                    <div class="tab-pane" id="home-just" role="tabpanel" aria-labelledby="home-tab-justified">
                                                                     {{-- First Stage --}}
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
                                                                     {{-- End of first Stage --}}
                                                                    </div>
                                                                    <div class="tab-pane" id="profile-just" role="tabpanel" aria-labelledby="profile-tab-justified">
                                                                        <p>
                                                                            Noch keine Aktivität

                                                                        </p>
                                                                    </div>
                                                                    <div class="tab-pane active" id="messages-just" role="tabpanel" aria-labelledby="messages-tab-justified">
                                                                        <p>
                                                                            Noch keine Aktivität

                                                                        </p>
                                                                    </div>
                                                                    <div class="tab-pane" id="settings-just" role="tabpanel" aria-labelledby="settings-tab-justified">
                                                                        <p>
                                                                            Noch keine Aktivität

                                                                        </p>
                                                                    </div>

                                                                    <div class="tab-pane" id="project_tab" role="tabpanel" aria-labelledby="project-tab-justified">
                                                                        <p>
                                                                            Noch keine Aktivität

                                                                        </p>
                                                                    </div>

                                                                    <div class="tab-pane" id="montage_tab" role="tabpanel" aria-labelledby="montage-tab-justified">
                                                                        <p>
                                                                            Noch keine Aktivität

                                                                        </p>
                                                                    </div>

                                                                    <div class="tab-pane" id="fertig_tab" role="tabpanel" aria-labelledby="fertig-tab-justified">
                                                                        <p>
                                                                            Noch keine Aktivität

                                                                        </p>
                                                                    </div>

                                                                    <div class="tab-pane" id="abschluss_tab" role="tabpanel" aria-labelledby="project-tab-justified">
                                                                        <p>
                                                                            Noch keine Aktivität

                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>


                                        
                                    </div>
                                 </div>
                            </div>
                            <div class="col-lg-3 col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Projektfoto</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4 col-6 user-latest-img">
                                                <img src="{{ asset('images/customer/home/1.png') }}"  class="img-fluid mb-1 rounded-sm" alt="avtar img holder">
                                            </div>
                                            <div class="col-md-4 col-6 user-latest-img">
                                                <img src="{{ asset('images/customer/home/2.png') }}" class="img-fluid mb-1 rounded-sm" alt="avtar img holder">
                                            </div>
                                            <div class="col-md-4 col-6 user-latest-img">
                                                <img src="{{ asset('images/customer/home/3.png') }}"  class="img-fluid mb-1 rounded-sm" alt="avtar img holder">
                                            </div>
                                            <div class="col-md-4 col-6 user-latest-img">
                                                <img src="{{ asset('images/customer/home/4.png') }}"  class="img-fluid mb-1 rounded-sm" alt="avtar img holder">
                                            </div>
                              
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between">
                                        <h4>Projektteam</h4>
                                        <i class="feather icon-more-horizontal cursor-pointer"></i>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-start align-items-center mb-1">
                                            <div class="avatar mr-50">
                                                <img src="../../../app-assets/images/portrait/small/avatar-s-5.jpg" alt="avtar img holder" height="35" width="35">
                                            </div>
                                            <div class="user-page-info">
                                                <h6 class="mb-0">Carissa Dolle</h6>
                                                <span class="font-small-2">6 Mutual Friends</span>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-icon ml-auto"><i class="feather icon-user-plus"></i></button>
                                        </div>
                                        
                                        <div class="d-flex justify-content-start align-items-center mb-1">
                                            <div class="avatar mr-50">
                                                <img src="../../../app-assets/images/portrait/small/avatar-s-7.jpg" alt="avtar img holder" height="35" width="35">
                                            </div>
                                            <div class="user-page-info">
                                                <h6 class="mb-0">Isidra Strunk</h6>
                                                <span class="font-small-2">2 Mutual Friends</span>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-icon ml-auto"><i class="feather icon-user-plus"></i></button>
                                        </div>
                                        <div class="d-flex justify-content-start align-items-center mb-1">
                                            <div class="avatar mr-50">
                                                <img src="../../../app-assets/images/portrait/small/avatar-s-8.jpg" alt="avtar img holder" height="35" width="35">
                                            </div>
                                            <div class="user-page-info">
                                                <h6 class="mb-0">Gerald Licea</h6>
                                                <span class="font-small-2">1 Mutual Friends</span>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-icon ml-auto"><i class="feather icon-user-plus"></i></button>
                                        </div>
                          
                                      
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Aufgabenfortschritt
                                        </h4>
                                    </div>
                                    <div class="card-body">
                                        <h6>Liste der Leistungen und Aufgabenerfüllung des Projekts</h6>
                                        <div class="polls-info mt-1">
                                            <div class="d-flex justify-content-between">
                                                <div class="vs-radio-con vs-radio-primary">
                                                    <input type="radio" name="vueradio" value="false">
                                                    <span class="vs-radio">
                                                        <span class="vs-radio--border"></span>
                                                        <span class="vs-radio--circle"></span>
                                                    </span>
                                                    <span class="">RDJ</span>
                                                </div>
                                                <div class="text-right">58%</div>
                                            </div>
                                            <div class="progress progress-bar-primary my-50">
                                                <div class="progress-bar" role="progressbar" aria-valuenow="58" aria-valuemin="58" aria-valuemax="100" style="width:58%"></div>
                                            </div>
                                            <ul class="list-unstyled users-list d-flex">
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Tonia Seabold" class="avatar pull-up ml-0">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-12.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Carissa Dolle" class="avatar pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-5.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Kelle Herrick" class="avatar pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-9.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Len Bregantini" class="avatar pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-10.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="John Doe" class="avatar pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-11.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Tonia Seabold" class="avatar pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-12.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Dirk Fornili" class="avatar pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-2.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="polls-info mt-1">
                                            <div class="d-flex justify-content-between">
                                                <div class="vs-radio-con vs-radio-primary">
                                                    <input type="radio" name="vueradio" value="false">
                                                    <span class="vs-radio">
                                                        <span class="vs-radio--border"></span>
                                                        <span class="vs-radio--circle"></span>
                                                    </span>
                                                    <span class="">Chris Hemswort</span>
                                                </div>
                                                <div class="text-right">16%</div>
                                            </div>
                                            <div class="progress progress-bar-primary my-50">
                                                <div class="progress-bar" role="progressbar" aria-valuenow="16" aria-valuemin="16" aria-valuemax="100" style="width:16%"></div>
                                            </div>
                                            <ul class="list-unstyled users-list d-flex">
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Liliana Pecor" class="avatar pull-up ml-0">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-6.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Kasandra NaleVanko" class="avatar pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-1.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="polls-info mt-1">
                                            <div class="d-flex justify-content-between">
                                                <div class="vs-radio-con vs-radio-primary">
                                                    <input type="radio" name="vueradio" value="false">
                                                    <span class="vs-radio">
                                                        <span class="vs-radio--border"></span>
                                                        <span class="vs-radio--circle"></span>
                                                    </span>
                                                    <span class="">Mark Ruffalo</span>
                                                </div>
                                                <div class="text-right">8%</div>
                                            </div>
                                            <div class="progress progress-bar-primary my-50">
                                                <div class="progress-bar" role="progressbar" aria-valuenow="8" aria-valuemin="8" aria-valuemax="100" style="width:8%"></div>
                                            </div>
                                            <ul class="list-unstyled users-list d-flex">
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Lorelei Lacsamana" class="avatar pull-up ml-0">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-4.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="polls-info mt-1">
                                            <div class="d-flex justify-content-between">
                                                <div class="vs-radio-con vs-radio-primary">
                                                    <input type="radio" name="vueradio" value="false">
                                                    <span class="vs-radio">
                                                        <span class="vs-radio--border"></span>
                                                        <span class="vs-radio--circle"></span>
                                                    </span>
                                                    <span class="">Chris Evans</span>
                                                </div>
                                                <div class="text-right">16%</div>
                                            </div>
                                            <div class="progress progress-bar-primary my-50">
                                                <div class="progress-bar" role="progressbar" aria-valuenow="16" aria-valuemin="16" aria-valuemax="100" style="width:16%"></div>
                                            </div>
                                            <ul class="list-unstyled users-list d-flex">
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="JeanieBulgrin" class="avatar pull-up ml-0">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-8.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Graig Muckey" class="avatar pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-3.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-center">
                                <button type="button" class="btn btn-primary block-element mb-1">Load More</button>
                            </div>
                        </div>
                    </section>
                </div>

            </div>
        </div>
    </div>
    <!-- END: Content-->
    @endsection

    @section('script')
    <!-- BEGIN: Page JS-->
    <script src="{{ asset('app-assets/js/scripts/pages/user-profile.js') }}"></script>
    @endsection