            <th scope="row">
                @if($item->linked=="linked")
                <i class="feather icon-link sucess"></i>
                @elseif($item->linked=="Linked to")
                <i class="feather icon-link-2 warning"></i>
                @endif
                {{ $item->id }}
            </th>
            <td><a href="{{ url('delivery_note_linked/'.$item->id) }}">{{ $item->delivery_note }}</a></td>
            <td>{{ $item->from }}</td>
            <td>{{ $item->branch }}</td>
            <td>{{ $item->name }} {{ $item->lastname }}</td>
            <td>{{ $item->handover_date }}</td>
            <td>
                <a href="{{ url('delivery_note_image/'.$item->delivery_note) }}">
                    <div class="badge badge-success mr-1 mb-1">
                        <i class="fa fa-image"></i>
                        <span>Bild</span>
                    </div>
                </a>
            
                |
                <a href="{{ url('delivery_note_pdf_read/'.$item->id) }}">
                    <div class="badge badge-success mr-1 mb-1">
                        <i class="fa fa-file-pdf-o"></i>
                        <span>Dokument</span>
                    </div>
                </a>
            </td>
            <td>{{ $item->status }}

                <div class="progress progress-bar-success progress-lg">
                    <div class="progress-bar" role="progressbar" aria-valuenow="{{ $item->progress }}" aria-valuemin="0" aria-valuemax="100" style="width:{{ $item->progress }}%">{{ $item->progress }}%</div>
                </div>
            </td>
            <td>

            <!-- Delete Modal -->
            <a class="" data-toggle="modal" data-target="#delete-pro{{$item->id}}">
            <i class="feather icon-trash danger"></i>
            </a>
            | 

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
                    <a type="button" href="{{url('/delivery_note_destroy').'/'.$item->id}}" class="btn btn-primary">Yes</a>
                    </div>
                </div>
            </div>
            </div>
            </div>
            <!-- End Delete Modal -->


            <!-- Begin: Edit -->
            <a  class="" data-toggle="modal" data-target="#editmodel{{$item->id}}">
            <i class="feather icon-edit"></i>
            </a>
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
                    <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\DeliveryNoteController@update')}}"class="custom-file-upload" enctype="multipart/form-data" >
                        @csrf

                        


                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>

                    </div>
                    </form>
                </div>
            </div>
            </div>
            <!-- End Edit Modal -->

                <!-- Progress Report-->
            <a  data-toggle="modal" data-target="#progress{{$item->id}}">
                <div class="badge badge-success mr-1 mb-1">
                    <i class="fa fa fa-tasks"></i>
                    <span>Paketfortschritt</span>
                </div>
            </a>
            <!-- Modal -->
            <div class="modal fade text-left" id="progress{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            Paketfortschritt:: {{ $item->delivery_note }}
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="post" action="{{ action('App\Http\Controllers\DeliveryNoteController@progress') }}">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" value="{{ $item->id }}" name="id">
                                <select class="form-control" name="progress">
                                    <option selected value="{{ $item->progress }}">{{ $item->progress }}%</option>
                                    <option value="10">10%</option>
                                    <option value="20">20%</option>
                                    <option value="30">30%</option>
                                    <option value="40">40%</option>
                                    <option value="50">50%</option>
                                    <option value="60">60%</option>
                                    <option value="70">70%</option>
                                    <option value="80">80%</option>
                                    <option value="90">90%</option>
                                    <option value="100">100%</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Aktualisieren</button>
                            </div>
                        </form>
                    
                    </div>
                </div>
            </div>
            </div>

            <!-- Progress Report-->
            <a  class="" data-toggle="modal" data-target="#pdf{{$item->id}}">
            <div class="badge badge-primary mr-1 mb-1">
                <i class="fa fa-file-pdf-o"></i>
                <span>PDF Datai</span>
            </div>
                </a>


            <!-- Modal -->
            <div class="modal fade text-left" id="pdf{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        Lieferschein-PDF-Datei hinzufügen: {{ $item->delivery_note }}
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="{{ action('App\Http\Controllers\DeliveryNoteController@pdf') }}" class="custom-file-upload" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" value="{{ $item->id }}" name="id">
                            <input type="file" class="form-control" name="pdf">
                            </div>
                            <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Aktualisieren</button>
                            </div>
                    </form>
                    
                </div>
            </div>
            </div>
            </div>
            <a type="button" href="{{ url('/linked_delivery/'.$item->id) }}" class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1" >
            <i class="feather icon-link    "></i>
            </a>

                @if($item->status=="Verfügbar" || $item->status=="")
                <a type="button" href="{{ url('/delivery_published/'.$item->id) }}" class="btn btn-icon btn-icon rounded-circle btn-success mr-1 mb-1" >
                <i class="feather icon-check"></i>
                </a>
                @else
                <a type="button" href="{{ url('/delivery_unpublish/'.$item->id) }}" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" >
                <i class="feather icon-check"></i>
                </a>
                @endif


            </td>

                <tr>
                    <th Scope="col"> <i class="feather icon-link"></i></th>
                    <th Scope="col">ID</th>
                    <th scope="col">Lieferschein#</th>
                    <th scope="col">Geliefert von</th>
                    <th scope="col">Zweig</th>
                    <th scope="col">Übergabe durch</th>
                    <th scope="col">Datum</th>
                    <th scope="col">Bild & Dokument</th>
                    <th scope="col">Status</th>
                    <th scope="col">Aktion</th>
                </tr>
                <tr>
                    <th> </th>
                    <th scope="row">
                        @if($item->linked=="linked")
                        <i class="feather icon-link sucess"></i>
                        @elseif($item->linked=="Linked to")
                        <i class="feather icon-link-2 warning"></i>
                        @endif
                        {{ $item->id }}
                    </th>
                    <td><a href="{{ url('delivery_note_linked/'.$item->id) }}">{{ $item->delivery_note }}</a></td>
                    <td>{{ $item->from }}</td>
                    <td>{{ $item->branch }}</td>
                    <td>{{ $item->name }} {{ $item->lastname }}</td>
                    <td>{{ $item->handover_date }}</td>
                    <td>
                        <a href="{{ url('delivery_note_image/'.$item->delivery_note) }}">
                            <div class="badge badge-success mr-1 mb-1">
                                <i class="fa fa-image"></i>
                                <span>Bild</span>
                            </div>
                        </a>
                    
                        |
                        <a href="{{ url('delivery_note_pdf_read/'.$item->id) }}">
                            <div class="badge badge-success mr-1 mb-1">
                                <i class="fa fa-file-pdf-o"></i>
                                <span>Dokument</span>
                            </div>
                        </a>
                    </td>
                    <td>{{ $item->status }}
        
                        <div class="progress progress-bar-success progress-lg">
                            <div class="progress-bar" role="progressbar" aria-valuenow="{{ $item->progress }}" aria-valuemin="0" aria-valuemax="100" style="width:{{ $item->progress }}%">{{ $item->progress }}%</div>
                        </div>
                    </td>
                    <td>
        
                    <!-- Delete Modal -->
                    <a class="" data-toggle="modal" data-target="#delete-pro{{$item->id}}">
                    <i class="feather icon-trash danger"></i>
                    </a>
                    | 
        
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
                            <a type="button" href="{{url('/delivery_note_destroy').'/'.$item->id}}" class="btn btn-primary">Yes</a>
                            </div>
                        </div>
                    </div>
                    </div>
                    </div>
                    <!-- End Delete Modal -->
        
        
                    <!-- Begin: Edit -->
                    <a  class="" data-toggle="modal" data-target="#editmodel{{$item->id}}">
                    <i class="feather icon-edit"></i>
                    </a>
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
                            <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\DeliveryNoteController@update')}}"class="custom-file-upload" enctype="multipart/form-data" >
                                @csrf
        
                                
        
        
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
        
                            </div>
                            </form>
                        </div>
                    </div>
                    </div>
                    <!-- End Edit Modal -->
                        <!-- Progress Report-->
                    <a  data-toggle="modal" data-target="#progress{{$item->id}}">
                        <div class="badge badge-success mr-1 mb-1">
                            <i class="fa fa fa-tasks"></i>
                            <span>Paketfortschritt</span>
                        </div>
                    </a>
                    <!-- Modal -->
                    <div class="modal fade text-left" id="progress{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    Paketfortschritt:: {{ $item->delivery_note }}
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form method="post" action="{{ action('App\Http\Controllers\DeliveryNoteController@progress') }}">
                                    @csrf
                                    <div class="modal-body">
                                        <input type="hidden" value="{{ $item->id }}" name="id">
                                        <select class="form-control" name="progress">
                                            <option selected value="{{ $item->progress }}">{{ $item->progress }}%</option>
                                            <option value="10">10%</option>
                                            <option value="20">20%</option>
                                            <option value="30">30%</option>
                                            <option value="40">40%</option>
                                            <option value="50">50%</option>
                                            <option value="60">60%</option>
                                            <option value="70">70%</option>
                                            <option value="80">80%</option>
                                            <option value="90">90%</option>
                                            <option value="100">100%</option>
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Aktualisieren</button>
                                    </div>
                                </form>
                            
                            </div>
                        </div>
                    </div>
                    </div>
        
                    <!-- Progress Report-->
                    <a  class="" data-toggle="modal" data-target="#pdf{{$item->id}}">
                    <div class="badge badge-primary mr-1 mb-1">
                        <i class="fa fa-file-pdf-o"></i>
                        <span>PDF Datai</span>
                    </div>
                        </a>
        
        
                    <!-- Modal -->
                    <div class="modal fade text-left" id="pdf{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                Lieferschein-PDF-Datei hinzufügen: {{ $item->delivery_note }}
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form method="post" action="{{ action('App\Http\Controllers\DeliveryNoteController@pdf') }}" class="custom-file-upload" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                    <input type="hidden" value="{{ $item->id }}" name="id">
                                    <input type="file" class="form-control" name="pdf">
                                    </div>
                                    <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Aktualisieren</button>
                                    </div>
                            </form>
                            
                        </div>
                    </div>
                    </div>
                    </div>
                    <a type="button" href="{{ url('/linked_delivery/'.$item->id) }}" class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1" >
                    <i class="feather icon-link    "></i>
                    </a>
        
                        @if($item->status=="Verfügbar" || $item->status=="")
                        <a type="button" href="{{ url('/delivery_published/'.$item->id) }}" class="btn btn-icon btn-icon rounded-circle btn-success mr-1 mb-1" >
                        <i class="feather icon-check"></i>
                        </a>
                        @else
                        <a type="button" href="{{ url('/delivery_unpublish/'.$item->id) }}" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" >
                        <i class="feather icon-check"></i>
                        </a>
                        @endif
        
        
                    </td>
                </tr>


<!-- Operation Section -->