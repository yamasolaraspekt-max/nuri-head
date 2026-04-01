@extends('admin.layouts.app')
@section('title') Produkt Typ @stop
@section('content')

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
                          
            <div class="content-body">
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
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Produkt Typ</h4>
                            </div>
                           
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
                                                            <!-- Table with outer spacing -->
                                                            <form novalidate action="{{ action('App\Http\Controllers\ProductTypeController@store')}}" method="post">
                                                             @csrf
                                                            <div class="table-responsive">
                                                            @if(DB::table('user_rolls')
                                                                        ->where('user_rolls.user_id', '=', auth()->user()->id)
                                                                        ->where('user_rolls.item_id', '=', 'Product')
                                                                        ->where('user_rolls.is_add', '=', 'on')
                                                                        ->first())
                                                                <table class="table" id="add_department">
                                                                
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Unternehmen/Marke</th>
                                                                            <th>Lieferant/Supplier</th> 
                                                                            <th>Produkt</th> 
                                                                            <th>Article#</th>
                                                                            <th>Serial#</th>
                                                                            <th>EAN#</th>
                                                                        </tr>
                                                                    </thead>
                                                                    
                                                                    <tbody>
                                                                        <tr>
                                                                            <input type="hidden" name="product[0][product_id]" value="{{$brand->id}}">
                                                                            <td>
                                                                                <input type="text" class="form-control required" disabled value="{{$brand->name}}"> 
                                                                            </td>
                                                                            <td>
                                                                                <select class="select2-customize-result form-control" name="product[0][distributor_id]" id="distributor"  required>
                                                                                    <option value=""> A </option>
                                                                                    <option value=""> B </option>
                                                                                    <option value=""> C </option>
                                                                                  
                                                                                </select>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" class="form-control required" placeholder="Produkt-type" name="product[0][type]">
                                                                            </td>
                                                                            <td><input type="text" class="form-control required" placeholder="Articlenummer" name="product[0][article]"></td>
                                                            
                                                                            <td><input type="text" class="form-control required" placeholder="Serialnummer" name="product[0][serial]"></td>
                                                                        
                                                                            <td><input type="text" class="form-control required" placeholder="EAN-nummer" name="product[0][ean]"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <label for="distributor"><h7><strong>Einkauf Prize</strong></h7></label>
                                                                                <input type="number" class="form-control required"  id="purchase_price" placeholder="Einkauf Prize" name="product[0][purchase_price]">
                                                                            </td>
                                                                            <td>
                                                                                <label for="distributor"><h7><strong></strong></h7></label>
                                                                                <select class="select2-customize-result form-control" name="product[0][price_type]" id="price_type" required>
                                                                                    <option value="Fest"> Fest</option>
                                                                                    <option value="Verkauf"> Verkauf </option>
                                                                                    
                                                                                </select>
                                                                                <div id="plus" style="display: none">
                                                                                    <label for="distributor"><h7><strong>Plus (%)</strong></h7></label>
                                                                                    <input type="number" class="form-control required" placeholder="Plus (%)"  id="plus_price"  name="product[0][plus_price]">
                                                                                </div>
                                                                            </td>
                                                                            
                                                                            <td>
                                                                                <label for="distributor"><h7><strong>Zahlungsarten</strong></h7></label>
                                                                                <select class="select2-customize-result form-control" name="product[0][payment_method_id]" id="payment" required>
                                                                                    <option value="Normal">Normal</option>
                                                                                    <option value="Vorous">Vorous</option>
                                                                                    <option value="In 30 Tagen"> In 30 Tagen </option>
                                                                                    
                                                                                </select>
                                                                                <div id="advance"  style="display: none">
                                                                                    <label for="distributor"><h7><strong>Anzahlungswert</strong></h7></label>
                                                                                    <input type="text" id="advance" class="form-control required" placeholder="Anzahlungswert" name="product[0][payment_method_price]">
                                                                                </div>
                                                                            </td>
                                                                          
                                                                            <td>
                                                                                <label for="distributor"><h7><strong>Steuer</strong></h7></label>
                                                                                <select class="select2-customize-result form-control" name="product[0][tax]" id="tax"  required>
                                                                                    <option value="19">19%</option>
                                                                                    <option value="16">16%</option>
                                                                                    <option value="9">9%</option>
                                                                                    <option value="0">0</option>
                                                                                  
                                                                                </select>
                                                                            </td>
                                                                             <td>
                                                                            <label for="distributor"><h7><strong>Rabbat (%)</strong></h7></label>
                                                                            <input type="text" class="form-control required" placeholder="Rabatt in Prozent" name="product[0][discount]" id="discount">
                                                                            </td>

                                                                            <td>
                                                                                <label for="distributor"><h7><strong>Netto</strong></h7></label>
                                                                                <input type="text" id="netto" class="form-control required" placeholder="Netto Prize" name="product[0][total]" id="total">
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                            
                                                                            <div class="col-8">
                                                                            <div class="input-group">
                                                                                <a type="button" href="{{ url('/brand')}}"class="btn btn-outline-warning mr-1 mb-1"><i class="feather icon-chevrons-left"></i> Zurück</a>
                                                                                    <button type="submit" class="btn btn-outline-success mr-1 mb-1"><i class="feather icon-save"></i> Datensatz speichern</button>
                                                                                    <button type="button" class="btn btn-outline-warning mr-1 mb-1" id="add_brand"><i class="feather icon-plus"></i> Add Record</button>
                                                                                </div>
                                                                                
                                                                          </div>
                                                                    </form>
                                                                </table>
                                                                @endif
                                                                <table class="table" id="brand_table">
                                                                    <thead>
                                                                      
                                                                        <tr>
                                                                        <th>QR</th>
                                                                            <th>Article#</th>
                                                                            <th>Serial#</th>
                                                                            <th>EAN#</th>
                                                                            <th>Unternehmen/Marke</th>
                                                                            <th>Produkt Typ</th>
                                                                            <th>Deskription</th>
                                                                            <th>Foto</th>
                                                                            <th>Action</th>
                                                                        </tr>
                                                                       
                                                                    </thead>
                                                                <tbody>
                                                                        @foreach ($types as $typ)
                                                                            
                                                                  
                                                                        <tr>
                                                                            <td> {!! DNS2D::getBarcodeHTML("$typ->ean", 'QRCODE',3,3,'#8fc63f'  ) !!}</td>
                                                                            <td>{{ $typ->article}}</td>
                                                                            <td>{{ $typ->serial}}</td>
                                                                            <td>{{ $typ->ean}}</td>
                                                                            <td>{{ $typ->uname}}</td>
                                                                            <td>{{ $typ->type}}</td>
                                                                            <td>
                                                                                <!-- Image Modal -->
                                                                                <a type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" data-toggle="modal" data-target="#description_add{{$typ->id}}">
                                                                                <i class="feather icon-plus"></i>
                                                                                </a>
                                                                                <a type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" data-toggle="modal" data-target="#description{{$typ->id}}">
                                                                                <i class="feather icon-info"></i>
                                                                                </a>

                                                                                    <!-- Modal -->
                                                                                    <div class="modal fade text-left" id="description{{$typ->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                                            <div class="modal-content">
                                                                                                <div class="modal-header">
                                                                                                    <lable><h3>Dekription</h3></lable>
                                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                        <span aria-hidden="true">&times;</span>
                                                                                                    </button>
                                                                                                </div>
                                                                                                <table class="responsible" >
                                                                                                        <tr>
                                                                                                            <th>Unternahmen</th>
                                                                                                            <th>Produkt-typ</th>
                                                                                                            <th>Article-nummer</th>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td>{{ $typ->uname}}</td>
                                                                                                            <td>{{ $typ->type}}</td>
                                                                                                            <td>{{ $typ->article}}</td>
                                                                                                        </tr>
                                                                                                    </table>
                                                                                                    <hr>
                                                                                                <div class="modal-body" style="text-align: center;">
                                                                                                    <p>{!! $typ->description !!}</p>
                                                                                                </div>
                                                                                                <div class="modal-footer">
                                                                                                
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <!-- End Image Modal -->


                                                                                 <!-- Modal -->
                                                                                 <div class="modal fade text-left" id="description_add{{$typ->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                                            <div class="modal-content">
                                                                                                <div class="modal-header">
                                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                        <span aria-hidden="true">&times;</span>
                                                                                                    </button>
                                                                                                </div>
                                                                                                <form method="post" action="{{ action('App\Http\Controllers\ProductTypeController@description')}}">
                                                                                                    @csrf
                                                                                                <div class="modal-body" style="text-align: center;">
                                                                                                    <input type="hidden" name="id" value="{{ $typ->id}}">
                                                                                                    <table class="responsible" >
                                                                                                        <tr>
                                                                                                            <th>Unternahmen</th>
                                                                                                            <th>Produkt-typ</th>
                                                                                                            <th>Article-nummer</th>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td>{{ $typ->uname}}</td>
                                                                                                            <td>{{ $typ->type}}</td>
                                                                                                            <td>{{ $typ->article}}</td>
                                                                                                        </tr>
                                                                                                    </table>
                                                                                                    <hr>
                                                                                                    <lable>Geben Sie die Beschreibung des Artikels ein</lable>
                                                                                                    <textarea name="description" class="form-control" style="height:400px"></textarea>
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
                                                                            <td>
                                                                                <!-- Image Modal -->
                                                                                <a type="button" class="btn btn-icon btn-icon  mr-1 mb-1" data-toggle="modal" data-target="#image{{$typ->id}}">
                                                                                <div class="avatar mr-1 ">
                                                                                <img src="{{ asset('images/type/'.$typ->image) }}" alt="avtar img holder" height="32" width="32">
                                                                            </div>
                                                                                </a>

                                                                                    <!-- Modal -->
                                                                                    <div class="modal fade text-left" id="image{{$typ->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                                            <div class="modal-content">
                                                                                                <div class="modal-header">
                                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                        <span aria-hidden="true">&times;</span>
                                                                                                    </button>
                                                                                                </div>
                                                                                                <div class="modal-body" style="text-align: center;">
                                                                                                    <img src="{{ asset('images/type/'.$typ->image) }}" alt="avtar img holder" height="200" width="200">
                                                                                                </div>
                                                                                               

                                                                                                <form method="post" action="{{ action('App\Http\Controllers\ProductTypeController@save_image')}}" class="custom-file-upload" enctype="multipart/form-data">
                                                                                                @csrf
                                                                                                <div class="modal-body" style="text-align: center;">
                                                                                                <input type="hidden" value="{{ $typ->id}}" name="id">
                                                                                                    <h4>Wählen Sie das Bild für das Produkt aus </h4>
                                                                                                    <lable><code> . Die Größe des Bildes wirkt sich auf die Leistung aus</code></lable>
                                                                                                    <input type="file" name="image" class="form-control" >
                                                                                                </div>
                                                                                                  
                                                                                                    <div class="modal-footer">
                                                                                                        <button type="submit" class="btn btn-primary">Einreichen</button>
                                                                                                    </div>
                                                                                                </form>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <!-- End Image Modal -->
                                                    
                                                 
                                                                            </td>
                                                                        <td>
                                                                        <a type="button" href="{{ route('brand.department.delete',['id'=>$typ->id] )}}" class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1"><i class="feather icon-trash-2"></i></a>
                                                                        <a type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1"  data-toggle="modal" data-target="#edit{{$typ->id}}"><i class="feather icon-edit"></i></a>
                                                                          <!-- Modal -->
                                                                          <div class="modal fade text-left" id="edit{{$typ->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                                            <div class="modal-content">
                                                                                                <div class="modal-header">
                                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                        <span aria-hidden="true">&times;</span>
                                                                                                    </button>
                                                                                                </div>
                                                                                                <form method="post" action="{{ action('App\Http\Controllers\ProductTypeController@update')}}">
                                                                                                    @csrf
                                                                                                <div class="modal-body" style="text-align: left;">
                                                                                                    <input type="hidden" name="id" value="{{ $typ->id}}">
                                                                                                    <table class="responsible" >
                                                                                                       
                                                                                                            <tr><label>Unternahmen</label>
                                                                                                            <input type="text" disabled class="form-control" value="{{ $typ->uname}}"></tr>

                                                                                                            <input type="hidden" class="form-control" name="product_id" value="{{ $brand->id}}">

                                                                                                            <tr>
                                                                                                                <label>Produkt-ty</label>
                                                                                                                <input type="text" class="form-control" name="type" value="{{ $typ->type}}">
                                                                                                            </tr>

                                                                                                            <tr>
                                                                                                            <label>Article-nummer</label>
                                                                                                            <input type="text" class="form-control" name="article" value="{{ $typ->article}}">
                                                                                                            </tr>

                                                                                                            <tr><label>Serial-nummer</label>
                                                                                                          <input type="text" class="form-control" name="serial" value="{{ $typ->serial}}"></tr>

                                                                                                            <tr><label>EAN-nummer</label>
                                                                                                            <input type="text" class="form-control" name="ean" value="{{ $typ->ean}}"></tr>

                                                                                                            <tr><label>Deskription</label>
                                                                                                            <textarea class="form-control" name="description" style="height:400px">{{ $typ->description}}</textarea>
                                                                                                        
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
                {{$types->links()}}
            </div>
        </div>
    </div>
    <!-- END: Content-->
@stop

@section('script')
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
        var i = 0;
        $('#add_brand').click(function(){
            ++i;
            $('#add_department').append(
                ' <tr><input type="hidden" name="product['+i+'][product_id]" value="{{$brand->id}}"><td><input type="text" class="form-control required" disabled value="{{$brand->name}}"></td><td><input type="text" class="form-control required" placeholder="Articlenummer" name="product['+i+'][article]"></td><td><input type="text" class="form-control required" placeholder="Serialnummer" name="product['+i+'][serial]"></td><td><input type="text" class="form-control required" placeholder="EAN-nummer" name="product['+i+'][ean]"></td><td><input type="text" class="form-control required" placeholder="Produkt-type" name="product['+i+'][type]"></td><td><button type="button"  class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1" id="add_remove"><i class="fa fa-trash"></i></button></td></tr> ' 
                );
        });

        $(document).on('click', '#add_remove', function(){
            $(this).parents('tr').remove();
        })

    </script>

 

<script>

    $('#price_type').change(function(){
        if(this.value=="Fixed"){
            $("#plus").hide() ;
        }else{
            $("#plus").show() ;
        }
    })

    $('#payment').change(function(){
        if(this.value=="Vorous"){
            $("#advance").show();
        }else{
            $("#advance").hide();
        }
    })

    
</script>

<script>
    $('#advance').change( function(){

        var price = parseFloat(document.getElementById('purchase_price').value);
        var plus_price = parseFloat(document.getElementById('plus_price').value);
        var price_type= document.getElementById('#price_type');
        var payment= document.getElementById('#payment');
        var advance= document.getElementById('#advance');
        var tax= document.getElementById('#tax');
        var discount= document.getElementById('#discount');
        var total= document.getElementById('#total');

        if(price_type.value == "Verkauf"){
            var result = price + price / 100 * plus_price;
        }
        if (isNaN(price) || isNaN(advance)) {
             console.log('Invalid input. Please enter valid numbers.');
        } else {
            // Perform the calculation (e.g., addition) and log the result
            var result = price + price / 100 * advance;
            console.log('The result is: ' + result);
        }
       

})
</script>
@endsection