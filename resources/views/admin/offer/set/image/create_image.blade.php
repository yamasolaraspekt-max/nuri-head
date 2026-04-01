@extends('admin.layouts.app')

@section('title')Set Image @endsection
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
                                                                <span><h3>Set Foto</h3></span>
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
                                                    <form novalidate action="{{ action('App\Http\Controllers\AddImageToSetController@create', request()->master)}}" method="get" >
                                                     @csrf
                                                    <div class="table-responsive">
                                                    @if(DB::table('user_rolls')
                                                                ->where('user_rolls.user_id', '=', auth()->user()->name)
                                                                ->where('user_rolls.item_id', '=', 'Product')
                                                                ->where('user_rolls.is_add', '=', 'on')
                                                                ->first())
                                                 
                                                                <div class="input-group">
                                                                    <select class="select2"  name="search" id="item" style="" aria-describedby="button-addon2">
                                                                        @foreach ($products as $pro)
                                                                            <option {{ $pro->id }}>{{ $pro->product }}</option>
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
                                                        <table class="table" id="brand_table">
                                                            <thead>
                                                                <tr style=" background: #8fc73e;   color: white;  ">
                                                                    <th style="border: 1px; border-style: solid; ">#</th>
                                                                    <th style="border: 1px; border-style: solid; ">Artikel#</th>
                                                                    <th style="border: 1px; border-style: solid; ">Product</th> 
                                                                    <th style="border: 1px; border-style: solid; ;">Image Name</th>
                                                                    <th style="border: 1px; border-style: solid; ;">Image</th>
                                                                    <th style="border: 1px; border-style: solid;">Action</th>
                                                                </tr>
                                                               
                                                            </thead>
                                                        <tbody>
                                                                @foreach ($data as $item)
                                                                <tr>
                                                                    <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;"> {{ $item->id}}</td>
                                                                    <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;"> {{ $item->article_no}}</td>
                                                                    <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;" > {{ $item->product}}</td>
                                                                    <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;">{{ $item->name}}</td>   
                                                                    <td>
                                                                        <div class="avatar mr-1 avatar-xl">
                                                                            <img src="{{ asset('images/products/'.$item->image)}}" alt="{{$item->name}}">
                                                                        </div>
                                                                    </td>   
                                                                <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;">
                                                                    <button type="button" class="btn btn-outline-primary mr-1 mb-1 waves-effect waves-light"  data-toggle="modal" data-target="#add-product{{$item->id}}{{ $item->image_id}}"><i class="feather icon-plus"></i>  Foto zum Set hinzufügen</button>
                                                                 
                    
                                                                    <!-- Modal -->
                                                                    <div class="modal fade text-left" id="add-product{{$item->id}}{{$item->image_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\AddImageToSetController@store')}}" class="custom-file-upload" enctype="multipart/form-data">
                                                                                    @csrf
                                                                                <div class="modal-body">                                       
                                                                                    <h5>Produkt zum Set hinzufügen</h5>
                                                                                    <div class="col-md-12">
                                                                                        <div class="form-group">
                                                                                            <label for="Title">
                                                                                       Product
                                                                                            </label>
                                                                                            <input type="hidden" name="master_set_id" value="{{ request()->master }}">
                                                                                            <input type="hidden" name="product_id" value="{{ $item->id }}">
                                                                                            <input type="hidden" name="image" value="{{ $item->image }}">
                                                                                            <input type="hidden" name="name" value="{{ $item->name }}">
                                                                                            <input type="text" class="form-control"  name="product" value="{{ $item->product }}"  required>
                                                                                         
                                                                                            @if ($errors->has('product'))<p style="color:red;">{!!$errors->first('product')!!}</p>@endif
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-12">
                                                                                        <div class="form-group">
                                                                                            <label for="Title">
                                                                                       Image
                                                                                            </label>
                                                                                            <div class="avatar mr-1 avatar-xl">
                                                                                                <img src="{{ asset('images/products/'.$item->image)}}" alt="{{$item->name}}">
                                                                                            </div>
                                                                                         
                                                                                            @if ($errors->has('product'))<p style="color:red;">{!!$errors->first('product')!!}</p>@endif
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






