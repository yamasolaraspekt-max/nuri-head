
@extends('admin.layouts.app')
@section('title') Problem Beendet @stop
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
                            <h2 class="content-header-title float-left mb-0">Problem</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{url('problem_view')}}">Problem beendet</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Klärung</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
            <div class="content-body"> 
               <section id="nav-justified">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="card overflow-hidden"> 
                                <div class="card-content">
                                    <div class="card-body">  
                                        <div class="tab-content pt-1">
                                            <div class="tab-pane active" id="home-just" role="tabpanel" aria-labelledby="home-tab-justified"> 
                                                <section id="validation">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="card"> 
                                                                <div class="card-content">
                                                                    <div class="card-body">
                                                                        <form action="{{ action('App\Http\Controllers\ProblemController@closeSave')}}" method="post"  > 
                                                                            @csrf
                                                                            <h6>Bitte geben Sie das Datum für die Erledigung des Problems an  </h6>
                                                                            <fieldset> 
                                                                                <div class="col-md-12" >
                                                                                    <div class="form-group">
                                                                                        <label for="Title">
                                                                                                                                                                                        </label>
                                                                                        <input type="hidden" value="{{ request()->id }}" name="id">
                                                                                        <input type="date" class="form-control required" id="end_date" name="end_date" >
                                                                                    </div>
                                                                                </div> 
                                                                            </fieldset> 
                                                                            <fieldset> 
                                                                                <div id="editor" class="form-control"  style="height: 400px !important;"> </div>
                                                                                    <textarea name="editor_text" hidden id="editor_text" cols="30" rows="10"></textarea>    
                                                                                </div> 
                                                                            </fieldset>
                                                                            <div class="modal-footer">
                                                                                <a  type="button" href="{{url('/problem_view')}}" class="btn btn-danger">Abbrechen</a> 
                                                                                <button type="submit" class="btn btn-primary">Speichern</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </section> 
                                                </div> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Nav Justified Ends -->
               

            </div>
        </div>
    </div>
    <!-- END: Content--> 
@endsection

@section('style')
<!-- Include stylesheet -->
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css')}}">

@endsection

@section('script')
<!-- <script src="{{asset('app-assets/js/scripts/editors/editor-quill.js')}}"></script> -->
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{asset('app-assets/vendors/js/editors/quill/quill.min.js')}}"></script>

  

<!-- Quill Other Editor -->
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



@endsection

