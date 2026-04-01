@extends('admin.layouts.app')

@section('title') LIEFERSCHEIN @endsection
@section('style')
<!-- Include stylesheet -->

<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">


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
                            <h2 class="content-header-title float-left mb-0">HOME</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('delivery.note') }}">LIEFERSCHEIN</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Neu</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            
            </div>
            <div class="content-body">
                <!-- Basic Horizontal form layout section start -->
                <section id="basic-horizontal-layouts">
                    <div class="row match-height">
                        <div class="col-md-12 col-12">
                
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\DeliveryNoteController@store')}}" class="custom-file-upload" enctype="multipart/form-data">
                                            @csrf
                                            <fieldset> 
                                                <div class="row">
                                                    <div class="col-md-12" id="camera" >
                                                        <div id="reader" width="600px"></div>
        
                                                    </div>
        
                                                    <div class="col-md-12">
                                                        <fieldset>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" type="text" class="form-control"  name="delivery_note"  id="delivery_note" placeholder="Lieferschein..." aria-describedby="button-addon2">
                                                                <div class="input-group-append" id="button-addon2">
                                                                    <button class="btn btn-primary waves-effect waves-light" type="button" id="hide_camera"><i class="fa fa-camera"></i></button>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                    </div>
        
        
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                             Geliefert von
                                                            </label>
                                                            
                                                             <input type="text" class="form-control"  name="from"  required>
                                                             @if ($errors->has('from'))<p style="color:red;">{!!$errors->first('from')!!}</p>@endif
                                                        </div>
                                                    </div>
        
        
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                Zweig
                                                            </label>
                                                            
                                                            <fieldset class="form-group">
                                                                <select class="select2-customize-result form-control required" name="to"  id="branch"  style="width:100%">
                                                                    @foreach ($branch as $br)
                                                                    <option value="{{ $br->id }}">{{ $br->branch }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </fieldset>
                                                             @if ($errors->has('to'))<p style="color:red;">{!!$errors->first('to')!!}</p>@endif
                                                        </div>
                                                    </div>
        
        
                                                    <div class="col-md-6">
                                                     <div class="form-group">
                                                         <label for="Title">
                                                             Übergabe durch
                                                         </label>
                                                         
                                                         <fieldset class="form-group">
                                                             <select class="select2-customize-result form-control required" name="handover_by"  id="handover_by"  style="width:100%">
                                                                 @foreach ($employee as $emp)
                                                                 <option value="{{ $emp->id }}">{{ $emp->name }} {{ $emp->lastname }}</option>
                                                                 @endforeach
                                                             </select>
                                                         </fieldset>
                                                          @if ($errors->has('handover_by'))<p style="color:red;">{!!$errors->first('handover_by')!!}</p>@endif
                                                     </div>
                                                 </div>
        
                                                   
                                                  
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                Datum
                                                            </label>
                                                            
                                                             <input type="date" class="form-control"  name="handover_date"  required>
                                                             @if ($errors->has('handover_date'))<p style="color:red;">{!!$errors->first('handover_date')!!}</p>@endif
                                                        </div>
                                                    </div>
        
                                                 <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                            Beschreibung
                                                        </label>
                                                        
                                                         <textarea  class="form-control"  name="description"  required></textarea>
                                                         @if ($errors->has('description'))<p style="color:red;">{!!$errors->first('description')!!}</p>@endif
                                                    </div>
                                                </div>
                                             </fieldset>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Nächste</button>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        </div>
                            <div class="col-md-6 col-12">
                            
                               
                            </div>
                        </div>
                    </div>
                </section>
                <!-- // Basic Horizontal form layout section end -->

               

            </div>
        </div>
    </div>
    <!-- END: Content-->

@endsection

@section('script')
 
<script src="{{ asset('js/select2.min.js') }}"></script>


<script>
        $(document).ready(function() {
            $('#product').select2();
        });

    </script>


<script>
   
   $(document).ready(function(){
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

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
   
    
<script>
    function onScanSuccess(decodedText, decodedResult) {
  // handle the scanned code as you like, for example:
  console.log(`Code matched = ${decodedText}`, decodedResult);
  var qrcode = document.getElementById('delivery_note');
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


@endsection


