                            
                                            <div class="header">
                                                    <div class="header-actions float-right mb-2">
                                                        <button class="btn btn-icon btn-pills btn-primary" type="button" data-toggle="modal" data-target="#new_rent">Neue</button>
                                                    </div>
                                                    <h2 class="mb-0 title"><i class="fa fa-home primary" ></i> Mietobjekte</h2>
                                                        <!-- New Rent Location Dialog: Start  -->
                                                        <div class="modal fade text-left" id="new_rent" tabindex="-1" role="dialog"
                                                            aria-labelledby="myModalLabel33" style="display: none;" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                                                role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title" id="myModalLabel33">Kostenübersicht für <div
                                                                                class="badge badge-primary">{{ $data->branch }}</div>
                                                                        </h4>
                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                            aria-label="Close">
                                                                            <span aria-hidden="true">×</span>
                                                                        </button>
                                                                    </div>
                                                                    <form class="form-horizontal" novalidate method="post"   action="{{action('App\Http\Controllers\BranchRentController@store')}}" class="custom-file-upload" enctype="multipart/form-data">
                                                                        @csrf
                                                                        <input type="hidden" name="active_tab" id="active_tab" value="rent">

                                                                        <div class="modal-body">
                                                                            <label>Name der Mieteigenschaft </label>
                                                                            <div class="form-group">
                                                                                <input type="hidden" name="expense_details_id" value="{{  $data->id }}">
                                                                                <input type="text" placeholder="Name der Mieteigenschaft..."
                                                                                    class="form-control" name="object_name"
                                                                                    value="{{ old('object_name') }}">
                                                                            </div>

                                                                            <div class="form-group">
                                                                                <input type="text" placeholder="Name der Straße ..."
                                                                                    class="form-control" name="street" id="location-input"
                                                                                    value="{{ old('street') }}">
                                                                                @if ($errors->has('street'))<p style="color:red;">
                                                                                    {!!$errors->first('street')!!}</p>@endif
                                                                            </div>

                                                                            <div class="form-group">
                                                                                <input type="text" placeholder="Hous-nummer..."
                                                                                    class="form-control" name="house_no"
                                                                                    id="administrative_area_level_1"
                                                                                    value="{{ old('house_no') }}">
                                                                                @if ($errors->has('house_no'))<p style="color:red;">
                                                                                    {!!$errors->first('house_no')!!}</p>@endif
                                                                            </div>

                                                                            <div class="form-group">
                                                                                <input type="text" placeholder="Postleitzahl"
                                                                                    class="form-control" name="postcode"
                                                                                    id="postal_code-input" value="{{ old('postcode') }}">
                                                                                @if ($errors->has('postcode'))<p style="color:red;">
                                                                                    {!!$errors->first('postcode')!!}</p>@endif
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <input type="text" placeholder="Stadt..."
                                                                                    class="form-control" name="city" id="locality-input"
                                                                                    value="{{ old('city') }}">
                                                                                @if ($errors->has('city'))<p style="color:red;">
                                                                                    {!!$errors->first('city')!!}</p>@endif
                                                                            </div>

                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="submit"
                                                                                class="btn btn-primary waves-effect waves-light">Einreichen</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- New Rent Location Dialog: End  -->

                                                </div>
                                                <hr> 
                                                    <div class="row">
                                                        <div class="table-responsive">
                                                            <table class="table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>Objektname</th>
                                                                        <th>Adress</th>
                                                                        <th>Kosten</th>
                                                                        <th>Aktion</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($branch_rent as $br_rent)
                                                                        @if($br_rent->expense_details_id == request()->id)
                                                                        <tr>
                                                                            <th scope="row">{{$br_rent->id}}</th>
                                                                            <td>
                                                                                <a class="btn btn-flat-primary mr-1 mb-1 waves-effect waves-light rent-btn" data-id="{{ $br_rent->id }}">
                                                                                {{ $br_rent->object_name }}
                                                                                </a>
                                                                            </td>
                                                                            <td>{{ $br_rent->street }} {{ $br_rent->house_no }} {{ $br_rent->postcode }}, {{ $br_rent->city }}</td>
                                                                            <td>{{ number_format( $br_rent->total , 2, ',', '.') }}€</td>
                                                                            <td>
                                                                                <div class="btn-group dropup dropdown-icon-wrapper mr-0 mb-0">
                                                                                    <button type="button"
                                                                                        class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light"
                                                                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                                        <i class="feather icon-menu dropdown-icon"></i>
                                                                                    </button>
                                                                                    <div class="dropdown-menu">
                                                                                        <span class="dropdown-item">
                                                                                            <a data-toggle="modal" data-target="#UpdateModal{{ $br_rent->id }}"><i
                                                                                                    class="feather icon-edit"></i></a>
                                                                                        </span>
                                                                                        <span class="dropdown-item">
                                                                                            <!-- Button trigger modal -->
                                                                                            <a data-toggle="modal" data-target="#deleteModal{{ $br_rent->id }}"><i
                                                                                                    class="feather icon-trash"></i></a>
                                                                                        </span>
                                                                                    </div>
                                                                                </div>

                                                                                <!-- Modal -->
                                                                                <div class="modal fade" id="deleteModal{{ $br_rent->id }}" tabindex="-1"
                                                                                    role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                                                                        role="document">
                                                                                        <div class="modal-content">
                                                                                            <div class="modal-header">
                                                                                                <h4 class="modal-title" id="deleteModalLabel">Bestätigung löschen
                                                                                                </h4>
                                                                                                <button type="button" class="close" data-dismiss="modal"
                                                                                                    aria-label="Close">
                                                                                                    <span aria-hidden="true">&times;</span>
                                                                                                </button>
                                                                                            </div>
                                                                                            <div class="modal-body">
                                                                                                <p>Sind Sie sicher, dass Sie dieses Element löschen möchten?</p>
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button type="button" class="btn btn-secondary"
                                                                                                    data-dismiss="modal">Nein</button>
                                                                                                <a href="{{ url('branch_rent_destroy/'.$br_rent->id) }}"
                                                                                                    type="button" class="btn btn-danger">Ja</a>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <!-- UpdateModal -->
                                                                                <div class="modal fade" id="UpdateModal{{ $br_rent->id }}" tabindex="-1"
                                                                                    role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                                                                        role="document">
                                                                                        <div class="modal-content">
                                                                                            <div class="modal-header">
                                                                                                <h4 class="modal-title" id="deleteModalLabel">Bestätigung
                                                                                                    Aktualisieren</h4>
                                                                                                <button type="button" class="close" data-dismiss="modal"
                                                                                                    aria-label="Close">
                                                                                                    <span aria-hidden="true">&times;</span>
                                                                                                </button>
                                                                                            </div>

                                                                                            <form id="branchRentForm" class="form-horizontal" method="POST" action="{{ route('branch_rent.update') }}" class="custom-file-upload" enctype="multipart/form-data">
                                                                                                @csrf
                                                                                                <div class="modal-body">
                                                                                                    <label>Name der Mieteigenschaft</label>
                                                                                                    <div class="form-group">
                                                                                                        <input type="hidden" name="expense_details_id" value="{{ $data->id }}">
                                                                                                        <input type="hidden" name="id" value="{{ $br_rent->id }}">
                                                                                                        <input type="text" placeholder="Name der Mieteigenschaft..." class="form-control" name="object_name" value="{{ $br_rent->object_name }}">
                                                                                                    </div>

                                                                                                    <div class="form-group">
                                                                                                        <input type="text" placeholder="Name der Straße..." class="form-control" name="street" id="location-input" value="{{ $br_rent->street }}">
                                                                                                        @if ($errors->has('street'))<p style="color:red;">{!!$errors->first('street')!!}</p>@endif
                                                                                                    </div>

                                                                                                    <div class="form-group">
                                                                                                        <input type="text" placeholder="Hausnummer..." class="form-control" name="house_no" id="administrative_area_level_1" value="{{ $br_rent->house_no }}">
                                                                                                        @if ($errors->has('house_no'))<p style="color:red;">{!!$errors->first('house_no')!!}</p>@endif
                                                                                                    </div>

                                                                                                    <div class="form-group">
                                                                                                        <input type="text" placeholder="Postleitzahl" class="form-control" name="postcode" id="postal_code-input" value="{{ $br_rent->postcode }}">
                                                                                                        @if ($errors->has('postcode'))<p style="color:red;">{!!$errors->first('postcode')!!}</p>@endif
                                                                                                    </div>

                                                                                                    <div class="form-group">
                                                                                                        <input type="text" placeholder="Stadt..." class="form-control" name="city" id="locality-input" value="{{ $br_rent->city }}">
                                                                                                        @if ($errors->has('city'))<p style="color:red;">{!!$errors->first('city')!!}</p>@endif
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="modal-footer">
                                                                                                    <button type="submit" class="btn btn-primary waves-effect waves-light">Einreichen</button>
                                                                                                </div>
                                                                                            </form>

                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                         <div id="rentSlide{{ $br_rent->id }}" style="overflow-y:auto; max-height:80vh; width:60%;" class="slide">
                                                                            <div class="container">
                                                                                <form novalidate method="post" action="{{ action('App\Http\Controllers\RadiatorInstallationController@store')}}" class="custom-file-upload" enctype="multipart/form-data">
                                                                                    @csrf 
                                                                                    <div class="close"> 
                                                                                        <button type="button"   id="close_slider_save" data-id="{{ $br_rent->id }}" class="rentClose"> <i class="feather icon-x white"></i> Schließen</button>
                                                                                    </div>
                                                                                    <div class="row" style="background: #8fc73e;  color: white;     height: 56px;    align-content: center; justify-content: center;"> 
                                                                                        <h2 class="title white"> Information</h2>
                                                                                    </div>  

                                                                                    <div class="row mt-2">
                                                                                        <!-- Customer Information -->
                                                                                        <div class="col-md-12 mb-1">
                                                                                            <label for="customer" class="form-label h3 primary"><strong>MeitObjekte</strong></label>
                                                                                            <a href="{{ url('branch_rent/'.$br_rent->id.'/'.$br_rent->expense_details_id) }}" > 
                                                                                                  <div class="alert alert-warning mb-2" role="alert">
                                                                                                 <i class="feather icon-edit"></i> Klicken zum Bearbeiten    <strong class="danger">{{ $br_rent->object_name }}</strong>
                                                                                                </div>
                                                                                            </a>
                                                                                          
                                                                                             
                                                                                        </div>
                                                                                    </div>
                                                
                                                                                </form>
                                                                            </div> 
                                                                            
                                                                            <hr> 
                                                                        </div> 
                                                                        @endif
                                                                    @endforeach
                                                                    
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div> 

                                                   



                                           