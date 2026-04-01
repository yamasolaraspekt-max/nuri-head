@extends('admin.layouts.app')

@section('title') REUQEST OUT @endsection
@section('style') 
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css')}}">
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
                            <h2 class="content-header-title float-left mb-0">Anfrageformular</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="">
                                        @foreach ($content as $title)
                                            {{ $title->product }}
                                        @endforeach
                                    </a>
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
                        <div class="col-md-6 col-12">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                    </ul>
                                    <div class="alert alert-danger" role="alert" id="dialog" style="display: none;">
                                        <h4 class="alert-heading">INFORMATION</h4>
                                        <p class="mb-0" id="dialog_text">
                                            
                                        </p>
                                    </div>
                                        <form class="form form-horizontal" method="post" action="{{ action('App\Http\Controllers\InventoryRequestOutController@store') }}">
                                            @csrf
                                            <div class="form-body">
                                                <div class="row">
                                               @foreach ($content as $pro )
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Produkt</span>
                                                            </div>
                                                           <div class="col-md-8">
                                                                <input type="hidden" value="{{ $pro->id }}" name="product_id">
                                                                <input type="hidden" value="{{ $old_quantity }}" name="old_quantity">
                                                                <input type="text" value="{{ $pro->product }}" class="form-control" name="product" >
                                                                @if ($errors->has('product'))<p style="color:red;">{!!$errors->first('product')!!}</p>@endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Available Unit</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="number" disabled  value="{{ $old_quantity }}"  class="form-control" id="available"  name="available" >
                                                                @if ($errors->has('available'))<p style="color:red;">{!!$errors->first('available')!!}</p>@endif

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Quantity</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="number"   value="{{ $old_quantity }}"  class="form-control"  id="quantity"  name="quantity" >
                                                                @if ($errors->has('quantity'))<p style="color:red;">{!!$errors->first('quantity')!!}</p>@endif

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Reason</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <textarea  id="first-name" class="form-control" name="reason"></textarea>
                                                                @if ($errors->has('reason'))<p style="color:red;">{!!$errors->first('reason')!!}</p>@endif

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Responsible</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="hidden" name="responsible_id" value="{{ $pro->responsible_id }}">
                                                                <input type="text" disabled class="form-control" name="responsible_id" 
                                                                value="{{ DB::table('employees')->where('id', $pro->responsible_id)->select('name')->value('name')}} {{ DB::table('employees')->where('id', $pro->responsible_id)->select('lastname')->value('lastname')}}">
                                                            </div>
                                                            @if ($errors->has('responsible_id'))<p style="color:red;">{!!$errors->first('responsible_id')!!}</p>@endif

                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Request from</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <fieldset class="form-group">
                                                                     <select class="form-control" id="requester" name="requester">
                                                                            @foreach ($employee as $emp)
                                                                            <option value="{{ $emp->id }}">{{ $emp->name }} {{ $emp->lastname }}</option>
                                                                            @endforeach
                                                                     </select>
                                                                 </fieldset>
                                                            </div>
                                                            @if ($errors->has('requester'))<p style="color:red;">{!!$errors->first('requester')!!}</p>@endif

                                                        </div>
                                                    </div>

                                           
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-8">
                                                        <a type="button" class="btn btn-primary" href="{{ url()->previous() }}"> Zurück </a>
                                                        <button type="submit" class="btn btn-primary"> Einreichen </button>

                                                    </div>

                                                    
                                                </div>
                                            </div>  
                                        </form>
                                    </div>

                                </div>

                                                   
                                @endforeach
                                  
                         </div>
                        </div>
                    </div>
                 
                    <div class="col-md-6 col-6">
                        <div class="row" id="table-bordered">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="card-title"> Verfügbare Produkte im Lager</h4>
                                            </div>
                                            <div class="card-content">
                                                <div class="card-body">
                                                    <p class="card-text"></p>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered mb-0">
                                                        <thead>
                                                            <tr>
                                                              
                                                                <th>#</th>
                                                                <th>Produkt</th>
                                                                <th>Unternahmen</th>
                                                                <th>Liefrant</th>
                                                                <th>Lagerung</th>
                                                                <th>Regal</th>
                                                                <th>Reihe</th>

                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($content as $invent)
                                                               <tr>
                                                                <td>{{ $invent->serial_no }}</td>
                                                                <td>{{ $invent->product }}</td>
                                                                <td>{{ $invent->brandname }}</td>
                                                                <td>{{ $invent->distributor }}</td>
                                                                <td>{{ DB::table('branches')->where('id', $invent->location)->select('branch')->value('branch') }}</td>
                                                                <td>{{ $invent->shelf }}</td>
                                                                <td>{{ $invent->row }}</td>
                                                               </tr>
                                                            @endforeach
                                                         
                                                           
                                                        </tbody>
                                                    </table>
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

<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>

<script>
    $(document).ready(function() {
        $('#product').select2();
        $('#requester').select2();
        $('#responsible').select2();
        $('#requester').select2();
    });

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