@extends('admin.layouts.app')

@section('title') KUNDEN @stop

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

 <style>
    .opens {
        border-color: #e53060;
        background: white;
        padding: 6px;
        border-style: solid;
        height: 110px !important;
        width: 110px !important;
        margin-right: 11px;
    }

    .actives {
        border-color: #92b532;
        background: white;
        padding: 6px;
        border-style: solid;
        height: 110px !important;
        width: 110px !important;
        margin-right: 11px;
    }

    .inactives {
        border-color: #78a7cc;
        background: white;
        padding: 6px;
        border-style: solid;
        height: 110px !important;
        width: 110px !important;
        margin-right: 11px;
    }

    .project_ends {
        border-color: #213985;
        background: white;
        padding: 6px;
        border-style: solid;
        height: 110px !important;
        width: 110px !important;
        margin-right: 11px;
    }
    .project_cancel {
        background: white;
        padding: 6px;
         border-style: solid;
         border-color:#b1aaaa;
         height: 110px !important;
         width: 110px !important;
         margin-right: 11px;
    }
    .inner_size {
        height: 90px !important;
    }
   .articles {
    background: #b1aaaa;
    border-radius: 50%;
    height: 50px !important;
    width: 50px !important;
    margin-right: 11px;
    display: grid;
    align-items: center;
    text-align: center;
    cursor: pointer;
}
.articles input[type="radio"] {
    display: none;
}
.articles label {
    font-size: 20px !important;
    cursor: pointer;
    display: grid;
    align-items: center;
    height: 50px;
    width: 50px;
    margin: 0;
    padding: 0;
    border-radius: 50%; /* Ensure label maintains border-radius */
}
.articles input[type="radio"]:checked + label {
    background: #92b532;
    color: white;
    border-radius: 50%; /* Maintain border-radius when selected */
}
.article_text {
    color: #b1aaaa;
}
.article_text p {
    font-size: 15px !important;
}
 
    .scrollable-container {
        display: flex;
        flex-wrap: nowrap;
        justify-content: space-evenly;
        overflow-x: auto;
        width: 100%;
        padding: 10px 0;
    }

    .scrollable-container::-webkit-scrollbar {
        height: 8px;
    }

    .scrollable-container::-webkit-scrollbar-thumb {
        background-color: #888;
        border-radius: 10px;
    }

    .scrollable-container::-webkit-scrollbar-thumb:hover {
        background-color: #555;
    }

    .products {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        margin: 0 2px !important;
        flex-direction: column;
    }

    .card {
        min-width: 150px;
        margin: 0 10px;
    }

    .inner_size {
        padding: 20px;
    }
    .modal-backdrop {
    z-index: 1040 !important;
    }
    .modal {
        z-index: 1050 !important;
    }
    .modal-backdrop {
    position: absolute;
    }
    
    .openli {
        color: white;background: #e53060;display: flex;padding: 6px 6px 6px 6px;
    }
     .activeli {
        color: white;background:#92b532;display: flex;padding: 6px 6px 6px 6px;
    }
     .inactiveli {
        color: white;background: #78a7cc;display: flex;padding: 6px 6px 6px 6px;
    }
     .endedli {
        color: white;background: #213985;display: flex;padding: 6px 6px 6px 6px;
    }
     .cancelli {
        color: white;background: #7e7d7d;display: flex;padding: 6px 6px 6px 6px;
    }
    .sumli {
          color: white;background: #782567;display: flex;padding: 6px 6px 6px 6px;
    }
    .openli1 {
            display: flex;
    align-content: center;
    border: 1px #e53060;
    border-style: solid;
    }
     .activeli1 {
            display: flex;
    align-content: center;
    border: 1px #92b532;
    border-style: solid;
    }
     .inactiveli1 {
            display: flex;
    align-content: center;
    border: 1px #78a7cc;
    border-style: solid;
    }
     .endedli1 {
            display: flex;
    align-content: center;
    border: 1px #213985;
    border-style: solid;
    }
     .cancelli1 {
            display: flex;
    align-content: center;
    border: 1px #7e7d7d;
    border-style: solid;
    }

    .sumli1 {
            display: flex;
    align-content: center;
    border: 1px #782567;
    border-style: solid;
    }
    .simpleli {
        display: flex;padding: 6px 6px 6px 6px;
    }

    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0; }
        100% { opacity: 1; }
    }

    .blink {
        animation: blink 1s infinite;
    }
    .bolders {
            font-size: 15px;
            font-weight: bolder;
            width: 167px;
        }

    .status-b {
            background: #91b531;
        padding: 4px;
        border-radius: 50%;
        position: absolute;
        border: 2px solid white;
          left: 24px;
        top: 19px;
    }

     .status-a {
            background: #f8ac01;
        padding: 4px;
        border-radius: 50%;
        position: absolute;
        border: 2px solid white;
        left: 24px;
        top: 19px;
    }
     .status-c{
            background: #ea5555;
        padding: 4px;
        border-radius: 50%;
        position: absolute;
        border: 2px solid white;
         left: 24px;
        top: 19px;
    }
    
</style>
 
 <style>
    .circle {
      width: 35px;
      height: 35px;
      background-color: #7DC242;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
      font-size: 1.2rem;
    }
    .line {
         width: 9px;
            height: 4px;
            background-color: #7DC242;
            margin-left: -3px;
            margin-right: -2px;
            position: relative;
            top: 2px;
    }
    .profile {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #7DC242;
    }

    .profile-s {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #f4a459;
    }
    .profile-r {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #ea5455;
    }
    .text {
      font-size: 10px;
      font-weight: 500;
      color: #555;
      text-align: center;
      margin-top: 10px;
    }
  </style>

  <style>
        .accordion-row {
            cursor: pointer;
            background: white;
            position: relative;
        }

 
        .accordion-content {
            display: none;
            position: relative;
        }

        .accordion-content.visible {
            display: table-row;
        }
        .table {
            color: #464545 !important;
        }

       #danger_1 .popover-header {
            background-color: #ff0000 !important;
        }

        
        .employee-img {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    margin-right: 8px;
    vertical-align: middle;
}

.employee-img-small {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    margin-right: 5px;
    vertical-align: middle;
}



    @keyframes flash {
        0%   { background-color: #c3f3c3; }
        50%  { background-color: #a8e6a8; }
        100% { background-color: #c3f3c3; }
        }

        .animated.flash {
        animation: flash 2s ease-in-out 1;
        }
    </style>

@endsection

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
                                <h2 class="content-header-title float-left mb-0">LEAD-LISTE</h2>
                                <div class="breadcrumb-wrapper col-12">
                                     <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Home</a></li>
                                        @if(Route::currentRouteName() == 'deleted.leads')
                                        <li class="breadcrumb-item active"><a href="{{ url('/employee_dashboard') }}">Gelöschte Leads</a></li>
                                        @elseif(Route::currentRouteName() == 'my.leads')
                                        <li class="breadcrumb-item active"><a href="{{ url('/employee_dashboard') }}">Meine Leads</a></li>
                                        @elseif(Route::currentRouteName() == 'new.leads')
                                        <li class="breadcrumb-item active"><a href="{{ url('/employee_dashboard') }}">Neue Leads</a></li>
                                               @elseif(Route::currentRouteName() == 'inquiry.junk.list')
                                        <li class="breadcrumb-item active"><a href="{{ url('/employee_dashboard') }}">Junk Liste</a></li>
                                        @else
                                        <li class="breadcrumb-item active"><a href="{{ url('/employee_dashboard') }}">Alle Leads</a></li>
                                        @endif
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                     
                </div>

                <div class="content-body">
                <!-- Table Hover Animation start -->
                    <div class="row" id="table-hover-animation">
                        <div class="col-12">
                            <div class="cards">
                                <div class="card-content">
                                    <div class="card-body">   
                                        <!-- Colors Section --> 
                                        <section id="upper_view" style="display:none">
                                            <div class="card-title">
                                                <h4 class="text-bold-700 mt-2 mb-2" style="    text-align: center; color: #b1aaaa;" >LEADS ÜBERSICHT</h4>
                                            </div>
                                            <div class="row" style="display: flex; flex-wrap: nowrap; justify-content: space-evenly; overflow-x: auto; width: 100%;">
                                                <div class="container scrollable-container">
                                                    <div class="col-md-12" style="display: flex !important; justify-content: space-evenly;">
                                                        <div class="cards text-center">
                                                            <div class="card-content opens">
                                                                <div class="card-body inner_size" style="background: #e53060;">
                                                                    <h6 class="text-bold-700" style="color:white; font-width:bold; font-size:8px;">NEUE ANFRAGE</h6>
                                                                    <h1 class="text-bold-700" style="color:white;font-width:bold;margin-bottom: 0;margin-top: -12px;">{{ $counts['open'] }}</h1>
                                                                    <p class=" " style="color:white; font-width:bold;">({{ number_format($counts['open_per'], 0)  }}%)</p>
                                                                    
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="cards text-center">
                                                            <div class="card-content actives">
                                                                <div class="card-body inner_size" style="background: #92b532;">
                                                                    <h6 class="text-bold-700" style="color:white; font-width:bold;font-size:8px;">AKTIV ANFRAGE</h6>
                                                                    <h1 class="text-bold-700" style="color:white;font-width:bold;margin-bottom: 0;margin-top: -12px;">{{ $counts['active'] }}</h1>
                                                                    <p class=" " style="color:white; font-width:bold;"> ({{ number_format($counts['active_per'], 0)  }}%)</p>


                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="cards text-center">
                                                            <div class="card-content inactives">
                                                                <div class="card-body inner_size" style="background: #78a7cc;">
                                                                    <h6 class="text-bold-700" style="color:white; font-width:bold;font-size:8px;">INAKTIV ANFRAGE</h6>
                                                                    <h1 class="text-bold-700" style="color:white;font-width:bold;margin-bottom: 0;margin-top: -12px;">{{ $counts['inactive'] }}</h1>
                                                                    <p class=" " style="color:white; font-width:bold;">({{ number_format($counts['inactive_per'], 0)  }}%)</p>


                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="cards text-center">
                                                            <div class="card-content project_ends">
                                                                <div class="card-body inner_size" style="background: #213985;">
                                                                    <h6 class="text-bold-700" style="color:white; font-width:bold;font-size:8px;">JUNK ANFRAGE</h6>
                                                                    <h1 class="text-bold-700" style="color:white;font-width:bold;margin-bottom: 0;margin-top: -12px;">{{ $counts['ended'] }}</h1>
                                                                    <p class=" " style="color:white; font-width:bold;">({{ number_format($counts['end_per'], 0) }}%)</p>


                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="cards text-center">
                                                            <div class="card-content project_cancel">
                                                                <div class="card-body inner_size" style="background: #7e7d7d;">
                                                                    <h6 class="text-bold-700" style="color:white; font-width:bold;font-size:8px;">ABSAGE</h6>
                                                                    <h1 class="text-bold-700" style="color:white;font-width:bold;margin-bottom: 0;margin-top: -12px;">{{ $counts['cancel'] }}</h1>
                                                                    <p class=" " style="color:white; font-width:bold;">({{ number_format($counts['cancel_per'], 0) }}%)</p>


                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="cards text-center">
                                                            <div class="card-content project_cancel">
                                                                <div class="card-body inner_size" style="background: #782568;">
                                                                    <h6 class="text-bold-700" style="color:white; font-width:bold;font-size:8px;">ALLE GEWERKE</h6>
                                                                    <h1 class="text-bold-700" style="color:white;font-width:bold;margin-bottom: 0;margin-top: -12px;">{{ $counts['all']}}</h1>
                                                                    <p class=" " style="color:white; font-width:bold;">(100%)</p>


                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Article Groups Section -->
                                            <div class="row" >
                                                <div class="container" >
                                                    <div class="card-header" style="justify-content: center !important;background: transparent;border: 0;justify-items: center;">
                                                        <h4 class="text-bold-700 mt-2 mb-2" style="color: #b1aaaa;">SORTIERUNG NACH GEWERKEN</h4>
                                                    
                                                    </div>
                                                    <div class="col-md-12 text-center" style="display: flex; flex-wrap: nowrap; justify-content: space-evenly; overflow-x: auto; width: 100%;">
                                                     @foreach ($article as $index => $ar)
                                                        <div class="col-md-2 products" style="display: flex; align-items: center;">
                                                            <div class="articles">
                                                                <input type="radio" id="article{{ $index }}" name="article" value="article{{ $index }}">
                                                                <label for="article{{ $index }}" class="text-bold-700 white">{{ $ar->initial }}</label>
                                                            </div>
                                                            <div class="article_text mt-1">
                                                                <p class="text-bold-700">{{ $ar->article_group }}
                                                                    <br>
                                                                 @php
                                                                    $allCount = $counts['all'] ?? 0; 
                                                                    $count_product = $customer_product_count->where('article_group', $ar->article_group)->count();

                                                                    // Calculate the percentage of total
                                                                    $percentage = $allCount > 0 ? ($count_product / $allCount) * 100 : 0;

                                                                    $open = $customer_product_count->where('article_group', $ar->article_group)->where('status', 'open')->count();
                                                                    $active = $customer_product_count->where('article_group', $ar->article_group)->where('status', 'active')->count();
                                                                    $inactive = $customer_product_count->where('article_group', $ar->article_group)->where('status', 'inactive')->count();
                                                                    $ended = $customer_product_count->where('article_group', $ar->article_group)->where('status', 'ended')->count();
                                                                    $cancel = $customer_product_count->where('article_group', $ar->article_group)->where('status', 'cancel')->count();

                                                                    // Ensure we avoid division by zero
                                                                    if ($count_product > 0) {
                                                                        $open_per = ($open / $count_product) * 100;
                                                                        $active_per = ($active / $count_product) * 100;
                                                                        $inactive_per = ($inactive / $count_product) * 100;
                                                                        $ended_per = ($ended / $count_product) * 100;
                                                                        $cancel_per = ($cancel / $count_product) * 100;
                                                                    } else {
                                                                        $open_per = 0;
                                                                        $active_per = 0;
                                                                        $inactive_per = 0;
                                                                        $ended_per = 0;
                                                                        $cancel_per = 0;
                                                                    }

                                                                    $sum_per = $open_per + $active_per + $inactive_per + $ended_per + $cancel_per;
                                                                @endphp

                                                                    <label for="" class="lable">Anzahl Leads</label>
                                                                    <h2 class="bold">{{ $count_product }}</h2>
                                                                    <label for="" class="lable"><code>{{ number_format($percentage, 2) }}%</code></label>
                                                                </p>
                                                            </div>

                                                            <div class="article_text mt-1">
                                                                <p class="text-bold-700">
                                                                    <ul style="padding:0">
                                                                        <li class="mb-1 openli1">
                                                                            <label for="" class="openli ">#{{$customer_product_count->where('article_group', $ar->article_group)->where('status', 'open')->count() }}</label>
                                                                            <label for="" class="simpleli">{{ number_format($open_per, 0) }}% </label> 
                                                                        </li> 
                                                                        <li class="mb-1 activeli1">
                                                                            <label for="" class="activeli ">#{{$customer_product_count->where('article_group', $ar->article_group)->where('status', 'active')->count() }}</label>
                                                                            <label for="" class="simpleli">{{ number_format($active_per, 0) }}%</label> 
                                                                        </li> 
                                                                        <li class="mb-1 inactiveli1">
                                                                            <label for="" class="inactiveli ">#{{$customer_product_count->where('article_group', $ar->article_group)->where('status', 'inactive')->count() }}</label>
                                                                            <label for="" class="simpleli">{{ number_format($inactive_per, 0) }}%</label> 
                                                                        </li>  
                                                                        <li class="mb-1 endedli1">
                                                                            <label for="" class="endedli ">#{{$customer_product_count->where('article_group', $ar->article_group)->where('status', 'ended')->count() }}</label>
                                                                            <label for="" class="simpleli">{{ number_format($ended_per, 0) }}%</label> 
                                                                        </li> 
                                                                        <li class="mb-1 cancelli1">
                                                                            <label for="" class="cancelli ">#{{$customer_product_count->where('article_group', $ar->article_group)->where('status', 'cancel')->count() }}</label>
                                                                            <label for="" class="simpleli">{{ number_format($cancel_per, 0) }}%</label> 
                                                                        </li>   
                                                                        <hr style="    border: 2px solid black;">
                                                                        <li class="mb-1 sumli1">
                                                                            <label for="" class="sumli ">Σ {{$customer_product_count->where('article_group', $ar->article_group)->count() }}</label>
                                                                            <label for="" class="simpleli">{{ number_format($sum_per, 0) }}%</label> 
                                                                        </li>  
                                                                    </ul>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        @endforeach 
                                                    </div>
                                                    <div class="col-md-12 d-none">
                                                         <canvas id="statusPieChart"></canvas>
                                                    </div>
                                                      @php
                                                            $statusCounts = [
                                                                'open' => 0,
                                                                'active' => 0,
                                                                'inactive' => 0,
                                                                'ended' => 0,
                                                                'cancel' => 0
                                                            ];
                                                        @endphp 
                                                        @foreach ($article as $index => $ar)
                                                            @php
                                                                $statusCounts['open'] += $customer_product_count->where('article_group', $ar->article_group)->where('status', 'open')->count();
                                                                $statusCounts['active'] += $customer_product_count->where('article_group', $ar->article_group)->where('status', 'active')->count();
                                                                $statusCounts['inactive'] += $customer_product_count->where('article_group', $ar->article_group)->where('status', 'inactive')->count();
                                                                $statusCounts['ended'] += $customer_product_count->where('article_group', $ar->article_group)->where('status', 'ended')->count();
                                                                $statusCounts['cancel'] += $customer_product_count->where('article_group', $ar->article_group)->where('status', 'cancel')->count();
                                                            @endphp
                                                        @endforeach

                                                </div>
                                            </div> 
                                        </section>
                                 
                                        <!-- Search Section -->
                                        <div class="row"  >  
                                            <div class="col-12 " style="    display: flex !important; flex-direction: row-reverse;">
                                                <a type="button" 
                                                    class="btn btn-icon btn-icon rounded-circle btn-primary ml-1 mr-1 mb-1 waves-effect waves-light float-left"  
                                                    data-toggle="popover" data-content="Liste der Produkte und deren Prozentsatz nach jeder Artikelgruppe." 
                                                    href="{{ url('lead/overview') }}"
                                                    data-trigger="hover" data-original-title="KUNDENÜBERSICHT">
                                                    <i class="feather icon-list"></i>
                                                </a> 

                                                <form action="{{ action('App\Http\Controllers\NewLeadsController@index') }}">
                                                    <fieldset>
                                                        <div class="input-group">
                                                            <input type="text" name="search" class="form-control" placeholder="Search Form" aria-describedby="button-addon2">
                                                            <div class="input-group-append" id="button-addon2">
                                                                <button class="btn btn-primary" type="submit">Go</button>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </form>
                                                <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary ml-1 mr-1 mb-1 waves-effect waves-light" id="colaps" name="colaps" data-toggle="popover" data-content="Liste der Produkte und deren Prozentsatz nach jeder Artikelgruppe." data-trigger="hover" data-original-title="KUNDENÜBERSICHT"><i class="feather icon-chevron-down"></i></button> 
                                            </div>  
                                        </div>

                                    
                                        <!-- Contents Details of Customer -->
                                        <div class="row"> 
                                                 @if ($errors->any())
                                                    <div class="alert alert-danger">
                                                        <ul>
                                                            @foreach ($errors->all() as $error)
                                                                <li>{{ $error }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif

                                            
                                                <div class="table-responsive">
                                                    <table class="table">
                                                       @php
                                                            $direction = request()->get('sort_order') === 'asc' ? 'desc' : 'asc';
                                                            $icon = request()->get('sort_order') === 'asc' ? 'feather icon-chevron-up' : 'feather icon-chevron-down';
                                                        @endphp

                                                        <thead style="background:white;">
                                                        <tr>
                                                            <th class="{{ request()->get('sort_by') == 'new_leads.customer_no' ? 'table-active' : '' }}">
                                                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'new_leads.customer_no', 'sort_order' => $direction]) }}">
                                                                    KUNDE-NR <i class="{{ request()->get('sort_by') == 'new_leads.customer_no' ? $icon : '' }}"></i>
                                                                </a>
                                                            </th>

                                                            <th class="{{ request()->get('sort_by') == 'new_leads.quelle' ? 'table-active' : '' }}">
                                                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'new_leads.quelle', 'sort_order' => $direction]) }}">
                                                                    QUELLE <i class="{{ request()->get('sort_by') == 'new_leads.quelle' ? $icon : '' }}"></i>
                                                                </a>
                                                            </th>

                                                            <th class="{{ request()->get('sort_by') == 'new_leads.name' ? 'table-active' : '' }}">
                                                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'new_leads.name', 'sort_order' => $direction]) }}">
                                                                    NAME <i class="{{ request()->get('sort_by') == 'new_leads.name' ? $icon : '' }}"></i>
                                                                </a>
                                                            </th>

                                                            <th>KONTAKT</th>
                                                            <th>GEWERKE</th>

                                                            <th style="width:100px !important;">NOTIZ</th>

                                                            <th style="width:30px !important;">
                                                                <span data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">STATUS</span>
                                                                <div class="dropdown-menu">
                                                                    <span><label for="">Filtern nach</label></span>

                                                                    <span class="dropdown-item">
                                                                        <a href="{{ url('/lead_qualified_sort') }}">
                                                                            <i class="fa fa-circle primary"></i> QUALIFIZIERT
                                                                        </a>
                                                                    </span>

                                                                    <span class="dropdown-item">
                                                                        <a href="{{ url('/lead_not_qualified_sort') }}">
                                                                            <i class="fa fa-circle warning"></i> ERFORDERLICHE INFORMATIONEN
                                                                        </a>
                                                                    </span>

                                                                    <span class="dropdown-item">
                                                                        <a href="{{ url('/lead_incomplete_sort') }}">
                                                                            <i class="fa fa-circle danger"></i> NICHT QUALIFIZIERT
                                                                        </a>
                                                                    </span>

                                                                    <span class="dropdown-item">
                                                                        <a href="{{ url('/lead_junk_sort') }}">
                                                                            <i class="fa fa-power-off danger"></i> JUNKS
                                                                        </a>
                                                                    </span>
                                                                </div>
                                                            </th>

                                                            <th>VERFASSER</th>
                                                            <th width="2">BEARBEITEN</th>
                                                        </tr>
                                                        </thead>

                                                        @php
                                                            $highlightId = session('highlight_lead_id');
                                                        @endphp
                                                        <tbody id="accordion-table-body">
                                                            @foreach($data as $item)    
                                                                <tr class="accordion-row mb-2 {{ $highlightId == $item->id ? 'table-success animated flash' : '' }}" data-row="{{$item->id}}"   >  
                                                                    <td>{{$item->customer_no}}</td>
                                                                    <td>{{$item->source}}</td>
                                                                    <td><a href="{{ url('new_lead_profile/'.$item->id) }} " class="black">{{ $item->title }} {{ $item->name}} {{ $item->lastname}} <br>
                                                                        <small><i class="feather icon-map"></i> {{ $item->full_address ?? null }} </small>
                                                                    
                                                                        </a>
                                                                    </td> 
                                                                    <td>
                                                                        <p class="mb-0" ><i class="feather icon-phone-call" ></i> {{ $item->telephone }}</p>
                                                                        <p class="mb-0" ><i class="feather icon-smartphone" ></i> {{ $item->phone }}</p>
                                                                        <p class="mb-0" ><i class="feather icon-mail" ></i> {{ $item->email }}</p>
                                                                    </td> 
                                                                    <td>     

                                                                       @php
                                                                            // Ensure the filtered collection only contains non-soft-deleted leads
                                                                            $filteredProducts = $productcount->where('customer_id', $item->id);
                                                                            $groupedProducts = $filteredProducts->groupBy('product_id');
                                                                        @endphp

                                                                        @foreach ($groupedProducts as $productId => $products)
                                                                            @php
                                                                                $productC = $products->first(); // Get the first product instance
                                                                                $productCount = $products->count(); // Count how many times the product exists
                                                                            @endphp

                                                                            <div class="position-relative d-inline-block mr-2" style="background: #8fc73e; padding: 15px; border-radius: 50%; font-size: 8px; width: 10px; height: 10px;">
                                                                                <span style="padding: 0; margin: 0; font-size: 8px; position: relative; top: -5px; left: -10px; color: white;">
                                                                                    {{ $productC->initial }}
                                                                                </span>
                                                                                <span class="badge badge-pill badge-primary badge-up" style="position: absolute; top: -7px; right: -7px; border: 1px solid; font-size: 8px !important; background:#73B1D5 !important;">
                                                                                    {{ $productCount }}
                                                                                </span>
                                                                            </div>
                                                                        @endforeach

                                                                    </td>
                                                                    <td>
                                                                        @if($item->info)
                                                                        <!-- Button to open modal -->
                                                                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1" data-toggle="modal" data-target="#note{{$item->id}}">
                                                                            <i class="fa fa-sticky-note-o"></i>
                                                                        </button>
                                                                        @else
                                                                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" >
                                                                            <i class="fa fa-sticky-note-o"></i>
                                                                        </button>
                                                                        @endif
                                                                        <!-- Modal -->
                                                                        <div class="modal fade" id="note{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header bg-primary white">
                                                                                        <h5 class="modal-title" id="myModalLabel120">Notizen</h5>
                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                            <span aria-hidden="true">×</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <div class="col-md-10"> 
                                                                                            <h1>{{ $item->title }} {{$item->name}} {{ $item->lastname}}</h1> 
                                                                                            <p>{{ $item->street}}<br>{{ $item->postcode }} 
                                                                                            </p>
                                                                                            <p style="margin:0; line-height:0px"><i class="feather icon-phone-call" ></i> {{ $item->telephone }}</p>
                                                                                            <p style="margin:0; line-height:0px"><i class="feather icon-smartphone" ></i> {{ $item->phone }}</p>
                                                                                            <p style="margin:0; line-height:0px"><i class="feather icon-mail" ></i> {{ $item->email }}</p>
                                                                                        </div>
                                                                                        <hr>
                                                                                        <h1 class="mb-2">Notizen</h1>
                                                                                        <div class="col-md-12">
                                                                                            <p>{{ $item->info }}</p>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <!-- Modal footer (optional) -->
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </td>   
                                                                    <td style="width: 20px;">
                                            
                                                                        @if($item->status_msg=="QUALIFIZIERT")  
                                                                        <div class="image d-flex">
                                                                            <div class="image">
                                                                                <img src="{{ asset('images/icons/ampel-gruen.svg') }}" alt="Icon"  style="width:20px"
                                                                                    data-content="DIE ANFRAGE IST BEREIT ZU QUALIFIZIEREN" 
                                                                                    data-trigger="hover" 
                                                                                    data-original-title="QUALIFIZIERT"> 
                                                                            </div> 
                                                                        </div>       

                                                                        @elseif($item->status_msg == "um zu qualifizieren, bitte per Brief  Kontakt aufnehmen")   
                                                                    <div class="image d-flex">
                                                                        <div class="image">
                                                                        <img src="{{ asset('images/icons/ampel-rot.svg') }}" alt="Icon" style="width:20px" 
                                                                                data-toggle="popover" 
                                                                                    data-content=" {{ $item->status_msg }}" 
                                                                                    data-trigger="hover" 
                                                                                color="red"
                                                                                    data-original-title="NICHT QUALIFIZIERT">
                                                                        </div>
                                                                        
                                                                    </div>

                                                                    @elseif($item->status == "Junk")   
                                                                    <div class="image d-flex">
                                                                        <div class="image"> 
                                                                        <img src="{{ asset('images/icons/ampel-rot.svg') }}" alt="Icon" style="width:20px" 
                                                                                data-toggle="popover" 
                                                                                    data-content=" {{ $item->status_msg }}" 
                                                                                    data-trigger="hover" 
                                                                                color="red"
                                                                                    data-original-title="NICHT QUALIFIZIERT">  
                                                                        </div>
                                                                        
                                                                    </div>
                                                                

                                                                        @else 
                                                                        <div class="image d-flex">
                                                                            <div class="image">
                                                                            <img src="{{ asset('images/icons/ampel-gelb.svg') }}" alt="Icon"  style="width:20px"
                                                                                data-toggle="popover" 
                                                                                    data-content=" {{ $item->status_msg }}" 
                                                                                    data-trigger="hover" 
                                                                                    data-original-title="NICHT QUALIFIZIERT">   
                                                                                </div> 
                                                                            </div>

                                                                        @endif
                                                                    </td>
                                                                    <td style="width:20px">
                                                                        <div class="image">
                                                                            <div class="avatar mr-1 ">
                                                                                <img src="{{ asset('images/employee/'.$item->c_image)}}" alt="avtar img holder" height="32" width="32" data-toggle="tooltip" data-placement="top" title data-original-tiitle="{{ $item->c_name }} {{ $item->c_lastname}}">
                                                                            </div>
                                                                            <div class="text">
                                                                                <span class="font-weight-bold"></span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="direction-dropdown-default mt-1">
                                                                        <div class="btn-group mr-1 mb-1">
                                                                            <div class="dropdown">
                                                                                <button type="button" class="btn btn-flat-primary mr-1 mb-1 waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                                    <i class="feather icon-menu"></i>
                                                                                </button>
                                                                                <div class="dropdown-menu dropdown-menu-right" x-placement="top-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(189px, 38px, 0px);">
                                                                                @if($item->status_msg == "QUALIFIZIERT") <a class="dropdown-item active" href="{{ url('new_object/'.$item->id)}}">Neue Objekt</a> @endif
                                                                                    <a class="dropdown-item" href="{{ url('new_lead_details_edit/'.$item->id)}}">Bearbeiten</a>
                                                                                    @if($item->deleted_at == Null)
                                                                                    <a class="dropdown-item"  data-toggle="modal" data-target="#delete-pro{{$item->id}}">Löschen</a>
                                                                                    @else
                                                                                    <a class="dropdown-item"  data-toggle="modal" data-target="#delete-pro{{$item->id}}">Wiederherstellen</a>
                                                                                    @endif  
                                                                                
                                                                                    @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_delete', '=', 'on')->first())
                                                                                        @if($item->status!="Junk")
                                                                                            <a  class="dropdown-item"  data-toggle="modal" data-target="#junk{{$item->id}}"><i class="fa fa-power-off danger " ></i> Junk</a> 
                                                                                        @else 
                                                                                            <a class="dropdown-item"  data-toggle="modal" data-target="#unjunk{{$item->id}}"><i class="fa fa-power-off primary" ></i> Unjunk</a> 
                                                                                        @endif
                                                                                    @endif
                                                                            
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                            
                                                                        <!-- Delete Modal -->
                                                                            <div class="modal fade" id="delete-pro{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header bg-danger white">
                                                                                            <h5 class="modal-title" id="myModalLabel120"> @if(Route::currentRouteName() != 'deleted.leads') Daten Löschen @else Wiederherstellen @endif</h5>
                                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                <span aria-hidden="true">×</span>
                                                                                            </button>
                                                                                        </div>
                                                                                        @if(Route::currentRouteName() != 'deleted.leads')
                                                                                        <div class="modal-body">
                                                                                            <h5>Aufzeichnung löschen</h5>
                                                                                            <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                                            <p>Die Datensatznummer lautet:{{$item->id}}. {{ $item->name }} {{ $item->lastname }} </p>
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                            <a type="button" href="{{url('/new_lead_delete').'/'.$item->id}}" class="btn btn-danger">Ja</a>
                                                                                        </div>
                                                                                        @else

                                                                                        <div class="modal-body">
                                                                                        <h5>Daten wiederherstellen: </h5>
                                                                                        <p>Möchten Sie diese Daten wirklich wiederherstellen?</p>
                                                                                        <p>Die Datensatznummer lautet:{{$item->id}}. {{ $item->name }} {{ $item->lastname }} </p>
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                            <a type="button" href="{{url('/restore_leads').'/'.$item->id}}" class="btn btn-danger">Ja</a>
                                                                                        </div>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Delete Modal -->
                                                                            <div class="modal fade" id="junk{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header bg-danger white">
                                                                                            <h5 class="modal-title" id="myModalLabel120">{{ $item->name }} {{ $item->lastname }}</h5>
                                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                <span aria-hidden="true">×</span>
                                                                                            </button>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                            <h5>Junk record</h5>
                                                                                            <p>Möchten Sie diese Anfrage als Junk festlegen?</p>
                                                                                            <p>Die Datensatznummer lautet:{{$item->id}} </p>
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                            <a type="button" href="{{url('/lead_junk').'/'.$item->id}}" class="btn btn-danger">Ja</a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                                <!-- Unjunk Modal -->
                                                                            <div class="modal fade" id="unjunk{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="false" data-backdrop="false">
                                                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header bg-primary white">
                                                                                            <h5 class="modal-title" id="myModalLabel120">{{ $item->name }} {{ $item->lastname }}</h5>
                                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                <span aria-hidden="true">×</span>
                                                                                            </button>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                            <h5>Junk record</h5>
                                                                                            <p>Möchten Sie die Junk-Anfrage wiederherstellen?</p>
                                                                                            <p>Die Datensatznummer lautet:{{$item->id}} </p>
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                            <a type="button" href="{{url('/lead_unjunk').'/'.$item->id}}" class="btn btn-primary">Ja</a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>          <!-- Unjunk Modal -->
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr class="accordion-content hidden" data-row="{{$item->id}}">
                                                                <td colspan="12"> 
                                                                     <div class="table-responsive">
                                                                        <table class="table">
                                                                            <thead>
                                                                                <tr style="background:white; ">  
                                                                                    <th>ID</th>  
                                                                                    <th style=" display: flex;justify-content: space-around;     width: 135px;    border: 0;">
                                                                                    <a class="secondary" data-toggle="popover" 
                                                                                        data-content="Bitte dringend die Anfrage bearbeiten" 
                                                                                        data-trigger="hover" 
                                                                                        data-original-title="Wichtigkeit grad sehr hoch!">
                                                                                        <i class="feather icon-alert-circle " style="font-size: 20px;"></i>
                                                                                    </a>
                                                                            
                                                                                    <a class="secondary" data-toggle="popover" 
                                                                                        data-content="die Anfrage liegt länger als 48 Stunden es muss dringend bearbeitet werden" 
                                                                                        data-trigger="hover" 
                                                                                        data-original-title="Zeit von 48 Stunden überschritten!">
                                                                                        <i class="feather icon-bell " style="font-size: 20px;"></i>
                                                                                    </a>
                                                                                    <a class="secondary" data-toggle="popover" 
                                                                                        data-content="bitte innerhalb von 48 Stunden die Anfrage Qualifzieren" 
                                                                                        data-trigger="hover" 
                                                                                        data-original-title="Neue Anfrage">
                                                                                        <i class="feather icon-star " style="font-size: 20px;"> </i>
                                                                                    </a>
                                                                                    </th>
                                                                                    <th>DATUM</th>  
                                                                                    <th>OBJEKT</th> 
                                                                                    <th>ADRESSE</th> 
                                                                                    <th>NOTIZ</th> 
                                                                                    <th>PRODUKT</th>  
                                                                                    <th width="2">BEARBEITEN</th> 
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach($alternative->where('lead_id', $item->id) as $alter) 
                                                                                    <tr style="background:white;border-bottom: 1px solidrgb(81, 81, 81);" class="mb-2">   
                                                                                        <td>{{$alter->id}}</td>
                                                                                        <th scope="row" style="width:20px">
                                                                                                <div style="display: flex; flex-wrap: nowrap; align-content: center; justify-content: space-evenly;width: 93px;">
                                                                                                    <?php 
                                                                                                    $currentDateTime = new DateTime(); // Current date and time
                                                                                                    $requestDateTime = new DateTime($alter->request_date); // Request date and time

                                                                                                    $interval = $currentDateTime->diff($requestDateTime); // Difference between current date and request date
                                                                                                    $hoursDifference = ($interval->days * 24) + $interval->h; // Convert difference to hours

                                                                                                    // Check if the priority is 'sehr dringend'
                                                                                                    if (strtolower($alter->periority) === 'sehr dringend') {
                                                                                                        echo '<a href=""><i class="feather icon-alert-circle danger blink" id="alert' . $alter->id . '" style="font-size: 20px;"></i></a><br>';
                                                                                                    } else {
                                                                                                        echo '<a href=""><i class="feather icon-alert-circle secondary" id="alert' . $alter->id . '" style="font-size: 20px;"></i></a><br>';
                                                                                                    }

                                                                                                    // Check if the request date is more than 48 hours ago
                                                                                                    if ($hoursDifference > 48) {
                                                                                                        echo '<a href=""><i class="feather icon-bell danger blink" id="bell' . $alter->id . '"  style="font-size: 20px;"></i></a><br>';
                                                                                                    } else {
                                                                                                        echo '<a href=""><i class="feather icon-bell secondary" id="bell' . $alter->id . '" style="font-size: 20px;"></i></a><br>';
                                                                                                    }

                                                                                                    // Check if the request date is within 48 hours
                                                                                                    if ($hoursDifference <= 48) {
                                                                                                        echo '<a href=""><i class="feather icon-star warning" id="stars' . $alter->id . '" style="font-size: 20px;"></i></a><br>';
                                                                                                    } else {
                                                                                                        echo '<a href=""><i class="feather icon-star secondary" id="stars' . $alter->id . '" style="font-size: 20px;"></i></a><br>';
                                                                                                    }
                                                                                                    ?>
                                                                                                </div>
                                                                                            </th> 
                                                                                        <td>
                                                                                            <i class="feather icon-calendar"></i> {{ \Carbon\Carbon::parse($alter->request_date)->isoFormat('DD.MM.YY') }} <br>
                                                                                            <code> <strong> 
                                                                                                {{ \Carbon\Carbon::parse($alter->request_date)->diffForHumans() }}                                   
                                                                                            </strong></code>  
                                                                                        </td>
                                                                                        <td>
                                                                                            <a href="{{url('new_lead_profile/'.$item->id )}}">
                                                                                            {{ $alter->object_name }}   </a>
                                                                                        </td>
                                                                                        <td>
                                                                                            <small>
                                                                                                <i class="feather icon-map"></i> {{ $alter->street ?? null }} <br>
                                                                                                {{ $alter->postcode }} <br>
                                                                                                {{ $alter->city }}
                                                                                            </small>
                                                                                        </td>  
                                                                                        
                                                                                        <td>
                                                                                            @if($alter->note)
                                                                                            <!-- Button to open modal -->
                                                                                            <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1" data-toggle="modal" data-target="#info{{$alter->id}}">
                                                                                                <i class="fa fa-sticky-note-o"></i>
                                                                                            </button>
                                                                                            @else
                                                                                            <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" >
                                                                                                <i class="fa fa-sticky-note-o"></i>
                                                                                            </button>
                                                                                            @endif
                                                                                            <!-- Modal -->
                                                                                            <div class="modal fade" id="info{{$alter->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                                                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                                    <div class="modal-content">
                                                                                                        <div class="modal-header bg-primary white">
                                                                                                            <h5 class="modal-title" id="myModalLabel120">Notizen</h5>
                                                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                <span aria-hidden="true">×</span>
                                                                                                            </button>
                                                                                                        </div>
                                                                                                        <div class="modal-body">
                                                                                                            <div class="col-md-10"> 
                                                                                                                <h1>{{ $item->title }} {{$item->name}} {{ $item->lastname}}</h1> 
                                                                                                                <p>{{ $item->street}}<br>{{ $item->postcode }}
                                                                                                                    @if($alter->main == 1)
                                                                                                                    <small><code>Die Adresse des Kunden stimmt nicht mit seiner Hauptwohnadresse überein</code></small>
                                                                                                                    @endif
                                                                                                                </p>
                                                                                                                <p style="margin:0; line-height:0px"><i class="feather icon-phone-call" ></i> {{ $item->telephone }}</p>
                                                                                                                <p style="margin:0; line-height:0px"><i class="feather icon-smartphone" ></i> {{ $item->phone }}</p>
                                                                                                                <p style="margin:0; line-height:0px"><i class="feather icon-mail" ></i> {{ $item->email }}</p>
                                                                                                            </div>
                                                                                                            <hr>
                                                                                                            <h1 class="mb-2">Notizen</h1>
                                                                                                            <div class="col-md-12">
                                                                                                                <p>{{ $alter->note }}</p>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="modal-footer">
                                                                                                            <!-- Modal footer (optional) -->
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </td> 
                                                                                        <td class="">
                                                                                            <div style="justify-items: center;display: flex;align-items: center;justify-content: flex-start;flex-wrap: nowrap;">
                                                                                            @php
                                                                                                $product_list = collect($productList);
                                                                                            @endphp

                                                                                            @foreach ($product_list->where('customer_id', $item->id)->where('alternative_id', $alter->id)->unique(fn($product) => $product->product_id.'-'.$product->alternative_id) as $product)
                                                                                                @if ($product->status == "open")
                                                                                                    @php
                                                                                                        $services = [
                                                                                                            'complete' => 'Komplettlösung',
                                                                                                            'montage' => 'Montage',
                                                                                                            'product' => 'Produkt',
                                                                                                            'plan' => 'Planung',
                                                                                                            'maintenance' => 'Wartung',
                                                                                                            'repair' => 'Reparatur',
                                                                                                            'emergency' => 'Notdienst',
                                                                                                            'others' => 'Sonstiges',
                                                                                                        ];
                                                                                                        $service = $services[$product->service] ?? $product->service;
                                                                                                        $status = $services[$product->res_status] ?? $product->res_status;
                                                                                                        $reason = $services[$product->reason] ?? $product->reason;
                                                                                                    @endphp

                                                                                                    @php
                                                                                                       $name = $lastname = $emp_image = $gender = $msg = $state = null;

                                                                                                        if (isset($productEmployees) && is_iterable($productEmployees)) {
                                                                                                            foreach ($productEmployees as $employee) {
                                                                                                                // Ensure both are the same type (cast if necessary)
                                                                                                                if ((string)$employee->id === (string)$product->current_employee) {
                                                                                                                    $name = $employee->name;
                                                                                                                    $lastname = $employee->lastname;
                                                                                                                    $emp_image = $employee->image;
                                                                                                                    $gender = $employee->gender;
                                                                                                                    $state = $product->res_status ?? null;
                                                                                                                    $msg = null;
                                                                                                                    break;
                                                                                                                }
                                                                                                            }
                                                                                                        }

                                                                                                    @endphp

                                                                                                    @php
                                                                                                        $defaultImage = $gender === "Male" 
                                                                                                            ? asset('images/gender/male.png') 
                                                                                                            : asset('images/gender/female.png');

                                                                                                        $employeeImage = file_exists('images/employee/'.$emp_image) && $emp_image 
                                                                                                            ? asset('images/employee/'.$emp_image) 
                                                                                                            : $defaultImage;
                                                                                                    @endphp 

                                                                                                    <div class="d-flex flex-column align-items-center mr-1">
                                                                                                        <div class="d-flex align-items-center">
                                                                                                            <div class="circle" data-toggle="tooltip" data-original-title="{{ $product->article_group }}">
                                                                                                                {{ $product->initial }}
                                                                                                            </div>
                                                                                                            <div class="line"></div> 
                                                                                                            <div class="image" data-toggle="tooltip" data-original-title="{{ $name && $lastname ? $name . ' ' . $lastname : 'Nicht zugewiesen' }}">
                                                                                                                <img src="{{ $employeeImage }}" alt="Profile" 
                                                                                                                    data-employee-id="{{ $product->current_employee ?? '' }}" 
                                                                                                                    data-product-id="{{ $product->product_id }}" 
                                                                                                                    data-new-lead-id="{{ $item->id }}" 
                                                                                                                    data-alternative-id="{{ $alter->id}}" 
                                                                                                                    data-toggle="modal" data-target="#addEmployee{{$alter->id}}"
                                                                                                                    class="@if($status=='accept') profile @elseif($status=='reject') profile-r @else profile-s @endif">
                                                                                                            </div> 
                                                                                                        </div>

                                                                                                    
                                                                                                        <div class="text">{{ $services[$product->service] ?? $product->service }}</div>
                                                                                                        @if($employee->id == auth()->user()->name && $status=='send')
                                                                                                            <button type="button" class="btn btn-primary mr-1 mb-1 waves-effect waves-light p-1 acceptModal"  
                                                                                                                data-toggle="modal" data-target="accept{{$alter->id}}"
                                                                                                                data-id="{{$item->id}}"
                                                                                                                data-product-name="{{ $product->article_group }}"
                                                                                                                data-employee="{{ $product->current_employee }}"
                                                                                                                data-product-id="{{ $product->product_id }}"
                                                                                                                data-service="{{ $product->service}}"
                                                                                                                data-product-list="{{ $product->p_list_id}}"
                                                                                                                data-alternative-id="{{ $alter->id}}">
                                                                                                                <span style="font-size: 10px;"> Antwort</span>
                                                                                                            </button>
                                                                                                        @endif
                                                                                                    </div> 


                                                                                                <!-- Add Employee Modal  -->
                                                                                                <div class="modal fade text-left" id="addEmployee{{$alter->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                                                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                                        <div class="modal-content">
                                                                                                            <div class="modal-header bg-primary white">
                                                                                                                <h5 class="modal-title" id="myModalLabel160">Wählen Sie Mitarbeiter aus</h5>
                                                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                    <span aria-hidden="true">×</span>
                                                                                                                </button>
                                                                                                            </div>
                                                                                                            <form method="post" action="{{ route('save.selectedEmployee') }}">
                                                                                                                @csrf
                                                                                                                <input type="hidden" name="employee_id" id="modalEmployeeId" value="">
                                                                                                                <input type="hidden" name="product_id" id="modalProductId" value="">
                                                                                                                <input type="hidden" name="lead_id" id="modalLeadId" value="">
                                                                                                                <input type="hidden" name="alternative_id" id="modalAlternativeId" value=""> 
                                                                                                                <div class="modal-body">
                                                                                                                    <select id="employeeSelect" name="employees" style="width: 100%;" data-placeholder="Mitarbeiter auswählen">
                                                                                                                    </select>

                                                                                                                </div>
                                                                                                                <div class="modal-footer">
                                                                                                                    <button type="submit" class="btn btn-primary waves-effect waves-light">Speichern</button>
                                                                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Absagen</button>
                                                                                                                </div>
                                                                                                            </form>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div> 
                                                                                                @endif
                                                                                            @endforeach


                                                                                            </div>
                                                                                        </td> 
                                                                                    
                                                                                       
                                                                                        
                                                                                        <td>

                                                                                        <div class="btn-group dropup dropdown-icon-wrapper mr-1 mb-1"> 
                                                                                            <button type="button" class="btn   dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                                                <i class="feather icon-menu dropdown-icon"></i>
                                                                                            </button>
                                                                                            <div class="dropdown-menu">

                                                                                                @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_update', '=', 'on')->first())
                                                                                                <span class="dropdown-item">
                                                                                                <a  data-toggle="modal" 
                                                                                                    data-target="#add_employee{{$alter->id}}"   
                                                                                                    data-employee-id="" 
                                                                                                    data-product-id="" 
                                                                                                    data-new-lead-id="{{ $item->id }}" >
                                                                                                    <i class="feather icon-user" ></i> 
                                                                                                    Verantwortlicher bearbeiten
                                                                                                </a> 
                                                                                                </span>
                                                                                                @endif
                                                                                                @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_update', '=', 'on')->first())
                                                                                                <span class="dropdown-item">
                                                                                                <a  href="{{ url('/new_lead_edit/'.$item->id.'/'.$alter->id)}}" ><i class="feather icon-edit" ></i> Bearbeiten</a> 
                                                                                                </span>
                                                                                                @endif

                                                                                                @if(DB::table('user_rolls')->where('user_id', auth()->user()->name)->where('item_id', 'Customer')->where('is_delete', 'on')->exists())
                                                                                                    <a href="#" class="dropdown-item addNewProduct" data-id="{{ $item->id }}" data-alternative-id="{{$alter->id}}" data-toggle="modal" data-target="#addProductModal">
                                                                                                        <i class="feather icon-plus text-success"></i> Produkt Erstellen
                                                                                                    </a>
                                                                                                @endif
                                                                                                @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_delete', '=', 'on')->first())
                                                                                                    @if(Route::currentRouteName() != 'deleted.leads')
                                                                                                    <span class="dropdown-item danger">
                                                                                                        <a data-toggle="modal" data-target="#delete-alter{{$alter->id}}"><i class="feather icon-trash-2 danger" ></i>Löschen</a>
                                                                                                    </span>
                                                                                                    @else
                                                                                                        <span class="dropdown-item danger">
                                                                                                        <a data-toggle="modal" data-target="#delete-alter{{$alter->id}}"><i class="feather icon-trash-2 danger" ></i>Wiederherstellen</a>
                                                                                                    </span>
                                                                                                    @endif
                                                                                                @endif

                                                                                                <!-- @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_delete', '=', 'on')->first())
                                                                                                    @if($item->status!="Junk")
                                                                                                    <span class="dropdown-item danger">
                                                                                                        <a data-toggle="modal" data-target="#alter_junk{{$alter->id}}"><i class="fa fa-power-off danger " ></i> Junk</a>
                                                                                                    </span>
                                                                                                    @else
                                                                                                    <span class="dropdown-item danger">
                                                                                                        <a data-toggle="modal" data-target="#alter_unjunk{{$alter->id}}"><i class="fa fa-power-off primary" ></i> Unjunk</a>
                                                                                                    </span>
                                                                                                    @endif
                                                                                                @endif -->
                                                                                
                                                                                            </div>
                                                                                        </div>

                                                                                            <div class="modal fade text-left" id="add_employee{{$alter->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                                                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                                                                    <div class="modal-content">
                                                                                                        <div class="modal-header bg-primary white">
                                                                                                            <h5 class="modal-title" id="myModalLabel160">Liste der Verantwortlichen</h5>
                                                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                <span aria-hidden="true">×</span>
                                                                                                            </button>
                                                                                                        </div>
                                                                                                        <form action="{{ route('accept.lead')}}" method="post">
                                                                                                            @csrf 
                                                                                                        <div class="modal-body"> 
                                                                                                            <div class="table-responsive">
                                                                                                                <table class="table">
                                                                                                                    <thead>
                                                                                                                        <tr>
                                                                                                                            <th>Kunde</th>
                                                                                                                            <th>Produkt</th>
                                                                                                                            <th>Adress</th>
                                                                                                                        </tr>
                                                                                                                    </thead>
                                                                                                                    <tbody>
                                                                                                                        <tr> 
                                                                                                                            <td>{{ $item->name }} {{ $item->lastname }}</td>
                                                                                                                            <td style="display: flex; justify-content: space-evenly;">
                                                                                                                            @foreach ($current_request as $request) 
                                                                                                                            @if($request->customer_id == $item->id) 
                                                                                                                        
                                                                                                                                <div class="badge badge-primary ">
                                                                                                                                    {{ $request->initial }}
                                                                                                                                </div> 
                                                                                                                                <input type="hidden" name="product_id" value="{{ $request->product_id }}"> 
                                                                                                                            @endif
                                                                                                                            @endforeach
                                                                                                                                </td>

                                                                                                                            <td>{{ $alter->street }} {{$alter->postcode}}, {{$alter->city}}</td>
                                                                                                                        </tr> 
                                                                                                                    </tbody>
                                                                                                                </table>
                                                                                                            </div>
                                                                                                        <div class="row">
                                                                                                            <input type="hidden" name="customer_id" value="{{ $item->id }}">
                                                                                                            <input type="hidden" name="employee_id" value="{{ auth()->user()->name }}">
                                                                                                            <div class="col-xl-12 col-md-12 col-12 mb-1">
                                                                                                                    <table class="table">
                                                                                                                        <thead>
                                                                                                                            <tr>
                                                                                                                                <th>#</th>
                                                                                                                                <th>Produkt</th>
                                                                                                                                <th>Leistung</th>
                                                                                                                                <th>Verantwortlich</th>
                                                                                                                                <th>Status</th>
                                                                                                                                <th>Aktion</th>
                                                                                                                            </tr>
                                                                                                                        </thead>
                                                                                                                        <tbody>
                                                                                                                            @foreach ($product_list->where('customer_id', $item->id)->where('alternative_id', $alter->id)->unique(fn($product) => $product->product_id.'-'.$product->alternative_id) as $product)
                                                                                                                                @if ($product->status == "open")
                                                                                                                                <tr>  

                                                                                                                                    @php
                                                                                                                                        $services = [
                                                                                                                                            'complete' => 'Komplettlösung',
                                                                                                                                            'montage' => 'Montage',
                                                                                                                                            'product' => 'Produkt',
                                                                                                                                            'plan' => 'Planung',
                                                                                                                                            'maintenance' => 'Wartung',
                                                                                                                                            'repair' => 'Reparatur',
                                                                                                                                            'emergency' => 'Notdienst',
                                                                                                                                            'others' => 'Sonstiges',
                                                                                                                                        ];

                                                                                                                                        $service = $services[$product->service] ?? $product->service;
                                                                                                                                        $status = $services[$product->res_status] ?? $product->res_status;
                                                                                                                                        $reason = $services[$product->reason] ?? $product->reason;
                                                                                                                                    @endphp
                                                                                                                                        @php
                                                                                                                                            $name = null;
                                                                                                                                            $lastname = null;
                                                                                                                                            $emp_image = null;
                                                                                                                                            $gender = null;
                                                                                                                                            $msg = 'Not Defined';
                                                                                                                                            $state = null;

                                                                                                                                            if (isset($productEmployees) && is_iterable($productEmployees)) {
                                                                                                                                                foreach ($productEmployees as $employee) {
                                                                                                                                                    if ($employee->id == $product->current_employee) {
                                                                                                                                                        $name = $employee->name;
                                                                                                                                                        $lastname = $employee->lastname;
                                                                                                                                                        $emp_image = $employee->image;
                                                                                                                                                        $gender = $employee->gender;
                                                                                                                                                        $state = $product->res_status ?? null;
                                                                                                                                                        $msg = null;
                                                                                                                                                        break;
                                                                                                                                                    }
                                                                                                                                                }
                                                                                                                                            }
                                                                                                                                        @endphp
                                                        
                                                                                                                                        @php
                                                                                                                                                // Determine the default image based on gender
                                                                                                                                                $defaultImage = $gender === "Male" 
                                                                                                                                                    ? asset('images/gender/male.png') 
                                                                                                                                                    : asset('images/gender/female.png');

                                                                                                                                                // Determine the actual image to use
                                                                                                                                                $employeeImage = file_exists('images/employee/'.$emp_image) && $emp_image 
                                                                                                                                                    ? asset('images/employee/'.$emp_image) 
                                                                                                                                                    : $defaultImage;
                                                                                                                                            @endphp 
                                                                                                                                        <td>  
                                                                                                                                        {{ $loop->index + 1 }}
                                                                                                                                        </td>
                                                                                                                                        <td>
                                                                                                                                            <div class="circle">{{ $product->initial }}</div>
                                                                                                                                        </td>
                                                                                                                                        <td>
                                                                                                                                            {{ $services[$product->service] ?? $product->service }}
                                                                                                                                        </td>
                                                                                                                                        <td>
                                                                                                                                        
                                                                                                                                            <div class="image" data-toggle="tooltip" 
                                                                                                                                                data-original-title="{{ $name && $lastname ? $name . ' ' . $lastname : 'Nicht zugewiesen' }}">
                                                                                                                                                <img src="{{ $employeeImage }}" alt="Profile" 
                                                                                                                                                    data-employee-id="{{ $employee->id }}" 
                                                                                                                                                    data-product-id="{{ $product->product_id }}" 
                                                                                                                                                    data-new-lead-id="{{ $item->id }}" 
                                                                                                                                                    data-toggle="modal" data-target="#addEmployee"
                                                                                                                                                
                                                                                                                                                class="@if($status=='accept') profile @elseif($status=='reject') profile-r @else profile-s @endif">
                                                                                                                                            {{ $name && $lastname ? $name . ' ' . $lastname : 'Nicht zugewiesen' }}

                                                                                                                                            </div> 
                                                                                                                                        </td> 
                                                                                                                                        <td>
                                                                                                                                        @if($status=='accept')  Vom Kunden akzeptiert @elseif($status=='reject') Kunde abgelehnt @else Warten auf Annahme @endif 
                                                                                                                                        </td>
                                                                                                                                        <td>
                                                                                                                                            <i class="feather icon-trash danger delete-responsible" 
                                                                                                                                                data-responsible="{{ $product->responsible_id }}" 
                                                                                                                                                data-product="{{ $product->p_list_id }}" 
                                                                                                                                                data-alternative="{{ $alter->id }}" 
                                                                                                                                            data-toggle="modal" data-target="#deleteModal{{$alter->id}}"></i>
                                                                                                                                        </td>
                                                                                                                                        </tr>
                                                                                                                                @endif
                                                                                                                            @endforeach 
                                                                                                                            
                                                                                                                            <!-- Delete Confirmation Modal -->

                                                                                                                            
                                                                                                                            <div class="modal fade" id="deleteModal{{$alter->id}}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="false">
                                                                                                                                    <div class="modal-dialog" role="document">
                                                                                                                                        <div class="modal-content">
                                                                                                                                            <div class="modal-header">
                                                                                                                                                <h5 class="modal-title" id="deleteModalLabel">Modal löschen</h5>
                                                                                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                                                    <span aria-hidden="true">&times;</span>
                                                                                                                                                </button>
                                                                                                                                            </div>
                                                                                                                                            
                                                                                                                                            <input type="hidden" name="product" value="">
                                                                                                                                            <input type="hidden" name="employee" value="">
                                                                                                                                            <input type="hidden" name="alternative" value="">

                                                                                                                                            <div class="modal-body">
                                                                                                                                                <p>Zum Löschen die Option auswählen</p> 
                                                                                                                                                <ul class="list-unstyled mb-0">
                                                                                                                                                    <li class="d-inline-block mr-2">
                                                                                                                                                        <fieldset> 
                                                                                                                                                            <input type="radio" id="deleteEmployee{{$alter->id}}" name="customerRadio{{$alter->id}}" checked>
                                                                                                                                                            <label for="deleteEmployee{{$alter->id}}">Löschen Sie nur die verantwortliche Person</label> 
                                                                                                                                                        </fieldset>
                                                                                                                                                    </li>
                                                                                                                                                    <li class="d-inline-block mr-2">
                                                                                                                                                        <fieldset> 
                                                                                                                                                            <input type="radio" id="deleteProduct{{$alter->id}}" name="customerRadio{{$alter->id}}">
                                                                                                                                                            <label for="deleteProduct{{$alter->id}}">Löschen Sie das gesamte Produkt vom Kunden</label> 
                                                                                                                                                        </fieldset>
                                                                                                                                                    </li>
                                                                                                                                                </ul>
                                                                                                                                            </div>

                                                                                                                                            <div class="modal-footer">
                                                                                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Absagen</button>
                                                                                                                                                <a type="button" class="btn btn-danger" id="confirmDelete{{$alter->id}}" href="#">Löschen bestätigen</a>
                                                                                                                                            </div>
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                </div>


                                                                                                                        </tbody>
                                                                                                                    </table>
                                                                                                                </div>  
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="modal-footer"> 
                                                                                                            <button type="submit" class="btn btn-primary waves-effect waves-light"  >Speichern</button>
                                                                                                        </div>
                                                                                                        </form>
                                                                                                    </div>
                                                                                                    
                                                                                                </div>
                                                                                            </div>
                                                                                        
                                                                                            

                                                                                            <div class="modal fade" id="delete-alter{{$alter->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                                                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                                    <div class="modal-content">
                                                                                                        <div class="modal-header bg-danger white">
                                                                                                            <h5 class="modal-title" id="myModalLabel120"> @if(Route::currentRouteName() != 'deleted.leads') Objekt Löschen @else Wiederherstellen @endif</h5>
                                                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                <span aria-hidden="true">×</span>
                                                                                                            </button>
                                                                                                        </div>
                                                                                                        @if(Route::currentRouteName() != 'deleted.leads')
                                                                                                        <div class="modal-body">
                                                                                                            <h5>Aufzeichnung löschen</h5>
                                                                                                            <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                                                            <p>Die Datensatznummer lautet:{{$item->customer_no}}. {{ $item->name }} {{ $item->lastname }} </p>
                                                                                                            <p>Die Objektname: {{$alter->object_name}}</p>
                                                                                                        </div>
                                                                                                        <div class="modal-footer">
                                                                                                            <a type="button" href="{{url('/delete_lead_alternative').'/'.$alter->id}}" class="btn btn-danger">Ja</a>
                                                                                                        </div>
                                                                                                        @else

                                                                                                <div class="modal-body">
                                                                                                        <h5>Daten wiederherstellen: </h5>
                                                                                                        <p>Möchten Sie diese Daten wirklich wiederherstellen?</p>
                                                                                                        <p>Die Datensatznummer lautet:{{$item->id}}. {{ $item->name }} {{ $item->lastname }} </p>
                                                                                                        </div>
                                                                                                        <div class="modal-footer">
                                                                                                            <a type="button" href="{{url('/restore_alternative_leads').'/'.$alter->id}}" class="btn btn-danger">Ja</a>
                                                                                                        </div>
                                                                                                        @endif
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div> 
                                                                                        </td>  
                                                                                    </tr>   
                                                                                @endforeach
                                                                            </tbody>

                                                                            

                                                                        </table>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                   
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                        </div>
                                        <div class="d-flex justify-content-center mt-3">
                                            {{ $data->appends(request()->query())->links() }}
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Table head options end -->
                </div>
            </div>
        </div>
    </div>
</div>


<!-- MOdals  -->

<!-- Accept Modal  -->
 <div class="modal fade text-left acceptModal" id="acceptModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary white">
                <h5 class="modal-title" id="myModalLabel160">Antwort</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="post" action="{{ route('accept.lead')}}">
                @csrf
            <div class="modal-body"> 
                 <input type="hidden" name="customer_id" value="">
                <input type="hidden" name="product_name" id="product_name" value="">
                <input type="hidden" name="employee_id" value="" >
                <input type="hidden" name="product_id" value="" >
                <input type="hidden" name="service" value="" >
                <input type="hidden" name="product_list" value="" >
                <input type="hidden" name="alternative_id" value="" >
                 <label for="">Antwort</label>
                <select name="response" id="" class="form-control">
                    <option value="accept">akzeptieren</option>
                    <option value="reject">Nicht akzeptieren</option>
                </select>
                 <label for="">Grund <code><small>.Erforderlich, wenn Sie den Job ablehnen</small></code></label>
                 <textarea name="reason" id="" cols="30" rows="10" class="form-control"></textarea>  
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Abbrechen</button>
                <button type="submit" class="btn btn-primary waves-effect waves-light" >Speichern</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- Accept Modal  -->


 <div class="modal fade" id="addProductModal" tabindex="-1">  <!-- New Product Modal: start  -->
    <div class="modal-dialog modal-xl">
        <form id="addProductForm">
            @csrf
            <input type="hidden" name="customer_id" id="modal_customer_id">
            <input type="hidden" name="alternative_id" id="modal_alternative_id">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h5 class="modal-title">Produkt hinzufügen</h5>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="modalProductTable">
                        <thead>
                            <tr>
                                <th>Produkt</th>
                                <th>Dienstleistung</th>
                                <th>Abteilung</th>
                                <th>Mitarbeiter</th>
                                <th>Aktion</th>
                            </tr>
                        </thead>
                        <tbody id="existingProductRows"></tbody>
                        <tbody id="modalNewRows"></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-success mt-1" id="modalAddRow">+ Neue </button>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </div>
            </div>
        </form>
    </div>
</div>  <!-- New Product Modal: end  --> 

<!-- END: Content-->
@endsection
 
@section('script')  
 <!-- Accordian:start  -->

<script>
    document.querySelectorAll('.accordion-row').forEach(row => {
        row.addEventListener('click', () => {
            const rowId = row.dataset.row;
            const contentRow = document.querySelector(`.accordion-content[data-row="${rowId}"]`);
            contentRow.classList.toggle('visible');
        });
    });
</script>

<script>
    $(document).ready(function(){
        @if(Session::has('update_msg'))
        toastr.success("{{ session('updated_msg') }}");
        @endif
        @if(Session::has('save_msg'))
        toastr.success("{{ session('save_msg') }}");
        @endif
        @if(Session::has('delete_msg'))
        toastr.error("{{ session('delete_msg') }}");
        @endif
    });
</script>


<script>
$(document).ready(function() {
    $('.articles input[type="radio"]').on('change', function() {
        // Reset styles for all labels
        $('.articles input[type="radio"] + label').css({
            'background': '#b1aaaa',
            'color': 'inherit',
            'border-radius': '50%'
        });

        // Apply styles for the selected label
        if (this.checked) {
            $(this).next('label').css({
                'background': '#92b532',
                'color': 'white',
                'border-radius': '50%'
            });

            // Send AJAX request
            let articleGroup = $(this).val();
            $.ajax({
                url: '/customer_details', // Your endpoint for searching article group
                method: 'GET',
                data: { search: articleGroup, is_ajax: true },
                success: function(response) {
                    // Handle the response here
                    console.log(response);
                    // Update the page content based on the response
                    $('#results').html(response); // Assuming 'results' is the id of the element where you want to display the results
                },
                error: function(error) {
                    // Handle the error here
                    console.error(error);
                }
            });
        }
    });
});
</script>

<!-- Showing the accept button modal: start -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Add an event listener for all buttons with the class "acceptModal"
    document.querySelectorAll('.btn.acceptModal').forEach(function(button) {
        button.addEventListener('click', function(event) {
            // Get the modal element by its class
            const modal = document.querySelector('.modal.acceptModal');

            // Retrieve data attributes from the button
            const customerId = button.getAttribute('data-id');
            const productName = button.getAttribute('data-product-name');
            const currentEmployee = button.getAttribute('data-employee');
            const productId = button.getAttribute('data-product-id');
            const service = button.getAttribute('data-service');
            const productList = button.getAttribute('data-product-list');
            const alternativeId = button.getAttribute('data-alternative-id');

            // Populate the modal inputs with the retrieved data
            modal.querySelector('input[name="customer_id"]').value = customerId;
            modal.querySelector('input[name="product_name"]').value = productName;
            modal.querySelector('input[name="employee_id"]').value = currentEmployee;
            modal.querySelector('input[name="product_id"]').value = productId;
            modal.querySelector('input[name="service"]').value = service;
            modal.querySelector('input[name="product_list"]').value = productList;
            modal.querySelector('input[name="alternative_id"]').value = alternativeId;

            // Show the modal if not automatically triggered
            $(modal).modal('show');
        });
    });
});

</script>
<!-- Showing the accept button modal: end -->
<!-- deleteing the responsible and product from the list of empoyee modal:start  -->
<script>
    document.addEventListener('DOMContentLoaded', function () { 
        // Handle delete button click and set modal input values
        document.querySelectorAll('.delete-responsible').forEach(function(button) {
            button.addEventListener('click', function() {
                const responsibleId = this.dataset.responsible || '';
                const productId = this.dataset.product || '';
                const alternativeId = this.dataset.alternative || '';

                // Set hidden input values in the modal
                document.querySelector('input[name="employee"]').value = responsibleId;
                document.querySelector('input[name="product"]').value = productId;
                document.querySelector('input[name="alternative"]').value = alternativeId;
            });
        });

        // Handle the confirm delete button click
        document.querySelectorAll('[id^="confirmDelete"]').forEach(function(deleteButton) {
            deleteButton.addEventListener('click', function () {
                const modalId = this.id.replace('confirmDelete', '');
                const responsibleId = document.querySelector('input[name="employee"]').value;
                const productId = document.querySelector('input[name="product"]').value;

                // Check which radio button is selected
                const deleteEmployeeRadio = document.getElementById(`deleteEmployee${modalId}`).checked;
                const deleteProductRadio = document.getElementById(`deleteProduct${modalId}`).checked;

                let url = '';

                if (deleteEmployeeRadio && responsibleId) {
                    url = `/delete_lead_responsible/${responsibleId}`;
                } else if (deleteProductRadio && productId) {
                    url = `/delete_lead_product/${productId}`;
                } else {
                    alert('Bitte wählen Sie eine gültige Option aus.');
                    return;
                }

                // Update the href attribute on the button
                this.setAttribute('href', url);
            });
        });
    });
</script>


 
<!-- deleteing the responsible and product from the list of empoyee modal:end  -->

<!-- seding the customer to planing phase: start:  -->
<script>
  document.querySelectorAll('.sendToPlaning').forEach(button => {
    button.addEventListener('click', function() {
        // Get data from button attributes
        const employeeId = this.getAttribute('data-employee');
        const productId = this.getAttribute('data-product');
        const customerId = this.getAttribute('data-customer');
        const service = this.getAttribute('data-service');
        const productList = this.getAttribute('data-product-list');

        Swal.fire({
            title: "Bist du sicher?",
            text: "Möchten Sie diesen Kunden zur Planung senden?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ja, senden!",
            cancelButtonText: "Abbrechen"
        }).then((result) => {
            if (result.isConfirmed) {
                // Perform POST request
                fetch("{{ route('planing.save') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        employee_id: employeeId,
                        product_id: productId,
                        customer_id: customerId,
                        service: service,
                        product_list: productList,
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP-Fehler! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            "Erfolg!",
                            "Der Kunde wurde erfolgreich zur Planung gesendet.",
                            "success"
                        ).then(() => {
                            location.reload();  // Refresh the page on success
                        });
                    } else {
                        Swal.fire(
                            "Fehler!",
                            data.error || "Beim Senden des Kunden ist ein Fehler aufgetreten.",
                            "error"
                        );
                    }
                })
                .catch((error) => {
                    console.error("Fehler:", error);
                    Swal.fire(
                        "Fehler!",
                        "Ein unerwarteter Fehler ist aufgetreten.",
                        "error"
                    );
                });
            }
        });
    });
});

</script>
<!-- seding the customer to planing phase: end:  -->

<script>
document.getElementById('colaps').addEventListener('click', function() {
    var section = document.getElementById('upper_view');
    var icon = this.querySelector('i');
    
    if (section.style.display === 'none' || section.style.display === '') {
        section.style.display = 'block';
        icon.classList.remove('feather', 'icon-chevron-down');
        icon.classList.add('feather', 'icon-chevron-up');
    } else {
        section.style.display = 'none';
        icon.classList.remove('feather', 'icon-chevron-up');
        icon.classList.add('feather', 'icon-chevron-down');
    }
});
</script>

<script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
        // Pass the PHP data to JavaScript
        const statusCounts = @json($statusCounts);

        // Data for Chart.js
        const data = {
            labels: ['Open', 'Active', 'Inactive', 'Ended', 'Cancel'],
            datasets: [{
                data: [
                    statusCounts.open,
                    statusCounts.active,
                    statusCounts.inactive,
                    statusCounts.ended,
                    statusCounts.cancel
                ],
                backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#cc65fe', '#ff9f40']
            }]
        };

        // Config for Chart.js
        const config = {
            type: 'pie',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw + ' (' + tooltipItem.raw + '%)';
                            }
                        }
                    }
                }
            },
        };

        // Render the pie chart
        window.onload = function() {
            const ctx = document.getElementById('statusPieChart').getContext('2d');
            new Chart(ctx, config);
        };
    </script>



<!-- Adding new Responsible  -->

 <script>
   $(document).ready(function () {
    let newLeadId = null;
    let productId = null;
    let alternative = null;

    // Open the modal and load employees
    $('[data-target="#addEmployee"]').on('click', function () {
        const employeeId = $(this).data('employee-id'); // Current employee ID
        newLeadId = $(this).data('new-lead-id'); // Lead ID
        productId = $(this).data('product-id'); // Product ID
        alternative = $(this).data('alternative-id'); // Product ID

        // Populate hidden inputs in the modal
        $('#modalEmployeeId').val(employeeId);
        $('#modalProductId').val(productId);
        $('#modalLeadId').val(newLeadId);
        $('#modalAlternativeId').val(alternative);

        // Fetch available employees via AJAX
        $.ajax({
            url: '/checkEmployeeAvailability',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                product_id: productId
            },
            success: function (response) {
                // Clear previous options
                $('#employeeSelect').empty();

                // Populate select2 with available employees or fallback
                const employees = response.availableEmployees.length > 0 
                    ? response.availableEmployees 
                    : response.inCaseEmployees;

                if (employees.length > 0) {
                    employees.forEach(employee => {
                        $('#employeeSelect').append(new Option(
                            `${employee.name} ${employee.lastname}`,
                            employee.id
                        ));
                    });
                } else {
                    toastr.warning('No employees found for this product.');
                }

                // Initialize or refresh select2
                $('#employeeSelect').select2();
            },
            error: function (xhr) {
                toastr.error('Failed to fetch employees. Please try again.');
                console.error(xhr.responseText);
            }
        });
    });
});

 </script>


<script>
    $(document).ready(function () {
    loadEmployees(); // Call function to fetch employees

    function loadEmployees() {
        $.ajax({
            url: "/getEmployees",
            method: "POST",
            data: { _token: $('meta[name="csrf-token"]').attr("content") },
            success: function (response) {
                console.log("📌 Employees Loaded:", response); // Debugging

                let employees = response.map(emp => {
                    return {
                        id: emp.id,
                        text: `${emp.name} ${emp.lastname}`,
                        image: emp.image ? `/images/employees/${emp.image}` : "/images/default-user.png"
                    };
                });

                // Initialize Select2 with images
                $("#employeeSelect").select2({
                    data: employees,
                    templateResult: formatEmployee,  // Customize how options appear
                    templateSelection: formatEmployeeSelection, // Customize selected item
                    escapeMarkup: function (m) { return m; }, // Allow HTML rendering
                    width: '100%'
                });
            },
            error: function (xhr, status, error) {
                console.error("❌ Error fetching employees:", error);
            }
        });
    }

    // Function to format options in the dropdown
    function formatEmployee(employee) {
        if (!employee.id) {
            return employee.text;
        }
        return $(`<span><img src="${employee.image}" class="employee-img" /> ${employee.text}</span>`);
    }

    // Function to format the selected item
    function formatEmployeeSelection(employee) {
        return $(`<span><img src="${employee.image}" class="employee-img-small" /> ${employee.text}</span>`);
    }
});

</script>



 <script>
    const services = @json($serviceList);
const productInfo = @json($productInfo); 
const departments = @json($departments);
const employeeImagePath = "{{ asset('images/employee') }}";
const defaultAvatar = "{{ asset('images/gender/male.png') }}";

let modalRowIndex = 0;

$(document).ready(function () {
    $('#addProductModal').on('hidden.bs.modal', function () {
        location.reload();
    });

    $(document).on('click', '.addNewProduct', function () {
        const customerId = $(this).data('id');
        const alternativeId = $(this).data('alternative-id');
        $('#modal_customer_id').val(customerId);
        $('#modal_alternative_id').val(alternativeId);
        $('#existingProductRows').empty();
        $('#modalNewRows').empty();
        modalRowIndex = 0;

        $.get(`/lead/get/products/${customerId}`, function (data) {
            data.forEach(row => {
                const productLabel = row.article_group || '-';
                const department = departments.find(d => d.id == row.department_id);
                const service = services.find(s => s.id == row.service_id);

                const serviceLabel = translateService(service?.phase_section); 
                const departmentLabel = department?.department_name || '-';
                const employeeImg = row.image ? `${employeeImagePath}/${row.image}` : defaultAvatar;

                $('#existingProductRows').append(`
                    <tr>
                        <td>${productLabel}</td>
                        <td>${serviceLabel}</td>
                        <td>${departmentLabel}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="${employeeImg}" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                                <span>${row.name ?? ''} ${row.lastname ?? ''}</span>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-danger delete-product" 
                                    data-id="${row.id}" 
                                    data-product-id="${row.product_id}" 
                                    data-inquiry-id="${customerId}">
                                <i class="feather icon-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
        });

        $('#addProductModal').modal('show');
        $('#modalAddRow').trigger('click');
    });

    $('#modalAddRow').click(function () {
        modalRowIndex++;
        const newRow = `
        <tr data-index="${modalRowIndex}">
            <td>
                <select class="form-select product-select" data-index="${modalRowIndex}" name="product_id[]" style="width:100%">
                    <option value="">Produkt wählen</option>
                        ${productInfo.length > 0 ? productInfo.map(p => `<option value="${p.id}">${p.article_group}</option>`).join('') : '<option disabled>No products available</option>'}
                </select>
            </td>
            <td>
                <select class="form-select service-select" data-index="${modalRowIndex}" name="service_id[]" style="width:100%">
                    <option value="">Service wählen</option>
                </select>
            </td>
            <td>
                <select class="form-select department-select" data-index="${modalRowIndex}" name="department_id[]" style="width:100%">
                    <option value="">Abteilung wählen</option>
                    ${departments.map(d => `<option value="${d.id}">${d.department_name}</option>`).join('')}
                </select>
            </td>
            <td>
                <select class="form-select employee-select" data-index="${modalRowIndex}" name="employee_id[]" style="width:100%">
                    <option value="">Mitarbeiter wählen</option>
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-outline-danger btn-sm removeRow">
                    <i class="feather icon-trash"></i>
                </button>
            </td>
        </tr>`;
        $('#modalNewRows').append(newRow);
        initializeSelect2(modalRowIndex);
    });

    function initializeSelect2(index) {
        const $product = $(`.product-select[data-index="${index}"]`);
        const $service = $(`.service-select[data-index="${index}"]`);
        const $department = $(`.department-select[data-index="${index}"]`);
        const $employee = $(`.employee-select[data-index="${index}"]`);

        $product.select2().on('change', () => {
            loadServices(index);
            loadEmployees(index);
        });

        $service.select2().on('change', () => loadEmployees(index));
        $department.select2().on('change', () => loadEmployees(index));

        $employee.select2({
            templateResult: formatEmployee,
            templateSelection: formatEmployeeSelection,
            escapeMarkup: m => m
        });
    }

    function loadServices(index) {
        const productId = $(`.product-select[data-index="${index}"]`).val();
        const $serviceSelect = $(`.service-select[data-index="${index}"]`);
        const filtered = services.filter(s => s.product_id == productId);

        const map = {
            complete: "Komplettlösung",
            montage: "Montage",
            product: "Produkt",
            plan: "Planung",
            maintenance: "Wartung",
            repair: "Reparatur",
            reclaim: "Reklamation",
            others: "Sonstiges"
        };

        $serviceSelect.empty().append('<option value="">Service wählen</option>');
        filtered.forEach(s => {
            const label = map[s.phase_section?.toLowerCase()] || s.phase_section;
            $serviceSelect.append(`<option value="${s.id}">${label}</option>`);
        });
        $serviceSelect.select2();
    }

    function loadEmployees(index) {
        const productId = $(`.product-select[data-index="${index}"]`).val();
        const serviceId = $(`.service-select[data-index="${index}"]`).val();
        const departmentId = $(`.department-select[data-index="${index}"]`).val();
        const $employeeSelect = $(`.employee-select[data-index="${index}"]`);

        if (productId && serviceId && departmentId) {
            $.post('{{ route("lead.department.employees") }}', {
                _token: $('input[name="_token"]').val(),
                product_id: productId,
                service_id: serviceId,
                department_id: departmentId
            }, function (data) {
                $employeeSelect.empty().append('<option value="">Mitarbeiter wählen</option>');
                data.forEach(emp => {
                    $employeeSelect.append(
                        `<option value="${emp.id}" data-img="${emp.image}" data-positions="${emp.positions.join(', ')}">${emp.name} ${emp.lastname}</option>`
                    );
                });

                $employeeSelect.select2({
                    templateResult: formatEmployee,
                    templateSelection: formatEmployeeSelection,
                    escapeMarkup: m => m
                });
            });
        }
    }

    function formatEmployee(emp) {
        if (!emp.id) return emp.text;
        const img = $(emp.element).data('img') ? `${employeeImagePath}/${$(emp.element).data('img')}` : defaultAvatar;
        const pos = $(emp.element).data('positions') || '';
        return `
            <div style="display:flex;align-items:center;">
                <img src="${img}" class="me-2 rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                <div><strong>${emp.text}</strong><br><small>${pos}</small></div>
            </div>`;
    }

    function formatEmployeeSelection(emp) {
        return emp.text;
    }

    function translateService(key) {
        const map = {
            complete: "Komplettlösung",
            montage: "Montage",
            product: "Produkt",
            plan: "Planung",
            maintenance: "Wartung",
            repair: "Reparatur",
            reclaim: "Reklamation",
            others: "Sonstiges"
        };
        return map[key?.toLowerCase()] || key;
    }

    $(document).on('click', '.removeRow', function () {
        $(this).closest('tr').remove();
    });

    $(document).on('click', '.delete-product', function () {
        const row = $(this).closest('tr');
        const customerId = $(this).data('inquiry-id');
        const productId = $(this).data('product-id');

        $.ajax({
            url: '{{ route("lead.products.delete") }}',
            method: 'DELETE',
            data: {
                _token: $('input[name="_token"]').val(),
                id: customerId,
                product_id: productId
            },
            success: function () {
                row.remove();
                Swal.fire('Gelöscht', 'Produkt erfolgreich gelöscht', 'success');
            },
            error: function () {
                Swal.fire('Fehler', 'Löschen fehlgeschlagen', 'error');
            }
        });
    });

    $('#addProductForm').submit(function (e) {
        e.preventDefault();
        const rows = $('#modalNewRows tr');
        let isValid = true;

        const data = {
            customer_id: $('#modal_customer_id').val(),
            alternative_id: $('#modal_alternative_id').val(),
            product_id: [],
            service_id: [],
            department_id: [],
            employee_id: [],
            _token: $('input[name="_token"]').val()
        };

        rows.each(function () {
            const index = $(this).data('index');
            const p = $(`.product-select[data-index="${index}"]`).val();
            const s = $(`.service-select[data-index="${index}"]`).val();
            const d = $(`.department-select[data-index="${index}"]`).val();
            const e = $(`.employee-select[data-index="${index}"]`).val();

            if (!p || !s || !d || !e) {
                $(this).addClass('table-danger');
                isValid = false;
            } else {
                $(this).removeClass('table-danger');
                data.product_id.push(p);
                data.service_id.push(s);
                data.department_id.push(d);
                data.employee_id.push(e);
            }
        });

        if (!isValid) {
            Swal.fire({ icon: 'warning', title: 'Fehler', text: 'Alle Felder müssen ausgefüllt werden' });
            return;
        }

        $.post('{{ route("lead.products.save") }}', data, function (res) {
            Swal.fire({ icon: 'success', title: 'Gespeichert', text: res.message })
                .then(() => location.reload());
        });
    });

});

 </script>


@endsection
