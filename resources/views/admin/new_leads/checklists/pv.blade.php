                <div id="pv"  >
                        <article class="col-md-12 col-sm-12 col-12">
               
                            <form method="post" enctype="multipart/form-data" id="pvForm">
                                @csrf
                                <div class="container"
                                    style="display: flex;flex-wrap: wrap;align-content: flex-start; background: white; ">
                                    <div class="card-title h4  " style="    position: absolute;right: -64px;">
                                        <div class="col-md-2">
                                            <span style="color: #74b2d3; font-size:13px;"> Bewertung Projekt: <span
                                                    style="color:#e50056">4</span>/23 </span>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="progress progress-bar-danger progress-lg">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                    role="progressbar" aria-valuenow="60" aria-valuemin="60"
                                                    aria-valuemax="100" style="width:60%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="section_1">
                                        <div class="cards">
                                            <div class="card-body d-flex">
                                                <div class="col-md-2 image">
                                                    <img src="{{ asset('images/articles/pv.png') }}"
                                                        alt="alternative" style="width: 128px;">
                                                </div>
                                                <div class="col-md-10 contents">
                                                    <input type="hidden" name="customer_id"
                                                        value="{{ $customer->id }}">
                                                    <h2 class="title" style="color: #74b2d3">PHOTOVOLTAIK</h2>
                                                    <div class="form-group row">
                                                        <div class="col-md-12 mb-2 mt-2">
                                                            <span>Intention</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="intention"
                                                                                id="intention_interest"
                                                                                value="Interesse">
                                                                            <label class="custom-control-label"
                                                                                for="intention_interest">Interesse</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="intention"
                                                                                id="intention_available"
                                                                                value="vorhanden">
                                                                            <label class="custom-control-label"
                                                                                for="intention_available">vorhanden</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="intention"
                                                                                id="intention_extension"
                                                                                value="Erweiterung">
                                                                            <label class="custom-control-label"
                                                                                for="intention_extension">Erweiterung</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="intention"
                                                                                id="intention_spater"
                                                                                value="später">
                                                                            <label class="custom-control-label"
                                                                                for="intention_spater">später</label>
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
                                            <div class="card-body"
                                                style="display: flex !important;flex-wrap: wrap;">
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <h4 class="bold ">Objektart</h4>
                                                        </div>
                                                        <input type="hidden" name="customer_id"
                                                            value="{{ request()->id }}">
                                                        <input type="hidden" name="postcode"
                                                            value="{{ request()->postcode }}">
                                                        <input type="hidden" name="address_no"
                                                            value="{{ request()->address_no }}">


                                                        <div class="col-md-10">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="property_type"
                                                                                id="objective_EFH" checked
                                                                                value="EFH">
                                                                            <label class="custom-control-label"
                                                                                for="objective_EFH">EFH</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="property_type"
                                                                                id="objectiveMFH" value="MFH">
                                                                            <label class="custom-control-label"
                                                                                for="objectiveMFH">MFH</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="property_type"
                                                                                id="objectiveNeubau"
                                                                                value="Neubau">
                                                                            <label class="custom-control-label"
                                                                                for="objectiveNeubau">Neubau</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="property_type"
                                                                                id="consultation_telefonisch"
                                                                                value="Sanierung">
                                                                            <label class="custom-control-label"
                                                                                for="consultation_telefonisch">Sanierung</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="property_type"
                                                                                id="consultation_Einzelmassnahmen"
                                                                                value="Einzelmaßnahmen">
                                                                            <label class="custom-control-label"
                                                                                for="consultation_Einzelmassnahmen">Einzelmaßnahmen</label>
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
                                                        <div class="col-md-8 textbox-container empty">
                                                            <input type="text" class="form-control textbox"
                                                                name="number_of_units" value="">
                                                            <div class="indicator"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <h4 class="bold">Anzahl Zähler</h4>
                                                        </div>
                                                        <div class="col-md-8 textbox-container empty">
                                                            <input type="text" class="form-control textbox"
                                                                name="number_of_meters">
                                                            <div class="indicator"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="form-group row ">
                                                        <div class="col-md-4">
                                                            <h4 class="bold">Stromverbrauch</h4>
                                                        </div>
                                                        <div class="col-md-8 flex_me textbox-container empty ">
                                                            <input type="text" class="form-control textbox"
                                                                name="electricity_consumption" value="{{ old('electricity_consumption', $customer->annual_consumption) }}">
                                                            <div class="indicator"></div>
                                                            <span style="  margin-left: -45px;">
                                                                kWh</span>

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
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="electric_car"
                                                                                id="e_auto_no" @if($customer->electric_car=="Nein") checked @endif
                                                                                value="nein">
                                                                            <label class="custom-control-label"
                                                                                for="e_auto_no">nein</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="electric_car"
                                                                                id="e_auto_yes" value="ja" @if($customer->electric_car=="Ja") checked @endif>
                                                                            <label class="custom-control-label"
                                                                                for="e_auto_yes">ja</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>

                                                                <li class="d-inline-blocks mr-1 "
                                                                    style="width:330px">
                                                                    <div class="form-group row ">
                                                                        <div class="col-md-4">
                                                                            <h4 class="bold">Anzahl</h4>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <input type="text"
                                                                                class="form-control"
                                                                                name="number_of_electric_cars" value="{{old('number_of_electric_cars', $customer->electric_car_plan)}}">
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
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="wallbox_desired"
                                                                                id="wall_box_no" checked
                                                                                value="nein">
                                                                            <label class="custom-control-label"
                                                                                for="wall_box_no">nein</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="wallbox_desired"
                                                                                id="wall_box_yes" value="ja">
                                                                            <label class="custom-control-label"
                                                                                for="wall_box_yes">ja</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>

                                                                <li class="d-inline-blocks mr-1 "
                                                                    style="width:330px">
                                                                    <div class="form-group row ">
                                                                        <div class="col-md-4">
                                                                            <h4 class="bold">Anzahl</h4>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <input type="text"
                                                                                class="form-control"
                                                                                name="number_of_wallboxes">
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
                                    <section class="col-md-12 dynamic-section" id="section_3">
                                        <div class="cards">
                                            <div class="card-body"
                                                style="display: flex !important; flex-wrap: wrap;">
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <h4 class="bold">Dach 1</h4>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <span>Bezeichnung</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control"
                                                                name="designation[0]" value="">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" id="add_more"
                                                                class="btn btn-icon btn-icon rounded-circle btn-light mr-1 mb-1 waves-effect waves-light">
                                                                <i class="feather icon-plus"></i>
                                                            </button>
                                                            <button type="button"
                                                                class="remove_roof btn btn-icon btn-icon rounded-circle btn-light mr-1 mb-1 waves-effect waves-light d-none">
                                                                <i class="feather icon-minus"></i>
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
                                                                        <img src="{{ asset('images/roofs/Satteldach.png') }}"
                                                                            alt="" srcset=""
                                                                            style="width:150px;"
                                                                            for="roof_Satteldach_0">
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="roof[0]"
                                                                                id="roof_Satteldach_0"
                                                                                value="Satteldach" @if($customer->roof_type=="Satteldach") checked @endif>
                                                                            <label class="custom-control-label"
                                                                                for="roof_Satteldach_0">Satteldach</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <img src="{{ asset('images/roofs/Flachdach.png') }}"
                                                                            alt="" srcset=""
                                                                            style="width:150px;"
                                                                            for="roof_Flachdach_0">
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="roof[0]"
                                                                                id="roof_Flachdach_0"
                                                                                value="Flachdach" @if($customer->roof_type=="Flachdach") checked @endif>
                                                                            <label class="custom-control-label"
                                                                                for="roof_Flachdach_0">Flachdach</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <img src="{{ asset('images/roofs/Garage.png') }}"
                                                                            alt="" srcset=""
                                                                            style="width:150px;"
                                                                            for="roof_Garage_0">
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="roof[0]"
                                                                                id="roof_Garage_0"
                                                                                value="Garage" @if($customer->roof_type=="Garage") checked @endif>
                                                                            <label class="custom-control-label"
                                                                                for="roof_Garage_0">Garage</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <img src="{{ asset('images/roofs/Carport.png') }}"
                                                                            alt="" srcset=""
                                                                            style="width:150px;"
                                                                            for="roof_Carport_0">
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="roof[0]"
                                                                                id="roof_Carport_0"
                                                                                value="Carport" @if($customer->roof_type=="Carport") checked @endif>
                                                                            <label class="custom-control-label"
                                                                                for="roof_Carport_0">Carport</label>
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
                                                            <select class="tiles" name="tiles[0]"
                                                                style="width:100%">
                                                                @foreach ($tiles as $tile)
                                                                <option value="{{ $tile->id }}"
                                                                    data-image="{{ asset('images/products/'.$tile->image) }}"
                                                                    data-roof-type="{{ $tile->roof_type }}">
                                                                    {{ $tile->product }} ->
                                                                    {{ $tile->roof_type }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6" id="construction_fluid_section_0">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="construction_fluid[0]"
                                                                                id="construction_fluid_boton_0"
                                                                                value="Beton">
                                                                            <label class="custom-control-label"
                                                                                for="construction_fluid_boton_0">Beton</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="construction_fluid[0]"
                                                                                id="construction_fluid_ton_0"
                                                                                value="Ton">
                                                                            <label class="custom-control-label"
                                                                                for="construction_fluid_ton_0">Ton</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                            </ul>
                                                        </div>

                                                        <div class="col-md-6" id="tilt_section_0">
                                                            <div class="form-group row">
                                                                <div class="col-md-4">
                                                                    <h4 class="bold">Neigung</h4>
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <input type="text" class="form-control"
                                                                        name="tilt[0]">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12" id="insulation_section_0">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <h3 class="bold">Aufdachdämmung</h3>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="pv_insulation[0]"
                                                                                id="insulation_ja_0" value="ja">
                                                                            <label class="custom-control-label"
                                                                                for="insulation_ja_0">ja</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="pv_insulation[0]"
                                                                                id="insulation_nein_0"
                                                                                value="nein">
                                                                            <label class="custom-control-label"
                                                                                for="insulation_nein_0">nein</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1"
                                                                    style="width:330px">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-4">
                                                                            <h4 class="bold">Stärke</h4>
                                                                        </div>
                                                                        <div
                                                                            class="col-md-8 textbox-container empty">
                                                                            <input type="text"
                                                                                class="form-control textbox"
                                                                                name="thickness_roof_insulation[0]"
                                                                                placeholder=" ">
                                                                            <div class="indicator"></div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12" id="rafter_section_0">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <h3 class="bold">Zwischen sparrendämmung</h3>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="between_rafter_insulation[0]"
                                                                                id="rafter_ja_0" value="ja">
                                                                            <label class="custom-control-label"
                                                                                for="rafter_ja_0">ja</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="between_rafter_insulation[0]"
                                                                                id="rafter_nein_0" value="nein">
                                                                            <label class="custom-control-label"
                                                                                for="rafter_nein_0">nein</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1"
                                                                    style="width:330px">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-4">
                                                                            <h4 class="bold">Stärke</h4>
                                                                        </div>
                                                                        <div
                                                                            class="col-md-8 textbox-container empty">
                                                                            <input type="text"
                                                                                class="form-control textbox"
                                                                                name="thickness_between_rafter[0]">
                                                                            <div class="indicator"></div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12" id="asbestos_section_0">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <h3 class="bold">Asbesthaltig</h3>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="asbestos[0]"
                                                                                id="asbestos_ja_0" value="ja">
                                                                            <label class="custom-control-label"
                                                                                for="asbestos_ja_0">ja</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="asbestos[0]"
                                                                                id="asbestos_nein_0"
                                                                                value="nein">
                                                                            <label class="custom-control-label"
                                                                                for="asbestos_nein_0">nein</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12" id="roof_renovation_section_0">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <h3 class="bold">Dachsanierung notwendig</h3>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="roof_renovation[0]"
                                                                                id="roof_renovation_ja_0"
                                                                                value="ja">
                                                                            <label class="custom-control-label"
                                                                                for="roof_renovation_ja_0">ja</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="roof_renovation[0]"
                                                                                id="roof_renovation_nein_0"
                                                                                value="nein">
                                                                            <label class="custom-control-label"
                                                                                for="roof_renovation_nein_0">nein</label>
                                                                        </div>
                                                                    </fieldset>
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
                                            <div class="card-body"
                                                style="display: flex !important;flex-wrap: wrap;">
                                                <div class="col-md-4">
                                                    <div class="form-group row">
                                                        <div class="col-md-12">
                                                            <h4 class="bold">Zählerschrank</h4>
                                                        </div>
                                                        <div class="col-md-4 flex_me">
                                                            <ul class="mb-0"
                                                                style="display:flex; flex-wrap: wrap;flex-direction: column;">
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="meter_cabinet"
                                                                                id="cabinet_ok" checked
                                                                                value="ok">
                                                                            <label class="custom-control-label"
                                                                                for="cabinet_ok">ok</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="meter_cabinet"
                                                                                id="cabinet_strengthen"
                                                                                value="strengthen">
                                                                            <label class="custom-control-label"
                                                                                for="cabinet_strengthen">ertüchtigen</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="meter_cabinet"
                                                                                id="cabinet_neu" value="neu">
                                                                            <label class="custom-control-label"
                                                                                for="cabinet_neu">neu</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="meter_cabinet"
                                                                                id="cabinet_neuer"
                                                                                value="neuer Zählerschrank zwischen HAK und Zählerschrank">
                                                                            <label class="custom-control-label"
                                                                                style="width: 278px;"
                                                                                for="cabinet_neuer">neuer
                                                                                Zählerschrank zwischen HAK und
                                                                                Zählerschrank</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>

                                                            </ul>

                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <div class="col-md-12">
                                                            <h4 class="bold">Größe</h4>
                                                        </div>
                                                        <div class="col-md-4 flex_me">
                                                            <ul class="mb-0"
                                                                style="display:flex; flex-wrap: wrap;flex-direction: column;">
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="cabinet_size"
                                                                                id="cabinet_size_550" checked
                                                                                value="550">
                                                                            <label class="custom-control-label"
                                                                                for="cabinet_size_550">550</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="cabinet_size"
                                                                                id="cabinet_size_800"
                                                                                value="800">
                                                                            <label class="custom-control-label"
                                                                                for="cabinet_size_800">800</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="cabinet_size"
                                                                                id="cabinet_size_1100"
                                                                                value="1100">
                                                                            <label class="custom-control-label"
                                                                                for="cabinet_size_1100">1100</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>

                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="cabinet_size"
                                                                                id="cabinet_size_son"
                                                                                value="Sonstiges">
                                                                            <label class="custom-control-label"
                                                                                for="cabinet_size_son">Sonstiges</label>
                                                                            <input type="text"
                                                                                name="cabinet_size_sonstiges">

                                                                        </div>
                                                                    </fieldset>
                                                                </li>

                                                            </ul>

                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group row">
                                                        <div class="col-md-12">

                                                        </div>
                                                        <div class="col-md-4 flex_me">
                                                            <fieldset>
                                                                <label>Hersteller</label>

                                                                <select name="meter_cabinet_company" id=""
                                                                    class="form-control">
                                                                    @foreach ($electro as $elec)
                                                                    <option value="{{ $elec->id }}">
                                                                        {{ $elec->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </fieldset>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group row">
                                                        <div class="col-md-12">
                                                            <h4 class="bold">Einzubauende Komponenten</h4>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            <ul class="mb-0"
                                                                style="display:flex; flex-wrap: wrap; flex-direction: column;">
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="checkbox"
                                                                                class="custom-control-input"
                                                                                name="meter_adapter_plate"
                                                                                id="meter_adapter_plate">
                                                                            <label class="custom-control-label"
                                                                                for="meter_adapter_plate">Zähleradapterplatte</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="checkbox"
                                                                                class="custom-control-input"
                                                                                name="ac_surge_protection"
                                                                                id="ac_surge_protection">
                                                                            <label class="custom-control-label"
                                                                                for="ac_surge_protection"
                                                                                style="width: 232px;">AC
                                                                                Überspannungsschutz</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="checkbox"
                                                                                class="custom-control-input"
                                                                                name="ac_switch" id="ac_switch">
                                                                            <label class="custom-control-label"
                                                                                for="ac_switch">SLS
                                                                                Schalter</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="checkbox"
                                                                                class="custom-control-input"
                                                                                name="apz_field" id="apz_field">
                                                                            <label class="custom-control-label"
                                                                                for="apz_field">APZ Feld</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="checkbox"
                                                                                class="custom-control-input"
                                                                                name="disconnect_relay"
                                                                                id="disconnect_relay">
                                                                            <label class="custom-control-label"
                                                                                for="disconnect_relay">Trenn-Relais</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="checkbox"
                                                                                class="custom-control-input"
                                                                                name="equipotential_bonding"
                                                                                id="equipotential_bonding_busbar">
                                                                            <label class="custom-control-label"
                                                                                for="equipotential_bonding_busbar">Potentialausgleichsschiene</label>
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
  

                                    <div class="col-12 float-right">
                                        <button type="button" id="saveButton"
                                            class="btn btn-icon btn-icon btn-light mr-1 mb-1 waves-effect waves-light float-right">
                                            <i class="feather icon-save"></i> Daten Ubernahnen
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </article>
                    </div>