     <div class="col-xl-6 col-sm-6 col-md-6   ">
        <div class="row"> 
            <div class="col-4">
                <h2 class="content-header-title float-left ">KUNDENDATEN</h2>
            </div>
            <div class="col-8 mb-2"> 
                <input type="hidden" name="answer_input" id="answer_input" value="0">
                <input type="hidden" name="total_number_input" id="total_number_input" value="30">
                <label for="" id="answered_number">0</label> / <label for="" id="total_number">30</label>
                
                <div class="progress progress-bar-primary progress-lg">
                    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                        <span id="percent">0%</span>
                    </div>
                </div>
            </div>
            <input type="hidden" value="{{$id}}" name="lead_id"> 
            <div class="card">
                <div class="row p-1 mb-2">
                    <div class="col-6"> 
                        <div class="col-12 mb-1"> 
                            <div class="col-md-12">
                                <span class="font-bold">Kunde-typ: </span> 
                                {{ $leads->customer_type }}
                            </div> 
                        </div>

                        <!-- Additional form fields go here -->
                        <div class="col-12 mb-1"> 
                            <div class="col-md-12">
                                <span class="font-bold">Kunde-Nr:</span> 
                                {{ $leads->customer_no }}
                            </div> 
                        </div>
                        <div class="col-12 mb-1 " id="firma-container"> 
                            <div class="col-md-12">
                                <span class="font-bold">Firma:</span> 
                                {{ $leads->firma ?? Null }}
                            </div> 
                        </div> 
                        <div class="col-12 mb-1"> 
                            <div class="col-md-12">
                                <span  class="font-bold">Kunde</span> 
                                    {{ $leads->title }}   {{ $leads->name }} {{ $leads->lastname }}
                            </div> 
                        </div>

                        <div class="col-12 mb-1"> 
                            <div class="col-md-12">
                                <span class="font-bold">Adresse:</span> 
                                {{ $leads->street }}  {{ $leads->postcode }},  {{ $leads->city }}
                            </div> 
                        </div>
                        
                    </div>
                    <div class="col-6">
                        <div class="row">
                            
                            <div class="col-12"> 
                                <div class="col-md-12">
                                    <span class="font-bold">Quelle</span> 
                                    {{ $leads->source }}
                                </div> 
                            </div>
                            <div class="col-12"> 
                                <div class="col-md-12">
                                    <span class="font-bold">Info:</span> 
                                    {{ $leads->info }}
                                </div> 
                            </div> 
                            @php
                            $user_name = DB::table('employees')
                            ->join('users', 'users.name', '=', 'employees.id')
                            ->select('employees.name', 'employees.lastname')
                            ->where('users.name', '=', $leads->contact_person)
                            ->first()
                            @endphp

                            @php
                            $employee = DB::table('employees')
                            ->select('employees.id','employees.name', 'employees.lastname')
                            ->get()
                            @endphp
                            <div class="col-12"> 
                                    <div class="col-md-12">
                                        <span class="font-bold">erste Kontaktperson</span> 
                                        @if($user_name)
                                        {{ $user_name->name }} {{ $user_name->lastname }}
                                        @else
                                        <div class="alert alert-danger" role="alert">
                                            <h4 class="alert-heading">Info</h4>
                                            <p class="mb-0">
                                                There is no Employee in the system!
                                            </p>
                                        </div>
                                        @endif 
                                    </div> 
                            </div>

                                <div class="col-12 mb-1 p-2"> 
                                <div class="col-md-12">
                                    <span class="font-bold">Kontakt:</span> 
                                    <p style="margin:0; line-height:1px" class="mb-1"><i class="feather icon-phone-call" ></i> {{ $leads->telephone }}</p>
                                    <p style="margin:0; line-height:1px" class="mb-1"><i class="feather icon-smartphone" ></i> {{ $leads->phone }} 
                                    <p style="margin:0; line-height:1px"><i class="feather icon-mail" ></i> {{ $leads->email }}</p>
                                </div> 
                            </div>  
                        </div> 
                    </div> 
                </div>
            </div> 
        </div> 
    </div>  

    <div class="col-xl-6 col-sm-6 col-md-6  ">
        <div class="row">
            <div class="col-6">
                <div class="col-12">
                    <div class="form-group row form-element">
                        <div class="col-md-12">
                            <span>Anfrage-Datum</span>
                        </div>
                        <div class="col-md-12 p-0">
                        <input type="date" class="form-control text form-element" name="request_date" 
                            value="{{ old('request_date', \Carbon\Carbon::today()->toDateString()) }}" />

                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group row form-element">
                        <div class="col-md-12">
                            <span>Objektname</span>
                        </div>
                        <div class="col-md-12 p-0">
                            <input type="text" class="form-control text form-element" name="object_name" value="{{ old('object_name', $alternative->object_name )}}" />
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <span>Dringlichkeit</span>
                        </div>
                        <div class="col-md-8 p-0">
                            <div class="star-rating form-element">
                                <select name="periority" id="" class="form-control text form-element"> 
                                    <Option value="Normal" @if(isset($inquiry->periority) && $inquiry->periority == 'Normal') selected @endif>Normal</Option>
                                    <Option value="Dringend" @if(isset($inquiry->periority) && $inquiry->periority == 'Dringend') selected @endif>Dringend</Option>
                                    <Option value="Sehr dringend" @if(isset($inquiry->periority) && $inquiry->periority == 'Sehr dringend') selected @endif>Sehr dringend</Option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div> 

                <div class="col-12">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <span>Betrieb</span>
                        </div>
                        <div class="col-md-8 p-0">
                            <div class="star-rating form-element">
                                <select name="branch_id" id="" class="form-control text form-element">  
                                    @foreach ($branch as $br)
                                    <Option value="{{ $br->id }}"@if(isset($inquiry->branch_id) && $inquiry->branch_id == $br->id) selected @elseif($leads->branch == $br->id) selected  @endif >
                                        {{ $br->branch }}
                                    </Option> 
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div> 
                    
                <div class="col-12">
                    <div class="form-group row form-element">
                        <div class="col-md-6">
                            <span>Kunde aufgefordert Unterlagen zu schicken</span>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled mb-0">
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input form-element"   name="document"  id="customRadio1" @if($alternative->document == 'on') checked @endif>
                                            <label class="custom-control-label" for="customRadio1">Ja</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-2">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input form-element"  checked name="document" id="customRadio2" @if($alternative->document != 'on') checked @endif>
                                            <label class="custom-control-label" for="customRadio2">Nein</label>
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Alternative Address Inputs --> 
                <div class="col-12" id="street2s">
                    <div class="form-group row form-element">
                        <div class="col-md-4">
                            <span>STR./NR./PLZ./ORT</span>
                        </div>
                        <div class="col-md-8 p-0">
                            <input id="full_address2" type="text" class="form-control text form-element" placeholder="Adresse eingeben" name="full_address" value="{{ old('full_address2', $alternative->full_address) }}">
                            <input type="hidden" id="latitude-input2" name="latitude" value="{{ old('latitude', $alternative->lat) }}">
                            <input type="hidden" id="longitude-input2" name="longitude" value="{{ old('longitude', $alternative->lon) }}">
                            <input type="hidden" id="elevation-input2" placeholder="Elevation in meters" name="elevation" value="{{ old('elevation', $alternative->elevation) }}">
                            <input type="hidden" class="form-control text form-element" value="{{old('postcode', $alternative->postcode)}}" name="postcode" id="postal_code-input2">
                            <input type="hidden" class="form-control text form-element" value="{{old('city2', $alternative->city)}}" name="city" id="locality-input2">
                            <input type="hidden" class="form-control text form-element" value="{{old('street', $alternative->street)}}" name="street" id="street-input2">

                        </div>
                    </div>
                </div> 
            </div> 
            <div class="col-6">
                <div class="col-12">
                    <div class="form-group row form-element">
                        <div class="col-md-4">
                            <span>Notizen</span>
                        </div>
                        <div class="col-md-8 p-0"> 
                            <textarea name="note" id="" cols="30" rows="3" class="form-control">
                                        {{ old('note', $inquiry['note'] ?? $alternative->note) }}
                            </textarea>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group row form-element">
                        <div class="col-md-4">
                            <span>Termin für die Erstberatung vorhanden?</span>
                        </div>
                        <div class="col-md-8">
                            <input type="date" class="form-control text form-element" name="appointment">
                        </div>
                    </div>
                </div> 
                <div class="col-12">
                    <div class="form-group row form-element">
                        <div class="col-md-12">
                            <ul class="list-unstyled mb-0" style="    display: flex;flex-direction: column;">
                                <li class="d-inline-block mb-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input form-element" name="appointment_by" id="appointment_by_telefonisch" value="telefonisch" @if($alternative->appointment_by == 'telefonisch') checked @endif>
                                            <label class="custom-control-label" for="appointment_by_telefonisch">telefonisch</label>
                                        </div>
                                    </fieldset>
                                </li>
                        
                                <li class="d-inline-block mb-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input form-element" name="appointment_by" id="appointment_by_ort" value="Vor Ort Besuch" @if($alternative->appointment_by == 'Vor Ort Besuc') checked @endif>
                                            <label class="custom-control-label" for="appointment_by_ort">Vor Ort Besuch</label>
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
    