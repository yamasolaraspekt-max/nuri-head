@extends('admin.layouts.app')
@section('title') Abteilung @stop
@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css')}}">

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
                            <h2 class="content-header-title float-left mb-0">ABTEILUNG & STELLENBEREICH</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">HOME</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">DETAILS</a>
                                    </li> 
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrum-right">
                        <div class="dropdown">
                            <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="feather icon-settings"></i></button>
                            <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">Chat</a><a class="dropdown-item" href="#">Email</a><a class="dropdown-item" href="#">Calendar</a></div>
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
                                    <div class="col-md-12 d-flex">
                                        <div class="col-md-9">
                                            <form action="{{action('App\Http\Controllers\DepartmentController@index')}}"> 
                                                    <div class="input-group">
                                                        <input type="text" name="search" class="form-control"
                                                            placeholder="Search Form" aria-describedby="button-addon2">
                                                        <div class="input-group-append" id="button-addon2">
                                                            <button class="btn btn-primary" type="submit">SUCHEN</button>
                                                        </div>
                                                    </div> 
                                            </form>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="card-body">
                                                <button type="button" class="btn btn-outline-primary block btn-lg"
                                                    data-toggle="modal" data-target="#default">
                                                    Add New
                                                </button>
                                                <!-- Modal -->
                                                <div class="modal fade text-left" id="default" tabindex="-1" role="dialog"
                                                    aria-labelledby="myModalLabel1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel1">Neu</h4>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form class="form-horizontal" novalidate method="post"
                                                                    action="{{action('App\Http\Controllers\DepartmentController@store')}}"
                                                                    class="custom-file-upload" enctype="multipart/form-data">
                                                                    @csrf
                                                                    <fieldset>
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label for="Title">
                                                                                        Abteilung
                                                                                    </label>

                                                                                    <input type="text" class="form-control"
                                                                                        name="department_name" required>
                                                                                    @if ($errors->has('department_name'))<p
                                                                                        style="color:red;">
                                                                                        {!!$errors->first('department_name')!!}
                                                                                    </p>@endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </fieldset>
                                                                    <div class="modal-footer">
                                                                        <button type="submit"
                                                                            class="btn btn-primary">Einreichen</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
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
                                                        <th scope="col">Abteilung / Position</th>
                                                        <th scope="col">Stellenbeschreibung</th>
                                                        <th scope="col">Status</th>
                                                        <th scope="col">Ackion</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($parents as $item)
                                                        <tr>
                                                            <th scope="row">{{$item->id}}</th>
                                                            <td> {{ $item->department_name }} <br>
                                                                 @foreach($children as $child)
                                                                    @if($item->id == $child->parent_id)
                                                                        <div class="badge badge-pill badge-primary mr-1 mb-1">{{ $child->department_name }}</div> 
                                                                    @endif
                                                                @endforeach
                                                            
                                                            </td> 
                                                            <td>

                                                               
                                                                    <button type="button" class="btn btn-outline-primary waves-effect waves-light" data-toggle="modal" data-target="#jobs{{ $item->id }}">
                                                                    <i class="feather icon-edit" ></i> Stellenbeschreibung
                                                                    </button>
                                                             

                                                                    <!-- Job Description Modal -->
                                                                    <div class="modal fade text-left" id="jobs{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h4 class="modal-title" id="myModalLabel17">Arbeitsbeschreibung: {{$item->department_name}}</h4>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">×</span>
                                                                                    </button>
                                                                                </div>
                                                                                <form class="form form-horizontal" method="post" action="{{ route('department.description.update') }}"  class="custom-file-upload" enctype="multipart/form-data">
                                                                                    @csrf  
                                                                                    <div class="modal-body"> 
                                                                                        <h6><i class="step-icon feather icon-image"></i> Schreiben Sie die Stellenbeschreibung für diese Position</h6>   
                                                                                                    <input type="hidden" value="{{ $item->id }}" name="id"> 
                                                                                        <div class="row"> 
                                                                                            <div id="editor{{$item->id}}" class="editor-container form-control" data-target="#editor_text{{$item->id}}" style="height: 400px !important;">
                                                                                                  {!! old('description', $item->description) !!}
                                                                                            </div>
                                                                                            <textarea name="job_description" hidden id="editor_text{{$item->id}}" cols="30" rows="10">
                                                                                                {{ old('description', $item->description) }}
                                                                                            </textarea> 
                                                                                        </div>         
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="submit" class="btn btn-primary waves-effect waves-light">Einreichen</button>
                                                                                    </div>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                     
                                                            </td> 
                                                             <td>
                                                                @if($item->status=="Published")
                                                                    <div class="badge badge-pill badge-success mr-1 mb-1">Aktiv</div>  
                                                                @else
                                                                    <div class="badge badge-pill badge-danger mr-1 mb-1">Inaktiv</div>  
                                                                @endif
                                                            </td>
                                                      
                                                            <td> 
                                                               <!-- Delete Button -->
                                                                <button type="button"
                                                                    class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1"
                                                                    data-toggle="modal" data-target="#delete-pro{{$item->id}}">
                                                                    <i class="feather icon-trash"></i>
                                                                </button> 

                                                                <!-- Delete Confirmation Modal -->
                                                                <div class="modal fade text-left" id="delete-pro{{$item->id}}"
                                                                    tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
                                                                    aria-hidden="true">
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
                                                                                <form action="{{ route('department.destroy', $item->id) }}" method="POST">
                                                                                    @csrf
                                                                                    @method('DELETE')  <!-- This converts the request to DELETE -->
                                                                                    <button type="submit" class="btn btn-primary">Ja</button>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div> 


                                                                <!-- Begin: Edit -->
                                                                <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1"
                                                                    data-toggle="modal" data-target="#editmodel{{$item->id}}">
                                                                    <i class="feather icon-edit"></i>
                                                                </button>
                                                                <!-- Modal -->
                                                                <div class="modal fade text-left" id="editmodel{{$item->id}}" tabindex="-1"
                                                                    role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h4 class="modal-title" id="myModalLabel1">Bearbeiten</h4>
                                                                                <button type="button" class="close" data-dismiss="modal"
                                                                                    aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>
                                                                            <form class="form-horizontal" novalidate method="post"
                                                                                    action="{{action('App\Http\Controllers\DepartmentController@update')}}">
                                                                                    @csrf 
                                                                                <div class="modal-body"> 
                                                                                    <fieldset>
                                                                                        <div class="row">
                                                                                            <div class="col-md-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="Title">
                                                                                                        Abteilung
                                                                                                    </label>
                                                                                                    <input type="text" class="form-control"
                                                                                                        name="department_name"
                                                                                                        value="{{$item->department_name}}" required>
                                                                                                    <input type="hidden" class="form-control" name="id"
                                                                                                        value="{{$item->id}}" required>
                                                                                                    @if ($errors->has('department_name'))<p
                                                                                                        style="color:red;">
                                                                                                        {!!$errors->first('department_name')!!}</p>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div> 
                                                                                        </div> 
                                                                                    </fieldset>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="submit" class="btn btn-primary">Einreichen</button> 
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- End Edit Modal -->
                                                                 @if($item->status=="Unpublished")
                                                                <a type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1" href="{{ url('department_publish/'.$item->id)}}"><i class="feather icon-check"></i> </a>
                                                                @else
                                                                <a type="button" class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1" href="{{ url('department_unpublish/'.$item->id)}}"><i class="feather icon-x"></i></a>
                                                                @endif

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
                {{$parents->links()}}
            </div>
        </div>
    </div>
<!-- END: Content-->
@stop

@section('script')
<script>
$(document).ready(function() {
    @if(Session::has('update_msg'))
    toastr.success("{{ session('updated_msg') }}");
    @endif
    @if(Session::has('save_msg'))
    toastr.success("{{ session('save_msg') }}");
    @endif

     @if(session('error_msg'))
        toastr.error("{{ session('error_msg') }}");
    @endif

    @if(Session::has('delete_msg'))
    toastr.error("{{ session('delete_msg') }}");
    @endif
});
</script>




<script src="{{asset('app-assets/vendors/js/editors/quill/quill.min.js')}}"></script>
<!-- Quill Other Editor -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toolbarOptions = [
            ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
            ['blockquote', 'code-block'],
            [{ 'header': 1 }, { 'header': 2 }],               // custom button values
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
            [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
            [{ 'direction': 'rtl' }],                         // text direction
            [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'font': [] }],
            [{ 'align': [] }],
            ['link', 'image', 'video', 'formula'],
            ['clean']
        ];

        document.querySelectorAll('.editor-container').forEach(function (editorContainer) {
            var quill = new Quill(editorContainer, {
                modules: {
                    toolbar: toolbarOptions
                },
                theme: 'snow'
            });

            var targetTextarea = document.querySelector(editorContainer.getAttribute('data-target'));

            quill.on('text-change', function () {
                targetTextarea.value = quill.root.innerHTML;
            });
        });
    });

 
</script>
@endsection