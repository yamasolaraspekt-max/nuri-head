
        <div class="row">
            <div class="col-12">
                @if(DB::table('user_rolls')
                ->where('user_rolls.user_id', '=', auth()->user()->name)
                ->where('user_rolls.item_id', '=', 'Customer')
                ->where('user_rolls.is_update', '=', 'on')
                ->first())
                    <!-- Delete Modal -->
                    <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1 float-right" data-toggle="modal" data-target="#delete-pro{{$data->id}}">
                    <i class="feather icon-edit "></i>
                    </button>
                    @endif

                    <!-- Modal -->
                    <div class="modal fade text-left" id="delete-pro{{$data->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ action('App\Http\Controllers\CustomerController@details_update') }}" method="post">
                                    @csrf

                                    <div class="row">

                                        <div class="col-6">
                                               <div class="col-md-12">
                                                <span>Personen Anzahl:</span>
                                                    <input type="hidden" name="id" value="{{ request()->id }}">
                                                    <input type="text"  id="number_people" value="{{$data->number_people }}" class="form-control" name="number_people" >
                                                </div>
                                        </div>

                                        <div class="col-6">
                                               <div class="col-md-12">
                                                <span>Gebäudeart:</span>
                                                <fieldset class="form-group">
                                                    <select class="form-control" id="building_type" name="building_type">
                                                        @foreach ($building_types as $building_type)
                                                        <option value="{{ $building_type->id }}">{{ $building_type->building_type }}</option>
                                                        @endforeach

                                                    </select>
                                                </fieldset>
                                                </div>
                                        </div>


                                        <div class="col-6">
                                               <div class="col-md-12">
                                                <span>Wohnfläche:</span>
                                                    <input type="text"  id="living_space" value="{{$data->living_space }}"class="form-control" name="living_space" >
                                                </div>
                                        </div>

                                        <div class="col-6">
                                               <div class="col-md-12">
                                                <span>Baujahr:</span>
                                                    <input type="text"  id="construction_year" value="{{$data->construction_year }}"class="form-control" name="construction_year" >
                                                </div>
                                        </div>

                                        <div class="col-12">
                                               <div class="col-md-12">
                                                <span>Heizungsart:</span>
                                                <fieldset class="form-group">
                                                    <select class="form-control" id="heating_type" name="heating_type">
                                                        @foreach ($heating_types as $heating_type)
                                                        <option value="{{ $heating_type->id }}">{{ $heating_type->heating_type }}</option>
                                                        @endforeach

                                                    </select>
                                                </fieldset>
                                                </div>
                                        </div>


                                        <div class="col-6">
                                               <div class="col-md-12">
                                                <span>Durschn. Verbrauch:</span>

                                                    <input type="text"  id="consumption" value="{{$data->consumption }}"class="form-control" name="consumption" >
                                                </div>
                                        </div>
                                        <div class="col-6">
                                               <div class="col-md-12">
                                                <span>Maßeinheit:</span>
                                                <fieldset class="form-group">
                                                    <select class="form-control" id="measure_unit" name="measure_unit">
                                                        <option value="L"> L</option>
                                                        <option value="kWh" selected> kWh</option>
                                                        <option value="m³"> m³</option>
                                                        <option value="m²"> m²</option>
                                                        <option value="kg"> kg</option>
                                                        <option value="L"> L</option>
                                                    </select>
                                                </fieldset>
                                                </div>
                                        </div>


                                        <div class="col-6">
                                               <div class="col-md-12">
                                                <span>Fussbodenheizung:</span>
                                                    <fieldset class="form-group">
                                                        <select class="form-control" id="underfloor_heating" name="underfloor_heating">
                                                            <option value="Yes">Ja</option>
                                                            <option value="NO"> Nein</option>
                                                        </select>
                                                    </fieldset>
                                                </div>
                                        </div>

                                        <div class="col-6">
                                               <div class="col-md-12">
                                                <span>Heizkörper:</span>
                                                    <fieldset class="form-group">
                                                        <select class="form-control" id="radiator" name="radiator">
                                                            <option value="Yes">Ja</option>
                                                            <option value="NO"> Nein</option>
                                                        </select>
                                                    </fieldset>
                                                </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="col-md-12">
                                                <span>Vorlauftemperatur:</span>
                                                <select name="flow_temp" class="form-control" id="flow_temp">

                                                    <option value="60">60 °C</option>
                                                    <option value="55">55 °C</option>
                                                    <option value="50">50 °C</option>
                                                    <option value="45">45 °C</option>
                                                    <option value="40">40 °C</option>
                                                    <option value="35">35 °C</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="col-md-12">
                                                <span>Vorlauftemperatur:</span>
                                                <select name="underfloor_flow_temp" class="form-control" id="underfloor_flow_temp">

                                                    <option value="60">60 °C</option>
                                                    <option value="55">55 °C</option>
                                                    <option value="50">50 °C</option>
                                                    <option value="45">45 °C</option>
                                                    <option value="40">40 °C</option>
                                                    <option value="35">35 °C</option>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-6">
                                                <div class="col-md-12">
                                                    <span>Warmwasser:</span>
                                                        <select class="form-control" id="warm_water" name="warm_water">
                                                            <option value="Central">Zenteral</option>
                                                            <option value="Decentral"> Dezenteral</option>
                                                        </select>
                                                </div>
                                            </div>

                                        <div class="col-6">
                                            <div class="col-md-12">
                                                <span>Baujahr Heizung:</span>
                                                <input type="text"  id="heating_manufacture_year" value="{{$data->heating_manufacture_year }}"class="form-control" name="heating_manufacture_year" oninput="updateEfficiency()">
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                </thead>
                                                <tbody>
                                                        <tr>
                                                            <td>Spez. Wirkungsgrad: <span id="efficiency_result" style="font-weight: bold; color:#8fc73e !important;"> </span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Alter der Heizungsanlage: <span style="font-weight: bold; color:#8fc73e !important;" id="heating_age_label"> </span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Energieverlust in kWh/Jahr <span style="font-weight: bold; color:#8fc73e !important;" id="efficiency"> </span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Effektiver Heizenergiebedarf abzgl. des Wirkungsgradsverlust in kWh/Jahr
                                                                <span style="font-weight: bold; color:#8fc73e !important;" id="effective"></span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                               Anteil Heizung:<span style="font-weight: bold; color:#8fc73e !important;" id="warm_water_result"></span></br>
                                                                Anteil Warmwasser:<span style="font-weight: bold; color:#8fc73e !important;" id="heatpump_result"></span>
                                                            </td>
                                                        </tr>
                                            </tbody>
                                            </table>
                                        </div>


                                    </div>
                                </div>
                                <div class="modal-footer">
                                <button type="submit"  class="btn btn-primary">Aktualisieren</button>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
                <!-- End Delete Modal -->
            </div>
        </div>
        <div class="row">

            <div class="col-4">
                <div class="form-group row">
                   <div class="col-md-12">
                    <span>Personen Anzahl:</span>
                        <input type="text" disabled id="number_people" value="{{$data->number_people }}"class="form-control" name="number_people" >
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="form-group row">
                   <div class="col-md-12">
                    <span>Gebäudeart:</span>
                        <input type="text" disabled id="building_type" value="{{$data->building_type }}"class="form-control" name="building_type" >
                    </div>
                </div>
            </div>



            <div class="col-4">
                <div class="form-group row">
                   <div class="col-md-12">
                    <span>Wohnfläche:</span>
                        <input type="text" disabled id="living_space" value="{{$data->living_space }}"class="form-control" name="living_space" >
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="form-group row">
                   <div class="col-md-12">
                    <span>Baujahr:</span>
                        <input type="text" disabled id="construction_year" value="{{$data->construction_year }}"class="form-control" name="construction_year" >
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="form-group row">
                   <div class="col-md-12">
                    <span>Heizungsart:</span>
                        <input type="text" disabled id="heating_type" value="{{$data->heating_type }}"class="form-control" name="heating_type" >
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="form-group row">
                   <div class="col-md-12">
                    <span>Durschn. Verbrauch:</span>
                        <input type="text" disabled id="consumption" value="{{$data->consumption }}"class="form-control" name="consumption" >
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="form-group row">
                   <div class="col-md-12">
                    <span>Fussbodenheizung:</span>
                        <input type="text" disabled id="underfloor_heating" value="{{$data->underfloor_heating }}"class="form-control" name="underfloor_heating" >
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="form-group row">
                   <div class="col-md-12">
                    <span>Heizkörper:</span>
                        <input type="text" disabled id="radiator" value="{{$data->radiator }}"class="form-control" name="radiator" >
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="form-group row">
                   <div class="col-md-12">
                    <span>Baujahr Heizung:</span>
                        <input type="text" disabled id="heating_manufacture_year" value="{{$data->heating_manufacture_year }}"class="form-control" name="heating_manufacture_year" >
                    </div>
                </div>
            </div>

            <hr>
            <div class="col-4">
                <div class="form-group row">
                   <div class="col-md-12">
                    <span>Heizlast:</span>
                        <input type="text" disabled id="heating_load" value="{{$data->heating_load }} KW"class="form-control" name="heating_load" >
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="form-group row">
                   <div class="col-md-12">
                    <span>Spez. Wirkungsgrad:</span>
                        <input type="text" disabled id="efficiency" value="{{$data->efficiency * 100}}%"class="form-control" name="efficiency" >
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="form-group row">
                   <div class="col-md-12">
                    <span>Heizlastberechnung </span>
                        <input type="text" disabled id="heating_output" value="{{$data->heating_output}}"class="form-control" name="heating_output" style="    background: #ff5555;">
                    </div>
                </div>
            </div>



        </div>
        @section('script')

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
            const heatingYearInput = document.getElementById('heating_manufacture_year');
            const heatingAgeLabel = document.getElementById('heating_age_label');
            const efficiencyResult = document.getElementById('efficiency_result');
            const effectiveDisplay = document.getElementById('effective');
            const efficiencyDisplay = document.getElementById('efficiency');
            const consumption = document.getElementById('consumption');
            const warm = document.getElementById('warm_water');
            const warmwaterResult = document.getElementById('warm_water_result');
            const heatpumpResult = document.getElementById('heatpump_result');
            const numberPeople = document.getElementById('number_people');

            let previousNumberPeople = numberPeople.value;

            function updateHeatingAge() {
                const currentYear = new Date().getFullYear();
                const heatingYear = parseInt(heatingYearInput.value);

                if (!isNaN(heatingYear)) {
                    const age = currentYear - heatingYear;
                    heatingAgeLabel.textContent = ` ${age} Jahr(e) Alt`;
                } else {
                    heatingAgeLabel.textContent = ``; // Handle invalid input
                }
            }

            function calculateEfficiency(year) {
                if (year < 1980) {
                    return 65;
                } else if (year < 1990) {
                    return 75;
                } else if (year < 2000) {
                    return 83;
                } else if (year < 2010) {
                    return 88;
                } else if (year < 2020) {
                    return 92;
                } else {
                    return 96;
                }
            }

            function updateEfficiency() {
                const heatingYear = parseInt(heatingYearInput.value);

                if (!isNaN(heatingYear)) {
                    const efficiencyValue = calculateEfficiency(heatingYear);
                    efficiencyResult.innerText = `${efficiencyValue}%`;

                    const consumptionValue = parseInt(consumption.value);
                    if (!isNaN(consumptionValue)) {
                        const effectiveTotal = (efficiencyValue / 100) * consumptionValue;
                        const remainingAmount = consumptionValue - effectiveTotal;
                        const effectivePercentage = (effectiveTotal / consumptionValue) * 100;
                        const remainingPercentage = (remainingAmount / consumptionValue) * 100;

                        if (warm.value === "Central") {
                            const heatpumpAmount = (82 / 100) * effectiveTotal;
                            const warmwaterAmount = (18 / 100) * effectiveTotal;
                            effectiveDisplay.textContent = `(Mit Warmwasser): ${effectiveTotal.toFixed(0)} (${effectivePercentage.toFixed(0)}%)`;
                            efficiencyDisplay.textContent = `${remainingAmount.toFixed(2)} (${remainingPercentage.toFixed(2)}%)`;
                            heatpumpResult.textContent = ` ${heatpumpAmount.toFixed(0)} (${(heatpumpAmount / effectiveTotal * 100).toFixed(0)}%)`;
                            warmwaterResult.textContent = ` ${warmwaterAmount.toFixed(0)} (${(warmwaterAmount / effectiveTotal * 100).toFixed(0)}%)`;
                          numberPeople.value = previousNumberPeople;
                            numberPeople.disabled = false;
                        } else {
                            effectiveDisplay.textContent = `(Ohne Warmwasser) ${effectiveTotal.toFixed(2)} (${effectivePercentage.toFixed(0)}%)`;
                            efficiencyDisplay.textContent = `${remainingAmount.toFixed(0)} (${remainingPercentage.toFixed(0)}%)`;
                            heatpumpResult.textContent = ``;
                            warmwaterResult.textContent = ``;

                            previousNumberPeople = numberPeople.value;
                            numberPeople.value = 0;
                            numberPeople.disabled = true;
                        }
                    } else {
                        effectiveDisplay.textContent = '';
                        efficiencyDisplay.textContent = '';
                        heatpumpResult.textContent = '';
                        warmwaterResult.textContent = '';
                    }
                } else {
                    efficiencyResult.innerText = '';
                    effectiveDisplay.textContent = '';
                    efficiencyDisplay.textContent = '';
                    heatpumpResult.textContent = '';
                    warmwaterResult.textContent = '';
                }
            }

            heatingYearInput.addEventListener('input', () => {
                updateHeatingAge();
                updateEfficiency();
            });

            consumption.addEventListener('input', updateEfficiency);
            warm.addEventListener('change', updateEfficiency);

            // Initial calculation
            updateHeatingAge();
            updateEfficiency();
        });
</script>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
                const radiator = document.getElementById('radiator');
                const flowTemp = document.getElementById('flow_temp');

                function updateFlowTemp() {
                    if (radiator.value === "Yes") {
                        flowTemp.value = "50";
                    } else {
                        flowTemp.value = "35";
                    }
                }

                // Run the function once on DOMContentLoaded
                updateFlowTemp();

                // Add event listener for change on the radiator dropdown
                radiator.addEventListener('change', updateFlowTemp);
            });
    </script>
        @endsection
