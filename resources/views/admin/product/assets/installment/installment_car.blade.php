<div class="content-body">
    <!-- Table Hover Animation start -->
    <div class="row" id="table-hover-animation">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Auto & Machine Ratenzahlung</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <form action="{{route('assets.installment.show')}}">
                                    <fieldset>
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control"
                                                placeholder="Search Form" aria-describedby="button-addon2">
                                            <div class="input-group-append" id="button-addon2">
                                                <button class="btn btn-primary" type="submit">Go</button>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>

                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th Scope="col">ID</th>
                                        <th scope="col">Artikel</th>
                                        <th scope="col">Gekauft bei</th>
                                        <th scope="col">Preis pro Monat</th>
                                        <th scope="col">Strafe</th>
                                        <th scope="col">Ratenzahlungsdauer</th>
                                        <th scope="col">Fälligkeitsdatum</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Bezahlt von</th>
                                        <th scope="col">Versicherer</th>
                                        <th scope="col">Vertrag</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Aktion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($car as $item)
                                    <tr>
                                        <th scope="row">{{ $item->id }}</th>
                                        <td>{{ $item->name }} - <code>{{ $item->installment_id }}</code>
                                            <br>
                                            <div class="badge badge-pill badge-primary">@if($item->type=="asset") Asset
                                                @else Machine @endif</div>
                                        </td>
                                        <td>{{ $item->purchased_from }}</td>
                                        <td>{{ $item->price_per_month }}</td>
                                        <td>{{ $item->fines }}</td>
                                        <td>{{ $item->installment_duration }}</td>
                                        <td>{{ $item->due_date }}</td>
                                        <td>{{ $item->total }}</td>
                                        <td>{{ $item->paid_by }}</td>
                                        <td>{{ $item->insurance_provider }}
                                            <br>
                                            <div class="badge badge-pill badge-primary">{{
                                                $item->insurance_payment_month }}</div>
                                            <br>
                                            <div class="badge badge-pill badge-danger">{{ $item->insurance_expiry_date}}
                                            </div>
                                        </td>
                                        <td>
                                            <!-- Image Modal -->
                                            <a type="button"
                                                class="btn btn-icon btn-icon rounded-circle btn-outline-primary mr-1 mb-1 waves-effect waves-light"
                                                data-toggle="modal" data-target="#pdffile{{ $item->id }}">
                                                <i class="feather icon-book"></i></a>

                                            {{-- Contract PDF Modal --}}

                                            <div class="modal fade text-left" id="pdffile{{ $item->id }}" tabindex="-1"
                                                role="dialog" aria-labelledby="myModalLabel160" style="display: none;"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                                    role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary white">
                                                            <h5 class="modal-title" id="myModalLabel160">
                                                                Eigentumsvertrag</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                        </div>
                                                        <form action="{{ route('assets.installment.pdf') }}"
                                                            method="post" enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <label for="pdf_file">Neues PDF hochladen: </label>
                                                                <div class="form-group">
                                                                    <input type="hidden" value="{{ $item->id }}"
                                                                        name="id">
                                                                    <input type="file" id="pdf_file" name="pdf_file"
                                                                        class="form-control">
                                                                    @if ($errors->has('pdf_file'))<p style="color:red;">
                                                                        {!!$errors->first('pdf_file')!!}</p>@endif

                                                                </div>
                                                                <div class="form-group">
                                                                    <label> Eigentumsvertrag </label>
                                                                    @if($item->contract_document)
                                                                    <iframe
                                                                        src="{{ asset('images/installment/contract/'.$item->contract_document) }}"
                                                                        width="100%" height="600px"></iframe>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit"
                                                                    class="btn btn-primary waves-effect waves-light">Hochladen</button>
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Stornieren</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Contract PDF Modal --}}

                                        </td>
                                        <td>@if($item->status==Null) Nicht veröffentlicht @else Veröffentlicht @endif
                                        </td>

                                        <td>

                                            <!-- Begin: Edit -->

                                            <div class="btn-group dropdown-menu-right dropdown-icon-wrapper mr-1 mb-1">
                                                <button type="button"
                                                    class="btn  dropdown-toggle dropdown-toggle-split waves-effect waves-light"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="feather icon-align-justify dropdown-icon"></i>
                                                </button>
                                                <div class="dropdown-menu" x-placement="top-start"
                                                    style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(79px, -233px, 0px);">


                                                    @if(DB::table('user_rolls')
                                                    ->where('user_rolls.user_id', '=', auth()->user()->id)
                                                    ->where('user_rolls.item_id', '=', 'Product')
                                                    ->where('user_rolls.is_update', '=', 'on')
                                                    ->first())

                                                    <!-- Begin: Edit -->
                                                    <a
                                                        href="{{ url('/asset_installment_edit/'.$item->id.'/'.$item->asset_id.'/'.$item->type) }}">
                                                        <span class="dropdown-item">
                                                            <i class="feather icon-edit"></i> Bearbeiten
                                                        </span>
                                                    </a>


                                                    <a href="{{ url('installment_payment/'.$item->id) }}">
                                                        <span class="dropdown-item">
                                                            <i class="feather icon-circle"></i> Zahlen Sie die Rate
                                                        </span>
                                                    </a>

                                                    <a data-toggle="modal" data-target="#delete_pro{{$item->id}}">
                                                        <span class="dropdown-item">
                                                            <i class="feather  icon-trash"></i> Löschen
                                                        </span>
                                                    </a>

                                                    @endif
                                                </div>
                                            </div>



                                            <!-- Modal -->
                                            <div class="modal fade text-left" id="delete_pro{{$item->id}}" tabindex="-1"
                                                role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <h5>Datensatz löschen</h5>
                                                            <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                            <p>Die Recard-Nummer lautet: {{$item->id}} </p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a type="button"
                                                                href="{{url('/asset_installment_destroy').'/'.$item->id}}"
                                                                class="btn btn-primary">Yes</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                        </div>
                        <!-- End Delete Modal -->



                        <!-- Operation Section -->
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
<!-- Table head options end -->
{{$car->links()}}
</div>
@section('script')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>

<script>
    function onScanSuccess(decodedText, decodedResult) {
  // handle the scanned code as you like, for example:
  console.log(`Code matched = ${decodedText}`, decodedResult);
  var qrcode = document.getElementById('serial_no');
  qrcode.value = decodedResult.decodedText;
}

function onScanFailure(error) {
  // handle scan failure, usually better to ignore and keep scanning.
  // for example:
  //console.warn(`Code scan error = ${error}`);
}

let html5QrcodeScanner = new Html5QrcodeScanner(
  "reader",
  { fps: 10, qrbox: {width: 250, height: 250} },
  /* verbose= */ false);
html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>

<script>
    $(document).ready(function(){
    $("#hide_camera").click(function(){
        $("#camera").toggle(); // Use jQuery for both selection and toggling
    });
});

</script>




<script>
    $(document).ready(function() {
        $('#branch').select2();
        $('#parent').select2();
    });

    
</script>


@endsection