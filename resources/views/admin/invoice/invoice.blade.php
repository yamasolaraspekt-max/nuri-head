@extends('admin.layouts.app')
@section('title') Rechnung @stop

@section('style')
<!-- Include stylesheet -->
<!-- <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css')}}"> -->
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">


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
                                <h4 class="card-title">Rechnung</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                <div class="col-9">
                                        <form action="{{action('App\Http\Controllers\InvoiceController@index')}}">
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
                                        <a type="button" class="btn btn-outline-primary block btn-lg" href="{{ route('invoice.create') }}">
                                            Add New
                                        </a>
                                    </div>
                                </div>
                                     
                                    
                                    
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Rechnung-no</th>
                                                    <th scope="col">Date</th>
                                                    <th scope="col">Gekauft für</th>
                                                    <th scope="col">Unternehmen</th>
                                                    <th scope="col">Erworben von</th>
                                                    <th scope="col">Bearbeitet von</th>
                                                    <th scope="col">Genehmigt durch</th>
                                                    <th scope="col">Anhang</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">Action</th>
                                                    <th scope="col">Operation</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data as $item)
                                                <tr>
                                                    <th scope="row">{{ $item->id }}</th>
                                                    <td>{{ $item->invoice_no }}</td>
                                                    <td>{{ $item->invoice_date}}</td>
                                                    <td>
                                                        Gekauft für:{{ $item->purchase_for}} </br>
                                                        @if($item->purchase_for == "Kunden")
                                                            @foreach ($customer as $cus)
                                                                @if($cus->cid == $item->customer_id && $cus->invoice_id == $item->invoice_no)
                                                                <div class="badge badge-pill badge-glow badge-warning mr-1 mb-1"> <a href="{{ url('/customer_show/'.$cus->cid)}}">{{ $cus->customer_name }} {{ $cus->customer_lastname }}</a></div>
                                                                @endif
                                                            @endforeach
                                                        @elseif( $item->purchase_for = "Personal")
                                                         @foreach ($employee as $emp)
                                                            @if($emp->emp_id == $item->employee_id && $emp->invoice_id == $item->invoice_no)
                                                                <div class="badge badge-pill badge-glow badge-warning mr-1 mb-1"> <a href="">{{ $emp->employee_name }} {{ $emp->employee_lastname }}</a></div>
                                                            @endif
                                                            @endforeach
                                                    @endif
                                                    </td>
                                                    <td>{{ $item->name}}</td>                                                  
                                                    <td>{{ $item->pname}} {{ $item->plastname}}</td>                                                  
                                                    <td>
                                                        {{ $item->ename}} {{ $item->elastname}} </br>
                                                        <div class="badge badge-pill badge-glow badge-danger mr-1 mb-1">Bearbeiteter Benutzer:{{ DB::table('users')->join('employees', 'employees.id', '=', 'users.name')->where('users.name', '=', $item->edited_user)->select('employees.name')->pluck('name')->first() }}</div>
                                                    </td>
                                                    @if($item->approved_by != null)                                                  
                                                    <td>{{ DB::table('users')->join('employees', 'employees.id' , '=', 'users.name')->where('users.name', '=', $item->approved_by)->select('employees.name')->pluck('name')->first()}} {{ DB::table('users')->join('employees', 'employees.id' , '=', 'users.name')->where('users.name', '=', $item->approved_by)->select('employees.lastname')->pluck('lastname')->first()}} </td> 
                                                    @else
                                                    <td>nicht definiert</td>
                                                    @endif
                                                    <td>
                                                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1" data-toggle="modal" data-target="#pdf{{$item->id}}">
                                                            <i class="feather icon-file"></i>
                                                            </button>
            
                                                            <!-- PDF Modal -->
                                                            <div class="modal fade text-left" id="pdf{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body" >
                                                                            <h5>Rechnung: {{ $item->invoice_no }}</h5>
                                                                            <div class="row justify-content-center">
                                                                                <iframe src="{{ url('invoices/'.$item->pdf) }}" width="100%" height="800"></iframe>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                          
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- End PDF Modal -->
                                                    </td>                                                  
                                                    <td>
                                                        @if($item->status=="notApproved")
                                                             <div class="badge badge-pill badge-glow badge-danger mr-1 mb-1">Warten auf die Bestätigung</div>
                                                        @elseif($item->status=="approved")
                                                             <div class="badge badge-pill badge-glow badge-success mr-1 mb-1">Genehmigt</div>
                                                                 </br>
                                                              <!-- PDF Modal -->
                                                                  
                                                                        <a data-toggle="modal" data-target="#signature{{$item->id}}"><div class="badge badge-pill badge-glow badge-success mr-1 mb-1" >Unterschrift</div></a>
                                                                        <!-- Modal -->
                                                                        <div class="modal fade text-left" id="signature{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                            <span aria-hidden="true">&times;</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body" >
                                                                                        <h5>Rechnung: {{ $item->invoice_no }}</h5>
                                                                                        <p>Status: Genehmigt</p>
                                                                                        <p>Approved by: {{ DB::table('users')->join('employees', 'employees.id' , '=', 'users.name')->where('users.name', '=', $item->approved_by)->select('employees.name')->pluck('name')->first()}} {{ DB::table('users')->join('employees', 'employees.id' , '=', 'users.name')->where('users.name', '=', $item->approved_by)->select('employees.lastname')->pluck('lastname')->first()}} </p>
                                                                                        <img src="{{ asset('invoices/signed/'.$item->signature)}}" width="50%">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                        @elseif($item->status=="draft")
                                                        <a data-toggle="modal" data-target="#draft{{$item->id}}"><div class="badge badge-pill badge-glow badge-danger mr-1 mb-1">Die Rechnung wird abgelehnt </div></a>
                                                             <!-- Modal -->
                                                             <div class="modal fade text-left" id="draft{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                 <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                     <div class="modal-content">
                                                                         <div class="modal-header">
                                                                             <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                 <span aria-hidden="true">&times;</span>
                                                                             </button>
                                                                         </div>
                                                                         <div class="modal-body" >
                                                                             <h5>Rechnung: {{ $item->invoice_no }}</h5>
                                                                             <p>Status: Zurückgegangen</p>
                                                                             <p>Abgelehnt von:{{ DB::table('users')->join('employees', 'employees.id' , '=', 'users.name')->where('users.name', '=', $item->draft_by)->select('employees.name')->pluck('name')->first()}} {{ DB::table('users')->join('employees', 'employees.id' , '=', 'users.name')->where('users.name', '=', $item->draft_by)->select('employees.lastname')->pluck('lastname')->first()}} </p>
                                                                            <hr>
                                                                             <h4>Grund der Ablehnung</h4>
                                                                             <p> {!! $item->draft_reason !!}</p>
                                                                         </div>
                                                                     </div>
                                                                 </div>
                                                             </div>
                                                         </div>
                                                        @endif
                                                    </td>
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
                                                            <div class="modal-body" >
                                                                <h5>Datensatz löschen</h5>
                                                                <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                <p>Die Recard-Nummer lautet: {{$item->id}} </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                              <a type="button" href="{{url('/invoice_destroy').'/'.$item->id}}" class="btn btn-primary">Yes</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Delete Modal -->
                                            <!-- Begin: Edit -->
                                                    <a type="button" href="{{ route('invoice.edit', ['id'=>$item->id]) }}" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1">
                                                        <i class="feather icon-edit"></i>
                                                    </a>
                                                    </td>
                                                    <!-- Operation Section -->
                                                    <td>
                                                        @if($item->status=="notApproved")
                                                        <a type="button" href="{{ url('invoice_apr/'.$item->id) }}" class="btn btn-icon btn-icon rounded-circle btn-success mr-1 mb-1" >
                                                        <i class="feather icon-check"></i>
                                                        </a>

                                                            <a type="button" href="{{ url('invoice_draft/'.$item->id) }}" class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1" >
                                                            <i class="feather icon-info"></i>
                                                            </a>
                                                        @elseif ($item->status=="draft")
                                                        <a type="button" href="{{ url('invoice_apr/'.$item->id) }}" class="btn btn-icon btn-icon rounded-circle btn-success mr-1 mb-1" >
                                                            <i class="feather icon-check"></i>
                                                            </a>
    
                                                                <a type="button" href="{{ url('invoice_draft/'.$item->id) }}" class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1" >
                                                                <i class="feather icon-info"></i>
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

@section('script')

<script src="{{ asset('js/select2.min.js') }}"></script>
<script>
   
    $(document).ready(function(){
        @if(Session::has('update_msg'))
        toastr.success("{{ session('updated_msg') }}");
        @endif
        @if(Session::has('save_msg'))
        toastr.success("{{ session('save_msg') }}");
        @endif

        @if(Session::has('not_save'))
        toastr.error("{{ session('not_save') }}");
        @endif


       
  
  @if(Session::has('delete_msg'))
  toastr.error("{{ session('delete_msg') }}");
  @endif
    });
    

</script>

<script>
$(document).ready(function() {
    $('#brand_id').select2();
    
});
</script>
@endsection