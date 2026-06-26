@extends('admin.layouts.app')

@section('title') Kaufanfrage @endsection
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
                        <div class="col-md-6 col-12">
                            <div class="card">
                                <div class="card-content">
                                @if (count($errors) > 0)
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                     
                                    <div class="card-body">
                                        <form class="form form-horizontal" method="post" action="{{ action('App\Http\Controllers\PurchaseRequestController@store')}}"  class="custom-file-upload" enctype="multipart/form-data">
                                        @csrf    
                                        <div class="form-body">
                                                <div class="row">
                                                <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-8">
                                                                <span><h3>Kaufanfrage</h3></span>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Herstellername</span>
                                                            </div>
                                                            <div class="col-8">
                                                                <select id='brand' name="brand_id" style="width:100%" required>
                                                                    <option value="new" data-image="{{ asset('images/icons/new.png') }}">Neuer Hersteller</option>
                                                                    @foreach ($brand as $br)
                                                                    @if($br->status=="Published")
                                                                    <option value="{{ $br->id }}" data-image="{{ asset('images/brand/'.$br->image )}}"> {{ $br->name }}</option>
                                                                    @endif
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12" id="newM">
                                                        <div class="form-group row">
                                                            <div class="col-4">
                                                                <span>Neuer Hersteller</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text"  class="form-control" value="{{old('new_brand')}}" name="new_brand" >
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-4">
                                                                <span>Produkt Name</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="product" class="form-control" value="{{old('product')}}" name="product" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-4">
                                                                <span>Produktmodell</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="model" class="form-control" value="{{old('model')}}" name="model" required>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-4">
                                                                <span>Liefrant/Supplier</span>
                                                            </div>
                                                            <div class="col-8">
                                                                <fieldset class="form-group">
                                                                    
                                                                    @if(count($distributor))
                                                                    <select id='distributor' name="distributor" style="width:100%" required>
                                                                    <option value="new"  data-image="{{ asset('images/icons/new.png') }}"> Neuer Liefrant/Supplier</option>
                                                                    @foreach ($distributor as $dis)
                                                                    @if($dis->status=="Published")
                                                                    <option value="{{ $dis->id }}" data-image="{{ asset('images/distributor/'.$dis->image )}}"> {{ $dis->name }}</option>
                                                                    @endif
                                                                    @endforeach
                                                                    </select>

                                                                    @else
                                                                    <a type="button" class="btn btn-primary" href="{{ route('distributor.info')}}">Neu Liefrant/Supper</a>
                                                                    @endif
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12" id="newS" >
                                                        <div class="form-group row">
                                                            <div class="col-4">
                                                                <span>Neuer Liefrant/Supplier</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text"  class="form-control" value="{{old('new_distributor')}}" name="new_distributor" >
                                                            </div>
                                                        </div>
                                                    </div>
                                                  
                                          

                                                        <div class="col-12">
                                                            <div class="form-group row">
                                                                <div class="col-4">
                                                                    <span>Benennung</span>
                                                                </div>
                                                            <div class="col-8">
                                                                <fieldset class="form-group">
                                                                    <select class="select2-customize-result form-control" name="used" id="used" required>
                                                                        <option value="Kunden">Kunden</option>
                                                                        <option value="Mitarbeiter">Mitarbeiter</option>
                                                                        <option value="Problem">Problem/Ticket</option>
                                                                    </select>
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12" id="customer" >
                                                        <div class="form-group row">
                                                            <div class="col-4">
                                                                <span>Kunden</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <fieldset class="form-group">
                                                                    <select class="select2-customize-result form-control" name="customer_id" style="width:100%" id="customer_id" >
                                                                        <option value="nicht auswählen">Nicht auswählen</option>
                                                                        @foreach ($customer as $cus)
                                                                        <option value="{{ $cus->id }}">{{ $cus->name }} {{ $cus->lastname }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-12" id="employee"  style="display:none">
                                                        <div class="form-group row">
                                                            <div class="col-4">
                                                                <span>Mitarbeiter</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <fieldset class="form-group">
                                                                    <select class="select2-customize-result form-control" name="employee_id"  id="employee_id"  style="width:100%">
                                                                        <option value="nicht auswählen">Nicht auswählen</option>
                                                                        @foreach ($employee as $emp)
                                                                        <option value="{{ $emp->id }}">{{ $emp->name }} {{ $emp->lastname }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12" id="problem"  style="display:none">
                                                        <div class="form-group row">
                                                            <div class="col-4">
                                                                <span>Ticket</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <fieldset class="form-group">
                                                                    <select class="select2-customize-result form-control" name="problem_id"  id="problem_id"  style="width:100%">
                                                                        <option value="nicht auswählen">Nicht auswählen</option>
                                                                        @foreach ($problem as $prob)
                                                                        <option value="{{ $prob->ticket_no }}">{{ $prob->ticket_no }}|{{ $prob->name }}| {{ $prob->product }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                    </div>


                                           


                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-4">
                                                                <span>Farbe</span>
                                                            </div>
                                                        <div class="col-8">
                                                            <fieldset class="form-group">
                                                                <select class=" form-control" name="color" id="color" placeholder="Select Color" required>
                                                                    <option value="Schwarz">Schwarz</option>
                                                                    <option value="Grau">Grau</option>
                                                                    <option value="Braun">Braun</option>
                                                                    <option value="Beige">Beige</option>
                                                                    <option value="Gold">Gold</option>
                                                                    <option value="Blau">Blau</option>
                                                                    <option value="Gelb">Gelb</option>
                                                                    <option value="Lila">Lila</option>
                                                                    <option value="Silver">Silver</option>
                                                            
                                                                </select>
                                                            </fieldset>
                                                        </div>
                                                    </div>
                                                </div>
                                                  
                                                
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-4">
                                                                <span>Mengeneinheit:</span>
                                                            </div>
                                                            <div class="col-8">
                                                                
                                                                <fieldset class="form-group">
                                                                    @if(count($measures))
                                                                    <select id='measure_unit' name="measure_unit" style="width:100%" required>
                                                                    @foreach ($measures as $measure)
                                                                    <option value="{{ $measure->id }}" > {{ $measure->measure }}</option>
                                                                    @endforeach
                                                                    </select>
                                                                    @else
                                                                    <a type="button" class="btn btn-primary" href="{{ route('measure.info')}}">Neu Mengeneinheit</a>
                                                                    @endif
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-4">
                                                                <span>Preiseinheit</span>
                                                            </div>
                                                            <div class="col-8">
                                                                <input type="text" id="price_unit" class="form-control" value="{{old('price_unit')}}" name="price_unit" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="card" style=" height:420px !important;">
                                <div class="card-content">
                                    <div class="card-body" >  
                                        <div class="form-body">
                                                <div class="row">

                                                <div class="col-12">
                                                        <div class="form-group row">
                                                         
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Unverbindliche Preisempfehlung (UVP):</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="number" id="retail_price" class="form-control" value="{{old('retail_price')}}" name="retail_price" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Einzelhandelsrabatt:</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select class="select2-customize-result form-control" name="retail_discount_type" id="retail_drop" required>
                                                                    <option value="Percent">Percent</option>
                                                                    <option value="Euro">Euro</option>
                                                                    
                                                                </select>
                                                            </div>

                                                            <div class="col-md-2" id="percent_text" >
                                                                <input type="number" class="form-control required" placeholder="%" id="r_discount_p" value="{{ old('retail_discount') }}" name="retail_discount_p">
                                                            </div>

                                                            <div class="col-md-2" id="euro_text" style="display: none">
                                                                <input type="number" class="form-control required" placeholder="Euro" id="r_discount_e"  value="{{ old('retail_discount') }}" name="retail_discount_e">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Einkaufspreis:</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="purchase_price"  class="form-control" value="{{old('purchase_price')}}" name="purchase_price" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Zu kaufende Menge:</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="quantity"  class="form-control" value="{{old('quantity')}}" name="quantity" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-4">
                                                                <span>Anfrage von:</span>
                                                            </div>
                                                            <div class="col-8">
                                                                
                                                                <fieldset class="form-group">
                                                                    <select class="select2-customize-result form-control required" name="request_from"  id="request_from"  style="width:100%">
                                                                        @foreach ($employee as $emp)
                                                                        <option value="{{ $emp->id }}">{{ $emp->name }} {{ $emp->lastname }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-4">
                                                                <span>Anfrage zu</span>
                                                            </div>
                                                            <div class="col-8">
                                                                
                                                                <fieldset class="form-group">
                                                                    <select class="select2-customize-result form-control required" name="request_to"  id="request_to"  style="width:100%">
                                                                        @foreach ($employee as $emp)
                                                                        <option value="{{ $emp->id }}">{{ $emp->name }} {{ $emp->lastname }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Online-Link :</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="link"  class="form-control" value="{{old('link')}}" name="link" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Foto:</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="file" id="image"  class="form-control" value="{{old('image')}}" name="image" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-4">
                                                                <span>kurze Beschreibung </span>
                                                            </div>
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <fieldset>
                                                            <div id="editor" class="form-control"  style="height: 400px !important;">
                                                            
                                                            </div>
                                                            <textarea name="editor_text" hidden id="editor_text"  style="text-align:right !important;"cols="30" rows="10"></textarea>
                
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary"> <i class="fa fa-save"></i> Speichern und Weiter</button>
                                                            </div>
                                                    </fieldset>
                                                    </div>
                                                   
                                                   

                                                    
                                                </div>
                                            </div>

                                         
                                    </div>
                                </form>  
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
        $(document).ready(function() {
        var input1 = $('#article_no');
        var input2 = $('#invoice_no');
        
        // Add event listener to input1
        input1.on('change', function() {
            // Set the value of input2 to the value of input1
            input2.val(input1.val());
        });
        });
</script>

<script>
        $(document).ready(function() {
        
            $('#color').select2({
                placeholder: "Farbe auswählen",
                allowClear: true
            
            });

            $('#measure_unit').select2({
                placeholder: "Mangeneinheit auswählen",
                allowClear: true
            
            });
            $('#article_group').select2();
            $('#discount_group').select2();
            $('#employee_id').select2();
            $('#problem_id').select2();
            $('#customer_id').select2();
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
   
 

<script>
        $('#distributor').select2({
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
    $('#brand').select2({
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

{{-- Hide and Show of Advance Payment --}}
<script>
$('#discount_type').change(function(){
        if(this.value=="Euro"){
            $("#discount_e").show();
            $("#discount_p").hide();
        }else if (this.value=="Percent"){
            $("#discount_e").hide();
            $("#discount_p").show();
        } else {
            $("#discount_e").hide();
            $("#discount_p").hide();
        }
    })
</script>



<script>
    $('#payment').change(function(){
            if(this.value=="Vorous"){
                $("#payment_type").show();
            } 
            else if( this.value=="Normal"){
                var advance = document.getElementById('advance_e').value=0;
                $("#payment_type").hide();
                $("#advance_e").hide();
                 $("#advance_p").hide();

                 console.log('Advance is: ' + advance)
                }  
            
        })
    </script>

<script>
    $('#plus_percent').change(function(){
            if(this.value=="Euro"){
                $("#plus_e").show();
                $("#plus_p").hide();
            }else {
                $("#plus_e").hide();
                $("#plus_p").show();
            }
        })
    </script>
{{-- Hide and Show of Advance Payment --}}

{{-- Hide and Show of Retail Percent and Euro --}}
<script>
    // Use jQuery event handler
    $('#retail_drop').change(function(){
        // Corrected variable names and getElementById
        var retail_dropDown = document.getElementById('retail_drop');
        console.log('retial:' + retail_dropDown);
        if (retail_dropDown.value=="Euro") {
           $('#percent_text').hide();
           $('#euro_text').show();
          ;
        } else if(retail_dropDown.value=="Percent") {
            $('#percent_text').show();
           $('#euro_text').hide();
        }
    });
</script>

{{-- Hide and Show of Retail Percent and Euro --}}
  

{{-- Purchase Price Calculation By Percent--}}
<script>
    $( "#r_discount_p" ).change(function() {
        var retail_price = parseInt($('#retail_price').val());
        var retail_percent = parseInt($('#r_discount_p').val());
        var purchase = document.getElementById('purchase_price');

        var result = retail_price - retail_price / 100 * retail_percent;
        purchase.value = Math.round((result));

        console.log('Result:' + result );
    });
</script>
{{-- Purchase Price Calculation by Percent--}}

{{-- Sale Price Calculation --}}
<script>
   function sales() {
  var sale_price_input = document.getElementById('sale_price');
  var purchase_price = parseFloat(document.getElementById('purchase_price').value);
  var price = parseFloat(document.getElementById('purchase_price').value);
  var payment_method = document.getElementById('payment_method_type').value;
  var advance_p = parseFloat(document.getElementById('advance_price_percent').value);
  var advance_e = parseFloat(document.getElementById('advance_price_euro').value);

  var plus = document.getElementById('plus_percent').value
  var plus_p = parseFloat(document.getElementById('plus_price_percent').value);
  var plus_e = parseFloat(document.getElementById('plus_price_euro').value);

  if (payment_method == "Percent") {
    var advance_payment = price * advance_p / 100;
  } else if (payment_method == "Euro") {
    var advance_payment = advance_e;
  } else {
    var advance_payment = 0;
  }

  var plus_result;
  if (plus == "Percent") {
    plus_result = price * plus_p / 100;
  } else if (plus == "Euro") {
    plus_result = plus_e;
  } else {
    plus_result = 0;
  }

  var total = (purchase_price - advance_payment) + plus_result;
  sale_price_input.value = total;

  console.log('Result: ' + total + ' advance_payment: ' + advance_payment + ' plus Result: ' + plus_result);
}

</script>
{{-- Sale Price Calculation --}}


{{-- Purchase Price Calculation By Euro--}}
<script>
    $( "#r_discount_e" ).change(function() {

        var retail_price = parseInt($('#retail_price').val());
        var retail_euro = parseInt($('#r_discount_e').val());
        var purchase = document.getElementById('purchase_price');

        var result = retail_price - retail_euro;
        purchase.value = result;

        console.log('Result:' + result );
    });
</script>
{{-- Purchase Price Calculation by Euro--}}

<script>
    function calculate(){
        var price = parseFloat(document.getElementById('purchase_price').value);
        var sale_price = parseFloat(document.getElementById('sale_price').value);
        var payment_method =  document.getElementById('payment_method_type').value;
        var advance_p =  document.getElementById('advance_price_percent').value;
        var advance_e =  document.getElementById('advance_price_euro').value;

        var plus =  document.getElementById('plus_percent').value
        var plus_p =  parseFloat(document.getElementById('plus_price_percent').value);
        var plus_e =  parseFloat(document.getElementById('plus_price_euro').value);
        var tax = parseFloat(document.getElementById('tax').value);
        var discount_type = document.getElementById('discount_type').value;
        var discount_p = document.getElementById('discount_percent').value;
        var discount_e = document.getElementById('discount_euro').value;
        var total =  document.getElementById('total')

            var discount= 0;
            if( discount_type == "Percent"){
                 discount = sale_price/100 * discount_p;
            } else if( discount_type == "Euro"){
                discount = discount_e;
            }
            var tax_result = sale_price / 100 * tax;
            var total_price = sale_price + tax_result - discount ;
           
            var net_total = price - total_price;
            total.value=total_price;
            console.log('price ' + price + ' Discount: ' + discount + ' tax: '+tax_result +' Total Price: ' + total_price + ' Net Total :' + net_total);}


</script>

<script>
 $( "#sale_price" ).change(function() {
    tax_result();
 })

 $( "#plus_price_percent" ).change(function() {
    tax_result();
 })

 $( "#plus_price_euro" ).change(function() {
    tax_result();
 })

function tax_result() {
    var tax_result = document.getElementById('tax_result');
    var sale_price = parseFloat(document.getElementById('sale_price').value);
    var tax = parseInt(document.getElementById('tax').value);
    var result = sale_price / 100 * tax;

    if (isNaN(sale_price)) {
        tax_result.innerText = "Der Preis ist nicht definiert";
    } else {
        tax_result.innerText = result + ' Euro';
    }

    console.log('tax result is: ' + result + ' sale Price ' + sale_price);
}

</script>

<script>
    $(document).ready(function(){
        $('#brand').change(function(){
            var brand=document.getElementById('brand').value;
            if(brand == "new"){
                $('#newM').show();
            }else{
                $('#newM').hide();
            }
            console.log(brand);
        })
    });
</script>

<script>
    $(document).ready(function(){
        $('#distributor').change(function(){
            var brand=document.getElementById('distributor').value;
            if(brand == "new"){
                $('#newS').show();
            }else{
                $('#newS').hide();
            }
            console.log(brand);
        })
    });
</script>


<script>
    $(document).ready(function(){
        $('#used').change(function(){
            var used=document.getElementById('used').value;
            if(used == "Kunden"){
                $('#customer').show();
                $('#employee').hide();
                $('#problem').hide();
            }else if(used =="Mitarbeiter"){
                $('#customer').hide();
                $('#employee').show();
                $('#problem').hide();
            }
            else if(used == "Problem"){
                $('#customer').hide();
                $('#employee').hide();
                $('#problem').show();
            }
            console.log(used);
        })
    });
</script>


@endsection


