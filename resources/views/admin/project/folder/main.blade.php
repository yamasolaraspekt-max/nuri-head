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
                            <h2 class="content-header-title float-left mb-0">Profile</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Pages</a>
                                    </li>
                                    <li class="breadcrumb-item active">Profile
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
        
            </div>
            <style>
                #beratung {
                    width: 100px;
                }
                #planung {
                    width: 100px;
                }
                #kalkulation {
                    width: 100px;
                }
                #montage {
                    width: 100px;
                }
                #projektierung {
                    width: 100px;
                }
                #bauleitung {
                    width: 100px;
                }

            </style>
      
            <div class="content-body">
                <div id="user-profile">
                    <div class="row">
                 

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>
                                        <img src="{{ asset('logo/shape.svg') }}" id="montage">
                                        Beratung</th>
                                        <th>
                                        <img src="{{ asset('logo/shape.svg') }}" id="montage">
                                        Planung</th>
                                        <th>
                                        <img src="{{ asset('logo/shape.svg') }}" id="montage">
                                        Kalkulation</th>
                                        <th>
                                        <img src="{{ asset('logo/shape.svg') }}" id="montage">
                                        Montage</th>
                                        <th>
                                        <img src="{{ asset('logo/shape.svg') }}" id="montage">
                                        Projektierung</th>
                                        <th>
                                        <img src="{{ asset('logo/shape.svg') }}" id="montage">
                                        Bauleitung</th>
                                    </tr>
                                </thead>
                               
                            </table>
                        </div>
               
                        
                    </div>
                    <section id="profile-info">
                        <div class="row">
                            <div class="col-lg-3 col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>About</h4>
                                        <i class="feather icon-more-horizontal cursor-pointer"></i>
                                    </div>
                                    <div class="card-body">
                                        <p>Tart I love sugar plum I love oat cake. Sweet roll caramels I love jujubes. Topping cake wafer.</p>
                                        <div class="mt-1">
                                            <h6 class="mb-0">Joined:</h6>
                                            <p>November 15, 2015</p>
                                        </div>
                                        <div class="mt-1">
                                            <h6 class="mb-0">Lives:</h6>
                                            <p>New York, USA</p>
                                        </div>
                                        <div class="mt-1">
                                            <h6 class="mb-0">Email:</h6>
                                            <p>bucketful@fiendhead.org</p>
                                        </div>
                                        <div class="mt-1">
                                            <h6 class="mb-0">Website:</h6>
                                            <p>www.pixinvent.com</p>
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
                                        <h4 class="card-title">Products</h4>
                                    </div>
                                    <div class="card-body suggested-block">
                                        <div class="d-flex justify-content-start align-items-center mb-1">
                                            <div class="avatar mr-50">
                                                <img src="../../../app-assets/images/profile/pages/page-09.jpg" alt="avtar img holder" height="35" width="35">
                                            </div>
                                            <div class="user-page-info">
                                                <p>Rockose</p>
                                                <span class="font-small-2">Company</span>
                                            </div>
                                            <div class="ml-auto"><i class="feather icon-star"></i></div>
                                        </div>
                          
                                  
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Responsible Employees</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="twitter-feed">
                                            <div class="d-flex justify-content-start align-items-center mb-1">
                                                <div class="avatar mr-50">
                                                    <img src="../../../app-assets/images/portrait/small/avatar-s-12.jpg" alt="avtar img holder" height="35" width="35">
                                                </div>
                                                <div class="user-page-info">
                                                    <p class="text-bold-600 mb-0">Kathrin Nuri</p>
                                                    <small>@tiana59</small>
                                                    <div class="badge badge-primary badge-pill badge-sm p-0">
                                                        <i class="feather icon-check font-small-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="mb-0">Position: ------</p>
                                            <small>12 Dec 2018</small>
                                        </div>
                                       
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                              
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-start align-items-center mb-1">
                                            <div class="user-page-info">
                                                <h6 class="mb-0">Project Name</h6>
                                                <span class="font-small-2">10 Dec 2018 at 5:35 AM</span>
                                            </div>
                                            <div class="ml-auto user-like"><i class="feather icon-heart"></i></div>
                                        </div>
                                        <p>Description of Project </p>
                                        <iframe width="1280" height="720" src="https://www.youtube.com/embed/_BXf_h8tEes" title="Introducing Project Sunroof" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                        <div class="d-flex justify-content-start align-items-center mb-1">
                                            <div class="d-flex  cursor-pointeralign-items-center">
                                                <i class="feather icon-heart font-medium-2 mr-50"></i>
                                                <span>269</span>
                                            </div>
                                            <div class="ml-2">
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
                                                    <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Elicia Rieske" class="avatar pull-up">
                                                        <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-7.jpg" alt="Avatar" height="30" width="30">
                                                    </li>
                                                    <li class="d-inline-block pl-50">
                                                        <span>+264 more</span>
                                                    </li>
                                                </ul>
                                            </div>
                                            <p class="ml-auto d-flex align-items-center">
                                                <i class="feather icon-message-square font-medium-2 mr-50"></i>98
                                            </p>
                                        </div>
                                        <div class="d-flex justify-content-start align-items-center mb-1">
                                            <div class="avatar mr-50">
                                                <img src="../../../app-assets/images/portrait/small/avatar-s-8.jpg" alt="Avatar" height="30" width="30">
                                            </div>
                                            <div class="user-page-info">
                                                <h6 class="mb-0">Darcey Nooner</h6>
                                                <span class="font-small-2">I love cupcake danish jujubes sweet.</span>
                                            </div>
                                            <div class="ml-auto cursor-pointer">
                                                <i class="feather icon-heart mr-50"></i>
                                                <i class="feather icon-message-square"></i>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-start align-items-center mb-2">
                                            <div class="avatar mr-50">
                                                <img src="../../../app-assets/images/portrait/small/avatar-s-6.jpg" alt="Avatar" height="30" width="30">
                                            </div>
                                            <div class="user-page-info">
                                                <h6 class="mb-0">Lai Lewandowski</h6>
                                                <span class="font-small-2">Wafer I love brownie jelly bonbon tart apple pie</span>
                                            </div>
                                            <div class="ml-auto cursor-pointer">
                                                <i class="feather icon-heart mr-50"></i>
                                                <i class="feather icon-message-square"></i>
                                            </div>
                                        </div>
                                        <fieldset class="form-label-group mb-50">
                                            <textarea class="form-control" id="label-textarea3" rows="3" placeholder="Add Comment"></textarea>
                                            <label for="label-textarea3">Add Comment</label>
                                        </fieldset>
                                        <button type="button" class="btn btn-sm btn-primary">Post Comment</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Project Photos</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4 col-6 user-latest-img">
                                                <img src="../../../app-assets/images/profile/user-uploads/user-01.jpg" class="img-fluid mb-1 rounded-sm" alt="avtar img holder">
                                            </div>
                                            <div class="col-md-4 col-6 user-latest-img">
                                                <img src="../../../app-assets/images/profile/user-uploads/user-02.jpg" class="img-fluid mb-1 rounded-sm" alt="avtar img holder">
                                            </div>
                                            <div class="col-md-4 col-6 user-latest-img">
                                                <img src="../../../app-assets/images/profile/user-uploads/user-03.jpg" class="img-fluid mb-1 rounded-sm" alt="avtar img holder">
                                            </div>
                                            <div class="col-md-4 col-6 user-latest-img">
                                                <img src="../../../app-assets/images/profile/user-uploads/user-04.jpg" class="img-fluid mb-1 rounded-sm" alt="avtar img holder">
                                            </div>
                                            <div class="col-md-4 col-6 user-latest-img">
                                                <img src="../../../app-assets/images/profile/user-uploads/user-05.jpg" class="img-fluid mb-1 rounded-sm" alt="avtar img holder">
                                            </div>
                                            <div class="col-md-4 col-6 user-latest-img">
                                                <img src="../../../app-assets/images/profile/user-uploads/user-06.jpg" class="img-fluid mb-1 rounded-sm" alt="avtar img holder">
                                            </div>
                                            <div class="col-md-4 col-6 user-latest-img">
                                                <img src="../../../app-assets/images/profile/user-uploads/user-07.jpg" class="img-fluid mb-1 rounded-sm" alt="avtar img holder">
                                            </div>
                                            <div class="col-md-4 col-6 user-latest-img">
                                                <img src="../../../app-assets/images/profile/user-uploads/user-08.jpg" class="img-fluid mb-1 rounded-sm" alt="avtar img holder">
                                            </div>
                                            <div class="col-md-4 col-6 user-latest-img">
                                                <img src="../../../app-assets/images/profile/user-uploads/user-09.jpg" class="img-fluid mb-1 rounded-sm" alt="avtar img holder">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between">
                                        <h4>Suggestions</h4>
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
                                                <img src="../../../app-assets/images/portrait/small/avatar-s-6.jpg" alt="avtar img holder" height="35" width="35">
                                            </div>
                                            <div class="user-page-info">
                                                <h6 class="mb-0">Liliana Pecor</h6>
                                                <span class="font-small-2">3 Mutual Friends</span>
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
                                        <div class="d-flex justify-content-start align-items-center mb-1">
                                            <div class="avatar mr-50">
                                                <img src="../../../app-assets/images/portrait/small/avatar-s-9.jpg" alt="avtar img holder" height="35" width="35">
                                            </div>
                                            <div class="user-page-info">
                                                <h6 class="mb-0">Kelle Herrick</h6>
                                                <span class="font-small-2">1 Mutual Friends</span>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-icon ml-auto"><i class="feather icon-user-plus"></i></button>
                                        </div>
                                        <div class="d-flex justify-content-start align-items-center mb-1">
                                            <div class="avatar mr-50">
                                                <img src="../../../app-assets/images/portrait/small/avatar-s-10.jpg" alt="avtar img holder" height="35" width="35">
                                            </div>
                                            <div class="user-page-info">
                                                <h6 class="mb-0">Cesar Lee</h6>
                                                <span class="font-small-2">1 Mutual Friends</span>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-icon ml-auto"><i class="feather icon-user-plus"></i></button>
                                        </div>
                                        <div class="d-flex justify-content-start align-items-center mb-1">
                                            <div class="avatar mr-50">
                                                <img src="../../../app-assets/images/portrait/small/avatar-s-11.jpg" alt="avtar img holder" height="35" width="35">
                                            </div>
                                            <div class="user-page-info">
                                                <h6 class="mb-0">John Doe</h6>
                                                <span class="font-small-2">1 Mutual Friends</span>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-icon ml-auto"><i class="feather icon-user-plus"></i></button>
                                        </div>
                                        <div class="d-flex justify-content-start align-items-center mb-2">
                                            <div class="avatar mr-50">
                                                <img src="../../../app-assets/images/portrait/small/avatar-s-12.jpg" alt="avtar img holder" height="35" width="35">
                                            </div>
                                            <div class="user-page-info">
                                                <h6 class="mb-0">Tonia Seabold</h6>
                                                <span class="font-small-2">1 Mutual Friends</span>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-icon ml-auto"><i class="feather icon-user-plus"></i></button>
                                        </div>
                                        <button type="button" class="btn btn-primary w-100 mt-1"><i class="feather icon-plus mr-25"></i>Load More</button>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Polls</h4>
                                    </div>
                                    <div class="card-body">
                                        <h6>Who is the best actor in Marvel Cinematic Universe?</h6>
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