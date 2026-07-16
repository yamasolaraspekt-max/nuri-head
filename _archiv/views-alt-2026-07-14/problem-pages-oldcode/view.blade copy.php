    <div class="row" id="table-hover-animation">
                    <div class="col-12">
                        <div class="card"> 
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row">

                                       <div class="col-9 ">


                                            <fieldset>
                                                <form action="{{ route('problem.view') }}" method="GET">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                                                <i class="fa fa-filter"></i>
                                                            </button>
                                                            <div class="dropdown-menu">
                                                             <a class="dropdown-item" href="{{ route('problem.view', ['filter' => 'mine']) }}">Meine Tickets</a>
                                                            <a class="dropdown-item" href="{{ route('problem.view', ['filter' => 'open']) }}">Offene Tickets</a>
                                                            <a class="dropdown-item" href="{{ route('problem.view', ['filter' => 'progress']) }}">In Bearbeitung</a>
                                                            <a class="dropdown-item" href="{{ route('problem.view', ['filter' => 'end']) }}">Abgeschlossene Tickets</a>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item" href="{{ route('problem.view', ['sort_field' => 'date', 'sort_order' => 'asc']) }}">Nach Datum sortieren</a>
                                                            <a class="dropdown-item" href="#" onclick="filterByDate()">Nach Datum filtern</a>

                                                            </div>
                                                        </div>
                                                        <input type="text" name="search" class="form-control" placeholder="Suchbegriff...">
                                                        <div class="input-group-append">
                                                            <button class="btn btn-primary" type="submit">Suchen</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </fieldset>


                                        </div>
                                        <div class="col-2">
                                            <a type="button" class="btn btn-outline-primary square mr-1 mb-1 waves-effect waves-light" href="{{ url('problem_create')}}">Erstellen</a> 
                                        </div>

                                    </div> 
                                 
 
                                    <div class="table-responsive">
                                        <table class="table">
                                           <thead>
                                                @php
                                                    $sortField = request('sort_field');
                                                    $sortOrder = request('sort_order') === 'asc' ? 'desc' : 'asc';

                                                    $getSortIcon = function ($field) {
                                                        if (request('sort_field') === $field) {
                                                            return request('sort_order') === 'asc'
                                                                ? '<i class="feather icon-chevron-up"></i>'
                                                                : '<i class="feather icon-chevron-down"></i>';
                                                        }
                                                        return '';
                                                    };
                                                @endphp
                                                <tr>
                                                    <th>#</th>

                                                    <th>
                                                        <a href="{{ route('problem.view', array_merge(request()->all(), ['sort_field' => 'ticket_no', 'sort_order' => $sortOrder])) }}">
                                                            Ticket-Nr. {!! $getSortIcon('ticket_no') !!}
                                                        </a>
                                                    </th>

                                                    <th>
                                                        <a href="{{ route('problem.view', array_merge(request()->all(), ['sort_field' => 'new_leads.name', 'sort_order' => $sortOrder])) }}">
                                                            Kundeninfo {!! $getSortIcon('new_leads.name') !!}
                                                        </a>
                                                    </th>

                                                    <th>
                                                        <a href="{{ route('problem.view', array_merge(request()->all(), ['sort_field' => 'error_type', 'sort_order' => $sortOrder])) }}">
                                                            Problem & Status {!! $getSortIcon('error_type') !!}
                                                        </a>
                                                    </th>

                                                    <th>
                                                        <a href="{{ route('problem.view', array_merge(request()->all(), ['sort_field' => 'article_groups.article_group', 'sort_order' => $sortOrder])) }}">
                                                            Produkt {!! $getSortIcon('article_groups.article_group') !!}
                                                        </a>
                                                    </th>

                                                    <th>
                                                        <a href="{{ route('problem.view', array_merge(request()->all(), ['sort_field' => 'date', 'sort_order' => $sortOrder])) }}">
                                                            Melde Info {!! $getSortIcon('date') !!}
                                                        </a>
                                                    </th>

                                                    <th>Zuständig</th> 
                                                    <th>Aktion</th> 
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach($data as $item)
                                                <tr> 
                                                    <th scope="row">{{$item->id}}</th>
                                                    <td>{{$item->ticket_no}}  
                                                        @php
                                                            $errorTypeLabels = [
                                                                'complaint' => 'REKLAMATION',
                                                                'emergency_service' => 'NOTDIENST',
                                                                'repair' => 'REPARATUR',
                                                                'maintenance' => 'WARTUNG',
                                                                'malfunction' => 'STÖRUNG',
                                                                'installation' => 'INSTALLATION',
                                                                'configuration_error' => 'KONFIGURATION',
                                                                'system_outage' => 'SYSTEMAUSFALL',
                                                                'security_issue' => 'SICHERHEITSPROBLEM',
                                                                'user_error' => 'BEDIENUNGSFEHLER',
                                                                'network_problem' => 'NETZWERKFEHLER',
                                                                'software_bug' => 'SOFTWAREFEHLER',
                                                                'hardware_defect' => 'HARDWAREFEHLER',
                                                                'spare_part_request' => 'ERSATZTEILANFRAGE',
                                                                'timeout' => 'ZEITÜBERSCHREITUNG',
                                                                'communication_failure' => 'KOMMUNIKATIONSPROBLEM',
                                                                'power_outage' => 'ENERGIEAUSFALL',
                                                                'update_failure' => 'UPDATEFEHLER',
                                                                'access_issue' => 'ZUGRIFFSPROBLEM',
                                                                'other' => 'SONSTIGES',
                                                            ];
                                                        @endphp

                                                        <p  class="m-0 p-0">
                                                            <div class="badge badge-pill badge-primary">
                                                                {{ $errorTypeLabels[$item->error_type] ?? strtoupper($item->error_type) }}
                                                            </div>

                                                        </p>
                                                 
                                                      <p class="m-0 p-0">
                                                          @if($item->repeated)
                                                            <div class="badge badge-pill  badge-warning ">Wiederholtes</div>
                                                            @endif
                                                      </p>
                                                    </td>
                                                    <td>
                                                        <a href="{{ url('problem/profile/'.$item->id)}} "  class="customer_names">
                                                            <strong>{{$item->firma}}</strong>
                                                            <strong><p class="m-0 p-0">{{$item->name}} {{$item->lastname}}</p> </strong>
                                                            <p class="m-0 p-0">{{$item->street}} {{$item->postcode}} {{ $item->city}}</p>  
                                                            <p class="m-0 p-0"><i class="feather icon-phone"></i> {{$item->phone}} </p>  
                                                            <p class="m-0 p-0"><i class="feather icon-mail"></i> {{$item->email}} </p>     
                                                        </a>
                                                        <p class="m-0 p-0">  Qualle: {{$item->source}} </p>    
                                                        Registiert durch: {{ DB::table('employees')->select('name','lastname')->where('id','=', $item->start_user)->pluck('name')->first() }} 

                                                        
                                                    </td>
                                                    <td>
                                                          <p class="m-0 p-0">
                                                               @if($item->status == "offen")
                                                                    <div class="badge badge-pill  badge-danger "
                                                                        data-toggle="tooltip"
                                                                        data-placement="top"
                                                                        title="Erfasst durch: {{ \App\Models\Employee::find($item->start_user)?->name }} {{ \App\Models\Employee::find($item->start_user)?->lastname }}">
                                                                        Offen
                                                                    </div>

                                                                @elseif($item->status == "process")
                                                                    <div class="badge badge-pill  badge-warning "
                                                                        data-toggle="tooltip"
                                                                        data-placement="top"
                                                                        title="In Bearbeitung von: {{ \App\Models\Employee::find($item->progress_user)?->name }} {{ \App\Models\Employee::find($item->progress_user)?->lastname }}">
                                                                        In Bearbeitung
                                                                    </div>

                                                                @elseif($item->status == "end")
                                                                    <div class="badge badge-pill  badge-success "
                                                                        data-toggle="tooltip"
                                                                        data-placement="top"
                                                                        title="Abgeschlossen von: {{ \App\Models\Employee::find($item->end_user)?->name }} {{ \App\Models\Employee::find($item->end_user)?->lastname }}">
                                                                        Beendet
                                                                    </div>
                                                                @endif

                                                            </p> 

                                                        <p class="m-0 p-0">
                                                            <a   data-toggle="modal" data-target="#problem{{$item->id}}">
                                                                <div class="badge badge-danger  ">
                                                                    <i class="feather icon-alert-octagon"></i>
                                                                    <span>Problembeschreibung</span>
                                                                </div>
                                                            </a>
                                                               @if($item->solution)
                                                              <a  data-toggle="modal" data-target="#solution{{$item->id}}">
                                                                <div class="badge badge-primary  ">
                                                                    <i class="feather icon-alert-octagon"></i>
                                                                    <span>Lösungsbeschreibung</span>
                                                                </div>
                                                            </a>
                                                            @endif
                                                        </p> 
                                                        <small> 
                                                            <p class="m-0 p-0"><i class="fa fa-hourglass-half" ></i> Offen seit: {{ \Carbon\Carbon::parse($item->date)->diffForHumans() }}</p>
                                                            <p class="m-0 p-0"> <i class="feather icon-clock"></i> Latzte Änderung: {{ \Carbon\Carbon::parse($item->updated_at) }}</p> 
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <p> {{ $item->product}}</p> 
                                                        
                                                      @foreach ($error as $proE)
                                                            @if($proE->problem_id == $item->id)
                                                                @php
                                                                    $tooltipHtml = "
                                                                        <strong>Fehlercode:</strong> {$proE->error_code}<br>
                                                                        <strong>Typ:</strong> {$proE->problem_types}<br>
                                                                        <strong>Produkt:</strong> {$proE->product}<br>
                                                                        <strong>Artikel:</strong> {$proE->article_name}<br><hr>
                                                                        <strong>Grund:</strong><br>{$proE->reason}<br><hr>
                                                                        <strong>Lösung:</strong><br>{$proE->solution}
                                                                    ";
                                                                @endphp
                                                                <a  >
                                                                    <div class="badge badge-pill badge-warning mr-1 custom-error-tooltip"
                                                                        data-preview-html="{{ strip_tags($tooltipHtml, '<br><strong><hr>') }}">
                                                                        {{ $proE->error_code }}
                                                                    </div>
                                                                </a>
                                                            @endif
                                                        @endforeach



                                                                 <!-- Modal -->
                                                            <div class="modal fade text-left" id="problem{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            Problembeschreibung
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <a href="{{ url('/new_lead_profile/'.$item->cid)}} "  class="customer_names">
                                                                                <strong>{{$item->firma}}</strong>
                                                                                <strong><p class="m-0 p-0">{{$item->name}} {{$item->lastname}}</p> </strong>
                                                                                <p class="m-0 p-0">{{$item->street}} {{$item->postcode}} {{ $item->city}}</p>  
                                                                                <p class="m-0 p-0"><i class="feather icon-phone"></i> {{$item->phone}} </p>  
                                                                                <p class="m-0 p-0"><i class="feather icon-mail"></i> {{$item->email}} </p>     
                                                                            </a>
                                                                            <p><code>Ticket Nr.: {{ $item->ticket_no}}</code> <code>Verfasser: {{ $item->fname}} {{ $item->flastname}}</code>
                                                                                 <code>Produkt: {{ $item->product}} </code>  <code>Erstellt am: {{ \Carbon\Carbon::parse($item->date)->isoFormat('DD.MM.YYYY')}} </code>
                                                                            </p>
                                                                              @foreach ($error as $proE)
                                                                                    @if($proE->problem_id==$item->id)
                                                                                    <a href="{{ url('error?search='.$proE->error_code) }}">
                                                                                        <div class="badge badge-pill  badge-warning mr-1  ">{{ $proE->error_code }} - {{ $proE->problem_types }}</div>
                                                                                    </a>
                                                                                    @endif
                                                                                @endforeach
                                                                            <hr/> 
                                                                            <p>{!! $item->problem !!}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="modal fade text-left" id="solution{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                             Lösungsbeschreibung
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">

                                                                             <a href="{{ url('/new_lead_profile/'.$item->cid)}} "  class="customer_names">
                                                                                <strong>{{$item->firma}}</strong>
                                                                                <strong><p class="m-0 p-0">{{$item->name}} {{$item->lastname}}</p> </strong>
                                                                                <p class="m-0 p-0">{{$item->street}} {{$item->postcode}} {{ $item->city}}</p>  
                                                                                <p class="m-0 p-0"><i class="feather icon-phone"></i> {{$item->phone}} </p>  
                                                                                <p class="m-0 p-0"><i class="feather icon-mail"></i> {{$item->email}} </p>     
                                                                            </a>
                                                                            <p><code>Ticket Nr.: {{ $item->ticket_no}}</code> <code>Verfasser: {{ $item->fname}} {{ $item->flastname}}</code>
                                                                                 <code>Produkt: {{ $item->product}} </code>  <code>Erstellt am: {{ \Carbon\Carbon::parse($item->date)->isoFormat('DD.MM.YYYY')}} </code>
                                                                                  <code>Status: {{ $item->status}}</code>
                                                                                  <code>Beendet von: {{ $item->end_user}}</code>
                                                                            </p>
                                                              
                                                                            @if($item->solution)
                                                                            <p>{!! $item->solution !!}</p>
                                                                            @else
                                                                            <p>Das Problem ist noch nicht gelöst</p>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        
                                                    </td>

                                                    <td>
                                                        <small>
                                                              <p  class="m-0 p-0">Verfasser: {{ $item->fname}} {{ $item->flastname}}</p>
                                                        </small>
                                                       <small>
                                                        <p class="m-0 p-0">Erstellt am: 
                                                            {{ $item->date ? \Carbon\Carbon::parse($item->date)->isoFormat('DD.MM.YYYY') : '-' }}
                                                        </p>
                                                        @if($item->date)
                                                            <p class="m-0 p-0">
                                                                <div class="badge badge-pill badge-danger mr-1">
                                                                    {{ \Carbon\Carbon::parse($item->date)->diffForHumans() }}
                                                                </div>
                                                            </p>
                                                        @endif
                                                    </small>

                                                    <small>
                                                        <p class="m-0 p-0">Prozessdatum: 
                                                            {{ $item->progress_date ? \Carbon\Carbon::parse($item->progress_date)->isoFormat('DD.MM.YYYY') : '-' }}
                                                        </p>
                                                        @if($item->progress_date)
                                                            <p class="m-0 p-0">
                                                                <div class="badge badge-pill badge-warning">
                                                                    {{ \Carbon\Carbon::parse($item->progress_date)->diffForHumans() }}
                                                                </div>
                                                            </p>
                                                        @endif
                                                    </small>

                                                    <small>
                                                        <p class="m-0 p-0">Ticket-Enddatum: 
                                                            {{ $item->end_date ? \Carbon\Carbon::parse($item->end_date)->isoFormat('DD.MM.YYYY') : '-' }}
                                                        </p>

                                                        @if($item->end_date)
                                                            <p class="m-0 p-0">
                                                                <div class="badge badge-pill badge-warning">
                                                                    {{ \Carbon\Carbon::parse($item->end_date)->diffForHumans() }}
                                                                </div>
                                                            </p>
                                                            <p class="m-0 p-0">
                                                                <div class="badge badge-pill badge-warning">
                                                                    {{ \Carbon\Carbon::parse($item->end_date)->diffInDays(\Carbon\Carbon::parse($item->date)) }} Tage
                                                                </div>
                                                                <div class="badge badge-pill badge-warning">
                                                                    {{ \Carbon\Carbon::parse($item->end_date)->diffInHours(\Carbon\Carbon::parse($item->date)) }} Std
                                                                </div>
                                                            </p>
                                                        @endif
                                                    </small>

                                                      
                                                    </td>
                                            
                                                    <td> 
                                                       @php
                                                            $maxVisible = 4;
                                                            $responsiblesForItem = $responsible->where('problem_id', $item->id);
                                                            $visibleResponsibles = $responsiblesForItem->take($maxVisible);
                                                            $hiddenResponsibles = $responsiblesForItem->slice($maxVisible);
                                                        @endphp

                                                        <ul class="list-unstyled users-list m-0 d-flex align-items-center">
                                                            @foreach ($visibleResponsibles as $resp)
                                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom"
                                                                    data-original-title="{{ $resp->rname }} {{ $resp->rlastname }}" class="avatar pull-up">
                                                                    <img class="media-object rounded-circle"
                                                                        src="{{ asset('images/employee/'.$resp->rimage) }}"
                                                                        alt="Avatar" height="30" width="30">
                                                                </li>
                                                            @endforeach

                                                            @if ($hiddenResponsibles->count())
                                                                <li class=" ">
                                                                    <a href="javascript:void(0);" onclick="showResponsibleModal({{ $item->id }})"
                                                                    class=""
                                                                    style="width: 40px;height: 28px;display: flex;align-items: center;justify-content: center;padding: 20px;border: 1px solid;border-radius: 50%;">
                                                                        +{{ $hiddenResponsibles->count() }}
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        </ul>
  

                                                        <div class="modal fade" id="responsibleModal" tabindex="-1" role="dialog" aria-labelledby="responsibleModalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-lg" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Verantwortliche Personen</h5>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <table class="table table-hover table-striped" id="responsibleTable">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Bild</th>
                                                                                        <th>Name</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody id="responsibleTableBody">
                                                                                    {{-- dynamically filled --}}
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                    </td>
 
                                                     <td style="width: 50px;"> 
                                                        <div class="custom-dropdown">
                                                        <!-- Trigger -->
                                                        <button onclick="toggleMenu(this)">
                                                            <!-- 3-dot vertical icon -->
                                                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6h.01M12 12h.01M12 18h.01"/>
                                                            </svg>
                                                        </button>

                                                        <!-- Menu -->
                                                        <div class="custom-dropdown-menu">
                                                            {{-- Edit --}}
                                                            @if(DB::table('user_rolls')->where('user_id', auth()->user()->name)->where('item_id', 'Problem')->where('is_add', 'on')->first())
                                                                <a href="{{ url('problem_edit/'.$item->id) }}">
                                                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13h3l9-9-3-3-9 9v3z"/>
                                                                </svg>
                                                                Bearbeiten
                                                                </a>
                                                            @endif

                                                            {{-- Status Change --}}
                                                            @if(DB::table('user_rolls')->where('user_id', auth()->user()->name)->where('item_id', 'Problem')->where('is_update', 'on')->first())
                                                                <div style="border-top:1px solid #eee; margin:4px 0;"></div>
                                                                @if($item->status == 'offen')
                                                                <a href="{{ url('/problem_progress/'.$item->id) }}">
                                                                    <i class="feather icon-alert-circle text-primary mr-2"></i> In Klärung
                                                                </a>
                                                                <a href="{{ url('/problem_close/'.$item->id) }}">
                                                                    <i class="feather icon-check text-success mr-2"></i> Beendet
                                                                </a>
                                                                @elseif($item->status == 'process')
                                                                <a href="{{ url('/problem_open/'.$item->id) }}">
                                                                    <i class="feather icon-slash text-danger mr-2"></i> Offen
                                                                </a>
                                                                <a href="{{ url('/problem_close/'.$item->id) }}">
                                                                    <i class="feather icon-check text-success mr-2"></i> Beendet
                                                                </a>
                                                                @elseif($item->status == 'end' || $item->status = 'beendet')
                                                                <a href="{{ url('/problem_progress/'.$item->id) }}">
                                                                    <i class="feather icon-alert-circle text-primary mr-2"></i> In Klärung
                                                                </a>
                                                                <a href="{{ url('/problem_open/'.$item->id) }}">
                                                                    <i class="feather icon-slash text-danger mr-2"></i> Offen
                                                                </a>
                                                                @endif
                                                            @endif

                                                            {{-- Delete --}}
                                                            @if(DB::table('user_rolls')->where('user_id', auth()->user()->name)->where('item_id', 'Problem')->where('is_delete', 'on')->first())
                                                                <div style="border-top:1px solid #eee; margin:4px 0;"></div>
                                                                <button data-toggle="modal" data-target="#delete-pro{{ $item->id }}">
                                                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7L18 21H6L5 7m5 4v6m4-6v6M9 7V4h6v3"/>
                                                                </svg>
                                                                Löschen
                                                                </button>
                                                            @endif
                                                        </div>
                                                        </div>

                                                        {{-- Delete Modal --}}
                                                        <div class="modal fade" id="delete-pro{{ $item->id }}" tabindex="-1" role="dialog">
                                                            <div class="modal-dialog modal-dialog-scrollable">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Aufzeichnung löschen</h5>
                                                                        <button type="button" class="btn-close" data-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                        <p>Datensatznummer: {{ $item->id }}</p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <a href="{{ url('/problem_destroy/'.$item->id) }}" class="btn btn-danger">Ja, löschen</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
 
                                                    </td> 

                                                </tr>
                                               
                                                @endforeach
                                   
                                            </tbody>
                                        </table>

                                        <!-- Image Modal: start  -->
                                        <div class="modal fade" id="galleryModal" tabindex="-1" role="dialog">
                                            <div class="modal-dialog modal-xl" role="document">
                                                <div class="modal-content p-2">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Ticket Gallery</h5>
                                                        <button type="button" class="btn btn-outline-primary round  waves-effect waves-light" data-dismiss="modal">X</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        
                                                        <form id="dropzoneForm" class="dropzone" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="ticket_id" id="ticket_id">
                                                        <input type="hidden" name="stage" value="upload">
                                                        </form>

                                                        <div class="row mt-3" id="gallery">
                                                        <!-- Loaded via AJAX -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Preview Modal -->
                                        <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                                <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Preview</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center" id="previewContent">
                                                    <!-- Dynamic content goes here -->
                                                </div>
                                                </div>
                                            </div>
                                            </div>

                                       <!-- Image Modal: end  -->
                                    </div>
                                 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Table head options end -->
                {{$data->links()}}