@extends('admin.layouts.app')
@section('title') Kunden Details @stop
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
                            <h4 class="card-title">Kunden Details</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">


                                <div class="col-9">
                                    <form action="{{action('App\Http\Controllers\CustomerController@index')}}">
                                        <fieldset>
                                            <div class="input-group">
                                                <input type="text" name="search" class="form-control"
                                                    placeholder="Search Form" aria-describedby="button-addon2">
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
                                                <th scope="col">Kunden</th>
                                                <th scope="col">Product</th>
                                                <th scope="col">Contact Person</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Aktion</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data as $item)
                                            <tr>

                                                <th scope="row">{{ $item->id }}</th>
                                                <td> <a href="{{ url('/customer_show/'.$item->id)}} "> {{ $item->name }}
                                                        {{ $item->lastname }}</a></td>
                                                <td>    
                                                        {{-- @if($product->count() != 0)
                                                                @foreach ($product as $pr)
                                                                @if($pr->customer_id==$item->id)
                                                                 <a href="{{ url('customer_product_create/'.$item->id)}}">
                                                                        <div class="badge badge-pill badge-glow badge-primary mr-1 mb-1"> {{ $pr->article_group }} </div>
                                                                    </a> 
                                                                @endif 
                                                                 @endforeach
                                                        @else
                                                        <a type="button" href="{{ url('customer_product_create/'.$item->id)}}">
                                                         Wählen Sie das Produkt
                                                        </a>
                                                        @endif --}}
                                                    
                                                   
                                                </td>
                                                <td>{{ $item->empname}} {{ $item->emplastname}}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</td>

                                                <td>
                                                    @if(DB::table('user_rolls')
                                                    ->where('user_rolls.user_id', '=',  auth()->user()->name)
                                                    ->where('user_rolls.item_id', '=', 'Customer')
                                                    ->where('user_rolls.is_delete', '=', 'on')
                                                    ->first())
                                                    <!-- Delete Modal -->
                                                    <button type="button"
                                                        class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1"
                                                        data-toggle="modal" data-target="#delete-pro{{$item->id}}">
                                                        <i class="feather icon-trash"></i>
                                                    </button>
                                                    @endif

                                                    <!-- Modal -->
                                                    <div class="modal fade text-left" id="delete-pro{{$item->id}}"
                                                        tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-scrollable"
                                                            role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <h5>Aufzeichnung löschen</h5>
                                                                    <p>Möchten Sie diesen Datensatz wirklich löschen?
                                                                    </p>
                                                                    <p>Die Datensatznummer lautet:{{$item->id}} </p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <a type="button"
                                                                        href="{{url('/customer_destroy').'/'.$item->id}}"
                                                                        class="btn btn-primary">Yes</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                </div>
                                <!-- End Delete Modal -->


                                <!-- Begin: Edit -->
                                @if(DB::table('user_rolls')
                                ->where('user_rolls.user_id', '=',  auth()->user()->name)
                                ->where('user_rolls.item_id', '=', 'Customer')
                                ->where('user_rolls.is_update', '=', 'on')
                                ->first())
                                <a type="button" href="{{ url('/customer_edit/'.$item->id)}}"
                                    class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1">
                                    <i class="feather icon-edit"></i>
                                </a>

                                @endif

                                @if(DB::table('user_rolls')
                                ->where('user_rolls.user_id', '=', auth()->user()->name)
                                ->where('user_rolls.item_id', '=', 'Customer')
                                ->where('user_rolls.is_update', '=', 'on')
                                ->first())
                                <a type="button" href="{{ url('/offer_product/'.$item->id)}}"
                                    class="btn btn-icon btn-icon btn-primary mr-1 mb-1">
                                    <i class="fa fa-file-text-o"></i> Angebot
                                </a>

                                @endif

                                @if(DB::table('user_rolls')
                                ->where('user_rolls.user_id', '=', auth()->user()->name)
                                ->where('user_rolls.item_id', '=', 'Customer')
                                ->where('user_rolls.is_update', '=', 'on')
                                ->first())
                                <a type="button" href="{{ url('/weather/'.$item->id)}}"
                                    class="btn btn-icon btn-icon btn-primary mr-1 mb-1">
                                    <i class="feather icon-cloud-lightning"></i> WEATHER
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