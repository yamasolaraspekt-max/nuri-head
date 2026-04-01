<div id="neighbor-wrapper"
     @if($hasCoords)
         data-base-lat="{{ $baseLat }}"
         data-base-lng="{{ $baseLng }}"
     @endif
     data-lead-id="{{ $lead->id }}"
     data-alt-id="{{ optional($baseAlternative)->id }}"
     data-radius="{{ $radius }}"
     data-neighbors='@json($neighborsForJs)'>

    {{-- SCROLLABLE AREA --}}
    <div class="neighbor-scroll">
        <div class="card shadow-sm border-0 mb-2">
            <div class="card-body py-2 px-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="mb-1">
                        <div class="small text-uppercase text-muted mb-1">Nachbarschaft</div>
                        <div class="font-weight-semibold">
                            {{ $lead->firma ?: trim($lead->lastname.' '.$lead->name) ?: 'Lead #'.$lead->id }}
                        </div>
                        @if($baseAlternative)
                            <div class="small text-muted">
                                Objekt: {{ $baseAlternative->object_name ?? '–' }},
                                {{ $baseAlternative->street }} {{ $baseAlternative->postcode }} {{ $baseAlternative->city }}
                            </div>
                        @endif
                    </div>

                    <div class="mb-1">
                        <div class="small text-muted">Radius auswählen</div>
                        <div class="d-flex align-items-center">
                            <input type="range"
                                   id="radiusRange"
                                   min="1" max="25" step="0.5"
                                   value="{{ $radius }}"
                                   class="custom-range mr-2" style="width:180px;">
                            <span id="radiusLabel"
                                  class="badge badge-light border font-weight-semibold">
                                {{ number_format($radius, 1, ',', '.') }} km
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(!$hasCoords)
            <div class="alert alert-warning mb-0">
                Für diesen Kunden / dieses Objekt sind keine Koordinaten hinterlegt.
            </div>
        @else
            <div class="card shadow-sm border-0 mb-2">
                <div class="card-body p-2">
                    <div id="neighbor-map"
                         style="height:380px;border-radius:12px;border:1px solid #e5e7eb;"></div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 font-weight-semibold">
                            Gefundene Nachbarn
                        </h6>
                        <span class="badge badge-light border">
                            {{ $neighbors->count() }}
                        </span>
                    </div>

                    <div id="neighbor-list">
                        @include('admin.new_leads.layouts.neighbor_list', ['neighbors' => $neighbors])
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
