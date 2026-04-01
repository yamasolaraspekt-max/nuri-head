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
   .circle-container {
    position: relative;
    width: 37px;
    height: 35px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.circle-svg {
    position: absolute;
    width: 100%;
    height: 100%;
}

.circle-svg text {
    font-size: 16px;
    fill: black;
    text-transform: uppercase;
    font-weight: bold;
}

.circle-icon {
  position: absolute;
    width: 25px;
    height: 25px;
    background-color: #92b531;
    color: white;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 9px;
    font-weight: bold;
}
.avatars{
    white-space: nowrap;
    position: relative;
    cursor: pointer;
    color: #fff;
    display: inline-flex;
    font-size: .75rem;
    text-align: center;
    vertical-align: middle;
    margin: 5px;
    border: 1px dotted #92b531;
    padding: 3px;
    border-radius: 42px
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
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">KUNDE-LISTE</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Home</a></li>
                            </ol>
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
                                                        <div class="card text-center">
                                                            <div class="card-content opens">
                                                                <div class="card-body inner_size" style="background: #e53060;">
                                                                    <h6 class="text-bold-700" style="color:white; font-width:bold; font-size:8px;">NEUE ANFRAGE</h6>
                                                                    <h1 class="text-bold-700" style="color:white;font-width:bold;margin-bottom: 0;margin-top: -12px;">{{ $counts['open'] }}</h1>
                                                                    <p class=" " style="color:white; font-width:bold;">({{ number_format($counts['open_per'], 0)  }}%)</p>
                                                                    
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card text-center">
                                                            <div class="card-content actives">
                                                                <div class="card-body inner_size" style="background: #92b532;">
                                                                    <h6 class="text-bold-700" style="color:white; font-width:bold;font-size:8px;">AKTIV ANFRAGE</h6>
                                                                    <h1 class="text-bold-700" style="color:white;font-width:bold;margin-bottom: 0;margin-top: -12px;">{{ $counts['active'] }}</h1>
                                                                    <p class=" " style="color:white; font-width:bold;"> ({{ number_format($counts['active_per'], 0)  }}%)</p>


                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card text-center">
                                                            <div class="card-content inactives">
                                                                <div class="card-body inner_size" style="background: #78a7cc;">
                                                                    <h6 class="text-bold-700" style="color:white; font-width:bold;font-size:8px;">INAKTIV ANFRAGE</h6>
                                                                    <h1 class="text-bold-700" style="color:white;font-width:bold;margin-bottom: 0;margin-top: -12px;">{{ $counts['inactive'] }}</h1>
                                                                    <p class=" " style="color:white; font-width:bold;">({{ number_format($counts['inactive_per'], 0)  }}%)</p>


                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card text-center">
                                                            <div class="card-content project_ends">
                                                                <div class="card-body inner_size" style="background: #213985;">
                                                                    <h6 class="text-bold-700" style="color:white; font-width:bold;font-size:8px;">JUNK ANFRAGE</h6>
                                                                    <h1 class="text-bold-700" style="color:white;font-width:bold;margin-bottom: 0;margin-top: -12px;">{{ $counts['ended'] }}</h1>
                                                                    <p class=" " style="color:white; font-width:bold;">({{ number_format($counts['end_per'], 0) }}%)</p>


                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card text-center">
                                                            <div class="card-content project_cancel">
                                                                <div class="card-body inner_size" style="background: #7e7d7d;">
                                                                    <h6 class="text-bold-700" style="color:white; font-width:bold;font-size:8px;">ABSAGE</h6>
                                                                    <h1 class="text-bold-700" style="color:white;font-width:bold;margin-bottom: 0;margin-top: -12px;">{{ $counts['cancel'] }}</h1>
                                                                    <p class=" " style="color:white; font-width:bold;">({{ number_format($counts['cancel_per'], 0) }}%)</p>


                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="card text-center">
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
                                                    <div class="card-header" style=" justify-content: center !important;">
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
                                        <div class="row mt-6" style="margin-top:100px;">
                                            <div class="container d-flex"> 
                                                <div class="col-9">
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
                                                </div>
                                                <div class="col-3 d-flex">
                                                    <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1 waves-effect waves-light" id="colaps" name="colaps" data-toggle="popover" data-content="Liste der Produkte und deren Prozentsatz nach jeder Artikelgruppe." data-trigger="hover" data-original-title="KUNDENÜBERSICHT"><i class="feather icon-chevron-down"></i></button>
                                         
                                                </div> 
                                            </div>
                                        </div>

                                    
                                        <!-- Contents Details of Customer -->
                                        <div class="row">
                                              <div class="col-md-12" style=" justify-content: center !important;">
                                                <h4 class="text-bold-700 mt-2 mb-2" style="    text-align: center; color: #b1aaaa;" >KUNDE-LISTE</h4> 
                                            </div>
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
                                                    <thead>
                                                         <tr style="background:#cfe09a; "> 
                                                            <th style="width: 45px;" >ID</th>
                                                            <th style=" display: flex;justify-content: space-around;     width: 135px;    border: 0;">
                                                            <a href="{{ url('/lead_important_sort') }}"  data-toggle="popover" 
                                                                data-content="Bitte dringend die Anfrage bearbeiten" 
                                                                data-trigger="hover" 
                                                                data-original-title="Wichtigkeit grad sehr hoch!">
                                                                <i class="feather icon-alert-circle primary" style="font-size: 20px;"></i>
                                                            </a>
                                                      
                                                            <a href="{{ url('/lead_over_clock_sort') }}"  data-toggle="popover" 
                                                                data-content="die Anfrage liegt länger als 48 Stunden es muss dringend bearbeitet werden" 
                                                                data-trigger="hover" 
                                                                data-original-title="Zeit von 48 Stunden überschritten!">
                                                                <i class="feather icon-bell primary" style="font-size: 20px;"></i>
                                                            </a>
                                                                  <a href="{{ url('/lead_new_sort') }}"  data-toggle="popover" 
                                                                data-content="bitte innerhalb von 48 Stunden die Anfrage Qualifzieren" 
                                                                data-trigger="hover" 
                                                                data-original-title="Neue Anfrage">
                                                                <i class="feather icon-star primary" style="font-size: 20px;"> </i>
                                                            </a>
                                                            </th>
                                                            <th  class="bolders ">DATUM</th> 
                                                            <th  class="bolders ">NAME</th>
                                                            <th  class="bolders ">ADRESSE</th>
                                                            <th  class="bolders ">KONTAKT</th>
                                                            <th  class="bolders ">NOTIZ</th> 
                                                            <th  class="bolders ">PRODUKT</th> 
                                                            <th style="width:20px !important" >   <span data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">STATUS  </span> 
                                                                    <div class="dropdown-menu">
                                                                        <span><label for="">Filtern nach</label></span>
                                                                         <span class="dropdown-item">
                                                                           <a  href="{{ url('/lead_qualified_sort') }}" ><i class="fa fa-circle primary" ></i> QUALIFIZIERT</a> 
                                                                        </span>
                                                                       
                                                                        <span class="dropdown-item">
                                                                             <a  href="{{ url('/lead_not_qualified_sort') }}" ><i class="fa fa-circle warning" ></i> ERFORDERLICHE INFORMATIONEN</a>  
                                                                        </span>

                                                                        <span class="dropdown-item">
                                                                             <a  href="{{ url('/lead_incomplete_sort') }}" ><i class="fa fa-circle danger" ></i> NICHT QUALIFIZIERT</a>  
                                                                        </span> 

                                                                        <span class="dropdown-item">
                                                                             <a  href="{{ url('/lead_junk_sort') }}" ><i class="fa fa-power-off danger" ></i> JUNKS</a>  
                                                                        </span> 
                                                                    </div> 
                                                            </th>
                                                            <th>VERFASSER</th>
                                                            <th width="2">BEARBEITEN</th>
                                                            <th >OPTION</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($data as $item)    
                                                            <tr style="background:white;border-bottom: 13px solid #f8f8f8;" class="mb-2"> 
                                                                <th scope="row">{{ $item->id }}</th>
                                                                <th scope="row" style="width:20px">
                                                                        <div style="display: flex; flex-wrap: nowrap; align-content: center; justify-content: space-evenly;width: 115px;">
                                                                            <?php 
                                                                            $currentDateTime = new DateTime(); // Current date and time
                                                                            $requestDateTime = new DateTime($item->request_date); // Request date and time

                                                                            $interval = $currentDateTime->diff($requestDateTime); // Difference between current date and request date
                                                                            $hoursDifference = ($interval->days * 24) + $interval->h; // Convert difference to hours

                                                                            // Check if the priority is 'sehr dringend'
                                                                            if (strtolower($item->periority) === 'sehr dringend') {
                                                                                echo '<a href=""><i class="feather icon-alert-circle danger blink" id="alert' . $item->id . '" style="font-size: 20px;"></i></a><br>';
                                                                            } else {
                                                                                echo '<a href=""><i class="feather icon-alert-circle secondary" id="alert' . $item->id . '" style="font-size: 20px;"></i></a><br>';
                                                                            }

                                                                            // Check if the request date is more than 48 hours ago
                                                                            if ($hoursDifference > 48) {
                                                                                echo '<a href=""><i class="feather icon-bell primary blink" id="bell' . $item->id . '"  style="font-size: 20px;"></i></a><br>';
                                                                            } else {
                                                                                echo '<a href=""><i class="feather icon-bell secondary" id="bell' . $item->id . '" style="font-size: 20px;"></i></a><br>';
                                                                            }

                                                                            // Check if the request date is within 48 hours
                                                                            if ($hoursDifference <= 48) {
                                                                                echo '<a href=""><i class="feather icon-star warning" id="stars' . $item->id . '" style="font-size: 20px;"></i></a><br>';
                                                                            } else {
                                                                                echo '<a href=""><i class="feather icon-star secondary" id="stars' . $item->id . '" style="font-size: 20px;"></i></a><br>';
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                    </th> 
                                                                <td>
                                                                    <i class="feather icon-calendar"></i> {{ \Carbon\Carbon::parse($item->request_date)->isoFormat('DD.MM.YY') }} <br>
                                                                    <code> <strong> 
                                                                        {{ \Carbon\Carbon::parse($item->request_date)->diffForHumans() }}                                   
                                                                    </strong></code>  
                                                                </td>
                                                                <td><a href="{{url('new_lead_profile/'.$item->id.'/'.$item->postcode.'/'.$item->address_no )}}">
                                                                     {{ $item->name }}  {{ $item->lastname }} </a></td>
                                                                    <td>
                                                                    <i class="feather icon-pin"></i> {{ $item->street }} <br>
                                                                    {{ $item->postcode }} <br>
                                                                    {{ $item->city }}</td>  
                                                                <td>
                                                                    <p class="mb-0" ><i class="feather icon-phone-call" ></i> {{ $item->telephone }}</p>
                                                                    <p class="mb-0" ><i class="feather icon-smartphone" ></i> {{ $item->phone }}</p>
                                                                    <p class="mb-0" ><i class="feather icon-mail" ></i> {{ $item->email }}</p>
                                                                </td> 
                                                                <td>
                                                                    @if($item->note)
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
                                                                                            @if($item->alternative_address == 'false')
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
                                                                                        <p>{{ $item->note }}</p>
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
                                                                        // Ensure $product_list is a collection
                                                                        $product_list = collect($product_list);
                                                                    @endphp

                                                                    @foreach ($product_list->where('customer_id', $item->id) as $product)
                                                                        @if ($product->status == "open")
                                                                            @php
                                                                                $services = [
                                                                                    'complete' => 'Komplettlösung',
                                                                                    'montage' => 'Montage',
                                                                                    'product' => 'Produkt',
                                                                                    'plan' => 'Planung',
                                                                                    'maintenance' => 'Wartung',
                                                                                    'repair' => 'Reparatur',
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
 
                                                                           <ul class="list-unstyled users-list m-0 d-flex align-items-center">
                                                                                <li 
                                                                                    data-toggle="tooltip" 
                                                                                    data-popup="tooltip-custom" 
                                                                                    data-placement="bottom" 
                                                                                    data-original-title="{{ $name && $lastname ? $name . ' ' . $lastname : 'Der Verantwortliche ist nicht benannt' }}" 
                                                                                    class="avatars pull-up" 
                                                                                    style="background-color: 
                                                                                            @if($name && $lastname && $state === 'send') transparent 
                                                                                            @elseif($state === 'reject') #ea5455 
                                                                                            @else transparent 
                                                                                            @endif;"
                                                                                >
                                                                                    @php
                                                                                        // Determine the default image based on gender
                                                                                        $defaultImage = $gender === "male" 
                                                                                            ? asset('images/gender/male.png') 
                                                                                            : asset('images/gender/female.png');

                                                                                        // Determine the actual image to use
                                                                                        $employeeImage = file_exists(public_path('images/employee/'.$emp_image)) && $emp_image 
                                                                                            ? asset('images/employee/'.$emp_image) 
                                                                                            : $defaultImage;
                                                                                    @endphp 
                                                                                    <!-- Employee Image -->
                                                                                    <img 
                                                                                        class="media-object rounded-circle" 
                                                                                        src="{{ $employeeImage }}" 
                                                                                        alt="Avatar" 
                                                                                        height="30" 
                                                                                        width="30"
                                                                                         data-employee-id="{{ $product->current_employee }}" 
                                                                                         data-product-id="{{ $product->product_id }}" 
                                                                                         data-new-lead-id="{{ $item->id }}" 
                                                                                         data-toggle="modal" data-target="#addEmployee"
                                                                                    >
                                                                                    
                                                                                    <span class="@if($status=='accept') status-b @elseif($status=='reject') status-c @else status-a @endif"></span>
                                                                                    
                                                                                    <!-- Circular Text -->
                                                                                    <div class="circle-container" 
                                                                                        data-toggle="popover" 
                                                                                       data-content="{{ $item->name }} {{ $item->lastname }} ist am {{ $service }}  
                                                                                                        für {{ $product->article_group }} interessiert und der Verantwortliche ist: 
                                                                                                        {{ $name && $lastname ? $name . ' ' . $lastname : 'Nicht zugewiesen' }} und die Antwort ist:   {{ $reason ? $reason : 'Nicht deklariert'  }} "
                                                                                        data-trigger="hover" 
                                                                                        data-original-title="{{ $product->article_group }}"
                                                                                    >
                                                                                        <svg viewBox="0 0 100 100" class="circle-svg">
                                                                                            <defs>
                                                                                                <path id="circlePath-{{ $loop->index }}" d="M50,50 m-40,0 a40,40 0 1,1 80,0 a40,40 0 1,1 -80,0" />
                                                                                            </defs>
                                                                                            <text>
                                                                                                <textPath xlink:href="#circlePath-{{ $loop->index }}" startOffset="50%" text-anchor="middle">
                                                                                                    {{ $services[$product->service] ?? $product->service }}
                                                                                                </textPath>
                                                                                            </text>
                                                                                        </svg>

                                                                                        <!-- Icon in the Center -->
                                                                                        <div class="circle-icon">
                                                                                            <span class="icon">{{ $product->initial }}</span>
                                                                                        </div>
                                                                                    </div>
                                                                                </li>  
                                                                            </ul>

                                                                        @endif
                                                                    @endforeach

                                                                      
                                                                        <div class="modal fade text-left" id="addEmployee" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header bg-primary white">
                                                                                        <h5 class="modal-title" id="myModalLabel160">Wählen Sie Mitarbeiter aus</h5>
                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                            <span aria-hidden="true">×</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <select id="employeeSelect" name="employees"  style="width: 100%;">
                                                                                            <!-- Options will be populated via AJAX -->
                                                                                        </select>  
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" id="saveEmployees" class="btn btn-primary waves-effect waves-light">Speichern</button>
                                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Absagen</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div> 

                                                                    </div>
                                                                </td> 
                                                             
                                                                <style>
                                                                    #danger_1 .popover-header {
                                                                        background-color: #ff0000 !important;
                                                                    }
                                                                </style>
                                                                <td style="width: 20px;">
                                                                    @php
                                                                        if($item->answered_number == 0) {
                                                                            $answered_number = "0";
                                                                        } 
                                                                        else {
                                                                            $answered_number = $item->answered_number;
                                                                        }
                                                                        if($answered_number){
                                                                            $percent = ($answered_number * 100) / $item->total_number;
                                                                        }
                                                                        else{
                                                                            $percent = 0;
                                                                        }

                                                                    @endphp
                                                                    @if($item->status_msg=="QUALIFIZIERT")  
                                                                    <div class="image d-flex">
                                                                        <div class="image">
                                                                              <img src="{{ asset('images/icons/ampel-gruen.svg') }}" alt="Icon"  style="width:20px"
                                                                                data-content="DIE ANFRAGE IST BEREIT ZU QUALIFIZIEREN" 
                                                                                data-trigger="hover" 
                                                                                data-original-title="QUALIFIZIERT"> 
                                                                        </div>
                                                                        <div class="percent ml-1">
                                                                              <small><span class="black">{{$item->answered_number}}</span> / <span class="primary">{{ $item->total_number}}</span> </small>  
                                                                                <small><span class="black">{{number_format($percent, 2, '.', ',');}}%</span> </small>
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
                                                                    <div class="percent ml-1">
                                                                        <small><span class="black">{{$item->answered_number}}</span> / <span class="primary">{{ $item->total_number}}</span> </small>  
                                                                                    <small><span class="black">{{number_format($percent, 2, '.', ',');}}%</span> </small>
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
                                                                    <div class="percent ml-1">
                                                                        <small><span class="black">Junk Lead</span> </small>  
                                                                        <small><span class="black">{{$item->answered_number}}</span> / <span class="primary">{{ $item->total_number}}</span> </small>  
                                                                                    <small><span class="black">{{number_format($percent, 2, '.', ',');}}%</span> </small>
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
                                                                            <div class="percent ml-1">
                                                                                <small><span class="black">{{$item->answered_number}}</span> / <span class="black">{{ $item->total_number}}</span> </small> 
                                                                                <small><span class="black">{{number_format($percent, 2, '.', ',');}}%</span> </small>
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

                                                                <div class="btn-group dropup dropdown-icon-wrapper mr-1 mb-1"> 
                                                                    <button type="button" class="btn   dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                        <i class="feather icon-menu dropdown-icon"></i>
                                                                    </button>
                                                                    <div class="dropdown-menu">

                                                                        @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_update', '=', 'on')->first())
                                                                        <span class="dropdown-item">
                                                                        <a  href="{{ url('/new_lead_edit/'.$item->id.'/'.$item->postcode.'/'.$item->address_no)}}" ><i class="feather icon-user" ></i> Verantwortlicher bearbeiten</a> 
                                                                        </span>
                                                                        @endif
                                                                        @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_update', '=', 'on')->first())
                                                                        <span class="dropdown-item">
                                                                        <a  href="{{ url('/new_lead_edit/'.$item->id.'/'.$item->address_no)}}" ><i class="feather icon-edit" ></i> Bearbeiten</a> 
                                                                        </span>
                                                                        @endif
                                                                        @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_delete', '=', 'on')->first())
                                                                            <span class="dropdown-item danger">
                                                                                <a data-toggle="modal" data-target="#delete-pro{{$item->id}}"><i class="feather icon-trash-2 danger" ></i>Löschen</a>
                                                                            </span>
                                                                        @endif

                                                                        @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_delete', '=', 'on')->first())
                                                                            @if($item->status!="Junk")
                                                                            <span class="dropdown-item danger">
                                                                                <a data-toggle="modal" data-target="#junk{{$item->id}}"><i class="fa fa-power-off danger " ></i> Junk</a>
                                                                            </span>
                                                                            @else
                                                                             <span class="dropdown-item danger">
                                                                                <a data-toggle="modal" data-target="#unjunk{{$item->id}}"><i class="fa fa-power-off primary" ></i> Unjunk</a>
                                                                            </span>
                                                                            @endif
                                                                        @endif
                                                        
                                                                    </div>
                                                                </div>

                                                                
                                                                    <!-- Delete Modal -->
                                                                    <div class="modal fade" id="delete-pro{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header bg-danger white">
                                                                                    <h5 class="modal-title" id="myModalLabel120">Daten Löschen</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">×</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <h5>Aufzeichnung löschen</h5>
                                                                                    <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                                    <p>Die Datensatznummer lautet:{{$item->id}}. {{ $item->name }} {{ $item->lastname }} </p>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <a type="button" href="{{url('/new_lead_delete').'/'.$item->id}}" class="btn btn-danger">Ja</a>
                                                                                </div>
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
                                                                    <div class="modal fade" id="unjunk{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
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
                                                                    </div>  
                                                                </td> 
                                                                <td> 
                                                                 @php
                                                                    $current_status = null; 
                                                                    foreach ($current_request as $temp_re) {
                                                                        if ($temp_re->customer_id == $item->id) {
                                                                            $current_status = $temp_re->res_status;
                                                                            break; // Stop the loop once a match is found
                                                                        }
                                                                    }
 
                                                                @endphp
  
                                                                 
                                                                        <button type="button" 
                                                                                class="btn btn-outline-success round mr-1 mb-1 waves-effect waves-light"  
                                                                                data-toggle="modal" 
                                                                                data-target="#accept{{$item->id}}">
                                                                            Antwort
                                                                        </button>
                                                         

                                                                    <div class="modal fade text-left" id="accept{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable " role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header bg-primary white">
                                                                                    <h5 class="modal-title" id="myModalLabel160">Stellenanfrage</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">×</span>
                                                                                    </button>
                                                                                </div>
                                                                                <form action="{{ route('accept.lead')}}" method="post">
                                                                                    @csrf 
                                                                                <div class="modal-body">
                                                                                    <p><i class="feather icon-info warning"></i> Sie wurden als Verantwortlicher für den folgenden Kunden ausgewählt</p>
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
                                                                                                    @foreach ($current_request as $request) 
                                                                                                    @if($request->customer_id == $item->id && $request->current_employee == auth()->user()->name) 
                                                                                                    <td>{{ $request->article_group }}</td>
                                                                                                    <input type="hidden" name="product_id" value="{{ $request->product_id }}"> 
                                                                                                    @endif
                                                                                                     @endforeach
                                                                                                    <td>{{ $item->street }} {{$item->postcode}}, {{$item->city}}</td>
                                                                                                </tr> 
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                  <div class="row">
                                                                                    <input type="hidden" name="customer_id" value="{{ $item->id }}">
                                                                                    <input type="hidden" name="employee_id" value="{{ auth()->user()->name }}">
                                                                                   <div class="col-xl-12 col-md-12 col-12 mb-1">
                                                                                        <fieldset class="form-group">
                                                                                            <label for="basicInput">Antwort anfordern</label>
                                                                                         <select name="response" id="" class="form-control">
                                                                                            <option value="accept">Akzeptieren</option>
                                                                                            <option value="reject">Ablehnen</option>
                                                                                         </select>
                                                                                        </fieldset>
                                                                                    </div> 
                                                                                    <div class="col-xl-12 col-md-12 col-12 mb-1">
                                                                                        <fieldset class="form-group">
                                                                                            <label for="basicInput">Notiz</label>
                                                                                            <textarea name="reason" class="form-control" id="" cols="30" rows="10"></textarea>
                                                                                        </fieldset>
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
                                                                    
                                                                </td>
                                                            </tr>  
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        {{$data->links()}}
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
<!-- END: Content-->
@endsection
 
@section('script')  
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
    $(document).ready(function() {
    let selectedEmployees = [];
    let newLeadId = null;
    let productId = null;

    // Open the modal and load employees
    $('[data-target="#addEmployee"]').on('click', function() {
        newLeadId = $(this).data('new-lead-id');
        productId = $(this).data('product-id');
        
        // Fetch available employees
        $.ajax({
            url: '/checkEmployeeAvailability',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                product_id: productId
            },
            success: function(response) {
                // Clear previous options
                $('#employeeSelect').empty();
                
                // Populate select2 with available employees or in case employees
                let employees = response.availableEmployees.length > 0 ? response.availableEmployees : response.inCaseEmployees;
                
                if (employees.length > 0) {
                    employees.forEach(employee => {
                        $('#employeeSelect').append(new Option(employee.name + ' ' + employee.lastname, employee.id));
                    });
                } else {
                    toastr.warning('No employees found for this product.');
                }

                // Initialize or refresh select2
                $('#employeeSelect').select2();
            }
        });
    });

    // Save selected employees
    $('#saveEmployees').on('click', function() {
        selectedEmployees = $('#employeeSelect').val();
        if (selectedEmployees.length > 0) {
            // Send the selected employees to the server
            $.ajax({
                url: '/saveSelectedEmployees',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    new_lead_id: newLeadId,
                    product_id: productId,
                    employee_ids: selectedEmployees
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Employees saved successfully.');
                        $('#addEmployee').modal('hide');
                    } else {
                        toastr.error(response.message || 'An error occurred.');
                    }
                },
                error: function() {
                    toastr.error('An error occurred while saving employees.');
                }
            });
        } else {
            toastr.warning('Please select at least one employee.');
        }
    });
});

 </script>
@endsection
