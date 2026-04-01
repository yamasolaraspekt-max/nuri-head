 <div class="default-collapse collapse-bordered">
    
    <div class="cards collapse-header"> 
    <form id="pv_form_store">
        @csrf
        <input type="hidden" name="customer_id" id="pv_customer_id" value="{{ $customer->id }}">
        <input type="hidden" name="alternative_id" id="pv_alternative_id" value="{{ $alternative->id }}">

        <div class="cards collapse-header">

        <div id="headingCollapse1" class="card-header collapsed coll" data-toggle="collapse" role="button" data-target="#collapse0">
                <span class="lead collapse-title"><strong>OBJEKTDATEN</strong></span>
                  <span id="pv-progress" class="col-xl-5 col-xl-5 flex_me float-right"> 
                        <div class="col-md-7">
                            <div class="progress progress-bar-primary progress-lg">
                                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                                    <span id="percent">0%</span>
                                </div>
                            </div>
                        </div>
                    </span>

            </div>
            <div id="collapse0" class="collapse">
                <div class="card-content">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-6 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <article> 
                                              <div class="row mb-1">
                                                <div class="col-md-6"><strong>Intention</strong></div>
                                                <div class="col-md-6">
                                                    <select class="form-control editable" data-field="intention">
                                                        <option value="Interesse"   >Interesse</option>
                                                        <option value="vorhanden"  >Vorhanden</option>
                                                        <option value="Erweiterung" >Erweiterung</option>
                                                        <option value="später" >Später</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Dachtyp</strong></div>
                                                <div class="col-md-6">
                                                    <select class="form-control editable" data-field="roof_type">
                                                        <option value="EFH" @if($alternative->objective == 'EFH') selected @endif >EFH</option>
                                                        <option value="MFH" @if($alternative->objective == 'MFH') selected @endif >MFH</option>
                                                        <option value="Neubau" @if($alternative->objective == 'Neubau') selected @endif >Neubau</option>
                                                        <option value="Sanierung" @if($alternative->objective == 'Sanierung') selected @endif >Sanierung</option>
                                                        <option value="Einzelmaßnahmen" @if($alternative->objective == 'Einzelmaßnahmen') selected @endif >Einzelmaßnahmen</option>
                                                    </select>
                                                </div>
                                            </div>
                                
                                           
                                              
                                        </article>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <article>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Alter des Hauses</strong></div>
                                                <div class="col-md-6 editable" data-field="house_year">{{ $alternative->house_year ?? 'N/V' }} </div> 
                                            </div>
                          
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Geschosse</strong></div>
                                                <div class="col-md-6 editable" data-field="number_stories">{{ $alternative->number_stories ?? 'N/V' }}</div>
                                            </div>
                                            
                                             <div class="row mb-1">
                                                <div class="col-md-6"><strong>Anzahl WE</strong></div>
                                                <div class="col-md-6 editable" data-field="number_we">{{ $alternative->number_we ?? 'N/V' }}</div>
                                            </div>
                                        </article>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- Row end -->
                    </div>
                </div>
            </div>

            <!-- Energieverbrauchsdaten -->
            <div id="headingCollapse1" class="card-header collapsed coll" data-toggle="collapse" role="button" data-target="#collapse1">
                <span class="lead collapse-title"><strong>ENERGIEVERBRAUCHSDATEN</strong></span> 

            </div>
            <div id="collapse1" class="collapse">
                <div class="card-content">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-6 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <article>
                                          
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Stromverbrauch <small>kWh</small></strong></div>
                                                <div class="col-md-6 editable" data-field="annual_consumption">{{ $alternative->annual_consumption ?? 'N/V' }} </div> 
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Anzahl Zähler</strong></div>
                                                <div class="col-md-6 editable" data-field="number_of_meters">{{ $pv_checklist->number_of_meters ?? 'N/V' }}</div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>E-Auto vorhanden</strong></div>
                                                <div class="col-md-6 editable" data-field="electric_car">{{ $pv_checklist->electric_car ?? 'N/V' }}</div>
                                            </div>
                                        </article>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <article>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Wallbox gewünscht</strong></div>
                                                <div class="col-md-6 editable" data-field="wallbox_desired">{{ $pv_checklist->wallbox_desired ?? 'N/V' }}</div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Messeinrichtung Adapterplatte</strong></div>
                                                <div class="col-md-6 editable" data-field="meter_adapter_plate">{{ $pv_checklist->meter_adapter_plate ?? 'N/V' }}</div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Trennrelais</strong></div>
                                                <div class="col-md-6 editable" data-field="disconnect_relay">{{ $pv_checklist->disconnect_relay ?? 'N/V' }}</div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Geplantes Datum</strong></div> 
                                                <div class="col-md-6">
                                                <input type="date" value="{{ $pv_checklist->planned_date ?? 'N/V' }}" class="editable form-control" data-field="planned_date" >
                                                </div> 
                                            </div>
                                        </article>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- Row end -->
                    </div>
                </div>
            </div>

            <!-- Elektrischer Anschluss -->
            <div id="headingCollapse3" class="card-header collapsed coll" data-toggle="collapse" role="button" data-target="#collapse3">
                <span class="lead collapse-title"><strong>ELEKTRISCHER ANSCHLUSS</strong></span>
            </div>
            <div id="collapse3" class="collapse">
                <div class="card-content">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-6 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <article>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Position HAK</strong></div>
                                                <div class="col-md-6 editable" data-field="position_hak">{{ $pv_checklist->position_hak ?? 'N/V' }}</div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Abstand Wechselrichter</strong></div>
                                                <div class="col-md-6 editable" data-field="distance_inverter">{{ $pv_checklist->distance_inverter ?? 'N/V' }}</div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-6"><strong>Abstand neuer Zählerschrank</strong></div>
                                                <div class="col-md-6 editable" data-field="distance_new_meter_cabinet">{{ $pv_checklist->distance_new_meter_cabinet ?? 'N/V' }}</div>
                                            </div>
                                        </article>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- Row end -->
                    </div>
                </div>
            </div>  
        </div>
    </form>
            <div id="headingCollapse2" class="card-header collapsed coll" data-toggle="collapse" role="button" data-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                <span class="lead collapse-title" style="font-weight: bolder;">
                    DACH DATEN
                </span>
                <span class=" col-xl-5 col-xl-5 flex_me float-right"> 
                    <div class="col-md-7">
                        <div class="progress progress-bar-primary progress-lg">
                            <div class="progress-bar" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%;">
                                <span id="percent">0%</span>
                            </div>
                        </div>
                    </div>
                </span>
            </div>
            <div id="collapse2" role="tabpanel" aria-labelledby="headingCollapse2" class="collapse" style="">
                <div class="card-content">
                    <div class="card-body">
                        <div class="row">
                              @foreach ($pv_roof as $roof) 
                               <div class="col-xl-6 col-xl-6 col-12">
                                    <form id="roof_edit">
                                        @csrf 
                                        <input type="hidden" name="customer_id" id="roof_customer" value="{{$customer->id}}">
                                        <input type="hidden" name="alternative_id" id="roof_alternative" value="{{$alternative->id}}"> 
                                        <input type="hidden" name="roof_id" id="roof_id" value="{{$roof->id}}"> 
                                       
                                        <div class="card ">
                                            <div class="card-body"> 
                                                <article>
                                                    <div class="row mb-1">
                                                        <div class="col-md-6">Bezeichnung</div>
                                                        <div class="col-md-6 roof_editable" data-field="designation">{{ $roof->designation ?? '' }}</div>
                                                    </div>   
                                                    <div class="row mb-1">
                                                        <div class="col-md-6">Dacheindeckung</div>
                                                        <div class="col-md-6 pl-0">
                                                            <select class="roof_covering" name="roof_covering[0]" style="width:100%">
                                                                @foreach ($tiles as $tile)
                                                                    <option value="{{ $tile->product_id }}"
                                                                        data-image="{{ asset('images/products/'.$tile->image) }}"
                                                                        @if($tile->product_id == $roof->roof_covering) selected @endif
                                                                        data-roof-type="{{ $tile->roof_type }}">
                                                                        {{ $tile->product }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div> 
                                                    <div class="row mb-1">
                                                        <div class="col-md-6">Aufdachdämmung</div>
                                                        <div class="col-md-6 roof_editable" data-field="thickness_roof_insulation">{{ $roof->thickness_roof_insulation ?? 'Nein' }}</div>
                                                    </div>   
                                                    <div class="row mb-1">
                                                        <div class="col-md-6">Zwischensparrendämmung</div>
                                                        <div class="col-md-6 roof_editable" data-field="between_rafter_insulation">{{ $roof->between_rafter_insulation ?? 'Nein' }}</div>
                                                    </div> 
                                                    <div class="row mb-1">
                                                        <div class="col-md-6">Solarhalteziegel gewünscht</div>
                                                        <div class="col-md-6 roof_editable" data-field="solar_holding_tile_desired">ja</div>
                                                    </div>
                                                    <div class="row mb-1">
                                                        <div class="col-md-6">geliefert durch</div>
                                                        <div class="col-md-6 roof_editable" data-field="delivered_by">Dachdecker</div>
                                                    </div>
                                                    <div class="row mb-1">
                                                        <div class="col-md-6">Maße Dachfläche</div>
                                                        <div class="col-md-6 roof_editable" data-field="roof_area">15x10</div>
                                                    </div>
                                                    <div class="row mb-1">
                                                        <div class="col-md-6">Dachüberstand Sparren links</div>
                                                        <div class="col-md-6 roof_editable" data-field="rafter_overhang_left">45</div>
                                                    </div>
                                                    <div class="row mb-1">
                                                        <div class="col-md-6">Dachüberstand Sparren rechts</div>
                                                        <div class="col-md-6 roof_editable" data-field="rafter_overhang_right">45</div>
                                                    </div>
                                                    <div class="row mb-1">
                                                        <div class="col-md-6">Sparrenstärke</div>
                                                        <div class="col-md-6 roof_editable" data-field="rafter_thickness">45</div>
                                                    </div>
                                                    <div class="row mb-1">
                                                        <div class="col-md-6">Statik vorhanden</div>
                                                        <div class="col-md-6 roof_editable" data-field="structural_analysis_available">nein</div>
                                                    </div>
                                                    <div class="row mb-1">
                                                        <div class="col-md-6">Dachsanierung notwendig</div>
                                                        <div class="col-md-6 roof_editable" data-field="roof_renovation">{{$roof->roof_renovation}}</div>
                                                    </div>
                                                </article> 
                                            </div>
                                        </div> 
                                    </form>
                                </div>
                                @endforeach

                        </div>
                    </div>

                </div>
            </div>
 
    </div>
</div>

            <a type="button" class="btn btn-outline-primary square mt-2 waves-effect waves-light" href="{{ url('/lead_pv_edit/'.$customer->id.'/'.$alternative->id) }}">Bearbeiten</a>

 