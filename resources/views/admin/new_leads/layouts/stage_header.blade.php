<div class="d-flex align-items-center justify-content-between flex-wrap">
    <div>
        <strong>{{ $stageLabel }}</strong><br>
        <span>{{ $doneCount }} / {{ $total }} erledigt</span>
    </div>

    <div>
        @foreach($suggestedEmployees->take(5) as $emp)  
            <img src="{{ asset('images/employee/' . $emp->image) }}"
                class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover;"
                title="{{ $emp->name }} {{ $emp->lastname }}">
        @endforeach
    </div>

    <div>
        <strong>Nächster Schritt:</strong><br>
        @if ($nextRealActivity)
            <span class="text-dark font-weight-bold">{{ $nextRealActivity->title }}</span><br>
            <small class="text-muted">{{ $nextRealActivity->description }}</small>
        @else
            <span class="text-muted">Alle Schritte erledigt 🎉</span>
        @endif
    </div>

    <div>
        <button class="btn btn-sm btn-primary change_stages"
            data-customer-id="{{ $customer_id }}"
            data-alternative-id="{{ $alternative_id }}"
            data-product-id="{{ $productId }}"
            data-phase-id="{{ $phaseId }}">
            <i class="feather icon-git-branch"></i> Phase wechseln
        </button>
    </div>
</div>
