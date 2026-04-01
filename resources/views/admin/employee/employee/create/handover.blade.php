    <div class="row">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th Scope="col">ID</th>
                        <th scope="col">Element</th>
                        <th scope="col">Übergaben</th>
                        <th scope="col">Menge</th>
                        <th scope="col">Bild</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aktion</th>
                        <th scope="col">Vorgang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($handover as $item)
                    <tr>
                        <th scope="row">1</th>
                        <td>{{ $item->item }} 
                        </br>
                            <div class="badge badge-square badge-primary mr-1 mb-1">
                                <i class="feather icon-hashtag"></i>
                                <span>Seriennummer: {{ $item->serial_no }}</span>
                            </div>
                        </br>

                            <div class="badge badge-square badge-primary mr-1 mb-1">
                                <i class="feather icon-facebook"></i>
                                <span>Artikelnummer: {{ $item->article_no }}</span>
                            </div>

                        
                        </td>
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
                
                        <td>{{ $item->quantity }}</td>
                        <td>
                            <!-- Image Modal -->
                            <a type="button" class="btn btn-icon btn-icon  mr-1 mb-1" data-toggle="modal" data-target="#image{{$item->id}}">
                            <div class="avatar mr-1 ">
                                <img src="{{ asset('images/asset/'.$item->image) }}" alt="avtar img holder" height="32" width="32">
                            </div>
                            </a>

                                <!-- Modal -->
                                <div class="modal fade text-left" id="image{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                {{ $item->item }} | {{ $item->serial_no }}
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body" style="text-align: center;">
                                                <img src="{{ asset('images/asset/'.$item->image) }}" alt="avtar img holder" height="200" width="200">
                                            </div>
                                            <div class="modal-footer">
                                            {!! $item->purpose !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Image Modal -->
                        
                        </td>
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
