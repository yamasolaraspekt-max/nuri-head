<div class="row">
    <div class="col-12 mb-1 float-right">
        <form action="{{ action('App\Http\Controllers\DealController@index') }}" method="GET">
            <fieldset>
                <div class="input-group mb-1">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Suche..." aria-describedby="button-addon2">
                    
                    <select name="status" class="form-control ml-1" style="max-width: 150px;">
                        <option value="">Alle Status</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Offen</option>
                        <option value="confirm" {{ request('status') == 'confirm' ? 'selected' : '' }}>Bestätigt</option>
                        <option value="inconfirm" {{ request('status') == 'inconfirm' ? 'selected' : '' }}>Unbestätigt</option>
                    </select>

                    <select name="filter" id="filter" class="form-control ml-1">
                        <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>Alle Angebote</option>
                        <option value="my" {{ request('filter', 'my') == 'my' ? 'selected' : '' }}>Meine Angebote</option>
                    </select>

                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Go</button>
                    </div>
                </div>
            </fieldset>
        </form> 
    </div>  
</div>
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr style="background:white;">
                <th >Auftrags Nr.</th>
                <th >Kunde</th> 
                <th >Produkt</th>  
                <th >Geprüft durch</th>
                <th >Auftragssumme</th> 
                <th>Datum</th> 
                <th >Notizen</th>
                <th >Dokument</th>
                <th >Status</th> 
                <th width="2">Bearbeiten</th>
            </tr>

        </thead>
        <tbody>
            @foreach($data as $item)    
            <tr id="deal-{{ $item->id }}" class="deal-row mb-2" style="background:white; border-bottom: 1px solid rgb(243, 243, 243);">
                <th scope="row">{{ $item->offer_number }}</th> 
                    <td><a href="{{url('new_lead_profile/'.$item->customer_id )}}">
                            {{ $item->name }}  {{ $item->lastname }} <br>
                            <small>
                                    <p class="m-0"><i class="feather icon-map-pin"></i> {{$item->city}}</p>
                                <p class="m-0"><i class="feather icon-clock"></i> {{ \Carbon\Carbon::parse($item->created_at)->isoFormat('DD.MM.YY') }}</p>
                                <p class="m-0"><i class="feather icon-clock"></i> {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}  </p> 
                            </small>
                        </a>
                    </td>
               
                    
                    <td>
                        <div style="justify-items: center;display: flex;align-items: center;justify-content: flex-start;flex-wrap: nowrap;">
                    

                                @php
                                    $services = [
                                        'complete' => 'Komplettlösung',
                                        'montage' => 'Montage',
                                        'product' => 'Produkt',
                                        'plan' => 'Planung',
                                        'maintenance' => 'Wartung',
                                        'repair' => 'Reparatur',
                                        'others' => 'Sonstiges',
                                    ]; 
                                    $service = $services[$item->service] ?? $item->service;  
                                @endphp
                    

                                        @php
                                            // Determine the default image based on gender
                                            $defaultImage = $item->gender === "Male" 
                                                ? asset('images/gender/male.png') 
                                                : asset('images/gender/female.png');

                                            // Determine the actual image to use
                                            $employeeImage = file_exists('images/employee/'.$item->emp_image) && $item->emp_image 
                                                ? asset('images/employee/'.$item->emp_image) 
                                                : $defaultImage;
                                        @endphp 

                                        <div class="d-flex flex-column align-items-center mr-1">
                                            <div class="d-flex align-items-center">
                                                <div class="circle">{{ $item->initial }}</div>
                                                <div class="line"></div> 
                                                <div class="image" data-toggle="tooltip" 
                                                    data-original-title="{{ $item->emp_name && $item->emp_lastname ? $item->emp_name . ' ' . $item->emp_lastname : 'Nicht zugewiesen' }}">
                                                    <img src="{{ $employeeImage }}" alt="Profile"  
                                                    
                                                    class="profile">
                                                </div> 
                                            </div>
                                        <div class="text">{{ $service }}</div>
                                    </div>
                    </td>  
                    
 

                    @php
                        $checkedBy = DB::table('employees')->where('id', $item->checked_by)->select('name', 'lastname')->first();
                        $reviewedBy = DB::table('employees')->where('id', $item->reviewer_id)->select('name', 'lastname')->first();
                    @endphp

                    <td  style="min-width: 140px;"
                            ondblclick="openEmployeeSelector({{ $item->id }}, {{ $item->checked_by ?? 'null' }}, {{ $item->reviewer_id ?? 'null' }})"
                            data-id="{{ $item->id }}"
                            class="select-reviewers">
                        <div class="d-flex flex-column">
                            @if ($checkedBy)
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fa fa-user-circle-o  text-primary mr-1" title="Geprüft durch"></i>
                                    <span>{{ $checkedBy->name }} {{ $checkedBy->lastname }}</span>
                                </div>
                            @else
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fa fa-user-circle-o  text-primary mr-1" title="Geprüft durch"></i>
                                    <span class="text-danger small">Kontrolleur Nicht definiert</span>
                                </div>
                            @endif 
                        </div>
                    </td>

                    <td class="editable-cell price-cell" data-field="price" data-id="{{ $item->id }}">
                        {{ $item->price ?? 'unbekannt' }}
                    </td>

                    <td>
                        <p class="editable-cell sign-date-cell" data-field="sign_date" data-id="{{ $item->id }}">
                            <i class="feather icon-calendar"></i> Signierungsdatum: {{ $item->sign_date ?? 'unbekannt' }}
                        </p>

                        <p class="editable-cell confirmed-date-cell" data-field="confirmed_at" data-id="{{ $item->id }}">
                            <i class="feather icon-calendar"></i> Bestätigt am: {{ $item->confirmed_at ?? 'unbekannt' }}
                        </p>

                        <p class="editable-cell delivered-date-cell" data-field="delivered_at" data-id="{{ $item->id }}">
                            <i class="feather icon-calendar"></i> Geliefert am: {{ $item->delivered_at ?? 'unbekannt' }}
                        </p>
                    </td>




                    <td>
                        <button type="button" class="btn btn-icon rounded-circle btn-warning waves-effect waves-light open-notes-sidebar"
                            data-customer-id="{{ $item->customer_id }}"
                            data-alternative-id="{{ $item->alternative_id }}"
                            data-product-id="{{ $item->product_id }}">
                            <i class="fa fa-sticky-note-o"></i>
                        </button> 

                    </td>
                           
                        <!-- 📤 Upload Button (opens modal) -->
                        <td>
                        <!-- 📤 Upload Button -->
                        <button type="button"
                            class="btn btn-icon rounded-circle btn-primary waves-effect waves-light open-upload-sidebar mt-2"
                            data-customer-id="{{ $item->customer_id }}"
                            data-alternative-id="{{ $item->alternative_id }}"
                            data-product-id="{{ $item->product_id }}"
                            data-item-id="{{ $item->id }}">
                            <i class="fa fa-picture-o"></i>
                        </button>

                        <!-- 🖼️ Gallery Section (inline preview) -->
                        <div class="gallery-container mt-2 d-flex" id="gallery-container-{{ $item->id }}"></div>
                    </td>

                 
                    
                    <td class="editable-cell status-cell" data-field="status" data-id="{{ $item->id }}">
                        <div class="badge badge-primary">
                            @if($item->status == 'confirm') Bestätigt
                            @elseif($item->status == 'inconfirm') Unbestätigt
                            @elseif($item->status == 'open') Offen
                            @else Nicht gesetzt
                            @endif
                        </div>
                    </td>
  
                       
                    <td>

                    <div class="btn-group dropup dropdown-icon-wrapper mr-1 mb-1"> 
                        <button type="button" class="btn   dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="feather icon-menu dropdown-icon"></i>
                        </button>
                        <div class="dropdown-menu"> 


                            @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_update', '=', 'on')->first())
                                
                                <span class="dropdown-item">
                                    <a data-toggle="modal" class="primary" data-target="#skip{{$item->id}}"><i class="feather icon-fast-forward primary" ></i>Überspringen</a>
                                </span>
                                
                            @endif

                                @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_update', '=', 'on')->first())
                                
                                <span class="dropdown-item">
                                    <a data-toggle="modal" class="danger" data-target="#delete-pro{{$item->id}}">
                                    @if($item->deleted_at == Null )<i class="feather icon-trash danger" ></i> Löschen @else <i class="feather icon-refresh-ccw" ></i> Wiederherstellen @endif</a>
                                </span> 
                            @endif
                            @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_delete', '=', 'on')->first())
                                @if($item->status!="Junk")
                                <span class="dropdown-item">
                                    <a data-toggle="modal" class="danger" data-target="#junk{{$item->id}}"><i class="fa fa-power-off danger" ></i> Junk</a>
                                </span>
                                @else
                                    <span class="dropdown-item">
                                    <a data-toggle="modal" class="danger" data-target="#unjunk{{$item->id}}"><i class="fa fa-power-off primary" ></i>Un-Junk</a>
                                </span>
                                @endif
                            @endif  
                        </div>
                    </div>
                    
                        <!-- Delete Modal -->
                        <div class="modal fade" id="delete-pro{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger white">
                                        <h5 class="modal-title" id="myModalLabel120">Daten Löschen</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    @if($item->deleted_at == Null)
                                    <div class="modal-body">
                                        <h5>Aufzeichnung löschen</h5>
                                        <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                        <p>Die Datensatznummer lautet:{{$item->id}}. {{ $item->name }} {{ $item->lastname }} </p>
                                    </div>
                                    <div class="modal-footer">
                                        <a type="button" href="{{url('/deal_delete').'/'.$item->id}}" class="btn btn-danger">Ja</a>
                                    </div>
                                    @else
                                        <div class="modal-body"> 
                                        <p>Möchten Sie diesen Datensatz wiederherstellen?</p>
                                        <p>Die Datensatznummer lautet:{{$item->id}}. {{ $item->name }} {{ $item->lastname }} </p>
                                    </div>
                                    <div class="modal-footer">
                                        <a type="button" href="{{url('/deal_restore').'/'.$item->id}}" class="btn btn-danger">Ja</a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="junk{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger white">
                                        <h5 class="modal-title" id="myModalLabel120">{{ $item->name }} {{ $item->lastname }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <h5>Junk record</h5>
                                        <p>Möchten Sie diese Anfrage als Junk festlegen?</p>
                                        <p>Die Datensatznummer lautet:{{$item->id}} </p>
                                    </div>
                                    <div class="modal-footer">
                                        <a type="button" href="{{url('/deal_junk').'/'.$item->id}}" class="btn btn-danger">Ja</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                                <!-- Unjunk Modal -->
                        <div class="modal fade" id="unjunk{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary white">
                                        <h5 class="modal-title" id="myModalLabel120">{{ $item->name }} {{ $item->lastname }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <h5>Junk record</h5>
                                        <p>Möchten Sie die Junk-Anfrage wiederherstellen?</p>
                                        <p>Die Datensatznummer lautet:{{$item->id}} </p>
                                    </div>
                                    <div class="modal-footer">
                                        <a type="button" href="{{url('/deal_unjunk').'/'.$item->id}}" class="btn btn-primary">Ja</a>
                                    </div>
                                </div>
                            </div>
                        </div>  
                            <!-- //Jump  -->
                            <div class="modal fade" id="skip{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger white">
                                        <h5 class="modal-title" id="myModalLabel120">Optionen überspringen</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('planing.jump')}}" method="post">
                                        @csrf
                                        <div class="modal-body"> 
                                            <p>Möchten Sie zu einer anderen Stufe springen?</p>
                                            <input type="hidden" name="customer_id" value="{{$item->customer_id}}">
                                            <input type="hidden" name="product_id" value="{{$item->product_id}}">
                                            <input type="hidden" name="alternative_id" value="{{$item->alternative_id}}">
                                            <input type="hidden" name="employee_id" value="{{$item->employee_id}}">
                                            <input type="hidden" name="service" value="{{$item->service}}"> 
                                            <select name="project_status" id="" class="form-control">
                                                <option value="offer">Angebote</option>
                                                <option value="deals">Aufträge</option>
                                                <option value="project">Projekt</option>
                                            </select>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit"  class="btn btn-primary">OK</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td> 
                </tr>  
            @endforeach
        </tbody>
    </table>
</div>

{{ $data->appends(request()->input())->links() }}

