        <section id="nav-filled">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card overflow-hidden"> 
                        <div class="card-content">
                            <div class="card-body"> 
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="home-tab-fill" data-toggle="tab" href="#home-fill" role="tab" aria-controls="home-fill" aria-selected="true">Hauptadresse des Mitarbeiters</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="profile-tab-fill" data-toggle="tab" href="#profile-fill" role="tab" aria-controls="profile-fill" aria-selected="false">Notfallkontakt</a>
                                    </li> 
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content pt-1">
                                    <div class="tab-pane active" id="home-fill" role="tabpanel" aria-labelledby="home-tab-fill">
                                        <div class="row">
                                           <div class="col-12">
                                             <button type="button" class="btn btn-outline-primary waves-effect waves-light float-right mb-1" data-toggle="modal" data-target="#main_address">
                                                erstellen
                                            </button> 
                                           </div>
                                            <div class="modal fade text-left" id="main_address" tabindex="-1" role="dialog" aria-labelledby="myModalLabel140" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary white">
                                                            <h5 class="modal-title" id="myModalLabel140">Hauptadresse des Mitarbeiters</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                        </div>
                                                        <form novalidate action="{{ route('emp.address.save')}}" method="post"  class="custom-file-upload" enctype="multipart/form-data" >
                                                            @csrf
                                                            <input type="hidden" name="active_tab" id="active_tab" value="address"> 
                                                            <div class="modal-body"> 
                                                                <table class="table" id="address_table" > 
                                                                        <thead>
                                                                            <tr> 
                                                                                <th>Adressname</th>
                                                                                <th>Straße</th>
                                                                                <th>Wohnung</th>
                                                                                <th>Postleitzahl</th>
                                                                                <th>Stadt</th>
                                                                                <th>Action</th>
                                                                            </tr>
                                                                        </thead>
                                                                                
                                                                        <tbody>
                                                                            <tr>
                                                                                <input type="hidden" name="address[0][emp_id]" value="{{$data->id}}" >  
                                                                                <td>
                                                                                    <select class="form-control"  name="address[0][address_name]">>
                                                                                        <option value="Heim" selected>Heim</option>
                                                                                        <option value="Büro">Büro</option>
                                                                                        <option value="Lagerhaus">Lagerhaus</option>
                                                                                        <option value="Notfall">Notfall</option>
                                                                                    </select>
                                                                                </td>
                                                                                <td><input type="text" class="form-control required" placeholder="Straße..." name="address[0][street]"></td>
                                                                    
                                                                                <td><input type="text" class="form-control required" placeholder="Wohnung..." name="address[0][apartment]"></td>
                                                                            
                                                                                <td><input type="number" class="form-control required" placeholder="Postleitzahl..." name="address[0][postal]"></td>
                                                                            
                                                                                <td><input type="text" class="form-control" placeholder="Stadt..." name="address[0][city]"></td>
                                                                            
                                                                                <td>
                                                                                <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" id="add_address"><i class="feather icon-plus"></i></button>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>  
                                                                        
                                                                    </table> 
                                                                        
                                                            
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">abbrechen</button>
                                                                <button type="submit" class="btn btn-outline-primary "><i class="feather icon-save"></i> Datensatz speichern</button>  
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <div class="card-body" style="padding-top: 0px;">
                                                        @if ($errors->addressForm->any())
                                                            <div class="alert alert-danger">
                                                                <ul>
                                                                    @foreach ($errors->addressForm->all() as $error)
                                                                        <li>{{ $error }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                        
                                                        <table class="table">
                                                            <thead>
                                                                <tr> 
                                                                    <th>Adressname</th>
                                                                    <th>Straße</th>
                                                                    <th>Wohnung</th>
                                                                    <th>Postleitzahl</th>
                                                                    <th>Stadt</th>
                                                                    <th>Hauptadresse</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($addresses as $add)
                                                                <tr> 
                                                                    <td>{{ $add->address_name }}</td>
                                                                    <td>{{ $add->street }}</td>
                                                                    <td>{{ $add->apartment }}</td>
                                                                    <td>{{ $add->postal }}</td>
                                                                    <td>{{ $add->city }}</td>
                                                                    <td>
                                                                        {{ $add->main == 'active' ? 'Ja' : 'Nein' }}
                                                                    </td>
                                                                
                                                                    <td>
                                                                        @if($add->main != 'active')
                                                                            <a type="button" href="{{ url('emp_address_main/'.$add->id)}}"class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1"><i class="feather icon-map-pin"></i></a> 
                                                                        @else
                                                                            <a type="button" href="{{ url('emp_address_main_deactive/'.$add->id)}}"class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1"><i class="feather icon-map-pin"></i></a> 

                                                                        @endif
                                                                    <a type="button" href="{{ url('emp_address_delete/'.$add->id)}}"class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1"><i class="feather icon-trash-2"></i></a>
                                                                    <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" data-toggle="modal" data-target="#address_edit{{$add->id}}"><i class="feather icon-edit"></i></button>
                                                                        <!-- Edit Model: Start -->
                                                                        <div class="modal fade text-left" id="address_edit{{$add->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h4 class="modal-title" id="myModalLabel1">{{ $data->name }} {{ $data->lastname }}</h4>
                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                            <span aria-hidden="true">&times;</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                    <form class="form-horizontal" novalidate method="post" action="{{ action('App\Http\Controllers\EmployeeAddressController@update') }}" class="custom-file-upload" enctype="multipart/form-data">
                                                                                        @csrf
                                                                                        <fieldset> 
                                                                                            <div class="row">
                                                                                                <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                        <label for="Title">
                                                                                                        Adressname
                                                                                                        </label>
                                                                                                        <input type="hidden" name="id" value="{{ $add->id}}">
                                                                                                        <input type="hidden" name="emp_id" value="{{$data->id}}" >
                                                                                                        <select class="form-control"  name="address_name" >
                                                                                                                <option selected value="{{ $add->address_name}}" >#{{ $add->address_name}}</option>
                                                                                                            <option value="Heim" >Heim</option>
                                                                                                            <option value="Büro">Büro</option>
                                                                                                            <option value="Lagerhaus">Lagerhaus</option>
                                                                                                            <option value="Notfall">Notfall</option>
                                                                                                        </select>
                                                                                                        @if ($errors->has('address_name'))<p style="color:red;">{!!$errors->first('address_name')!!}</p>@endif
                                                                                                    </div>
                                                                                                </div>


                                                                                                <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                        <label for="Title">
                                                                                                        Straße
                                                                                                        </label>
                                                                                                    
                                                                                                        <input type="text" class="form-control"  name="street"  value="{{ $add->street}}" r required>
                                                                                                        @if ($errors->has('street'))<p style="color:red;">{!!$errors->first('street')!!}</p>@endif
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                        <label for="Title">
                                                                                                        Wohnung
                                                                                                        </label>
                                                                                                    
                                                                                                        <input type="text" class="form-control"  name="apartment"  value="{{ $add->apartment}}" required>
                                                                                                        @if ($errors->has('apartment'))<p style="color:red;">{!!$errors->first('apartment')!!}</p>@endif
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                        <label for="Title">
                                                                                                        Postleitzahl
                                                                                                        </label>
                                                                                                    
                                                                                                        <input type="text" class="form-control"  name="postal"  value="{{ $add->postal}}" required>
                                                                                                        @if ($errors->has('postal'))<p style="color:red;">{!!$errors->first('postal')!!}</p>@endif
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                        <label for="Title">
                                                                                                        Stadt
                                                                                                        </label>
                                                                                                    
                                                                                                        <input type="text" class="form-control"  name="city"  value="{{ $add->city}}" required>
                                                                                                        @if ($errors->has('city'))<p style="color:red;">{!!$errors->first('city')!!}</p>@endif
                                                                                                    </div>
                                                                                                </div>
                                                                                                
                            
                                                                                            </div>
                                                                                            
                                                                                        </fieldset>

                                                                                        

                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="submit" class="btn btn-primary">Submit</button>
                                                                                        
                                                                                        <!-- Edit Model: End -->
                                                                                    </div>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div> 
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  
                                    </div>
                                    <div class="tab-pane" id="profile-fill" role="tabpanel" aria-labelledby="profile-tab-fill">
                                        <div class="row">
                                           <div class="col-12">
                                             <button type="button" class="btn btn-outline-primary waves-effect waves-light float-right mb-1" data-toggle="modal" data-target="#emergency_address">
                                                erstellen
                                            </button> 
                                           </div>
                                            <div class="modal fade text-left" id="emergency_address" tabindex="-1" role="dialog" aria-labelledby="myModalLabel140" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary white">
                                                            <h5 class="modal-title" id="myModalLabel140">Notfall-Kontaktdaten</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                        </div>
                                                        <form novalidate action="{{ route('emergency.save')}}" method="post"  class="custom-file-upload" enctype="multipart/form-data" >
                                                            @csrf
                                                            <input type="hidden" name="active_tab" id="active_tab" value="address"> 
                                                            <div class="modal-body"> 
                                                                <div id="emergency_contacts">
                                                                    <div class="emergency-contact">
                                                                        <input type="hidden" name="emer[0][emp_id]" value="{{$data->id}}">

                                                                        <div class="row">
                                                                            <div class="col-md-4">
                                                                                <label>Beziehung</label>
                                                                                <select class="form-control" name="emer[0][relation]">
                                                                                    <option value="Vater" selected>Vater</option>
                                                                                    <option value="Mutter">Mutter</option>
                                                                                    <option value="Frau">Frau</option>
                                                                                    <option value="Ehemann">Ehemann</option>
                                                                                    <option value="Schwester">Schwester</option>
                                                                                    <option value="Bruder">Bruder</option>
                                                                                    <option value="Freund">Freund</option>
                                                                                    <option value="Nachbar">Nachbar</option>
                                                                                    <option value="Familie">Familie</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <label>Telefon</label>
                                                                                <input type="text" class="form-control required" placeholder="Telefon..." name="emer[0][phone]">
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <label>Festnetztelefon</label>
                                                                                <input type="text" class="form-control required" placeholder="Festnetztelefon..." name="emer[0][home_phone]">
                                                                            </div>
                                                                        </div>

                                                                        <div class="row mt-2">
                                                                            <div class="col-md-4">
                                                                                <label>Email</label>
                                                                                <input type="text" class="form-control required" placeholder="Email..." name="emer[0][email]">
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <label>Straße</label>
                                                                                <input type="text" class="form-control required" placeholder="Straße..." name="emer[0][street]">
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <label>Postleitzahl</label>
                                                                                <input type="number" class="form-control required" placeholder="Postleitzahl..." name="emer[0][postal]">
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <label>Stadt</label>
                                                                                <input type="text" class="form-control" placeholder="Stadt..." name="emer[0][city]">
                                                                            </div>
                                                                        </div> 
                                                                    </div>
                                                                </div>
                                                            
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">abbrechen</button>
                                                                <button type="submit" class="btn btn-outline-primary "><i class="feather icon-save"></i> Datensatz speichern</button>  
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Mitarbeitername</th>
                                                            <th>Beziehung</th>
                                                            <th>Telefon</th>
                                                            <th>Festnetztelefon</th>
                                                            <th>Email</th>
                                                            <th>Straße</th>
                                                            <th>Postleitzahl</th>
                                                            <th>Stadt</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($emergency as $em)
                                                        <tr>
                                                            
                                                            <td>{{ $data->name }} {{ $data->lastname }}</td>
                                                            <td>{{ $em->relation }}</td>
                                                            <td>{{ $em->phone }}</td>
                                                            <td>{{ $em->home_phone }}</td>
                                                            <td>{{ $em->email }}</td>
                                                            <td>{{ $em->street }}</td>
                                                            <td>{{ $em->postal }}</td>
                                                            <td>{{ $em->city }}</td>
                                                        
                                                            <td>
                                                               
                                                            <a type="button" href="{{ url('emergency_delete/'.$em->id)}}"class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1"><i class="feather icon-trash-2"></i></a>
                                                            <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" data-toggle="modal" data-target="#emergency{{$em->id}}"><i class="feather icon-edit"></i></button>
                                                                <!-- Edit Model: Start -->
                                                                <div class="modal fade text-left" id="emergency{{$em->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h4 class="modal-title" id="myModalLabel1">{{ $data->name }} {{ $data->lastname }}</h4>
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                            <form class="form-horizontal" novalidate method="post" action="{{ action('App\Http\Controllers\EmergencyContactController@update') }}" class="custom-file-upload" enctype="multipart/form-data">
                                                                                @csrf
                                                                                <fieldset> 
                                                                                    <div class="row">
                                                                                        <div class="col-md-12">
                                                                                            <div class="form-group">
                                                                                                <label for="Title">
                                                                                                Adressname
                                                                                                </label>
                                                                                                <input type="hidden" name="id" value="{{ $em->id}}">
                                                                                                <input type="hidden" name="emp_id" value="{{$data->id}}" >
                                                                                                

                                                                                                <select class="form-control"  name="relation">
                                                                                                    <option value="{{ $em->relation }}" selected>{{ $em->relation }}</option>
                                                                                                    <option value="Vater" >Vater</option>
                                                                                                    <option value="Mutter">Mutter</option>
                                                                                                    <option value="Frau">Frau</option>
                                                                                                    <option value="Ehemann">Ehemann</option>
                                                                                                    <option value="Schwester">Schwester</option>
                                                                                                    <option value="Bruder">Bruder</option>
                                                                                                    <option value="Freund">Freund</option>
                                                                                                    <option value="Nachbar">Nachbar</option>
                                                                                                    <option value="Familie">Familie</option>
                                                                                                </select>
                                                                                                @if ($errors->has('relation'))<p style="color:red;">{!!$errors->first('relation')!!}</p>@endif
                                                                                            </div>
                                                                                        </div>


                                                                                        <div class="col-md-12">
                                                                                            <div class="form-group">
                                                                                                <label for="Title">
                                                                                                Telefon
                                                                                                </label>
                                                                                            
                                                                                                <input type="text" class="form-control"  name="phone"  value="{{ $em->phone}}"  required>
                                                                                                @if ($errors->has('phone'))<p style="color:red;">{!!$errors->first('phone')!!}</p>@endif
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-12">
                                                                                            <div class="form-group">
                                                                                                <label for="Title">
                                                                                                Festnetztelefon
                                                                                                </label>
                                                                                            
                                                                                                <input type="text" class="form-control"  name="home_phone"  value="{{ $em->home_phone}}" required>
                                                                                                @if ($errors->has('home_phone'))<p style="color:red;">{!!$errors->first('home_phone')!!}</p>@endif
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-12">
                                                                                            <div class="form-group">
                                                                                                <label for="Title">
                                                                                                Email
                                                                                                </label>
                                                                                            
                                                                                                <input type="text" class="form-control"  name="street"  value="{{ $em->email}}" required>
                                                                                                @if ($errors->has('street'))<p style="color:red;">{!!$errors->first('street')!!}</p>@endif
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-12">
                                                                                            <div class="form-group">
                                                                                                <label for="Title">
                                                                                                Straße
                                                                                                </label>
                                                                                            
                                                                                                <input type="text" class="form-control"  name="street"  value="{{ $em->street}}"  required>
                                                                                                @if ($errors->has('street'))<p style="color:red;">{!!$errors->first('street')!!}</p>@endif
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="col-md-12">
                                                                                            <div class="form-group">
                                                                                                <label for="Title">
                                                                                                Postleitzahl
                                                                                                </label>
                                                                                            
                                                                                                <input type="text" class="form-control"  name="postal"  value="{{ $em->postal}}" required>
                                                                                                @if ($errors->has('postal'))<p style="color:red;">{!!$errors->first('postal')!!}</p>@endif
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-12">
                                                                                            <div class="form-group">
                                                                                                <label for="Title">
                                                                                                Stadt
                                                                                                </label>
                                                                                                <input type="text" class="form-control"  name="city"  value="{{ $em->city}}" required>
                                                                                                @if ($errors->has('city'))<p style="color:red;">{!!$errors->first('city')!!}</p>@endif
                                                                                            </div>
                                                                                        </div>
                                                                                        
                    
                                                                                    </div>
                                                                                    
                                                                                </fieldset>

                                                                                

                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                                                
                                                                                <!-- Edit Model: End -->
                                                                            </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div> 
                                                            </td>
                                                        </tr>
                                                        @endforeach
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
        </section> 
                                        
                                                    

        @push('scripts')
        <script>
            var j = 0;

            document.getElementById('add_address').addEventListener('click', function(){
                ++j;
                var addressTable = document.getElementById('address_table');

                var newRow = document.createElement('tr');
                newRow.innerHTML = '<input type="hidden" name="address['+j+'][emp_id]" value="{{$data->id}}">' + 
                                '<td><select class="form-control" name="address['+j+'][address_name]">' +
                                '<option value="Heim" selected>Heim</option><option value="Büro">Büro</option>' +
                                '<option value="Lagerhaus">Lagerhaus</option><option value="Notfall">Notfall</option></select></td>' +
                                '<td><input type="text" class="form-control required" placeholder="Straße..." name="address['+j+'][street]"></td>' +
                                '<td><input type="text" class="form-control required" placeholder="Wohnung..." name="address['+j+'][apartment]"></td>' +
                                '<td><input type="number" class="form-control required" placeholder="Postleitzahl..." name="address['+j+'][postal]"></td>' +
                                '<td><input type="text" class="form-control" placeholder="Stadt..." name="address['+j+'][city]"></td>' +
                                '<td><button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1 remove_address">' +
                                '<i class="feather icon-minus-square"></i></button></td>';

                addressTable.appendChild(newRow);
            });

            document.addEventListener('click', function(event) {
                // Ensure we target the button, even if the icon inside is clicked
                let targetButton = event.target.closest('.remove_address');
                if (targetButton) {
                    let row = targetButton.closest('tr');
                    if (row) row.remove();
                }
            });
        </script>


<script>
    var em = 0;

    document.getElementById('add_emergency').addEventListener('click', function(){
        ++em;
        var emergencyTable = document.getElementById('emergency_table');

        var newRow = document.createElement('tr');
        newRow.innerHTML = `
            <input type="hidden" name="emer[${em}][emp_id]" value="{{$data->id}}"> 
            <td>
                <select class="form-control" name="emer[${em}][relation]">
                    <option value="Vater" selected>Vater</option>
                    <option value="Mutter">Mutter</option>
                    <option value="Frau">Frau</option>
                    <option value="Ehemann">Ehemann</option>
                    <option value="Schwester">Schwester</option>
                    <option value="Bruder">Bruder</option>
                    <option value="Friend">Freund</option>
                    <option value="Nachbar">Nachbar</option>
                    <option value="Familie">Familie</option>
                </select>
            </td>
            <td><input type="text" class="form-control required" placeholder="Telefon..." name="emer[${em}][phone]"></td>
            <td><input type="text" class="form-control required" placeholder="Festnetztelefon..." name="emer[${em}][home_phone]"></td>
            <td><input type="text" class="form-control required" placeholder="Email..." name="emer[${em}][email]"></td>
            <td><input type="text" class="form-control required" placeholder="Straße..." name="emer[${em}][street]"></td>
            <td><input type="number" class="form-control required" placeholder="Postleitzahl..." name="emer[${em}][postal]"></td>
            <td><input type="text" class="form-control" placeholder="Stadt..." name="emer[${em}][city]"></td>
            <td>
                <button type="button" class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1 remove_emergency">
                    <i class="feather icon-minus-square"></i>
                </button>
            </td>
        `;

        emergencyTable.appendChild(newRow);
    });

    document.addEventListener('click', function(event) {
        // ✅ Ensure the click is on the button or the icon inside the button
        let targetButton = event.target.closest('.remove_emergency');
        if (targetButton) {
            let row = targetButton.closest('tr');
            if (row) row.remove();
        }
    });
</script>

@endpush