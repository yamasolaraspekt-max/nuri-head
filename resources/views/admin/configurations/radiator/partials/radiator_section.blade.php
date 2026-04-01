
 <section id="radiator_section" class="add-section-template">
                    <div class="container"> 
                            <div class="row">
                                <!-- Customer Information -->
                                <div class="col-md-12 mb-3">
                                    <label for="customer" class="form-label h3 primary"><strong>Kunde</strong></label>
                                    <select name="customer_id" id="customer" class="select22" style="width:100% !important;">
                                        @foreach ($data as $customer )
                                            <option value="{{ $customer->id }}"> {{$customer->name}} {{$customer->lastname }} - {{ $customer->city}}</option>
                                        @endforeach
                                    </select> 
                                </div>
                            </div>

                            <div class="row">
                                <!-- Number -->
                                <div class="col-md-3 mb-3">
                                    <label for="number" class="form-label h3 primary"><strong>Nr.</strong></label>
                                    <input type="text" class="form-control" id="number" name="number">
                                </div>

                                <!-- Type -->
                                <div class="col-md-3 mb-3">
                                    <label for="type" class="form-label h3 primary"><strong>Typ</strong></label>
                                    <select class="form-control" id="type" name="type">
                                        <option selected>Wählen...</option>
                                        <option value="Kompaktheizkörper">Kompaktheizkörper</option>
                                        <option value="Ventilheizkörper">Ventilheizkörper</option>
                                        <option value="DIN Stahl Radiator">DIN Stahl Radiator</option>
                                        <option value="Gußradiator">Gußradiator</option>
                                        <option value="Handtuchheizkörper">Handtuchheizkörper</option> 
                                    </select>
                                </div>

                                <!-- Floor -->
                                <div class="col-md-3 mb-3">
                                    <label for="floor" class="form-label h3 primary"><strong>Etage</strong></label>
                                    <select class="form-control" id="floor" name="floor">
                                        <option selected>Wählen...</option>
                                        <option value="Keller">Keller</option>
                                        <option value="Erdgeschoss">Erdgeschoss</option>
                                        <option value="Obergeschoss">Obergeschoss</option>
                                        <option value="Dachgeschoss">Dachgeschoss</option>
                                        <option value="Anbau">Anbau</option>
                                        <option value="sontiges">sontiges</option>
                                    </select>
                                </div>

                                <!-- Room -->
                                <div class="col-md-3 mb-3">
                                    <label for="room" class="form-label h3 primary"><strong>Raum</strong></label>
                                    <select class="form-control" id="room" name="room">
                                        <option selected>Wählen...</option>
                                        <option value="Keller">Keller</option>
                                        <option value="Abstellraum">Abstellraum</option>
                                        <option value="Bad">Bad</option>
                                        <option value="Kinderzimmer">Kinderzimmer</option>
                                        <option value="Wohnzimmer">Wohnzimmer</option>
                                        <option value="Esszimmer">Esszimmer</option> 
                                        <option value="Gästezimmer">Gästezimmer</option> 
                                        <option value="Flur">Flur</option> 
                                        <option value="Dach">Dach</option> 
                                        <option value="sontiges">sontiges</option> 
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-content">
                                            <button id="delete-image" class="btn btn-danger btn-sm position-absolute" style="top: 10px; right: 10px; display: none;">
                                                <i class="feather icon-trash"></i>
                                            </button>
                                            <img class="card-img-top img-fluid" src="" alt="Radiator Image" id="radiator_image" style="display: none;">
                                            <div class="card-body">
                                                <h4 class="card-title">Heizkörper Bild</h4> 
                                                <a href="#" class="btn btn-outline-primary waves-effect waves-light" data-toggle="modal" data-target="#upload"> <i class="feather icon-upload"></i> Upload</a> 
                                            </div>
                                        </div>
                                    </div> 
                                </div>

                                   <!-- Modal for Image Upload -->
                                    <div class="modal fade text-left" id="upload" tabindex="-1" role="dialog" aria-labelledby="myModalLabel20" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xs" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="myModalLabel20">Heizkörper Bild</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="file" name="image" class="form-control" id="image_input">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-primary waves-effect waves-light" data-dismiss="modal">Accept</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <div class="col-md-8 mb-3"> 
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label h3 primary"><strong>Größe</strong></label>
                                            <fieldset class="form-group">
                                                    <label for="basicInput">Breite</label>
                                                    <input type="text" class="form-control" placeholder="Breite" name="width"> 
                                            </fieldset>

                                            <fieldset class="form-group">
                                                    <label for="height">Höhe</label>
                                                    <input type="text" class="form-control" placeholder="Höhe" name="height"> 
                                            </fieldset>

                                            <fieldset class="form-group">
                                                    <label for="height">Tiefe</label>
                                                    <input type="text" class="form-control" placeholder="Höhe" name="depth"> 
                                            </fieldset>  
                                        </div>

                                        <div class="col-md-6 mb-2">
                                            <label class="form-label h3 primary"><strong>Nische</strong></label>
                                             <fieldset class="form-group">
                                                    <label for="oben">oben</label>
                                                    <input type="text" class="form-control" placeholder="oben" name="niche_top"> 
                                            </fieldset>
                                             <fieldset class="form-group">
                                                    <label for="niche_bottom">unten</label>
                                                    <input type="text" class="form-control" placeholder="unten" name="niche_bottom"> 
                                            </fieldset>
                                             <fieldset class="form-group">
                                                    <label for="niche_left">links</label>
                                                    <input type="text" class="form-control" placeholder="links" name="niche_left"> 
                                            </fieldset>
                                             <fieldset class="form-group">
                                                    <label for="niche_right">rechts</label>
                                                    <input type="text" class="form-control" placeholder="rechts" name="niche_right"> 
                                            </fieldset>  
                                            <label class="form-label   mt-2"><strong>Fensterbank</strong></label> 
                                            <ul class="list-unstyled mb-0">
                                                <li class="d-inline-block mr-2">
                                                    <fieldset>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" class="custom-control-input" name="has_window_still" id="has_window_still1" checked="">
                                                            <label class="custom-control-label" for="has_window_still1">Ja</label>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                                <li class="d-inline-block mr-2">
                                                    <fieldset>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" class="custom-control-input" name="has_window_still" id="has_window_still2">
                                                            <label class="custom-control-label" for="has_window_still2">Nein</label>
                                                        </div>
                                                    </fieldset>
                                                </li> 
                                            </ul>
                                        </div>
                                    </div>
                                
                                    <div class="row">
                                        <div class="title h3 primary mb-2"><strong>ANSCHLÜSSE</strong></div>
                                        <div class="col-md-12 mb-1"> 
                                            <div class="col-md-12 col-md-12 select d-flex"> 
                                                <div class="col-md-2">
                                                        <label for="supply_valve" class="form-label">Vorlaufventil</label> 
                                                    </div>
                                                    <div class="col-md-8">
                                                        <select class="form-control" id="supply_valve" name="supply_valve">
                                                            <option selected>Wählen...</option>
                                                            <option value="oben links">oben links</option>
                                                            <option value="oben rechts">oben rechts</option>
                                                            <option value="unten links">unten links</option>
                                                            <option value="unten rechts">unten rechts</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <fieldset>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" checked="" name="supply_valve_presettable" id="supply_valve_presettable">
                                                                <label class="custom-control-label" for="supply_valve_presettable">Voreinstellbar</label>
                                                            </div>
                                                        </fieldset>
                                                    </div> 
                                                </div>

                                                <div class="col-md-12 col-md-12 select d-flex mt-2"> 
                                                        <div class="col-md-2">
                                                            <label for="return_valve" class="form-label mt-1">Rücklaufventil</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <select class="form-control" id="return_valve" name="return_valve">
                                                                    <option selected>Wählen...</option>
                                                                    <option value="unten links Wand">unten links Wand</option>
                                                                    <option value="unten links Rückwand">unten links Rückwand</option>
                                                                    <option value="unten links Boden">unten links Boden</option>
                                                                    <option value="unten rechts Wand">unten rechts Wand</option>
                                                                    <option value="unten rechts Rückwand">unten rechts Rückwand</option>
                                                                    <option value="unten rechts Boden">unten rechts Boden</option>
                                                                    <option value="Boden unten links">Boden unten links</option>
                                                                    <option value="Boden unten rechts">Boden unten rechts</option>
                                                                </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <fieldset>
                                                                <div class="custom-control custom-checkbox">
                                                                    <input type="checkbox" class="custom-control-input" checked="" name="return_valve_present" id="return_valve_present">
                                                                    <label class="custom-control-label" for="return_valve_present">Vorhanden</label>
                                                                </div>
                                                            </fieldset>
                                                        </div> 
                                                </div>

                                                <div class="col-md-12 col-md-12 select d-flex mt-2"> 
                                                    <div class="col-md-2">
                                                        <label for="design" class="form-label mt-1">Bauform</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <select class="form-control" id="design" name="design" >
                                                            <option selected>Wählen...</option>
                                                            <option value="Eck">Eck</option>
                                                            <option value="Durchgang">Durchgang</option>
                                                            <option value="Winkeleck">Winkeleck</option> 
                                                        </select>
                                                    </div> 
                                                </div>

                                                <div class="col-md-12 col-md-12 select d-flex mt-2"> 
                                                    <div class="col-md-2">
                                                        <label for="design" class="form-label mt-1">Thermostatkopf erneuern</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <ul class="list-unstyled mb-0">
                                                            <li class="d-inline-block mr-2">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input" name="renew_thermostat_head" id="renew_thermostat_head1" checked="">
                                                                        <label class="custom-control-label" for="renew_thermostat_head1">Ja</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-2">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input" name="renew_thermostat_head" id="renew_thermostat_head2">
                                                                        <label class="custom-control-label" for="renew_thermostat_head2">Nein</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li> 
                                                        </ul>
                                                    </div> 
                                                </div>

                                                <div class="col-md-12 col-md-12 select d-flex mt-2">  
                                                    <div class="col-md-4">
                                                        <select class="form-control" id="design">
                                                            <option selected>Wählen...</option>
                                                            <option value="design1">Design 1</option>
                                                            <option value="design2">Design 2</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select class="form-control" id="design">
                                                            <option selected>Wählen...</option>
                                                            <option value="design1">Design 1</option>
                                                            <option value="design2">Design 2</option>
                                                        </select>
                                                    </div> 

                                                    <div class="col-md-4">
                                                        <select class="form-control" id="design">
                                                            <option selected>Wählen...</option>
                                                            <option value="design1">Design 1</option>
                                                            <option value="design2">Design 2</option>
                                                        </select>
                                                    </div> 
                                                </div>
            
                                                <div class="col-md-12 col-md-12 select d-flex mt-2"> 
                                                    <div class="col-md-3">
                                                        <label for="design" class="form-label mt-1">Steckdose vorhanden</label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <ul class="list-unstyled mb-0">
                                                            <li class="d-inline-block mr-2">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input" name="has_socket" id="has_socket1" checked="">
                                                                        <label class="custom-control-label" for="has_socket1">Ja</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-2">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input" name="has_socket" id="has_socket2">
                                                                        <label class="custom-control-label" for="has_socket2">Nein</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li> 
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-4 d-flex">
                                                        <input type="text" class="form-control" name="socket_distance"> 
                                                        <label for="socket_distance" class="form-label mt-1"> m</label>
                                                    </div> 
                                                </div> 
                                            </div>
                                    </div>  
                                </div>  
                            </div> 
                            <!-- Save Button --> 
                    </div> 
                    <hr> 
                </section>