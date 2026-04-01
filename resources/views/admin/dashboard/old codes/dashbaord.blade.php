@extends('admin.layouts.app')

@section('title') Dashbaord @endsection

@section('content')
            <style>
                    #banner {
                            background-image: url('../images/banner/x.png');
                            -webkit-background-size: cover;
                            -moz-background-size: cover;
                            -o-background-size: cover;
                            background-size: cover;
                    }
            </style>
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- Dashboard Analytics Start -->
                <section id="dashboard-analytics">
                    <div class="row">
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <div class="card bg-analytics text-white">
                                <div class="card-content" id="banner" >
                                    <div class="card-body text-center">
                                        <!-- <img src="{{ asset('app-assets/images/elements/decore-left.png')}}" class="img-left" alt="card-img-left">
                                        <img src="{{ asset('app-assets/images/elements/decore-right.png')}}" class="img-right" alt="card-img-right"> -->
                                        <div class="avatar avatar-xl bg-primary shadow mt-0">
                                            <div class="avatar-content">
                                            <span><img class="round" src="{{ asset('images/user/'.auth()->user()->image)}}" alt="avatar" height="40" width="40"></span>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <h1 class="mb-2 text-white">Hallo {{ DB::table('users')->join('employees', 'employees.id', '=', 'users.name' )->where('users.name', '=', auth()->user()->name)->select('employees.name')->pluck('name')->first() }} {{ DB::table('users')->join('employees', 'employees.id', '=', 'users.name' )->where('users.name', '=', auth()->user()->name)->select('employees.lastname')->pluck('lastname')->first() }}</h1>
                                            <p class="m-auto w-75">Your login date is : {{ \Carbon\Carbon::now() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex flex-column align-items-start pb-0">
                                    
                                    <h2 class="text-bold-700 mt-1 mb-25">{{  \Carbon\Carbon::parse(now())->isoFormat('HH:MM:SS')  }}</h2>
                                    <p class="mb-0">Current Time</p>
                                </div>
                                <div class="card-content">
                                    <div id="subscribe-gain-chart"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6 col-12">
                            <div class="card" style="background:#8fc73e">
                                <div class="card-header d-flex flex-column align-items-start pb-0">
                                    @if(auth()->user()->is_admin==1)
                                    <h2 class="text-bold-700 mt-1 mb-25">Administrator</h2>
                                    @else
                                    <h2 class="text-bold-700 mt-1 mb-25">Mitarbeiter</h2>
                                    @endif
                                    <p class="mb-0">User Authorities</p>
                                </div>
                                <div class="card-content">
                                    <div id="orders-received-chart"></div>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-2 col-md-6 col-12">
                            <div class="card" style="background:#0ca0db">
                                <div class="card-header d-flex flex-column align-items-start pb-0">
                                    @if(isset($weather['main']))
                                    <h2 class="text-bold-700 mt-1 mb-25"> 
                                    <span><img src="http://openweathermap.org/img/w/{{ $weather['weather'][0]['icon'] }}.png" alt="Weather Icon"></span>
                                        
                                        {{ $weather['main']['temp'] }} °K</h2>
                            
                                    <h6><i class="feather icon-chevron-down "></i> {{ $weather['main']['temp_min'] }} °K <i class="feather icon-chevron-up "></i> {{ $weather['main']['temp_max'] }} °K</h6>
                                    @if(isset($weather['weather'][0]))
                                    <p>{{ $weather['weather'][0]['description'] }}</p>
                                  
                                @endif

                                    @else
                                    <h2 class="text-bold-700 mt-1 mb-25">    <p>Unable to fetch weather data</p></h2>
                                    @endif
                                </div>
                                <div class="card-content">
                                    <div id="orders-received-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if(DB::table('user_rolls')
                        ->where('user_rolls.user_id', '=', auth()->user()->name)
                        ->where('user_rolls.item_id', '=', 'Employee')
                        ->where('user_rolls.is_read', '=', 'on')
                        ->first())
                    <div class="row match-height">
                        <div class="col-lg-2 col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between pb-0">
                                    <h4>Employees</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div id="product-order-chart" class="mb-3"></div>
                                        @foreach ($employees as $emp)
                                        <div class="chart-info d-flex justify-content-between mb-1">
                                            <div class="series-info d-flex align-items-center">
                                                <i class="fa fa-circle-o text-bold-700 text-primary"></i>
                                                <span class="text-bold-600 ml-50">{{ $emp->name }} {{ $emp->lastname }}</span>
                                            </div>
                                            <div class="product-result">
                                                <span>23043</span>
                                            </div>
                                        </div>  
                                        @endforeach
                                
                                     
                                     
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                   
                        @if(DB::table('user_rolls')
                        ->where('user_rolls.user_id', '=', auth()->user()->name)
                        ->where('user_rolls.item_id', '=', 'Problem')
                        ->where('user_rolls.is_read', '=', 'on')
                        ->first())
                        <div class="col-10">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="mb-0">TICKETSTATUS:
                                        <div class="badge badge-danger mr-1 mb-1">
                                            <i class="fa fa-info-circle"></i>
                                            <span>{{ DB::table('users')->join('employees', 'employees.id', '=', 'users.name' )->where('users.name', '=', auth()->user()->name)->select('employees.name')->pluck('name')->first() }} {{ DB::table('users')->join('employees', 'employees.id', '=', 'users.name' )->where('users.name', '=', auth()->user()->name)->select('employees.lastname')->pluck('lastname')->first() }}</span>
                                        </div>

                                        
                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Erster Kontakt" class="avatar pull-up">
                                                <img class="media-object rounded-circle" src="{{ asset('images/gender/male.png')}}" alt="Avatar" height="30" width="30" style="border-color: #eb5555; border-width: medium;">
                                             </li>
                                             <i class="feather icon-chevrons-right" ></i>
                                            
                                             <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Verantwortungsbewusste Menschen" class="avatar pull-up">
                                                <img class="media-object rounded-circle" src="{{ asset('images/gender/users.png')}}" alt="Avatar" height="30" width="30" style="border-color: #8fc73e; border-width: medium;">
                                             </li>
                                           
                                           
                                      
                                    </h4>
                                </div>
                                <div class="card-content">
                                    <div class="table-responsive mt-1">
                                        <table class="table table-hover-animation mb-0">
                                            <thead>
                                                <tr>
                                                    <th>TICKET</th>
                                                    <th>DATUM</th>
                                                    <th>VERANTWORTLICHE</th>
                                                    <th>KUNDEN</th>
                                                    <th>PRODUKT</th>
                                                    <th>PROBLEMS</th>
                                                    <th>STATUS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($problems as $pro)
                                                <tr>
                                                <td><a href="{{ url('problem_view?search='.$pro->ticket_no) }}">{{ $pro->ticket_no }}</a></td>
                                                    <td>{{ $pro->date }}</td>
                                                   
                                                    <td class="p-1">
                                                        <ul class="list-unstyled users-list m-0  d-flex align-items-center">
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="{{ $pro->fname }} {{ $pro->flastname }}" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$pro->fimage)}}" alt="Avatar" height="30" width="30" style="border-color: #eb5555; border-width: medium;">
                                                            </li>
                                                            <i class="feather icon-chevrons-right" ></i>

                                                            @foreach ($responsible as $res)
                                                               @if($res->problem_id==$pro->id)
                                                                    <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="{{ $res->rname }} {{ $res->rlastname }}" class="avatar pull-up">
                                                                        <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$res->rimage)}}" alt="Avatar" height="30" width="30" style="border-color: #8fc73e; border-width: medium;">
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                    <td>{{ $pro->cname }} {{ $pro->clastname }}</td>
                                                    <td>
                                                        @foreach ($products as $product)
                                                        @if($product->id==$pro->product_id)
                                                        <div class="badge badge-primary mr-1 mb-1">
                                                            <i class="fa fa-product-hunt"></i>
                                                            <span>{{ $product->product }}</span>
                                                        </div>
                                                        @endif
                                                        @endforeach
                                                        
                                                    </td>
                                                    
                                                    <td>
                                                        @foreach ($errors as $er)
                                                        @if($er->problem_id==$pro->id)
                                                        <div class="badge badge-danger mr-1 mb-1">
                                                            <i class="fa fa-info-circle"></i>
                                                            <span>{{ $er->problem_types }}</span>
                                                        </div>
                                                        @endif
                                                        @endforeach
                                                      
                                                    </td>
                                                    @if($pro->status=='offen')
                                                    <td>
                                                        <div class="badge badge-danger mr-1 mb-1">
                                                            <i class="fa fa-info-circle"></i>
                                                            <span>OFFEN</span>
                                                        </div>
                                                    </td>
                                                   
                                                    @elseif($pro->status=='in Klärung')
                                                    <td>
                                                        <div class="badge badge-warning mr-1 mb-1">
                                                            <i class="fa fa-refresh"></i>
                                                            <span>IN KLÄRUNG</span>
                                                        </div>
                                                    </td>
                                                   
                                                    @elseif($pro->status=='beendet')
                                                    <td>
                                                        <div class="badge badge-success mr-1 mb-1">
                                                            <i class="fa fa-check"></i>
                                                            <span>BEENDET</span>
                                                        </div>
                                                    </td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        {{-- Invoice Notification Section --}}
                        @if(DB::table('user_rolls')
                        ->where('user_rolls.user_id', '=', auth()->user()->name)
                        ->where('user_rolls.item_id', '=', 'Problem')
                        ->where('user_rolls.is_read', '=', 'on')
                        ->first())
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="mb-0">RECHNUNGSSTATUS
                                        <div class="badge badge-danger mr-1 mb-1">
                                            <i class="fa fa-info-circle"></i>
                                            <span>{{ DB::table('users')->join('employees', 'employees.id', '=', 'users.name' )->where('users.name', '=', auth()->user()->name)->select('employees.name')->pluck('name')->first() }} {{ DB::table('users')->join('employees', 'employees.id', '=', 'users.name' )->where('users.name', '=', auth()->user()->name)->select('employees.lastname')->pluck('lastname')->first() }}</span>
                                        </div>
                                        
                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Person, die den Artikel gekauft hat" class="avatar pull-up">
                                                <img class="media-object rounded-circle" src="{{ asset('images/gender/male.png')}}" alt="Avatar" height="30" width="30" style="border-color: #eb5555; border-width: medium;">
                                             </li>
                                             <i class="feather icon-chevrons-right" ></i>
                                            
                                             <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Die Person, die die Rechnung bearbeitet" class="avatar pull-up">
                                                <img class="media-object rounded-circle" src="{{ asset('images/gender/users.png')}}" alt="Avatar" height="30" width="30" style="border-color: #8fc73e; border-width: medium;">
                                             </li>
                                           
                                           
                                      
                                    </h4>
                                </div>
                                <div class="card-content">
                                    <div class="table-responsive mt-1">
                                        <table class="table table-hover-animation mb-0">
                                            <thead>
                                                <tr>
                                                    <th>RECHNUNG_NO</th>
                                                    <th>DATUM</th>
                                                    <th>VERANTWORTLICHE</th>
                                                    <th>GEKAUFT FÜR</th>
                                                    <th>STATUS</th>
                                                    <th>OPERATION</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($invoices as $inv)
                                                <tr>
                                                <td><a href="{{ url('invoice?search='.$inv->invoice_no) }}">{{ $inv->invoice_no }}</a></td>
                                                    <td>{{ $inv->invoice_date }}</td>
                                                   
                                                    <td class="p-1">
                                                        <ul class="list-unstyled users-list m-0  d-flex align-items-center">
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="{{ $inv->pname }} {{ $inv->plastname }}" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$inv->pimage)}}" alt="Avatar" height="30" width="30" style="border-color: #eb5555; border-width: medium;">
                                                            </li>
                                                            <i class="feather icon-chevrons-right" ></i>

                                                         
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="{{ $inv->ename }} {{ $inv->elastname }}" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$inv->eimage)}}" alt="Avatar" height="30" width="30" style="border-color: #8fc73e; border-width: medium;">
                                                            </li>
                                                        </ul>
                                                    </td>
                                                    <td>
                                                        Gekauft für:{{ $inv->purchase_for}} </br>
                                                        @if($inv->purchase_for == "Kunden")
                                                            @foreach ($customer as $cus)
                                                                @if($cus->cid == $inv->customer_id && $cus->invoice_id == $inv->invoice_no)
                                                                <div class="badge badge-pill badge-glow badge-warning mr-1 mb-1"> <a href="{{ url('/customer_show/'.$cus->cid)}}">{{ $cus->customer_name }} {{ $cus->customer_lastname }}</a></div>
                                                                @endif
                                                            @endforeach
                                                        @elseif( $inv->purchase_for = "Personal")
                                                         @foreach ($employee as $emp)
                                                            @if($emp->emp_id == $inv->employee_id && $emp->invoice_id == $inv->invoice_no)
                                                                <div class="badge badge-pill badge-glow badge-warning mr-1 mb-1"> <a href="">{{ $emp->employee_name }} {{ $emp->employee_lastname }}</a></div>
                                                            @endif
                                                            @endforeach
                                                    @endif
                                                    </td>
                                                    
                                                    @if($inv->status=='approved')
                                                    <td>
                                                        <div class="badge badge-success mr-1 mb-1">
                                                            <i class="fa fa-info-circle"></i>
                                                            <span>Genehmigt</span>
                                                        </div>
                                                    </td>
                                                   
                                                    @elseif($inv->status=='notApproved')
                                                    <td>
                                                        <div class="badge badge-warning mr-1 mb-1">
                                                            <i class="fa fa-refresh"></i>
                                                            <span>IN KLÄRUNG</span>
                                                        </div>
                                                    </td>
                                                   
                                                    @elseif($inv->status=='draft')
                                                    <td>

                                                        <div class="badge badge-danger mr-1 mb-1">
                                                            <i class="fa fa-refresh"></i>
                                                            <a href="{{ url('/draft_view/'.$inv->id) }}">Die Rechnung wird abgelehnt</a>
                                                        </div>
                                                                                                   
                                                    </td>
                                                    @endif

                                                    <td>
                                                        @if($inv->status=="notApproved")
                                                        <a type="button" href="{{ url('invoice_apr/'.$inv->id) }}" class="btn btn-icon btn-icon rounded-circle btn-success mr-1 mb-1" >
                                                        <i class="feather icon-check"></i>
                                                        </a>
                                                       
                                                        @endif
                                                        
                                                    </td>
                                                </tr>
                                                @endforeach
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        {{-- Invoice Notification Section: End --}}

                         {{-- all Problems Notification Section --}}
                        @if(DB::table('user_rolls')
                        ->where('user_rolls.user_id', '=', auth()->user()->id)
                        ->where('user_rolls.item_id', '=', 'Problems')
                        ->where('user_rolls.is_read', '=', 'on')
                        ->first())
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="mb-0">TICKETSTATUS:
                                        <div class="badge badge-danger mr-1 mb-1">
                                            <i class="fa fa-info-circle"></i>
                                            <span>{{ DB::table('users')->join('employees', 'employees.id', '=', 'users.name' )->where('users.name', '=', auth()->user()->name)->select('employees.name')->pluck('name')->first() }} {{ DB::table('users')->join('employees', 'employees.id', '=', 'users.name' )->where('users.name', '=', auth()->user()->name)->select('employees.lastname')->pluck('lastname')->first() }}</span>
                                        </div>

                                        
                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Erster Kontakt" class="avatar pull-up">
                                                <img class="media-object rounded-circle" src="{{ asset('images/gender/male.png')}}" alt="Avatar" height="30" width="30" style="border-color: #eb5555; border-width: medium;">
                                             </li>
                                             <i class="feather icon-chevrons-right" ></i>
                                            
                                             <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Verantwortungsbewusste Menschen" class="avatar pull-up">
                                                <img class="media-object rounded-circle" src="{{ asset('images/gender/users.png')}}" alt="Avatar" height="30" width="30" style="border-color: #8fc73e; border-width: medium;">
                                             </li>
                                           
                                           
                                      
                                    </h4>
                                </div>
                                <div class="card-content">
                                    <div class="table-responsive mt-1">
                                        <table class="table table-hover-animation mb-0">
                                            <thead>
                                                <tr>
                                                    <th>TICKET</th>
                                                    <th>DATUM</th>
                                                    <th>VERANTWORTLICHE</th>
                                                    <th>KUNDEN</th>
                                                    <th>PRODUKT</th>
                                                    <th>PROBLEMS</th>
                                                    <th>STATUS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($all_problems as $pro)
                                                <tr>
                                                <td><a href="{{ url('problem_view?search='.$pro->ticket_no) }}">{{ $pro->ticket_no }}</a></td>
                                                    <td>{{ $pro->date }}</td>
                                                   
                                                    <td class="p-1">
                                                        <ul class="list-unstyled users-list m-0  d-flex align-items-center">
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="{{ $pro->fname }} {{ $pro->flastname }}" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$pro->fimage)}}" alt="Avatar" height="30" width="30" style="border-color: #eb5555; border-width: medium;">
                                                            </li>
                                                            <i class="feather icon-chevrons-right" ></i>

                                                            @foreach ($responsible as $res)
                                                               @if($res->problem_id==$pro->id)
                                                                    <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="{{ $res->rname }} {{ $res->rlastname }}" class="avatar pull-up">
                                                                        <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$res->rimage)}}" alt="Avatar" height="30" width="30" style="border-color: #8fc73e; border-width: medium;">
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                    <td>{{ $pro->cname }} {{ $pro->clastname }}</td>
                                                    <td>
                                                        @foreach ($products as $product)
                                                        @if($product->id==$pro->product_id)
                                                        <div class="badge badge-primary mr-1 mb-1">
                                                            <i class="fa fa-product-hunt"></i>
                                                            <span>{{ $product->product }}</span>
                                                        </div>
                                                        @endif
                                                        @endforeach
                                                        
                                                    </td>
                                                    
                                                    <td>
                                                        @foreach ($errors as $er)
                                                        @if($er->problem_id==$pro->id)
                                                        <div class="badge badge-danger mr-1 mb-1">
                                                            <i class="fa fa-info-circle"></i>
                                                            <span>{{ $er->problem_types }}</span>
                                                        </div>
                                                        @endif
                                                        @endforeach
                                                      
                                                    </td>
                                                    @if($pro->status=='offen')
                                                    <td>
                                                        <div class="badge badge-danger mr-1 mb-1">
                                                            <i class="fa fa-info-circle"></i>
                                                            <span>OFFEN</span>
                                                        </div>
                                                    </td>
                                                   
                                                    @elseif($pro->status=='in Klärung')
                                                    <td>
                                                        <div class="badge badge-warning mr-1 mb-1">
                                                            <i class="fa fa-refresh"></i>
                                                            <span>IN KLÄRUNG</span>
                                                        </div>
                                                    </td>
                                                   
                                                    @elseif($pro->status=='beendet')
                                                    <td>
                                                        <div class="badge badge-success mr-1 mb-1">
                                                            <i class="fa fa-check"></i>
                                                            <span>BEENDET</span>
                                                        </div>
                                                    </td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="row">
                        <!-- permissions start -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header border-bottom mx-2 px-0">
                                    <h6 class="border-bottom py-1 mb-0 font-medium-2"><i class="feather icon-lock mr-50 "></i>Permission
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
                                                        <div class="custom-control custom-checkbox ml-50"><input type="checkbox" id="users-checkbox1" class="custom-control-input" disabled checked>
                                                            <label class="custom-control-label" for="users-checkbox1"></label>
                                                        </div>
                                                        @else
                                                        <div class="custom-control custom-checkbox ml-50"><input type="checkbox" id="users-checkbox1" class="custom-control-input" disabled unchecked>
                                                            <label class="custom-control-label" for="users-checkbox1"></label>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($pre->is_update=='on')
                                                        <div class="custom-control custom-checkbox ml-50"><input type="checkbox" id="users-checkbox1" class="custom-control-input" disabled checked>
                                                            <label class="custom-control-label" for="users-checkbox1"></label>
                                                        </div>
                                                        @else
                                                        <div class="custom-control custom-checkbox ml-50"><input type="checkbox" id="users-checkbox1" class="custom-control-input" disabled unchecked>
                                                            <label class="custom-control-label" for="users-checkbox1"></label>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($pre->is_add=='on')
                                                        <div class="custom-control custom-checkbox ml-50"><input type="checkbox" id="users-checkbox1" class="custom-control-input" disabled checked>
                                                            <label class="custom-control-label" for="users-checkbox1"></label>
                                                        </div>
                                                        @else
                                                        <div class="custom-control custom-checkbox ml-50"><input type="checkbox" id="users-checkbox1" class="custom-control-input" disabled unchecked>
                                                            <label class="custom-control-label" for="users-checkbox1"></label>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($pre->is_delete=='on')
                                                        <div class="custom-control custom-checkbox ml-50"><input type="checkbox" id="users-checkbox1" class="custom-control-input" disabled checked>
                                                            <label class="custom-control-label" for="users-checkbox1"></label>
                                                        </div>
                                                        @else
                                                        <div class="custom-control custom-checkbox ml-50"><input type="checkbox" id="users-checkbox1" class="custom-control-input" disabled unchecked>
                                                            <label class="custom-control-label" for="users-checkbox1"></label>
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
                <!-- Dashboard Analytics end -->

            </div>
        </div>
    </div>
    <!-- END: Content-->

@endsection