@extends('admin.layouts.app')

@section('title')  Rechnung  @endsection
@section('style')
<!-- Include stylesheet -->
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css')}}">
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
                            <h2 class="content-header-title float-left mb-0"> Rechnung</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Grund </a>
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
                                                                <span>Benutzer</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" disabled class="form-control" value="{{ DB::table('users')->join('employees', 'employees.id', '=', 'users.name')->where('users.name', '=', $data->draft_by)->select('employees.name')->pluck('name')->first() }}" name="draft_by"  >
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Genehmigtes Datum</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input disabled type="text" class="form-control" value="{{ $data->draft_date }}" name="draft_date" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <h6><i class="step-icon feather icon-image"></i>Grund für die Ablehnung der Rechnung</h6>
                                                    <fieldset>
                                                        <div class="row" style="pointer-events: none;
                                                        opacity: 0.5; ">
                                                        
                                                        <div id="editor" class="form-control"  style="height: 400px !important;">
                                                            {!! $data->draft_reason !!}
                                                        </div>
                                                        <textarea name="editor_text" disabled hidden id="editor_text" cols="30" rows="10"> {!! $data->draft_reason !!}</textarea>

                                                               
                                                                
                                                                
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a type="button" href="{{ url('/') }}" class="btn btn-primary">Zurück </a>
                                                        </div>
                                                </fieldset>

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
                                                    <iframe src="{{ url('invoices/'.$data->pdf) }}" width="100%" height="800"></iframe>
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