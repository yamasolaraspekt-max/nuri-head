@extends('admin.layouts.app')
@section('title') Kundendokumentation @stop
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
                                <h4 class="card-title">Kundendokumentation</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                
                                    <div class="row">
                                        <div class="col-md-1">
                                            <div class="card text-center">
                                                <div class="card-content" style="background: white; padding: 6px;  border-style: solid;"">
                                                    <div class="card-body" style="    background: gray;">
                                                        <h6 class="text-bold-700">NUE</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-1">
                                            <div class="card text-center">
                                                <div class="card-content" style="background: white; padding: 6px;  border-style: solid; border-color:#e53060">
                                                    <div class="card-body" style="    background: #e53060;">
                                                        <h6 class="text-bold-700" style="color:white; font-width:bold; font-size:10px;">OFFEN</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="card text-center">
                                                <div class="card-content" style="background: white; padding: 6px;  border-style: solid; border-color:#92b532">
                                                    <div class="card-body" style="    background: #92b532;">
                                                        <h6 class="text-bold-700" style="color:white; font-width:bold;font-size:10px;">AKTIV</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="card text-center">
                                                <div class="card-content" style="background: white; padding: 6px;  border-style: solid; border-color:#78a7cc">
                                                    <div class="card-body" style="    background: #78a7cc;">
                                                        <h6 class="text-bold-700" style="color:white; font-width:bold;font-size:10px;">IN AKTIV</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="card text-center">
                                                <div class="card-content" style="background: white; padding: 6px;  border-style: solid; border-color:#213985">
                                                    <div class="card-body" style="    background: #213985;">
                                                        <h6 class="text-bold-700" style="color:white; font-width:bold;font-size:8px;">PROJECT </br> BEENDET</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="card text-center">
                                                <div class="card-content" style="background: white; padding: 6px;  border-style: solid; border-color:#b53232">
                                                    <div class="card-body" style="    background: #b53232;">
                                                        <h6 class="text-bold-700" style="color:white; font-width:bold;font-size:10px;">ABSAGE</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="card text-center">
                                                <div class="card-content" style="background: white; padding: 6px;  border-style: solid; border-color:#316838">
                                                    <div class="card-body" style="    background: #316838;">
                                                        <h6 class="text-bold-700" style="color:white; font-width:bold;font-size:10px;">ARCHIV</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                       
                                    </div>
                        
                                <div class="col-9">
                                        <form action="{{action('App\Http\Controllers\CustomerController@index')}}">
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

                        
                               
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Name</th>
                                                    <th scope="col">PLZ</th>
                                                    <th scope="col">ORT</th>
                                                    <th scope="col">EINGING</th>
                                                    <th scope="col">GEWERK</th>
                                                    <th scope="col">WARTUNG</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data as $item)
                                                <tr>
                                                
                                                    <th scope="row">{{ $item->id }}</th>
                                                    <td> <a href="{{ url('/customer_product_list/'.$item->id)}} "> {{ $item->name   }} {{ $item->lastname }}</a></td>
                                                    <td>{{ $item->postcode }}</td>
                                                    <td>{{ $item->city }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</td>

                                                    <td>
                                                       
                                                        
                                                      
                                                        @foreach ($article as $ar )
                                                        @if($ar->customer_id==$item->id)
                                                            <a type="button" class="btn btn-icon btn-icon  rounded-circle btn-primary mr-1 mb-1" id="inactive" style="height: 40px;  width: 40px; background:{{ $ar->status }} !important;">
                                                                <span style="font-size: 10px;font-weight: bold; color:white; margin:0;font-family: sans-serif !important;">{{ $ar->initial }}</span>
                                                            </a>
                                                        @endif
                                                        @endforeach

                                                  
                                                        
                                                       
                                                    </td>
                                                    <td>
                                                @if(DB::table('user_rolls')
                                                    ->where('user_rolls.user_id', '=', auth()->user()->id)
                                                    ->where('user_rolls.item_id', '=', 'Customer')
                                                    ->where('user_rolls.is_delete', '=', 'on')
                                                    ->first())
                                                <!-- Delete Modal -->
                                                <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" data-toggle="modal" data-target="#delete-pro{{$item->id}}">
                                                <i class="feather icon-trash"></i>
                                                </button>
                                                @endif

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
                                                                <h5>Aufzeichnung löschen</h5>
                                                                <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                <p>Die Datensatznummer lautet:{{$item->id}} </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                              <a type="button" href="{{url('/customer_destroy').'/'.$item->id}}" class="btn btn-primary">Ja</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Delete Modal -->


                                            <!-- Begin: Edit -->
                                            @if(DB::table('user_rolls')
                                    ->where('user_rolls.user_id', '=', auth()->user()->id)
                                    ->where('user_rolls.item_id', '=', 'Customer')
                                    ->where('user_rolls.is_update', '=', 'on')
                                    ->first())
                                            <a type="button" href="{{ url('/customer_edit/'.$item->id)}}" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1">
                                            <i class="feather icon-edit"></i>
                                                </a>
                                        
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
                <!-- Table head options end -->
                {{$data->links()}}
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
@endsection