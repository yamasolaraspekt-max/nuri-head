@extends('admin.layouts.app')
@section('title') Sub Article Group @stop
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
                            <h2 class="content-header-title float-left mb-0">Article Group</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('article_group') }}">{{ $title->article_group }} </a>
                                    </li>
                                  
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                <div class="card">
                <div class="content-body">
                
                    <div class="row" id="table-hover-animation">
                        <div class="col-md-6 col-12 mb-1">
                            <form action="">
                                <fieldset>
                                    <div class="input-group">
                
                                        <input type="text" class="form-control" placeholder="Geben Sie die Details Ihrer Suche ein"
                                            aria-describedby="button-addon2" name="search">
                                        <div class="input-group-append" id="button-addon2">
                                            <button class="btn btn-primary waves-effect waves-light" type="button"><i
                                                    class="feather icon-search"></i></button>
                                        </div>
                
                                    </div>
                
                                </fieldset>
                            </form>
                        </div>
                        <div class="col-md-2 mb-1">
                            <button type="button" class="btn btn-outline-primary block btn-lg" data-toggle="modal"
                                data-target="#default">
                                Neue hinzufügen
                            </button>
                            <!-- Modal -->
                            <div class="modal fade text-left" id="default" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-scrollable" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myModalLabel1">Neu</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form class="form-horizontal" novalidate method="post"
                                                action="{{action('App\Http\Controllers\SubArticleGroupController@store')}}"
                                                class="custom-file-upload" enctype="multipart/form-data">
                                                @csrf
                                                <fieldset>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="Title">
                                                                    Instials
                                                                </label>
                                                                <input type="hidden" name="article_group_id"
                                                                    value="{{ request()->id }}">
                                                                <input type="hidden" name="status" value="Published">
                                                                <input type="text" class="form-control" name="initial" required>
                                                                @if ($errors->has('initial'))<p style="color:red;">
                                                                    {!!$errors->first('initial')!!}</p>@endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                                <label for="Title">
                                                                    Sub-Artikel-Gruppe
                                                                </label>
                
                                                                <input type="text" class="form-control" name="sub_article" required>
                                                                @if ($errors->has('sub_article'))<p style="color:red;">
                                                                    {!!$errors->first('sub_article')!!}</p>@endif
                                                            </div>
                                                        </div>
                
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="Title">
                                                                    Value
                                                                </label>
                
                                                                <input type="text" class="form-control" name="value" required>
                                                                @if ($errors->has('value'))<p style="color:red;">
                                                                    {!!$errors->first('value')!!}</p>@endif
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
                            </div>
                        </div>
                    </div>
                    <!-- Modal End -->
                
                </div> 
                <div class="col-12">
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Sub Article</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $item)
                                    <tr>
                                        <th scope="row">{{ $item->id }}</th>
                                        <th scope="row">{{ $item->sub_article }}</th>
                                        <td>
                
                                            <!-- Delete Modal -->
                                            @if(DB::table('user_rolls')
                                            ->where('user_rolls.user_id', '=', auth()->user()->name)
                                            ->where('user_rolls.item_id', '=', 'Product')
                                            ->where('user_rolls.is_delete', '=', 'on')
                                            ->first())
                                            <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1"
                                                data-toggle="modal" data-target="#delete-pro{{$item->id}}">
                                                <i class="feather icon-trash"></i>
                                            </button>
                                            @endif
                
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
                                                            <h5>Aufzeichnung löschen</h5>
                                                            <p>Möchten Sie diesen Datensatz wirklich löschen?
                                                            </p>
                                                            <p>Die Datensatznummer lautet:{{$item->id}} </p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a type="button" href="{{url('/sub_article_destroy').'/'.$item->id}}"
                                                                class="btn btn-primary">Yes</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Delete Modal -->
                
                                            @if(DB::table('user_rolls')
                                            ->where('user_rolls.user_id', '=', auth()->user()->name)
                                            ->where('user_rolls.item_id', '=', 'Product')
                                            ->where('user_rolls.is_update', '=', 'on')
                                            ->first())
                                            <!-- Begin: Edit -->
                                            <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1"
                                                data-toggle="modal" data-target="#editmodel{{$item->id}}">
                                                <i class="feather icon-edit"></i>
                                            </button>
                                            @endif
                                            <!-- Modal -->
                                            <div class="modal fade text-left" id="editmodel{{$item->id}}" tabindex="-1" role="dialog"
                                                aria-labelledby="myModalLabel1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="myModalLabel1">Edit</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form class="form-horizontal" novalidate method="post"
                                                                action="{{action('App\Http\Controllers\SubArticleGroupController@update')}}">
                                                                @csrf
                
                                                                <fieldset>
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Sub Article
                                                                                </label>
                                                                                <input type="text" class="form-control"
                                                                                    name="sub_article" value="{{ $item->sub_article }}"
                                                                                    required>
                                                                                <input type="hidden" class="form-control"
                                                                                    name="article_group" value="{{ request()->id }}">
                                                                                <input type="hidden" class="form-control" name="id"
                                                                                    value="{{$item->id }}">
                                                                                @if ($errors->has('sub_article'))<p style="color:red;">
                                                                                    {!!$errors->first('sub_article')!!}</p>@endif
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
                                            <!-- End Edit Modal -->
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {{$data->links()}}
                </div>
                </div>          
            
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