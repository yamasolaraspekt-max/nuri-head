@extends('admin.layouts.app')
@section('title') Gegenstände übergeben
@stop
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
                                <h4 class="card-title">Gegenstände übergeben
                                </h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                         
                                    <div class="row">
                                        <div class="col-8">
                                            <form action="{{action('App\Http\Controllers\HandoverController@multiple')}}">
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

                                            <div class="col-md-2 float-right">
                                                <a type="button" class="btn btn-outline-primary block btn-lg" href="{{ url('/handover') }}">
                                                    <i class="feather icon-plus-square "></i>
                                                    Neue hinzufügen
                                                    </a>
                                            </div>
                                            <div class="col-md-2 float-right">
                                                <a type="button" class="btn btn-outline-primary block btn-lg" href="{{ url('/handover_details') }}">
                                                    <i class="feather icon-server   "></i>
                                                    Einzelansicht
                                                    </a>
                                            </div>
                                    </div>
                                        <!-- Modal -->
                                    
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th Scope="col">ID</th>
                                                    <th scope="col">Person Responsibles</th>
                                                    <th scope="col">Items</th>
                                                    <th scope="col">Date</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">Aktion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data as $item)
                                                <tr>
                                                    <th scope="row">{{ $item->id }}</th>
                                                    <td class="p-1">
                                                        <ul class="list-unstyled users-list m-0  d-flex align-items-center">
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Übergabe von: {{ $item->hand_from_name }}  {{ $item->hand_from_lastname }}" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$item->hand_from_image) }}" alt="Avatar" height="30" width="30">
                                                            </li>
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Übergeben: {{ $item->hand_to_name }} {{ $item->hand_to_lastname }} " class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$item->hand_to_image) }}" alt="Avatar" height="60" width="60">
                                                            </li>
                                                         
                                                            @foreach ($employee as $hand_by )
                                                            @if($hand_by->id==$item->handover_by)
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Übergabe durch: {{ $hand_by->name }} {{ $hand_by->lastname }}" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$hand_by->image) }}" alt="Avatar" height="30" width="30">
                                                            </li>
                                                            @endif
                                                            @endforeach
                                                        </ul>
                                                    </td>

                                                    <td>
                                                        @foreach ($handovers as $handover)
                                                        @if($handover->handover_id == $item->handover_id)
                                                    </br>
                                                        {{ $handover->item }} 
                                                    <div class="badge badge-square badge-primary mr-1 mb-1">
                                                        <i class="feather icon-hashtag"></i>
                                                        <span>Seriennummer: {{ $handover->serial_no }}</span>
                                                        <span>Menge: {{ $handover->quantity }}</span>
                                                    </div>
                                              
                                                        @endif
                                                        @endforeach
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($handover->created_at)->isoFormat('DD.MM.YYYY') }}</td>

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
                                                              <a type="button" href="{{url('/handover_destroy').'/'.$item->id}}" class="btn btn-primary">Yes</a>
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
                                                           
                                                        </div>
                                                    </div>
                                            </div>
                                    <!-- End Edit Modal -->

                                                    </td>
                                                    <!-- Operation Section -->
                                                    <td>
                                                        <a type="button" href="{{ url('/handover_print/'.$item->handover_id) }}" class="btn btn-icon btn-icon rounded-circle btn-success mr-1 mb-1" >
                                                        <i class="feather icon-printer"></i>
                                                        </a>
                                                    
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