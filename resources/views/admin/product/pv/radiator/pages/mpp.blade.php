                <div class="card-content">
                    <div class="card-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span><h4>MPP-Tracker</h4></span>
                                        </div>
                                    
                                    </div>
                                </div>
                
                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span>Leistungsbereich < 20% der Nennleistung in %</span>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" id="leistungs_lower_20" class="form-control" value="{{old('leistungs_lower_20')}}" name="leistungs_lower_20" >
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span>Leistungsbereich > 20% der Nennleistung in %</span>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" id="leistungs_greater_20" class="form-control" value="{{old('leistungs_greater_20')}}" name="leistungs_greater_20" >
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span>Parallelbetrieb </span>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" id="parallelbetrieb" class="form-control"  value="{{old(' parallelbetrieb')}}"  name="parallelbetrieb">
                                        </div>
                                    </div>
                                </div>
                                    <div class="col-6">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Anzahl MPP-Tracker </span>
                                            </div>
                                    
                                        <div class="col-md-8">
                                            <input type="text" id="anzahl_mpp_tracker" class="form-control" value="{{old('anzahl_mpp_tracker')}}" name="anzahl_mpp_tracker">
                                        </div>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Identische elektrische Eigenschaften </span>
                                            </div>
                                    
                                        <div class="col-md-8">
                                            <input type="text" id="identisch_electisch" class="form-control" value="{{old('identisch_electisch')}}" name="identisch_electisch">
                                        </div>
                                        </div>
                                    </div>
                        

                                    <div class="col-6">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Max. Eingangsstrom pro MPP-Tracker in A</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" id="	max_eingangsstrom_mpp_a" class="form-control" value="{{old('max_eingangsstrom_mpp_a')}}" name="	max_eingangsstrom_mpp_a">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Max. Kurzschlussstrom pro MPP-Tracker in A</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" id="max_kurzschlussstrom_mpp_a" class="form-control" value="{{old('max_kurzschlussstrom_mpp_a')}}" name="max_kurzschlussstrom_mpp_a">
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-6">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Max. Eingangsleistung pro MPP-Tracker in kW</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" id="	max_eingangsleistung_mpp_kw" class="form-control" value="{{old('max_eingangsleistung_mpp_kw')}}" name="	max_eingangsleistung_mpp_kw">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Min. MPP-Spannung in V</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" id="min_mpp_spannung_v" class="form-control" value="{{old('min_mpp_spannung_v')}}" name="min_mpp_spannung_v">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Max. MPP-Spannung in V</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" id="max_mpp_spannung_v" class="form-control" value="{{old('max_mpp_spannung_v')}}" name="max_mpp_spannung_v">
                                            </div>
                                        </div>
                                    </div>

                            </div>
                        </div>
                    </div>
                </div>   