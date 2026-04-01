                <div class="card-content">
                    <div class="card-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span><h4>UI Kennwerte bei STC</h4></span>
                                        </div>
                                    
                                    </div>
                                </div>
                
                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span>Spannung im MPP in V</span>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" id="spannung_mpp_v" class="form-control" value="{{old('spannung_mpp_v')}}" name="spannung_mpp_v" >
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span>Strom im MPP in A</span>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" id="strom_mpp_a" class="form-control" value="{{old('strom_mpp_a')}}" name="strom_mpp_a" >
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span>Leerlaufspannung in V</span>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" id="leerlaufspannung_v" class="form-control"  value="{{old(' leerlaufspannung_v')}}"  name="leerlaufspannung_v">
                                        </div>
                                    </div>
                                </div>
                                    <div class="col-6">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Kurzschlussstrom in A</span>
                                            </div>
                                    
                                        <div class="col-md-8">
                                            <input type="text" id="kurzschlussstrom_a	" class="form-control" value="{{old('kurzschlussstrom_a	')}}" name="kurzschlussstrom_a	">
                                        </div>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Erhöhung Leerlaufspannung vor Stabilisierung in %</span>
                                            </div>
                                    
                                        <div class="col-md-8">
                                            <input type="text" id="erhohung_leerlaufspannung" class="form-control" value="{{old('erhohung_leerlaufspannung')}}" name="erhohung_leerlaufspannung">
                                        </div>
                                        </div>
                                    </div>
                        

                                    <div class="col-6">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Nennleistung in W </span>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" id="nenleistung_w" class="form-control" value="{{old('nenleistung_w')}}" name="nenleistung_w">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Füllfaktor in % </span>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" id="fullfaktor_percent" class="form-control" value="{{old('fullfaktor_percent')}}" name="fullfaktor_percent">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Wirkungsgrad in %</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" id="wirkungsgrad_percent" class="form-control" value="{{old('wirkungsgrad_percent')}}" name="wirkungsgrad_percent">
                                            </div>
                                        </div>
                                    </div>

                            </div>
                        </div>
                    </div>
                </div>   