@extends('admin.layouts.app')
@section('title') Ratenzahlungen @stop
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
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Ratenzahlungen </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('asset_installment_show') }}">Details</a>
                                </li>

                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Table Hover Animation start -->
            <div class="row" id="table-hover-animation">
                <div class="col-12">
                    <div class="card">

                        <div class="card-content">
                            <div class="card-body">
                                <section id="nav-justified">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="card overflow-hidden">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <ul class="nav nav-tabs nav-justified" id="myTab2"
                                                            role="tablist">
                                                            <li class="nav-item">
                                                                <a class="nav-link active" id="home-tab-justified"
                                                                    data-toggle="tab" href="#home-just" role="tab"
                                                                    aria-controls="home-just" aria-selected="true"><i
                                                                        class="feather icon-paperclip"></i> AUTOS
                                                                    & MACHINE RATENZAHLUNG</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" id="profile-tab-justified"
                                                                    data-toggle="tab" href="#profile-just" role="tab"
                                                                    aria-controls="profile-just" aria-selected="true">
                                                                    <i class="feather icon-box"></i> VERMÖGENSWERTE</a>
                                                            </li>

                                                        </ul>

                                                        <!-- Tab panes -->
                                                        <div class="tab-content pt-1">
                                                            <div class="tab-pane active" id="home-just" role="tabpanel"
                                                                aria-labelledby="home-tab-justified">
                                                                @include('admin.product.assets.installment.installment_car')
                                                            </div>
                                                            <div class="tab-pane" id="profile-just" role="tabpanel"
                                                                aria-labelledby="profile-tab-justified">
                                                                @include('admin.product.assets.installment.installment_asset')
                                                            </div>


                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Table head options end -->

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