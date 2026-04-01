@extends('admin.layouts.app')

@section('title')  PDF Datai @endsection
@section('style')
<!-- Include stylesheet -->

<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>
    <link rel="stylesheet" href="http://keith-wood.name/css/jquery.signature.css">

<style>
    .kbw-signature { width: 100%; height: 200px;}
    #sig canvas{
        width: 100% !important;
        height: auto;
    }
    body { overflow: hidden;}
</style>
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
                            <h2 class="content-header-title float-left mb-0"> Lieferschein</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">PDF </a>
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
                                        <div class="form-body">
                                                <div class="row">

                                                

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Paket geliefert %</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <div class="progress progress-bar-success progress-lg">
                                                                    <div class="progress-bar" role="progressbar" aria-valuenow="{{ $data->progress }}" aria-valuemin="0" aria-valuemax="100" style="width:{{ $data->progress }}%">{{ $data->progress }}%</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Benutzer</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="hidden" value="{{ url()->previous() }}" name="router">
                                                                <input type="text" disabled class="form-control" value="{{ DB::table('users')->join('employees', 'employees.id', '=', 'users.name')->where('users.name', '=', auth()->user()->name)->select('employees.name')->pluck('name')->first() }}" name="approved_by"  >
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Lieferschein</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input disabled type="text" class="form-control" value="{{ $data->delivery_note }}" name="approved_date" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Geliefert von</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input disabled type="text" class="form-control" value="{{ $data->from }}" name="approved_date" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Zweig</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input disabled type="text" class="form-control" value="{{ $data->branch }}" name="approved_date" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Übergabe durch</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input disabled type="text" class="form-control" value="{{ $data->name }} {{ $data->lastname }}" name="approved_date" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Datum</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input disabled type="text" class="form-control" value="{{ $data->handover_date }}" name="approved_date" required>
                                                            </div>
                                                        </div>
                                                    </div>



                                                  <a type="button" href="{{ url('delivery_note_details?search='.$data->delivery_note) }}" class="btn btn-success mr-1 mb-1 waves-effect waves-light">Zurück</a>

                                                  <a type="button" class="btn btn-warning mr-1 mb-1 waves-effect waves-light"  data-toggle="modal" data-target="#progress{{$data->id}}"><i class="fa fa-tasks"></i> Paketfortschritt</a>
    
                                                  <!-- Modal -->
                                                  <div class="modal fade text-left" id="progress{{$data->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                      <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                          <div class="modal-content">
                                                              <div class="modal-header">
                                                                  Paketfortschritt:: {{ $data->delivery_note }}
                                                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                      <span aria-hidden="true">&times;</span>
                                                                  </button>
                                                              </div>
                                                              <form method="post" action="{{ action('App\Http\Controllers\DeliveryNoteController@progress') }}">
                                                                  @csrf
                                                                  <div class="modal-body">
                                                                      <input type="hidden" value="{{ $data->id }}" name="id">
                                                                      <select class="form-control" name="progress">
                                                                          <option selected value="{{ $data->progress }}">{{ $data->progress }}%</option>
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


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
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-12 justify-content-center">
                                                    <iframe src="{{ url('images/delivery_note/pdf/'.$data->pdf) }}" width="100%" height="800"></iframe>
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
            $('#company').select2();
        });

        $(document).ready(function() {
            $('#purchased_by').select2();
        });

        $(document).ready(function() {
            $('#edited_by').select2();
        });
        
    </script>

 <!-- Include required JavaScript -->
 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
 <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
 <script src="http://keith-wood.name/js/jquery.signature.js"></script>

 <script type="text/javascript">
     var sig = $('#sig').signature({
         syncField: '#signature',
         syncFormat: 'PNG'
     });

     $('#clear').click(function(e) {
         e.preventDefault();
         sig.signature('clear');
         $("#signature").val('');
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

       @if(Session::has('not_save'))
       toastr.danger("{{ session('not_save') }}");
       @endif

      
            
    @if(Session::has('delete_msg'))
    toastr.error("{{ session('delete_msg') }}");
    @endif
    });
    

</script>
@endsection