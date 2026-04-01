@extends('admin.layouts.app')
@section('title') QRCODE @stop
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
                                    <div class="input-group-append" id="button-addon2">
                                        <a class="btn btn-danger waves-effect waves-light" type="button" href="{{ url('/qr_print') }}"><i class="feather icon-printer"></i></a>
                                    </div>

                                    <div class="input-group-append" id="button-addon2">
                                        <a class="btn btn-warning waves-effect waves-light" type="button"  data-toggle="modal" data-target="#qrcode"> <i class="fa fa-qrcode"></i></a>
                                    </div>
     
                                        <!-- Modal -->
                                        <div class="modal fade text-left" id="qrcode" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form method="post" action="{{ action('App\Http\Controllers\QrCodeController@store') }}">
     
                                                   @csrf
                                                    <div class="modal-body">
                                                        <h5>Branch</h5>
                                                        <select class="select2-customize-result form-control required" name="branch"  id="branch"  style="width:100%">
                                                            @foreach ($branch as $br)
                                                            <option value="{{ $br->id }}">{{ $br->branch }}</option>
                                                            @endforeach
                                                        </select>
     
                                                        <h5>Menge</h5>
                                                        <input type="number" class="form-control" name="quantity">
                                                    </div>
                                                    <div class="modal-footer">
                                                      <button type="submit" href="" class="btn btn-primary">Drucken</button>
                                                    </div>
                                                 </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Delete Modal -->
                                
                                
                                </div>
                            
                            </fieldset>
                        </form>
                        </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">QRCODE</h4>
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
                                                        
                                                                <table class="table" id="brand_table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>QRCODE</th>
                                                                            <th>Branch</th>
                                                                            <th>Action</th>
                                                                        </tr>
                                                                       
                                                                    </thead>
                                                                <tbody>
                                                                        @foreach ($data as $item)
                                                                            
                                                                  
                                                                        <tr>
                                                                            <td>{{ $item->id}}</td>
                                                                            <td>{{ $item->qrcode}}</td>   
                                                                            <td>{{ $item->branch}}</td>   
                                                                        <td>
                                                                        <a type="button" href="{{ route('qr.destroy',['id'=>$item->id])}}" class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1"><i class="feather icon-trash-2"></i></a>
                                                                        
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

<script>
        var i = 0;
        $('#add_itemuage').click(function(){
            ++i;
            $('#itemuage').append(
                '<tr> <td><input type="text" class="form-control required" placeholder="Sprachen" name="itemuage['+i+'][itemuage]"></td><td><button type="button"  class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1" id="add_remove"><i class="fa fa-trash"></i></button></td></tr> ' 
                );
        });

        $(document).on('click', '#add_remove', function(){
            $(this).parents('tr').remove();
        })

    </script>
@endsection