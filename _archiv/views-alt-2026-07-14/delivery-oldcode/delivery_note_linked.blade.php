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
                                    <li class="breadcrumb-item"><a href="#">Verlinkt</a>
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
                        <div class="col-md-6 col-12">
                
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
                                        <div class="col-md-12 col-12">
                                            <div class="card" style="height: 583.562px;">
                                                <div class="card-header">
                                                    <h4 class="card-title">LIEFERSCHEIN: {{ $delivery_note->delivery_note }}</h4>
                                                </div>
                                                <div class="card-content">
                                                    <div class="card-body">
                                                                    <div class="timeline-info">
                                                                        <div class="table-responsive">
                                                                            <table class="table mb-0">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th scope="col">Geliefert von</th>
                                                                                        <th scope="col">Zweig</th>
                                                                                        <th scope="col">Übergabe durch</th>
                                                                                        <th scope="col">Datum</th>
                                                                                        <th scope="col">Bild & Dokument</th>
                                                                                        <th scope="col">Status</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    
                                                                                    <tr>
                                                                                        <td>{{ $delivery_note->from }}</td>
                                                                                        <td>{{ $delivery_note->branch }}</td>
                                                                                        <td>{{ $delivery_note->name }} {{ $delivery_note->lastname }}</td>
                                                                                        <td>{{ $delivery_note->handover_date }}</td>
                                                                                        <td>
                                                                                            <a href="{{ url('delivery_note_image/'.$delivery_note->delivery_note) }}">
                                                                                                <div class="badge badge-success mr-1 mb-1">
                                                                                                    <i class="fa fa-image"></i>
                                                                                                    <span>Bild</span>
                                                                                                </div>
                                                                                            </a>
                                                                                          
                                                                                            |
                                                                                            <a href="{{ url('delivery_note_pdf_read/'.$delivery_note->id) }}">
                                                                                                <div class="badge badge-success mr-1 mb-1">
                                                                                                    <i class="fa fa-file-pdf-o"></i>
                                                                                                    <span>Dokument</span>
                                                                                                </div>
                                                                                            </a>
                                                                                        </td>

                                                                                        <td>  <a  data-toggle="modal" data-target="#progress{{$delivery_note->id}}">
                                                                                            {{ $delivery_note->status }}
                                                                                        </a>

                                                                                            <div class="progress progress-bar-success progress-lg">
                                                                                                <div class="progress-bar" role="progressbar" aria-valuenow="{{ $delivery_note->progress }}" aria-valuemin="0" aria-valuemax="100" style="width:{{ $delivery_note->progress }}%">{{ $delivery_note->progress }}%</div>
                                                                                            </div>

                                                                                              <!-- Progress Report-->
                                                                                    
                                                                                                <!-- Modal -->
                                                                                                <div class="modal fade text-left" id="progress{{$delivery_note->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                                                        <div class="modal-content">
                                                                                                            <div class="modal-header">
                                                                                                                Paketfortschritt:: {{ $delivery_note->delivery_note }}
                                                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                    <span aria-hidden="true">&times;</span>
                                                                                                                </button>
                                                                                                            </div>
                                                                                                            <form method="post" action="{{ action('App\Http\Controllers\DeliveryNoteController@progress') }}">
                                                                                                                @csrf
                                                                                                                <div class="modal-body">
                                                                                                                    <input type="hidden" value="{{ $delivery_note->id }}" name="id">
                                                                                                                    <select class="form-control" name="progress">
                                                                                                                        <option selected value="{{ $delivery_note->progress }}">{{ $delivery_note->progress }}%</option>
                                                                                                                        <option value="10">10%</option>
                                                                                                                        <option value="20">20%</option>
                                                                                                                        <option value="30">30%</option>
                                                                                                                        <option value="40">40%</option>
                                                                                                                        <option value="50">50%</option>
                                                                                                                        <option value="60">60%</option>
                                                                                                                        <option value="70">70%</option>
                                                                                                                        <option value="80">80%</option>
                                                                                                                        <option value="90">90%</option>
                                                                                                                        <option value="100">100%</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                                <div class="modal-footer">
                                                                                                                <button type="submit" class="btn btn-primary">Aktualisieren</button>
                                                                                                                </div>
                                                                                                            </form>
                                                                                                        
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>

                                                                                        </td>
                                                                                    </tr>
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
                        </div>
                            <div class="col-md-6 col-12">
                                <div class="card">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="col-md-12 col-12">
                                                <div class="card" style="height: 583.562px;">
                                                    <div class="card-header">
                                                        <h4 class="card-title">VERLINKTER LIEFERSCHEIN:</h4>
                                                    </div>
                                                    <div class="card-content">
                                                        <div class="card-body">
                                                            <ul class="activity-timeline timeline-left list-unstyled">
                                                                    <li>
                                                                        <div class="timeline-icon bg-primary">
                                                                            <i class="feather icon-link font-medium-2 align-middle"></i>
                                                                        </div>
                                                                        <div class="timeline-info">
                                                                            <div class="table-responsive">
                                                                                <table class="table mb-0">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th scope="col">Geliefert von</th>
                                                                                            <th scope="col">Zweig</th>
                                                                                            <th scope="col">Übergabe durch</th>
                                                                                            <th scope="col">Datum</th>
                                                                                            <th scope="col">Bild & Dokument</th>
                                                                                            <th scope="col">Status</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        @foreach ($linked as $link)
                                                                                        <tr>
                                                                                            <td>{{ $link->from }}</td>
                                                                                            <td>{{ $link->branch }}</td>
                                                                                            <td>{{ $link->name }} {{ $link->lastname }}</td>
                                                                                            <td>{{ $link->handover_date }}</td>
                                                                                            <td>
                                                                                                <a href="{{ url('delivery_note_image/'.$link->delivery_note) }}">
                                                                                                    <div class="badge badge-success mr-1 mb-1">
                                                                                                        <i class="fa fa-image"></i>
                                                                                                        <span>Bild</span>
                                                                                                    </div>
                                                                                                </a>
                                                                                              
                                                                                                |
                                                                                                <a href="{{ url('delivery_note_pdf_read/'.$link->id) }}">
                                                                                                    <div class="badge badge-success mr-1 mb-1">
                                                                                                        <i class="fa fa-file-pdf-o"></i>
                                                                                                        <span>Dokument</span>
                                                                                                    </div>
                                                                                                </a>
                                                                                            </td>
    
                                                                                            <td>  <a  data-toggle="modal" data-target="#progress{{$link->id}}">
                                                                                                {{ $link->status }}
                                                                                            </a>
    
                                                                                                <div class="progress progress-bar-success progress-lg">
                                                                                                    <div class="progress-bar" role="progressbar" aria-valuenow="{{ $link->progress }}" aria-valuemin="0" aria-valuemax="100" style="width:{{ $link->progress }}%">{{ $link->progress }}%</div>
                                                                                                </div>
    
                                                                                                  <!-- Progress Report-->
                                                                                        
                                                                                                    <!-- Modal -->
                                                                                                    <div class="modal fade text-left" id="progress{{$link->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                                                            <div class="modal-content">
                                                                                                                <div class="modal-header">
                                                                                                                    Paketfortschritt:: {{ $link->delivery_note }}
                                                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                        <span aria-hidden="true">&times;</span>
                                                                                                                    </button>
                                                                                                                </div>
                                                                                                                <form method="post" action="{{ action('App\Http\Controllers\DeliveryNoteController@progress') }}">
                                                                                                                    @csrf
                                                                                                                    <div class="modal-body">
                                                                                                                        <input type="hidden" value="{{ $link->id }}" name="id">
                                                                                                                        <select class="form-control" name="progress">
                                                                                                                            <option selected value="{{ $link->progress }}">{{ $link->progress }}%</option>
                                                                                                                            <option value="10">10%</option>
                                                                                                                            <option value="20">20%</option>
                                                                                                                            <option value="30">30%</option>
                                                                                                                            <option value="40">40%</option>
                                                                                                                            <option value="50">50%</option>
                                                                                                                            <option value="60">60%</option>
                                                                                                                            <option value="70">70%</option>
                                                                                                                            <option value="80">80%</option>
                                                                                                                            <option value="90">90%</option>
                                                                                                                            <option value="100">100%</option>
                                                                                                                        </select>
                                                                                                                    </div>
                                                                                                                    <div class="modal-footer">
                                                                                                                    <button type="submit" class="btn btn-primary">Aktualisieren</button>
                                                                                                                    </div>
                                                                                                                </form>
                                                                                                            
                                                                                                            </div>
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
                                                                    </li>
                                                               
                                                     
                                                               
                                                               
                                                            </ul>
                                                        </div>
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


