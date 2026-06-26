@extends('admin.layouts.app')

@section('title') PV-PRODUKT @endsection

@section('style')
<!-- Include stylesheet -->
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">
<style>
    .is-invalid {
        border-color: #dc3545;
    }

    .text-danger {
        color: #dc3545;
        margin-top: 0.25rem;
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
            <div class="content-header-left col-md-9 col-6 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-6">
                        <h2 class="content-header-title float-left mb-0">PRODUCT</h2>
                        <div class="breadcrumb-wrapper col-6">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('product_details/'.request()->id) }}">MODULE KONFIGURATION</a></li>
                                <li class="breadcrumb-item"><a href="#">Neu</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
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
        <div class="content-body">
            <div class="col-xl-12 text-center" style="display: flex; flex-wrap: nowrap; justify-content: center;">
                <div class="col-6">
                    <div class="cards">
                        <div class="card-header">
                            <h4>Wählen Sie die Konfiguration</h4>
                        </div>
                        <div class="card-body">
                            <fieldset class="form-group">
                                <select class="form-control" id="modules">
                                    <option value="pv_modules">PV-Module</option>
                                    <option value="batteries">Batterien</option>
                                    <option value="battery_systems">Batteriesysteme (Netzgekoppelt)</option>
                                    <option value="battery_inverters">Batteriewechselrichter (Netzautark)</option>
                                    <option value="electric_vehices">Elektrofahrzeuge</option>
                                    <option value="power_optimizers">Leistungsoptimierer</option>
                                    <option value="inverters">Wechselrichter</option>
                                    <option value="backup_generators">Zusatzgeneratoren</option>
                                </select>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div> 

            <div class="col-xl-12 col-lg-12"  id="pv_modules">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <h4 class="card-title">Technische Daten <code>PV-Module</code></h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="nav-vertical">
                                <ul class="nav nav-tabs nav-left flex-column" role="tablist" style="height: 56px;">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="pv_tab1" data-toggle="tab" aria-controls="pvLeft1" href="#pvLeft1" role="tab" aria-selected="true">Elektrische Daten</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pv_tab2" data-toggle="tab" aria-controls="pvLeft2" href="#pvLeft2" role="tab" aria-selected="false">UI Kennwerte bei STC</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pv_tab3" data-toggle="tab" aria-controls="pvLeft3" href="#pvLeft3" role="tab" aria-selected="false">UI Kennwerte bei Schwachlicht</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pv_tab4" data-toggle="tab" aria-controls="pvLeft4" href="#pvLeft4" role="tab" aria-selected="false">Weitere Parameter</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pv_tab5" data-toggle="tab" aria-controls="pvLeft5" href="#pvLeft5" role="tab" aria-selected="false">Abmessungen</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <form method="post"  id="pv-form" >
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ request()->id }}"> 
                                        <input type="hidden" name="article_group_id" value="{{ request()->article }}"> 

                                        <div class="tab-pane active" id="pvLeft1" role="tabpanel" aria-labelledby="pv_tab1">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="cell_type">Zelltyp</label>
                                                        <input type="text" class="form-control" name="cell_type"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="half_cell_module">Halbzellen-Modul</label>
                                                        <input type="checkbox" name="half_cell_module">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="num_cells">Anzahl Zellen</label>
                                                        <input type="number" class="form-control" name="num_cells"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="num_bypass_diodes">Anzahl Bypassdioden</label>
                                                        <input type="number" class="form-control" name="num_bypass_diodes" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="voltage_loss_per_bypass_diode">Verlustspannung pro Bypassdiode in V</label>
                                                        <input type="number" class="form-control" name="voltage_loss_per_bypass_diode" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="integrated_power_optimizer">Integrierter Leistungsoptimierer</label>
                                                        <input type="text" class="form-control" name="integrated_power_optimizer"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="trafo_inverter_only">Nur Trafo-Wechselrichter geeignet</label>
                                                        <input type="checkbox" name="trafo_inverter_only">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="cell_strands_vertical">Zellstränge senkrecht zur kurzen Seite</label>
                                                        <input type="checkbox" name="cell_strands_vertical" checked>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="pvLeft2" role="tabpanel" aria-labelledby="pv_tab2">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="mpp_voltage">Spannung im MPP in V</label>
                                                            <input type="number" class="form-control" name="mpp_voltage"  >
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="mpp_current">Strom im MPP in A</label>
                                                            <input type="number" class="form-control" name="mpp_current"  >
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="open_circuit_voltage">Leerlaufspannung in V</label>
                                                            <input type="number" class="form-control" name="open_circuit_voltage"  >
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="short_circuit_current">Kurzschlussstrom in A</label>
                                                            <input type="number" class="form-control" name="short_circuit_current" >
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="voltage_increase_before_stabilization">Erhöhung Leerlaufspannung vor Stabilisierung in %</label>
                                                            <input type="number" class="form-control" name="voltage_increase_before_stabilization"  >
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="nominal_power">Nennleistung in W</label>
                                                            <input type="number" class="form-control" name="nominal_power"  >
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="fill_factor">Füllfaktor in %</label>
                                                            <input type="number" class="form-control" name="fill_factor"  >
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="efficiency">Wirkungsgrad in %</label>
                                                            <input type="number" class="form-control" name="efficiency"  >
                                                        </div>
                                                    </div> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="pvLeft3" role="tabpanel" aria-labelledby="pv_tab3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="low_light_model">Modell</label>
                                                        <input type="text" class="form-control" name="low_light_model"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="irradiance">Einstrahlung (Schwachlicht) in W/m²</label>
                                                        <input type="number" class="form-control" name="irradiance"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="mpp_voltage_low_light">MPP-Spannung (Schwachlicht) in V</label>
                                                        <input type="number" class="form-control" name="mpp_voltage_low_light" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="mpp_current_low_light">MPP-Strom (Schwachlicht) in A</label>
                                                        <input type="number" class="form-control" name="mpp_current_low_light" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="open_circuit_voltage_low_light">Leerlaufspannung (Schwachlicht) in V</label>
                                                        <input type="number" class="form-control" name="open_circuit_voltage_low_light"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="short_circuit_current_low_light">Kurzschlussstrom (Schwachlicht) in A</label>
                                                        <input type="number" class="form-control" name="short_circuit_current_low_light" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="fill_factor_low_light">Füllfaktor (Schwachlicht) in %</label>
                                                        <input type="number" class="form-control" name="fill_factor_low_light"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="efficiency_low_light">Rel. Wirkungsgrad (Schwachlicht) in %</label>
                                                        <input type="number" class="form-control" name="efficiency_low_light" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="standard_low_light_behavior">Standard Schwachlichtverhalten</label>
                                                        <input type="checkbox" name="standard_low_light_behavior" checked>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="pvLeft4" role="tabpanel" aria-labelledby="pv_tab4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="temperature_coefficient_voc">Temperaturkoeffizient Uoc in mV/K</label>
                                                        <input type="number" class="form-control" name="temperature_coefficient_voc"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="temperature_coefficient_voc_pct">Temperaturkoeffizient Uoc in %/K</label>
                                                        <input type="number" class="form-control" name="temperature_coefficient_voc_pct"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="temperature_coefficient_isc">Temperaturkoeffizient Isc in mA/K</label>
                                                        <input type="number" class="form-control" name="temperature_coefficient_isc" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="temperature_coefficient_isc_pct">Temperaturkoeffizient Isc in %/K</label>
                                                        <input type="number" class="form-control" name="temperature_coefficient_isc_pct"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="temperature_coefficient_pmax">Temperaturkoeffizient Pmpp in %/K</label>
                                                        <input type="number" class="form-control" name="temperature_coefficient_pmax"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="angle_correction_factor">Winkelkorrekturfaktor (IAM) in %</label>
                                                        <input type="number" class="form-control" name="angle_correction_factor"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="max_system_voltage">Maximale Systemsprannung in V</label>
                                                        <input type="number" class="form-control" name="max_system_voltage"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="bifaciality_factor">Bifazialitätsfaktor in %</label>
                                                        <input type="number" class="form-control" name="bifaciality_factor">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="pvLeft5" role="tabpanel" aria-labelledby="pv_tab5">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="width">Breite in mm</label>
                                                        <input type="number" class="form-control" name="width"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="height">Höhe in mm</label>
                                                        <input type="number" class="form-control" name="height"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="area">Fläche in m²</label>
                                                        <input type="number" class="form-control" name="area"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="depth">Tiefe in mm</label>
                                                        <input type="number" class="form-control" name="depth" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="frame_width">Rahmenbreite in mm</label>
                                                        <input type="number" class="form-control" name="frame_width"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="weight">Gewicht in kg</label>
                                                        <input type="number" class="form-control" name="weight"  >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 float-right">
                                            <button type="submit" class="btn btn-primary">Absenden</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-12"   id="batteries"> 
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <h4 class="card-title">Battery Configuration</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="nav-vertical">
                                <ul class="nav nav-tabs nav-left flex-column" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="tab_battery1" data-toggle="tab" aria-controls="tab1_batteryContent" href="#tab1_batteryContent" role="tab" aria-selected="true">Basic Data</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab_battery2" data-toggle="tab" aria-controls="tab2_batteryContent" href="#tab2_batteryContent" role="tab" aria-selected="false">Electrical Data</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab_battery3" data-toggle="tab" aria-controls="tab3_batteryContent" href="#tab3_batteryContent" role="tab" aria-selected="false">Discharge Cycles</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab_battery4" data-toggle="tab" aria-controls="tab4_batteryContent" href="#tab4_batteryContent" role="tab" aria-selected="false">Capacity Curve</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab_battery5" data-toggle="tab" aria-controls="tab5_batteryContent" href="#tab5_batteryContent" role="tab" aria-selected="false">Mechanical Data</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                     <form id="battery-form">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ request()->id }}">
                                        <input type="hidden" name="article_group_id" value="{{ request()->article }}">

                                        <div class="tab-pane active" id="tab1_batteryContent" role="tabpanel" aria-labelledby="tab_battery1">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="company">Company</label>
                                                        <input type="text" class="form-control" name="company" value="Example">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name">Name</label>
                                                        <input type="text" class="form-control" name="name" value="12 V - 109 Ah - Pb valve regulated">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="description">Description</label>
                                                        <textarea class="form-control" name="description"></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="available">Available</label>
                                                        <input type="checkbox" name="available" checked>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="version">Version</label>
                                                        <input type="text" class="form-control" name="version" value="2">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="created_at">Created At</label>
                                                        <input type="text" class="form-control" name="created_at" value="1/24/2014 1:00:00 AM">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="user_id">User ID</label>
                                                        <input type="text" class="form-control" name="user_id" value="Keine - Systemdatensatz">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab2_batteryContent" role="tabpanel" aria-labelledby="tab_battery2">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="battery_type">Battery Type</label>
                                                        <input type="text" class="form-control" name="battery_type" value="Blei-Säure - Verschlossen (Gel)">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="cell_voltage">Cell Voltage (V)</label>
                                                        <input type="number" class="form-control" name="cell_voltage" value="2">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="num_cells">Number of Cells in Series</label>
                                                        <input type="number" class="form-control" name="num_cells" value="6">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="nominal_voltage">Resulting Nominal Voltage (V)</label>
                                                        <input type="number" class="form-control" name="nominal_voltage" value="12">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="num_strings">Number of Strings</label>
                                                        <input type="number" class="form-control" name="num_strings" value="2">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="internal_resistance">Internal Resistance (mOhm)</label>
                                                        <input type="number" class="form-control" name="internal_resistance" value="11">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="self_discharge">Self-Discharge (%/Month)</label>
                                                        <input type="number" class="form-control" name="self_discharge" value="3">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab3_batteryContent" role="tabpanel" aria-labelledby="tab_battery3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="dod_20">Depth of Discharge (DoD) 20%</label>
                                                        <input type="number" class="form-control" name="dod_20" value="8164">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="dod_40">Depth of Discharge (DoD) 40%</label>
                                                        <input type="number" class="form-control" name="dod_40" value="3700">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="dod_60">Depth of Discharge (DoD) 60%</label>
                                                        <input type="number" class="form-control" name="dod_60" value="2213">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="dod_80">Depth of Discharge (DoD) 80%</label>
                                                        <input type="number" class="form-control" name="dod_80" value="1470">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab4_batteryContent" role="tabpanel" aria-labelledby="tab_battery4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="capacity_10min">Discharge Duration 10 min (Ah)</label>
                                                        <input type="number" class="form-control" name="capacity_10min" value="22">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="capacity_30min">Discharge Duration 30 min (Ah)</label>
                                                        <input type="number" class="form-control" name="capacity_30min" value="47">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="capacity_1h">Discharge Duration 1 hr (Ah)</label>
                                                        <input type="number" class="form-control" name="capacity_1h" value="64.3">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="capacity_5h">Discharge Duration 5 hr (Ah)</label>
                                                        <input type="number" class="form-control" name="capacity_5h" value="97.5">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="capacity_10h">Discharge Duration 10 hr (Ah)</label>
                                                        <input type="number" class="form-control" name="capacity_10h" value="109">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="capacity_100h">Discharge Duration 100 hr (Ah)</label>
                                                        <input type="number" class="form-control" name="capacity_100h" value="137">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab5_batteryContent" role="tabpanel" aria-labelledby="tab_battery5">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="length">Length (mm)</label>
                                                        <input type="number" class="form-control" name="length" value="280">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="width">Width (mm)</label>
                                                        <input type="number" class="form-control" name="width" value="210">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="height">Height (mm)</label>
                                                        <input type="number" class="form-control" name="height" value="390">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="weight">Weight (kg)</label>
                                                        <input type="number" class="form-control" name="weight" value="52">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 float-right">
                                            <button type="submit" class="btn btn-primary" id="battery-submit-button">Submit</button>

                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div> 
                <!-- Content for Batteries -->
            </div>
            <div class="col-xl-12" id="battery_systems"> 
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <h4 class="card-title">Technische Daten <code>Batteriesystem</code></h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="nav-vertical">
                                <ul class="nav nav-tabs nav-left flex-column" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="tab1_energy_storage_system" data-toggle="tab" aria-controls="tab1Content_energy_storage_system" href="#tab1Content_energy_storage_system" role="tab" aria-selected="true">Basic Data</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab2_energy_storage_system" data-toggle="tab" aria-controls="tab2Content_energy_storage_system" href="#tab2Content_energy_storage_system" role="tab" aria-selected="false">Inverter Data</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab3_energy_storage_system" data-toggle="tab" aria-controls="tab3Content_energy_storage_system" href="#tab3Content_energy_storage_system" role="tab" aria-selected="false">Efficiency</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab4_energy_storage_system" data-toggle="tab" aria-controls="tab4Content_energy_storage_system" href="#tab4Content_energy_storage_system" role="tab" aria-selected="false">Charging Strategy</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab5_energy_storage_system" data-toggle="tab" aria-controls="tab5Content_energy_storage_system" href="#tab5Content_energy_storage_system" role="tab" aria-selected="false">Battery</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <form method="post"  id="battery-system-form">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ request()->id }}">
                                        <input type="hidden" name="article_group_id" value="{{ request()->article }}">

                                        <div class="tab-pane active" id="tab1Content_energy_storage_system" role="tabpanel" aria-labelledby="tab1_energy_storage_system">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="company">Company</label>
                                                        <input type="text" class="form-control" name="ess_company" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name">Name</label>
                                                        <input type="text" class="form-control" name="ess_name" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="description">Description</label>
                                                        <textarea class="form-control" name="ess_description"></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="ess_available">Available</label>
                                                        <input type="checkbox" name="ess_available" checked>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="version">Version</label>
                                                        <input type="text" class="form-control" name="ess_version" >
                                                    </div> 
                                                    <div class="form-group">
                                                        <label for="user_id">User ID</label>
                                                        <input type="text" class="form-control" name="ess_user_id" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab2Content_energy_storage_system" role="tabpanel" aria-labelledby="tab2_energy_storage_system">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="nominal_power">Nominal Power (kW)</label>
                                                        <input type="number" class="form-control" name="ess_nominal_power" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="max_charge_power">Max Charge Power (kW)</label>
                                                        <input type="number" class="form-control" name="ess_max_charge_power" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="max_discharge_power">Max Discharge Power (kW)</label>
                                                        <input type="number" class="form-control" name="ess_max_discharge_power"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="coupling_type">Coupling Type</label>
                                                        <input type="text" class="form-control" name="ess_coupling_type"  >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab3Content_energy_storage_system" role="tabpanel" aria-labelledby="tab3_energy_storage_system">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="efficiency_0">Load 0% (Efficiency %)</label>
                                                        <input type="number" class="form-control" name="ess_efficiency_0" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="efficiency_5">Load 5% (Efficiency %)</label>
                                                        <input type="number" class="form-control" name="ess_efficiency_5"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="efficiency_10">Load 10% (Efficiency %)</label>
                                                        <input type="number" class="form-control" name="ess_efficiency_10" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="efficiency_20">Load 20% (Efficiency %)</label>
                                                        <input type="number" class="form-control" name="ess_efficiency_20" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="efficiency_30">Load 30% (Efficiency %)</label>
                                                        <input type="number" class="form-control" name="ess_efficiency_30"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="efficiency_50">Load 50% (Efficiency %)</label>
                                                        <input type="number" class="form-control" name="ess_efficiency_50"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="efficiency_75">Load 75% (Efficiency %)</label>
                                                        <input type="number" class="form-control" name="ess_efficiency_75" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="efficiency_100">Load 100% (Efficiency %)</label>
                                                        <input type="number" class="form-control" name="ess_efficiency_100"  >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab4Content_energy_storage_system" role="tabpanel" aria-labelledby="tab4_energy_storage_system">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="equalization_charge">Equalization Charge Start (%)</label>
                                                        <input type="number" class="form-control" name="ess_equalization_charge"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="equalization_charge_end">Equalization Charge End (%)</label>
                                                        <input type="number" class="form-control" name="ess_equalization_charge_end"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="equalization_charge_duration">Equalization Charge Duration (hrs)</label>
                                                        <input type="number" class="form-control" name="ess_equalization_charge_duration"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="equalization_charge_cycle">Equalization Charge Cycle (days)</label>
                                                        <input type="number" class="form-control" name="ess_equalization_charge_cycle" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="full_charge">Full Charge Start (%)</label>
                                                        <input type="number" class="form-control" name="ess_full_charge" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="full_charge_end">Full Charge End (%)</label>
                                                        <input type="number" class="form-control" name="ess_full_charge_end" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="full_charge_duration">Full Charge Duration (hrs)</label>
                                                        <input type="number" class="form-control" name="ess_full_charge_duration" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="full_charge_cycle">Full Charge Cycle (days)</label>
                                                        <input type="number" class="form-control" name="ess_full_charge_cycle"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="maintenance_charge">Maintenance Charge (%)</label>
                                                        <input type="number" class="form-control" name="ess_maintenance_charge"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="uo_charge">U0 Charge Start (%)</label>
                                                        <input type="number" class="form-control" name="ess_uo_charge"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="uo_charge_end">U0 Charge End (%)</label>
                                                        <input type="number" class="form-control" name="ess_uo_charge_end" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="uo_charge_duration">U0 Charge Duration (hrs)</label>
                                                        <input type="number" class="form-control" name="ess_uo_charge_duration"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="i_charge">I Charge Start (%)</label>
                                                        <input type="number" class="form-control" name="ess_i_charge"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="i_charge_end">I Charge End (%)</label>
                                                        <input type="number" class="form-control" name="ess_i_charge_end" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab5Content_energy_storage_system" role="tabpanel" aria-labelledby="tab5_energy_storage_system">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="battery">Battery</label>
                                                        <input type="text" class="form-control" name="battery"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="num_batteries_per_string">Number of Batteries per String</label>
                                                        <input type="number" class="form-control" name="ess_num_batteries_per_string"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="num_battery_strings">Number of Battery Strings</label>
                                                        <input type="number" class="form-control" name="ess_num_battery_strings" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="system_voltage">Battery System Voltage (V)</label>
                                                        <input type="number" class="form-control" name="ess_system_voltage" value="24">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="usable_energy">Usable Battery Energy (kWh)</label>
                                                        <input type="number" class="form-control" name="ess_usable_energy" value="14.4">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="capacity_c10">Battery Capacity C10 (Ah)</label>
                                                        <input type="number" class="form-control" name="ess_capacity_c10" value="860">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 float-right">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>  
            <div class="col-xl-12 col-lg-12"   id="battery_inverters">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <h4 class="card-title">Technische Daten <code>Batterie-Wechselrichter</code></h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="nav-vertical">
                                <ul class="nav nav-tabs nav-left flex-column" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="tab1" data-toggle="tab" aria-controls="tab1Content" href="#tab1Content" role="tab" aria-selected="true">Basisdaten</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab2" data-toggle="tab" aria-controls="tab2Content" href="#tab2Content" role="tab" aria-selected="false">Elektrische Daten - AC</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab3" data-toggle="tab" aria-controls="tab3Content" href="#tab3Content" role="tab" aria-selected="false">Batteriedaten - DC</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab4" data-toggle="tab" aria-controls="tab4Content" href="#tab4Content" role="tab" aria-selected="false">Wirkungsgrad</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab5" data-toggle="tab" aria-controls="tab5Content" href="#tab5Content" role="tab" aria-selected="false">Einsatz im Inselnetz</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <form method="post" id="battery-inverter-form">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ request()->id }}">
                                        <input type="hidden" name="article_group_id" value="{{ request()->article }}">

                                        <div class="tab-pane active" id="tab1Content" role="tabpanel" aria-labelledby="tab1">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="company">Unternehmen</label>
                                                        <input type="text" class="form-control" name="company"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name">Name</label>
                                                        <input type="text" class="form-control" name="name"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="description">Beschreibung</label>
                                                        <textarea class="form-control" name="description">Off-grid inverter, MPPT charger integrated</textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="available">Lieferbar</label>
                                                        <input type="checkbox" name="available" checked>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="version">Version</label>
                                                        <input type="text" class="form-control" name="version" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="created_at">Erstellt am</label>
                                                        <input type="text" class="form-control" name="created_at"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="user_id">Benutzer-ID</label>
                                                        <input type="text" class="form-control" name="user_id"  >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab2Content" role="tabpanel" aria-labelledby="tab2">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="nominal_voltage">Nennspannung in V</label>
                                                        <input type="number" class="form-control" name="nominal_voltage"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="max_ac_current">Max. AC-Strom in A</label>
                                                        <input type="number" class="form-control" name="max_ac_current"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="continuous_power">AC-Dauerleistung bei 25°C in W</label>
                                                        <input type="number" class="form-control" name="continuous_power"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="power_30min">AC-Leistung für 30 min bei 25°C in W</label>
                                                        <input type="number" class="form-control" name="power_30min"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="power_60min">AC-Leistung für 60 min bei 25°C in W</label>
                                                        <input type="number" class="form-control" name="power_60min"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="no_load_consumption">Eigenverbrauch ohne Last in W</label>
                                                        <input type="number" class="form-control" name="no_load_consumption" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="standby_consumption">Eigenverbrauch (Standby) in W</label>
                                                        <input type="number" class="form-control" name="standby_consumption" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab3Content" role="tabpanel" aria-labelledby="tab3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="battery_voltage">Batterienennspannung in V</label>
                                                        <input type="number" class="form-control" name="battery_voltage"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="min_battery_voltage">Min. Batteriespannung in V</label>
                                                        <input type="number" class="form-control" name="min_battery_voltage"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="max_battery_voltage">Max. Batteriespannung in V</label>
                                                        <input type="number" class="form-control" name="max_battery_voltage" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="max_battery_charge_current">Max. Batterieladestrom in A</label>
                                                        <input type="number" class="form-control" name="max_battery_charge_current" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab4Content" role="tabpanel" aria-labelledby="tab4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="efficiency_0">Auslastung 0% (Wirkungsgrad in %)</label>
                                                        <input type="number" class="form-control" name="efficiency_0"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="efficiency_5">Auslastung 5% (Wirkungsgrad in %)</label>
                                                        <input type="number" class="form-control" name="efficiency_5"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="efficiency_10">Auslastung 10% (Wirkungsgrad in %)</label>
                                                        <input type="number" class="form-control" name="efficiency_10" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="efficiency_20">Auslastung 20% (Wirkungsgrad in %)</label>
                                                        <input type="number" class="form-control" name="efficiency_20"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="efficiency_30">Auslastung 30% (Wirkungsgrad in %)</label>
                                                        <input type="number" class="form-control" name="efficiency_30"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="efficiency_50">Auslastung 50% (Wirkungsgrad in %)</label>
                                                        <input type="number" class="form-control" name="efficiency_50"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="efficiency_75">Auslastung 75% (Wirkungsgrad in %)</label>
                                                        <input type="number" class="form-control" name="efficiency_75"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="efficiency_100">Auslastung 100% (Wirkungsgrad in %)</label>
                                                        <input type="number" class="form-control" name="efficiency_100">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab5Content" role="tabpanel" aria-labelledby="tab5">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="max_devices_per_phase_single">Maximale Anzahl Geräte pro Phase (einphasig)</label>
                                                        <input type="number" class="form-control" name="max_devices_per_phase_single"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="max_devices_per_phase_dual">Maximale Anzahl Geräte pro Phase (zweiphasig)</label>
                                                        <input type="number" class="form-control" name="max_devices_per_phase_dual"  >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="max_clusters">Maximale Anzahl Cluster</label>
                                                        <input type="number" class="form-control" name="max_clusters"  >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 float-right">
                                            <button type="submit" class="btn btn-primary">Absenden</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 

            <div class="col-xl-12 col-lg-12"   id="electric_vehices">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <h4 class="card-title">Technische Daten <code>Elektrofahrzeuge</code></h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="nav-vertical">
                                <ul class="nav nav-tabs nav-left flex-column" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="tab1" data-toggle="tab" aria-controls="tab1Content" href="#tab1Content" role="tab" aria-selected="true">Basisdaten</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab2" data-toggle="tab" aria-controls="tab2Content" href="#tab2Content" role="tab" aria-selected="false">Fahrzeug</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab3" data-toggle="tab" aria-controls="tab3Content" href="#tab3Content" role="tab" aria-selected="false">Ladestation</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <form method="post" action=" " id="electric-vehicle_form">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ request()->id }}">
                                        <input type="hidden" name="article_group_id" value="{{ request()->article }}">

                                        <div class="tab-pane active" id="tab1Content" role="tabpanel" aria-labelledby="tab1">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="company">Unternehmen</label>
                                                        <input type="text" class="form-control" name="company" value="Example">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name">Name</label>
                                                        <input type="text" class="form-control" name="name" value="22 kWh">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="description">Beschreibung</label>
                                                        <textarea class="form-control" name="description"></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="available">Lieferbar</label>
                                                        <input type="checkbox" name="available" checked>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="version">Version</label>
                                                        <input type="text" class="form-control" name="version" value="2">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="created_at">Erstellt am</label>
                                                        <input type="text" class="form-control" name="created_at" value="11/17/2023 7:55:55 AM">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="user_id">Benutzer-ID</label>
                                                        <input type="text" class="form-control" name="user_id" value="Keine - Systemdatensatz">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab2Content" role="tabpanel" aria-labelledby="tab2">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="range_wltp">Reichweite nach WLTP in km</label>
                                                        <input type="number" class="form-control" name="range_wltp" value="190">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="consumption">Verbrauch in kWh/100km</label>
                                                        <input type="number" class="form-control" name="consumption" value="12.9">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="battery_capacity">Batteriekapazität in kWh</label>
                                                        <input type="number" class="form-control" name="battery_capacity" value="21.6">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="discharge_power">Entladeleistung in kW</label>
                                                        <input type="number" class="form-control" name="discharge_power" value="3.7">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="motor_power">Motorleistung in kW</label>
                                                        <input type="number" class="form-control" name="motor_power" value="125">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="empty_weight">Leergewicht in kg</label>
                                                        <input type="number" class="form-control" name="empty_weight" value="1195">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="max_speed">Höchstgeschwindigkeit in km/h</label>
                                                        <input type="number" class="form-control" name="max_speed" value="150">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="payload">Zuladung in kg</label>
                                                        <input type="number" class="form-control" name="payload" value="425">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="seats">Anzahl Sitzplätze</label>
                                                        <input type="number" class="form-control" name="seats" value="4">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab3Content" role="tabpanel" aria-labelledby="tab3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="charging_technology">Ladetechnik</label>
                                                        <input type="text" class="form-control" name="charging_technology" value="AC Typ 2">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="charging_power">Ladeleistung in kW</label>
                                                        <input type="number" class="form-control" name="charging_power" value="11">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="discharge_for_consumption">Entladen zur Verbrauchsdeckung</label>
                                                        <input type="checkbox" name="discharge_for_consumption">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 float-right">
                                            <button type="submit" class="btn btn-primary">Absenden</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
 
            <div class="col-xl-12 col-lg-12" id="power_optimizers">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <h4 class="card-title">Technische Daten <code>Leistungsoptimierer</code></h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="nav-vertical">
                                <ul class="nav nav-tabs nav-left flex-column" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="tab1power" data-toggle="tab" aria-controls="tab1Contentpower" href="#tab1Contentpower" role="tab" aria-selected="true">Basisdaten</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab2power" data-toggle="tab" aria-controls="tab2Contentpower" href="#tab2Contentpower" role="tab" aria-selected="false">Elektrische Daten</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab3power" data-toggle="tab" aria-controls="tab3Contentpower" href="#tab3Contentpower" role="tab" aria-selected="false">Brennstoffverbrauch</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <form method="post"  id="power-optimizer_form">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ request()->id }}">
                                        <input type="hidden" name="article_group_id" value="{{ request()->article }}">
                                        <div class="tab-pane active" id="tab1Contentpower" role="tabpanel" aria-labelledby="tab1power">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="company">Unternehmen</label>
                                                        <input type="text" class="form-control" name="company" value="Example">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name">Name</label>
                                                        <input type="text" class="form-control" name="name" value="10.2 kW - 240 V - 1p">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="description">Beschreibung</label>
                                                        <textarea class="form-control" name="description"></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="available">Lieferbar</label>
                                                        <input type="checkbox" name="available" checked>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="version">Version</label>
                                                        <input type="text" class="form-control" name="version" value="2">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="created_at">Erstellt am</label>
                                                        <input type="text" class="form-control" name="created_at" value="5/20/2011 11:09:10 AM">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="user_id">Benutzer-ID</label>
                                                        <input type="text" class="form-control" name="user_id" value="Keine - Systemdatensatz">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab2Contentpower" role="tabpanel" aria-labelledby="tab2power">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="ac_nominal_voltage">AC-Nennspannung in V</label>
                                                        <input type="number" class="form-control" name="ac_nominal_voltage" value="240">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="ac_nominal_current">AC-Nennstrom in A</label>
                                                        <input type="number" class="form-control" name="ac_nominal_current" value="42">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="ac_nominal_power">AC-Nennleistung in kVA</label>
                                                        <input type="number" class="form-control" name="ac_nominal_power" value="10.2">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="max_ac_power">Max. AC-Leistung in kVA</label>
                                                        <input type="number" class="form-control" name="max_ac_power" value="12">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="num_phases">Anzahl Phasen</label>
                                                        <input type="number" class="form-control" name="num_phases" value="1">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab3Contentpower" role="tabpanel" aria-labelledby="tab3power">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="load_0">Auslastung 0% (Brennstoffverbrauch in l/h)</label>
                                                        <input type="number" class="form-control" name="load_0" value="0">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="load_25">Auslastung 25% (Brennstoffverbrauch in l/h)</label>
                                                        <input type="number" class="form-control" name="load_25" value="2.75">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="load_50">Auslastung 50% (Brennstoffverbrauch in l/h)</label>
                                                        <input type="number" class="form-control" name="load_50" value="3.82">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="load_75">Auslastung 75% (Brennstoffverbrauch in l/h)</label>
                                                        <input type="number" class="form-control" name="load_75" value="5.45">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="load_100">Auslastung 100% (Brennstoffverbrauch in l/h)</label>
                                                        <input type="number" class="form-control" name="load_100" value="8">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 float-right">
                                            <button type="submit" class="btn btn-primary">Absenden</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
 
            <div class="col-xl-12 col-lg-12" id="inverters">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <h4 class="card-title">Technische Daten <code>Wechselrichter</code> </h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="nav-vertical">
                                <ul class="nav nav-tabs nav-left flex-column" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="tab1" data-toggle="tab" aria-controls="tab1Content" href="#tab1Content" role="tab" aria-selected="true">Basisdaten</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab2inverters" data-toggle="tab" aria-controls="tab2Contentinverters" href="#tab2Contentinverters" role="tab" aria-selected="false">Elektrische Daten - DC</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab3inverters" data-toggle="tab" aria-controls="tab3Contentinverters" href="#tab3Contentinverters" role="tab" aria-selected="false">Elektrische Daten - AC</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab4inverters" data-toggle="tab" aria-controls="tab4Contentinverters" href="#tab4Contentinverters" role="tab" aria-selected="false">Elektrische Daten - Sonstige</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab5inverters" data-toggle="tab" aria-controls="tab5Contentinverters" href="#tab5Contentinverters" role="tab" aria-selected="false">MPP-Tracker</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab6inverters" data-toggle="tab" aria-controls="tab6Contentinverters" href="#tab6Contentinverters" role="tab" aria-selected="false">Wirkungsgrad</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <form method="post" id="inverter_form" >
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ request()->id }}">
                                        <input type="hidden" name="article_group_id" value="{{ request()->article }}">

                                        <div class="tab-pane active" id="tab1Contentinverters" role="tabpanel" aria-labelledby="tab1inverters">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="company">Unternehmen</label>
                                                        <input type="text" class="form-control" name="company" value="Example">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name">Name</label>
                                                        <input type="text" class="form-control" name="name" value="2 MPP - 3400 W">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="description">Beschreibung</label>
                                                        <textarea class="form-control" name="description"></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="available">Lieferbar</label>
                                                        <input type="checkbox" name="available" checked>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="version">Version</label>
                                                        <input type="text" class="form-control" name="version" value="4">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="created_at">Erstellt am</label>
                                                        <input type="text" class="form-control" name="created_at" value="11/18/2022 7:19:49 AM">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="user_id">Benutzer-ID</label>
                                                        <input type="text" class="form-control" name="user_id" value="Keine - Systemdatensatz">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab2Contentinverters" role="tabpanel" aria-labelledby="tab2inverters">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="dc_nominal_power">DC-Nennleistung in kW</label>
                                                        <input type="number" class="form-control" name="dc_nominal_power" value="3.6">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="max_dc_power">Max. DC-Leistung in kW</label>
                                                        <input type="number" class="form-control" name="max_dc_power" value="4.2">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="dc_nominal_voltage">DC-Nennspannung in V</label>
                                                        <input type="number" class="form-control" name="dc_nominal_voltage" value="450">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="max_input_voltage">Max. Eingangsspannung in V</label>
                                                        <input type="number" class="form-control" name="max_input_voltage" value="1000">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="max_input_current">Max. Eingangsstrom in A</label>
                                                        <input type="number" class="form-control" name="max_input_current" value="20">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="max_dc_short_circuit_current">Max. Kurzschlussstrom DC in A</label>
                                                        <input type="number" class="form-control" name="max_dc_short_circuit_current" value="30">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="num_dc_inputs">Anzahl DC-Eingänge</label>
                                                        <input type="number" class="form-control" name="num_dc_inputs" value="1">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab3Contentinverters" role="tabpanel" aria-labelledby="tab3inverters">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="ac_nominal_power">AC-Nennleistung in kW</label>
                                                        <input type="number" class="form-control" name="ac_nominal_power" value="3.4">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="max_ac_power">Max. AC-Leistung in kVA</label>
                                                        <input type="number" class="form-control" name="max_ac_power" value="3.5">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="ac_nominal_voltage">AC-Nennspannung in V</label>
                                                        <input type="number" class="form-control" name="ac_nominal_voltage" value="240">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="num_phases">Anzahl Phasen</label>
                                                        <input type="number" class="form-control" name="num_phases" value="1">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="with_transformer">Mit Trafo</label>
                                                        <input type="checkbox" name="with_transformer" checked>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab4Contentinverters" role="tabpanel" aria-labelledby="tab4inverters">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="efficiency_change">Änderung des Wirkungsgrades bei Abweichung der Eingangsspannung von der Nennspannung in %/100V</label>
                                                        <input type="number" class="form-control" name="efficiency_change" value="0.2">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="min_feed_power">Min. Einspeiseleistung in W</label>
                                                        <input type="number" class="form-control" name="min_feed_power" value="2">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="standby_consumption">Standby-Verbrauch in W</label>
                                                        <input type="number" class="form-control" name="standby_consumption" value="0.4">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="night_consumption">Nachtverbrauch in W</label>
                                                        <input type="number" class="form-control" name="night_consumption" value="0">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab5Contentinverters" role="tabpanel" aria-labelledby="tab5inverters">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="power_range_below_20">Leistungsbereich < 20% der Nennleistung in %</label>
                                                        <input type="number" class="form-control" name="power_range_below_20" value="99.9">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="power_range_above_20">Leistungsbereich > 20% der Nennleistung in %</label>
                                                        <input type="number" class="form-control" name="power_range_above_20" value="100">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="parallel_operation">Parallelbetrieb</label>
                                                        <input type="text" class="form-control" name="parallel_operation" value="Zusammenschalten der MPP-Tracker ist nicht möglich">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="num_mpp_trackers">Anzahl MPP-Tracker</label>
                                                        <input type="number" class="form-control" name="num_mpp_trackers" value="2">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="identical_properties">Alle Tracker haben identische elektrische Eigenschaften</label>
                                                        <input type="checkbox" name="identical_properties" checked>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="max_input_current_per_mpp">Max. Eingangsstrom pro MPP-Tracker in A</label>
                                                        <input type="number" class="form-control" name="max_input_current_per_mpp" value="10">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="max_short_circuit_current_per_mpp">Max. Kurzschlussstrom pro MPP-Tracker in A</label>
                                                        <input type="number" class="form-control" name="max_short_circuit_current_per_mpp" value="15">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="max_input_power_per_mpp">Max. Eingangsleistung pro MPP-Tracker in kW</label>
                                                        <input type="number" class="form-control" name="max_input_power_per_mpp" value="2.1">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="min_mpp_voltage">Min. MPP-Spannung in V</label>
                                                        <input type="number" class="form-control" name="min_mpp_voltage" value="100">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="max_mpp_voltage">Max. MPP-Spannung in V</label>
                                                        <input type="number" class="form-control" name="max_mpp_voltage" value="850">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab6Contentinverters" role="tabpanel" aria-labelledby="tab6inverters">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="efficiency_0">Auslastung 0% (Wirkungsgrad in %)</label>
                                                        <input type="number" class="form-control" name="efficiency_0" value="0">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="efficiency_5">Auslastung 5% (Wirkungsgrad in %)</label>
                                                        <input type="number" class="form-control" name="efficiency_5" value="89.43">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="efficiency_10">Auslastung 10% (Wirkungsgrad in %)</label>
                                                        <input type="number" class="form-control" name="efficiency_10" value="92.88">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="efficiency_20">Auslastung 20% (Wirkungsgrad in %)</label>
                                                        <input type="number" class="form-control" name="efficiency_20" value="94.27">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="efficiency_30">Auslastung 30% (Wirkungsgrad in %)</label>
                                                        <input type="number" class="form-control" name="efficiency_30" value="94.39">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="efficiency_50">Auslastung 50% (Wirkungsgrad in %)</label>
                                                        <input type="number" class="form-control" name="efficiency_50" value="94.61">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="efficiency_75">Auslastung 75% (Wirkungsgrad in %)</label>
                                                        <input type="number" class="form-control" name="efficiency_75" value="94.39">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="efficiency_100">Auslastung 100% (Wirkungsgrad in %)</label>
                                                        <input type="number" class="form-control" name="efficiency_100" value="94.17">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 float-right">
                                            <button type="submit" class="btn btn-primary">Absenden</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
 

              <div class="col-xl-12 col-lg-12" id="backup_generators">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <h4 class="card-title">Technische Daten <code>Zusatzgenerator</code></h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="nav-vertical">
                                <ul class="nav nav-tabs nav-left flex-column" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="tab1backup" data-toggle="tab" aria-controls="tab1Contentbackup" href="#tab1Contentbackup" role="tab" aria-selected="true">Basisdaten</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab2backup" data-toggle="tab" aria-controls="tab2Contentbackup" href="#tab2Contentbackup" role="tab" aria-selected="false">Elektrische Daten</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab3backup" data-toggle="tab" aria-controls="tab3Contentbackup" href="#tab3Contentbackup" role="tab" aria-selected="false">Brennstoffverbrauch</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <form method="post" id="backup_generator_form">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ request()->id }}">
                                        <input type="hidden" name="article_group_id" value="{{ request()->article }}">

                                        <div class="tab-pane active" id="tab1Contentbackup" role="tabpanel" aria-labelledby="tab1backup">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="company">Unternehmen</label>
                                                        <input type="text" class="form-control" name="company" value="Example">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name">Name</label>
                                                        <input type="text" class="form-control" name="name" value="10.2 kW - 240 V - 1p">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="description">Beschreibung</label>
                                                        <textarea class="form-control" name="description"></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="available">Lieferbar</label>
                                                        <input type="checkbox" name="available" checked>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="version">Version</label>
                                                        <input type="text" class="form-control" name="version" value="2">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="created_at">Erstellt am</label>
                                                        <input type="text" class="form-control" name="created_at" value="5/20/2011 11:09:10 AM">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="user_id">Benutzer-ID</label>
                                                        <input type="text" class="form-control" name="user_id" value="Keine - Systemdatensatz">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab2Contentbackup" role="tabpanel" aria-labelledby="tab2backup">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="ac_nominal_voltage">AC-Nennspannung in V</label>
                                                        <input type="number" class="form-control" name="ac_nominal_voltage" value="240">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="ac_nominal_current">AC-Nennstrom in A</label>
                                                        <input type="number" class="form-control" name="ac_nominal_current" value="42">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="ac_nominal_power">AC-Nennleistung in kVA</label>
                                                        <input type="number" class="form-control" name="ac_nominal_power" value="10.2">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="max_ac_power">Max. AC-Leistung in kVA</label>
                                                        <input type="number" class="form-control" name="max_ac_power" value="12">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="num_phases">Anzahl Phasen</label>
                                                        <input type="number" class="form-control" name="num_phases" value="1">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab3Contentbackup" role="tabpanel" aria-labelledby="tab3backup">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="load_0">Auslastung 0% (Brennstoffverbrauch in l/h)</label>
                                                        <input type="number" class="form-control" name="load_0" value="0">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="load_25">Auslastung 25% (Brennstoffverbrauch in l/h)</label>
                                                        <input type="number" class="form-control" name="load_25" value="2.75">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="load_50">Auslastung 50% (Brennstoffverbrauch in l/h)</label>
                                                        <input type="number" class="form-control" name="load_50" value="3.82">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="load_75">Auslastung 75% (Brennstoffverbrauch in l/h)</label>
                                                        <input type="number" class="form-control" name="load_75" value="5.45">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="load_100">Auslastung 100% (Brennstoffverbrauch in l/h)</label>
                                                        <input type="number" class="form-control" name="load_100" value="8">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 float-right">
                                            <button type="submit" class="btn btn-primary">Absenden</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- END: Content-->
@endsection

@section('script')
<script src="{{ asset('js/select2.min.js') }}"></script>
<script async src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&callback=console.debug&libraries=maps,marker&v=beta"></script>
<script>
    $(document).ready(function() {
        $('#modules').select2(); 

        @if(Session::has('update_msg'))
        toastr.success("{{ session('updated_msg') }}");
        @endif
        @if(Session::has('save_msg'))
        toastr.success("{{ session('save_msg') }}");
        @endif
        @if(Session::has('delete_msg'))
        toastr.error("{{ session('delete_msg') }}");
        @endif

        // Show/Hide divs based on dropdown selection
        $('#modules').change(function() {
            var selectedValue = $(this).val();
            // Hide all content divs
            $('#pv_modules, #batteries, #battery_systems, #battery_inverters, #electric_vehices, #power_optimizers, #inverters, #backup_generators').hide();
            // Show the selected div
            $('#' + selectedValue).show();
        });

        // Trigger change event on page load to show the correct div if a value is already selected
        $('#modules').trigger('change');
    });
</script>


<!-- Battery Save  -->
 
<script>
    $(document).ready(function() {
        let product_id = $('input[name="product_id"]').val();
        let article_group_id = $('input[name="article_group_id"]').val();

        // Fetch and populate the form data
        $.ajax({
            url: '{{ url("/battery_load") }}',
            method: 'GET',
            data: {
                product_id: product_id,
                article_group_id: article_group_id
            },
            success: function(response) {
                if (response) {
                    for (let key in response) {
                        let input = $('[name="' + key + '"]');
                        if (input.attr('type') === 'checkbox') {
                            input.prop('checked', response[key]);
                        } else {
                            input.val(response[key]);
                        }
                    }
                }
            },
            error: function(xhr) {
                toastr.error('An error occurred while loading the data. Please try again.');
            }
        });

        // Handle form submission
        $('#battery-form').on('submit', function(e) {
            e.preventDefault();
            
            // Clear previous validation errors
            $('.form-group').find('.text-danger').remove();
            $('.form-control').removeClass('is-invalid');

            let formData = $(this).serialize();

            $.ajax({
                url: '{{ url("/batteries") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Battery configuration saved successfully!');
                    console.log(response);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let firstErrorField = null;
                        let errorMessages = '';

                        for (let key in errors) {
                            let input = $('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            input.closest('.form-group').append('<div class="text-danger">' + errors[key][0] + '</div>');

                            // Collect error messages for toastr
                            errorMessages += errors[key][0] + '<br>';

                            // Record the first error field
                            if (!firstErrorField) {
                                firstErrorField = input;
                            }
                        }

                        if (firstErrorField) {
                            // Scroll to the first error field
                            $('html, body').animate({
                                scrollTop: firstErrorField.offset().top - 100
                            }, 500);

                            firstErrorField.focus();
                        }

                        // Show toastr error message
                        toastr.error(errorMessages);
                    } else {
                        toastr.error('An error occurred. Please try again.');
                    }
                }
            });
        });
    });
</script>

 
<!-- PV CRUD  -->
<script>
    $(document).ready(function() {
        let product_id = $('input[name="product_id"]').val();
        let article_group_id = $('input[name="article_group_id"]').val(); // Assuming you have this field

        // Make #pv_modules visible
        $('#pv_modules').show();

        // Fetch and populate the form data
        $.ajax({
            url: '{{ route("product.pv.load") }}',
            method: 'GET',
            data: {
                product_id: product_id,
                article_group_id: article_group_id
            },
            success: function(response) {
                if (response) {
                    for (let key in response) {
                        let input = $('[name="' + key + '"]');
                        if (input.attr('type') === 'checkbox') {
                            input.prop('checked', response[key]);
                        } else {
                            input.val(response[key]);
                        }
                    }
                }
            },
            error: function(xhr) {
                toastr.error('An error occurred while loading the data. Please try again.');
            }
        });

        // Handle form submission
        $('#pv-form').on('submit', function(e) {
            e.preventDefault();
            
            // Clear previous validation errors
            $('.form-group').find('.text-danger').remove();
            $('.form-control').removeClass('is-invalid');

            let formData = $(this).serialize();

            $.ajax({
                url: '{{ route("product.pv.store") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('PV module configuration saved successfully!');
                    console.log(response);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let firstErrorField = null;
                        let errorMessages = '';

                        for (let key in errors) {
                            let input = $('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            input.closest('.form-group').append('<div class="text-danger">' + errors[key][0] + '</div>');
                            
                            // Collect error messages for toastr
                            errorMessages += errors[key][0] + '<br>';

                            // Record the first error field
                            if (!firstErrorField) {
                                firstErrorField = input;
                            }
                        }
                        
                        if (firstErrorField) {
                            // Switch to the tab containing the first error field
                            let tabPane = firstErrorField.closest('.tab-pane');
                            let tabPaneId = tabPane.attr('id');
                            $('.nav-link[href="#' + tabPaneId + '"]').tab('show');
                            
                            // Delay focusing to ensure the tab is fully shown
                            setTimeout(() => {
                                firstErrorField[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                                firstErrorField.focus();
                            }, 200);
                        }

                        // Show toastr error message
                        toastr.error(errorMessages);
                    } else {
                        toastr.error('An error occurred. Please try again.');
                    }
                }
            });
        });
    });
</script>



<!-- Battery System Save -->

<script>
    $(document).ready(function() {
        let product_id = $('input[name="product_id"]').val();
        let article_group_id = $('input[name="article_group_id"]').val();

        // Fetch and populate the form data
        $.ajax({
            url: '{{ route("battery.system.load") }}',
            method: 'GET',
            data: {
                product_id: product_id,
                article_group_id: article_group_id
            },
            success: function(response) {
                if (response) {
                    for (let key in response) {
                        let input = $('[name="' + key + '"]');
                        if (input.attr('type') === 'checkbox') {
                            input.prop('checked', response[key]);
                        } else {
                            input.val(response[key]);
                        }
                    }
                }
            },
            error: function(xhr) {
                toastr.error('An error occurred while loading the data. Please try again.');
            }
        });

        // Handle form submission
        $('#battery-system-form').on('submit', function(e) {
            e.preventDefault();
            
            // Clear previous validation errors
            $('.form-group').find('.text-danger').remove();
            $('.form-control').removeClass('is-invalid');

            let formData = $(this).serialize();

            $.ajax({
                url: '{{ route("battery.system.save") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Battery system configuration saved successfully!');
                    console.log(response);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let firstErrorField = null;
                        let errorMessages = '';

                        for (let key in errors) {
                            let input = $('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            input.closest('.form-group').append('<div class="text-danger">' + errors[key][0] + '</div>');

                            // Collect error messages for toastr
                            errorMessages += errors[key][0] + '<br>';

                            // Record the first error field
                            if (!firstErrorField) {
                                firstErrorField = input;
                            }
                        }

                        if (firstErrorField) {
                            // Scroll to the first error field
                            $('html, body').animate({
                                scrollTop: firstErrorField.offset().top - 100
                            }, 500);

                            firstErrorField.focus();
                        }

                        // Show toastr error message
                        toastr.error(errorMessages);
                    } else {
                        toastr.error('An error occurred. Please try again.');
                    }
                }
            });
        });
    });
</script>

<!-- Battery Inverter Save -->
<script>
    $(document).ready(function() {
        let product_id = $('input[name="product_id"]').val();
        let article_group_id = $('input[name="article_group_id"]').val();

        // Make #Model visible
        $('#battery-inverter-form').show();

        // Fetch and populate the form data
        $.ajax({
            url: '{{ url("/battery_inverter_load") }}',
            method: 'GET',
            data: {
                product_id: product_id,
                article_group_id: article_group_id
            },
            success: function(response) {
                if (response) {
                    for (let key in response) {
                        let input = $('[name="' + key + '"]');
                        if (input.attr('type') === 'checkbox') {
                            input.prop('checked', response[key]);
                        } else {
                            input.val(response[key]);
                        }
                    }
                }
            },
            error: function(xhr) {
                toastr.error('An error occurred while loading the data. Please try again.');
            }
        });

        // Handle form submission
        $('#battery-inverter-form').on('submit', function(e) {
            e.preventDefault();
            
            // Clear previous validation errors
            $('.form-group').find('.text-danger').remove();
            $('.form-control').removeClass('is-invalid');

            let formData = $(this).serialize();

            $.ajax({
                url: '{{ url("/battery-inverters") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Battery inverter configuration saved successfully!');
                    console.log(response);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let firstErrorField = null;
                        let errorMessages = '';

                        for (let key in errors) {
                            let input = $('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            input.closest('.form-group').append('<div class="text-danger">' + errors[key][0] + '</div>');

                            // Collect error messages for toastr
                            errorMessages += errors[key][0] + '<br>';

                            // Record the first error field
                            if (!firstErrorField) {
                                firstErrorField = input;
                            }
                        }

                        if (firstErrorField) {
                            // Scroll to the first error field
                            $('html, body').animate({
                                scrollTop: firstErrorField.offset().top - 100
                            }, 500);

                            firstErrorField.focus();
                        }

                        // Show toastr error message
                        toastr.error(errorMessages);
                    } else {
                        toastr.error('An error occurred. Please try again.');
                    }
                }
            });
        });
    });
</script>

<!-- Electric Auto Save  -->
<script>
    $(document).ready(function() {
        let product_id = $('input[name="product_id"]').val();
        let article_group_id = $('input[name="article_group_id"]').val();

        // Fetch and populate the form data
        $.ajax({
            url: '{{ url("/electric_vehicle_load") }}',
            method: 'GET',
            data: {
                product_id: product_id,
                article_group_id: article_group_id
            },
            success: function(response) {
                if (response) {
                    for (let key in response) {
                        let input = $('[name="' + key + '"]');
                        if (input.attr('type') === 'checkbox') {
                            input.prop('checked', response[key]);
                        } else {
                            input.val(response[key]);
                        }
                    }
                }
            },
            error: function(xhr) {
                toastr.error('An error occurred while loading the data. Please try again.');
            }
        });

        // Handle form submission
        $('#electric-vehicle_form').on('submit', function(e) {
            e.preventDefault();
            
            // Clear previous validation errors
            $('.form-group').find('.text-danger').remove();
            $('.form-control').removeClass('is-invalid');

            let formData = $(this).serialize();

            $.ajax({
                url: '{{ url("/electric-vehicles") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Electric vehicle configuration saved successfully!');
                    console.log(response);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let firstErrorField = null;
                        let errorMessages = '';

                        for (let key in errors) {
                            let input = $('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            input.closest('.form-group').append('<div class="text-danger">' + errors[key][0] + '</div>');

                            // Collect error messages for toastr
                            errorMessages += errors[key][0] + '<br>';

                            // Record the first error field
                            if (!firstErrorField) {
                                firstErrorField = input;
                            }
                        }

                        if (firstErrorField) {
                            // Scroll to the first error field
                            $('html, body').animate({
                                scrollTop: firstErrorField.offset().top - 100
                            }, 500);

                            firstErrorField.focus();
                        }

                        // Show toastr error message
                        toastr.error(errorMessages);
                    } else {
                        toastr.error('An error occurred. Please try again.');
                    }
                }
            });
        });
    });
</script>


<!-- Power Optimizer -->
<script>
    $(document).ready(function() {
        let product_id = $('input[name="product_id"]').val();
        let article_group_id = $('input[name="article_group_id"]').val();

        // Fetch and populate the form data
        $.ajax({
            url: '{{ url("/power_optimizer_load") }}',
            method: 'GET',
            data: {
                product_id: product_id,
                article_group_id: article_group_id
            },
            success: function(response) {
                if (response) {
                    for (let key in response) {
                        let input = $('[name="' + key + '"]');
                        if (input.attr('type') === 'checkbox') {
                            input.prop('checked', response[key]);
                        } else {
                            input.val(response[key]);
                        }
                    }
                }
            },
            error: function(xhr) {
                toastr.error('An error occurred while loading the data. Please try again.');
            }
        });

        // Handle form submission
        $('#power-optimizer_form').on('submit', function(e) {
            e.preventDefault();
            
            // Clear previous validation errors
            $('.form-group').find('.text-danger').remove();
            $('.form-control').removeClass('is-invalid');

            let formData = $(this).serialize();

            $.ajax({
                url: '{{ url("/power-optimizers") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Power optimizer configuration saved successfully!');
                    console.log(response);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let firstErrorField = null;
                        let errorMessages = '';

                        for (let key in errors) {
                            let input = $('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            input.closest('.form-group').append('<div class="text-danger">' + errors[key][0] + '</div>');

                            // Collect error messages for toastr
                            errorMessages += errors[key][0] + '<br>';

                            // Record the first error field
                            if (!firstErrorField) {
                                firstErrorField = input;
                            }
                        }

                        if (firstErrorField) {
                            // Scroll to the first error field
                            $('html, body').animate({
                                scrollTop: firstErrorField.offset().top - 100
                            }, 500);

                            firstErrorField.focus();
                        }

                        // Show toastr error message
                        toastr.error(errorMessages);
                    } else {
                        toastr.error('An error occurred. Please try again.');
                    }
                }
            });
        });
    });
</script>


<!-- Inverter -->
<script>
    $(document).ready(function() {
        let product_id = $('input[name="product_id"]').val();
        let article_group_id = $('input[name="article_group_id"]').val();

        // Fetch and populate the form data
        $.ajax({
            url: '{{ url("/inverter_load") }}',
            method: 'GET',
            data: {
                product_id: product_id,
                article_group_id: article_group_id
            },
            success: function(response) {
                if (response) {
                    for (let key in response) {
                        let input = $('[name="' + key + '"]');
                        if (input.attr('type') === 'checkbox') {
                            input.prop('checked', response[key]);
                        } else {
                            input.val(response[key]);
                        }
                    }
                }
            },
            error: function(xhr) {
                toastr.error('An error occurred while loading the data. Please try again.');
            }
        });

        // Handle form submission
        $('#inverter_form').on('submit', function(e) {
            e.preventDefault();
            
            // Clear previous validation errors
            $('.form-group').find('.text-danger').remove();
            $('.form-control').removeClass('is-invalid');

            let formData = $(this).serialize();

            $.ajax({
                url: '{{ url("/inverters") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Inverter configuration saved successfully!');
                    console.log(response);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let firstErrorField = null;
                        let errorMessages = '';

                        for (let key in errors) {
                            let input = $('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            input.closest('.form-group').append('<div class="text-danger">' + errors[key][0] + '</div>');

                            // Collect error messages for toastr
                            errorMessages += errors[key][0] + '<br>';

                            // Record the first error field
                            if (!firstErrorField) {
                                firstErrorField = input;
                            }
                        }

                        if (firstErrorField) {
                            // Scroll to the first error field
                            $('html, body').animate({
                                scrollTop: firstErrorField.offset().top - 100
                            }, 500);

                            firstErrorField.focus();
                        }

                        // Show toastr error message
                        toastr.error(errorMessages);
                    } else {
                        toastr.error('An error occurred. Please try again.');
                    }
                }
            });
        });
    });
</script>


<!-- Backup Generator -->
<script>
    $(document).ready(function() {
        $('#backup_generator_form').on('submit', function(e) {
            e.preventDefault();
            
            // Clear previous validation errors
            $('.form-group').find('.text-danger').remove();
            $('.form-control').removeClass('is-invalid');

            let formData = $(this).serialize();

            $.ajax({
                url: '{{ url("/backup-generators") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Backup generator configuration saved successfully!');
                    console.log(response);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        for (let key in errors) {
                            let input = $('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            input.closest('.form-group').append('<div class="text-danger">' + errors[key][0] + '</div>');
                        }
                    } else {
                        toastr.error('An error occurred. Please try again.');
                    }
                }
            });
        });

        // Load form data
        $.ajax({
            url: '{{ url("/backup_generator_load") }}', // Update the URL to your data endpoint
            method: 'GET',
            success: function(response) {
                if (response.data) {
                    for (let key in response.data) {
                        let input = $('[name="' + key + '"]');
                        if (input.attr('type') === 'checkbox') {
                            input.prop('checked', response.data[key]);
                        } else {
                            input.val(response.data[key]);
                        }
                    }
                }
            },
            error: function(xhr) {
                toastr.error('An error occurred while loading data. Please try again.');
            }
        });
    });
</script>

@endsection
