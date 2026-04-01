 

 <div class="card-content">
    <div class="table-responsive mt-1">
        <table class="table   mb-0">
            <thead>
                <tr>    
                    <th>Zweck</th>    
                    <th>Anfrage durch</th>      
                    <th>Erstellt von</th>   
                    <th>Anfrage an</th>  
                    <th>Status</th>
                    <th>Bearbeitung</th>
                </tr>
            </thead>
            <tbody>
                @if(count($leave) > 0)
                    @foreach($leave as $item)
                        <tr style="border-bottom: 10px solid #f8f8f8; ">    
                            <td>
                                <p class="m-0 p-0">Urlaubsanfrage </p>
                                    <p class="m-0 p-0">  <small><i class="feather icon-calendar primary"></i> <strong>{{ $item->start_date }}</strong> - <strong>{{ $item->end_date }}</strong></small> </p>
                                     @if($item->old_start)
                                    <p class="m-0 p-0"><small class="warning"><i class="fa fa-calendar-times-o warning"></i>   <strong>{{ $item->old_start }}</strong> - <strong>{{ $item->old_end }}</strong></small> </p> 
                                     @endif
                               
                            </td>
                            <td>
                                <span data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title=" {{ $item->emp_name }} {{ $item->emp_lastname }} " class="avatar pull-up">
                                    <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$item->emp_image) }}"alt="Avatar" height="30" width="30">
                                </span>
                                {{ $item->emp_lastname }} {{ $item->emp_name }} 
                            </td>

                            <td>
                                <span data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title=" {{ $item->cname }} {{ $item->clastname }} " class="avatar pull-up">
                                    <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$item->cimage) }}"alt="Avatar" height="30" width="30">
                                </span>
                                {{ $item->clastname }} {{ $item->cname }} 
                            </td>

                             <td>
                                <span data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title=" {{ $item->rname }} {{ $item->rlastname }} " class="avatar pull-up">
                                    <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$item->rimage) }}"alt="Avatar" height="30" width="30">
                                </span>
                                {{ $item->rlastname }} {{ $item->rname }} 
                            </td>
                            <td>
                                  
                                    @if($item->approved == "Yes")
                                    <span class="badge badge-primary badge-pill"> 
                                        Genehmigt
                                    </span>
                                    @else
                                        <span class="badge badge-warning badge-pill"> 
                                        Ausstehend
                                        </span> 
                                    @endif


                                      @if($item->status == 'accept')
                                    <span class="badge badge-primary badge-pill"> 
                                       Akzeptiert
                                    </span>
                                     @else
                                     {{ $item->status }}
                                    @endif

                                    
                            </td>
                            <td>

                                 <button class="btn btn-success  check-leave" 
                                 data-id="{{ $item->leave_id }}" data-start-date="{{$item->start_date}}" data-end-date="{{$item->end_date}}" data-employee-id="{{$item->emp_id}}" >
                                        Konflikt prüfen  
                                </button> 

                        
                                @if( $item->request_answer !='accept')
                                   
                                        <button class="btn btn-success accept-btn" 
                                                data-leave-id="{{ $item->leave_id   }}" 
                                                data-employee-id="{{ $item->emp_id }}">
                                            Akzeptieren
                                        </button>                                     
                                       <button class="btn btn-danger reject-btn" 
                                            data-leave-id="{{ $item->leave_id }}" 
                                            data-start="{{ $item->start_date }}" 
                                            data-end="{{ $item->end_date }}"       
                                            data-employee-id="{{ $item->emp_id }}">
                                            Ablehnen
                                        </button>

                                                      
                                @endif
                            </td>


                        </tr>  
                    
                    @endforeach 
                @else
                    <p class="text-muted">No notifications found.</p>
                @endif

               
            </tbody>
        </table>
    </div>

  
</div>


<div class="mt-2">
    {{ $leave->links('pagination::bootstrap-4') }}
</div>
