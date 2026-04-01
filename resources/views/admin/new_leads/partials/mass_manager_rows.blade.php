{{-- THIS FILE IS LOADED VIA AJAX --}}

<script>
    const MASS_ROUTE_EMPLOYEES = '{{ route("inquiry.department.employees") }}';
    const MASS_IMG_PATH        = "{{ asset('images/employee/') }}";
    const MASS_CSRF            = '{{ csrf_token() }}';
    const MASS_STAGE           = 'lead'; 

    window.massData = {
        products:    @json($products),
        departments: @json($departments),
        services:    @json($services)
    };
</script>

{{-- HERE IS THE COUNT VARIABLE --}}
<div class="alert alert-info d-flex justify-content-between align-items-center" 
     style="background-color: #e3f2fd; border-color: #74b2d4; color: #0c5460; margin-bottom: 20px; border-radius: 6px;">
    <span>
        <i class="feather icon-info"></i> 
        <strong>{{ $total_count }}</strong> Kunden gefunden. 
        (Zeige die ersten {{ $customers->count() }})
    </span>
    @if($total_count > $customers->count())
        <small>Bitte Suche verfeinern für mehr Ergebnisse.</small>
    @endif
</div>

@forelse($customers as $customer)
    <div class="cm-customer-card">
        <div class="cm-customer-header" onclick="$(this).next('.cm-object-container').slideToggle();">
            <span>
                <i class="feather icon-user"></i> 
                <strong>{{ $customer->name }} {{ $customer->lastname }}</strong> 
                <span class="badge badge-light ml-2">{{ $customer->customer_no }}</span>
                <small style="color:#888;"> — {{ $customer->city }}</small>
            </span>
            <span>
                <span class="badge badge-secondary" style="background-color: #cfe09b; color: #333;">{{ $customer->source }}</span>
                <i class="feather icon-chevron-down ml-2"></i>
            </span>
        </div>
        
        <div class="cm-object-container">
            @foreach($customer->alternativeAddresses as $object)
                <div class="cm-object-card">
                    <div class="cm-object-title">
                        <span>
                            <i class="feather icon-home"></i> 
                            {{ $object->object_name ?? 'Objekt' }} 
                            <small class="text-muted">({{ $object->street }}, {{ $object->city }})</small>
                        </span>
                        
                        <button class="btn-cm-add btnAddRow" 
                                data-customer="{{ $customer->id }}" 
                                data-object="{{ $object->id }}">
                            <i class="feather icon-plus"></i> Produkt hinzufügen
                        </button>
                    </div>

                    <table class="cm-table">
                        <thead>
                            <tr>
                                <th width="20%">Produkt</th>
                                <th width="15%">Abteilung</th>
                                <th width="15%">Service</th>
                                <th width="15%">Innendienst</th>
                                <th width="15%">Außendienst</th>
                                <th width="10%">Interesse</th>
                                <th width="10%" class="text-center">Aktion</th>
                            </tr>
                        </thead>
                        <tbody id="cm-tbody-{{ $object->id }}">
                            @foreach($object->products as $prod)
                                <tr data-id="{{ $prod->id }}" style="background-color: #fff;">
                                    <td>
                                        <div class="p-2 border rounded bg-light text-muted">
                                            {{ $prod->articleGroup->article_group ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="text-muted small">{{ $prod->department->department_name ?? '-' }}</td>
                                    <td class="text-muted small">{{ $prod->service->phase_section ?? '-' }}</td>
                                    <td class="text-muted small">{{ $prod->employee->lastname ?? '-' }}</td>
                                    <td class="text-muted small">{{ $prod->fieldEmployee->lastname ?? '-' }}</td>
                                    <td class="text-muted small">{{ $prod->interest }}</td>
                                    <td class="text-center">
                                        <button class="btn-cm-del cmDeleteRow" data-id="{{ $prod->id }}" title="Löschen">
                                            <i class="feather icon-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
            @if($customer->alternativeAddresses->isEmpty())
                <div class="p-3 text-center text-muted font-italic">Keine Objekte für diesen Kunden gefunden.</div>
            @endif
        </div>
    </div>
@empty
    <div style="padding:40px; text-align:center; color: #777;">
        <h3>Keine Ergebnisse</h3>
        <p>Versuchen Sie es mit anderen Filtern.</p>
    </div>
@endforelse
 