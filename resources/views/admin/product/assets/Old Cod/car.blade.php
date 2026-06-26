@extends('admin.layouts.app')
@section('title') Vermögensbestand @stop
@section('style')

<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css')}}">
@endsection
@section('content')

<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div> 
        <div class="content-body">
            <!-- Table Hover Animation start -->
            <div class="row" id="table-hover-animation">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Vermögensbestand</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <form action="{{action('App\Http\Controllers\MachineController@index')}}">
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
                                    <div class="col-md-3 ">
                                        <a type="button" class="btn btn-outline-primary block btn-lg"
                                            href="{{ url('machine_create') }}">
                                            Neue hinzufügen
                                        </a>
                                    </div>
                                </div>

                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th Scope="col">#</th>
                                            <th Scope="col">Owner Name</th>
                                            <th Scope="col">Name / Model</th>
                                            <th Scope="col">Jahr</th>
                                            <th scope="col">Kauf art</th>
                                            <th scope="col">TÜV</th>
                                            <th scope="col">Bild</th>
                                            <th scope="col">Branch</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Aktion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data as $item)
                                        <tr>
                                            <th scope="row">
                                                {{ $item->id }}
                                            </th>
                                            <td>
                                                {{ $item->owner_name }}<br>
                                                <div class="badge badge-success mr-1 mb-1">
                                                    <span> {{ $item->owner_contact }}</span>
                                                </div>
                                            </td>

                                            <td>
                                                {{ $item->name }} {{ $item->model }} ({{ $item->color }})<br>
                                                <div class="badge badge-success mr-1 mb-1">
                                                    <span> {{ $item->engine_type }} | {{ $item->mileage }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $item->year }}</td>
                                            <td>
                                                @if($item->purchase_type=="Ratenzahlung")
                                                <a href="{{ url('asset_installment_show?search='.$item->name) }}">{{
                                                    $item->purchase_type }}</a>
                                                @elseif($item->purchase_type=="Leasing")
                                                <!-- Leasing Model -->
                                                <a type="button" class="btn btn-icon btn-icon  mr-1 mb-1"
                                                    data-toggle="modal" data-target="#leasing{{$item->id}}">
                                                    {{ $item->purchase_type }}
                                                </a>

                                                <!-- Modal -->
                                                <div class="modal fade text-left" id="leasing{{$item->id}}"
                                                    tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                {{ $item->name }} | {{ $item->model }}
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body" style="text-align: center;">
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Item</th>
                                                                            <th>Leasing Von</th>
                                                                            <th>Startdatum</th>
                                                                            <th>Enddatum</th>
                                                                            <th>Prize</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>{{ $item->name }}</td>
                                                                            <td>{{ $item->leasing_from }}</td>
                                                                            <td>{{ $item->leasing_start_date }}</td>
                                                                            <td>{{ $item->leasing_end_date }}</td>
                                                                            <td>{{ $item->leasing_price }}</td>
                                                                        </tr>

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer">

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                            </div>
                            @else
                            <!-- End Image Modal -->
                            {{ $item->purchase_type }}
                            @endif
                            </td>
                            <td>{{ $item->technical_inspection }}</td>
                            <td>
                                <!-- Image Modal -->
                                <a type="button" class="btn btn-icon btn-icon  mr-1 mb-1" data-toggle="modal"
                                    data-target="#image{{$item->id}}">
                                    <div class="avatar mr-1 ">
                                        <img src="{{ asset('images/asset/car/'.$item->image) }}" alt="avtar img holder"
                                            height="32" width="32">
                                    </div>
                                </a>

                                <!-- Modal -->
                                <div class="modal fade text-left" id="image{{$item->id}}" tabindex="-1" role="dialog"
                                    aria-labelledby="myModalLabel1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                {{ $item->name }} | {{ $item->model }} | {{ $item->year }} | {{
                                                $item->color }}
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body" style="text-align: center;">
                                                <img src="{{ asset('images/asset/car/'.$item->image) }}"
                                                    alt="avtar img holder" height="200" width="200">
                                                <p>{{ $item->description }}</p>
                                            </div>
                                            <div class="modal-footer">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <!-- End Image Modal -->

                        </td>
                        <td>{{ $item->branch }}</td>

                        <td>{{ $item->status }}</td>
                        <td>

                            <!-- Begin: Edit -->

                            <div class="btn-group dropup dropdown-icon-wrapper mr-1 mb-1">
                                <button type="button"
                                    class="btn btn-outline-dark dropdown-toggle dropdown-toggle-split waves-effect waves-light"
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
                                    <a href="{{url('/machine_edit').'/'.$item->id}}">
                                        <span class="dropdown-item">
                                            <i class="feather icon-circle"></i> Bearbeiten
                                        </span>
                                    </a>


                                    <a href="{{ url('asset_installment/'.$item->id.'/'.'machine'.'/'.$item->branch_id) }}">
                                        <span class="dropdown-item">
                                            <i class="feather icon-circle"></i> Ratenzahlung Details
                                        </span>
                                    </a>

                                    <a href="{{ url('machine_service_details/'.$item->id) }}">
                                        <span class="dropdown-item">
                                            <i class="feather  icon-file"></i> Technical Service
                                        </span>
                                    </a>

                                    <a data-toggle="modal" data-target="#delete-pro{{$item->id}}">
                                        <span class="dropdown-item">
                                            <i class="feather  icon-trash"></i> Löschen
                                        </span>
                                    </a>

                                    @endif
                                </div>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade text-left" id="delete-pro{{$item->id}}" tabindex="-1" role="dialog"
                                aria-labelledby="myModalLabel1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-scrollable" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <h5>Datensatz löschen</h5>
                                            <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                            <p>Die Recard-Nummer lautet: {{$item->id}} </p>
                                        </div>
                                        <div class="modal-footer">
                                            <a type="button" href="{{url('/machine_destroy').'/'.$item->id}}"
                                                class="btn btn-primary">Ja</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <!-- End Delete Modal -->

                    </td>
                    <!-- Operation Section -->

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
{{$data->links()}}
</div>
</div>
</div>
<!-- END: Content-->
@stop

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