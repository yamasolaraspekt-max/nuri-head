@if($neighbors->isEmpty())
    <p class="text-muted small mb-0">Keine Nachbarn im aktuellen Radius gefunden.</p>
@else
    <div class="list-group list-group-flush" id="neighbor-list-inner">
        @foreach($neighbors as $n)
            <button type="button"
                    class="list-group-item px-0 py-2 border-0 neighbor-item text-left"
                    data-neighbor-id="{{ $n->id }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="font-weight-semibold">
                            {{ $n->display_name }}
                        </div>
                        <div class="small text-muted">
                            {{ $n->street }} {{ $n->postcode }} {{ $n->city ?? $n->lead_city }}
                        </div>

                        @if($n->object_name)
                            <div class="small text-muted">
                                Objekt:
                                <span class="font-italic">{{ $n->object_name }}</span>
                            </div>
                        @endif

                        @php
                            $productsSummary = $n->products_summary ?? null;
                        @endphp

                        @if($productsSummary)
                            <div class="small">
                                <span class="badge badge-light border mr-25">
                                    <i class="feather icon-box mr-25"></i> Produkte
                                </span>
                                <span class="text-muted">{{ $productsSummary }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="text-right">
                        <span class="badge badge-light border small">
                            {{ number_format($n->distance_km, 2, ',', '.') }} km
                        </span>
                    </div>
                </div>
            </button>
        @endforeach
    </div>
@endif
