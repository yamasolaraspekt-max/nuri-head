@extends('admin.layouts.app')
@section('title') Lieferschein @stop
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
                                <h4 class="card-title">Lieferschein</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-4">
                                            <form action="{{action('App\Http\Controllers\DeliveryNoteController@index')}}">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <input type="text" name="search" class="form-control" placeholder="Search Form" aria-describedby="button-addon2">
                                                        <div class="input-group-append" id="button-addon2">
                                                            <button class="btn btn-primary" type="submit">Go</button>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </form>
                                        </div>
                                       <div class="col-3 float-right">
                                        <a type="button" class="btn btn-outline-primary block btn-lg" href="{{ url('delivery_note_create') }}">
                                        Neue hinzufügen
                                       </a>
                               <!-- Modal -->
                                       <div class="modal fade text-left" id="default" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                           <div class="modal-dialog modal-dialog-scrollable" role="document">
                                               <div class="modal-content">
                                                   <div class="modal-header">
                                                       <h4 class="modal-title" id="myModalLabel1">NEU</h4>
                                                       <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                           <span aria-hidden="true">&times;</span>
                                                       </button>
                                                   </div>
                                                   <div class="modal-body">
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

                                                               <div class="col-md-6">
                                                                   <div class="form-group">
                                                                       <label for="Title">
                                                                           Beschreibung
                                                                       </label>
                                                                       
                                                                        <textarea  class="form-control"  name="description"  required></textarea>
                                                                        @if ($errors->has('description'))<p style="color:red;">{!!$errors->first('description')!!}</p>@endif
                                                                   </div>
                                                               </div>
                                                               
                                                               <div class="col-md-6">
                                                                   <div class="form-group">
                                                                       <label for="Title">
                                                                          Foto 
                                                                       </label>
                                                                      
                                                                        <input type="file" class="form-control"  name="image"  required>
                                                                        @if ($errors->has('image'))<p style="color:red;">{!!$errors->first('image')!!}</p>@endif
                                                                   </div>
                                                               </div>
                                                           </div>
                                                        </fieldset>
                                                   </div>
                                                   <div class="modal-footer">
                                                       <button type="submit" class="btn btn-primary">Submit</button>
                                                   </div>
                                                   </form>
                                               </div>
                                           </div>
                                       </div>
                                    </div>
                                </div>

                                @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th Scope="col">ID</th>
                                                    <th scope="col">Lieferschein#</th>
                                                    <th scope="col">Geliefert von</th>
                                                    <th scope="col">Zweig</th>
                                                    <th scope="col">Übergabe durch</th>
                                                    <th scope="col">Datum</th>
                                                    <th scope="col">Bild & Dokument</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">Aktion</th>
                                                    <th scope="col">Vorgang</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                     
                                                @foreach($content as $item)
                                                @if($item->level==1)
                                      
                                                <tr>
                                                    <th scope="row">
                                                        @if($item->linked=="linked")
                                                        <i class="feather icon-link sucess"></i>
                                                        @elseif($item->linked=="Linked to")
                                                        <i class="feather icon-link-2 warning"></i>
                                                        @endif
                                                         {{ $item->id }}
                                                    </th>
                                                    <td><a href="{{ url('delivery_note_linked/'.$item->id) }}">{{ $item->delivery_note }}</a></td>
                                                    <td>{{ $item->from }}</td>
                                                    <td>{{ $item->branch }}</td>
                                                    <td>{{ $item->name }} {{ $item->lastname }}</td>
                                                    <td>{{ $item->handover_date }}</td>
                                                    <td>
                                                        <a href="{{ url('delivery_note_image/'.$item->delivery_note) }}">
                                                            <div class="badge badge-success mr-1 mb-1">
                                                                <i class="fa fa-image"></i>
                                                                <span>Bild</span>
                                                            </div>
                                                        </a>
                                                      
                                                        |
                                                        <a href="{{ url('delivery_note_pdf_read/'.$item->id) }}">
                                                            <div class="badge badge-success mr-1 mb-1">
                                                                <i class="fa fa-file-pdf-o"></i>
                                                                <span>Dokument</span>
                                                            </div>
                                                        </a>
                                                    </td>
                                                    <td>{{ $item->status }}

                                                        <div class="progress progress-bar-success progress-lg">
                                                            <div class="progress-bar" role="progressbar" aria-valuenow="{{ $item->progress }}" aria-valuemin="0" aria-valuemax="100" style="width:{{ $item->progress }}%">{{ $item->progress }}%</div>
                                                        </div>
                                                    </td>
                                                    <td>

                                                <!-- Delete Modal -->
                                                <a class="" data-toggle="modal" data-target="#delete-pro{{$item->id}}">
                                                <i class="feather icon-trash danger"></i>
                                                </a>
                                                 | 

                                                <!-- Modal -->
                                                <div class="modal fade text-left" id="delete-pro{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
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
                                                                <p>Die Datensatznummer lautet: {{$item->id}} </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                              <a type="button" href="{{url('/delivery_note_destroy').'/'.$item->id}}" class="btn btn-primary">Yes</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Delete Modal -->


                                            <!-- Begin: Edit -->
                                            <a  class="" data-toggle="modal" data-target="#editmodel{{$item->id}}">
                                            <i class="feather icon-edit"></i>
                                                </a>
                                        <!-- Modal -->
                                        <div class="modal fade text-left" id="editmodel{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel1">Bearbeiten</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                            <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\DeliveryNoteController@update')}}"class="custom-file-upload" enctype="multipart/form-data" >
                                                                @csrf

                                                                


                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">Submit</button>

                                                            </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                            </div>
                                    <!-- End Edit Modal -->

                                                    </td>
                                                    <!-- Operation Section -->
                                                    <td>

                                                         <!-- Progress Report-->
                                                    <a  data-toggle="modal" data-target="#progress{{$item->id}}">
                                                        <div class="badge badge-success mr-1 mb-1">
                                                            <i class="fa fa fa-tasks"></i>
                                                            <span>Paketfortschritt</span>
                                                        </div>
                                                    </a>
                                                    <!-- Modal -->
                                                    <div class="modal fade text-left" id="progress{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    Paketfortschritt:: {{ $item->delivery_note }}
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <form method="post" action="{{ action('App\Http\Controllers\DeliveryNoteController@progress') }}">
                                                                    @csrf
                                                                    <div class="modal-body">
                                                                        <input type="hidden" value="{{ $item->id }}" name="id">
                                                                        <select class="form-control" name="progress">
                                                                            <option selected value="{{ $item->progress }}">{{ $item->progress }}%</option>
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

                                                   <!-- Progress Report-->
                                                   <a  class="" data-toggle="modal" data-target="#pdf{{$item->id}}">
                                                    <div class="badge badge-primary mr-1 mb-1">
                                                        <i class="fa fa-file-pdf-o"></i>
                                                        <span>PDF Datai</span>
                                                    </div>
                                                        </a>

                                                   
                                                   <!-- Modal -->
                                                   <div class="modal fade text-left" id="pdf{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                       <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                           <div class="modal-content">
                                                               <div class="modal-header">
                                                                   Lieferschein-PDF-Datei hinzufügen: {{ $item->delivery_note }}
                                                                   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                       <span aria-hidden="true">&times;</span>
                                                                   </button>
                                                               </div>
                                                               <form method="post" action="{{ action('App\Http\Controllers\DeliveryNoteController@pdf') }}" class="custom-file-upload" enctype="multipart/form-data">
                                                                   @csrf
                                                                   <div class="modal-body">
                                                                       <input type="hidden" value="{{ $item->id }}" name="id">
                                                                       <input type="file" class="form-control" name="pdf">
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                      <button type="submit" class="btn btn-primary">Aktualisieren</button>
                                                                    </div>
                                                               </form>
                                                              
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <a type="button" href="{{ url('/linked_delivery/'.$item->id) }}" class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1" >
                                                <i class="feather icon-link    "></i>
                                                </a>

                                                        @if($item->status=="Verfügbar" || $item->status=="")
                                                        <a type="button" href="{{ url('/delivery_published/'.$item->id) }}" class="btn btn-icon btn-icon rounded-circle btn-success mr-1 mb-1" >
                                                        <i class="feather icon-check"></i>
                                                        </a>
                                                        @else
                                                        <a type="button" href="{{ url('/delivery_unpublish/'.$item->id) }}" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" >
                                                        <i class="feather icon-check"></i>
                                                        </a>
                                                        @endif

                                                    
                                                    </td>
                                                    <!-- Operation Section -->
                                                   
                                                </tr>
                                               

                                                @endif
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
                {{$content->links()}}
            </div>
        </div>
    </div>
    <!-- END: Content-->
@stop

@section('script')
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


@endsection