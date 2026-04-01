@extends('admin.layouts.app')

@section('title')Set Mitarbeiter @endsection
@section('style')
<!-- Include stylesheet -->

<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
@endsection

<style>
    .img-flag{
        width : 20px !important;
    } 
</style>
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
                            <h2 class="content-header-title float-left mb-0">Mitarbeiter</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('sets/'.request()->master.'/'.request()->phase) }}"> Set Artikle</a>
                                    </li>

                                    <li class="breadcrumb-item"><a href="{{ url('add_employee_set/'.request()->master.'/'.request()->phase) }}"> Set Mitarbeiter</a>
                                    </li> 
                                     <li class="breadcrumb-item"><a href=""> Mitarbeiter zum Set hinzufügen</a>
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
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-content">  
                                    <div class="card-body">
                                        <div class="alert alert-danger" role="alert" id="dialog" style="display: none;">
                                            <h4 class="alert-heading">INFORMATION</h4>
                                            <p class="mb-0" id="dialog_text">
                                                
                                            </p>
                                        </div>
                                          
                                        <div class="form-body">
                                                <div class="row">
                                                <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-8">
                                                                <span><h3>Set Mitarbeiter</h3></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                          
                                            <div class="col-12">
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
                                                    <form novalidate action="{{ route('add.employee.set.create',['master'=>request()->master, 'phase'=>request()->phase]) }}" method="get" >
                                                     @csrf
                                                    <div class="table-responsive">
                                                    @if(DB::table('user_rolls')
                                                                ->where('user_rolls.user_id', '=', auth()->user()->name)
                                                                ->where('user_rolls.item_id', '=', 'Product')
                                                                ->where('user_rolls.is_add', '=', 'on')
                                                                ->first())
                                                 
                                                                <div class="input-group">
                                                                    <select class="select2"  name="search" id="item" style="" aria-describedby="button-addon2">
                                                                        @foreach ($position as $pro)
                                                                            <option {{ $pro->id }}>{{ $pro->position }}</option>
                                                                        @endforeach
                                                                        
                                                                        </select>
                                                                    <div class="input-group-append" id="button-addon2">
                                                                        <button class="btn btn-primary waves-effect waves-light" type="submit"><i class="feather icon-search"></i> Suchen</button>
                                                                    </div>
                                                                </div>
                                                    
                                                            </form>
                                                        </table>
                                                        @endif
                                                        <hr>
                                                        <div class="col-12">
                                                            <div class="form-group row">
                                                                <div class="col-md-8">
                                                                    <span><h3>Ergebnisprodukt</h3></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <table class="table" id="">
                                                            <thead>
                                                                <tr style=" background: #8fc73e;   color: white;  " class="mb-2">
                                                                    <th style="">#</th>
                                                                    <th style="">Positionen</th>
                                                                    <th style="">Lohn</th>
                                                                    <th style="">Gewerk</th>
                                                                    <th style="">Beratung</th>
                                                                    <th style="">Planung</th>
                                                                    <th style="">Kalkulation</th>
                                                                    <th style="">Montage</th>
                                                                    <th style="">Projektierung</th>
                                                                    <th style="">Bauleitung</th>
                                                                    <th style="">Action</th>
                                                                </tr>
                                                            </thead>
                                                        <tbody>
                                                                @foreach ($skills as $skil)
                                                                <tr> 
                                                                    <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;">{{ $skil->id }}</td>
                                                                    <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;">{{ $skil->position }}</td>
                                                                    <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;">{{ number_format($skil->salary_per_hour, 2, ',', '.') }} €</td>
                                                                    <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;">{{ $skil->article_group }}</td>
                                                                    <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;">
                                                                        <div class="fonticon-wrap">
                                                                        @for ($i = 1; $i <=  $skil->advice; $i++)
                                                                        <i class="fa fa-star" style="color:gold"></i>
                                                                        @endfor
                                                                        </div>
                                                                    </td> 
                                                                    <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;">
                                                                        <div class="fonticon-wrap">
                                                                        @for ($i = 1; $i <=  $skil->plan; $i++)
                                                                        <i class="fa fa-star" style="color:gold"></i>
                                                                        @endfor
                                                                        </div>
                                                                    </td>

                                                                    <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;">
                                                                        <div class="fonticon-wrap">
                                                                        @for ($i = 1; $i <= $skil->calculation; $i++)
                                                                        <i class="fa fa-star" style="color:gold"></i>
                                                                        @endfor
                                                                        </div> 
                                                                    </td>

                                                                    <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;">
                                                                        <div class="fonticon-wrap">
                                                                        @for ($i = 1; $i <= $skil->montage; $i++)
                                                                        <i class="fa fa-star" style="color:gold"></i>
                                                                        @endfor
                                                                        </div>
                                                                    </td>

                                                                    <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;">
                                                                        <div class="fonticon-wrap">
                                                                        @for ($i = 1; $i <= $skil->project_planing; $i++)
                                                                        <i class="fa fa-star" style="color:gold"></i>
                                                                        @endfor
                                                                        </div>
                                                                    </td>
                                                                    <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;">
                                                                    <div class="fonticon-wrap">
                                                                        @for ($i = 1; $i <= $skil->site_management; $i++)
                                                                        <i class="fa fa-star" style="color:gold"></i>
                                                                        @endfor
                                                                        </div>
                                                                    </td> 
                                                                <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;">
                                                                    <button type="button" class="btn btn-outline-primary mr-1 mb-1 waves-effect waves-light"  data-toggle="modal" data-target="#add-pro{{$skil->id}}"><i class="feather icon-plus"></i>  Position zum Satz hinzufügen
                                                                    </button>
                                                                 
                    
                                                                    <!-- Modal -->
                                                                    <div class="modal fade text-left" id="add-pro{{$skil->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    {{ $skil->position }} - {{ $skil->article_group }}
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\EmployeeSetController@store')}}" class="custom-file-upload" enctype="multipart/form-data">
                                                                                    @csrf
                                                                                <div class="modal-body">                                       
                                                                                    <h5>Position zum Satz hinzufügen
                                                                                    </h5>
                                                                                   
                                                                                    <div class="col-md-12">
                                                                                        <div class="form-group">
                                                                                            <label for="Title">
                                                                                            Positionen
                                                                                            </label>
                                                                                            <input type="hidden" name="master_set_id" value="">
                                                                                            <input type="hidden" name="product_id" value="">
                                                                                            <input type="hidden" name="position_id" value="">
                                                                                            <input type="hidden" name="grade" value="">
                                                                                            <input type="hidden" name="phase_id" value="">
                                                                                            <input type="hidden" name="sale_price" value="">
                                                                                            <select name="" id="">
                                                                                                @foreach ($options as $option)
                                                                                                    <option value="{{$option->id}}"> {{ $option->position }}</option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>

                                                                                       <div class="col-md-12">
                                                                                        <div class="form-group">
                                                                                            <label for="Title">
                                                                                            Lohn
                                                                                            </label> 
                                                                                            <input type="text" class="form-control"  value="{{ $skil->salary_per_hour }}" disabled>
                                                                                        </div>
                                                                                    </div>
                                                                                      <div class="col-md-12">
                                                                                            <div class="form-group">
                                                                                                <label for="Title">Aufgaben</label>
                                                                                                <select name="activity_id[]" id="activity" class="form-control activity" multiple="true" style="width:100%">
                                                                                                    @foreach ($activity as $active) 
                                                                                                        <option value="{{ $active->id }}"> {{ $active->title }}: {{ $active->description }}</option> 
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>

                                                                                    <div class="col-md-12">
                                                                                        <div class="form-group">
                                                                                            <label for="Title">
                                                                                                Arbeitsstunde
                                                                                            </label>
                                                                                            <input type="text" class="form-control" name="work_hour">
                                                                                            @if ($errors->has('work_hour'))<p style="color:red;">{!!$errors->first('work_hour')!!}</p>@endif
                                                                                        </div>
                                                                                    </div>
                                                                                
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                  <button type="submit" class="btn btn-primary">Einreichen</button>
                                                                                </div>
                                                                                </form>
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
                        
                            
                  
                </section>
                <!-- // Basic Horizontal form layout section end -->

               

            </div>
        </div>
    </div>
    <!-- END: Content-->


@endsection

@section('script')

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


<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
  <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>

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
        // Initialize Select2 for the activity select box
        $('.activity').select2({
            placeholder: "Select Activities", // Add a placeholder
            allowClear: true // Allow the user to clear the selection
        });
    });
</script>
 
    

@endsection






