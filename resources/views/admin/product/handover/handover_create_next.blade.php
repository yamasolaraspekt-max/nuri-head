@extends('admin.layouts.app')

@section('title')Übergabegegenstand @endsection
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
                                                   
                                                        <hr>
                                                        <div class="col-12">
                                                            <div class="form-group row">
                                                                <div class="col-md-8">
                                                                    <span><h3>Artikel im Übergabepaket</h3></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <table class="table" id="brand_table">
                                                            <thead>
                                                                <tr style=" background: #8fc73e;   color: white;  ">
                                                                    <th style="border: 1px; border-style: solid;">#</th>
                                                                    <th style="border: 1px; border-style: solid;width: 20px;">Vorgansnummer</th>
                                                                    <th style="border: 1px; border-style: solid;">Artikel</th>
                                                                    <th style="border: 1px; border-style: solid;">Menge</th>
                                                                    <th style="border: 1px; border-style: solid;">Notiz</th>
                                                                </tr>
                                                               
                                                            </thead>
                                                        <tbody>
                                                                @foreach ($data as $item)
                                                                    
                                                          
                                                                <tr>
                                                                    <td style="border: 1px; border-style: solid;"> {{ $item->id}}</td>
                                                                    <td style="border: 1px; border-style: solid;"> {{ $item->handover_id}}</td>
                                                                    <td style="border: 1px; border-style: solid;">{{ $item->item}} - {{ $item->description }}</td>   
                                                                    <td style="border: 1px; border-style: solid;">{{ $item->quantity}}</td>   
                                                                    <td style="border: 1px; border-style: solid;">{{ $item->remark}}</td>   
                                                                
                                                                </tr>
                                                                @endforeach
                                                                
                                                            </tbody>
                                                    </table>
                                                </div>

                                                <hr>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-8">
                                                            <span><h3>Verantwortungsbewusste Menschen</h3></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <form method="post" action="{{ action('App\Http\Controllers\HandoverToController@store') }}">
                                                    @csrf
                                               
                                                    <div class="col-12">
                                                        <div class="form-group row">

                                                            <div class="col-2">
                                                                <span>Vorgansnummer</span>
                                                                <input type="text" class="form-control" disabled placeholder="Vorgansnummer" value="{{ request()->id }}" >
                                                                <input type="hidden"  name="handover_id"  value="{{ request()->id }}" >
                                                              
                                                            </div>
                                                            <div class="col-2">
                                                                <span>Übergabe durch</span>
                                                                <select id='handover_from' name="handover_from" style="width:100%" required>
                                                                    <option  value="">Nicht auswählen</option>
                                                                    @foreach ($employee as $hand_from)
                                                                    <option value="{{ $hand_from->id }}" data-image="{{ asset('images/employee/'.$hand_from->image )}}"> {{ $hand_from->name }} {{ $hand_from->lastname }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-2">
                                                                <span>Weiter Übergabe</span>
                                                                <select id='handover_to' name="handover_to" style="width:100%" required>
                                                                    <option  value=""  >Nicht auswählen</option>
                                                                    @foreach ($employee as $hand_to)
                                                                    <option value="{{ $hand_to->id }}" data-image="{{ asset('images/employee/'.$hand_to->image )}}"> {{ $hand_to->name }} {{ $hand_to->lastname }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-2">
                                                                <span>Übernahme </span>
                                                                <select id='handover_by' name="handover_by" style="width:100%" required>
                                                                    <option value="" >Nicht auswählen</option>
                                                                    @foreach ($employee as $hand_by)
                                                                    <option value="{{ $hand_by->id }}" data-image="{{ asset('images/employee/'.$hand_by->image )}}"> {{ $hand_by->name }} {{ $hand_by->lastname }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <span>Übergabedatum</span>
                                                                <input type="date" id="handover_date" class="form-control" value="{{old('product')}}" name="handover_date" required>
                                                            </div>
                                                           
                                                        </div>

                                                           

                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-4">
                                                                <span>Zweck </span>
                                                            </div>
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <fieldset>
                                                            <div id="editor" class="form-control"  style="height: 400px !important;">
                                                            
                                                            </div>
                                                            <textarea name="editor_text" hidden id="editor_text"  style="text-align:right !important;"cols="30" rows="10"></textarea>
                
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary"> <i class="fa fa-save"></i> Einreichen</button>
                                                            </div>
                                                    </fieldset>
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
        $('#handover_from').select2({
    templateResult: formatOption,
    templateSelection: formatOption
});

function formatOption(option) {
    if (!option.id) {
        return option.text;
    }

    var $option = $('<span><img src="' + $(option.element).data('image') + '" class="img-flag" /> ' + option.text + '</span>');
    return $option;
}
    </script>

          
  
<script>
    $('#handover_to').select2({
templateResult: formatOption,
templateSelection: formatOption
});

function formatOption(option) {
if (!option.id) {
    return option.text;
}

var $option = $('<span><img src="' + $(option.element).data('image') + '" class="img-flag" /> ' + option.text + '</span>');
return $option;
}
</script>


<script>
    $('#handover_by').select2({
templateResult: formatOption,
templateSelection: formatOption
});

function formatOption(option) {
if (!option.id) {
    return option.text;
}

var $option = $('<span><img src="' + $(option.element).data('image') + '" class="img-flag" /> ' + option.text + '</span>');
return $option;
}
</script>



<script>

    $(document).ready(function(){
        $('#quantity').on('blur',function(){
            var quantity = parseInt($('#quantity').val());
            var available = parseInt($('#available').val());

        if(quantity > available){
           alert("Die Menge ist höher als die, die wir im Lager haben");
           $('#dialog').show()
            document.getElementById("dialog_text").innerHTML = "Die Menge ist höher als die, die wir im Lager haben";
            
            $('#quantity').focus();

        }
        else{
            $('#dialog').hide()
        }
         console.log('quantity '+ quantity + ' availble ' + available);
        })
    })

</script>



@endsection






