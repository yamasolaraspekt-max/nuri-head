<section id="content-types">
    <div class="row match-height">
        <!-- Basic Expense Start -->
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card" style="height: 614.562px;">
                <div class="card-content">
                    <div class="card-body">
                        <h4 class="card-title"> Kostenübersicht für <div class="badge badge-primary">{{ $data->branch }}
                            </div>
                        </h4>
                        <p class="card-text">In diesem Abschnitt werden die grundlegenden und sonstigen Ausgaben der
                            Zweigstelle untersucht</p>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($branch_rent as $br_rent)
                        @if($br_rent->expense_details_id == request()->id)
                        <li class="list-group-item">

                            <div class="btn-group dropup dropdown-icon-wrapper mr-0 mb-0">
                                <button type="button"
                                    class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="feather icon-folder dropdown-icon"></i>
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
                            <div class="modal fade" id="deleteModal{{ $br_rent->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="deleteModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="deleteModalLabel">Bestätigung löschen</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Sind Sie sicher, dass Sie dieses Element löschen möchten?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Nein</button>
                                            <a href="{{ url('branch_rent_destroy/'.$br_rent->id) }}" type="button"
                                                class="btn btn-danger">Ja</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- UpdateModal -->
                            <div class="modal fade" id="UpdateModal{{ $br_rent->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="deleteModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="deleteModalLabel">Bestätigung Aktualisieren</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <form class="form-horizontal" novalidate method="post"
                                            action="{{action('App\Http\Controllers\BranchRentController@update')}}"
                                            class="custom-file-upload" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-body">
                                                <label>Name der Mieteigenschaft </label>
                                                <div class="form-group">
                                                    <input type="hidden" name="expense_details_id" value="{{ $data->id }}">
                                                    <input type="hidden" name="id" value="{{ $br_rent->id }}">
                                                    <input type="text" placeholder="Name der Mieteigenschaft..."
                                                        class="form-control" name="object_name"
                                                        value="{{ $br_rent->object_name }}">
                                                </div>

                                                <div class="form-group">
                                                    <input type="text" placeholder="Name der Straße ..."
                                                        class="form-control" name="street" id="location-input"
                                                        value="{{ $br_rent->street }}">
                                                    @if ($errors->has('street'))<p style="color:red;">
                                                        {!!$errors->first('street')!!}</p>@endif
                                                </div>

                                                <div class="form-group">
                                                    <input type="text" placeholder="Hous-nummer..." class="form-control"
                                                        name="house_no" id="administrative_area_level_1"
                                                        value="{{ $br_rent->house_no }}">
                                                    @if ($errors->has('house_no'))<p style="color:red;">
                                                        {!!$errors->first('house_no')!!}</p>@endif
                                                </div>

                                                <div class="form-group">
                                                    <input type="text" placeholder="Postleitzahl" class="form-control"
                                                        name="postcode" id="postal_code-input"
                                                        value="{{ $br_rent->postcode }}">
                                                    @if ($errors->has('postcode'))<p style="color:red;">
                                                        {!!$errors->first('postcode')!!}</p>@endif
                                                </div>
                                                <div class="form-group">
                                                    <input type="text" placeholder="Stadt..." class="form-control"
                                                        name="city" id="locality-input" value="{{ $br_rent->city }}">
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


                            <!-- Action Buttons -->
                            <a href="{{ url('branch_rent/'.request()->id.'/'.$br_rent->id) }}"> {{ $br_rent->object_name
                                }}</a>
                            <small><code>{{ $br_rent->street }} {{ $br_rent->house_no }} {{ $br_rent->postcode }}, {{ $br_rent->city }}</code></small>
                            <span class="badge badge-pill bg-primary float-right">{{ number_format( $br_rent->total , 2,',', '.') }}€</span>

                        </li>
                        @endif
                        @endforeach


                    </ul>
                    <div class="card-body">
                        <div class="form-group">
                            <!-- Button trigger modal -->
                            <a type="button" class="btn btn-outline-success waves-effect waves-light"
                                data-toggle="modal" data-target="#rent">
                                Ausgaben hinzufügen
                            </a>

                            <!-- Modal -->
                            <div class="modal fade text-left" id="rent" tabindex="-1" role="dialog"
                                aria-labelledby="myModalLabel33" style="display: none;" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myModalLabel33">Kostenübersicht für <div
                                                    class="badge badge-primary">{{ $data->branch }}</div>
                                            </h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                        <form class="form-horizontal" novalidate method="post"
                                            action="{{action('App\Http\Controllers\BranchRentController@store')}}"
                                            class="custom-file-upload" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-body">
                                                <label>Name der Mieteigenschaft </label>
                                                <div class="form-group">
                                                    <input type="hidden" name="expense_details_id" value={{ $data->id
                                                    }}>
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
                                                    <input type="text" placeholder="Hous-nummer..." class="form-control"
                                                        name="house_no" id="administrative_area_level_1"
                                                        value="{{ old('house_no') }}">
                                                    @if ($errors->has('house_no'))<p style="color:red;">
                                                        {!!$errors->first('house_no')!!}</p>@endif
                                                </div>

                                                <div class="form-group">
                                                    <input type="text" placeholder="Postleitzahl" class="form-control"
                                                        name="postcode" id="postal_code-input"
                                                        value="{{ old('postcode') }}">
                                                    @if ($errors->has('postcode'))<p style="color:red;">
                                                        {!!$errors->first('postcode')!!}</p>@endif
                                                </div>
                                                <div class="form-group">
                                                    <input type="text" placeholder="Stadt..." class="form-control"
                                                        name="city" id="locality-input" value="{{ old('city') }}">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>