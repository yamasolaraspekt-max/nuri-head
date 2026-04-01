@extends('admin.layouts.app')
@section('title') Sprachen @stop
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
                    <div class="col-md-6 col-12 mb-1">
                    <form action="">
                            <fieldset>
                                <div class="input-group">
                               
                                    <input type="text" class="form-control" placeholder="Geben Sie die Details Ihrer Suche ein" aria-describedby="button-addon2" name="search" >
                                    <div class="input-group-append" id="button-addon2">
                                        <button class="btn btn-primary waves-effect waves-light" type="button"><i class="feather icon-search"></i></button>
                                    </div>
                                
                                </div>
                            
                            </fieldset>
                        </form>
                        </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Sprachen</h4>
                            </div>
                           
                            <div class="card-content">
                                    <div class="card-body">
                               
                                        
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <div class="card-body">
                                                        @if (count($errors) > 0)
                                                            <div class="alert alert-danger">
                                                                <ul>
                                                                    @foreach ($errors->all() as $error)
                                                                        <li>{{ $error }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                            <!-- Table with outer spacing -->
                                                            <form novalidate action="{{ action('App\Http\Controllers\LanguagesController@store')}}" method="post" >
                                                             @csrf
                                                            <div class="table-responsive">
                                                            @if(DB::table('user_rolls')
                                                                        ->where('user_rolls.user_id', '=', auth()->user()->name)
                                                                        ->where('user_rolls.item_id', '=', 'Employee')
                                                                        ->where('user_rolls.is_add', '=', 'on')
                                                                        ->first())
                                                                <table class="table" id="language">
                                                                
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Sprachen</th>
                                                                            <th>Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    
                                                                    <tbody>
                                                                        <tr>
                                                                            <td><input type="text" class="form-control required" placeholder="Sprachen" name="language[0][language]"></td>
                                                            
                                                                             <td>
                                                                                <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" id="add_language"><i class="feather icon-plus"></i></button>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                            
                                                                            <div class="col-8">
                                                                            <div class="input-group">
                                                                                    <button type="submit" class="btn btn-outline-success mr-1 mb-1"><i class="feather icon-save"></i> Datensatz speichern</button>
                                                                                   
                                                                                </div>
                                                                                
                                                                          </div>
                                                                    </form>
                                                                </table>
                                                                @endif
                                                                <table class="table" id="brand_table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>Sprachen</th>
                                                                            <th>Action</th>
                                                                        </tr>
                                                                       
                                                                    </thead>
                                                                <tbody>
                                                                        @foreach ($data as $lang)
                                                                            
                                                                  
                                                                        <tr>
                                                                            <td>{{ $lang->id}}</td>
                                                                            <td>{{ $lang->language}}</td>   
                                                                        <td>
                                                                        <a type="button" href="{{ route('language.destroy',['id'=>$lang->id])}}" class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1"><i class="feather icon-trash-2"></i></a>
                                                                        <a type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1"  data-toggle="modal" data-target="#edit{{$lang->id}}"><i class="feather icon-edit"></i></a>
                                                                          <!-- Modal -->
                                                                          <div class="modal fade text-left" id="edit{{$lang->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                                            <div class="modal-content">
                                                                                                <div class="modal-header">
                                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                        <span aria-hidden="true">&times;</span>
                                                                                                    </button>
                                                                                                </div>
                                                                                                <form method="post" action="{{ action('App\Http\Controllers\LanguagesController@update')}}">
                                                                                                    @csrf
                                                                                                <div class="modal-body" style="text-align: left;">
                                                                                                    <input type="hidden" name="id" value="{{ $lang->id}}">
                                                                                                    <table class="responsible" >
                                                                                                            <tr>
                                                                                                            <label>Sprachen</label>
                                                                                                            <input type="text" class="form-control" name="language" value="{{ $lang->language}}">
                                                                                                            </tr>      
                                                                                                    </table>
                                                                                                    <hr>
                                                                                                    
                                                                                                </div>
                                                                                                <div class="modal-footer">
                                                                                                    <input type="submit" class="btn btn-primary">
                                                                                                </div>
                                                                                                </form>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <!-- End Image Modal -->
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

<script>
        var i = 0;
        $('#add_language').click(function(){
            ++i;
            $('#language').append(
                '<tr> <td><input type="text" class="form-control required" placeholder="Sprachen" name="language['+i+'][language]"></td><td><button type="button"  class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1" id="add_remove"><i class="fa fa-trash"></i></button></td></tr> ' 
                );
        });

        $(document).on('click', '#add_remove', function(){
            $(this).parents('tr').remove();
        })

    </script>
@endsection