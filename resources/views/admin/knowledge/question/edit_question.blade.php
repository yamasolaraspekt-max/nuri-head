@extends('admin.layouts.app')
@section('content')
   <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Knowledge Base</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Forms</a>
                                    </li>
                                    <li class="breadcrumb-item active"><a href="#">Neue</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                 
            </div>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
            <div class="content-body">
                <!-- Basic Horizontal form layout section start -->
                <section id="basic-horizontal-layouts">
                    <div class="row match-height">
                        <div class="col-md-12 col-12">
                            <div class="card"> 
                                <div class="card-content">
                                    <div class="card-body">
                                      <form class="form form-horizontal" method="post" action="{{ route('question.update')}}">
                                            @csrf
                                            <div class="form-body">
                                                <div class="row">
                                                    <!-- Question Field -->
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <span>Question</span>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <input type="text"  class="form-control" name="question" placeholder="State the question" value="{{ old('question', $data->question) }}">
                                                                @error('question')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                <input type="hidden" name="knowledge_id" value="{{ $data->knowledge_id }}">
                                                <input type="hidden" name="id" value="{{ $data->id }}">

                                                         <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <span>Video</span>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <input type="text"  class="form-control" name="video" placeholder="Video Link" value="{{ old('video', $data->video) }}">
                                                                @error('question')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Editor Field -->
                                                    <div class="col-12">
                                                        <fieldset>
                                                            <div id="editor" class="form-control" style="height: 400px !important;">
                                                                <!-- Initialize the editor here -->
                                                                 {!!  $data->description !!}
                                                            </div>
                                                            <textarea name="editor_text" hidden id="editor_text" style="text-align:right !important;" cols="30" rows="10"> {!!  $data->description !!}</textarea>
                                                            @error('editor_text')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </fieldset>
                                                    </div>
                                                    
                                                    <!-- Submit and Reset Buttons -->
                                                    <div class="col-md-8 offset-md-4 mt-2">
                                                        <button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
                                                        <button type="reset" class="btn btn-outline-warning mr-1 mb-1">Reset</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

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
@endsection

@section('script')
<script src="{{asset('app-assets/vendors/js/editors/quill/quill.min.js')}}"></script>

<script>
    $(document).ready(function(){
           
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

                [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
                [{ 'font': [] }],
                [{ 'align': [] }],
                ['link', 'image', 'video', 'formula'],
        
                ['clean']                                         // remove formatting button
                ];

    var quill = new Quill('#editor', {
    modules: {
        toolbar: toolbarOptions
    },
    theme: 'snow'
    });

        quill.on('text-change', function(delta, oldDelta, source) {
                        if (source == 'api') {
                            console.log("An API call triggered this change.");
                        } else if (source == 'user') {
                            $('#editor_text').text($(".ql-editor").html())
                            console.log("A user action triggered this change.");
                        }
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