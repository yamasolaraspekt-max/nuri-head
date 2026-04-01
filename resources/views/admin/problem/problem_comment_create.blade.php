
@extends('admin.layouts.app')
@section('title') Problem Comment @stop
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
                            <h2 class="content-header-title float-left mb-0">Problem</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{url('problem_view')}}">Problem Comment</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrum-right">
                        
                    </div>
                </div>
            </div>
            <div class="content-body">
               <!-- Nav Justified Starts -->
               <section id="nav-justified">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card overflow-hidden">
                               
                                <div class="card-content">
                                    <div class="card-body">
                                        
                                        <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="home-tab-justified" data-toggle="tab" href="#home-just" role="tab" aria-controls="home-just" aria-selected="true"></a>
                                            </li>
                                          
                                        </ul>

                                        <!-- Tab panes -->
                                        <div class="tab-content pt-1">
                                            <div class="tab-pane active" id="home-just" role="tabpanel" aria-labelledby="home-tab-justified">
                                                 <!-- Form wizard with step validation section start -->
                                                    <section id="validation">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="card">
                                                                    <div class="card-header">
                                                                        <h4 class="card-title"></h4>
                                                                    </div>
                                                                    <div class="card-content">
                                                                        <div class="card-body">
                                                                            <form action="{{ action('App\Http\Controllers\ProblemCommentController@store')}}" method="post"  class="steps-validation wizard-circle">
                                                                                <!-- Step 1 -->
                                                                                @csrf
                                                                                <h6><i class="step-icon feather icon-home"></i> Step 1: Bitte geben Sie das Datum für die Erledigung des Problems an</h6>
                                                                                <fieldset>
                                                                                    <div class="row">
                                                                                        <div class="col-md-6" >
                                                                                            <div class="form-group">
                                                                                                <label for="Title">
                                                                                                        User                                                                                      
                                                                                            </label>
                                                                                                <input type="hidden" value="{{ request()->id }}" name="id">
                                                                                                @foreach ($data as $tik)
                                                                                                <input type="hidden" value="{{ request()->ticket }}" name="ticket">
                                                                                                @endforeach
                                                                                                <input type="text" value="{{ DB::table('users')->join('employees', 'employees.id', '=', 'users.name' )->where('users.name', '=', auth()->user()->name)->select('employees.name')->pluck('name')->first() }}" disabled class="form-control required" id="end_date" name="user" >
                                                                                            </div>
                                                                                        </div> 
                                                                                </fieldset> 
                                                                                <!-- Step 3 -->
                                                                                <h6><i class="step-icon feather icon-image"></i> Geben Sie Ihre Nachricht an die Gruppe ein</h6>
                                                                                <fieldset>
                                                                                    <div class="row"> 
                                                                                        <div id="editor" class="form-control"  style="height: 400px !important;">
                                                                                                
                                                                                        </div>
                                                                                        <textarea name="editor_text" hidden id="editor_text" cols="30" rows="10"></textarea>

                                                                                        <div class="modal-footer">
                                                                                            <button type="submit" class="btn btn-primary">Submit</button>
                                                                                        </div>  
                                                                                    </div>
                                                                                </fieldset>
                                                                            </form>  
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                            <!-- Form wizard with step validation section end -->
                                            </div>
                                            <div class="tab-pane" id="profile-just" role="tabpanel" aria-labelledby="profile-tab-justified">
                                               
                                            </section>
                                            <!-- Form wizard with step validation section end -->
                                            </div>
                                            <div class="tab-pane" id="messages-just" role="tabpanel" aria-labelledby="messages-tab-justified">
                                            
                                            </div>
                                            <div class="tab-pane" id="settings-just" role="tabpanel" aria-labelledby="settings-tab-justified">
                                                
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


<script>
$(document).ready(function() {
    $('#status').select2();
    $('#product_id').select2();
    $('#first_contact').select2();
    $('#responsible').select2();
    $('#error_code').select2();
    // $("#problem_types").select2({
    //     tags: true
    //     });
    
});
</script>




@endsection

