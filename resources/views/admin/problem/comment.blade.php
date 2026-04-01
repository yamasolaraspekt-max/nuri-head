@extends('admin.layouts.app')
@section('title') Report @endsection

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
        <div class="content-area-wrapper">
            <div class="sidebar-left col-2">
                <div class="sidebar">
                    <div class="sidebar-content">
                      
                        <div class="email-app-menu">

                            <div class="form-group form-group-compose text-center compose-btn">
                                <a type="button" href="{{ url('problem_comment_create'.'/'.request()->id.'/'.request()->ticket)}}" class="btn btn-outline-primary waves-effect waves-light" ><i class="feather icon-edit"></i> Zwischenbericht</a>
                              
                            </div>

                            <div class="form-group form-group-compose text-center compose-btn">
                                <a type="button" href="{{ url('problem_view')}}" class="btn btn-outline-primary waves-effect waves-light" ><i class="feather icon-arrow-left"></i> Zurück</a>
                              
                            </div>
                            <div class="sidebar-menu-list">
                                <hr>
                                <h5 class="my-2 pt-25">Status</h5>
                                <div class="list-group list-group-labels font-medium-1">
                                 
                                   @if($status=="offen")
                                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex align-items-center"><span class="bullet bullet-danger mr-1"></span> Offen</a>
                                    @elseif($status=="in Klärung")
                                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex align-items-center"><span class="bullet bullet-warning mr-1"></span> in Klärung</a>
                                    @else
                                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex align-items-center"><span class="bullet bullet-success mr-1"></span> Beendet</a>
                                    @endif
                                    
                                </div>

                                <h5 class="my-2 pt-25">Verantwortungsbewusste Benutzer </h5>
                                <div class="list-group list-group-labels font-medium-1">
                                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex align-items-center"><span class="bullet bullet-success mr-1"></span>{{ $first->fname}}</a>
                             
                                    @foreach ($responsible as $res)
                                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex align-items-center"><span class="bullet bullet-warning mr-1"></span>{{ $res->rname}}</a>
                                    @endforeach
                                 
                                   
                                   
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="email-user-list list-group col-10">
                                        <ul class="users-list-wrapper media-list">
                                        @foreach ($data as $com)
                                            <li class="media mail-read">
                                                <div class="media-left pr-20">
                                                    <div class="avatar">
                                                        <img src="{{ asset('images/employee/'.$com->userimage) }}" style="height: 40px;" alt="avtar img holder">
                                                    </div>
                                                 
                                                </div>
                                                <div class="media-body" style="border-style: solid;border-width: thin; border-radius:10px; border-color: #d4cbcb !important;">
                                                    <div class="user-details" style="background: #1a4ea2;">
                                                    <div class="mail-meta-item">
                                                    
                                                        
                                                        </div>
                                                        <div class="mail-items">
                                                        <span class="mail-date"><h5 class="list-group-item-heading text-bold-600 " style="padding:10px; color: white;">{{ $com->username }} {{ $com->userlastname }} <a  style="color: #d4cbcb;  padding: 0px; margin-top: 0px; float: right; position: revert;"> {{ \Carbon\Carbon::parse($com->created_at)->diffForHumans() }}</a></h5> </span>
                                                        
                                                        </div>
                                                       
                                                   
                                                    </div>
                                                
                                                    <div class="mail-message" style="padding:5px">
                                                    <p class="" > {!! $com->comment !!}<a type="button"  data-toggle="modal"  data-target="#x{{$com->id}}"><code>Weiterlesen</code></a></p>
                                                  
                                                            
                                                    
                                                    </div>
                                               
                                                </div>
                                            </li>
                                            <div class="modal fade text-left show" id="x{{$com->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel6" style="display: none;" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel1">  <div class="avatar">
                                                        <img src="{{ asset('images/employee/'.$com->userimage) }}" style="height: 40px;" alt="avtar img holder">
                                                    </div>
                                                    {{ $com->username }}</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">×</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h5>{{ \Carbon\Carbon::parse($com->created_at)->diffForHumans() }}</h5>
                                                                <p>{!! $com->comment !!}</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal">OK</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </ul>
                                    </div>
        </div>
    </div>
    <!-- END: Content-->
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
   
    $('#responsible').select2();

    
});
</script>


<script>
   
    $(document).ready(function(){
        @if(Session::has('update_msg'))
        toastr.warning("{{ session('update_msg') }}");
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