@extends('admin.layouts.app')
@section('title') Anfrageformular @stop
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
                                <h4 class="card-title">Anfrageformular</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                
                        
                                <div class="col-9">
                                        <form action="">
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
                                            <a type="button" class="btn btn-outline-primary block btn-lg" href="{{ route('request.out.create') }}">
                                            Add New
                                            </a>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Produkt</th>
                                                    <th scope="col">Quantity</th>
                                                    <th scope="col">Reason</th>
                                                    <th scope="col">Request From</th>
                                                    <th scope="col">Request To</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">Ackion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data as $item)
                                                <tr>
                                                    <th scope="row">{{$item->id}}</th>
                                                    <td>{{ $item->product }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>{{ $item->reason }}</td>
                                                    <td>{{ $item->requestname}} {{ $item->requestlastname  }}</td>
                                                    <td>{{ $item->rname  }} {{ $item->rlastname  }}</td>
                                                    <td>{{ $item->status }}</td>
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
                                                                <h5>Datensatz löschen</h5>
                                                                <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                <p>Die Datensatznummer lautet: {{$item->id}} </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                              <a type="button" href="{{url('/request_out_delete').'/'.$item->id}}" class="btn btn-primary">Yes</a>
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