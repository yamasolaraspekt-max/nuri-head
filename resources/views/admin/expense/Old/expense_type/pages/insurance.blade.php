<div class="col-xl-4 col-md-6 col-sm-12">
    <div class="card" style="height: 614.562px;">
        <div class="card-content">
            <div class="card-body">
                <h4 class="card-title"> Versicherungsaufwand von <div class="badge badge-primary">{{ $data->branch }}
                    </div>
                </h4>
                <p class="card-text">Alle Versicherungsdetails und Kosten </p>
            </div>
            <ul class="list-group list-group-flush">
                @foreach ($insurances as $insure)

                <li class="list-group-item">
                    <div class="btn-group dropup dropdown-icon-wrapper mr-0 mb-0">
                        <button type="button"
                            class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="feather icon-folder dropdown-icon"></i>
                        </button>
                        <div class="dropdown-menu">
                            <span class="dropdown-item">
                                <a data-toggle="modal" data-target="#insureUpdate{{ $insure->id }}"><i
                                        class="feather icon-edit"></i></a>
                            </span>
                            <span class="dropdown-item">
                                <!-- Button trigger modal -->
                                <a data-toggle="modal" data-target="#insuredelete{{ $insure->id }}"><i
                                        class="feather icon-trash"></i></a>
                            </span>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="insuredelete{{ $insure->id }}" tabindex="-1" role="dialog"
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
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Nein</button>
                                    <a href="{{ url('branch_rent_destroy/'.$insure->id) }}" type="button"
                                        class="btn btn-danger">Ja</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- UpdateModal -->
                    <div class="modal fade" id="insureUpdate{{ $insure->id }}" tabindex="-1" role="dialog"
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
                                            <input type="hidden" name="expense_details_id" value={{ $data->id }}>
                                            <input type="hidden" name="id" value={{ $insure->id }}>
                                            <input type="text" placeholder="Name der Mieteigenschaft..."
                                                class="form-control" name="object_name"
                                                value="">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" placeholder="Name der Straße ..." class="form-control"
                                                name="street" id="location-input" value="{{ $insure->street }}">
                                            @if ($errors->has('street'))<p style="color:red;">
                                                {!!$errors->first('street')!!}</p>@endif
                                        </div>

                                        <div class="form-group">
                                            <input type="text" placeholder="Hous-nummer..." class="form-control"
                                                name="house_no" id="administrative_area_level_1"
                                                value="{{ $insure->house_no }}">
                                            @if ($errors->has('house_no'))<p style="color:red;">
                                                {!!$errors->first('house_no')!!}</p>@endif
                                        </div>

                                        <div class="form-group">
                                            <input type="text" placeholder="Postleitzahl" class="form-control"
                                                name="postcode" id="postal_code-input" value="{{ $insure->postcode }}">
                                            @if ($errors->has('postcode'))<p style="color:red;">
                                                {!!$errors->first('postcode')!!}</p>@endif
                                        </div>
                                        <div class="form-group">
                                            <input type="text" placeholder="Stadt..." class="form-control" name="city"
                                                id="locality-input" value="{{ $insure->city }}">
                                            @if ($errors->has('city'))<p style="color:red;">{!!$errors->first('city')!!}
                                            </p>@endif
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
                    <span class="badge badge-pill bg-primary float-right">{{ number_format( $insure->monthly_payable, 2,
                        ',', '.') }}€</span>
                    {{ $insure->insurance_for }} - {{ $insure->provider }}
                </li>
                @endforeach
                <li class="list-group-item">
                    <span class="badge badge-pill bg-primary float-right">{{ number_format(
                        $insurances->sum('monthly_payable'), 2, ',', '.') }}€</span>
                    Zwischensumme
                </li>

            </ul>
            <div class="card-body">
                <!-- Button trigger modal -->
                <a type="button" class="btn btn-outline-success waves-effect waves-light" data-toggle="modal"
                    data-target="#insurance">
                    Ausgaben hinzufügen
                </a>

                <!-- Modal -->
                <div class="modal fade text-left" id="insurance" tabindex="-1" role="dialog"
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
                                action="{{action('App\Http\Controllers\BranchInsuranceController@store')}}"
                                class="custom-file-upload" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                    <label>Versicherung für </label>
                                    <div class="form-group">
                                        <input type="hidden" name="expense_types_id" value={{ request()->id }}>
                                        <select name="insurance_for" class="form-control">
                                            <option value="Gesundheit">Gesundheit</option>
                                            <option value="Haftung">Haftung</option>
                                            <option value="Eigentum">Eigentum</option>
                                            <option value="Reisen">Reisen</option>
                                            <option value="Berufshaftpflicht">Berufshaftpflicht</option>
                                            <option value="Cyber">Cyber</option>
                                            <option value="Betriebsunterbrechung">Betriebsunterbrechung</option>
                                        </select>
                                    </div>
                                    <label>Versicherungsnummer</label>
                                    <div class="form-group">
                                        <input type="text" placeholder="Versicherungsnummer..." class="form-control"
                                            name="policy_number" value="{{ old('policy_number') }}">
                                        @if ($errors->has('policy_number'))<p style="color:red;">
                                            {!!$errors->first('policy_number')!!}</p>@endif
                                    </div>
                                    <label>Versicherer</label>
                                    <div class="form-group">
                                        <input type="text" placeholder="Versicherer..." class="form-control"
                                            name="provider" value="{{ old('provider') }}">
                                        @if ($errors->has('provider'))<p style="color:red;">
                                            {!!$errors->first('provider')!!}</p>@endif
                                    </div>
                                    <label>Deckungsbetrag</label>
                                    <div class="form-group">
                                        <input type="text" placeholder="Deckungsbetrag" class="form-control"
                                            name="coverage_amount" value="{{ old('coverage_amount') }}">
                                        @if ($errors->has('coverage_amount'))<p style="color:red;">
                                            {!!$errors->first('coverage_amount')!!}</p>@endif
                                    </div>

                                    <label>Monatlich zahlbar </label>
                                    <div class="form-group">
                                        <input type="text" placeholder="Monatlich zahlbar " class="form-control"
                                            name="monthly_payable" value="{{ old('monthly_payable') }}">
                                        @if ($errors->has('monthly_payable'))<p style="color:red;">
                                            {!!$errors->first('monthly_payable')!!}</p>@endif
                                    </div>
                                    <label>Startdatum</label>
                                    <div class="form-group">
                                        <input type="date" placeholder="Startdatum..." class="form-control"
                                            name="start_date" id="locality-input" value="{{ old('start_date') }}">
                                        @if ($errors->has('start_date'))<p style="color:red;">
                                            {!!$errors->first('start_date')!!}</p>@endif
                                    </div>
                                    <label>Enddatum</label>
                                    <div class="form-group">
                                        <input type="date" placeholder="Enddatum..." class="form-control"
                                            name="end_date" id="locality-input" value="{{ old('end_date') }}">
                                        @if ($errors->has('end_date'))<p style="color:red;">
                                            {!!$errors->first('end_date')!!}</p>@endif
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