<article id="pv" class="col-md-8 col-sm-12 col-12 d-none"
    style="display: flex;flex-wrap: wrap;align-content: flex-start;">
    <div class="col-md-12" id="section_1">
        <div class="cards">
            <div class="card-body d-flex">
                <div class="col-md-2 image">
                    <img src="{{ asset('images/articles/1716800964.png') }}" alt="alternative" style="width: 128px;">
                </div>
                <div class="col-md-10 contents">
                    <h2 class="title" style="color: #74b2d3">PHOTOVOLTAIK</h2>
                    <div class="form-group row">
                        <div class="col-md-12 mb-2 mt-2">
                            <span>Intention</span>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled mb-0">
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="intention"
                                                id="intention_interest" value="Interesse">
                                            <label class="custom-control-label"
                                                for="intention_interest">Interesse</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="intention"
                                                id="intention_available" value="vorhanden">
                                            <label class="custom-control-label"
                                                for="intention_available">vorhanden</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="intention"
                                                id="intention_extension" value="Erweiterung">
                                            <label class="custom-control-label"
                                                for="intention_extension">Erweiterung</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="intention"
                                                id="intention_spater" value="später">
                                            <label class="custom-control-label" for="intention_spater">später</label>
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <hr>
    </div>
    <section class="col-md-12" id="section_2">
        <div class="cards">
            <div class="card-body" style="display: flex !important;flex-wrap: wrap;">
                <div class="col-12">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <h4 class="bold ">Objektart</h4>
                        </div>
                        <div class="col-md-10">
                            <ul class="list-unstyled mb-0">
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="consultation"
                                                id="consultation_yes" checked value="Ja"
                                                @if($customer->consultation=="Ja") checked enabled @else disabled
                                            @endif>
                                            <label class="custom-control-label" for="consultation_yes">EFH</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="consultation"
                                                id="consultation_no" value="Nein">
                                            <label class="custom-control-label" for="consultation_no">MFH</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="consultation"
                                                id="consultation_persönlich" value="persönlich">
                                            <label class="custom-control-label"
                                                for="consultation_persönlich">Neubau</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="consultation"
                                                id="consultation_telefonisch" value="telefonisch">
                                            <label class="custom-control-label"
                                                for="consultation_telefonisch">Sanierung</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="consultation"
                                                id="consultation_Video" value="Video">
                                            <label class="custom-control-label"
                                                for="consultation_Video">Einzelmaßnahmen</label>
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <h4 class="bold">Anzahl WE</h4>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="number_we" value="" required>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <h4 class="bold">Anzahl Zähler</h4>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="number_counter" required>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group row ">
                        <div class="col-md-4">
                            <h4 class="bold">Stromverbrauch</h4>
                        </div>
                        <div class="col-md-8 flex_me ">
                            <input type="text" class="form-control" name="consumption" required>&nbsp;<span> kWh</span>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <h4 class="bold ">E-Auto</h4>
                        </div>
                        <div class="col-md-10">
                            <ul class="list-unstyled mb-0">
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="e_auto"
                                                id="e_auto_no" checked value="nein">
                                            <label class="custom-control-label" for="e_auto_no">nein</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="e_auto"
                                                id="e_auto_yes" value="ja">
                                            <label class="custom-control-label" for="e_auto_yes">ja</label>
                                        </div>
                                    </fieldset>
                                </li>

                                <li class="d-inline-blocks mr-1 " style="width:330px">
                                    <div class="form-group row ">
                                        <div class="col-md-4">
                                            <h4 class="bold">Anzahl</h4>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="e_auto_count" required>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <h4 class="bold ">Wallbox gewünscht</h4>
                        </div>
                        <div class="col-md-10">
                            <ul class="list-unstyled mb-0">
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="wall_box"
                                                id="wall_box_no" checked value="nein">
                                            <label class="custom-control-label" for="wall_box_no">nein</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="wall_box"
                                                id="wall_box_yes" value="ja">
                                            <label class="custom-control-label" for="wall_box_yes">ja</label>
                                        </div>
                                    </fieldset>
                                </li>

                                <li class="d-inline-blocks mr-1 " style="width:330px">
                                    <div class="form-group row ">
                                        <div class="col-md-4">
                                            <h4 class="bold">Anzahl</h4>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="wall_box_count" required>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <div class="col-12">
        <hr>
    </div>
    @php
    $roof_no= 1;
    @endphp
    <section class="col-md-12" id="section_3">
        <div class="cards">
            <div class="card-body" style="display: flex !important; flex-wrap: wrap;">
                <div class="col-12">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <h4 class="bold">Dach 1</h4>
                        </div>
                        <div class="col-md-2">
                            <span>Bezeichnung</span>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="number_we[0]" value="" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" id="add_more"
                                class="btn btn-icon btn-icon rounded-circle btn-light mr-1 mb-1 waves-effect waves-light">
                                <i class="feather icon-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-12" style="margin-bottom: 40px;">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <ul class="list-unstyleds mb-0">
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <img src="{{ asset('images/roofs/Satteldach.png') }}" alt="" srcset=""
                                            style="width:150px;" for="roof_Satteldach_0">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="roof[0]"
                                                id="roof_Satteldach_0" value="Satteldach" checked>
                                            <label class="custom-control-label"
                                                for="roof_Satteldach_0">Satteldach</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <img src="{{ asset('images/roofs/Flachdach.png') }}" alt="" srcset=""
                                            style="width:150px;" for="roof_Flachdach_0">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="roof[0]"
                                                id="roof_Flachdach_0" value="Flachdach">
                                            <label class="custom-control-label" for="roof_Flachdach_0">Flachdach</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <img src="{{ asset('images/roofs/Garage.png') }}" alt="" srcset=""
                                            style="width:150px;" for="roof_Garage_0">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="roof[0]"
                                                id="roof_Garage_0" value="Garage">
                                            <label class="custom-control-label" for="roof_Garage_0">Garage</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <img src="{{ asset('images/roofs/Carport.png') }}" alt="" srcset=""
                                            style="width:150px;" for="roof_Carport_0">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="roof[0]"
                                                id="roof_Carport_0" value="Carport">
                                            <label class="custom-control-label" for="roof_Carport_0">Carport</label>
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <h3 class="bold">Dacheindeckung</h3>
                        </div>
                        <div class="col-md-4">
                            <select id="tiles" name="tiles[0]" style="width:100%" required>
                                @foreach ($tiles as $tile)
                                <option value="{{ $tile->id }}"
                                    data-image="{{ asset('images/products/'.$tile->image) }}">{{
                                    $tile->product }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled mb-0">
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input"
                                                name="construction_fluid[0]" id="construction_fluid_boton_0"
                                                value="Beton">
                                            <label class="custom-control-label"
                                                for="construction_fluid_boton_0">Beton</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input"
                                                name="construction_fluid[0]" id="construction_fluid_ton_0" value="Ton">
                                            <label class="custom-control-label"
                                                for="construction_fluid_ton_0">Ton</label>
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <h3 class="bold">Aufdachdämmung</h3>
                        </div>
                        <div class="col-md-10">
                            <ul class="list-unstyled mb-0">
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="insulation[0]"
                                                id="insulation_ja_0" value="ja">
                                            <label class="custom-control-label" for="insulation_ja_0">ja</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="insulation[0]"
                                                id="insulation_nein_0" value="nein">
                                            <label class="custom-control-label" for="insulation_nein_0">nein</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1" style="width:330px">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <h4 class="bold">Stärke</h4>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="insulation_strength[0]"
                                                required>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <h3 class="bold">Zwischen sparrendämmung</h3>
                        </div>
                        <div class="col-md-10">
                            <ul class="list-unstyled mb-0">
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="rafter[0]"
                                                id="rafter_ja_0" value="ja">
                                            <label class="custom-control-label" for="rafter_ja_0">ja</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="rafter[0]"
                                                id="rafter_nein_0" value="nein">
                                            <label class="custom-control-label" for="rafter_nein_0">nein</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1" style="width:330px">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <h4 class="bold">Stärke</h4>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="rafter_strength[0]" required>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="col-12">
        <hr>
    </div>

    <section class="col-md-12" id="section_4">
        <div class="cards">
            <div class="card-body" style="display: flex !important;flex-wrap: wrap; ">
                <div class="col-12">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <h4 class="bold ">Heizung</h4>
                        </div>
                    </div>
                </div>

                <div class="col-12" style=" margin-bottom: 40px;">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <ul class="list-unstyled mb-0">
                                <li class="d-inline-block mr-2">
                                    <fieldset>
                                        <div class="avatar bg-primary mr-1">
                                            <div class="avatar-content">
                                                WP
                                            </div>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="heatpump"
                                                id="heatpump">
                                            <label class="custom-control-label" for="heatpump">Wärmepumpe</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-2">
                                    <fieldset>
                                        <div class="avatar bg-primary mr-1">
                                            <div class="avatar-content">
                                                GAS
                                            </div>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="gas" id="gas">
                                            <label class="custom-control-label" for="gas">Gas</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-2">
                                    <fieldset>
                                        <div class="avatar bg-primary mr-1">
                                            <div class="avatar-content">
                                                Öl
                                            </div>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="oil" id="oil">
                                            <label class="custom-control-label" for="oil">Öl</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-2">
                                    <fieldset>
                                        <div class="avatar bg-primary mr-1">
                                            <div class="avatar-content">
                                                P
                                            </div>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="pellets"
                                                id="pellets">
                                            <label class="custom-control-label" for="pellets">Pellets</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-2">
                                    <fieldset>
                                        <div class="avatar bg-primary mr-1">
                                            <div class="avatar-content">
                                                K
                                            </div>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="kamin" id="kamin">
                                            <label class="custom-control-label" for="kamin">Kamin</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-2">
                                    <fieldset>
                                        <div class="avatar bg-primary mr-1">
                                            <div class="avatar-content">
                                                NS
                                            </div>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="nachtspeicher"
                                                id="nachtspeicher">
                                            <label class="custom-control-label"
                                                for="nachtspeicher">Nachtspeicher</label>
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <div class="col-12">
        <hr>
    </div>

    <section class="col-md-12" id="section_5">
        <div class="cards">
            <div class="card-body" style="display: flex !important;flex-wrap: wrap;">

                <div class="col-6">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <h4 class="bold ">Fußbodenheizung</h4>
                        </div>
                        <div class="col-md-9">
                            <ul class="list-unstyled mb-0">
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="underfloor_heating"
                                                id="underfloor_heating_yes" checked value="Ja">
                                            <label class="custom-control-label" for="underfloor_heating_yes">JA</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="underfloor_heating"
                                                id="underfloor_heating_no" value="Nein">
                                            <label class="custom-control-label" for="underfloor_heating_no">Nein</label>
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <h4 class="bold ">Heizkörper</h4>
                        </div>
                        <div class="col-md-10">
                            <ul class="list-unstyled mb-0">
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="radiator"
                                                id="radiator_yes" checked value="Ja">
                                            <label class="custom-control-label" for="radiator_yes">JA</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="radiator"
                                                id="radiator_no" value="Nein">
                                            <label class="custom-control-label" for="radiator_no">Nein</label>
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <h4 class="bold">Alter des Hauses</h4>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="construction_year" value="" required>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <h4 class="bold">Alter der Heizung</h4>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="heating_construction" required>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group row ">
                        <div class="col-md-4">
                            <h4 class="bold">beheizte Wohnfläche</h4>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="living_space" required>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group row ">
                        <div class="col-md-4">
                            <h4 class="bold">Nutzfläche unbeheizt</h4>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="unusable_space" required>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group row ">
                        <div class="col-md-4">
                            <h4 class="bold">Anzahl Personen</h4>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="number_people" required>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group row ">
                        <div class="col-md-4">
                            <h4 class="bold">Leistung der Anlage</h4>
                        </div>
                        <div class="col-md-8 flex_me">
                            <input type="text" class="form-control" name="system_performance"
                                required>&nbsp;<span>kWh</span>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group row ">
                        <div class="col-md-4">
                            <h4 class="bold">Verbrauch Heizung</h4>
                        </div>
                        <div class="col-md-8 flex_me">
                            <input type="text" class="form-control" name="consumption" required>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group row ">
                        <div class="col-md-4">
                            <h4 class="bold">Anzahl Heizkreise</h4>
                        </div>
                        <div class="col-md-8 flex_me">
                            <input type="text" class="form-control" name="no_heating" required>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group row ">
                        <div class="col-md-4">
                            <h4 class="bold">Vorlauftemperatur</h4>
                        </div>
                        <div class="col-md-8 flex_me">
                            <input type="text" class="form-control" name="flow_temp" required>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <h4 class="bold ">Solarthermie vorhanden</h4>
                        </div>
                        <div class="col-md-10">
                            <ul class="list-unstyled mb-0">
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="solor" id="solar_no"
                                                checked value="nein">
                                            <label class="custom-control-label" for="solar_no">nein</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="solor" id="solor_yes"
                                                value="ja">
                                            <label class="custom-control-label" for="solor_yes">ja</label>
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <h4 class="bold ">Heizlastberechnung vorhanden</h4>
                        </div>
                        <div class="col-md-10">
                            <ul class="list-unstyled mb-0">
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="hlb_calc"
                                                id="hlb_calc_no" checked value="nein">
                                            <label class="custom-control-label" for="hlb_calc_no">nein</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="hlb_calc"
                                                id="hlb_calc_yes" value="ja">
                                            <label class="custom-control-label" for="hlb_calc_yes">ja</label>
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <h4 class="bold ">Warmwasser durch</h4>
                        </div>
                        <div class="col-md-10">
                            <ul class="list-unstyled mb-0">
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="warmwater"
                                                id="warmwater_heating" checked value="Heizung">
                                            <label class="custom-control-label" for="warmwater_heating">Heizung</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="warmwater"
                                                id="warmwater_waterheater" value="Durchlauferhitzer">
                                            <label class="custom-control-label"
                                                for="warmwater_waterheater">Durchlauferhitzer</label>
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <h4 class="bold ">Fensterverglasung</h4>
                        </div>
                        <div class="col-md-10">
                            <ul class="list-unstyled mb-0">
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="glass" id="glass_1"
                                                checked value="1-fach">
                                            <label class="custom-control-label" for="glass_1">1-fach</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="glass" id="glass_2"
                                                value="2-fach">
                                            <label class="custom-control-label" for="glass_2">2-fach</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="glass" id="glass_3"
                                                value="3-fach">
                                            <label class="custom-control-label" for="glass_3">3-fach</label>
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <h4 class="bold ">Fensterrahmen</h4>
                        </div>
                        <div class="col-md-10">
                            <ul class="list-unstyled mb-0">
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="window_margin"
                                                id="window_margin_alu" checked value="Alu">
                                            <label class="custom-control-label" for="window_margin_alu">Alu</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="window_margin"
                                                id="window_margin_kunststoff" value="Kunststoff">
                                            <label class="custom-control-label"
                                                for="window_margin_kunststoff">Kunststoff</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="window_margin"
                                                id="window_margin_holz" value="Holz">
                                            <label class="custom-control-label" for="window_margin_holz">Holz</label>
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group row ">
                        <div class="col-md-4">
                            <h4 class="bold ">Aussendämmung Stärke</h4>
                        </div>
                        <div class="col-md-8 flex_me">
                            <input type="text" class="form-control" name="insulation_thickness" required>
                        </div>
                    </div>
                </div>

            </div>
    </section>



    </div>
</article>

 