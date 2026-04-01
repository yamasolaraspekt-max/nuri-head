                <div class="card-content">
                    <div class="card-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-md-12">
                                            <span><h4>Weitere Parameter </h4></span>
                                        </div>
                                    
                                    </div>
                                </div>
                
                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span>Temperaturkoeffizient Uoc in mV/K</span>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" id="temp_uoc_mv_k" class="form-control" value="{{old('temp_uoc_mv_k')}}" name="temp_uoc_mv_k" >
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span> Temperaturkoeffizient Uoc in %/K</span>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" id="temp_uoc_k_percent" class="form-control" value="{{old('temp_uoc_k_percent')}}" name="temp_uoc_k_percent" >
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span>Temperaturkoeffizient Isc in mA/K</span>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" id="temp_isc_ma_k	" class="form-control"  value="{{old(' temp_isc_ma_k	')}}"  name="temp_isc_ma_k	">
                                        </div>
                                    </div>
                                </div>
                                    <div class="col-6">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Temperaturkoeffizient Isc in %/k</span>
                                            </div>
                                    
                                        <div class="col-md-8">
                                            <input type="text" id="temp_isc_k_percent" class="form-control" value="{{old('temp_isc_k_percent')}}" name="temp_isc_k_percent">
                                        </div>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Temperaturkoeffizient Pmpp in %/K</span>
                                            </div>
                                    
                                        <div class="col-md-8">
                                            <input type="text" id="temp_pmpp_k" class="form-control" value="{{old('temp_pmpp_k')}}" name="temp_pmpp_k">
                                        </div>
                                        </div>
                                    </div>
                        

                                    <div class="col-6">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Winkelkorrektur (IAM) in % </span>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" id="winkelkorrecktur_iam_percent" class="form-control" value="{{old('winkelkorrecktur_iam_percent')}}" name="winkelkorrecktur_iam_percent">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Maximale Systemspanunng in V</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" id="maximale_systemspannung_v" class="form-control" value="{{old('maximale_systemspannung_v')}}" name="maximale_systemspannung_v">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Bifazialitätsfaktor in %</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" id="bifazial_tatsfaktor_percent" class="form-control" value="{{old('bifazial_tatsfaktor_percent')}}" name="bifazial_tatsfaktor_percent">
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>   