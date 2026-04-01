@extends('admin.layouts.app')
@section('title') Set Product @stop
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
                            <h2 class="content-header-title float-left mb-0">Set Product</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    
                                    <li class="breadcrumb-item"><a href="{{ url('article_group_set') }}"> {{ $title->article_group }}</a>
                                    </li>

                                    <li class="breadcrumb-item"><a href="{{ url('master_set/'.$title->article_group_id.'/'.$title->sub_id) }}"> {{ $title->sub_article }}</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ url('sets/'.$title->master_id) }}">{{ $title->setname }}</a>
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
                                <h4 class="card-title"> </h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">

                                    <div class="col-md-3 float-left">
                                        <div class="card-body">
                                            <a type="button" class="btn btn-outline-primary block btn-lg" href="{{ url('add_image_create/'.request()->master) }}">
                                            Neue hinzufügen
                                            </a>
                                        </div>
                                    </div>
                                        
                                            <div class="table-responsive">
                                                <table class="table table-striped mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">ID</th>
                                                            <th scope="col">Image</th>
                                                            <th scope="col">Name</th>
                                                            <th scope="col">Product</th>
                                                            <th scope="col">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($data as $item)
                                                        <tr>
                                                            <td>{{ $item->id }}</td>
                                                            <td>
                                                                <div class="avatar mr-1 avatar-xl">
                                                                    <img src="{{ asset('images/products/'.$item->image)}}" alt="{{$item->name}}">
                                                                </div>
                                                            </td>
                                                            <td>{{ $item->name }}</td>
                                                            <td>{{ $item->product_name }}</td>
                                                            <td>

                                                        <!-- Delete Modal -->
                                                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" data-toggle="modal" data-target="#delete-pro{{$item->id}}">
                                                        <i class="feather icon-trash"></i>
                                                        </button>

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
                                                                    <a type="button" href="{{url('/add_image_delete').'/'.$item->id}}" class="btn btn-primary">Ja</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- End Delete Modal -->
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
                <!-- Table head options end -->
                {{$data->links()}}
            </div>
        </div>
    </div>
    <!-- END: Content-->
@stop

@section('script')

<script src="{{ asset('js/select2.min.js') }}"></script>
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
$(document).ready(function() {
    $('#product').select2();
    $('#measure').select2();
    
});
</script>
@endsection