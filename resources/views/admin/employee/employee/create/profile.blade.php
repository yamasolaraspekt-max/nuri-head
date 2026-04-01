                        
                                                    <input type="hidden" name="active_tab" id="active_tab" value="profile"> 
                                               <form method="POST" action="{{ action('App\Http\Controllers\EmployeeController@profile_update') }}">
                                                    @csrf
                                                       <hr class="color-strip" id="colorStrip">
                                                    <input type="hidden" name="id" value="{{ $data->id}}" >
                                                    <div class="content-body">
                                                        <section id="page-account-settings">
                                                            <div class="row">
                                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                                    <div class="card">
                                                                        <div class="card-content">
                                                                            <div class="card-body">
                                                                                <div class="tab-content">
                                                                                    <div role="tabpanel" class="tab-pane active" id="account-vertical-general" aria-labelledby="account-pill-general" aria-expanded="true">
                                                                                        <div class="row">
                                                                                            <div class="col-12">
                                                                                                <div class="divider divider-left">
                                                                                                    <div class="divider-text">Persönliche Angaben</div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row">
                                                                                            <!-- Form fields for title, name, etc. -->
                                                                                            <div class="col-lg-1 col-md-2 col-sm-6">
                                                                                                <div class="form-group">
                                                                                                    <label for="accountSelect">Titel</label>
                                                                                                    <select class="form-control" id="accountSelect" name="title">
                                                                                                        <option disabled value="">Bitte wählen...</option>
                                                                                                        <option value="Mr." {{ old('title', $data->title) == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                                                                                                        <option value="Ms." {{ old('title', $data->title) == 'Ms.' ? 'selected' : '' }}>Ms.</option>
                                                                                                        <option value="Dr." {{ old('title', $data->title) == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                                                                                                        <option value="Pro." {{ old('title', $data->title) == 'Pro.' ? 'selected' : '' }}>Pro.</option>
                                                                                                    </select>
                                                                                                    @if ($errors->has('title'))
                                                                                                        <p style="color:red;">{!! $errors->first('title') !!}</p>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="col-lg-2 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="account-username">Name</label>
                                                                                                    <input type="text" class="form-control" value="{{ old('name', $data ? $data->name : '') }}" name="name" placeholder="Name" />
                                                                                                        @if ($errors->has('name'))
                                                                                                            <p style="color:red;">{!! $errors->first('name') !!}</p>
                                                                                                        @endif
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-2 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="account-name">Zweiter Vorname</label>
                                                                                                        <input type="text" class="form-control"  name="midname" value="{{ old('midname', $data->midname) }}"   >
                                                                                                        @if ($errors->has('midname'))
                                                                                                            <p style="color:red;">{!! $errors->first('midname') !!}</p>
                                                                                                        @endif
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-2 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="account-name">Familienname</label>
                                                                                                        <input type="text" class="form-control"  name="lastname" value="{{ old('lastname', $data->lastname) }}"  > 
                                                                                                        @if ($errors->has('lastname'))
                                                                                                            <p style="color:red;">{!! $errors->first('lastname') !!}</p>
                                                                                                        @endif
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-2 col-md-4 col-sm-6">
                                                                                                <label for="gender">Geschlecht</label>
                                                                                                <select class="form-control" name="gender" id="gender" >
                                                                                                    <option value="Male" {{ old('gender', $data->gender) == 'Male' ? 'selected' : '' }}>Männlich</option>
                                                                                                    <option value="Female" {{ old('gender', $data->gender) == 'Female' ? 'selected' : '' }}>Weiblich</option>
                                                                                                </select>
                                                                                                @if ($errors->has('gender'))
                                                                                                    <p style="color:red;">{!! $errors->first('gender') !!}</p>
                                                                                                @endif
                                                                                            </div>

                                                                                             <div class="col-lg-3 col-md-4 col-sm-6">
                                                                                                <label for="gender">Farbe</label>
                                                                                                <div class="color-container">
                                                                                                    <div id="colorIcon" class="color-icon"></div>
                                                                                                    <select id="colorPicker" class="color-select" name="color"></select>
                                                                                                </div>
                                                                                                @if ($errors->has('color'))
                                                                                                <p style="color:red;">{!! $errors->first('color') !!}</p>
                                                                                                @endif
                                                                                            </div>
                                                                                            <div class="col-lg-3 col-md-4 col-sm-6">
                                                                                                <div class="form-group">
                                                                                                    <label for="marital_status">Familienstand</label>
                                                                                                    <select class="form-control" name="marital_status" id="marital_status">
                                                                                                        <option selected disabled>{{ old('marital_status', $data->marital_status) }}</option>
                                                                                                        <option value="Ledig" {{ old('marital_status', $data->marital_status) == 'Ledig' ? 'selected' : '' }}>Ledig</option>
                                                                                                        <option value="verheiratet" {{ old('marital_status', $data->marital_status) == 'verheiratet' ? 'selected' : '' }}>verheiratet</option>
                                                                                                        <option value="Geschieden" {{ old('marital_status', $data->marital_status) == 'Geschieden' ? 'selected' : '' }}>Geschieden</option>
                                                                                                        <option value="Witwe" {{ old('marital_status', $data->marital_status) == 'Witwe' ? 'selected' : '' }}>Witwe</option>
                                                                                                    </select>
                                                                                                    @if ($errors->has('marital_status'))
                                                                                                        <p style="color:red;">{!! $errors->first('marital_status') !!}</p>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-3 col-md-4 col-sm-6">
                                                                                                <label for="kids">Kinder</label> 
                                                                                                <select class="form-control" name="kids" id="kids">
                                                                                                    <option selected disabled>{{ $data->kids == 'Yes' ? 'Ja' : 'Nein' }}</option>
                                                                                                    <option value="Yes" {{ old('kids') == 'Yes' ? 'selected' : '' }}>Ja</option>
                                                                                                    <option value="No" {{ old('kids') == 'No' ? 'selected' : '' }}>Nein</option>
                                                                                                </select>
                                                                                                @if ($errors->has('kids'))
                                                                                                    <p style="color:red;">{!! $errors->first('kids') !!}</p>
                                                                                                @endif
                                                                                            </div>
                                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="nationality">Staatsangehörigkeit</label>
                                                                                                        <select class="form-control" id="nationality" name="nationality">
                                                                                                            @foreach ($countries as $country)
                                                                                                                <option value="{{ $country->id }}" {{ old('nationality', $data->nationality) == $country->id ? 'selected' : '' }}>
                                                                                                                    {{ $country->nationality }}
                                                                                                                </option>
                                                                                                            @endforeach
                                                                                                        </select>
                                                                                                        @if ($errors->has('nationality'))
                                                                                                            <p style="color:red;">{!! $errors->first('nationality') !!}</p>
                                                                                                        @endif
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                                <label for="religion">Konfession</label>
                                                                                                <select class="form-control" name="religion" id="religion">
                                                                                                    <option disabled selected>{{ $data->religion }}</option>
                                                                                                    <option value="Katholisch" {{ old('religion') == 'Katholisch' ? 'selected' : '' }}>Katholisch</option>
                                                                                                    <option value="Evangelisch" {{ old('religion') == 'Evangelisch' ? 'selected' : '' }}>Evangelisch</option>
                                                                                                    <option value="Muslimisch" {{ old('religion') == 'Muslimisch' ? 'selected' : '' }}>Muslimisch</option>
                                                                                                    <option value="Orthodox" {{ old('religion') == 'Orthodox' ? 'selected' : '' }}>Orthodox</option>
                                                                                                    <option value="Keine" {{ old('religion') == 'Keine' ? 'selected' : '' }}>Keine</option>
                                                                                                    <option value="Hinduistisch" {{ old('religion') == 'Hinduistisch' ? 'selected' : '' }}>Hinduistisch</option>
                                                                                                    <option value="Buddhistisch" {{ old('religion') == 'Buddhistisch' ? 'selected' : '' }}>Buddhistisch</option>
                                                                                                    <option value="Jüdisch" {{ old('religion') == 'Jüdisch' ? 'selected' : '' }}>Jüdisch</option>
                                                                                                    <option value="Andere" {{ old('religion') == 'Andere' ? 'selected' : '' }}>Andere</option>
                                                                                                </select>
                                                                                                @if ($errors->has('religion'))
                                                                                                    <p style="color:red;">{!! $errors->first('religion') !!}</p>
                                                                                                @endif
                                                                                            </div>
                                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="dob">Geburtsdatum</label>
                                                                                                        <input type="date" class="form-control" required placeholder="Geburtsdatum" value="{{ old('dob', $data->dob) }}" name="dob" >
                                                                                                        @if ($errors->has('dob'))
                                                                                                            <p style="color:red;">{!! $errors->first('dob') !!}</p>
                                                                                                        @endif 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="place_birth">Geburtsort</label>
                                                                                                    <select class="form-control" id="country" name="country_id">
                                                                                                        @foreach ($countries as $country)
                                                                                                            <option value="{{ $country->id }}" {{ old('country_id', $data->country_id) == $country->id ? 'selected' : '' }}>
                                                                                                                {{ $country->country }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                    @if ($errors->has('place_birth'))
                                                                                                        <p style="color:red;">{!! $errors->first('place_birth') !!}</p>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="mother_tongue">Muttersprache</label>
                                                                                                    <select class="form-control" id="mother_tongue" name="mother_tongue">
                                                                                                        @foreach ($languages as $lang)
                                                                                                            <option value="{{ $lang->id }}" {{ old('mother_tongue', $data->mother_tongue) == $lang->id ? 'selected' : '' }}>
                                                                                                                {{ $lang->language }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                    @if ($errors->has('mother_tongue'))
                                                                                                        <p style="color:red;">{!! $errors->first('mother_tongue') !!}</p>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="language">Sprachen</label>
                                                                                                    <a href="" data-toggle="modal" data-target="#new_lang">
                                                                                                        <i class="feather icon-plus primary"><span>Neu</span></i>
                                                                                                    </a>
                                                                                                    <select class="form-control" id="language" multiple="multiple" name="language[]">
                                                                                                        @foreach ($languages as $lang)
                                                                                                            <option value="{{ $lang->id }}" 
                                                                                                                @if(in_array($lang->id, $emp_language->pluck('languages_id')->toArray())) selected @endif>
                                                                                                                {{ $lang->language }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                    @if ($errors->has('language'))
                                                                                                        <p style="color:red;">{!! $errors->first('language') !!}</p>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div> 
                                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="resident_permit">Aufenthaltstitel bei Ausländer</label>
                                                                                                    <select class="form-control" name="resident_permit" id="resident_permit">
                                                                                                        <option value="Yes" {{ old('resident_permit', $data->resident_permit) == 'Yes' ? 'selected' : '' }}>Ja</option>
                                                                                                        <option value="No" {{ old('resident_permit', $data->resident_permit) == 'No' ? 'selected' : '' }}>Nein</option>
                                                                                                    </select>
                                                                                                    @if ($errors->has('resident_permit'))
                                                                                                        <p style="color:red;">{!! $errors->first('resident_permit') !!}</p>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-2 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="resident_permit_end_date">Enddatum der Aufenthaltserlaubnis</label>
                                                                                                        <input type="date" class="form-control"  name="resident_permit_end_date"  value="{{ old('resident_permit_end_date', $data->resident_permit_end_date) }}" > 
                                                                                                        @if ($errors->has('resident_permit_end_date'))
                                                                                                            <p style="color:red;">{!! $errors->first('resident_permit_end_date') !!}</p>
                                                                                                        @endif
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="work_permit">Arbeitsberechtigung</label>
                                                                                                    <select class="form-control" name="work_permit" id="work_permit"> 
                                                                                                        <option value="Yes" {{ old('work_permit', $data->work_permit) == 'Yes' ? 'selected' : '' }}>Ja</option>
                                                                                                        <option value="No" {{ old('work_permit', $data->work_permit) == 'No' ? 'selected' : '' }}>Nein</option>
                                                                                                    </select>
                                                                                                    @if ($errors->has('work_permit'))
                                                                                                        <p style="color:red;">{!! $errors->first('work_permit') !!}</p>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-2 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="work_permit_end_date">Ende der Arbeitserlaubnis</label>
                                                                                                        <input type="date" class="form-control"   name="work_permit_end_date"  value="{{ old('work_permit_end_date', $data->work_permit_end_date) }}" > 
                                                                                                        @if ($errors->has('work_permit_end_date'))
                                                                                                            <p style="color:red;">{!! $errors->first('work_permit_end_date') !!}</p>
                                                                                                        @endif
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="branch">Steuer Klasse</label>
                                                                                                    @if(count($taxes))
                                                                                                        <select class="form-control" name="tax_class">
                                                                                                            @foreach ($taxes as $tax)
                                                                                                                <option value="{{ $tax->id }}" {{ old('tax_class', $data->tax_class) == $tax->id ? 'selected' : '' }}>
                                                                                                                    {{ $tax->tax }}% - {{$tax->class}}
                                                                                                                </option>
                                                                                                            @endforeach
                                                                                                        </select>
                                                                                                        @if ($errors->has('tax_class'))
                                                                                                            <p style="color:red;">{!! $errors->first('tax_class') !!}</p>
                                                                                                        @endif
                                                                                                    @else
                                                                                                        <a class="btn btn-success col-12" href="{{ url('/tax') }}">STEUER KLASSE HINZUFÜGEN</a>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="tax_id">Steuer ID</label>
                                                                                                        <input type="text" class="form-control" name="tax_id" value="{{ old('tax_id', $data->tax_id) }}" > 
                                                                                                        @if ($errors->has('tax_id'))
                                                                                                            <p style="color:red;">{!! $errors->first('tax_id') !!}</p>
                                                                                                        @endif
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="pension_no">RN-Nr.</label> 
                                                                                                        <input type="text" class="form-control" name="pension_no" value="{{ old('pension_no', $data->pension_no) }}" > 
                                                                                                        @if ($errors->has('pension_no'))
                                                                                                            <p style="color:red;">{!! $errors->first('pension_no') !!}</p>
                                                                                                        @endif
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="bio">Biografie</label>
                                                                                                    <textarea class="form-control" id="bio" name="bio" rows="3" placeholder="Ihre Biodaten hier..." >{{ old('bio', $data->bio )}}</textarea>
                                                                                                    @if ($errors->has('bio'))
                                                                                                        <p style="color:red;">{!! $errors->first('bio') !!}</p>
                                                                                                    @endif 
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row">
                                                                                            <div class="col-12">
                                                                                                <div class="divider divider-left">
                                                                                                    <div class="divider-text">
                                                                                                        <div class="badge badge-pill badge-primary">Befördererinformation</div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row">
                                                                                            <div class="col-lg-2 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="branch">Zuständigkeitsbereiche / Zweig</label>
                                                                                                    @if(count($branches))
                                                                                                        <select class="form-control" name="branch">
                                                                                                            @foreach ($branches as $bran)
                                                                                                                <option value="{{ $bran->id }}" {{ old('branch', $data->branch) == $bran->id ? 'selected' : '' }}>
                                                                                                                    {{ $bran->branch }}
                                                                                                                </option>
                                                                                                            @endforeach
                                                                                                        </select>
                                                                                                        @if ($errors->has('branch'))
                                                                                                            <p style="color:red;">{!! $errors->first('branch') !!}</p>
                                                                                                        @endif
                                                                                                    @else
                                                                                                        <a class="btn btn-success col-12" href="{{ url('/branch') }}">ZWEIG HINZUFÜGEN</a>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="col-lg-2 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="contract_type">Vertragstyp</label>
                                                                                                    @if(count($contracts))
                                                                                                        <select class="form-control" name="contract_type_id">
                                                                                                            @foreach ($contracts as $contract)
                                                                                                                <option value="{{ $contract->id }}" {{ old('contract_type_id', $data->contract_type_id ?? '') == $contract->id ? 'selected' : '' }}>
                                                                                                                    {{ $contract->contract_type }}
                                                                                                                </option>
                                                                                                            @endforeach
                                                                                                        </select>
                                                                                                        @if ($errors->has('contract_type_id'))
                                                                                                            <p style="color:red;">{!! $errors->first('contract_type_id') !!}</p>
                                                                                                        @endif
                                                                                                    @else
                                                                                                        <a class="btn btn-success col-12" href="{{ url('/contract_type') }}">VERTRAGSART HINZUFÜGEN</a>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="col-lg-2 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="contract_date">Vertragsdatum</label>
                                                                                                        <input type="date" class="form-control" name="contract_date" value="{{ old('contract_date',\Carbon\Carbon::parse($data->contract_date)->format('Y-m-d'))  }}" >
                                                                                                        @if ($errors->has('contract_date'))
                                                                                                            <p style="color:red;">{!! $errors->first('contract_date') !!}</p>
                                                                                                        @endif 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-2 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="working_hour">Arbeitsstunde</label>
                                                                                                        <input type="text" class="form-control" name="working_hour"   value="{{ old('working_hour', $data->working_hour) }}"  >
                                                                                                        @if ($errors->has('working_hour'))
                                                                                                            <p style="color:red;">{!! $errors->first('working_hour') !!}</p>
                                                                                                        @endif 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>

                                                                                             <div class="col-lg-2 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="daily_start_time">Arbeitsbeginn</label>
                                                                                                        <input type="time" class="form-control" name="daily_start_time" value="{{ old('daily_start_time', $data->daily_start_time) }}" placeholder="Arbeitsbeginn"  >
                                                                                                        @if ($errors->has('daily_start_time'))
                                                                                                        <p style="color:red;">{!! $errors->first('daily_start_time') !!}</p>
                                                                                                        @endif
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                                <div class="col-lg-2 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="daily_end_time">Arbeit Feierabend</label>
                                                                                                        <input type="time" class="form-control" name="daily_end_time" value="{{ old('daily_end_time',  $data->daily_end_time) }}" placeholder="Arbeitsbeginn"  >
                                                                                                        @if ($errors->has('daily_end_time'))
                                                                                                        <p style="color:red;">{!! $errors->first('daily_end_time') !!}</p>
                                                                                                        @endif
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-2 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="working_type">Vergütungsart</label>
                                                                                                    <select class="form-control" name="working_type">
                                                                                                        <option value="monthly" {{ old('working_type', $data->working_type ?? 'monthly') == 'monthly' ? 'selected' : '' }}>Monatsgehalt</option>
                                                                                                        <option value="hourly" {{ old('working_type', $data->working_type ?? '') == 'hourly' ? 'selected' : '' }}>Stundenlohn</option> 
                                                                                                        <option value="daily" {{ old('working_type', $data->working_type ?? '') == 'daily' ? 'selected' : '' }}>Taglohn</option> 
                                                                                                        <option value="piece_work" {{ old('working_type', $data->working_type ?? '') == 'piece_work' ? 'selected' : '' }}>Akkordlohn</option> 
                                                                                                        <option value="comission" {{ old('working_type', $data->working_type ?? '') == 'comission' ? 'selected' : '' }}>Provision</option> 
                                                                                                        <option value="mixed" {{ old('working_type', $data->working_type ?? '') == 'mixed' ? 'selected' : '' }}>Mischformen</option> 
                                                                                                    </select>
                                                                                                    @if ($errors->has('working_type'))
                                                                                                        <p style="color:red;">{!! $errors->first('working_type') !!}</p>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="col-lg-2 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="salary_per_hour">Lohn pro Stunde</label>
                                                                                                        <input type="text"  name="salary_per_hour" class="form-control"   value="{{ old('salary_per_hour',  $data->salary_per_hour) }}"  >
                                                                                                        @if ($errors->has('salary_per_hour'))
                                                                                                            <p style="color:red;">{!! $errors->first('salary_per_hour') !!}</p>
                                                                                                        @endif 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="leave">Urlaubstage</label>
                                                                                                        <input type="text" name="leave" class="form-control" value="{{ old('leave',  $data->leave )}}"  >
                                                                                                        @if ($errors->has('leave'))
                                                                                                            <p style="color:red;">{!! $errors->first('leave') !!}</p>
                                                                                                        @endif 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="sick_leave">Krankenstand</label>
                                                                                                        <input type="text" name="sick_leave" class="form-control" value="{{ old('sick_leave',  $data->sick_leave) }}"  >
                                                                                                        @if ($errors->has('sick_leave'))
                                                                                                            <p style="color:red;">{!! $errors->first('sick_leave') !!}</p>
                                                                                                        @endif 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="supervisor">Supervisor/in</label>
                                                                                                    <select class="form-control" id="supervisor" name="supervisor" style="width:100% !important;">
                                                                                                        <option value="Kein Betreuer/in" {{ old('supervisor', $data->supervisor) == 'Kein Betreuer/in' ? 'selected' : '' }}>Kein Betreuer/in</option>
                                                                                                        @foreach ($supervisor as $super)
                                                                                                            <option value="{{ $super->id }}" {{ old('supervisor', $data->supervisor) == $super->id ? 'selected' : '' }}>
                                                                                                                {{ $super->name }} {{ $super->lastname }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                    @if ($errors->has('supervisor'))
                                                                                                        <p style="color:red;">{!! $errors->first('supervisor') !!}</p>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row">
                                                                                            <div class="col-12">
                                                                                                <div class="divider divider-left">
                                                                                                    <div class="divider-text">
                                                                                                        <div class="badge badge-pill badge-primary">Auszubildenden-Informationen</div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row">
                                                                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="trainee">Ist er/sie ein Auszubildender/eine Auszubildende / einen befristet arbeitsvertrag?</label>
                                                                                                    <select class="form-control" name="trainee" id="trainee">
                                                                                                        <option value="" disabled {{ old('trainee', $data->trainee ?? '') == '' ? 'selected' : '' }}>Wenn dies nicht zutrifft, lassen Sie dieses Feld leer.</option>
                                                                                                        <option value="Yes" {{ old('trainee', $data->trainee ?? '') == 'Yes' ? 'selected' : '' }}>Ja</option>
                                                                                                        <option value="No" {{ old('trainee', $data->trainee ?? '') == 'No' ? 'selected' : '' }}>Nein</option>
                                                                                                    </select>
                                                                                                    @if ($errors->has('trainee'))
                                                                                                        <p style="color:red;">{!! $errors->first('trainee') !!}</p>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="trainee_start_date">wenn ja, Startdatum</label>
                                                                                                        <input type="date" name="trainee_start_date" value="{{ old('trainee_start_date',$data->trainee_start_date) }}" class="form-control" >
                                                                                                        @if ($errors->has('trainee_start_date'))
                                                                                                            <p style="color:red;">{!! $errors->first('trainee_start_date') !!}</p>
                                                                                                        @endif 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="trainee_end_date">Enddatum</label>
                                                                                                        <input type="date" name="trainee_end_date" value="{{ old('trainee_end_date',$data->trainee_end_date) }}" class="form-control" >
                                                                                                        @if ($errors->has('trainee_end_date'))
                                                                                                            <p style="color:red;">{!! $errors->first('trainee_end_date') !!}</p>
                                                                                                        @endif 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row">
                                                                                            <div class="col-12">
                                                                                                <div class="divider divider-left">
                                                                                                    <div class="divider-text">
                                                                                                        <div class="badge badge-pill badge-primary">Krankenkasse & Bankverbindung</div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row">
                                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="health_insurance">Krankenversicherung</label>
                                                                                                        <input type="text" name="health_insurance" value="{{ old('health_insurance', $data->health_insurance) }}" class="form-control" >
                                                                                                        @if ($errors->has('health_insurance'))
                                                                                                            <p style="color:red;">{!! $errors->first('health_insurance') !!}</p>
                                                                                                        @endif 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="insurance_id">Versicherungsnummer</label>
                                                                                                        <input type="text" name="insurance_id" value="{{ old('insurance_id',$data->insurance_id )}}" class="form-control" >
                                                                                                        @if ($errors->has('insurance_id'))
                                                                                                            <p style="color:red;">{!! $errors->first('insurance_id') !!}</p>
                                                                                                        @endif 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="bank_name">Bank Name</label>
                                                                                                        <input type="text" name="bank_name" value="{{ old('bank_name', $data->bank_name )}}" class="form-control" >
                                                                                                        @if ($errors->has('bank_name'))
                                                                                                            <p style="color:red;">{!! $errors->first('bank_name') !!}</p>
                                                                                                        @endif 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="iban">IBAN</label>
                                                                                                        <input type="text" value="{{ old('iban', $data->iban )}}" class="form-control" name="iban">
                                                                                                        @if ($errors->has('iban'))
                                                                                                            <p style="color:red;">{!! $errors->first('iban') !!}</p>
                                                                                                        @endif 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row">
                                                                                            <div class="col-12">
                                                                                                <div class="divider divider-left">
                                                                                                    <div class="divider-text">
                                                                                                        <div class="badge badge-pill badge-primary">Kontaktdetails</div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row">
                                                                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="email">E-mail</label>
                                                                                                        <input type="text" name="email" value="{{ old('email', $data->email )}}" class="form-control" >
                                                                                                        @if ($errors->has('email'))
                                                                                                            <p style="color:red;">{!! $errors->first('email') !!}</p>
                                                                                                        @endif 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="phone">Phone</label>
                                                                                                        <input type="text" name="phone" value="{{ old('phone',$data->phone )}}" class="form-control" >
                                                                                                        @if ($errors->has('phone'))
                                                                                                            <p style="color:red;">{!! $errors->first('phone') !!}</p>
                                                                                                        @endif 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="home_phone">Startseite Kontakt</label>
                                                                                                        <input type="text"  name="home_phone" value="{{ old('home_phone',$data->home_phone )}}" class="form-control" >
                                                                                                        @if ($errors->has('home_phone'))
                                                                                                            <p style="color:red;">{!! $errors->first('home_phone') !!}</p>
                                                                                                        @endif 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                                                                <div class="form-group">
                                                                                                    <div class="controls">
                                                                                                        <label for="work_phone">Arbeitskontakt</label>
                                                                                                        <input type="text" name="work_phone" value="{{ old('work_phone', $data->work_phone )}}" class="form-control" >
                                                                                                        @if ($errors->has('work_phone'))
                                                                                                            <p style="color:red;">{!! $errors->first('work_phone') !!}</p>
                                                                                                        @endif 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <br>
                                                                                        <hr>
                                                                                        <div class="col-12 d-flex flex-sm-row flex-column justify-content-end">
                                                                                            <button type="submit" class="btn btn-primary mr-sm-1 mb-1 mb-sm-0">Speichern</button>
                                                                                            <button type="reset" class="btn btn-outline-warning">Stornieren</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </section>
                                                    </div>
                                                </form>

                                         
                                                @section('script')
                                                <script>
                                                    $(document).ready(function() {
                                                        $('#department').select2();
                                                        $('#branch').select2();
                                                        $('#supervisor').select2();
                                                        $('#language').select2();
                                                        $('#grade').select2();
                                                        $('#position').select2(); 
                                                    });
                                              
                                                    
                                                </script>

                                                <script>
                                                    $(document).ready(function() {
                                                        @if(Session::has('update_msg'))
                                                        toastr.success("{{ session('updated_msg') }}");
                                                        @endif
                                                        @if(Session::has('save_msg'))
                                                        toastr.success("{{ session('save_msg') }}");
                                                        @endif



                                                        @if(Session::has('delete_msg'))
                                                        toastr.error("{{ session('delete_msg') }}");
                                                        @endif
                                                    });
                                                    </script>



                                            <script>
                                                document.addEventListener("DOMContentLoaded", function() {
                                                    const colors = [
                                                            { "hex": "006139", "name": "Dunkelgrün" }, 
                                                            { "hex": "009640", "name": "Grün" }, 
                                                            { "hex": "8abd24", "name": "Hellgrün" }, 
                                                            { "hex": "838b2d", "name": "Oliv" }, 
                                                            { "hex": "583c7a", "name": "Lila" }, 
                                                            { "hex": "891e82", "name": "Dunkellila" }, 
                                                            { "hex": "d5007f", "name": "Magenta" }, 
                                                            { "hex": "e78cba", "name": "Rosa" }, 
                                                            { "hex": "cd1719", "name": "Rot" }, 
                                                            { "hex": "e55c70", "name": "Hellrot" }, 
                                                            { "hex": "e9500e", "name": "Orange" }, 
                                                            { "hex": "ef9500", "name": "Hellorange" }, 
                                                            { "hex": "283583", "name": "Dunkelblau" }, 
                                                            { "hex": "0070ba", "name": "Blau" }, 
                                                            { "hex": "009fe3", "name": "Himmelblau" }, 
                                                            { "hex": "71cbf4", "name": "Hellblau" }, 
                                                            { "hex": "7d91c9", "name": "Grau-Blau" }, 
                                                            { "hex": "009bb1", "name": "Türkis" },

                                                            // Additional Colors:
                                                            { "hex": "4b5320", "name": "Moosgrün" }, 
                                                            { "hex": "006400", "name": "Dunkles Waldgrün" }, 
                                                            { "hex": "a3d900", "name": "Neon-Grün" }, 
                                                            { "hex": "ff1493", "name": "Neonpink" }, 
                                                            { "hex": "800000", "name": "Kastanienbraun" }, 
                                                            { "hex": "8b0000", "name": "Dunkelrot" }, 
                                                            { "hex": "ff4500", "name": "Feuerrot" }, 
                                                            { "hex": "ff8c00", "name": "Dunkelorange" }, 
                                                            { "hex": "ffd700", "name": "Gold" }, 
                                                            { "hex": "ffff00", "name": "Gelb" }, 
                                                            { "hex": "c0c0c0", "name": "Silber" }, 
                                                            { "hex": "808080", "name": "Grau" }, 
                                                            { "hex": "000000", "name": "Schwarz" }, 
                                                            { "hex": "ffffff", "name": "Weiß" }, 
                                                            { "hex": "8b4513", "name": "Schokoladenbraun" }, 
                                                            { "hex": "a52a2a", "name": "Braun" }, 
                                                            { "hex": "ffdab9", "name": "Pfirsich" }, 
                                                            { "hex": "40e0d0", "name": "Türkisblau" }
                                                        ];

                                                    const colorPicker = document.getElementById("colorPicker");
                                                    const colorStrip = document.getElementById("colorStrip");
                                                    const colorIcon = document.getElementById("colorIcon");

                                                    if (!colorPicker || !colorStrip || !colorIcon) {
                                                        console.error("Required elements not found!");
                                                        return;
                                                    }

                                                    // Fetch color from PHP variable (this should be dynamically printed from PHP)
                                                    const selectedColor = "<?php echo $data->color; ?>".replace("#", ""); // Remove # if present

                                                    // Populate the select options
                                                    colors.forEach(color => {
                                                        const option = document.createElement("option");
                                                        option.value = `#${color.hex}`;
                                                        option.textContent = color.name;
                                                        option.style.backgroundColor = `#${color.hex}`;
                                                        option.style.color = "#fff";
                                                        option.style.padding = "5px";
                                                        option.style.fontWeight = "bold";

                                                        // If the color matches $data->color, select it
                                                        if (color.hex.toLowerCase() === selectedColor.toLowerCase()) {
                                                            option.selected = true;
                                                            colorStrip.style.backgroundColor = `#${color.hex}`;
                                                            colorIcon.style.backgroundColor = `#${color.hex}`;
                                                        }

                                                        colorPicker.appendChild(option);
                                                    });

                                                    // Change background color on selection
                                                    colorPicker.addEventListener("change", function() {
                                                        if (this.value) {
                                                            colorStrip.style.backgroundColor = this.value;
                                                            colorIcon.style.backgroundColor = this.value;
                                                        }
                                                    });
                                                });
                                            </script>

                                                @endsection