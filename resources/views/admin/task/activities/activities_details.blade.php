@extends('admin.layouts.app')
@section('title') Arbeitsschritte Details @stop
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
                                <h2 class="content-header-title float-left mb-0">PHASE AKTIVITÄTEN UND SCHRITTE</h2>
                                <div class="breadcrumb-wrapper col-12">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ url('/') }}">HOME</a>
                                        </li>
                                        <li class="breadcrumb-item"><a href="{{ url('phase_management/'.request()->id.'/'.request()->product.'/'.request()->section_name) }}">PHASE</a>
                                        </li>
                                        <li class="breadcrumb-item open"><a href=" ">{{ $title->phase_name }}</a>
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
                            <div class="card-header">
                                <h4 class="card-title">{{ $title->phase_name }}</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">  
                                        <div class="row">
                                            <div class="col-md-1">
                                                <a type="button" class="btn btn-outline-primary block  mr-1 mb-1" href="{{ url('phase_management/'.request()->id.'/'.request()->product.'/'.request()->section_id, '/'.request()->section_name) }}">  Zurück </a>    
                                            </div>
                                            <div class="col-md-6">
                                                <form action="{{ route('activities', ['id'=>request()->id, 'product'=>request()->product, 'section_id'=>request()->id, 'section_name'=>request()->section_name])}}">
                                                    <fieldset>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control"  name="search" aria-describedby="button-addon2">
                                                            <div class="input-group-append" id="button-addon2">
                                                                <button class="btn btn-primary waves-effect waves-light" type="submit">Go</button>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </form>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-outline-primary block " data-toggle="modal" data-target="#default">
                                                    Neue Aufgabe hinzufügen
                                                </button> 
                                                <div class="modal fade text-left" id="default" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                            <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel1">Neu</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\PhaseActivitiesController@store')}}" class="custom-file-upload" enctype="multipart/form-data">
                                                                    @csrf
                                                                    <fieldset> 
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                
                                                                                    <input type="hidden" class="form-control"  name="phase_id" value="{{ request()->id }}" required>
                                                                                    <input type="hidden" class="form-control"  name="product_id" value="{{ request()->product }}" required>
                                                                                    <input type="hidden" class="form-control"  name="section_id" value="{{ request()->section_id }}" required>
                                                                                    <input type="hidden" class="form-control"  name="section_name" value="{{ request()->section_name }}" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label for="Title">
                                                                                        Initial
                                                                                    </label>
                                                                                
                                                                                    <input type="text" class="form-control"  name="initial"  required>
                                                                                    @if ($errors->has('initial'))<p style="color:red;">{!!$errors->first('initial')!!}</p>@endif
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label for="Title">
                                                                                        Title
                                                                                    </label>
                                                                                
                                                                                    <input type="text" class="form-control"  name="title"  required>
                                                                                    @if ($errors->has('title'))<p style="color:red;">{!!$errors->first('title')!!}</p>@endif
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
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label for="Title">
                                                                                        Description
                                                                                    </label>
                                                                                
                                                                                    <textarea  class="form-control"  name="description"   ></textarea>
                                                                                    @if ($errors->has('description'))<p style="color:red;">{!!$errors->first('description')!!}</p>@endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </fieldset>
                                                                    <div class="modal-footer">
                                                                        <button type="submit" class="btn btn-primary">Einreichen</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>   <!-- Modal End -->
                                            </div>
                                        </div> 
                             
                                        <div class="row">
                                             <div class="table-responsive">
                                                    <table class="table table-striped mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">#</th>
                                                                <th scope="col">Initial</th>
                                                                <th scope="col">Titel</th>
                                                                <th scope="col">Beschreibung</th>
                                                                <th scope="col">Foto erforderlich</th>
                                                                <th scope="col">Beantwortet von</th>
                                                                <th scope="col">Aktion</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($data as $item)
                                                            <tr> 
                                                                <th scope="row">{{ $item->id }}</th>
                                                                <th scope="row">{{ $item->initial }}</th>
                                                                <td > 
                                                                <a href="{{ url('sub_task/'.$item->id.'/'.$item->phase_id.'/'.request()->product) }}" type="button" class="btn btn-outline-primary mr-1 mb-1 waves-effect waves-light">
                                                                    {{$item->title}} </a>
                                                                </td>
                                                                <td>{{ $item->description}} </td>
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
                                                                                    <a type="button" href="{{url('/activities_destroy').'/'.$item->id}}" class="btn btn-primary">Ja</a>
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
                                                                    <a type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1" data-toggle="modal" data-target="#edit{{$item->id}}">
                                                                        <i class="feather icon-edit"></i>
                                                                    </a> 
                                                                    @endif 
                                                                      <div class="modal fade text-left" id="edit{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                                    <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h4 class="modal-title" id="myModalLabel1">Bearbeiten</h4>
                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                            <span aria-hidden="true">&times;</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\PhaseActivitiesController@update')}}" class="custom-file-upload" enctype="multipart/form-data">
                                                                                            @csrf
                                                                                            <fieldset> 
                                                                                                <div class="row">
                                                                                                    <div class="col-md-12">
                                                                                                        <div class="form-group">
                                                                                                        
                                                                                                            <input type="hidden" class="form-control"  name="phase_id" value="{{ request()->id }}"  >
                                                                                                            <input type="hidden" class="form-control"  name="product_id" value="{{ request()->product }}"  >
                                                                                                            <input type="hidden" class="form-control"  name="id" value="{{ $item->id }}"  >
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col-md-12">
                                                                                                        <div class="form-group">
                                                                                                            <label for="Title">
                                                                                                                Initial
                                                                                                            </label>
                                                                                                        
                                                                                                            <input type="text" class="form-control"  name="initial"  value="{{$item->initial}}" required>
                                                                                                            @if ($errors->has('initial'))<p style="color:red;">{!!$errors->first('initial')!!}</p>@endif
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col-md-12">
                                                                                                        <div class="form-group">
                                                                                                            <label for="Title">
                                                                                                                Title
                                                                                                            </label>
                                                                                                        
                                                                                                            <input type="text" class="form-control"  name="title" value="{{$item->title}}" required>
                                                                                                            @if ($errors->has('title'))<p style="color:red;">{!!$errors->first('title')!!}</p>@endif
                                                                                                        </div>
                                                                                                    </div>
                                                                                                     <div class="col-md-12">
                                                                                                        <div class="form-group">
                                                                                                            <fieldset>
                                                                                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                                                                                    <input type="checkbox" @if($item->photo=='needed') checked @endif value="needed" name="photo">
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
                                                                                                    <div class="col-md-12">
                                                                                                        <div class="form-group">
                                                                                                            <label for="Title">
                                                                                                                Description
                                                                                                            </label>
                                                                                                        
                                                                                                            <textarea  class="form-control"  name="description"   > {{$item->description}}</textarea>
                                                                                                            @if ($errors->has('description'))<p style="color:red;">{!!$errors->first('description')!!}</p>@endif
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </fieldset>
                                                                                            <div class="modal-footer">
                                                                                                <button type="submit" class="btn btn-primary">Einreichen</button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>   <!-- Modal End -->

                                                                    
                                                            
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
    <!-- END: Content-->
@stop

@section('script')
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