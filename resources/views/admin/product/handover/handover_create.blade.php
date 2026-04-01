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
                                                <div class="row">
                                                <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-8">
                                                                <span><h3>Übergabegegenstand</h3></span>
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
                                                    <form novalidate action="{{ action('App\Http\Controllers\HandoverController@store')}}" method="post" >
                                                     @csrf
                                                    <div class="table-responsive">
                                                    @if(DB::table('user_rolls')
                                                                ->where('user_rolls.user_id', '=', auth()->user()->name)
                                                                ->where('user_rolls.item_id', '=', 'Employee')
                                                                ->where('user_rolls.is_add', '=', 'on')
                                                                ->first())
                                                        <table class="table" id="asset">
                                                        
                                                            <thead>
                                                                <tr>
                                                                    <th>Vorgansnummer</th>
                                                                    <th>Artikel</th>
                                                                    <th>Menge</th>
                                                                    <th>Notiz</th>
                                                                </tr>
                                                            </thead>
                                                         
                                                            <tbody>
                                                                <tr>
                                                                    <td style='width:5%'>
                                                                        <input type="hidden" class="form-control required"  placeholder="test" name="old_handover_id" id="old_handover_id" value="{{ session('handoverID') }}" >

                                                                        <input type="text" class="form-control required"  placeholder="Vorgangsnummer"  name="handover_id" id="handover_id" required  value="{{ session('handoverID') }}">
                                                                    
                                                                    </td>
                                                                    <td>
                                                                        <select class="select2"  name="item_id" id="item" style="width:100%">
                                                                            @foreach ($asset as $as)
                                                                            <option value="{{ $as->id }}" @if($as->id==old('item_id')) selected @endif> {{ $as->serial_no }} {{ $as->item }} - Menge: {{ $as->quantity }}</option>
                                                                        
                                                                            @endforeach
                                                                           
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control required" placeholder="Menge" value="{{ old('quantity') }}" name="quantity">
                                                                      
                                                                    </td>
                                                    
                                                                     <td>
                                                                        <textarea class="form-control required" placeholder="Notiz..." value="{{ old('remark') }}" name="remark"></textarea>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                    
                                                                    <div class="col-8">
                                                                    <div class="input-group">
                                                                            <button type="submit" class="btn btn-outline-success mr-1 mb-1" id="submit"><i class="feather icon-save"></i> Datensatz speichern</button>
                                                                            <button type="button" class="btn btn-outline-success mr-1 mb-1" onclick="clearSession()"><i class="fa fa-refresh "></i> Clear Cart</button>
                                                                           
                                                                        </div>
                                                                        
                                                                  </div>
                                                            </form>
                                                        </table>
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
                                                                    <th style="border: 1px; border-style: solid;">Vorgansnummer</th>
                                                                    <th style="border: 1px; border-style: solid;">Artikel</th>
                                                                    <th style="border: 1px; border-style: solid;">Menge</th>
                                                                    <th style="border: 1px; border-style: solid;">Notiz</th>
                                                                    <th style="border: 1px; border-style: solid;">Action</th>
                                                                </tr>
                                                               
                                                            </thead>
                                                        <tbody>
                                                                @foreach ($data as $item)
                                                                    
                                                          
                                                                <tr>
                                                                    <td style="border: 1px; border-style: solid;"> {{ $item->id}}</td>
                                                                    <td style="border: 1px; border-style: solid;" > {{ $item->handover_id}}</td>
                                                                    <td style="border: 1px; border-style: solid;">{{ $item->item}}</td>   
                                                                    <td style="border: 1px; border-style: solid;">{{ $item->quantity}}</td>   
                                                                    <td style="border: 1px; border-style: solid;">{{ $item->remark}}</td>   
                                                                <td style="border: 1px; border-style: solid;"> <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" data-toggle="modal" data-target="#delete-pro{{$item->id}}">
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
                                                                                    <h5>Datensatz löschen</h5>
                                                                                    <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                                    <p>Die Datensatznummer lautet: {{$item->id}} </p>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                  <a type="button" href="{{url('/handover_destroy').'/'.$item->id}}" class="btn btn-primary">Ja</a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- End Delete Modal -->
                                                                
                                                                <a type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1"  data-toggle="modal" data-target="#edit{{$item->id}}"><i class="feather icon-edit"></i></a>
                                                                  <!-- Modal -->
                                                                  <div class="modal fade text-left" id="edit{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                                <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            {{ $item->item }} | Bearbeiten
                                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                <span aria-hidden="true">&times;</span>
                                                                                            </button>
                                                                                        </div>
                                                                                        <form method="post" action="{{ action('App\Http\Controllers\HandoverController@update')}}">
                                                                                            @csrf
                                                                                        <div class="modal-body" style="text-align: left;">
                                                                                            <input type="hidden" name="id" value="{{ $item->id}}">
                                                                                            <input type="hidden" name="item_id" value="{{ $item->item_id}}">
                                                                                            <input type="hidden" name="current_quantity" value="{{ $item->quantity}}">
                                                                                            <table class="responsible" >
                                                                                                <tr>
                                                                                                    <label>Artikel</label>
                                                                                                    <input type="text" disabled class="form-control" name="item" value="{{ $item->item}}">
                                                                                                    </tr>  
                                                                                                    <tr>
                                                                                                        <label>Notiz</label>
                                                                                                        <textarea class="form-control" name="remark">{{ $item->remark}}</textarea>
                                                                                                    </tr>  
                                                                                            </table>
                                                                                            <hr>
                                                                                            
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                            <input type="submit" class="btn btn-primary">
                                                                                        </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!-- End Image Modal -->
                                                            </td>
                                                                </tr>
                                                                @endforeach
                                                                
                                                            </tbody>
                                                    </table>
                                                </div>

                                            </div>
                                            @if(Session()->has('handoverID'))
                                            <a type="button" class="btn btn-primary" href="{{ url('/handover_next/'.Session('handoverID')) }}">Next</a>
                                            @endif
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

<script>
    $(document).ready(function(){
        $('#submit').on('mouseenter', function(){
            var handover_id = document.getElementById('handover_id').value;
            var old_handover_id = document.getElementById('old_handover_id').value;
            
            
            if(old_handover_id!="" && handover_id != old_handover_id){
                alert("Die Übergabe-ID des Artikels sollte identisch sein");
           $('#dialog').show()
            document.getElementById("dialog_text").innerHTML = "Die Übergabe-ID des Artikels sollte identisch sein ("+old_handover_id+")";
            
            $('#handover_id').focus();

            }
        });
    });
</script>

<script>
    function clearSession() {
        // Clear the session storage or local storage, depending on your use case
        sessionStorage.clear(); // To clear session storage
        // localStorage.clear(); // To clear local storage
    }
    </script>
@endsection






