 
 <style>
    .bd {
       border-bottom: 1px solid #e7e0e0 !important;
       
    }
    .select2-selection {
       border: 2px !important;
        width: 100% !important;
        background: #efeded !important;
        height: 40px !important;
        font-size: 20px;
        align-content: center;
        font-weight: bolder;
    }
    

 </style>
                    <section>
                        <div class="row">
                            <div class="col-12">
                                <div class="cards"> 
                                    <div class="card-content">
                                        <div class="card-body"> 
                                            <div class="row">
                                            <div class="col-lg-12 col-md-12">
                                                    <div class="progress-container">
                                                        @foreach ($phases as $progress) 
                                                            @foreach($stages as $active) 
                                                                @if( $active->customer_id == request()->customer_id)
                                                                    <div class="progress-item @if($active->phase_id == $progress->id && $active->status=='Complete') active @endif">{{ $progress->phase_name }} @if($active->phase_id == $progress->id && $active->status=="Complete") <i class="feather icon-check"></i> @endif</div>
                                                                @endif 
                                                            @endforeach 
                                                        @endforeach
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </section>

                    <section>
                        <div class="row">
                            <div class="col-12">
                                <div class="cards">
                                    <div class="card-content">
                                        <div class="card-body " style=" display: flex !important;  flex-wrap: nowrap;  justify-content: center;">
                                            <div class="3d"> 
                                                <button type="button" class="btn    mr-1 mb-1 waves-effect waves-light button-plan" disabled="">
                                                <span>BELEGUNGS TOOL </span>
                                                </button>
                                            </div>
                                            <div class="free-plan">
                                                <button type="button" class="btn mr-1 mb-1 waves-effect waves-light button-plan" disabled="">
                                                    <span>FREI PLANEN</span>
                                                </button> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                
            <section>
              @foreach ($phases as $phase)
                <div class="accordion" id="accordionExample{{ $loop->index }}">
                    <div class="card">
                        <div class="card-header" id="heading{{ $loop->index }}" style="padding-bottom:21px">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapse_card" type="button" data-toggle="collapse" data-target="#collapse{{ $loop->index }}" aria-expanded="true" aria-controls="collapse{{ $loop->index }}">
                                    <h4 style="font-weight: bold;" class="primary">
                                        <i class="icon-toggle feather icon-plus"></i> {{ $phase->phase_name }}
                                    </h4>
                                </button>
                            </h5>
                        </div>

                        <div id="collapse{{ $loop->index }}" class="collapse" aria-labelledby="heading{{ $loop->index }}" data-parent="#accordionExample{{ $loop->index }}">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover-animation mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Bezeichnung</th>
                                                <th scope="col">Erlidigt!</th>
                                                <th scope="col">Datum</th>
                                                <th>Verfasser</th>
                                                <th scope="col">Verantwortlich</th>
                                                <th scope="col">Ausführende</th>
                                                <th scope="col">Dokument</th>
                                                <th scope="col">Notiz</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tasks as $task)
                                                @if($task->phase_id == $phase->id)
                                                <tr>
                                                    <td></td>
                                                    <td>
                                                        <strong> {{ $task->initial }}. {{ $task->title }}</strong>: {{ $task->description }}
                                                        <div class="row">  
                                                            @foreach ($activities as $sub_task) 
                                                                @if($sub_task->task_id == $task->id)

                                                                  @php
                                                                    // Find the corresponding "sub" task in $to_does
                                                                    $foundSubTodo = $to_does->firstWhere(function($do) use ($sub_task, $phase) {
                                                                        return $do->phase_id == $phase->id 
                                                                            && $do->activities_id == $sub_task->task_id // Match the activity (task) ID
                                                                            && $do->sub_task_id == $sub_task->id        // Match the sub-task ID
                                                                            && $do->type == 'sub';                      // Ensure it's a sub-task
                                                                    });

                                                                    // Check if the sub-task is found
                                                                  
                                                                @endphp
 
                                                                    <table class="table mb-1"> 
                                                                        <tbody>
                                                                            <tr>
                                                                                <th class="bd" >
                                                                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                                                                        <input type="checkbox" class="doneSubTask" 
                                                                                            name="doneSubTask"
                                                                                            value="false"
                                                                                            data-customer="{{ request()->customer_id }}"
                                                                                            data-product="{{ request()->product_id }}"
                                                                                            data-address="{{ request()->address_no }}"
                                                                                            data-phase="{{ $phase->id }}"
                                                                                            data-task="{{ $task->id }}"
                                                                                            data-sub-task="{{ $sub_task->id }}"
                                                                                            {{ $foundSubTodo && $foundSubTodo->done == true ? 'checked disabled' : '' }}>
                                                                                        <span class="vs-checkbox">
                                                                                            <span class="vs-checkbox--check">
                                                                                                <i class="vs-icon feather icon-check"></i>
                                                                                            </span>
                                                                                        </span>
                                                                                        <span class="">Erlidigt</span>
                                                                                    </div> 
                                                                                    <h4 class="primary"><strong>{{ $sub_task->id }}. {{ $sub_task->task_title }}</strong> </h4><br>
                                                                                    <p>{{ $sub_task->description }}</p>
                                                                                </th>  
                                                                                <th>
                                                                                @if($foundSubTodo && $foundSubTodo->done == true)
                                                                                     <tr>  
                                                                                        <td style="display: flex; flex-wrap: nowrap; align-content: center;  align-items: center;    border-bottom: 1px solid #95c949; "> 
                                                                                            <h4 class="primary mr-1"><strong>Leistungsdetails: </strong>
                                                                                            <small> {{ \Carbon\Carbon::parse($foundSubTodo->done_date)->isoFormat('DD.MM.YY')}} <br> <small>{{ \Carbon\Carbon::parse($foundSubTodo->done_date)->diffForHumans()}} </small></small>

                                                                                            </h4>

                                                                                            <label for="contact_person_under">Verfasser</label> 
                                                                                            <div class="avatar mr-1">
                                                                                                <img src="{{ asset('images/employee/'.$foundSubTodo->cimage)}}" alt="" height="32" width="32" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ $foundSubTodo->cname }} {{ $foundSubTodo->clastname }}" id="contact_person_under"> 
                                                                                            </div>  

                                                                                            <label for="responsible_under">Verantwortlich</label> 
                                                                                            <div class="avatar mr-1">
                                                                                                <img src="{{ asset('images/employee/'.$foundSubTodo->rimage)}}" alt="" height="32" width="32" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ $foundSubTodo->rname }} {{ $foundSubTodo->rlastname }}" id="responsible_under"> 
                                                                                            </div> 
                                                                                            <label for="outside_service_under">Ausführende</label>
                                                                                    
                                                                                           <div class="avatar mr-1">
                                                                                                @if($foundSubTodo->outside_service != Null)
                                                                                                <img src="{{ asset('images/employee/'.$foundSubTodo->osimage)}}" alt=" " height="32" width="32" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ $foundSubTodo->osname}} {{ $foundSubTodo->oslastname}} "> 
                                                                                                @elseif($foundSubTodo->outside_company != Null)
                                                                                                <img src="{{ asset('images/gender/users.png')}}" alt=" " height="32" width="32" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ $foundSubTodo->company_name}} - {{ $foundSubTodo->admin_name}} ">  
                                                                                                @endif
                                                                                            </div> 
                                                                                            <label for="outside_service_under">Dokument</label>
                                                                                        
                                                                                            <button type="button" class="btn waves-effect waves-light documentModalButton" data-toggle="modal" data-target="#documentModal" data-url="{{ asset('task/documents/') }}">
                                                                                                    <i class="feather icon-file primary" style="font-size:25px"></i>
                                                                                            </button> 
                                                                                        </td>
                                                                                        <hr>
                                                                                    </tr>
                                                                                    @endif

                                                                                </th>
                                                                               
                                                                            </tr> 
                                                                           
                                                                             
                                                                        </tbody>
                                                                    </table>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </td>

                                                    @php
                                                        // Find the corresponding "main" task in $to_does
                                                        $foundTodo = $to_does->firstWhere(function($do) use ($task, $phase) {
                                                            return $do->phase_id == $phase->id && $do->activities_id == $task->id && $do->type == 'main';
                                                        });
                                                    @endphp

                                                    <td>
                                                        <fieldset>
                                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                                <input type="checkbox" 
                                                                    value="false" 
                                                                    name="doneTask" 
                                                                    id="doneTask"
                                                                    data-customer="{{ request()->customer_id }}"
                                                                    data-product="{{ request()->product_id }}"
                                                                    data-address="{{ request()->address_no }}"
                                                                    data-phase="{{ $phase->id }}"
                                                                    data-task="{{ $task->id }}"
                                                                    {{ $foundTodo && $foundTodo->done == true ? 'checked disabled' : '' }}>
                                                                <span class="vs-checkbox">
                                                                    <span class="vs-checkbox--check">
                                                                        <i class="vs-icon feather icon-check"></i>
                                                                    </span>
                                                                </span>
                                                            </div>
                                                        </fieldset>  
                                                    </td>

                                                    @if($foundTodo && $foundTodo->done == true)
                                                        <td> {{ \Carbon\Carbon::parse($foundTodo->done_date)->isoFormat('DD.MM.YY')}} <br> <small>{{ \Carbon\Carbon::parse($foundTodo->done_date)->diffForHumans()}} </small></td>
                                                        <td>
                                                           
                                                            <div class="avatar mr-1">
                                                                <img src="{{ asset('images/employee/'.$foundTodo->cimage)}}" alt="" height="32" width="32" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ $foundTodo->cname}} {{ $foundTodo->clastname}}"> 
                                                            </div> 
                                                        </td>
                                                        <td>
                                                            @if($foundTodo->responsible_person != Null)
                                                            <div class="avatar mr-1">
                                                                <img src="{{ asset('images/employee/'.$foundTodo->rimage)}}" alt=" " height="32" width="32" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ $foundTodo->rname}} {{ $foundTodo->rlastname}} "> 
                                                            </div> 
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="avatar mr-1">
                                                                @if($foundTodo->outside_service != Null)
                                                                <img src="{{ asset('images/employee/'.$foundTodo->osimage)}}" alt=" " height="32" width="32" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ $foundTodo->osname}} {{ $foundTodo->oslastname}} "> 
                                                                @elseif($foundTodo->outside_company != Null)
                                                                <img src="{{ asset('images/gender/users.png')}}" alt=" " height="32" width="32" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ $foundTodo->company_name}} - {{ $foundTodo->admin_name}} ">  
                                                                @endif
                                                            </div> 
                                                        </td>
                                                        <td>  
                                                            <button type="button" class="btn waves-effect waves-light documentModalButton" data-toggle="modal" data-target="#documentModal" data-url="{{ asset('task/documents/') }}">
                                                                <i class="feather icon-file primary" style="font-size:25px"></i>
                                                            </button> 
                                                        </td>
                                                    @else
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    @endif
                                                </tr>
                                                @endif
                                            @endforeach

                                            <!-- Additional rows here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach


            </section>
              <!-- Task Completion Modal -->
                    <div class="modal fade" id="doneModal" tabindex="-1" role="dialog" aria-labelledby="doneModalTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="doneModalTitle">Aufgabenerledigungsmodal</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="customer_id" value="">
                                    <input type="hidden" name="product_id" value="">
                                    <input type="hidden" name="address_no" value="">
                                    <input type="hidden" name="phase_id" value="">
                                    <input type="hidden" name="activities_id" value="">
                                    <input type="hidden" name="sub_task_id" value="">
                                    <input type="hidden" name="type" value="main">
                                    <input type="hidden" name="contact_person" value="{{$current_user->id}}">
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Datum</span>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="position-relative has-icon-left">
                                                    <input type="date" class="form-control" name="done_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" placeholder="Datum" data-np-intersection-state="visible">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="position-relative has-icon-left">
                                                    <fieldset>
                                                        <div class="vs-checkbox-con vs-checkbox-success">
                                                            <input type="checkbox" value="1" name="calendar">
                                                            <span class="vs-checkbox">
                                                                <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i> 
                                                                </span>
                                                    
                                                            </span>
                                                            Zum Kalender hinzufügen  
                                                        </div>
                                                    </fieldset> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Verfasser</span>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="position-relative has-icon-left">
                                                    <div class="photo" style="display: flex; align-items: center;">
                                                        <div class="avatar mr-1">
                                                            <img src="{{ asset('images/employee/'.$current_user->image) }}" alt="{{ $current_user->name }}" height="32" width="32">
                                                        </div>
                                                        <label for="avatar" class="mt-0" style="font-size:14px">
                                                            {{ $current_user->name }} {{ $current_user->lastname }}
                                                        </label> 
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                     <div class="form-group">
                                        <label for="responsible_person">Verantwortlicher</label>
                                        <select name="responsible_person" class="form-control select2" style="width:100%;">
                                            <option></option>
                                            @foreach($employees as $contact)
                                                <option value="{{ $contact->id }}">{{ $contact->name }} {{ $contact->lastname }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="responsible_person">Der Out-Source-Typ</label>

                                        <div class="card-body"> 
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-inline-block mr-2">
                                                <fieldset>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input internal" checked name="outside_type" id="internal" checked="">
                                                        <label class="custom-control-label" for="internal">Intern</label>
                                                    </div>
                                                </fieldset>
                                            </li>
                                            <li class="d-inline-block mr-2">
                                                <fieldset>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input external" name="outside_type" id="external">
                                                        <label class="custom-control-label" for="external">Extern</label>
                                                    </div>
                                                </fieldset>
                                            </li> 
                                        </ul>
                                    </div>
                                    </div>
                                    <div class="form-group outside_company">
                                        <label for="outside_company">Ausführende <code>Ausgelagert</code></label>
                                        <select name="outside_company" class="form-control select2 " style="width:100%;"> 
                                            <option></option>
                                            @foreach($outside as $out)
                                                <option value="{{ $out->id }}">{{ $out->company_name }} - {{ number_format($out->price, 2) }} € </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group outside_service ">
                                        <label for="outside_service">Ausführende</label>
                                        <select name="outside_service" class="form-control select2" style="width:100%;">
                                            <option></option> 
                                            @foreach($employees as $contact)
                                                <option value="{{ $contact->id }}">{{ $contact->name }} {{ $contact->lastname }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Dokument Name</span>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" id="file-icon" class="form-control" name="document_name" value="" placeholder="Dukument Name" data-np-intersection-state="visible">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-file"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Dokument Summe</span>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" id="file-icon" class="form-control" name="document_sum" value="" placeholder="Dukument Summe" data-np-intersection-state="visible">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-sum"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12"> 
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Notiz</span>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="position-relative has-icon-left"> 
                                                    <textarea name="document_note" class="form-control" value="" placeholder="Dukument Notiz" data-np-intersection-state="visible"></textarea>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-file"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12"> 
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>PDF</span>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="position-relative has-icon-left">  
                                                    <input type="file" name="document" class="form-control"> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                                    <button type="button" class="btn btn-primary" id="save-task-btn">Speichern</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sub Task Modal -->
                    <div class="modal fade" id="doneSubTaskModal" tabindex="-1" role="dialog" aria-labelledby="doneSubTaskModalTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="doneSubTaskModalTitle">Unteraufgabe</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="customer_id" value="">
                                    <input type="hidden" name="product_id" value="">
                                    <input type="hidden" name="address_no" value="">
                                    <input type="hidden" name="phase_id" value="">
                                    <input type="hidden" name="activities_id" value="">
                                    <input type="hidden" name="sub_task_id" value="">
                                    <input type="hidden" name="type" value="sub">
                                    <input type="hidden" name="contact_person" value="{{$current_user->id}}">

 
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Datum</span>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="position-relative has-icon-left">
                                                    <input type="date" class="form-control" name="done_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" placeholder="Datum" data-np-intersection-state="visible">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="position-relative has-icon-left">
                                                    <fieldset>
                                                        <div class="vs-checkbox-con vs-checkbox-success">
                                                            <input type="checkbox" value="1" name="calendar">
                                                            <span class="vs-checkbox">
                                                                <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i> 
                                                                </span>
                                                    
                                                            </span>
                                                            Zum Kalender hinzufügen  
                                                        </div>
                                                    </fieldset> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Verfasser</span>
                                            </div>
                                            <div class="col-md-8"> 
                                                <div class="position-relative has-icon-left">
                                                    <div class="photo" style="display: flex; align-items: center;">
                                                        <div class="avatar mr-1">
                                                            <img src="{{ asset('images/employee/'.$current_user->image) }}" alt="{{ $current_user->name }}" height="32" width="32">
                                                        </div>
                                                        <label for="avatar" class="mt-0" style="font-size:14px">
                                                            {{ $current_user->name }} {{ $current_user->lastname }}
                                                        </label>
                                                        <input type="hidden" name="contact_person" value="13" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                     <div class="form-group">
                                        <label for="responsible_person">Verantwortlicher</label>
                                        <select name="responsible_person" class="form-control select2" style="width:100%;">
                                            @foreach($employees as $contact)
                                                <option value="{{ $contact->id }}">{{ $contact->name }} {{ $contact->lastname }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="responsible_person">Der Out-Source-Typ</label>

                                        <div class="card-body"> 
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-inline-block mr-2">
                                                <fieldset>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input subinternal" name="outside_types" id="subinternal" checked="">
                                                        <label class="custom-control-label" for="subinternal">Intern</label>
                                                    </div>
                                                </fieldset>
                                            </li>
                                            <li class="d-inline-block mr-2">
                                                <fieldset>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input subexternal" name="outside_types" id="subexternal">
                                                        <label class="custom-control-label" for="subexternal">Extern</label>
                                                    </div>
                                                </fieldset>
                                            </li> 
                                        </ul>
                                    </div>
                                    </div>
                                    <div class="form-group outside_company">
                                        <label for="outside_company">Ausführende <code>Ausgelagert</code></label>
                                        <select name="outside_company" class="form-control " style="width:100%;">
                                            @foreach($outside as $out)
                                                <option value="{{ $out->id }}">{{ $out->company_name }} - {{ number_format($out->price, 2) }} € </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group outside_service">
                                        <label for="outside_service">Ausführende</label>
                                        <select name="outside_service" class="form-control " style="width:100%;">
                                            @foreach($employees as $contact)
                                                <option value="{{ $contact->id }}">{{ $contact->name }} {{ $contact->lastname }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Dokument Name</span>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" id="file-icon" class="form-control" name="document_name" value="" placeholder="Dukument Name" data-np-intersection-state="visible">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-file"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Dokument Summe</span>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" id="file-icon" class="form-control" name="document_sum" value="" placeholder="Dukument Summe" data-np-intersection-state="visible">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-sum"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12"> 
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Notiz</span>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="position-relative has-icon-left"> 
                                                    <textarea name="document_note" class="form-control" value="" placeholder="Dukument Notiz" data-np-intersection-state="visible"></textarea>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-file"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12"> 
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>PDF</span>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="position-relative has-icon-left">  
                                                    <input type="file" name="document" class="form-control"> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                                    <button type="button" class="btn btn-primary" id="save-sub-task-btn">Speichern</button>
                                </div>
                            </div>
                        </div>
                    </div>


                <!-- Modal -->
                <div class="modal fade" id="documentModal" tabindex="-1" role="dialog" aria-labelledby="documentModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="documentModalLabel">Document Viewer</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="embed-responsive embed-responsive-16by9">
                                    <iframe id="documentIframe" class="embed-responsive-item" src="" allowfullscreen></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            
            <section>
                <div class="accordion" id="documentscollaps">
                    <div class="card">
                        <div class="card-header" id="headingOne">
                            <h5 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#documents" aria-expanded="true" aria-controls="documents">
                                    <h4 style="font-weight: bold;" class="primary">
                                        <i class="icon-toggle feather icon-plus"></i> DOKUMENTE
                                    </h4>
                                </button>
                            </h5>
                        </div>

                        <div id="documents" class="collapse show" aria-labelledby="headingOne" data-parent="#documentscollaps">
                            <div class="row" id="table-hover-animation">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-hover-animation mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col"></th>
                                                                <th scope="col">DOKUMENT</th>
                                                                <th scope="col">VERFASSER</th> 
                                                                <th scope="col">VERANTWORTLISCHER</th>
                                                                <th scope="col">DETAILS</th>
                                                                <th scope="col">SUMME</th>
                                                                <th scope="col" colspan="2">STATUS</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                          @php
                                                                $unique_docs = $task_docs->unique('document');
                                                            @endphp

                                                            @foreach ($unique_docs as $docs)
                                                                @if($docs->customer_id == request()->customer_id && $docs->product_id == request()->product_id)
                                                                    <tr>
                                                                        <th scope="row">
                                                                            <button type="button" class="btn waves-effect waves-light documentCustomerButton" data-toggle="modal" data-target="#documentCustomer" pdf-url="{{ asset('task/documents/'.$docs->document) }}">
                                                                                <i class="feather icon-file primary" style="font-size:25px"></i>
                                                                            </button>
                                                                        </th>
                                                                        <td>{{ $docs->document_name }}</td>
                                                                        <td>
                                                                            @if($docs->cname)
                                                                                <div class="verfasser">
                                                                                    <div class="avatar mr-1">
                                                                                        <img src="{{ asset('images/employee/'.$docs->cimage) }}" alt="{{ $docs->cname }}" height="32" width="32" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ $docs->cname }} {{ $docs->clastname }}">
                                                                                    </div>
                                                                                    <div class="date">
                                                                                        <span class="font-medium-2">{{ \Carbon\Carbon::parse($docs->created_at)->isoFormat('MM.DD.YYYY') }}</span>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if($docs->rname)
                                                                            <div class="avatar mr-1">
                                                                                <img src="{{ asset('images/employee/'.$docs->rimage) }}" alt="{{ $docs->rname }}" height="32" width="32" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ $docs->rname }} {{ $docs->rlastname }}">
                                                                            </div>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            <p>
                                                                                {{ $docs->document_note }}
                                                                            </p>
                                                                        </td>
                                                                        <td> €</td>
                                                                        <td>{{$docs->document_status}}</td> 
                                                                    </tr>
                                                                @endif
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
            </section>
        <!-- Modal -->
                <div class="modal fade" id="documentCustomer" tabindex="-1" role="dialog" aria-labelledby="documentCustomerLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="documentCustomerLabel">Document Viewer</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="embed-responsive embed-responsive-16by9">
                                    <iframe id="pdfFrame" class="embed-responsive-item" src="" allowfullscreen></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
    @push('scripts')
    <!-- Expand Function  -->
     <!-- Include jQuery -->
 

        <script>
        $(document).ready(function () {
            $('div[id^="accordionExample"]').on('hide.bs.collapse show.bs.collapse', function (e) {
                var icon = $(e.target).prev('.card-header').find('.icon-toggle');
                if (e.type === 'show') {
                    icon.removeClass('icon-plus').addClass('icon-minus');
                } else {
                    icon.removeClass('icon-minus').addClass('icon-plus');
                }
            });
        });
        </script>


        <script>
            document.querySelector('.progress-item').classList.add('active');

        </script>
 
        <!-- Include this script at the bottom of your HTML or in a separate JS file -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const progressItems = document.querySelectorAll('.progress-item');

            progressItems.forEach((item, index) => {
                item.addEventListener('click', () => {
                    if (item.classList.contains('active')) {
                        const isConfirmed = confirm('This progress step and all previous steps are already active. Do you want to deactivate the following steps?');
                        if (isConfirmed) {
                            deactivateFollowingProgress(index);
                        }
                    } else {
                        const isConfirmed = confirm('Do you want to activate this and all previous progress steps?');
                        if (isConfirmed) {
                            activateProgress(index);
                        }
                    }
                });
            });

            function activateProgress(index) {
                for (let i = 0; i <= index; i++) {
                    progressItems[i].classList.add('active');
                }
            }

            function deactivateFollowingProgress(index) {
                for (let i = index + 1; i < progressItems.length; i++) {
                    progressItems[i].classList.remove('active');
                }
            }
        });
        </script>


<script>

    document.addEventListener('DOMContentLoaded', function () {
    // Select all buttons that will trigger the modal
    const documentButtons = document.querySelectorAll('.documentModalButton');

    documentButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            // Get the URL from the data-url attribute
            const documentUrl = this.getAttribute('data-url');
            
            // Get the iframe inside the modal
            const iframe = document.getElementById('documentIframe');
            
            // Set the iframe's src to the document URL
            iframe.setAttribute('src', documentUrl);
            
            // Now the modal will automatically open because of the data-toggle and data-target attributes
        });
    });
});

</script>


<script>

    document.addEventListener('DOMContentLoaded', function () {
    // Select all buttons that will trigger the modal
    const documentButtons = document.querySelectorAll('.documentCustomerButton');

    documentButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            // Get the URL from the data-url attribute
            const documentUrl = this.getAttribute('pdf-url');
            
            // Get the iframe inside the modal
            const iframe = document.getElementById('pdfFrame');
            
            // Set the iframe's src to the document URL
            iframe.setAttribute('src', documentUrl);
            
            // Now the modal will automatically open because of the data-toggle and data-target attributes
        });
    });
});

</script>

 <script>
  $(document).ready(function () {
    var currentTaskCheckbox;  // Store reference to the current task checkbox (either sub-task or main task)

    // Function to show SweetAlert for incomplete sub-tasks
    function showIncompleteSubTaskAlert() {
        Swal.fire({
            icon: 'warning',
            title: 'Incomplete Sub-Tasks',
            text: 'Please complete all sub-tasks before marking the main task as done.',
            confirmButtonText: 'OK'
        });
    }

    // Handle the main task checkbox
    $('input[type="checkbox"][name="doneTask"]').on('change', function () {
        if (this.checked) {
            var phaseId = $(this).data('phase');
            var taskId = $(this).data('task');

            // Check if there are incomplete sub-tasks related to the main task
            var incompleteSubTasks = $('input[type="checkbox"].doneSubTask[data-phase="' + phaseId + '"][data-task="' + taskId + '"]:not(:checked)');

            // If there are incomplete sub-tasks, show the SweetAlert and uncheck the checkbox
            if (incompleteSubTasks.length > 0) {
                $(this).prop('checked', false); // Uncheck the main task checkbox
                showIncompleteSubTaskAlert();  // Show alert
            } else {
                // No incomplete sub-tasks, show the main task modal
                currentTaskCheckbox = this;

                var customer = $(this).data('customer');
                var product = $(this).data('product');
                var address = $(this).data('address');
                var phase = $(this).data('phase');
                var task = $(this).data('task');
                var subTask = $(this).data('sub-task');

                // Populate the modal with task-related data
                $('#doneModal input[name="customer_id"]').val(customer);
                $('#doneModal input[name="product_id"]').val(product);
                $('#doneModal input[name="address_no"]').val(address);
                $('#doneModal input[name="phase_id"]').val(phase);
                $('#doneModal input[name="activities_id"]').val(task);
                $('#doneModal input[name="sub_task_id"]').val(subTask);

                // Open the main task modal
                $('#doneModal').modal('show');
            }
        }
    });

    // Handle sub-task checkbox click event and show the sub-task modal
    $('input[type="checkbox"][name="doneSubTask"]').on('change', function () {
        if (this.checked) {
            currentTaskCheckbox = this;

            var customer = $(this).data('customer');
            var product = $(this).data('product');
            var address = $(this).data('address');
            var phase = $(this).data('phase');
            var task = $(this).data('task');
            var subTask = $(this).data('sub-task');

            // Populate the modal with sub-task related data
            $('#doneSubTaskModal input[name="customer_id"]').val(customer);
            $('#doneSubTaskModal input[name="product_id"]').val(product);
            $('#doneSubTaskModal input[name="address_no"]').val(address);
            $('#doneSubTaskModal input[name="phase_id"]').val(phase);
            $('#doneSubTaskModal input[name="activities_id"]').val(task);
            $('#doneSubTaskModal input[name="sub_task_id"]').val(subTask);

            // Open the sub-task modal
            $('#doneSubTaskModal').modal('show');
        }
    });

    // Uncheck the checkbox when the modal is closed without saving
    $('#doneModal, #doneSubTaskModal').on('hidden.bs.modal', function () {
        if (currentTaskCheckbox) {
            $(currentTaskCheckbox).prop('checked', false); // Uncheck the checkbox
            currentTaskCheckbox = null; // Reset the reference
        }
    });
   $('#save-task-btn').on('click', function() {
    let formData = new FormData();

    // Append necessary fields manually
    formData.append('customer_id', $('#doneModal input[name="customer_id"]').val());
    formData.append('product_id', $('#doneModal input[name="product_id"]').val());
    formData.append('address_no', $('#doneModal input[name="address_no"]').val());
    formData.append('phase_id', $('#doneModal input[name="phase_id"]').val());
    formData.append('activities_id', $('#doneModal input[name="activities_id"]').val());
    formData.append('sub_task_id', $('#doneModal input[name="sub_task_id"]').val());
    formData.append('type', 'main');  // Specify it's a main task
    formData.append('contact_person', $('#doneModal input[name="contact_person"]').val());
    formData.append('responsible_person', $('#doneModal select[name="responsible_person"]').val());

    // Get the value of the selected outside_type radio button
    let outsideType = $('input[name="outside_type"]:checked').attr('id'); // get the ID of the checked radio button
    console.log('Outside Type:', outsideType); // Log to check the value
    formData.append('outside_type', outsideType);

    // Append the outside service and outside company
    formData.append('outside_service', $('#doneModal select[name="outside_service"]').val());
    formData.append('outside_company', $('#doneModal select[name="outside_company"]').val());

    // Append fields from form inputs
    formData.append('done_date', $('#doneModal input[name="done_date"]').val());
    formData.append('calendar', $('#doneModal input[name="calendar"]').is(':checked') ? 1 : 0);  // Checkbox value

    // Append document if uploaded
    let document = $('#doneModal input[name="document"]')[0].files[0];
    if (document) {
        formData.append('document', document);
    }

    // Optional fields
    formData.append('document_name', $('#doneModal input[name="document_name"]').val());
    formData.append('document_sum', $('#doneModal input[name="document_sum"]').val());
    formData.append('document_note', $('#doneModal textarea[name="document_note"]').val());

    // CSRF token for security
    formData.append('_token', '{{ csrf_token() }}');

    $.ajax({
        url: "{{ route('task.store') }}",  // Update with your actual route
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            toastr.success(response.message);
            $('#doneModal').modal('hide');
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                $.each(errors, function(key, value) {
                    toastr.error(value[0]);
                });
            } else {
                toastr.error('An unexpected error occurred.');
            }
        }
    });
});


$('#save-sub-task-btn').on('click', function() {
    let formData = new FormData();

    // Append necessary fields manually for sub-task
    formData.append('customer_id', $('#doneSubTaskModal input[name="customer_id"]').val());
    formData.append('product_id', $('#doneSubTaskModal input[name="product_id"]').val());
    formData.append('address_no', $('#doneSubTaskModal input[name="address_no"]').val());
    formData.append('phase_id', $('#doneSubTaskModal input[name="phase_id"]').val());
    formData.append('activities_id', $('#doneSubTaskModal input[name="activities_id"]').val());
    formData.append('sub_task_id', $('#doneSubTaskModal input[name="sub_task_id"]').val());
    formData.append('type', 'sub');  // Specify it's a sub-task
    formData.append('contact_person', $('#doneSubTaskModal input[name="contact_person"]').val());
    formData.append('responsible_person', $('#doneSubTaskModal select[name="responsible_person"]').val());

    // Get the value of the selected outside_type radio button for the sub-task modal
    let outsideType = $('input[name="outside_types"]:checked').attr('id'); // Get the ID of the checked radio button
    console.log('Outside Type (Sub-Task):', outsideType); // Log to check the value
    formData.append('outside_type', outsideType);

    // Append the outside service and outside company for the sub-task modal
    formData.append('outside_service', $('#doneSubTaskModal select[name="outside_service"]').val());
    formData.append('outside_company', $('#doneSubTaskModal select[name="outside_company"]').val());

    // Append fields from form inputs
    formData.append('done_date', $('#doneSubTaskModal input[name="done_date"]').val());
    formData.append('calendar', $('#doneSubTaskModal input[name="calendar"]').is(':checked') ? 1 : 0);  // Checkbox value

    // Append document if uploaded
    let document = $('#doneSubTaskModal input[name="document"]')[0].files[0];
    if (document) {
        formData.append('document', document);
    }

    // Optional fields
    formData.append('document_name', $('#doneSubTaskModal input[name="document_name"]').val());
    formData.append('document_sum', $('#doneSubTaskModal input[name="document_sum"]').val());
    formData.append('document_note', $('#doneSubTaskModal textarea[name="document_note"]').val());

    // CSRF token for security
    formData.append('_token', '{{ csrf_token() }}');

    $.ajax({
        url: "{{ route('task.store') }}",  // Update with your actual route for storing the sub-task
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            toastr.success(response.message);
            $('#doneSubTaskModal').modal('hide');
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                $.each(errors, function(key, value) {
                    toastr.error(value[0]);
                });
            } else {
                toastr.error('An unexpected error occurred.');
            }
        }
    });
});

    

});

 </script>
<!-- Task Completion Modal : End -->

<script>
    $(document).ready(function () {
        $('.select2').select2({
            theme: 'bootstrap4'
        });
    });
</script>
<script>
    $(document).ready(function () {
        // Initially hide both dropdowns
        $('.outside_company').hide();
        $('.outside_service').hide();

        // Function to toggle dropdowns for the main modal based on radio button selection
        function toggleDropdowns() {
            if ($('.internal').is(':checked')) {
                $('.outside_service').show();  // Show internal service dropdown
                $('.outside_company').hide();  // Hide external company dropdown
            } else if ($('.external').is(':checked')) {
                $('.outside_company').show();  // Show external company dropdown
                $('.outside_service').hide();  // Hide internal service dropdown
            }
        }

        // Function to toggle dropdowns for the subtask modal based on radio button selection
        function toggleDropdown() {
            if ($('.subinternal').is(':checked')) {
                $('.outside_service').show();  // Show internal service dropdown
                $('.outside_company').hide();  // Hide external company dropdown
            } else if ($('.subexternal').is(':checked')) {
                $('.outside_company').show();  // Show external company dropdown
                $('.outside_service').hide();  // Hide internal service dropdown
            }
        }

        // Call the toggle function for the main modal when the page loads
        toggleDropdowns();

        // Call the toggle function for the subtask modal when the page loads
        toggleDropdown();

        // Attach event listener to the radio buttons in the main modal to toggle the dropdowns on change
        $('input[name="outside_type"]').change(function () {
            toggleDropdowns();
        });

        // Attach event listener to the radio buttons in the subtask modal to toggle the dropdowns on change
        $('input[name="outside_types"]').change(function () {
            toggleDropdown();
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
    // Check if the URL contains the #project-management hash
    if (window.location.hash === "#project-management") {
        // Activate the PROJEKTMANAGEMENT tab
        var projectManagementTab = document.querySelector("#messages-tab-justified");
        if (projectManagementTab) {
            new bootstrap.Tab(projectManagementTab).show();
        }
    }
});

</script>


    @endpush