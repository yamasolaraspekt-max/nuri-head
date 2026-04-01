@extends('admin.layouts.app')
@section('title') ACTIVITIES @stop
@section('style')  
<style>
    .form-control {
    text-align: left; /* Ensures text aligns to the left */
    padding: 0;       /* Removes any additional padding */
    line-height: normal; /* Ensures line height is normal */
}
textarea.form-control {
    white-space: pre-line; /* Keeps line breaks but removes extra leading spaces */
}
</style>
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
                                <h2 class="content-header-title float-left mb-0">ARBEITSSCHRITTE</h2>
                                <div class="breadcrumb-wrapper col-12">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">HOME</a>
                                        </li> 
                                        <li class="breadcrumb-item"><a href="{{ url('/task_phase_details') }}">PHASE</a>
                                        </li>
                                        <li class="breadcrumb-item"><a href="{{ url('/activities/'.request()->phase_id.'/'.request()->product) }}">{{ DB::table('phase_activities')->where('id', request()->task_id)->value('title') }}
</a>
                                        </li>
                                         <li class="breadcrumb-item"><a href="">ACTIVITIES</a>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                          
            <div class="content-body">
                <section id="basic-horizontal-layouts">
                    <div class="row match-height">
                        <div class="col-md-6 col-12">
                            <div class="row" id="table-hover-animation">
                                    <div class="col-12">
                                        <div class="card"  > 
                                            <div class="card-content">
                                                <div class="card-body">  
                                                  <div class="row">
                                                      <div class="col-md-2">
                                                        <a type="button" class="btn btn-outline-primary block  mr-1 mb-1" href="{{ url('/activities/'.request()->phase_id.'/'.request()->product) }}">  Zurück </a>  
                                                        </div> 
                                                        <div class="col-md-6">
                                                            <form action="{{ route('sub.tasks.view',['phase_id'=>request()->phase_id, 'task_id'=>request()->task_id, 'product'=>request()->product])}}">
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
                                                          <div class="col-md-3"> 
                                                            <button type="button" class="btn btn-outline-primary waves-effect waves-light" data-toggle="modal" data-target="#primary">
                                                            Neue hinzufügen
                                                            </button>
                                                            <div class="modal fade text-left" id="primary" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true" style="display: none;">
                                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header bg-primary white">
                                                                            <h5 class="modal-title" id="myModalLabel160">NEUE PHASE</h5>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">×</span>
                                                                            </button>
                                                                        </div>
                                                                            <form class="form form-horizontal" method="post" action="{{ action('App\Http\Controllers\TaskSubTaskController@store')}}"  class="custom-file-upload" enctype="multipart/form-data">
                                                                                    @csrf   
                                                                                <div class="modal-body">    
                                                                                    <div class="row">
                                                                                        <div class="col-md-12" >
                                                                                            <div class="form-group">
                                                                                                <label for="Title"> Aufgabentitel </label>
                                                                                                <input type="hidden" value="{{ request()->task_id }}" name="task_id">
                                                                                                <input type="hidden" value="{{ request()->phase_id }}" name="phase_id">
                                                                                                <input type="text" class="form-control" name="task_title" >
                                                                                            </div>
                                                                                        </div> 
                                                                                    </div>  
                                                                                    <div class="row">
                                                                                        <div class="col-md-6" >
                                                                                            <div class="form-group">
                                                                                                <label for="Title"> Aufgabendauer </label> 
                                                                                                <input type="number" class="form-control" name="duration" >
                                                                                            </div>
                                                                                        </div> 
                                                                                        <div class="col-md-6" >
                                                                                            <div class="form-group">
                                                                                                <label for="Title"> Dauer Typ </label> 
                                                                                            <select name="duration_type" id="" class="form-control">
                                                                                                    <option value="Minuten">Minuten</option>
                                                                                                    <option value="Stunden">Stunden</option>
                                                                                                    <option value="Tage">Tage</option>
                                                                                                    <option value="Wochen">Wochen</option>
                                                                                                    <option value="Monate">Monate</option>
                                                                                                    <option value="Jahre">Jahre</option> 
                                                                                            </select>
                                                                                            </div>
                                                                                        </div> 
                                                                                    </div> 
                                                                                    <div class="col-md-12">
                                                                                        <div class="form-group">
                                                                                            <fieldset>
                                                                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                                                                    <input type="checkbox"  value="needed" name="photo">
                                                                                                    <span class="vs-checkbox">
                                                                                                        <span class="vs-checkbox--check">
                                                                                                            <i class="vs-icon feather icon-check"></i>
                                                                                                        </span>
                                                                                                    </span>
                                                                                                    <span class="">wird ein Foto benötigt?</span>
                                                                                                </div>
                                                                                            </fieldset>
                                                                                        </div>
                                                                                    </div> 
                                                                                     <div class="col-md-12">
                                                                                        <div class="form-group">
                                                                                            <label for="Title">
                                                                                                Beantwortet von
                                                                                            </label>
                                                                                            <select class="form-control" name="answered_by">
                                                                                                <option value="1">Kunden</option>
                                                                                                <option value="2" selected >Mitarbeiter</option> 
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                                    <!-- Step 3 -->
                                                                                    <h6><i class="step-icon feather icon-image"></i> Geben Sie Ihre Nachricht an die Gruppe ein</h6>
                                                                                                    
                                                                                    <div class="row"> 
                                                                                        
                                                                                        <textarea name="description"   rows="10" class="form-control"></textarea> 
                                                                                    </div>
                                                                                            
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="submit" class="btn btn-primary waves-effect waves-light" >Einreichen</button>
                                                                                </div>
                                                                            </form> 
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                  </div>

                                                    <div class="col-md-12">
                                                        <div class="table-responsive">
                                                            <table class="table table-striped mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th scope="col">ID</th> 
                                                                        <th scope="col">Phase</th>
                                                                        <th scope="col">Aufgaben Titel</th>
                                                                        <th scope="col">Dauer</th>
                                                                        <th scope="col">Beschreibug</th>
                                                                        <th scope="col">Foto</th>
                                                                        <th scope="col">Beantwortet von</th>
                                                                        <th scope="col">Aktion</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($data as $item)
                                                                    <tr>
                                                                        <th scope="row">{{ $item->id }}</th>
                                                                         <td>{{$item->phase_name}}</td>
                                                                         <td>{{$item->task_title}}</td>
                                                                         <td>{{$item->duration}} {{ $item->duration_type}}</td> 
                                                                         <td>
                                                                            <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1 waves-effect waves-light" data-toggle="modal" data-target="#large{{$item->id}}">
                                                                               <i class="feather icon-inbox"></i>
                                                                            </button>
                                                                            <div class="modal fade text-left" id="large{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true" style="display: none;">
                                                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h4 class="modal-title" id="myModalLabel17">{{$item->phase_name}} - {{$item->task_title}}</h4>
                                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                <span aria-hidden="true">×</span>
                                                                                            </button>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                      {{ $item->description}}
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                            <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal">OK</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                         </td>
                                                                            <td>@if($item->photo=='needed') Foto erforderlich @else Nicht erforderlich @endif </td>
                                                                            <td>@if($item->answered_by==1) Kunde @else Mitarbeiter @endif </td>
                                                                            

                                                                        <td>
                                                                        @if(DB::table('user_rolls')
                                                                            ->where('user_rolls.user_id', '=', auth()->user()->name)
                                                                            ->where('user_rolls.item_id', '=', 'Customer')
                                                                            ->where('user_rolls.is_delete', '=', 'on')
                                                                            ->first())
                                                                    <!-- Delete Modal -->
                                                                    <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" data-toggle="modal" data-target="#delete-pro{{$item->id}}">
                                                                    <i class="feather icon-trash"></i>
                                                                    </button>
                                                                    @endif

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
                                                                                    <h5>Aufzeichnung löschen</h5>
                                                                                    <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                                    <p>Die Datensatznummer lautet:{{$item->id}} </p>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                <a type="button" href="{{url('/sub_task_delete').'/'.$item->id}}" class="btn btn-primary">Ja</a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- End Delete Modal -->


                                                                <!-- Begin: Edit -->
                                                                        @if(DB::table('user_rolls')
                                                                            ->where('user_rolls.user_id', '=', auth()->user()->name)
                                                                            ->where('user_rolls.item_id', '=', 'Customer')
                                                                            ->where('user_rolls.is_update', '=', 'on')
                                                                            ->first())
                                                                                <a type="button"  class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1" data-toggle="modal" data-target="#edit{{$item->id}}">
                                                                                <i class="feather icon-edit"></i>
                                                                                </a> 
                                                                        @endif

                                                                         <div class="modal fade text-left" id="edit{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true" style="display: none;">
                                                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header bg-primary white">
                                                                                        <h5 class="modal-title" id="myModalLabel160">BEARBEITEN</h5>
                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                            <span aria-hidden="true">×</span>
                                                                                        </button>
                                                                                    </div>
                                                                                        <form class="form form-horizontal" method="post" action="{{ action('App\Http\Controllers\TaskSubTaskController@update')}}"  class="custom-file-upload" enctype="multipart/form-data">
                                                                                                @csrf   
                                                                                            <div class="modal-body">    
                                                                                                <div class="row">
                                                                                                    <div class="col-md-12" >
                                                                                                        <div class="form-group">
                                                                                                            <label for="Title"> Aufgabentitel </label>
                                                                                                            <input type="hidden" value="{{ request()->task_id }}" name="task_id">
                                                                                                            <input type="hidden" value="{{ request()->phase_id }}" name="phase_id">
                                                                                                            <input type="hidden" value="{{ $item->id }}" name="id">
                                                                                                            <input type="text" class="form-control" name="task_title" value="{{ $item->task_title}}" >
                                                                                                        </div>
                                                                                                    </div> 
                                                                                                </div>  
                                                                                                <div class="row">
                                                                                                    <div class="col-md-6" >
                                                                                                        <div class="form-group">
                                                                                                            <label for="Title"> Aufgabendauer </label> 
                                                                                                            <input type="number" class="form-control" name="duration" value="{{ $item->duration}}">
                                                                                                        </div>
                                                                                                    </div> 
                                                                                                    <div class="col-md-6" >
                                                                                                        <div class="form-group">
                                                                                                            <label for="Title"> Dauer Typ </label> 
                                                                                                       <select name="duration_type" id="" class="form-control">
                                                                                                            <option value="Minuten" @if($item->duration_type == "Minuten") selected @endif>Minuten</option>
                                                                                                            <option value="Stunden" @if($item->duration_type == "Stunden") selected @endif>Stunden</option>
                                                                                                            <option value="Tage" @if($item->duration_type == "Tage") selected @endif>Tage</option>
                                                                                                            <option value="Wochen" @if($item->duration_type == "Wochen") selected @endif>Wochen</option>
                                                                                                            <option value="Monate" @if($item->duration_type == "Monate") selected @endif>Monate</option>
                                                                                                            <option value="Jahre" @if($item->duration_type == "Jahre") selected @endif>Jahre</option> 
                                                                                                        </select>

                                                                                                        </div>
                                                                                                    </div> 
                                                                                                </div>  

                                                                                                <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                        <fieldset>
                                                                                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                                                                                <input type="checkbox"  value="needed" @if($item->photo=='needed') checked @endif name="photo">
                                                                                                                <span class="vs-checkbox">
                                                                                                                    <span class="vs-checkbox--check">
                                                                                                                        <i class="vs-icon feather icon-check"></i>
                                                                                                                    </span>
                                                                                                                </span>
                                                                                                                <span class="">wird ein Foto benötigt?</span>
                                                                                                            </div>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                </div> 

                                                                                                <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                        <label for="Title">
                                                                                                            Beantwortet von
                                                                                                        </label>
                                                                                                        <select class="form-control" name="answered_by">
                                                                                                            <option value="1" @if($item->answered_by==1) selected @endif >Kunden</option>
                                                                                                            <option value="2" @if($item->answered_by==2) selected @endif >Mitarbeiter</option> 
                                                                                                        </select>
                                                                                                    </div>
                                                                                                </div>
                                                                                                                <!-- Step 3 -->
                                                                                                <h6><i class="step-icon feather icon-image"></i> Geben Sie Ihre Nachricht an die Gruppe ein</h6>
                                                                                                                
                                                                                                <div class="row"> 
                                                                                                    <textarea name="description"   cols="30" rows="10" class="form-control">
                                                                                                        {!!  $item->description !!}
                                                                                                    </textarea> 
                                                                                                </div>
                                                                                                        
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button type="submit" class="btn btn-primary waves-effect waves-light" >Einreichen</button>
                                                                                            </div>
                                                                                        </form> 
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
                </section>  
            </div>
        </div>
    <!-- END: Content-->
@stop



@section('script')




<script src="{{ asset('js/select2.min.js') }}"></script> 

  

<!-- Quill Other Editor -->
 
   
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