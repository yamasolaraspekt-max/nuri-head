@extends('admin.layouts.app')
@section('title') FEEDBACK @stop

@section('style')

    <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}"> 
<link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" /> 
    <meta name="csrf-token" content="{{ csrf_token() }}">  
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

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
                        <h2 class="content-header-title float-left mb-0">SYSTEM FEEDBACK</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">HOME</a>
                                </li> 
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            </div>
                          
            <div class="content-body">

             <section>
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-start pb-0">
                                    <div>
                                        @php
                                           
                                            $files = DB::table('feedback')->get();
                                            $total = $files->count();
                                            $complete = $files->where('status', 'fixed')->count();
                                            $progress = $files->where('status', 'progress')->count();
                                            $remained = $files->whereNull('status')->count();
                                        @endphp
                                        <h2 class="text-bold-700 mb-0">{{$total}}</h2>
                                        <p>Gesamt</p>
                                    </div>
                                    <div class="avatar bg-rgba-primary p-50 m-0">
                                        <div class="avatar-content">
                                            <i class="feather icon-cpu text-primary font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-start pb-0">
                                    <div>
                                        <h2 class="text-bold-700 mb-0">{{ $complete }}</h2>
                                        <p>Abgeschlossen</p>
                                    </div>
                                    <div class="avatar bg-rgba-success p-50 m-0">
                                        <div class="avatar-content">
                                            <i class="feather icon-server text-success font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                 
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-start pb-0">
                                    <div>
                                        <h2 class="text-bold-700 mb-0">{{$progress}}</h2>
                                        <p>In Bearbeitung</p>
                                    </div>
                                    <div class="avatar bg-rgba-warning p-50 m-0">
                                        <div class="avatar-content">
                                            <i class="feather icon-alert-octagon text-warning font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                               <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-start pb-0">
                                    <div>
                                        <h2 class="text-bold-700 mb-0">{{$remained}}</h2>
                                        <p>Reste</p>
                                    </div>
                                    <div class="avatar bg-rgba-danger p-50 m-0">
                                        <div class="avatar-content">
                                            <i class="feather icon-activity text-danger font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
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
                    <div class="col-md-6 col-12 mb-1">
                        <button type="button" class="btn btn-outline-warning waves-effect waves-light" data-toggle="modal" data-target="#large">
                            NEUE IDEE
                        </button>
                        <div class="modal fade text-left" id="large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="myModalLabel17">FEEDBACK</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div> 
                                     <form action="{{ route('system.feedback.save') }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                        <div class="modal-body"> 
                                                    <div>
                                                        <input type="hidden" name="employee_id" value="{{ auth()->user()->name }}">
                                                        <label for="employee_id">EFEEDBACK VON</label>
                                                        <select name="employee_id" id="employee_id" class="form-control" style="width:100%;" disabled>
                                                            @foreach ($employees as $employee)
                                                                <option value="{{ $employee->id }}" data-image="{{ asset('images/employee/'.$employee->image) }}"
                                                                @if($employee->id == auth()->user()->name) selected @endif > {{$employee->name}} {{$employee->lastname }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label for="title">TITEL:</label>
                                                        <input type="text" name="title" id="title"  class="form-control"required>
                                                    </div>
                                                    <div>
                                                        <label for="description">IDEENBESCHREIBUNG:</label> 
                                                       <div id="editor-large" class="form-control editor" style="height: 400px !important;">
                                                        </div>
                                                        <textarea name="editor_text" hidden class="editor_text_large" cols="30" rows="10"></textarea>
                
                                                    </div>
                                                    
                                                    
                                                
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary waves-effect waves-light" >Speichern</button>
                                            <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Absagen</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card"> 
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
                                                            
                                                            <div class="table-responsive"> 
                                                                <table class="table" id="brand_table">
                                                                    <thead>
                                                                        <tr>
                                                                             <th>#</th>
                                                                             <th>TITEL</th>
                                                                            <th>BESCHREIBUNG</th>
                                                                            <th>MITARBEITER</th>
                                                                            <th>ERSTELLT AM</th>
                                                                            <th>ANTWORT</th>
                                                                            <th>STATUS</th>
                                                                        </tr>
                                                                       
                                                                    </thead>
                                                                <tbody>
                                                                        @foreach ($data as $item) 
                                                                  
                                                                        <tr>
                                                                            <td>{{ $item->ticket_no}}</td>
                                                                            <td>{{ $item->title}}</td>   
                                                                            <td> 
                                                                                <button type="button" class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1 waves-effect waves-light" data-toggle="modal" data-target="#discription{{$item->id}}">
                                                                               <i class="feather icon-file"></i>
                                                                             </button>
                                                                                <div class="modal fade text-left" id="discription{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true"> 
                                                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                                                                                        <div class="modal-content">
                                                                                            <div class="modal-header">
                                                                                                <h5 class="modal-title" id="dropzoneModalLabel">UBESCHREIBUNG: {{$item->ticket_no}}</h5> 
                                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                    <span aria-hidden="true">×</span>
                                                                                                </button>
                                                                                            </div>
                                                                                            <div class="modal-body">
                                                                                                {!! $item->description !!}
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> 
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>   
                                                                            <td>
                                                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="{{ $item->name }} {{ $item->lastname}}" class="avatar pull-up">
                                                                                    <img class="media-object rounded-circle" src="{{asset('images/employee/'.$item->image)}}" alt="Avatar" height="30" width="30">
                                                                                </li>
                                                                            </td>  
                                                                            <td>{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans()}}</td>   

                                                                        <td>
                                                                             
                                                                            @if($item->response) 
                                                                             <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1 waves-effect waves-light" data-toggle="modal" data-target="#answer{{$item->id}}"><i class="feather icon-flag"></i></button>
                                                                            @else
                                                                            <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1 waves-effect waves-light"><i class="feather icon-alert-circle"></i></button>
                                                                            @endif

                                                                               <div class="modal fade text-left" id="answer{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true">  
                                                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                                                                                        <div class="modal-content">
                                                                                            <div class="modal-header">
                                                                                                <h4 class="modal-title" id="myModalLabel17">Antwort</h4>
                                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                    <span aria-hidden="true">×</span>
                                                                                                </button>
                                                                                            </div>
                                                                                            <div class="modal-body">
                                                                                                <div class="row"> 
                                                                                                    @if($item->response)
                                                                                                   <p class="p-1"> {!! $item->response !!}</p>
                                                                                                    @else  
                                                                                                    <div class="col-md-12">
                                                                                                         
                                                                                                        <div class="alert alert-primary mb-2" role="alert">
                                                                                                             Warten auf Antwort...
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    @endif
                                                                                                </div> 
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal">OK</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                         </td>

                                                                         <td>
                                                                            @if($item->status== "fixed") 
                                                                            <div class="chip chip-primary mr-1">
                                                                                <div class="chip-body">
                                                                                    <span class="chip-text"> Aufgabe abgeschlossen</span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="chip chip-primary mr-1">
                                                                                <div class="chip-body">
                                                                                    <span class="chip-text"> {{ \Carbon\Carbon::parse($item->fixed_date)->isoFormat('DD.MM.YY') }} - ( {{ \Carbon\Carbon::parse($item->fixed_date)->diffForhumans() }}) </span>
                                                                                </div>
                                                                            </div>
                                                                            @elseif($item->status=="progress")
                                                                            <div class="chip chip-warning mr-1">
                                                                                <div class="chip-body">
                                                                                    <span class="chip-text"> im Prozess</span>
                                                                                </div>
                                                                            </div>
                                                                             <div class="chip chip-primary mr-1">
                                                                                <div class="chip-body">
                                                                                    <span class="chip-text"> {{ \Carbon\Carbon::parse($item->progress_date)->isoFormat('DD.MM.YY') }} - ( {{ \Carbon\Carbon::parse($item->progress_date)->diffForhumans() }}) </span>
                                                                                </div>
                                                                            </div>
                                                                            @else
                                                                             <div class="chip chip-danger mr-1">
                                                                                <div class="chip-body">
                                                                                    <span class="chip-text">Neue</span>
                                                                                </div>
                                                                            </div>
                                                                            @endif
                                                                         </td>

                                                                         <td>
                                                                            @if(DB::table('user_rolls')
                                                                                ->where('user_rolls.user_id', '=', auth()->user()->name)
                                                                                ->where('user_rolls.item_id', '=', 'Programmer')
                                                                                ->where('user_rolls.is_read', '=', 'on')
                                                                                ->first())
                                                                              <a type="button" class="btn btn-outline-warning waves-effect waves-light" href="{{ url('/feedback/fixed/'.$item->id)}}">Fixed</a>
                                                                              <a type="button" class="btn btn-outline-warning waves-effect waves-light " href="{{ url('/feedback/progress/'.$item->id)}}">Progress</a>
                                                                               <button type="button" class="btn btn-outline-warning waves-effect waves-light" data-toggle="modal" data-target="#response{{$item->id}}">
                                                                                   Response
                                                                                </button>

                                                                                <div class="modal fade text-left" id="response{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true" style="display: none;">
                                                                                    <div class="modal-dialog modal-lg" role="document">
                                                                                        <div class="modal-content">
                                                                                            <div class="modal-header">
                                                                                                <h4 class="modal-title" id="myModalLabel17">RESPONSE</h4>
                                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                    <span aria-hidden="true">×</span>
                                                                                                </button>
                                                                                            </div> 
                                                                                            <form action="{{ route('system.feedback.answer') }}" method="POST" enctype="multipart/form-data">
                                                                                                        @csrf
                                                                                                <div class="modal-body"> 
                                                                                                    <div>
                                                                                                        <input type="hidden" name="employee_id" value="{{ auth()->user()->name }}">
                                                                                                        <label for="employee_id">EFEEDBACK VON</label>
                                                                                                        <select name="employee_id" id="employee_id" class="form-control" style="width:100%;" disabled>
                                                                                                            @foreach ($employees as $employee)
                                                                                                                <option value="{{ $employee->id }}" data-image="{{ asset('images/employee/'.$employee->image) }}"
                                                                                                                @if($employee->id == auth()->user()->name) selected @endif > {{$employee->name}} {{$employee->lastname }}</option>
                                                                                                            @endforeach
                                                                                                        </select>
                                                                                                    </div>
                                                                                                  
                                                                                                        <input type="hidden" name="id"  value="{{$item->id}}" class="form-control" >
                                                                                         
                                                                                                    <div> 
                                                                                                       <label for="response_text_{{ $item->id }}">Antwort:</label>
                                                                                                    <div id="editor-answer-{{ $item->id }}" class="form-control editor" style="height: 300px;"></div>
                                                                                                    <textarea name="response_text" hidden class="editor_text_answer_{{ $item->id }}"></textarea>
                                                                
                                                                                                    </div>    
                                                                                                </div>
                                                                                                <div class="modal-footer">
                                                                                                    <button type="submit" class="btn btn-primary waves-effect waves-light" >Speichern</button>
                                                                                                    <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Absagen</button>
                                                                                                </div>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
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
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

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
    $(document).ready(function () {
    // Toolbar configuration
    var toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike'],
        ['blockquote', 'code-block'],
        [{ 'header': 1 }, { 'header': 2 }],
        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
        [{ 'script': 'sub' }, { 'script': 'super' }],
        [{ 'indent': '-1' }, { 'indent': '+1' }],
        [{ 'direction': 'rtl' }],
        [{ 'size': ['small', false, 'large', 'huge'] }],
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'font': [] }],
        [{ 'align': [] }],
        ['link', 'image', 'video', 'formula'],
        ['clean']
    ];

    // Dynamically initialize editors for feedback forms
    $('.editor').each(function () {
        let editorId = $(this).attr('id');
        let textareaClass = $(this).next('textarea').attr('class');

        // Initialize Quill only once
        if (!$(this).data('quill')) {
            var quill = new Quill(`#${editorId}`, {
                modules: { toolbar: toolbarOptions },
                theme: 'snow'
            });

            // Store the initialized Quill instance
            $(this).data('quill', quill);

            // Copy Quill content to the corresponding textarea before form submission
            $(this).closest('form').on('submit', function () {
                $(`.${textareaClass}`).val(quill.root.innerHTML);
            });
        }
    });
});

</script>

<script>
    $(document).ready(function() {
    function formatEmployee(option) {
        if (!option.id) {
            return option.text;
        }

        var imgSrc = $(option.element).data('image');
        if (imgSrc) {
            var $option = $(
                '<span><img src="' + imgSrc + '" class="img-circle" style="width: 30px; height: 30px; margin-right: 10px;" />' + option.text + '</span>'
            );
            return $option;
        } else {
            return option.text;
        }
    }

    // Initialize select2 with custom template
    $('#employee_id').select2({
        templateResult: formatEmployee,
        templateSelection: formatEmployee,
        placeholder: "Select an employee",
        allowClear: true, 
    });
});

</script>

<script>

    Dropzone.options.dropzoneForm = {
    paramName: "file", // The name of the file input field
    maxFilesize: 2, // MB
    acceptedFiles: "image/jpeg,image/png,image/jpg,image/gif",
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    init: function () {
        this.on("sending", function (file, xhr, formData) {
            formData.append("feedback_id", document.querySelector('input[name="feedback_id"]').value);
        });
    }
};

</script>
 
@endsection