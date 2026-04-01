@extends('admin.layouts.app')
@section('title') Zeitarbeitfirma @stop
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
                                <h2 class="content-header-title float-left mb-0">Zeitarbeitfirma</h2>
                                <div class="breadcrumb-wrapper col-12">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">HOME</a>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <div class="content-body">
             <!-- Table Hover Animation start -->
             <div class="row" id="table-hover-animation">
                    <div class="col-12">
                        <div class="card"> 
                            <div class="card-content">
                                <div class="card-body">

                                <div class="col-9">
                                        <form action="{{action('App\Http\Controllers\ExternalPersonalController@index')}}">
                                            <fieldset>
                                                <div class="input-group">
                                                    <input type="text" name="search" class="form-control" placeholder="Search Form" aria-describedby="button-addon2">
                                                    <div class="input-group-append" id="button-addon2">
                                                        <button class="btn btn-primary" type="submit">Go</button>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </form>
                                    </div>

                                <div class="col-md-3 float-right">
                                <div class="card-body">
                                                 <button type="button" class="btn btn-outline-primary block btn-lg" data-toggle="modal" data-target="#default">
                                                 Neue hinzufügen
                                                </button>
                                        <!-- Modal -->
                                             <div class="modal fade text-left" id="default" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel1">Nue</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                            <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\ExternalPersonalController@store')}}" class="custom-file-upload" enctype="multipart/form-data">
                                                                @csrf
                                                                <fieldset>
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Name der Firma
                                                                                </label>

                                                                                 <input type="text" class="form-control"  name="company_name"  required>
                                                                                 @if ($errors->has('company_name'))<p style="color:red;">{!!$errors->first('company_name')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Gewerke
                                                                                </label>

                                                                                <select name="product_id" id="" class="form-control">
                                                                                    <option disabled selected>WÄHLEN SIE DEN ZWECK</option>
                                                                                    @foreach ($products as $product)
                                                                                        <option value="{{$product->id}}"> {{$product->article_group}}</option>
                                                                                    @endforeach

                                                                                </select>
                                                                                 @if ($errors->has('product_id'))<p style="color:red;">{!!$errors->first('product_id')!!}</p>@endif
                                                                            </div>
                                                                        </div>
 
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Preistyp
                                                                                </label>

                                                                                <select name="price_type" id="" class="form-control">
                                                                                    <option disabled selected>WÄHLEN SIE DEN ZWECK</option>
                                                                                    <option value="1" >Pauschalpreis</option>
                                                                                    <option value="2" >Festpreis</option>
                                                                                    <option value="3" >Preis pro Stück</option>
                                                                                    <option value="4" >Stundenlohn</option> 

                                                                                </select>
                                                                                 @if ($errors->has('price_type'))<p style="color:red;">{!!$errors->first('price_type')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                         <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Preis
                                                                                </label>

                                                                                 <input type="number" class="form-control"  name="price"  required>
                                                                                 @if ($errors->has('price'))<p style="color:red;">{!!$errors->first('price')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Steuernummer
                                                                                </label>

                                                                                 <input type="number" class="form-control"  name="tax_id"  required>
                                                                                 @if ($errors->has('tax_id'))<p style="color:red;">{!!$errors->first('tax_id')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Administratorname
                                                                                </label>

                                                                                 <input type="text" class="form-control"  name="admin_name"  required>
                                                                                 @if ($errors->has('admin_name'))<p style="color:red;">{!!$errors->first('admin_name')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Email
                                                                                </label>

                                                                                 <input type="email" class="form-control"  name="email"  required>
                                                                                 @if ($errors->has('email'))<p style="color:red;">{!!$errors->first('email')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Phone
                                                                                </label>

                                                                                 <input type="text" class="form-control"  name="phone"  required>
                                                                                 @if ($errors->has('phone'))<p style="color:red;">{!!$errors->first('phone')!!}</p>@endif
                                                                            </div>
                                                                        </div> 
                                                                    </div>
                                                                 </fieldset>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-primary">Speichern</button> 
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                            </div>

                                    </div>
                                </div>



                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Firma</th>
                                                    <th scope="col">Preistyp</th>
                                                    <th scope="col">Preis</th>
                                                    <th scope="col">Administratorname</th>
                                                    <th scope="col">Kontakt</th>
                                                    <th scope="col">Steuernummer</th>
                                                    <th scope="col">Status</th> 
                                                    <th scope="col">Aktion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data as $item)
                                                <tr>
                                                    <th scope="row">{{ $item->id }}</th>
                                                    <td>{{ $item->company_name }}  </td>
                                                    <td>{{ $item->price_type }}  </td>
                                                    <td>{{ number_format($item->price, 2) }} €</td> 
                                                    <td>{{ $item->admin_name }}  </td>
                                                    <td>
                                                       <p> <i class="feather icon-mail"></i> {{ $item->email }}</p>  
                                                       <p> <i class="feather icon-phone mt-2"></i> {{ $item->phone }} </p> 
                                                        <a type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1 mt-1" href="{{url('external_department/'.$item->id)}}" ><i class="feather icon-mail"></i></a>
                                                    </td> 
                                                     
                                                    <td>@if($item->status=="Published")
                                                        Veröffentlicht
                                                        @else
                                                        Unveröffentlicht
                                                        @endif</td>
                                                    <td>

                                                <!-- Delete Modal -->
                                                <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" data-toggle="modal" data-target="#delete-pro{{$item->id}}">
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
                                                                <h5>Delete Record</h5>
                                                                <p>Do you really want to delete this record?</p>
                                                                <p>The Recard Number is: {{$item->id}} </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                              <a type="button" href="{{url('/external_destroy').'/'.$item->id}}" class="btn btn-primary">Yes</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Delete Modal -->


                                            <!-- Begin: Edit -->
                                            <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1" data-toggle="modal" data-target="#editmodel{{$item->id}}">
                                            <i class="feather icon-edit"></i>
                                                </button>
                                        <!-- Modal -->
                                                 <div class="modal fade text-left" id="editmodel{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel1">Bearbeiten</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                            <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\ExternalPersonalController@update')}}"class="custom-file-upload" enctype="multipart/form-data" >
                                                                @csrf

                                                                 <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Name der Firma
                                                                                </label>

                                                                                 <input type="text" class="form-control"  name="company_name"   value="{{ $item->company_name}}" required>
                                                                                 <input type="hidden" class="form-control"  name="id" value="{{ $item->id}}"  required>
                                                                                 @if ($errors->has('company_name'))<p style="color:red;">{!!$errors->first('company_name')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Gewerke
                                                                                </label>

                                                                                <select name="product_id" id="" class="form-control">
                                                                                    <option disabled selected>WÄHLEN SIE DEN ZWECK</option>
                                                                                    @foreach ($products as $product)
                                                                                        <option value="{{$product->id}}" @if($product->id == $item->product_id) selected @endif> {{$product->article_group}}</option>
                                                                                    @endforeach

                                                                                </select>
                                                                                 @if ($errors->has('product_id'))<p style="color:red;">{!!$errors->first('product_id')!!}</p>@endif
                                                                            </div>
                                                                        </div>
 
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Preistyp
                                                                                </label>

                                                                                <select name="price_type" id="" class="form-control">
                                                                                    <option disabled selected>WÄHLEN SIE DEN ZWECK</option>
                                                                                    <option value="1" @if($item->price_type == 1) selected @endif>Pauschalpreis</option>
                                                                                    <option value="2" @if($item->price_type == 2) selected @endif >Festpreis</option>
                                                                                    <option value="3" @if($item->price_type == 3) selected @endif>Preis pro Stück</option>
                                                                                    <option value="4" @if($item->price_type == 4) selected @endif>Stundenlohn</option> 

                                                                                </select>
                                                                                 @if ($errors->has('price_type'))<p style="color:red;">{!!$errors->first('price_type')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                         <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Preis
                                                                                </label>

                                                                                 <input type="number" class="form-control"  value="{{$item->price}}" name="price"  required>
                                                                                 @if ($errors->has('price'))<p style="color:red;">{!!$errors->first('price')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Steuernummer
                                                                                </label>

                                                                                 <input type="text" class="form-control" value="{{$item->tax_id}}" name="tax_id"  required>
                                                                                 @if ($errors->has('tax_id'))<p style="color:red;">{!!$errors->first('tax_id')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Administratorname
                                                                                </label>

                                                                                 <input type="text" class="form-control" value="{{$item->admin_name}}" name="admin_name"  required>
                                                                                 @if ($errors->has('admin_name'))<p style="color:red;">{!!$errors->first('admin_name')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Email
                                                                                </label>

                                                                                 <input type="email" class="form-control"  name="email" value="{{$item->email}}"  required>
                                                                                 @if ($errors->has('email'))<p style="color:red;">{!!$errors->first('email')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Phone
                                                                                </label>

                                                                                 <input type="text" class="form-control"  name="phone" value="{{$item->phone}}" required>
                                                                                 @if ($errors->has('phone'))<p style="color:red;">{!!$errors->first('phone')!!}</p>@endif
                                                                            </div>
                                                                        </div> 
                                                                    </div>


                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">Submit</button>

                                                            </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                 </div>
                                    <!-- End Edit Modal -->

                                                    </td>
                                                    <!-- Operation Section -->
                                                    <td>
                                                        @if($item->status=="Unpublished" || $item->status=="")
                                                        <a type="button" href="{{ url('/external_publish/'.$item->id) }}" class="btn btn-icon btn-icon rounded-circle btn-success mr-1 mb-1" >
                                                        <i class="feather icon-check"></i>
                                                        </a>
                                                        @else
                                                        <a type="button" href="{{ url('/external_unpublish/'.$item->id) }}" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" >
                                                        <i class="feather icon-check"></i>
                                                        </a>
                                                        @endif

                                                    </td>
                                                    <!-- Operation Section -->
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
                <!-- Table head options end -->
                {{$data->links()}}
            </div>
        </div>
    </div>
    <!-- END: Content-->
@stop

@push('scripts')
<script>
    $(document).ready(function() {
        // Display the save success message if it exists
        @if(Session::has('save_msg'))
            toastr.success("{{ session('save_msg') }}");
        @endif

        // Display the update message if it exists
        @if(Session::has('update_msg'))
            toastr.success("{{ session('update_msg') }}");
        @endif

        // Display the delete message if it exists
        @if(Session::has('delete_msg'))
            toastr.error("{{ session('delete_msg') }}");
        @endif

        // Display validation errors
        @if($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif
    });
</script>


@endpush
