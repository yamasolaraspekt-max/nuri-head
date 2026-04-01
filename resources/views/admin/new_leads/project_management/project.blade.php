 
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
     
     <input type="hidden" id="hiddenCustomer" value="">
    <input type="hidden" id="hiddenProduct" value="">
    <input type="hidden" id="hiddenAlternative" value="">
    <input type="hidden" id="hiddenService" value="">

            <section>
              @foreach ($phases  as $phase)
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
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">#</th>
                                                        <th scope="col">Bezeichnung</th>
                                                        <th scope="col">Erlidigt!</th>
                                                        <th scope="col">Datum</th>
                                                        <th scope="col">Verfasser</th>
                                                        <th scope="col">Verantwortlich</th>
                                                        <th scope="col">Ausführende</th>
                                                        <th scope="col">Dokument</th>
                                                        <th scope="col">Notiz</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($tasks as $task)
                                                        @if($task->phase_id == $phase->id)
                                                            @php
                                                                $foundTodo = $to_does->firstWhere(function($do) use ($task, $phase) {
                                                                    return $do->phase_id == $phase->id && $do->activities_id == $task->id && $do->type == 'main';
                                                                });
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>
                                                                    <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#task-{{ $task->id }}" aria-expanded="false" aria-controls="task-{{ $task->id }}">
                                                                        {{ $task->initial }}. {{ $task->title }}
                                                                    </button>
                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" 
                                                                        name="doneTask" 
                                                                        data-task="{{ $task->id }}"
                                                                        {{ $foundTodo && $foundTodo->done ? 'checked disabled' : '' }}>
                                                                </td>
                                                                <td>@php echo $foundTodo && $foundTodo->done_date ? \Carbon\Carbon::parse($foundTodo->done_date)->isoFormat('DD.MM.YY') : ''; @endphp</td>
                                                                <td>{{ $foundTodo->verfasser ?? '' }}</td>
                                                                <td>{{ $foundTodo->verantwortlich ?? '' }}</td>
                                                                <td>{{ $foundTodo->ausfuhrende ?? '' }}</td>
                                                                <td><button class="btn btn-primary">Dokument</button></td>
                                                                <td>{{ $foundTodo->notiz ?? '' }}</td>
                                                            </tr>

                                                            <tr class="collapse" id="task-{{ $task->id }}">
                                                                <td colspan="9">
                                                                    <table class="table table-bordered">
                                                                        <thead>
                                                                            <tr>
                                                                                <th scope="col">#</th>
                                                                                <th scope="col">Bezeichnung</th>
                                                                                <th scope="col">Erlidigt!</th>
                                                                                <th scope="col">Datum</th>
                                                                                <th scope="col">Verfasser</th>
                                                                                <th scope="col">Verantwortlich</th>
                                                                                <th scope="col">Ausführende</th>
                                                                                <th scope="col">Dokument</th>
                                                                                <th scope="col">Notiz</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($sub_tasks as $sub_task)
                                                                                @if($sub_task->task_id == $task->id)
                                                                                    @php
                                                                                        $foundSubTodo = $to_does->firstWhere(function($do) use ($sub_task, $phase) {
                                                                                            return $do->phase_id == $phase->id 
                                                                                                && $do->activities_id == $sub_task->task_id
                                                                                                && $do->sub_task_id == $sub_task->id
                                                                                                && $do->type == 'sub';
                                                                                        });
                                                                                    @endphp
                                                                                    <tr>
                                                                                        <td>{{ $loop->iteration }}</td>
                                                                                        <td>{{ $sub_task->title }}</td>
                                                                                        <td>
                                                                                            <input type="checkbox" 
                                                                                                name="doneSubTask" 
                                                                                                data-sub-task="{{ $sub_task->id }}"
                                                                                                {{ $foundSubTodo && $foundSubTodo->done ? 'checked disabled' : '' }}>
                                                                                        </td>
                                                                                        <td>@php echo $foundSubTodo && $foundSubTodo->done_date ? \Carbon\Carbon::parse($foundSubTodo->done_date)->isoFormat('DD.MM.YY') : ''; @endphp</td>
                                                                                        <td>{{ $foundSubTodo->verfasser ?? '' }}</td>
                                                                                        <td>{{ $foundSubTodo->verantwortlich ?? '' }}</td>
                                                                                        <td>{{ $foundSubTodo->ausfuhrende ?? '' }}</td>
                                                                                        <td><button class="btn btn-primary">Dokument</button></td>
                                                                                        <td>{{ $foundSubTodo->notiz ?? '' }}</td>
                                                                                    </tr>
                                                                                @endif
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </td>
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
            @endforeach


            </section>
              <!-- Task Completion Modal -->
                   
                                  
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
                                    <input type="hidden" name="alternative" value="">
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
                
 