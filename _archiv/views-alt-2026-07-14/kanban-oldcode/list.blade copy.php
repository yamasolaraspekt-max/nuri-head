<div class="row text-center " id="summaryStats">
    <div class="col-md-2">
        <div class="border rounded py-2 " style="border: 1px solid #8fc63f !important;">
            <strong class="text-primary">Gesamt Mitarbeiter</strong>
            <div id="totalEmployees" class="h4">{{ $totalEmployees }}</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="border rounded py-2  text-white" style="border: 1px solid #8fc63f !important;">
            <strong class="text-primary">Gesamt Produkt</strong>
            <div id="totalProduct" class="h4">{{ $totalProducts }}</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="border rounded py-2  text-white" style="border: 1px solid #8fc63f !important;" >
            <strong class="text-primary">Gesamt Kunde</strong>
            <div id="totalCustomer" class="h4">{{ $totalCustomers }}</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="border rounded py-2 " style="border: 1px solid #8fc63f !important;">
            <strong class="text-primary">Status: Offen</strong>
            <div class="h4">{{ $statusCounts['offen'] }} <small>({{ $statusPercentages['offen'] }}%)</small></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="border rounded py-2 bg-primary text-white">
            <strong>Status: Zusage</strong>
            <div class="h4">{{ $statusCounts['zusage'] }} <small>({{ $statusPercentages['zusage'] }}%)</small></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="border rounded py-2 bg-danger text-white">
            <strong>Status: Absage</strong>
            <div class="h4">{{ $statusCounts['absage'] }} <small>({{ $statusPercentages['absage'] }}%)</small></div>
        </div>
    </div>
</div>

 

<form id="kanbanFilterForm" class="row align-items-end g-2 mb-3 mt-2"> 

    <div class="col-md-2">  
        <label for="customerFilter" class="form-label">Kunde</label>
         <select name="customer" id="customerFilter" class="form-control select2">
            <option value="">Alle</option> 
            @foreach ($customers as $customer) 
                <option value="{{ $customer->id }}">{{ $customer->name }} {{ $customer->lastname }}</option>
            @endforeach
        </select> 
    </div>
    <div class="col-md-2">
        <label for="stageFilter" class="form-label">Phase</label>
        <select name="stage" id="stageFilter" class="form-control select2">
            <option value="">Alle Phasen</option>
            @foreach($stageNames as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <label for="employeeFilter" class="form-label">Mitarbeiter</label>
        <select name="employee" id="employeeFilter" class="form-control select2">
            <option value="">Alle</option> 
            @foreach ($employees as $employee) 
                <option value="{{ $employee->name }}">{{ $employee->name }} {{ $employee->lastname }}</option>
            @endforeach
        </select> 
    </div>

    <div class="col-md-2">
        <label for="departmentFilter" class="form-label">Abteilung</label>
        <select name="department" id="departmentFilter" class="form-control select2">
            <option value="">Alle</option> 
            @foreach ($departments as $department) 
                <option value="{{ $department->department_name }}">{{ $department->department_name }}</option>
            @endforeach
        </select> 
    </div>

    <div class="col-md-2">
        <label for="productFilter" class="form-label">Produkt</label>
        <select name="product" id="productFilter" class="form-control select2">
            <option value="">Alle</option> 
            @foreach ($products as $product) 
                <option value="{{ $product->id }}">{{ $product->article_group }}</option>
            @endforeach
        </select> 
    </div>

    <div class="col-md-1">
        <label for="interestFilter" class="form-label">Interesse</label>
        <select name="interest" id="interestFilter" class="form-control select2">
            <option value="">Alle Interessen</option>
            <option value="interest">Kaufinteresse</option>
            <option value="intent">Kaufabsicht</option>
            <option value="option">Kaufoption</option>
        </select>
    </div>

   

    <div class="col-md-1">
        <button type="submit" class="btn btn-primary w-100">Filtern</button>
    </div>

</form>


<div class="table-responsive p-3">
    <table class="table table-striped table-bordered align-middle">
        <thead class=" ">
            <tr>
                <th>Kunde</th> 
                <th>Produkt</th>
                <th>Mitarbeiter</th> 
                <th>Status</th> 
                <th>Phase</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody id="kanbanTableBody">
            @foreach($leads as $lead)
                <tr id="row-{{ $lead->lead_product_id }}"
                    data-customer-id="{{ $lead->customer_id }}"
                    data-alternative-id="{{ $lead->alternative_id }}"
                    data-product-id="{{ $lead->product_id }}"
                    data-employee-id="{{ $lead->employee->employee_id ?? 0 }}"
                    data-service="{{ $lead->service }}"
                    data-service-id="{{ $lead->service_id ?? 0 }}"
                    data-department-id="{{ $lead->department_id ?? 0 }}">

                    <td>{{ $lead->customer_name }} {{ $lead->customer_lastname }} <br>
                        <small><i class="feather icon-map-pin"></i> {{ $lead->city }}</small>
                    </td>

                    @php
                        $services = [
                            'complete'     => 'Komplett',
                            'montage'      => 'Montage',
                            'product'      => 'Produkt',
                            'plan'         => 'Planung',
                            'maintenance'  => 'Wartung',
                            'repair'       => 'Reparatur',
                            'emergency'    => 'Notdienst',
                            'others'       => 'Sonstiges',
                        ];

                        $translatedPhase = $services[$lead->phase_section_title ?? ''] ?? '-';
                    @endphp

                    <td>
                        {{-- Product Initial --}}
                        <div class="d-flex align-items-center ">
                            <img src="{{ asset('images/icons/produkt.svg') }}" alt="Produkt" style="width: 30px;" class="me-2">
                            <span>{{ $lead->initial }}</span>
                        </div>

                        {{-- Department --}}
                        <div class="d-flex align-items-center ">
                            <img src="{{ asset('images/icons/abteilung.svg') }}" alt="Abteilung" style="width: 30px;" class="me-2">
                            <span>{{ $lead->department_name ?? '-' }}</span>
                        </div>

                        {{-- Phase Section --}}
                        <div class="d-flex align-items-center ">
                            <img src="{{ asset('images/icons/dienstleistung.svg') }}" alt="Phase" style="width: 30px;" class="me-2">
                            <span>{{ $translatedPhase }}</span>
                        </div>

                        {{-- Interest --}}
                        <div class="d-flex align-items-center">
                            @if($lead->interest == 'interest')
                                <img src="{{ asset('images/icons/kaufinteresse.svg') }}" alt="Kaufinteresse" style="width: 19px;" class="me-2">
                                <span>Kaufinteresse</span>
                            @elseif($lead->interest == 'intent')
                                <img src="{{ asset('images/icons/kaufabsicht.svg') }}" alt="Kaufabsicht" style="width: 19px;" class="me-2">
                                <span>Kaufabsicht</span>
                            @elseif($lead->interest == 'option')
                                <img src="{{ asset('images/icons/kaufoption.svg') }}" alt="Kaufoption" style="width: 19px;" class="me-2">
                                <span>Kaufoption</span>
                            @else
                                <span>-</span>
                            @endif
                        </div>
                    </td>



                    <td>
                        @if($lead->employee && $lead->employee->name)
                            <img src="{{ asset('images/employee/' . $lead->employee->image) }}" width="30" class="rounded-circle me-1">
                            {{ $lead->employee->name }} {{ $lead->employee->lastname }}
                        @else
                            <small>-</small>
                        @endif
                    </td>

 
                    <td>
                        <span id="status" class="badge 
                            @if(in_array($lead->stage, ['lead', 'offer', 'deal'])) bg-warning text-dark
                            @elseif(in_array($lead->stage, ['project', 'completed'])) bg-success
                            @else bg-danger
                            @endif">
                            @if(in_array($lead->stage, ['lead', 'offer', 'open']))
                                Offen
                            @elseif(in_array($lead->stage, ['deal', 'project', 'completed']))
                                Zusage
                            @elseif(in_array($lead->stage, ['junk', 'cancel', 'rejeck']))
                                Absage
                            @else
                                Unbekannt
                            @endif
                        </span>
                    </td>

                    <td>
                        <select class="form-control stage-select" data-id="{{ $lead->lead_product_id }}">
                            @foreach($stageNames as $key => $label)
                                <option value="{{ $key }}" {{ $lead->stage == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </td>

                    <td>
                        <a href="{{ url("/new_lead_profile/{$lead->customer_id}") }}" class="btn btn-outline-primary">
                            <i class="feather icon-eye"></i>
                        </a>
                        <a href="{{ route('lead.history', [$lead->customer_id, $lead->alternative_id, $lead->product_id]) }}"
                            class="btn btn-outline-primary" data-show-history>
                            <i class="fa fa-tree"></i>
                        </a>
                    </td>
 
                  

                </tr>
                @endforeach

        </tbody>
    </table>
</div>