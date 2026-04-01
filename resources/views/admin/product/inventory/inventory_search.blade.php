@extends('admin.layouts.app')
@section('title') Inventory @stop

@section('style')
<!-- Include stylesheet -->

<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
@endsection
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
               
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Produkt Inventory</h4>
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

                                                                <table class="table" id="brand_table">
                                                                    <thead>
                                                                      
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>Produkt</th>
                                                                            <th>Seriennummer</th>
                                                                            <th>Artikel-nummer</th>
                                                                            <th>EAN-nummer</th>
                                                                            <th>Handbuch</th>
                                                                            <th>Lagerung</th>
                                                                            <th>Regal</th>
                                                                            <th>Reihe</th>
                                                                            <th>Quantität</th>
                                                                            <th>Verantwortlich</th>
                                                                            <th>Aktion</th>
                                                                        </tr>
                                                                       
                                                                    </thead>
                                                                <tbody>
                                                                     
                                                                            
                                                                  
                                                                        <tr>
                                                                          @foreach ($data as $invent)
                                                                          <td>{{ $invent->id }}</td>    
                                                                          <td>{{ $invent->product }}</td>    
                                                                          <td>{{ $invent->serial_no }}</td>    
                                                                          <td>{{ $invent->article_no }}</td>    
                                                                          <td>{{ $invent->ean }}</td>    
                                                                          <td>{{ $invent->manual_no }}</td>    
                                                                          <td>{{ $invent->location }}</td>    
                                                                          <td>{{ $invent->shelf }}</td>    
                                                                          <td>{{ $invent->row }}</td>    
                                                                          <td>{{ $invent->quantity }}</td>    
                                                                          <td>{{ $invent->name }} {{ $invent->lastname }}</td>    
                                                                        <td>
                                                                            <a type="button" href="{{ route('product.inventory.destroy',['id'=>$invent->id] )}}" class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1"><i class="feather icon-trash-2"></i></a>
                                                                        <a type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1"  data-toggle="modal" data-target="#edit"><i class="feather icon-edit"></i></a>
                                                                          <!-- Modal -->
                                                                          <div class="modal fade text-left" id="edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                                            <div class="modal-content">
                                                                                                <div class="modal-header">
                                                                                                    {{ $invent->product }} | Bearbaiten
                                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                        <span aria-hidden="true">&times;</span>
                                                                                                    </button>
                                                                                                </div>
                                                                                                <form method="post" action="{{ action('App\Http\Controllers\InventoryController@update') }}">
                                                                                                    @csrf
                                                                                                <div class="modal-body" style="text-align: left;">
                                                                                                    <input type="hidden" name="id" value="{{ $invent->id }}">
                                                                                                    <table class="responsible" >
                                                                                                            <tr>
                                                                                                                <label>Seriennummer</label>
                                                                                                                <input type="text" class="form-control" name="serial_no" value="{{ $invent->serial_no }}">
                                                                                                            </tr>

                                                                                                            <tr>
                                                                                                            <label>Artikel-nummer</label>
                                                                                                            <input type="text" class="form-control" name="article_no" value="{{ $invent->article_no }}">
                                                                                                            </tr>

                                                                                                            <tr><label>EAN-nummer</label>
                                                                                                            <input type="text" class="form-control" name="ean" value="{{ $invent->ean }}">
                                                                                                            </tr>

                                                                                                            <tr><label>Handbuch</label>
                                                                                                                <input type="text" class="form-control" name="manual_no" value="{{ $invent->manual_no }}">
                                                                                                                </tr>

                                                                                                            <tr>
                                                                                                                <label>Lagerung</label>
                                                                                                                <input type="text" class="form-control" name="location" value="{{ $invent->location }}">
                                                                                                            </tr>
                                                                                                            <tr>
                                                                                                                <label>Regal</label>
                                                                                                                <input type="text" class="form-control" name="shelf" value="{{ $invent->shelf }}">
                                                                                                            </tr>
                                                                                                            <tr>
                                                                                                                <label>Reihe</label>
                                                                                                                <input type="text" class="form-control" name="row" value="{{ $invent->row }}">
                                                                                                            </tr>
                                                                                                            <tr>
                                                                                                                <label>Quantität</label>
                                                                                                                <input type="text" class="form-control" name="quantity" value="{{ $invent->quantity }}">
                                                                                                            </tr>
                                                                                                            <tr>
                                                                                                                <label>Verantwortlich</label>
                                                                                                                <select class="select2 form-control" id="responsible" name="responsible_id">
                                                                                                                    @foreach ($responsible as $res)
                                                                                                                    <option value="{{ $res->id }}" @if($res->id == $invent->responsible_id) selected @endif>{{ $res->name }} {{ $res->lastname }}</option>
                                                                                                                    @endforeach
                                                                                                                </select>
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
            </div>
        </div>
    </div>
    <!-- END: Content-->
@stop

@section('script')
<script src="{{ asset('js/select2.min.js') }}"></script>

<script>
    $(document).ready(function() {
        $('#product').select2({
            placeholder: "Ausgewähltes Produkt",
            allowClear: true
        });

        $('#responsible').select2({
            placeholder: "Wählen Sie Die verantwortliche Person aus ",
            allowClear: true
        });
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
        var i = 0;
        $('#add_brand').click(function(){
            ++i;
            $('#add_department').append(
//Add product
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

@endsection