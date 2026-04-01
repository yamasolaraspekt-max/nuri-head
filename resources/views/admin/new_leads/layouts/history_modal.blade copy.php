
@php
    $allStages = collect($groupedPhases)->keys(); 
@endphp

@php
    $totalActivities = collect($groupedPhases)->flatten(1)->filter(fn($r) => $r->activity)->count();
    $doneActivities = collect($groupedPhases)->flatten(1)->filter(fn($r) => $r->is_done == 1)->count();
    $overallPercent = $totalActivities > 0 ? round(($doneActivities / $totalActivities) * 100) : 0;
@endphp

 <div class="customer-nav mb-0">
    <div class="row text-white align-items-stretch text-nowrap">
        <!-- Column 1: Name -->
        <div class="col d-flex flex-column justify-content-center">
            <div class="inner-col">
                <div class="fw-bold text-uppercase">{{$customer->title}}</div>
                <div class="fw-bold text-uppercase">{{ $customer->name }} {{ $customer->lastname }}</div>
                @if($customer->firma)
                <div class="fw-bold text-uppercase" style="font-size:11px">Firma: {{ $customer->firma }}</div> 
                @endif
                <small><div>{{ \Carbon\Carbon::parse($customer->created_at)->isoFormat('DD.MM.YYYY') }} - Quelle: {{ $customer->source }}</div></small>
            </div>
        </div>

        

        <!-- Column 2: Address -->
        <div class="col d-flex flex-column justify-content-center">
            <div class="inner-col border-start">
                <div>{{ $customer->street }}</div>
                <div>{{ $customer->postcode }} {{ $customer->city }}</div>
            </div>
        </div>

        <!-- Column 3: Contact -->
        <div class="col d-flex flex-column justify-content-center">
            <div class="inner-col border-start">
                <div>{{ $customer->email }}</div>
                <div>{{ $customer->phone }}</div>
                @if (!empty($customer->mobile))
                    <div>{{ $customer->mobile }}</div>
                @endif
            </div>
        </div>

        <!-- Column 4: Source -->
        <div class="col d-flex flex-column justify-content-center">
           
        </div>

        <!-- Column 5: Notes -->
        <div class="col d-flex flex-column justify-content-center">
            <div class="inner-col border-start p-2"
                style="
                    max-height: 80px;
                    overflow-y: auto;
                    overflow-x: hidden;
                    word-wrap: break-word;
                    white-space: pre-wrap;
                    font-size: 14px;
                    text-align: left;
                    cursor: pointer;"
                onclick="showFullNote(this)"
                data-note="{{ $customer->info ?? 'Notizen hier' }}">
                {{ $customer->info ?? 'Notizen hier' }}
            </div>
        </div> 
        <!-- Column 2: Deal and Offer -->
        <div class="col d-flex flex-column justify-content-center">
            <div class="inner-col border-start">
                <div><strong>Umsatz </strong></div>
                <div>Gesamt:  - EUR</div>
                <div>Letzter: -  EUR</div>
                <div>Datum: -</div>
            </div>
        </div>
 
    </div>
</div>

 <div class="customer-navs">
    <div class="row text-white align-items-stretch text-nowrap">
        <!-- Column 1: Name -->
        <div class="col d-flex flex-column justify-content-center   ">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-primary text-white font-weight-bold d-flex align-items-center justify-content-center"
                    style="width: 40px; height: 40px; font-size: 14px;">{{$productList->initial}}</div>
                    <img src="{{ asset('images/employee/'.$productList->image) }}" class="rounded-circle"
                        style="width: 24px; height: 24px; object-fit: cover; position: absolute; left: 40px; top: 33px;">
                    <div class="ml-2 small">

                        @php
                            $services = [
                                'complete' => 'Komplettlösung',
                                'montage' => 'Montage',
                                'product' => 'Produkt',
                                'plan' => 'Planung',
                                'maintenance' => 'Wartung',
                                'repair' => 'Reparatur',
                                'emergency' => 'Notdienst',
                                'others' => 'Sonstiges',
                            ];

                            $interests = [
                                'intent' => 'Kaufabsicht',
                                'interest' => 'Kaufinteresse',
                                'option' => 'Kaufoption',
                            ];

                            $realizations = [
                                'soon' => 'Schnellstmöglich',
                                '3' => '3 Monate',
                                '6' => '6 Monate',
                                'other' => 'Sonstiges',
                            ];
                        @endphp

                        <div>{{ $productList->department_name }}</div>
                        <div>{{ $services[$productList->phase_section] ?? $productList->phase_section }}</div> 
                        <div>{{ $interests[$productList->interest] ?? $productList->interest }}</div>
                    </div> 
            </div>
            <div class="col">
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-success" style="width: {{ $overallPercent }}%"></div>
                </div>
                <small class="text-muted mt-1 d-block">{{ $doneActivities }}/{{ $totalActivities }} erledigt</small>
            </div>
        </div>
 
        <!-- Column 5: Notes -->
            <div class="col d-flex flex-column justify-content-center">
                <div class="inner-col border-start p-2"
                    id="noteContainer"
                    data-customer="{{ $customer_id }}"
                    data-alternative="{{ $alternative_id }}"
                    data-product="{{ $productId }}"
                    data-title="{{ $note->title ?? '' }}"
                    data-description="{{ $note->description ?? '' }}">
                    
                    @if($note)
                        <div id="noteView" onclick="openNoteEditor()" style="cursor: pointer;">
                            <h5 class="fw-bold mb-0 text-white" id="noteTitle">{{ $note->title }}</h5>
                            <div id="noteDescription" style="color: #cfcfcf;">{!! nl2br(e($note->description)) !!}</div>
                        </div>
                    @else
                        <div class="text-muted" onclick="openNoteEditor()" style="cursor: pointer;">
                            <i class="fas fa-pen"></i> Klicken Sie hier, um eine Notiz hinzuzufügen
                        </div>
                    @endif 
                </div>
            </div>

        <!-- Column 2: Deal and Offer -->
        <div class="col d-flex flex-column justify-content-center">
            <div class="inner-col border-start">
                
                <div><strong>Start:</strong><span id="total_start"></span></div>
                <div><strong>Ende:</strong><span id="total_start"></span></div>
                <div>
                    <small class="text-white">
                        <span>P:</span> 20 Std.
                    </small>
                    |
                    <small class="text-white">
                        <span>I:</span> 10 Std.
                    </small>
                    |
                    <small class="text-white">
                        <span>D:</span> 10 Std.
                    </small>
                    |
                    <i class="fa fa-smile-o primary"></i>
                    <i class="fa fa-frown-o warning"></i>
                    <i class="fa fa-meh-o danger"></i>
                    
                </div>
         
            </div>
        </div>
        <button class="btn btn-icon btn-primary mr-1 mb-1 waves-effect waves-light closePhase" onclick="closePhaseSidebar()">×</button>

    </div>
</div>
@foreach ($allStages as $stageKey)
    @php
        $phaseGroup = $groupedPhases[$stageKey] ?? collect();
        $firstItem = $phaseGroup->first();
        $stageLabel = strtoupper($stageKey);
        $isCurrentStage = $currentStageKey === $stageKey;
        $total = $phaseGroup->filter(fn($r) => $r->activity)->count();
        $doneCount = $phaseGroup->filter(fn($r) => $r->is_done == 1)->count();
         $allEmployees = collect($groupedPhases)
            ->flatten(1)
            ->pluck('done_by')
            ->filter()
            ->unique()
            ->map(fn($id) => \App\Models\Employee::find($id))
            ->filter(); // remove nulls

        $activities = DB::table('phase_activities')
            ->where('phase_id', $firstItem->phase->id)
            ->orderBy('sort_order')
            ->get();

        $currentIndex = $activities->search(fn($a) => $a->id == optional($firstItem->activity)->id);
        $nextActivity = $activities->get($currentIndex + 1);
    @endphp

     <div class="card mt-0 mb-1">
       <div class="card-header d-flex justify-content-between align-items-center flex-wrap {{ $isCurrentStage ? 'active_stage text-white' : 'text-dark' }}"
            style="background:{{ $isCurrentStage ? '#73b1d4' : '#eff5df' }}; border-radius:0px; cursor:pointer;"
            data-toggle="collapse"
            data-target="#stage-{{ $stageKey }}">
            
            <!-- Left: Stage Title + Progress -->
            <div class="d-flex align-items-center" style="min-width: 200px;">
                 <div>
                    <div class="text-primary font-weight-bold">{{ $stageLabel }}</div>
                    <div class="d-flex align-items-center">
                        <div class="progress" style="height: 6px; width: 100px; margin-right: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $doneCount / max($total, 1) * 100 }}%;"></div>
                        </div>
                        <div class="text-muted small">{{ $doneCount }}/{{ $total }}</div>
                    </div>
                </div>
            </div>
 
            <!-- Mitarbeiter -->
            <div class="col-auto border-left pl-3">
                <div class="text-dark font-weight-bold small mb-1">Mitarbeiter</div>
                <div class="d-flex align-items-center">
                    @foreach($allEmployees->take(5) as $emp)
                        <img src="{{ asset('images/employee/' . $emp->image) }}"
                            title="{{ $emp->name }} {{ $emp->lastname }}"
                            class="rounded-circle border"
                            style="width: 26px; height: 26px; object-fit: cover; margin-right: 4px;">
                    @endforeach
                </div>
            </div>


            <div class="d-flex align-items-start border-left pl-3 py-2" style="gap: 20px; min-height: 80px;">
                <!-- Column 1: Phase -->
                <div class="pr-3 border-right pe-3" style="min-width: 180px;">
                    <div class="font-weight-bold text-dark mb-1">Phase</div>
                    <div class="text-muted">{{ optional($firstItem->phase)->phase_name ?? '–' }}</div>
                </div>

                <!-- Column 2: Nächster Schritt -->
                <div class="pl-3 flex-grow-1">
                    <div class="font-weight-bold text-dark mb-1">Nächster Schritt</div>
                        <div id="nextStepContainer_{{ $firstItem->phase->id ?? 'x' }}_{{ $firstItem->activity->id ?? 'x' }}_{{ $firstItem->product_id }}">
                            @if($nextActivity)
                                <p><strong>{{ $nextActivity->title }}</strong></p>
                                <p>{{ $nextActivity->description }}</p>
                            @else
                                <p>Keine weiteren Schritte vorhanden.</p>
                            @endif
                        </div>

                </div>
            </div>


            <!-- Right: Start/End Info -->
            <div class="d-flex flex-column align-items-start border-left pl-3" style="min-width: 200px;">
                <div><strong>Start:</strong> {{ optional($firstItem)->start_date ?? '–' }}</div>
                <div><strong>Ende:</strong> {{ optional($firstItem)->end_date ?? '–' }}</div>
                <div class="text-muted small">
                    P: 20 Std. | I: 10 Std. | D: 10 Std. | O
                </div>
            </div>

            <!-- Buttons -->
            <div class="d-flex align-items-center ml-auto">
                @if (!empty($firstItem?->product_id))
                    <button type="button"
                            class="btn btn-icon rounded-circle btn-outline-primary mr-2 change_stage"
                            data-customer-id="{{ $customer_id }}"
                            data-alternative-id="{{ $alternative_id }}"
                            data-product-id="{{ $firstItem->product_id }}"
                            data-stage="{{ $stageKey }}"
                            data-service="{{ $firstItem->service }}"
                            data-service-id="{{ $firstItem->service_id }}"
                            data-employee-id="{{ $firstItem->employee_id }}"
                            data-department-id="{{ $firstItem->department_id }}">
                        <i class="feather icon-edit"></i>
                    </button>
                @endif

                <button type="button" class="btn btn-icon rounded-circle btn-light border text-success">
                    <i class="feather icon-chevron-down"></i>
                </button>
            </div>
        </div>


        <div id="stage-{{ $stageKey }}" class="collapse {{ $isCurrentStage ? 'show' : '' }}">
            <div class="table-responsive">
                <table class="table table-bordered m-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 80px;">Kurz</th>
                            <th>Beschreibung</th>
                            <th style="width: 80px;">Erledigt!</th>
                            <th style="width: 100px;">Datum</th>
                            <th style="width: 100px;">Erledigt von</th>
                            <th style="width: 200px;">Zuständig</th>
                            <th style="width: 100px;">Dokument</th>
                            <th>Notiz</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($phaseGroup->isNotEmpty())
                            @foreach ($phaseGroup->groupBy(fn($item) => optional($item->phase)->id) as $phaseId => $phaseActs)
                                @php
                                    $phase = optional($phaseActs->first())->phase;
                                @endphp

                                @if ($phase)
                                    @php
                                        $allActivities = $phase->activities ?? collect();
                                        $total = $allActivities->count();
                                        $doneCount = $allActivities->filter(fn($act) =>
                                            \App\Models\CustomerHistory::where([
                                                ['activity_id', $act->id],
                                                ['customer_id', $customer_id],
                                                ['alternative_id', $alternative_id],
                                                ['is_done', 1],
                                            ])->exists()
                                        )->count();
                                    @endphp

                                    <tr class="bg-light">
                                        <td colspan="8">
                                            <strong>{{ $phase->phase_name }}</strong>
                                            <span class="badge badge-dark ml-2">{{ $doneCount }} / {{ $total }} erledigt</span>
                                        </td>
                                    </tr>

                                    @foreach ($phaseActs->filter(fn($a) => optional($a->activity)->parent_id === null) as $act)
                                        @include('admin.new_leads.layouts._activity_row', [
                                            'act' => $act,
                                            'allActivities' => $phaseGroup,
                                            'level' => 0,
                                            'customer' => (object)['id' => $customer_id],
                                            'alternative' => (object)['id' => $alternative_id],
                                            'currentActivityId' => $currentActivityId,
                                        ])
                                    @endforeach
                                @endif
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-muted text-center">Keine Phasen vorhanden.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endforeach
  