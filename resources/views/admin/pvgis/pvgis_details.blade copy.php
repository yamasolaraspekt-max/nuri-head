@extends('admin.layouts.app')

@section('title') PV System Data Overview @endsection
@section('style')
<!-- Include stylesheet -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

<style>
    body {
        margin: 0;
    }

    .sb-title {
        position: relative;
        top: -12px;
        font-family: Roboto, sans-serif;
        font-weight: 500;
    }

    .sb-title-icon {
        position: relative;
        top: -5px;
    }

    .card-container {
        display: flex;
        height: 500px;
        width: 600px;
    }

    .panel {
        background: white;
        width: 300px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-around;
    }

    .half-input-container {
        display: flex;
        justify-content: space-between;
    }

    .half-input {
        max-width: 120px;
    }

    .map {
        width: 300px;
    }

    h2 {
        margin: 0;
        font-family: Roboto, sans-serif;
    }

    input {
        height: 30px;
    }

    input {
        height: 30px;
        border: 0;
        border-bottom: 1px solid black;
        font-size: 14px;
        font-family: Roboto, sans-serif;
        font-style: normal;
        font-weight: normal;
    }

    input:focus::placeholder {
        color: white;
    }

    .category p {
    margin-bottom: 10px; /* Adds space between paragraphs */
    font-size: 14px; /* Sets the font size */
    }
    .category strong {
    color: #0056b3; /* Changes the color of the category titles */
    font-size: 16px; /* Sets a larger font size for the titles */
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
                        <h2 class="content-header-title float-left mb-0">PV System Data Overview</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('customer_details') }}">Kunden</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#">Neu</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="content-body">
            <!-- Basic Horizontal form layout section start -->
            <section id="basic-horizontal-layouts">
                <div class="row match-height">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>BREITENGRAD</th>
                                                            <td>{{ $customer->lat }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>LÄNGENGRAD</th>
                                                            <td>{{ $customer->lon }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>HÖHE ÜBER NN</th>
                                                            <td>{{ $customer->elevation }} Meters</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>


                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        @php
                        $category = [
                        'Kategorie 0' => ['description' => 'Meer, Küstengebiete, die dem offenen Meer ausgesetzt sind'],
                        'Kategorie I' => ['description' => 'Seen oder Gebiete mit niedriger Vegetation und ohne Hindernisse'],
                        'Kategorie II' => ['description' => 'Gebiete mit niedriger Vegetation wie Gras und einzelnen Hindernissen (Bäume,
                        Gebäude) mit Abständen von mindestens dem 20-fachen der Höhe des Hindernisses.'],
                        'Kategorie III' => ['description' => 'Gebiete mit gleichmäßiger Vegetation oder Bebauung oder mit einzelnen Objekten mit
                        Abständen von weniger als dem 20-fachen der Hindernishöhe (z.B. Dörfer, Vorstadtentwicklung, bewaldete Gebiete).'],
                        'Kategorie IV' => ['description' => 'Gebiete, in denen mindestens 15% der Oberfläche mit Gebäuden bedeckt sind, deren
                        durchschnittliche Höhe 15 m beträgt.'],
                        ];
                        
                        $categoryString = implode('', array_map(function ($key, $value) {
                        return "<p><strong>$key:</strong> {$value['description']}</p>";
                        }, array_keys($category), $category));
                        @endphp
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>WINDLASTZONE</th>
                                                            <td>3</td>
                                                        </tr>
                                                        <tr>
                                                            <th>SCHNELLLATZONE</th>
                                                            <td>3</td>
                                                        </tr>
                                                        <div class="modal-info mr-1 mb-1 d-inline-block">
                                                           
                                                        <tr>
                                                            <th>GELÄNDEKATEGORIE  <i class="feather icon-info category" data-toggle="popover"
                                                                    data-content="{!! $categoryString !!}"
                                                                    data-trigger="click" data-placement="right" data-original-title="GELÄNDEKATEGORIE"></i></th> 
                                                             
                                                    
                                                            <td>
                                                                <ul class="list-unstyled mb-0">
                                                                    <li class="d-inline-block mr-2">
                                                                        <fieldset>
                                                                            <label>
                                                                                <input type="radio" name="radio"
                                                                                    checked="">
                                                                                1
                                                                            </label>
                                                                        </fieldset>
                                                                    </li>
                                                                    <li class="d-inline-block mr-2">
                                                                        <fieldset>
                                                                            <label>
                                                                                <input type="radio" name="radio">
                                                                                2
                                                                            </label>
                                                                        </fieldset>
                                                                    </li>
                                                                    <li class="d-inline-block mr-2">
                                                                        <fieldset>
                                                                            <label>
                                                                                <input type="radio" name="radio">
                                                                                3
                                                                            </label>
                                                                        </fieldset>
                                                                    </li>
                                                                    <li class="d-inline-block mr-2">
                                                                        <fieldset>
                                                                            <label>
                                                                                <input type="radio" name="radio">
                                                                                4
                                                                            </label>
                                                                        </fieldset>
                                                                    </li>


                                                                </ul>
                                                            </td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-6">
                        <section id="nav-filled">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card overflow-hidden">
                                        <div class="card-header">
                                            <h4 class="card-title">Filled</h4>
                                        </div>
                                        <div class="card-content">
                                            <div class="card-body"> 
                                                <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" id="home-tab-fill" data-toggle="tab"
                                                            href="#home-fill" role="tab" aria-controls="home-fill"
                                                            aria-selected="true">Monthly Data</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="profile-tab-fill" data-toggle="tab"
                                                            href="#profile-fill" role="tab" aria-controls="profile-fill"
                                                            aria-selected="false">Total Annual Data</a>
                                                    </li>
                                                </ul>

                                                <!-- Tab panes -->
                                                <div class="tab-content pt-1">
                                                    <div class="tab-pane active" id="home-fill" role="tabpanel"
                                                        aria-labelledby="home-tab-fill">
                                                        <table class="table table-hover mb-0">
                                                            <thead>
                                                                <tr>
                                                                  <th>Monat</th>
                                                                <th>Tägliche Energie (E_d) [kWh/d]</th>
                                                                <th>Monatliche Energie (E_m) [kWh/Monat]</th>
                                                                <th>Tägliche Bestrahlungsstärke (H(i)_d) [kWh/m²/d]</th>
                                                                <th>Monatliche Bestrahlungsstärke (H(i)_m) [kWh/m²/Monat]</th>
                                                                <th>Standardabweichung (SD_m) [kWh]</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($data['outputs']['monthly']['fixed'] as $month)
                                                                <tr>
                                                              <td>{{ $germanMonths[$month['month']] }}</td>
                                                                    <td>{{ $month['E_d'] }}</td>
                                                                    <td>{{ $month['E_m'] }}</td>
                                                                    <td>{{ $month['H(i)_d'] }}</td>
                                                                    <td>{{ $month['H(i)_m'] }}</td>
                                                                    <td>{{ $month['SD_m'] }}</td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="tab-pane" id="profile-fill" role="tabpanel"
                                                        aria-labelledby="profile-tab-fill">
                                                        <table class="table table-hover mb-0">
                                                            <thead>
                                                                <tr>
                                                                  <th>Tägliche Energie (E_d) [kWh/d]</th>
                                                                <th>Monatliche Energie (E_m) [kWh/Monat]</th>
                                                                <th>Jährliche Energie (E_y) [kWh/Jahr]</th>
                                                                    <!-- Add other headers for additional data -->
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>{{ $data['outputs']['totals']['fixed']['E_d'] }}
                                                                    </td>
                                                                    <td>{{ $data['outputs']['totals']['fixed']['E_m'] }}
                                                                    </td>
                                                                    <td>{{ $data['outputs']['totals']['fixed']['E_y'] }}
                                                                    </td>
                                                                    <!-- Add other data similarly -->
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    {{-- Weather Information --}}
                    <div class="col-md-6">
                        <section id="nav-filled">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card overflow-hidden">
                                        <div class="card-header">
                                            <h4 class="card-title">Filled</h4>
                                        </div>
                                        <div class="card-content">
                                            <div class="card-body">
                                                <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" id="home-tab-fill" data-toggle="tab" href="#month"
                                                            role="tab" aria-controls="home-fill" aria-selected="true">Weather Info</a>
                                                    </li> 
                                                </ul>
                    
                                                <!-- Tab panes -->
                                                <div class="tab-content pt-1">
                                                    <div class="tab-pane active" id="month" role="tabpanel" aria-labelledby="home-tab-fill">
                                                        <table class="table table-hover mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Date/Time (Local)</th>
                                                                    <th>Temperature (°C)</th>
                                                                    <th>Apparent Temperature (°C)</th>
                                                                    <th>Wind Speed (m/s)</th>
                                                                    <th>Wind Direction</th>
                                                                    <th>Cloud Cover (%)</th>
                                                                    <th>Visibility (km)</th>
                                                                    <th>Precipitation (mm)</th>
                                                                    <th>Weather Description</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                    
                                                                @foreach ($weatherman as $entry)
                                                                <tr>
                                                                    <td>{{ \Carbon\Carbon::parse($entry['timestamp_local'])->isoFormat('D.MMM.YYYY')}}</td>
                                                                    <td>{{ $entry['temp'] }}</td>
                                                                    <td>{{ $entry['app_temp'] }}</td>
                                                                    <td>{{ $entry['wind_spd'] }}</td>
                                                                    <td>{{ $entry['wind_cdir_full'] }}</td>
                                                                    <td>{{ $entry['clouds'] }}</td>
                                                                    <td>{{ $entry['vis'] }}</td>
                                                                    <td>{{ $entry['precip'] }}</td>
                                                                    <td>{{ $entry['weather']['description'] }}</td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                   
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    {{-- Weather Information End --}}
 

                    <div class="col-md-6">
                        <section id="nav-filled">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card overflow-hidden">
                                        <div class="card-header">
                                            <h4 class="card-title">Filled</h4>
                                        </div>
                                        <div class="card-content">
                                            <div class="card-body">
                                                <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" id="home-tab-fill" data-toggle="tab" href="#month"
                                                            role="tab" aria-controls="home-fill" aria-selected="true">Monthly Data</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="profile-tab-fill" data-toggle="tab" href="#weather"
                                                            role="tab" aria-controls="profile-fill" aria-selected="false">Total Annual
                                                            Data</a>
                                                    </li>
                                                </ul>
                    
                                                <!-- Tab panes -->
                                                <div class="tab-content pt-1">
                                                    <div class="tab-pane active" id="month" role="tabpanel"
                                                        aria-labelledby="home-tab-fill">
                                                        <table class="table table-hover mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Monat</th>
                                                                    <th>HORIZONTAL GLOBAL STRAHLUNG(WH/M2/TAG)</th>
                                                                    <th>MITLERS TAGES TEMPERATUR(°C)</th>
                                                                    <th>MIN. TAGS TEMPERATUR (°C)</th>
                                                                    <th>MAX. TAGS TEMPERATUR (°C)</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                        
                                                                <tr>
                                                                    <td>{{ $temperature->date }}</td>
                                                                    <td>{{ $temperature->outside_temp }}</td>
                                                                    <td>{{ $averageTemperature }} °C</td>
                                                                    <td>{{ $averageTemperature }} °C</td>
                                                                    <td>{{ $averageTemperature }} °C</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="tab-pane" id="weather" role="tabpanel"
                                                        aria-labelledby="profile-tab-fill">
                                                       <canvas id="weatherChart" width="800" height="400"></canvas>
                                                       <script>
                                                        var ctx = document.getElementById('weatherChart').getContext('2d');
                                                        var startTimestamp = 1714996800000; // Example start time in milliseconds
                                                    
                                                        var weatherChart = new Chart(ctx, {
                                                            type: 'line',
                                                            data: {
                                                                labels: Array.from({length: 24}, (_, i) => new Date(startTimestamp + i * 3600000).toLocaleTimeString()), // Hourly labels
                                                                datasets: [{
                                                                    label: 'Temperature (°F)',
                                                                    data: [150, 136, 114, 109, 152, 149, 102, 109, 164, 155, 95, 95, 100, 100, 185, 185, 173, 173, 104, 104, 109, 109, 193, 193],
                                                                    borderColor: 'red',
                                                                    yAxisID: 'y',
                                                                }, {
                                                                    label: 'Wind Speed (mph)',
                                                                    data: [74, 93, 55, 55, 93, 111, 93, 74, 130, 130, 93, 93, 55, 55, 93, 93, 111, 111, 93, 93, 74, 74, 111, 111],
                                                                    borderColor: 'blue',
                                                                    yAxisID: 'y',
                                                                }, {
                                                                    label: 'Precipitation (mm)',
                                                                    data: [70, 38, 11, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                                                                    borderColor: 'green',
                                                                    type: 'bar',
                                                                    yAxisID: 'y1'
                                                                }]
                                                            },
                                                            options: {
                                                                scales: {
                                                                    y: {
                                                                        type: 'linear',
                                                                        display: true,
                                                                        position: 'left',
                                                                    },
                                                                    y1: {
                                                                        type: 'linear',
                                                                        display: true,
                                                                        position: 'right',
                                                                        grid: {
                                                                            drawOnChartArea: false, // only draw grid where the secondary y-axis is
                                                                        },
                                                                    }
                                                                }
                                                            }
                                                        });
                                                    </script>
                                                    </div>
                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
            <!-- // Basic Horizontal form layout section end --> 

        </div>
    </div>
</div>
<!-- END: Content-->

@endsection

@section('script')


<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/popover/popover.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


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
    $(document).ready(function(){
                                                        $('.category').popover({
                                                            html: true
                                                        });
                                                    });
</script>




@endsection