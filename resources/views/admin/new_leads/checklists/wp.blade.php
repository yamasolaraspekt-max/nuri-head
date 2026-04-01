  <div  id="wp"  >
                        <article class="col-md-12 col-sm-12 col-12">
                            <form method="post" action="{{ action('App\Http\Controllers\WPChecklistController@store')}}">
                                @csrf
                                <div class="container"
                                    style="display: flex;flex-wrap: wrap;align-content: flex-start; background: white; box-shadow: 0px 0px 10px 2px #a2a2a2;">
                                    <div class="col-md-12" id="section_1">
                                        <div class="cards">
                                            <div class="card-body d-flex">
                                                <div class="col-md-2 image">
                                                    <img src="{{ asset('images/articles/icon-wp.png') }}"
                                                        alt="alternative" style="width: 128px;">
                                                </div>
                                                <input type="hidden" name="customer_id" value="{{ $customer->id}}">
                                                <input type="hidden" name="postcode" value="{{ request()->postcode }}">
                                                <div class="col-md-10 contents">
                                                    <h2 class="title" style="color: #74b2d3">WÄRMEPUMPE</h2>
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
                                                                                name="wp_intention"
                                                                                id="wp_intention_interest"
                                                                                value="Interesse">
                                                                            <label class="custom-control-label"
                                                                                for="wp_intention_interest">Interesse</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="wp_intention"
                                                                                id="wp_intention_available"
                                                                                value="vorhanden">
                                                                            <label class="custom-control-label"
                                                                                for="wp_intention_available">vorhanden</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="wp_intention"
                                                                                id="wp_intention_extension"
                                                                                value="Erweiterung">
                                                                            <label class="custom-control-label"
                                                                                for="wp_intention_extension">Erweiterung</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="wp_intention"
                                                                                id="wp_intention_spater"
                                                                                value="später">
                                                                            <label class="custom-control-label"
                                                                                for="wp_intention_spater">später</label>
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
                                    <section class="col-md-12" id="section_wp_2">
                                        <div class="cards">
                                            <div class="card-body"
                                                style="display: flex !important;flex-wrap: wrap;"> 
                                                <div class="col-12">
                                                    <div class="form-group row">   
                                                        <h4 class="bold ">Objektart</h4> 
                                                        <select name="wp_objective" id="" class="form-control">
                                                        <option value="">Bitte wählen</option>
                                                        <option value="EFH" @if($wp && $wp->wp_objective == "EFH") selected @endif>EFH</option>
                                                        <option value="MFH" @if($wp && $wp->wp_objective == "MFH") selected @endif>MFH</option>
                                                        <option value="Gewerbe" @if($wp && $wp->wp_objective == "Gewerbe") selected @endif>Gewerbe</option>
                                                        <option value="others" @if($wp && $wp->wp_objective == "Sonstigis") selected @endif>Sonstigis</option>
                                                    </select> 
                                                    </div>
                                                </div>

                                                <div class="col-6 col-md-6 col-12">
                                                    <div class="form-group">   
                                                        <h4 class="bold ">Objektzustand</h4>
                                                        <select name="wp_object" id="" class="form-control">
                                                        <option value="">Bitte wählen</option>
                                                        <option value="new" @if($wp && $wp->wp_object == "new") selected @endif>Neubau</option>
                                                        <option value="renovation" @if($wp && $wp->wp_object == "renovation") selected @endif>Sanierung</option>
                                                        <option value="individual measures" @if($wp && $wp->wp_object == "individual measures") selected @endif>Einzelmaßnahmen</option>
                                                        <option value="others" @if($wp && $wp->wp_object == "others") selected @endif>Sonstigis</option>
                                                    </select>

                                                    </div>
                                                </div>

                                                    <div class="col-6 col-md-6 col-12">
                                                    <div class="form-group">   
                                                        <h4 class="bold ">Heizungsart</h4> 
                                                    <select name="wp_heating_type" id="wp_heating_type" class="form-control">
                                                        <option value="">Bitte wählen</option>
                                                        <option value="1" @if($wp && $wp->wp_heating_type == "1") selected @endif>Fußbodenheizung</option>
                                                        <option value="2" @if($wp && $wp->wp_heating_type == "2") selected @endif>Heizkörper</option>
                                                        <option value="3" @if($wp && $wp->wp_heating_type == "3") selected @endif>Fußbodenheizung + Heizkörper</option>
                                                        <option value="4" @if($wp && $wp->wp_heating_type == "4") selected @endif>Keine</option>
                                                    </select>

                                                    </div>
                                                </div>  
                                            </div> 

                                                <div class="row">
                                                    <div class="col-12">
                                                        <div id="accordionWrapa10" role="tablist" aria-multiselectable="true">
                                                            <div class="cards collapse-icon accordion-icon-rotate"> 
                                                                <div class="card-content">
                                                                    <div class="card-body p-0"> 
                                                                        <div class="default-collapse collapse-bordered"> 
                                                                            <div class="cards collapse-header" id="underfloorC">
                                                                                <div id="heading6" class="card-header collapsed" data-toggle="collapse" role="button" data-target="#accordion6" aria-expanded="false" aria-controls="accordion6">
                                                                                    <span class="lead collapse-title">
                                                                                        <h2 class="bold section_title">Fußboden Heizkreise</h2>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="accordion6" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading6" class="collapse" aria-expanded="false">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <div class="row">
                                                                                                    <div class="table-responsive">
                                                                                                        <form id="heating_circuit_form">
                                                                                                            @csrf
                                                                                                            <table class="table" id="number_of_heating_circuits">
                                                                                                                <thead>
                                                                                                                    <tr>
                                                                                                                        <th>Anzahl Heizkreise</th> 
                                                                                                                        <th>Vorlauf ℃</th>
                                                                                                                        <th>Rücklauf ℃</th>
                                                                                                                        <th>Geschoß</th>
                                                                                                                        <th>Rohedeminsion</th>
                                                                                                                        <th>Rohematerial</th> 
                                                                                                                        <th>Aktion</th>
                                                                                                                    </tr>
                                                                                                                </thead>
                                                                                                                <tbody>
                                                                                                                    <tr id="heating_circuit">
                                                                                                                        <th scope="row">
                                                                                                                            <input type="text" name="heating[0][heating_circuit_number]" class="form-control" value="1" readonly>
                                                                                                                        </th> 
                                                                                                                        <td>
                                                                                                                            <input type="text" class="form-control" placeholder="Vorlauf" name="heating[0][flow_temperature]">
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                            <input type="text" class="form-control" placeholder="Rücklauf" name="heating[0][return_flow_temperature]">
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                            <select name="heating[0][room_story]" class="form-control">
                                                                                                                                <option value=""></option>
                                                                                                                                <option value="KG">KG</option>
                                                                                                                                <option value="EG">EG</option>
                                                                                                                                <option value="OG">OG</option>
                                                                                                                                <option value="DG">DG</option>
                                                                                                                            </select>
                                                                                                                        </td> 
                                                                                                                        <td>
                                                                                                                            <select name="heating[0][pipe_dimension]" class="form-control">
                                                                                                                                <option value=""></option>
                                                                                                                                <option value="12">12</option>
                                                                                                                                <option value="14">14</option>
                                                                                                                                <option value="16">16</option>
                                                                                                                                <option value="17">17</option>
                                                                                                                                <option value="18">18</option>
                                                                                                                                <option value="20">20</option>
                                                                                                                            </select>
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                            <select name="heating[0][pipe_material]" class="form-control">
                                                                                                                                <option value=""></option>
                                                                                                                                <option value="Kupfer">Kupfer</option>
                                                                                                                                <option value="Kunststoff">Kunststoff</option>
                                                                                                                            </select>
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                            <button type="button" class="btn btn-icon btn-warning add-heating-row">
                                                                                                                                <i class="feather icon-plus"></i>
                                                                                                                            </button>
                                                                                                                        </td>
                                                                                                                    </tr> 
                                                                                                                </tbody>
                                                                                                            </table> 
                                                                                                            <button type="button" class="btn btn-outline-success mr-1 mb-1 waves-effect waves-light" id="save_heating_cercuit">Speichern</button>
                                                                                                        </form>

                                                                                                        </div>

                                                                                                    <div class="table-responsive">
                                                                                                        <table class="table" id="number_of_heating_circuits_details">
                                                                                                            <thead>
                                                                                                                <tr>
                                                                                                                    <th>Anzahl Heizkreise</th> 
                                                                                                                    <th>Vorlauf ℃</th>
                                                                                                                    <th>Rücklauf ℃</th>
                                                                                                                    <th>Geschoß</th>
                                                                                                                    <th>Rohedeminsion</th>
                                                                                                                    <th>Rohematerial</th> 
                                                                                                                    <th>Aktion</th>
                                                                                                                </tr>
                                                                                                            </thead>
                                                                                                            <tbody>
                                                                                                                
                                                                                                            </tbody>
                                                                                                        </table> 

                                                                                                        <!-- Modal for adding/editing Heating Circuit -->
                                                                                                    

                                                                                                    <div class="modal fade" id="heatingCircuitModal" tabindex="-1" role="dialog" aria-labelledby="heatingCircuitModalLabel" aria-hidden="true">
                                                                                                        <div class="modal-dialog" role="document">
                                                                                                            <div class="modal-content">
                                                                                                                <div class="modal-header">
                                                                                                                    <h5 class="modal-title" id="heatingCircuitModalLabel">Heizkreis hinzufügen/bearbeiten</h5>
                                                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                        <span aria-hidden="true">&times;</span>
                                                                                                                    </button>
                                                                                                                </div>
                                                                                                                <div class="modal-body">
                                                                                                                    <form id="heatingCircuitForm">
                                                                                                                        <div class="form-group">
                                                                                                                            <label>Anzahl Heizkreise</label>
                                                                                                                            <input type="text" id="heating_circuit_number" class="form-control" name="heating_circuit_number" readonly>
                                                                                                                        </div>
                                                                                                                        <div class="form-group">
                                                                                                                            <label>Vorlauf ℃</label>
                                                                                                                            <input type="text" id="flow_temperature" class="form-control" name="flow_temperature">
                                                                                                                        </div>
                                                                                                                        <div class="form-group">
                                                                                                                            <label>Rücklauf ℃</label>
                                                                                                                            <input type="text" id="return_flow_temperature" class="form-control" name="return_flow_temperature">
                                                                                                                        </div>
                                                                                                                        <div class="form-group">
                                                                                                                            <label>Geschoß</label>
                                                                                                                            <select id="room_story" class="form-control" name="room_story">
                                                                                                                                <option value=""></option>
                                                                                                                                <option value="KG">KG</option>
                                                                                                                                <option value="EG">EG</option>
                                                                                                                                <option value="OG">OG</option>
                                                                                                                                <option value="DG">DG</option>
                                                                                                                            </select>
                                                                                                                        </div>
                                                                                                                        <div class="form-group">
                                                                                                                            <label>Rohedeminsion</label>
                                                                                                                            <select id="pipe_dimension" class="form-control" name="pipe_dimension">
                                                                                                                                <option value=""></option>
                                                                                                                                <option value="12">12</option>
                                                                                                                                <option value="14">14</option>
                                                                                                                                <option value="16">16</option>
                                                                                                                                <option value="18">18</option>
                                                                                                                                <option value="20">20</option>
                                                                                                                            </select>
                                                                                                                        </div>
                                                                                                                        <div class="form-group">
                                                                                                                            <label>Rohematerial</label>
                                                                                                                            <select id="pipe_material" class="form-control" name="pipe_material">
                                                                                                                                <option value=""></option>
                                                                                                                                <option value="Kupfer">Kupfer</option>
                                                                                                                                <option value="Kunststoff">Kunststoff</option>
                                                                                                                            </select>
                                                                                                                        </div>
                                                                                                                        <input type="hidden" id="heating_circuit_id">
                                                                                                                    </form>
                                                                                                                </div>
                                                                                                                <div class="modal-footer">
                                                                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                                                                                                                    <button type="button" id="saveHeatingCircuit" class="btn btn-primary">Speichern</button>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>  
                                                                                                    </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>  
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div id="accordionWrapa10" role="tablist" aria-multiselectable="true">
                                                            <div class="cards collapse-icon accordion-icon-rotate"> 
                                                                <div class="card-content">
                                                                    <div class="card-body p-0"> 
                                                                        <div class="default-collapse collapse-bordered">  

                                                                            <div class="cards collapse-header" id="rediatorC">
                                                                                <div id="headingr" class="card-header collapsed" data-toggle="collapse" role="button" data-target="#accordionr" aria-expanded="false" aria-controls="accordionr">
                                                                                    <span class="lead collapse-title">
                                                                                        <h2 class="bold section_title">Heizkörper</h2>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="accordionr" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="headingr" class="collapse" aria-expanded="false">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <div class="row">  
                                                                                                    <div class="table-responsive">
                                                                                                        <a type="button" class="btn btn-outline-success waves-effect waves-light float-right mb-1" href="{{ url('radiator_config_create/'.$customer->id.'/'.$customer->postcode.'/'.request()->address_no) }}"> <i class="feather icon-plus"></i> Neue / Bearbiten </a>
                                                                                                        <table class="table" id="rediator_details">
                                                                                                            <thead>
                                                                                                                <tr>
                                                                                                                    <th>#</th> 
                                                                                                                    <th>ETAGE</th>
                                                                                                                    <th>RAUM</th>
                                                                                                                    <th>TYP</th>
                                                                                                                    <th>GRÖSSE <small><code>HxBxT</code></small></th>
                                                                                                                    <th>NISCHE</th> 
                                                                                                                    <th>ANSCHLÜSSE</th> 
                                                                                                                    <th>Aktion</th>
                                                                                                                </tr>
                                                                                                            </thead>
                                                                                                            <tbody>
                                                                                                                @foreach ($rediators as $red) 
                                                                                                                <tr>
                                                                                                                    <td>
                                                                                                                        <a href="" data-toggle="modal" data-target="#preview{{$red->id}}">
                                                                                                                        <div class="avatar mr-1 avatar-xl" >
                                                                                                                            <img src="{{ asset('images/radiators/'.$red->image) }}" >
                                                                                                                        </div> 
                                                                                                                        </a>
                                                                                                                        <div class="modal fade text-left" id="preview{{$red->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel150" style="display: none;" aria-hidden="true">
                                                                                                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                                                                <div class="modal-content">
                                                                                                                                    <div class="modal-header bg-dark white">
                                                                                                                                        <h5 class="modal-title" id="myModalLabel150">{{ $red->floor }} - {{ $red->room }}</h5>
                                                                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                                            <span aria-hidden="true">×</span>
                                                                                                                                        </button>
                                                                                                                                    </div>
                                                                                                                                    <div class="modal-body">
                                                                                                                                        <img src="{{ asset('images/radiators/'.$red->image) }}"  style="width:300px;"> 
                                                                                                                                    </div>
                                                                                                                                    <div class="modal-footer">
                                                                                                                                        <button type="button" class="btn btn-dark waves-effect waves-light" data-dismiss="modal">OK</button>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </td>
                                                                                                                    <td>{{ $red->floor }}</td>
                                                                                                                    <td>{{ $red->room }}</td>
                                                                                                                    <td>{{ $red->type }}</td>
                                                                                                                    <td>{{ $red->height }} x {{ $red->width }} x {{ $red->depth }}</td>
                                                                                                                    <td>{{ $red->niche_right }} x {{ $red->niche_left }} x {{ $red->niche_top }} {{ $red->niche_bottom }}</td>
                                                                                                                    <td>
                                                                                                                        <ul> 
                                                                                                                            <li><strong>Vorlaufventil</strong> {{ $red->supply_valve }} @if($red->supply_valve_presettable) (voreinstellbar) @endif</li>
                                                                                                                            <li><strong>Rücklaufventil</strong> {{ $red->return_valve }} @if($red->return_valve_present) (vorhanden) @endif</li>
                                                                                                                            <li><strong>Bauform</strong> {{ $red->design }}</li>
                                                                                                                            <li><strong>Thermostatkopf</strong> {{ $red->renew_thermostat_head ? 'muss erneuert werden' : 'muss nicht erneuert werden' }}</li>
                                                                                                                            <li><strong>Steckdose</strong> @if($red->has_socket) vorhanden, Entfernung {{ $red->socket_distance }} m @else nicht vorhanden @endif</li>
                                                                                                                        </ul>
                                                                                                                    </td>  
                                                                                                                    <td>
                                                                                                                        <a type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1 waves-effect waves-light" href="{{ url('radiator_config_delete/'.$red->id) }}"><i class="feather icon-trash"></i></a>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                @endforeach

                                                                                                            </tbody>
                                                                                                        </table> 

                                                                                                        <!-- Modal for adding/editing Heating Circuit --> 
                                                                                                    
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div> 
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12"><hr></div>
                                                <div class="row mt-2">
                                                    <div class="col-12">
                                                        <div id="accordionWrapa10" role="tablist" aria-multiselectable="true">
                                                            <div class="cards collapse-icon accordion-icon-rotate"> 
                                                                <div class="card-content">
                                                                    <div class="card-body p-0"> 
                                                                        <div class="default-collapse collapse-bordered"> 
                                                                            <div class="cards collapse-header">
                                                                                <div id="heading11" class="card-header" data-toggle="collapse" role="button" data-target="#accordion10" aria-expanded="true" aria-controls="accordion10">
                                                                                    <span class="lead collapse-title">
                                                                                        <h2 class="bold section_title ">OBJEKTDATEN</h2>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="accordion10" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading11" class="collapse show" style="">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <div class="row">
                                                                                                <div class="col-6">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-4">
                                                                                                            <h4 class="bold">Baujahr </h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-8 textbox-container">
                                                                                                     <input type="text" class="form-control" 
                                                                                                        name="construction_year" id="construction_year" 
                                                                                                        value="{{ old('construction_year', $wp ? $wp->construction_year : $customer->construction_year) }}">&nbsp;
                                                                                                        <label
                                                                                                                    id="house_age_label"> </label> </span>
                                                                                                            <div class="indicator"></div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-6">
                                                                                                    <div class="form-group row ">
                                                                                                        <div class="col-md-4">
                                                                                                            <h4 class="bold">beheizte Wohnfläche</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-8 flex_me">
                                                                                                            <input type="text" class="form-control"
                                                                                                                name="living_space" value="{{ old('living_space', $wp ? $wp->living_space: 0)}}">
                                                                                                            <span> m²</span> </span>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="col-6">
                                                                                                    <div class="form-group row ">
                                                                                                        <div class="col-md-4">
                                                                                                            <h4 class="bold">Nutzfläche</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-8 flex_me">
                                                                                                            <input type="text" class="form-control" value="{{ old('unusable_space', $wp ? $wp->unusable_space: 0)}}"
                                                                                                                name="unusable_space">
                                                                                                            <span> m²</span> </span>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="col-6">
                                                                                                    <div class="form-group row ">
                                                                                                        <div class="col-md-4">
                                                                                                            <h4 class="bold">Anzahl Personen</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-8">
                                                                                                            <input type="text" class="form-control"
                                                                                                                name="number_people" id="number_people"  value="{{ old('number_people', $wp ? $wp->number_people: 0)}}">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-6">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-4">
                                                                                                            <h4 class="bold">Anzahl WE</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-8 textbox-container">
                                                                                                            <input type="text" class="form-control textbox"
                                                                                                                name="wp_number_we" value="{{ old('wp_number_we', $wp ? $wp->wp_number_we: 0)}}">
                                                                                                            <div class="indicator"></div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="col-6">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-4">
                                                                                                            <h4 class="bold">Anzahl Geschoß</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-8">
                                                                                                            <input type="text" class="form-control" value="{{ old('wp_number_stories', $wp ? $wp->wp_number_stories: 0)}}"
                                                                                                                name="wp_number_stories">
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
                                                                                                                            <div
                                                                                                                                class="custom-control custom-radio">
                                                                                                                                <input type="checkbox"
                                                                                                                                    class="custom-control-input"
                                                                                                                                    name="glass1" id="1-fach"
                                                                                                                                     @if($wp && $wp->glass1=="on") checked @endif >
                                                                                                                                <label class="custom-control-label"
                                                                                                                                    for="1-fach">1-fach</label>
                                                                                                                            </div>
                                                                                                                        </fieldset>
                                                                                                                    </li>
                                                                                                                    <li class="d-inline-block mr-1">
                                                                                                                        <fieldset>
                                                                                                                            <div
                                                                                                                                class="custom-control custom-radio">
                                                                                                                                <input type="checkbox"
                                                                                                                                    class="custom-control-input"
                                                                                                                                    name="glass2" id="glass_2" @if($wp && $wp->glass2=="on") checked @endif>
                                                                                                                                <label class="custom-control-label"
                                                                                                                                    for="glass_2">2-fach</label>
                                                                                                                            </div>
                                                                                                                        </fieldset>
                                                                                                                    </li>
                                                                                                                    <li class="d-inline-block mr-1">
                                                                                                                        <fieldset>
                                                                                                                            <div
                                                                                                                                class="custom-control custom-radio">
                                                                                                                                <input type="checkbox"
                                                                                                                                    class="custom-control-input"    @if($wp && $wp->glass3=="on") checked @endif
                                                                                                                                    name="glass3" id="glass_3" >
                                                                                                                                <label class="custom-control-label"
                                                                                                                                    for="glass_3">3-fach</label>
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
                                                                                                                            <div
                                                                                                                                class="custom-control custom-radio">
                                                                                                                                <input type="radio"
                                                                                                                                    class="custom-control-input"
                                                                                                                                    name="window_margin"
                                                                                                                                    id="window_margin_alu"  @if($wp && $wp->window_margin=="Alu") checked @endif
                                                                                                                                    value="Alu">
                                                                                                                                <label class="custom-control-label"
                                                                                                                                    for="window_margin_alu">Alu</label>
                                                                                                                            </div>
                                                                                                                        </fieldset>
                                                                                                                    </li>
                                                                                                                    <li class="d-inline-block mr-1">
                                                                                                                        <fieldset>
                                                                                                                            <div
                                                                                                                                class="custom-control custom-radio">
                                                                                                                                <input type="radio"
                                                                                                                                    class="custom-control-input"
                                                                                                                                    name="window_margin"
                                                                                                                                    id="window_margin_kunststoff" @if($wp && $wp->window_margin=="Kunststoff") checked @endif
                                                                                                                                    value="Kunststoff">
                                                                                                                                <label class="custom-control-label"
                                                                                                                                    for="window_margin_kunststoff">Kunststoff</label>
                                                                                                                            </div>
                                                                                                                        </fieldset>
                                                                                                                    </li>
                                                                                                                    <li class="d-inline-block mr-1">
                                                                                                                        <fieldset>
                                                                                                                            <div
                                                                                                                                class="custom-control custom-radio">
                                                                                                                                <input type="radio"
                                                                                                                                    class="custom-control-input"
                                                                                                                                    name="window_margin"
                                                                                                                                    id="window_margin_holz" @if($wp && $wp->window_margin=="Holz") checked @endif
                                                                                                                                    value="Holz">
                                                                                                                                <label class="custom-control-label"
                                                                                                                                    for="window_margin_holz">Holz</label>
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
                                                                                                                <input type="text" class="form-control"
                                                                                                                    name="insulation_thickness" value="{{ old('insulation_thickness', $wp ? $wp->insulation_thickness: 0)}}">
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col-12">
                                                                                                    </div>

                                                                                                    <div class="col-6">
                                                                                                        <div class="form-group row ">
                                                                                                            <div class="col-md-4">
                                                                                                                <h4 class="bold ">Mauerart</h4>
                                                                                                            </div>
                                                                                                            <div class="col-md-8 flex_me">
                                                                                                                <select name="wall_type" class="form-control" id="">
                                                                                                                    <option value="Mauerwerk" @if($wp && $wp->wall_type == "Mauerwerk") selected @endif >Mauerwerk</option>
                                                                                                                    <option value="Holz" @if($wp && $wp->wall_type == "Holz") selected @endif>Holz</option>
                                                                                                                    <option value="Massivbau" @if($wp && $wp->wall_type == "Massivbau") selected @endif>Massivbau</option>
                                                                                                                </select>

                                                                                                                </select>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>

                                                                                                    <div class="col-6">
                                                                                                        <div class="form-group row ">
                                                                                                            <div class="col-md-4">
                                                                                                                <h4 class="bold ">Mauer-stärke</h4>
                                                                                                            </div>
                                                                                                            <div class="col-md-8 flex_me">
                                                                                                                <input type="text" class="form-control" value="{{ old('wall_thickness', $wp ? $wp->wall_thickness: 0)}}"
                                                                                                                    name="wall_thickness">&nbsp;<span>cm</span>
                                                                                                                </select>
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
                                                                                                                            <div
                                                                                                                                class="custom-control custom-radio">
                                                                                                                                <input type="radio"
                                                                                                                                    class="custom-control-input"
                                                                                                                                    name="wp_insulation"
                                                                                                                                    id="wp_insulation_ja" @if($wp && $wp->wp_insulation=="ja") checked @endif
                                                                                                                                    value="ja">
                                                                                                                                <label class="custom-control-label"
                                                                                                                                    for="wp_insulation_ja">ja</label>
                                                                                                                            </div>
                                                                                                                        </fieldset>
                                                                                                                    </li>
                                                                                                                    <li class="d-inline-block mr-1">
                                                                                                                        <fieldset>
                                                                                                                            <div
                                                                                                                                class="custom-control custom-radio">
                                                                                                                                <input type="radio"
                                                                                                                                    class="custom-control-input"
                                                                                                                                    name="wp_insulation"
                                                                                                                                    id="wp_insulation_nein"  @if($wp && $wp->wp_insulation=="nein") checked @endif
                                                                                                                                    value="nein">
                                                                                                                                <label class="custom-control-label"
                                                                                                                                    for="wp_insulation_nein">nein</label>
                                                                                                                            </div>
                                                                                                                        </fieldset>
                                                                                                                    </li>
                                                                                                                    <li class="d-inline-block mr-1"
                                                                                                                        style="width:330px">
                                                                                                                        <div class="form-group row">
                                                                                                                            <div class="col-md-4">
                                                                                                                                <h4 class="bold">Stärke</h4>
                                                                                                                            </div>
                                                                                                                            <div class="col-md-8">
                                                                                                                                <input type="text"
                                                                                                                                    class="form-control" value="{{ old('wp_insulation_strength', $wp ? $wp->wp_insulation_strength: 0)}}"
                                                                                                                                    name="wp_insulation_strength">
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
                                                                                                                            <div
                                                                                                                                class="custom-control custom-radio">
                                                                                                                                <input type="radio"
                                                                                                                                    class="custom-control-input"
                                                                                                                                    name="wp_rafter"  @if($wp && $wp->wp_rafter=="ja") checked @endif
                                                                                                                                    id="wp_rafter_ja" value="ja">
                                                                                                                                <label class="custom-control-label"
                                                                                                                                    for="wp_rafter_ja">ja</label>
                                                                                                                            </div>
                                                                                                                        </fieldset>
                                                                                                                    </li>
                                                                                                                    <li class="d-inline-block mr-1">
                                                                                                                        <fieldset>
                                                                                                                            <div
                                                                                                                                class="custom-control custom-radio">
                                                                                                                                <input type="radio"
                                                                                                                                    class="custom-control-input"
                                                                                                                                    name="wp_rafter" @if($wp && $wp->wp_rafter=="nein") checked @endif
                                                                                                                                    id="wp_rafter_nein"
                                                                                                                                    value="nein">
                                                                                                                                <label class="custom-control-label"
                                                                                                                                    for="wp_rafter_nein">nein</label>
                                                                                                                            </div>
                                                                                                                        </fieldset>
                                                                                                                    </li>
                                                                                                                    <li class="d-inline-block mr-1"
                                                                                                                        style="width:330px">
                                                                                                                        <div class="form-group row">
                                                                                                                            <div class="col-md-4">
                                                                                                                                <h4 class="bold">Stärke</h4>
                                                                                                                            </div>
                                                                                                                            <div class="col-md-8">
                                                                                                                                <input type="text"
                                                                                                                                    class="form-control" value="{{ old('wp_rafter_strength', $wp ? $wp->wp_rafter_strength: 0)}}"
                                                                                                                                    name="wp_rafter_strength">
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </li>
                                                                                                                </ul>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div> 

                                                                                                        <div class="col-6">
                                                                                                            <div class="form-group row">
                                                                                                                <div class="col-md-4">
                                                                                                                    <h4 class="bold">Anzahl Bäder</h4>
                                                                                                                </div>
                                                                                                                <div class="col-md-8">
                                                                                                                    <input type="text" class="form-control" value="{{ old('wp_bathrooms', $wp ? $wp->wp_bathrooms: 0)}}"
                                                                                                                        name="wp_bathrooms">
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>

                                                                                                        

                                                                                                        <div class="col-12">
                                                                                                            <div class="form-group row">
                                                                                                                <div class="col-md-2">
                                                                                                                    <h4 class="bold ">Badewanne</h4>
                                                                                                                </div>
                                                                                                                <div class="col-md-10">
                                                                                                                    <ul class="list-unstyled mb-0">
                                                                                                                        <li class="d-inline-block mr-1">
                                                                                                                            <fieldset>
                                                                                                                                <div
                                                                                                                                    class="custom-control custom-radio">
                                                                                                                                    <input type="radio"
                                                                                                                                        class="custom-control-input"
                                                                                                                                        name="wp_bathtub"
                                                                                                                                        id="wp_buthtub_no" @if($wp && $wp->wp_bathtub=="nein") checked @endif
                                                                                                                                        value="nein">
                                                                                                                                    <label class="custom-control-label"
                                                                                                                                        for="wp_buthtub_no">nein</label>
                                                                                                                                </div>
                                                                                                                            </fieldset>
                                                                                                                        </li>
                                                                                                                        <li class="d-inline-block mr-1">
                                                                                                                            <fieldset>
                                                                                                                                <div
                                                                                                                                    class="custom-control custom-radio">
                                                                                                                                    <input type="radio"
                                                                                                                                        class="custom-control-input"
                                                                                                                                        name="wp_bathtub" @if($wp && $wp->wp_bathtub=="ja") checked @endif
                                                                                                                                        id="wp_buthtub_yes" value="ja">
                                                                                                                                    <label class="custom-control-label"
                                                                                                                                        for="wp_buthtub_yes">Ja</label>
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
                                                                                                                                        class="form-control" value="{{ old('wp_bathtub_count', $wp ? $wp->wp_bathtub_count: 0)}}"
                                                                                                                                        name="wp_bathtub_count">
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </li>

                                                                                                                        <li class="d-inline-blocks mr-1 "
                                                                                                                            style="width:330px">
                                                                                                                            <div class="form-group row ">
                                                                                                                                <div class="col-md-4">
                                                                                                                                    <h4 class="bold">Abmessung</h4>
                                                                                                                                </div>
                                                                                                                                <div class="col-md-8">
                                                                                                                                    <input type="text" value="{{ old('wp_bathtub_measure', $wp ? $wp->wp_bathtub_measure: 0)}}"
                                                                                                                                        class="form-control"
                                                                                                                                        name="wp_bathtub_measure">
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
                                                                                                                    <h4 class="bold ">Schwimmbad</h4>
                                                                                                                </div>
                                                                                                                <div class="col-md-10">
                                                                                                                    <ul class="list-unstyled mb-0">
                                                                                                                        <li class="d-inline-block mr-1">
                                                                                                                            <fieldset>
                                                                                                                                <div
                                                                                                                                    class="custom-control custom-radio">
                                                                                                                                    <input type="radio"
                                                                                                                                        class="custom-control-input"
                                                                                                                                        name="wp_swimming_pool"
                                                                                                                                        id="wp_swimming_pool_no" @if($wp && $wp->wp_swimming_pool=="nein") checked @endif
                                                                                                                                        value="nein">
                                                                                                                                    <label class="custom-control-label"
                                                                                                                                        for="wp_swimming_pool_no">nein</label>
                                                                                                                                </div>
                                                                                                                            </fieldset>
                                                                                                                        </li>
                                                                                                                        <li class="d-inline-block mr-1">
                                                                                                                            <fieldset>
                                                                                                                                <div
                                                                                                                                    class="custom-control custom-radio">
                                                                                                                                    <input type="radio"
                                                                                                                                        class="custom-control-input"
                                                                                                                                        name="wp_swimming_pool" @if($wp && $wp->wp_swimming_pool=="ja") checked @endif
                                                                                                                                        id="wp_swimming_pool_yes" value="ja">
                                                                                                                                    <label class="custom-control-label"
                                                                                                                                        for="wp_swimming_pool_yes">ja</label>
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
                                                                                                                                    <input type="text" value="{{ old('wp_swimming_pool_count', $wp ? $wp->wp_swimming_pool_count: 0)}}"
                                                                                                                                        class="form-control"
                                                                                                                                        name="wp_swimming_pool_count">
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </li>
                                                                                                                    </ul>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div> 
                                                                                                        <div class="col-12">
                                                                                                            <hr>
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
                                                                                                                                <div
                                                                                                                                    class="custom-control custom-radio">
                                                                                                                                    <input type="radio"
                                                                                                                                        class="custom-control-input"
                                                                                                                                        name="solor" id="solar_no" @if($wp && $wp->solor=="nein") checked @endif
                                                                                                                                        checked value="nein">
                                                                                                                                    <label class="custom-control-label"
                                                                                                                                        for="solar_no">nein</label>
                                                                                                                                </div>
                                                                                                                            </fieldset>
                                                                                                                        </li>
                                                                                                                        <li class="d-inline-block mr-1">
                                                                                                                            <fieldset>
                                                                                                                                <div
                                                                                                                                    class="custom-control custom-radio">
                                                                                                                                    <input type="radio"
                                                                                                                                        class="custom-control-input"
                                                                                                                                        name="solor" id="solor_yes" @if($wp && $wp->solor=="ja") checked @endif
                                                                                                                                        value="ja">
                                                                                                                                    <label class="custom-control-label"
                                                                                                                                        for="solor_yes">ja</label>
                                                                                                                                </div>
                                                                                                                            </fieldset>
                                                                                                                        </li>

                                                                                                                        <li class="d-inline-blocks mr-1"
                                                                                                                            style="width:330px">
                                                                                                                            <div class="form-group row">
                                                                                                                                <div class="col-md-4">
                                                                                                                                    <h4 class="bold">Anzahl der
                                                                                                                                        Kollektoren</h4>
                                                                                                                                </div>
                                                                                                                                <div class="col-md-8 flex_me">
                                                                                                                                    <input type="text" 
                                                                                                                                        class="form-control"  value="{{ old('number_collector', $wp ? $wp->number_collector: 0)}}"
                                                                                                                                        name="number_collector">
                                                                                                                                    &nbsp;<span>Stk</span>
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
                                                                                                                    <h4 class="bold ">Kamin vorhanden</h4>
                                                                                                                </div>
                                                                                                                <div class="col-md-10">
                                                                                                                    <ul class="list-unstyled mb-0">
                                                                                                                        <li class="d-inline-block mr-1">
                                                                                                                            <fieldset>
                                                                                                                                <div
                                                                                                                                    class="custom-control custom-radio">
                                                                                                                                    <input type="radio"
                                                                                                                                        class="custom-control-input"
                                                                                                                                        name="chimney" id="chimney_no" @if($wp && $wp->chimney=="nein") checked @endif
                                                                                                                                        checked value="nein">
                                                                                                                                    <label class="custom-control-label"
                                                                                                                                        for="chimney_no">nein</label>
                                                                                                                                </div>
                                                                                                                            </fieldset>
                                                                                                                        </li>
                                                                                                                        <li class="d-inline-block mr-1">
                                                                                                                            <fieldset>
                                                                                                                                <div
                                                                                                                                    class="custom-control custom-radio">
                                                                                                                                    <input type="radio"
                                                                                                                                        class="custom-control-input"
                                                                                                                                        name="chimney" id="chimney_yes" @if($wp && $wp->chimney=="ja") checked @endif
                                                                                                                                        value="ja">
                                                                                                                                    <label class="custom-control-label"
                                                                                                                                        for="chimney_yes">ja</label>
                                                                                                                                </div>
                                                                                                                            </fieldset>
                                                                                                                        </li>

                                                                                                                        <li class="d-inline-blocks mr-1"
                                                                                                                            style="width:330px">
                                                                                                                            <div class="form-group row">
                                                                                                                                <div class="col-md-4">
                                                                                                                                    <h4 class="bold">Verbrauch</h4>
                                                                                                                                </div>
                                                                                                                                <div class="col-md-8 flex_me">
                                                                                                                                    <input type="text"
                                                                                                                                        class="form-control" value="{{ old('chimney_usage', $wp ? $wp->chimney_usage: 0)}}"
                                                                                                                                        name="chimney_usage">
                                                                                                                                    &nbsp;<span> Holz/ m3 pro
                                                                                                                                        Jahr</span>
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
                                                                                                                    <h4 class="bold ">Heizlastberechnung vorhanden</h4>
                                                                                                                </div>
                                                                                                                <div class="col-md-10">
                                                                                                                    <ul class="list-unstyled mb-0">
                                                                                                                        <li class="d-inline-block mr-1">
                                                                                                                            <fieldset>
                                                                                                                                <div
                                                                                                                                    class="custom-control custom-radio">
                                                                                                                                    <input type="radio"
                                                                                                                                        class="custom-control-input"
                                                                                                                                        name="hlb_calc" id="hlb_calc_no" @if($wp && $wp->hlb_calc=="nein") checked @endif
                                                                                                                                          value="nein">
                                                                                                                                    <label class="custom-control-label"
                                                                                                                                        for="hlb_calc_no">nein</label>
                                                                                                                                </div>
                                                                                                                            </fieldset>
                                                                                                                        </li>
                                                                                                                        <li class="d-inline-block mr-1">
                                                                                                                            <fieldset>
                                                                                                                                <div
                                                                                                                                    class="custom-control custom-radio">
                                                                                                                                    <input type="radio"
                                                                                                                                        class="custom-control-input"
                                                                                                                                        name="hlb_calc" @if($wp && $wp->hlb_calc=="ja") checked @endif
                                                                                                                                        id="hlb_calc_yes" value="ja">
                                                                                                                                    <label class="custom-control-label"
                                                                                                                                        for="hlb_calc_yes">ja</label>
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
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="cards collapse-header">
                                                                                <div id="heading2" class="card-header collapsed" data-toggle="collapse" role="button" data-target="#accordion2" aria-expanded="false" aria-controls="accordion2">
                                                                                    <span class="lead collapse-title">
                                                                                            <h2 class="bold section_title">Energiekosten, Verbrauch, Daten</h2>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="accordion2" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading2" class="collapse" style="">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <div class="row">
                                                                                                <div class="table-responsive">
                                                                                                    <table class="table">
                                                                                                        <thead>
                                                                                                            <tr> 
                                                                                                                <th>Erstes Jahr</th>
                                                                                                                <th>Zweites Jahr</th>
                                                                                                                <th>Drittes Jahr</th>
                                                                                                                <th>Energieverbrauchseinheit</th>
                                                                                                                <th>Gesamt</th>
                                                                                                                <th>AVG</th>
                                                                                                            </tr>
                                                                                                        </thead>
                                                                                                        <tbody>
                                                                                                            <tr> 
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Erstes Jahr"  value="{{ old('energy_first_year_consumption', $wp ? $wp->energy_first_year_consumption: 0)}}"
                                                                                                                            name="energy_first_year_consumption" id="energy_first_year_consumption">
                                                                                                                </td>
                                                                                                                <td><input type="text" class="form-control" placeholder="Zweites Jahr" value="{{ old('energy_second_year_consumption', $wp ? $wp->energy_second_year_consumption: 0)}}"
                                                                                                                        name="energy_second_year_consumption" id="energy_second_year_consumption">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Drittes Jahr" value="{{ old('energy_third_year_consumption', $wp ? $wp->energy_third_year_consumption: 0)}}"
                                                                                                                        name="energy_third_year_consumption"  id="energy_third_year_consumption">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <select name="energy_consumption_type" id="energy_consumption_type" class="form-control">
                                                                                                                        <option  >Energieverbrauchseinheit</option>
                                                                                                                        <option value="kWh">kWh</option>
                                                                                                                        <option value="m³">m³</option>
                                                                                                                        <option value="Liter">Liter</option>  
                                                                                                                    </select>
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <fieldset>
                                                                                                                        <div class="input-group mb-1"> 
                                                                                                                            <input type="text" class="form-control"    name="energy_total_year_consumption"  id="energy_total_year_consumption" placeholder="Gesamt" aria-describedby="energy_consumption_type_lable"  value="{{ old('energy_total_year_consumption', $wp ? $wp->energy_total_year_consumption: 0)}}">
                                                                                                                            <div class="input-group-prepend">
                                                                                                                                <span class="input-group-text" id="energy_consumption_type_lable">kWh</span>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </fieldset>

                                                                                                                    <fieldset>
                                                                                                                        <div class="input-group"> 
                                                                                                                            <input type="text" class="form-control"  name="energy_avg_year_consumption"  id="energy_avg_year_consumption" placeholder="Durchschnittliche" aria-describedby="energy_consumption_type_lable" value="{{ old('energy_avg_year_consumption', $wp ? $wp->energy_avg_year_consumption: 0)}}">
                                                                                                                            <div class="input-group-prepend">
                                                                                                                                <span class="input-group-text" id="energy_consumption_type_lable">kWh</span>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </fieldset>  
                                                                                                                
                                                                                                                </td>
                                                                                                            </tr> 

                                                                                                            <tr> 
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Erstes Jahr Kosten" value="{{ old('energy_first_year_cost', $wp ? $wp->energy_first_year_cost: 0)}}"
                                                                                                                            name="energy_first_year_cost" id="energy_first_year_cost">
                                                                                                                </td>
                                                                                                                <td><input type="text" class="form-control" placeholder="Zweites Jahr Kosten"  value="{{ old('energy_second_year_cost', $wp ? $wp->energy_second_year_cost: 0)}}"
                                                                                                                        name="energy_second_year_cost" id="energy_second_year_cost" > 
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Drittes Jahr Kosten"  value="{{ old('energy_third_year_cost', $wp ? $wp->energy_third_year_cost: 0)}}"
                                                                                                                        name="energy_third_year_cost" id="energy_third_year_cost">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                <input type="text" value="Euro" class="form-control" readonly>  
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <fieldset>
                                                                                                                        <div class="input-group mb-1"> 
                                                                                                                            <input type="text" class="form-control mb-1" placeholder="Gesamt Kosten" id="energy_total_year_cost"  value="{{ old('energy_total_year_cost', $wp ? $wp->energy_total_year_cost: 0)}}"
                                                                                                                                    name="energy_total_year_cost"  >
                                                                                                                            <div class="input-group-prepend">
                                                                                                                                <span class="input-group-text"  >€</span>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </fieldset>

                                                                                                                    <fieldset>
                                                                                                                        <div class="input-group mb-1"> 
                                                                                                                            <input type="text" class="form-control" placeholder="Durchschnittliche" id="energy_avg_year_cost"   value="{{ old('energy_avg_year_cost', $wp ? $wp->energy_avg_year_cost: 0)}}"
                                                                                                                        name="energy_avg_year_cost" >
                                                                                                                            <div class="input-group-prepend">
                                                                                                                                <span class="input-group-text">€</span>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </fieldset>

                                                                                                                </td>
                                                                                                            </tr>
                                                                                                            
                                                                                                            
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            
                                                                            <div class="cards collapse-header">
                                                                                <div id="heading4" class="card-header collapsed" data-toggle="collapse" role="button" data-target="#accordion4" aria-expanded="false" aria-controls="accordion4">
                                                                                    <span class="lead collapse-title">
                                                                                        <h2 class="bold section_title ">AKTUELLE HEIZUNG</h2>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="accordion4" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading4" class="collapse" aria-expanded="false">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <div class="row">
                                                                                                <div class="col-md-4 " >  
                                                                                                    <h4 class="bold ">Heizungart</h4>  
                                                                                                    <select name="heatpump" id="heatpump" class="form-control"> 
                                                                                                            <option value="WP" @if(isset($wp) && $wp->heatpump == "Wärmepumpe" || isset($customer) && $customer->heating_system_type == "Wärmepumpe") selected @endif>Wärmepumpe</option>
                                                                                                            <option value="Gas" @if(isset($wp) && $wp->heatpump == "Gas" || isset($customer) && $customer->heating_system_type == "Gas") selected @endif>GAS</option>
                                                                                                            <option value="oil" @if(isset($wp) && $wp->heatpump == "oil" || isset($customer) && $customer->heating_system_type == "oil") selected @endif>Öl</option>
                                                                                                            <option value="Pellets" @if(isset($wp) && $wp->heatpump == "Pellets" || isset($customer) && $customer->heating_system_type == "Pellets") selected @endif>Pellets</option>
                                                                                                            <option value="Nachtspeicher" @if(isset($wp) && $wp->heatpump == "Nachtspeicher" || isset($customer) && $customer->heating_system_type == "Nachtspeicher") selected @endif>Nachtspeicher</option>
                                                                                                        </select>


                                                                                                </div>

                                                                                                <div class="col-md-3 "  >  
                                                                                                    <h4 class="bold ">Aufstellort</h4>  
                                                                                                    <select name="exhibition_location" id="exhibition_location" class="form-control">
                                                                                                        <option value="">Bitte wählen</option>
                                                                                                        <option value="KG" @if($wp && $wp->exhibition_location == "KG") selected @endif>KG</option>
                                                                                                        <option value="EG" @if($wp && $wp->exhibition_location == "EG") selected @endif>EG</option>
                                                                                                        <option value="OG" @if($wp && $wp->exhibition_location == "OG") selected @endif> OG</option>
                                                                                                        <option value="DG" @if($wp && $wp->exhibition_location == "DG") selected @endif> DG</option> 
                                                                                                    </select>  
                                                                                                </div>

                                                                                                <div class="col-md-5 "  >  
                                                                                                    <h4 class="bold ">Notiz</h4>  
                                                                                                    <textarea name="exhibation_location_note" id="exhibation_location_note" cols="10" rows="2" class="form-control">
                                                                                                        {{ old('exhibation_location_note', $wp ? $wp->exhibation_location_note : "") }} 
                                                                                                    </textarea>
                                                                                        
                                                                                                </div>

                                                                                                <div class="col-4 mt-1">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-12 ">
                                                                                                            <h4 class="bold">Alter der Heizung <label
                                                                                                                    id="heating_age_label"></label></h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-12 flex_me">
                                                                                                            <input type="text" class="form-control"
                                                                                                                name="heating_manufacture_year" value="{{  old('heating_manufacture_year', $wp ? $wp->heating_manufacture_year : $customer->heating_manufacture_year) }}"
                                                                                                                id="heating_manufacture_year"> &nbsp;
                                                                                                            <span id="heating_lables"
                                                                                                                style="align-content: center;">
                                                                                                                <span> Jahr(e)</span> </span>

                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <!-- Enable only for Oil and Gas  -->
                                                                                                <div class="col-3 mt-1">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-12 ">
                                                                                                            <h4 class="bold">Heiztechnik</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-12 flex_me">
                                                                                                            <select class="form-control" id="heating_type"
                                                                                                                name="heating_type"> 
                                                                                                                <option value="Brennwert" @if($wp && $wp->heating_type == "Brennwert") selected @endif>Brennwert</option>
                                                                                                                <option value="Heizwert" @if($wp && $wp->heating_type == "Heizwert") selected @endif>Heizwert</option>
                                                                                                                 
                                                                                                            </select>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-4 mt-1">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-12 ">
                                                                                                            <h4 class="bold">Leistung der Anlage</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-12 flex_me">
                                                                                                            <input type="text" class="form-control" value="{{ old('system_performance', $wp ? $wp->system_performance : $customer->system_performance ) }}"
                                                                                                                name="system_performance">&nbsp;<span>kWh</span>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                    <div class="col-4 mt-1">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-12 ">
                                                                                                            <h4 class="bold">Hersteller</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-12 flex_me">
                                                                                                            <input type="text" class="form-control"  value="{{ old('heating_company', $wp ? $wp->heating_company : '') }}"
                                                                                                                name="heating_company"> 
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                                    <div class="col-4 mt-1">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-12 ">
                                                                                                            <h4 class="bold">Typbezeichnung</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-12 flex_me">
                                                                                                            <input type="text" class="form-control" value="{{ old('type_designation', $wp ? $wp->type_designation : '') }}"
                                                                                                                name="type_designation"> 
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="col-4 mt-1">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-12 ">
                                                                                                            <h4 class="bold">Warmwasseraufbereitung</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-12 flex_me">
                                                                                                            <select name="hot_water_preparation" id="" class="form-control">
                                                                                                                <option value="">Bitte wählen</option>
                                                                                                                <option value="Heizung"  @if($wp && $wp->hot_water_preparation == "Heizung") selected @endif>Heizung</option>
                                                                                                                <option value="Durchlauferhitzer"  @if($wp && $wp->hot_water_preparation == "Durchlauferhitzer") selected @endif>Durchlauferhitzer</option>
                                                                                                                <option value="Sonstiges"  @if($wp && $wp->hot_water_preparation == "Sonstiges") selected @endif>Sonstiges</option>
                                                                                                            </select>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                                    <div class="col-4 mt-1">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-12 ">
                                                                                                            <h4 class="bold">Warmwasserverbrauch pro Person</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-12 flex_me">
                                                                                                            <select name="number_hotWaterConsumptionPerPerson" id="number_hotWaterConsumptionPerPerson" class="form-control select2" >
                                                                                                                <option value="25" @if($wp && $wp->number_hotWaterConsumptionPerPerson == "25") selected @endif>25 Liter (Niedrig)</option>
                                                                                                                <option value="50" @if($wp && $wp->number_hotWaterConsumptionPerPerson == "50") selected @endif>50 Liter (Normal)</option>
                                                                                                                <option value="80" @if($wp && $wp->number_hotWaterConsumptionPerPerson == "80") selected @endif>80 Liter (Hoch)</option>
                                                                                                                <option value="120" @if($wp && $wp->number_hotWaterConsumptionPerPerson == "120") selected @endif>120 Liter (Luxus)</option>
                                                                                                            </select>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>


                                                                                                <div class="col-4 mt-1">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-12 ">
                                                                                                            <h4 class="bold">Heizsystem</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-12 flex_me">
                                                                                                            <select name="general_heating_system" class="form-control" id="general_heating_system">     
                                                                                                                <option value="underfloor heating" @if($wp && $wp->general_heating_system == "underfloor heating") selected @endif>Fußbodenheizung</option>    
                                                                                                                <option value="radiator" @if($wp && $wp->general_heating_system == "radiator") selected @endif>Heizkörper</option> 
                                                                                                                <option value="underfloor heating and radiator"  @if($wp && $wp->general_heating_system == "underfloor heating and radiator") selected @endif>Fußbodenheizung + Heizkörper</option> 
                                                                                                                <option value="none" @if($wp && $wp->general_heating_system == "none") selected @endif>Keine</option> 
                                                                                                            </select> 
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="col-4 mt-1">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-12 ">
                                                                                                            <h4 class="bold">Rohrsystem</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-12 flex_me">
                                                                                                            <select name="pipe_system" class="form-control" id="pipe_system">
                                                                                                                <option selected>Wählen...</option>     
                                                                                                                <option value="one" @if($wp && $wp->pipe_system == "one") selected @endif>Ein-Rohr-System</option>    
                                                                                                                <option value="two" @if($wp && $wp->pipe_system == "two") selected @endif>Zwei-Rohr-System</option> 
                                                                                                            </select> 
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                                    
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                                <div class="cards collapse-header">
                                                                                <div id="heading5" class="card-header collapsed" data-toggle="collapse" role="button" data-target="#accordion5" aria-expanded="false" aria-controls="accordion5">
                                                                                    <span class="lead collapse-title">
                                                                                        <h2 class="bold section_title ">Hydraulichen Eignungsprüfung Fußbodenheizung & elektrischen Anschluß: </h2>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="accordion5" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading5" class="collapse" aria-expanded="false">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <div class="row">
                                                                                                <div class="col-md-4 " >  
                                                                                                    <h4 class="bold ">Heizkreisverteiler</h4>  
                                                                                                    <select name="heating_circuit_distributor" id="heating_circuit_distributor" class="form-control">
                                                                                                        <option value="">Bitte wählen</option>
                                                                                                        <option value="yes" @if($wp && $wp->heating_circuit_distributor == "yes") selected @endif>Ja</option>
                                                                                                        <option value="no" @if($wp && $wp->heating_circuit_distributor == "no") selected @endif>Nein</option> 
                                                                                                    </select>  
                                                                                                </div>

                                                                                                <div class="col-md-4 "  >  
                                                                                                    <h4 class="bold ">Stellantriebe</h4>  
                                                                                                    <select name="actuators" id="actuators" class="form-control">
                                                                                                        <option value="">Bitte wählen</option>
                                                                                                        <option value="Ja / 230 Volt" @if($wp && $wp->actuators == "Ja / 230 Volt") selected @endif>Ja / 230 Volt</option>
                                                                                                        <option value="Ja / 24 Volt" @if($wp && $wp->actuators == "Ja / 24 Volt") selected @endif>Ja / 24 Volt</option>
                                                                                                        <option value="Nein / 230 Volt" @if($wp && $wp->actuators == "Nein / 230 Volt") selected @endif> Nein / 230 Volt</option>
                                                                                                        <option value="Nein / 24 Volt" @if($wp && $wp->actuators == "Nein / 24 Volt") selected @endif> Nein / 24 Volt</option> 
                                                                                                    </select>  
                                                                                                </div>

                                                                                                <div class="col-md-4"  >  
                                                                                                    <h4 class="bold ">Kühlung Funktion</h4>  
                                                                                                    <select name="suitable_cooling_system" id="suitable_cooling_system" class="form-control">
                                                                                                        <option value="">Bitte wählen</option>
                                                                                                        <option value="Ja" @if($wp && $wp->suitable_cooling_system == "Ja") selected @endif>Ja</option>
                                                                                                        <option value="Nein" @if($wp && $wp->suitable_cooling_system == "Nein") selected @endif> Nein</option>
                                                                                                    </select>  
                                                                                                </div>

                                                                                                <div class="col-md-12 "  >  
                                                                                                    <h4 class="bold ">Notiz</h4>  
                                                                                                    <textarea name="exhibation_location_note" id="exhibation_location_note" cols="10" rows="2" class="form-control">
                                                                                                        {{ old('exhibation_location_note', $wp ? $wp->exhibation_location_note : '') }}
                                                                                                    </textarea>
                                                                                        
                                                                                                </div> 
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div> 

                                                                            <div class="cards collapse-header">
                                                                                <div id="heading7" class="card-header collapsed" data-toggle="collapse" role="button" data-target="#accordion7" aria-expanded="false" aria-controls="accordion7">
                                                                                    <span class="lead collapse-title">
                                                                                        <h2 class="bold section_title ">Hydraulichen Eignungsprüfung Heizkörper</h2>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="accordion7" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading7" class="collapse" aria-expanded="false">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <div class="row">
                                                                                                <div class="col-md-4 " >  
                                                                                                    <h4 class="bold ">Heizkörper</h4>  
                                                                                                    <select name="radiator" id="radiator" class="form-control">
                                                                                                        <option value="">Bitte wählen</option>
                                                                                                        <option value="yes"  @if($wp && $wp->radiator == "yes") selected @endif>Ja</option>
                                                                                                        <option value="no" @if($wp && $wp->radiator == "no") selected @endif>Nein</option> 
                                                                                                    </select>  
                                                                                                </div>

                                                                                                <div class="col-md-4 "  >  
                                                                                                    <h4 class="bold ">Thermostate</h4>  
                                                                                                    <select name="thermostats" id="thermostats" class="form-control">
                                                                                                        <option value="">Bitte wählen</option>
                                                                                                        <option value="yes" @if($wp && $wp->thermostats == "yes") selected @endif>Ja</option>
                                                                                                        <option value="no" @if($wp && $wp->thermostats == "no") selected @endif>Nein</option> 
                                                                                                    </select>  
                                                                                                </div>

                                                                                                    <div class="col-md-4 "  >  
                                                                                                    <h4 class="bold ">Thermostatventile</h4>  
                                                                                                    <select name="thermostatic_valves" id="thermostatic_valves" class="form-control">
                                                                                                        <option value="">Bitte wählen</option>
                                                                                                        <option value="yes" @if($wp && $wp->thermostatic_valves == "yes") selected @endif>Ja</option>
                                                                                                        <option value="no"  @if($wp && $wp->thermostatic_valves == "no") selected @endif>Nein</option> 
                                                                                                    </select>  
                                                                                                </div>

                                                                                                <div class="col-md-4"  >  
                                                                                                    <h4 class="bold ">Kühlung Funktion</h4>  
                                                                                                    <select name="radiator_cooling_system" id="radiator_cooling_system" class="form-control">
                                                                                                        <option value="">Bitte wählen</option>
                                                                                                        <option value="Ja" @if($wp && $wp->radiator_cooling_system == "Ja") selected @endif>Ja</option>
                                                                                                        <option value="Nein" @if($wp && $wp->radiator_cooling_system == "Nein") selected @endif> Nein</option>
                                                                                                    </select>  
                                                                                                </div>

                                                                                                <div class="col-md-12 "  >  
                                                                                                    <h4 class="bold ">Notiz</h4>  
                                                                                                    <textarea name="radiator_note" id="radiator_note" cols="10" rows="2" class="form-control">
                                                                                                            {{ old('radiator_note', $wp ? $wp->radiator_note : '') }}
                                                                                                    </textarea>
                                                                                        
                                                                                                </div> 
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="cards collapse-header">
                                                                                <div id="heading8" class="card-header collapsed" data-toggle="collapse" role="button" data-target="#accordion8" aria-expanded="false" aria-controls="accordion8">
                                                                                    <span class="lead collapse-title">
                                                                                            <h2 class="bold section_title">Welche Leitungen sind verlegt?</h2>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="accordion8" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading8" class="collapse" aria-expanded="false">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <div class="row">
                                                                                                <div class="table-responsive">
                                                                                            
                                                                                                    <table class="table">
                                                                                                        <thead>
                                                                                                            <tr>
                                                                                                                <th>#</th>
                                                                                                                <th>Art</th>
                                                                                                                <th>Dimension</th>
                                                                                                                <th>Hersteller</th>
                                                                                                                <th>Typbezeichnung</th>
                                                                                                                <th>Notiz</th>
                                                                                                            </tr>
                                                                                                        </thead>
                                                                                                        <tbody>
                                                                                                            <!-- Heating -->
                                                                                                            <tr>
                                                                                                                <th scope="row">
                                                                                                                    <input type="text" value="Heizung" name="cable[0][system]" class="form-control" readonly>
                                                                                                                </th>
                                                                                                                <td>
                                                                                                                    <select name="cable[0][type]" class="form-control">
                                                                                                                        <option value="Kupfer" @if($heating && $heating->type == "Kupfer") selected @endif>Kupfer</option>
                                                                                                                        <option value="Kunststoff" @if($heating && $heating->type == "Kunststoff") selected @endif>Kunststoff</option>
                                                                                                                    </select>
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Dimension" name="cable[0][dimension]" value="{{ old('dimension', $heating->dimension ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Hersteller" name="cable[0][company]" value="{{ old('company', $heating->company ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Typbezeichnung" name="cable[0][designation]" value="{{ old('designation', $heating->designation ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Notiz" name="cable[0][note]" value="{{ old('note', $heating->note ?? '') }}">
                                                                                                                </td>
                                                                                                            </tr>

                                                                                                            <!-- Cold Water -->
                                                                                                            <tr>
                                                                                                                <th scope="row">
                                                                                                                    <input type="text" value="Kalt-Wasser" name="cable[1][system]" class="form-control" readonly>
                                                                                                                </th>
                                                                                                                <td>
                                                                                                                    <select name="cable[1][type]" class="form-control">
                                                                                                                        <option value="Kupfer" @if($cold_water && $cold_water->type == "Kupfer") selected @endif>Kupfer</option>
                                                                                                                        <option value="Kunststoff" @if($cold_water && $cold_water->type == "Kunststoff") selected @endif>Kunststoff</option>
                                                                                                                    </select>
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Dimension" name="cable[1][dimension]" value="{{ old('dimension', $cold_water->dimension ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Hersteller" name="cable[1][company]" value="{{ old('company', $cold_water->company ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Typbezeichnung" name="cable[1][designation]" value="{{ old('designation', $cold_water->designation ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Notiz" name="cable[1][note]" value="{{ old('note', $cold_water->note ?? '') }}">
                                                                                                                </td>
                                                                                                            </tr>

                                                                                                            <!-- Warm Water -->
                                                                                                            <tr>
                                                                                                                <th scope="row">
                                                                                                                    <input type="text" value="Warm-Wasser" name="cable[2][system]" class="form-control" readonly>
                                                                                                                </th>
                                                                                                                <td>
                                                                                                                    <select name="cable[2][type]" class="form-control">
                                                                                                                        <option value="Kupfer" @if($warm_water && $warm_water->type == "Kupfer") selected @endif>Kupfer</option>
                                                                                                                        <option value="Kunststoff" @if($warm_water && $warm_water->type == "Kunststoff") selected @endif>Kunststoff</option>
                                                                                                                    </select>
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Dimension" name="cable[2][dimension]" value="{{ old('dimension', $warm_water->dimension ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Hersteller" name="cable[2][company]" value="{{ old('company', $warm_water->company ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Typbezeichnung" name="cable[2][designation]" value="{{ old('designation', $warm_water->designation ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Notiz" name="cable[2][note]" value="{{ old('note', $warm_water->note ?? '') }}">
                                                                                                                </td>
                                                                                                            </tr>

                                                                                                            <!-- Circulation -->
                                                                                                            <tr>
                                                                                                                <th scope="row">
                                                                                                                    <input type="text" value="Zirkulation" name="cable[3][system]" class="form-control" readonly>
                                                                                                                </th>
                                                                                                                <td>
                                                                                                                    <select name="cable[3][type]" class="form-control">
                                                                                                                        <option value="Kupfer" @if($circulation && $circulation->type == "Kupfer") selected @endif>Kupfer</option>
                                                                                                                        <option value="Kunststoff" @if($circulation && $circulation->type == "Kunststoff") selected @endif>Kunststoff</option>
                                                                                                                    </select>
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Dimension" name="cable[3][dimension]" value="{{ old('dimension', $circulation->dimension ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Hersteller" name="cable[3][company]" value="{{ old('company', $circulation->company ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Typbezeichnung" name="cable[3][designation]" value="{{ old('designation', $circulation->designation ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Notiz" name="cable[3][note]" value="{{ old('note', $circulation->note ?? '') }}">
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        </tbody>
                                                                                                    </table>

                                                                                  
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="cards collapse-header">
                                                                                <div id="heading9" class="card-header" data-toggle="collapse" role="button" data-target="#accordion9" aria-expanded="false" aria-controls="accordion9">
                                                                                    <span class="lead collapse-title">
                                                                                        <h2 class="bold section_title">Einbringmaße Zuwegung Heizraum</h2>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="accordion9" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading9" class="collapse" aria-expanded="false">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <div class="row">
                                                                                                <div class="table-responsive">
                                                                                                    <form id="room_dimension_form">
                                                                                                        @csrf
                                                                                                        <table class="table" id="room_dimension_table">
                                                                                                            <thead>
                                                                                                                <tr>
                                                                                                                    <th>#</th>
                                                                                                                    <th>Dimensionstyp</th>
                                                                                                                    <th>Breite</th>
                                                                                                                    <th>Höhe</th>
                                                                                                                    <th>Deckenhöhe</th>
                                                                                                                    <th>Treppe Form</th>
                                                                                                                    <th>Treppe Breite</th>
                                                                                                                    <th>Geschoss</th>
                                                                                                                    <th>Aktion</th>
                                                                                                                </tr>
                                                                                                            </thead>
                                                                                                            <tbody id="room_dimension_tbody">
                                                                                                                <tr id="room_dimension_row_0">
                                                                                                                    <th scope="row">
                                                                                                                        <input type="text" name="room[0][room_number]" class="form-control" value="1" readonly>
                                                                                                                    </th>
                                                                                                                    <td>
                                                                                                                        <select name="room[0][dimension_type]" class="form-control dimension_type">
                                                                                                                            <option></option>
                                                                                                                            <option value="Tür">Tür</option>
                                                                                                                            <option value="Wand">Wand</option>
                                                                                                                            <option value="Deche">Deche</option>
                                                                                                                        </select>
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <input type="text" class="form-control" placeholder="Breite" name="room[0][width]"> 
                                                                                                                        <input type="hidden" class="form-control" name="customer_id" value="{{ $customer->id }}"> 
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <input type="text" class="form-control" placeholder="Höhe" name="room[0][height]"> 
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <input type="text" class="form-control ceiling_height" placeholder="Deckenhöhe" name="room[0][ceiling_height]">
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <select name="room[0][stair_form]" class="form-control stair_form">
                                                                                                                            <option></option>
                                                                                                                            <option value="L-Form">L-Form</option>
                                                                                                                            <option value="U-Form">U-Form</option>
                                                                                                                            <option value="Wendel">Wendel</option>
                                                                                                                            <option value="Gradeluäfig">Gradeluäfig</option>
                                                                                                                        </select>
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <input type="text" class="form-control stair_width" placeholder="Treppe Breite" name="room[0][stair_width]">
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <select name="room[0][room_story]" class="form-control">
                                                                                                                            <option></option>
                                                                                                                            <option value="KG">KG</option>
                                                                                                                            <option value="EG">EG</option>
                                                                                                                            <option value="OG">OG</option>
                                                                                                                            <option value="DG">DG</option>
                                                                                                                        </select>
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <button type="button" class="btn btn-icon btn-warning add_dimension">
                                                                                                                            <i class="feather icon-plus"></i>
                                                                                                                        </button>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                            </tbody>
                                                                                                        </table>
                                                                                                        <button type="button" class="btn btn-outline-success mr-1 mb-1 waves-effect waves-light" id="save_dimension">Speichern</button>
                                                                                                    </form>
                                                                                                </div>

                                                                                                <div class="table-responsive">  
                                                                                                    <table class="table" id="room_dimension_data">
                                                                                                        <thead>
                                                                                                            <tr>
                                                                                                                <th>#</th>
                                                                                                                <th>Dimensionstyp</th>
                                                                                                                <th>Breite</th>
                                                                                                                <th>Höhe</th>
                                                                                                                <th>Deckenhöhe</th>
                                                                                                                <th>Treppe Form</th>
                                                                                                                <th>Treppe Breite</th>
                                                                                                                <th>Geschoss</th>
                                                                                                                <th>Aktion</th>
                                                                                                            </tr>
                                                                                                        </thead>
                                                                                                        <tbody id="room_dimension_data">
                                                                                                            <!-- Rows will be dynamically inserted here -->
                                                                                                        </tbody>
                                                                                                    </table>

                                                                                                </div>

                                                                                                <!-- Room Dimension Modal for Editing -->
                                                                                                <div class="modal fade" id="editRoomDimensionModal" tabindex="-1" role="dialog" aria-labelledby="editRoomDimensionModalLabel" aria-hidden="true">
                                                                                                    <div class="modal-dialog" role="document">
                                                                                                        <div class="modal-content">
                                                                                                            <div class="modal-header">
                                                                                                                <h5 class="modal-title" id="editRoomDimensionModalLabel">Raumdetails bearbeiten</h5>
                                                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                                                                                                    <span aria-hidden="true">&times;</span>
                                                                                                                </button>
                                                                                                            </div>
                                                                                                            <div class="modal-body">
                                                                                                                <form id="editRoomDimensionForm">
                                                                                                                    <!-- Hidden field to store room ID -->
                                                                                                                    <input type="hidden" id="edit_room_id">
                                                                                                                    <div class="form-group">
                                                                                                                        <label>Türmaße#</label>
                                                                                                                        <input type="text" id="edit_room_number" class="form-control" name="room_number" readonly>
                                                                                                                    </div>
                                                                                                                    <div class="form-group">
                                                                                                                        <label>Dimensionstyp</label>
                                                                                                                        <select name="dimension_type" id="edit_dimension_type" class="form-control">
                                                                                                                            <option value="Tür">Tür</option>
                                                                                                                            <option value="Wand">Wand</option>
                                                                                                                            <option value="Deche">Deche</option>
                                                                                                                        </select>
                                                                                                                    </div>
                                                                                                                    <div class="form-group">
                                                                                                                        <label>Breite</label>
                                                                                                                        <input type="text" id="edit_width" class="form-control" name="width">
                                                                                                                    </div>
                                                                                                                    <div class="form-group">
                                                                                                                        <label>Höhe</label>
                                                                                                                        <input type="text" id="edit_height" class="form-control" name="height">
                                                                                                                    </div>
                                                                                                                    <div class="form-group">
                                                                                                                        <label>Deckenhöhe</label>
                                                                                                                        <input type="text" id="edit_ceiling_height" class="form-control" name="ceiling_height">
                                                                                                                    </div>
                                                                                                                    <div class="form-group">
                                                                                                                        <label>Treppe Form</label>
                                                                                                                        <select id="edit_stair_form" class="form-control" name="stair_form">
                                                                                                                            <option value="L-Form">L-Form</option>
                                                                                                                            <option value="U-Form">U-Form</option>
                                                                                                                            <option value="Wendel">Wendel</option>
                                                                                                                            <option value="Gradeluäfig">Gradeluäfig</option>
                                                                                                                        </select>
                                                                                                                    </div>
                                                                                                                    <div class="form-group">
                                                                                                                        <label>Treppe Breite</label>
                                                                                                                        <input type="text" id="edit_stair_width" class="form-control" name="stair_width">
                                                                                                                    </div>
                                                                                                                    <div class="form-group">
                                                                                                                        <label>Geschoss</label>
                                                                                                                        <select id="edit_room_story" class="form-control" name="room_story">
                                                                                                                            <option value="KG">KG</option>
                                                                                                                            <option value="EG">EG</option>
                                                                                                                            <option value="OG">OG</option>
                                                                                                                            <option value="DG">DG</option>
                                                                                                                        </select>
                                                                                                                    </div>
                                                                                                                </form>
                                                                                                            </div>
                                                                                                            <div class="modal-footer">
                                                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                                                                                                                <button type="button" id="updateRoomDimension" class="btn btn-primary">Speichern</button>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="cards collapse-header">
                                                                                <div id="heading13" class="card-header" data-toggle="collapse" role="button" data-target="#accordion13" aria-expanded="false" aria-controls="accordion13">
                                                                                    <span class="lead collapse-title">
                                                                                        <h2 class="bold section_title">Zustand Zählerschrank</h2>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="accordion13" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading13" class="collapse" aria-expanded="false">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <div class="row">  
                                                                                                <div class="col-md-4">
                                                                                                    <div class="form-group row">
                                                                                                        <label for=""><h4>Zählerschrank</h4></label>
                                                                                                        <select name="meter_cabinet" id="meter_cabinet" class="form-control" style="width:100% !important;">
                                                                                                            <option value="ok" @if($meter_cabinet && $meter_cabinet->meter_cabinet == "ok") selected @endif>OK</option>
                                                                                                            <option value="upgrade" @if($meter_cabinet && $meter_cabinet->meter_cabinet == "upgrade") selected @endif>ertüchtigen</option>
                                                                                                            <option value="new" @if($meter_cabinet &&  $meter_cabinet->meter_cabinet == "new") selected @endif>neu</option> 
                                                                                                        </select> 
                                                                                                    </div>

                                                                                                    <div class="form-group row" id="cabinet_size_div" @if(!$meter_cabinet || !$meter_cabinet->cabinet_size) style="display:none;" @endif>
                                                                                                        <label for=""><h4>Größe</h4></label> 
                                                                                                        <select name="cabinet_size" id="cabinet_size" class="form-control" style="width:100% !important;">
                                                                                                            <option value="550" @if($meter_cabinet &&  $meter_cabinet->cabinet_size == "550") selected @endif>550</option>
                                                                                                            <option value="800" @if($meter_cabinet &&  $meter_cabinet->cabinet_size == "800") selected @endif>800</option>
                                                                                                            <option value="1100" @if($meter_cabinet &&  $meter_cabinet->cabinet_size == "1100") selected @endif>1100</option> 
                                                                                                        </select> 
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="col-md-4">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-4">
                                                                                                            <label>Hersteller</label> 
                                                                                                        </div>
                                                                                                        <div class="col-md-8 flex_me">
                                                                                                            <fieldset>
                                                                                                                <select name="meter_cabinet_company" id="meter_cabinet_company" class="form-control" style="width:100% !important;">
                                                                                                                    @foreach ($electro as $elec)
                                                                                                                        <option value="{{ $elec->id }}" @if($meter_cabinet && $meter_cabinet->meter_cabinet_company == $elec->id) selected @endif> 
                                                                                                                            {{ $elec->name }}
                                                                                                                        </option>
                                                                                                                    @endforeach
                                                                                                                </select>
                                                                                                            </fieldset>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="col-md-4" id="cabinet_settings_div" @if(!$meter_cabinet || !$meter_cabinet->wp_meter_adapter_plate) style="display:none;" @endif>
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-12">
                                                                                                            <h4 class="bold">Einzubauende Komponenten</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-12 flex_me">
                                                                                                            <ul class="mb-0" style="display:flex; flex-wrap: wrap; flex-direction: column;">
                                                                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                                                                    <fieldset>
                                                                                                                        <div class="custom-control custom-radio">
                                                                                                                            <input type="checkbox" class="custom-control-input" name="wp_all" id="wp_all">
                                                                                                                            <label class="custom-control-label" for="wp_all">Alles</label>
                                                                                                                        </div>
                                                                                                                    </fieldset>
                                                                                                                </li>
                                                                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                                                                    <fieldset>
                                                                                                                        <div class="custom-control custom-radio">
                                                                                                                            <input type="checkbox" class="custom-control-input" name="wp_meter_adapter_plate" id="wp_meter_adapter_plate" @if($meter_cabinet && $meter_cabinet->wp_meter_adapter_plate) checked @endif>
                                                                                                                            <label class="custom-control-label" for="wp_meter_adapter_plate">Zähleradapterplatte</label>
                                                                                                                        </div>
                                                                                                                    </fieldset>
                                                                                                                </li>
                                                                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                                                                    <fieldset>
                                                                                                                        <div class="custom-control custom-radio">
                                                                                                                            <input type="checkbox" class="custom-control-input" name="wp_ac_surge_protection" id="wp_ac_surge_protection" @if($meter_cabinet && $meter_cabinet->wp_ac_surge_protection) checked @endif>
                                                                                                                            <label class="custom-control-label" for="wp_ac_surge_protection" style="width: 232px;">AC Überspannungsschutz</label>
                                                                                                                        </div>
                                                                                                                    </fieldset>
                                                                                                                </li>
                                                                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                                                                    <fieldset>
                                                                                                                        <div class="custom-control custom-radio">
                                                                                                                            <input type="checkbox" class="custom-control-input" name="wp_ac_switch" id="wp_ac_switch" @if($meter_cabinet && $meter_cabinet->wp_ac_switch) checked @endif>
                                                                                                                            <label class="custom-control-label" for="wp_ac_switch">SLS Schalter</label>
                                                                                                                        </div>
                                                                                                                    </fieldset>
                                                                                                                </li>
                                                                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                                                                    <fieldset>
                                                                                                                        <div class="custom-control custom-radio">
                                                                                                                            <input type="checkbox" class="custom-control-input" name="wp_apz_field" id="wp_apz_field" @if($meter_cabinet && $meter_cabinet->wp_apz_field) checked @endif>
                                                                                                                            <label class="custom-control-label" for="wp_apz_field">APZ Feld</label>
                                                                                                                        </div>
                                                                                                                    </fieldset>
                                                                                                                </li>
                                                                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                                                                    <fieldset>
                                                                                                                        <div class="custom-control custom-radio">
                                                                                                                            <input type="checkbox" class="custom-control-input" name="wp_disconnect_relay" id="wp_disconnect_relay" @if($meter_cabinet && $meter_cabinet->wp_disconnect_relay) checked @endif>
                                                                                                                            <label class="custom-control-label" for="wp_disconnect_relay">Trenn-Relais</label>
                                                                                                                        </div>
                                                                                                                    </fieldset>
                                                                                                                </li>
                                                                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                                                                    <fieldset>
                                                                                                                        <div class="custom-control custom-radio">
                                                                                                                            <input type="checkbox" class="custom-control-input" name="wp_equipotential_bonding" id="wp_equipotential_bonding_busbar" @if($meter_cabinet && $meter_cabinet->wp_equipotential_bonding) checked @endif>
                                                                                                                            <label class="custom-control-label" for="wp_equipotential_bonding_busbar">Potentialausgleichsschiene</label>
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
                                                                            </div> 

                                                                            <div class="cards collapse-header">
                                                                                <div id="heading40" class="card-header" data-toggle="collapse" role="button" data-target="#accordion40" aria-expanded="false" aria-controls="accordion40">
                                                                                    <span class="lead collapse-title">
                                                                                        <h2 class="bold section_title">Lüftung</h2>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="accordion40" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading40" class="collapse" aria-expanded="false">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <div class="row">
                                                                                                <div class="table-responsive">
                                                                                                    <table class="table">
                                                                                                        <thead>
                                                                                                            <tr>
                                                                                                                <th>Lüftung</th>
                                                                                                                <th>Lüftungsystem</th>
                                                                                                                <th>Wärmerückgewinnung</th>
                                                                                                                <th>Hersteller</th>
                                                                                                                <th>Typ</th>
                                                                                                            </tr>
                                                                                                        </thead>
                                                                                                        <tbody>
                                                                                                            <tr>
                                                                                                                <th scope="row">
                                                                                                                    <select name="ventilation" id="ventilation" class="form-control">
                                                                                                                        <option value="">Bitte wählen</option>
                                                                                                                        <option value="Ja" @if($wp && $wp->ventilation == "Ja" ) selected @endif>Ja</option>
                                                                                                                        <option value="nein"@if($wp && $wp->ventilation == "nein" ) selected @endif>Nein</option> 
                                                                                                                        <option value="geplant"@if($wp && $wp->ventilation == "geplant" ) selected @endif>Geplant</option> 
                                                                                                                    </select>  
                                                                                                                </th>
                                                                                                                <td>
                                                                                                                    <select name="ventilation_system" id="ventilation_system" class="form-control">
                                                                                                                        <option value="">Bitte wählen</option>
                                                                                                                        <option value="Zentral"  @if($wp && $wp->ventilation_system == "Zentral" ) selected @endif>Zentral</option>
                                                                                                                        <option value="Dezentral" @if($wp && $wp->ventilation_system == "Dezentral" ) selected @endif>Dezentral</option>  
                                                                                                                    </select>  
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <select name="heat_recovery" id="heat_recovery" class="form-control">
                                                                                                                        <option value="">Bitte wählen</option>
                                                                                                                        <option value="Ja" @if($wp && $wp->heat_recovery == "Ja" ) selected @endif>Ja</option>
                                                                                                                        <option value="Nein"  @if($wp && $wp->heat_recovery == "Nein" ) selected @endif>Nein</option>  
                                                                                                                    </select>  
                                                                                                                </td> 
                                                                                                                <td>
                                                                                                                    <input type="text" name="ventilation_company" class="form-control" placeholder="Hersteller" value="{{ old('ventilation_company', isset($wp) ? $wp->ventilation_company : '') }}"> 
                                                                                                                </td>

                                                                                                                <td>
                                                                                                                    <input type="text" name="ventilation_type" class="form-control" placeholder="Typ" value="{{ old('ventilation_type', isset($wp) ? $wp->ventilation_type : '') }}">
                                                                                                                </td>
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
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>    
                                    </section> 
                                    <section class="col-md-12" id="section_wp_4">
                                        <div class="cards">
                                            <div class="card-body"
                                                style="display: flex !important;flex-wrap: wrap;">   
                                                <div class="col-12">
                                                    <hr>
                                                </div>
                                                <section class="col-md-12 dynamic-section" id="section_4">
                                                    <div class="cards">
                                                        <div class="card-body"
                                                            style="display: flex !important; flex-wrap: wrap;">
                                                            <div class="col-12">
                                                                <div class="form-group row">
                                                                    <div class="col-md-12">
                                                                        <h4 class="bold">Fotos/Unterlagen des
                                                                            Kunden
                                                                            erforderlich</h4>
                                                                    </div>

                                                                    <div class="md-12">
                                                                        <ul>
                                                                            <li>Architekten-Pläne des Hauses
                                                                                (Etagen +
                                                                                Schnitte) wenn vorhanden</li>
                                                                            <li>Fotos der gesamten Dachflächen
                                                                                (so dass
                                                                                diese vollständig sichtbar sind)
                                                                            </li>
                                                                            <li>Foto des geöffneten
                                                                                Zählerschranks</li>
                                                                            <li>Fotos der Typbezeichnung des
                                                                                Zählerschranks (Aufkleber in der
                                                                                Ecke
                                                                                der Tür)
                                                                            </li>
                                                                            <li>Heizlastberechnung</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </section>
                                                <div class="col-12">
                                                    <button type="submit"
                                                        class="btn btn-icon btn-icon  btn-light mr-1 mb-1 waves-effect waves-light float-right ">
                                                        <i class="feather icon-save"></i> Daten Ubernahnen
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </form>
                        </article>
                    </div>