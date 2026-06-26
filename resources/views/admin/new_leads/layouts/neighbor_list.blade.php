@if($neighbors->isEmpty())
    <div class="text-center p-4 text-muted">Keine Nachbarn im aktuellen Radius gefunden.</div>
@else
    @foreach($neighbors as $n)
        <div class="oc-item-row" style="grid-template-columns: 50px 1.5fr 1.8fr 2.6fr 150px;">
            <div class="oc-cell">
                <div class="oc-stat-icon muted" style="width:40px;height:40px;">
                    <i class="feather icon-user"></i>
                </div>
            </div>

            <div class="oc-cell">
                <div class="oc-cell-title">Kunde</div>
                <div class="oc-ttl">


                    @if(!empty($n->customer_name) || !empty($n->customer_lastname))
                        <div class="oc-subt">
                            {{ trim(($n->customer_name ?? '') . ' ' . ($n->customer_lastname ?? '')) }}
                        </div>
                    @endif
                   
                    @if(!empty($n->is_current))
                        <span class="oc-badge accent" style="margin-left:8px;">Aktuelles Objekt</span>
                    @endif
                </div>
                <div class="oc-subt">#{{ $n->customer_id ?? $n->lead_id }}</div>
                 {{ $n->display_name }}

            </div>

            <div class="oc-cell">
                <div class="oc-cell-title">Adresse</div>
                <div class="oc-subt">
                    <i class="feather icon-map-pin"></i>
                    {{ $n->full_address ?: trim(($n->street ?? '') . ' ' . ($n->postcode ?? '') . ' ' . ($n->city ?? $n->lead_city ?? '')) }}
                </div>
                <div class="oc-subt">
                    {{ number_format((float)$n->distance_km, 2, ',', '.') }} km
                </div>
            </div>

            <div class="oc-cell">
                <div class="oc-cell-title">Produkte</div>

                @php
                    $leadProducts = collect($n->product_rows ?? [])->where('source', 'lead_product_list')->values();
                    $cpiProducts  = collect($n->product_rows ?? [])->where('source', 'customer_product_info')->values();
                @endphp

                @if($leadProducts->count() || $cpiProducts->count())
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        @foreach($leadProducts as $product)
                            @php
                                $relatedCpi = $cpiProducts->firstWhere('product_id', $product['product_id']);
                                $noteText = $relatedCpi['notes'] ?? null;
                                $countVal = $relatedCpi['product_count'] ?? null;
                                $serialNo = $relatedCpi['serial_number'] ?? null;
                            @endphp

                            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                <span class="oc-badge {{ in_array($product['stage_key'], ['deal','project','completed']) ? 'accent' : 'primary' }}">
                                    {{ $product['product_name'] }} ({{ $product['stage_label'] }})
                                </span>

                                @if(!empty($countVal))
                                    <span class="oc-badge" title="Anzahl">
                                        <i class="feather icon-layers mr-25"></i> {{ $countVal }}
                                    </span>
                                @endif

                                @if(!empty($serialNo))
                                    <span class="oc-badge" title="Seriennummer">
                                        <i class="feather icon-hash mr-25"></i> {{ $serialNo }}
                                    </span>
                                @endif

                                @if(!empty($noteText))
                                    <button type="button"
                                            class="icon-note-btn show-note-btn"
                                            data-note="{{ e($noteText) }}"
                                            title="Notiz anzeigen">
                                        <i class="feather icon-file-text"></i>
                                    </button>
                                @endif
                            </div>
                        @endforeach

                        @foreach($cpiProducts as $product)
                            @if(!$leadProducts->contains(fn($lp) => (int)$lp['product_id'] === (int)$product['product_id']))
                                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                    <span class="oc-badge accent">
                                        {{ $product['product_name'] }} ({{ $product['stage_label'] }})
                                    </span>

                                    @if(!empty($product['product_count']))
                                        <span class="oc-badge" title="Anzahl">
                                            <i class="feather icon-layers mr-25"></i> {{ $product['product_count'] }}
                                        </span>
                                    @endif

                                    @if(!empty($product['serial_number']))
                                        <span class="oc-badge" title="Seriennummer">
                                            <i class="feather icon-hash mr-25"></i> {{ $product['serial_number'] }}
                                        </span>
                                    @endif

                                    @if(!empty($product['notes']))
                                        <button type="button"
                                                class="icon-note-btn show-note-btn"
                                                data-note="{{ e($product['notes']) }}"
                                                title="Notiz anzeigen">
                                            <i class="feather icon-file-text"></i>
                                        </button>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <span class="oc-badge">Keine</span>
                @endif
            </div>

            <div class="oc-cell" style="text-align:right;">
                <a href="{{ url('/new_lead_profile/' . ($n->customer_id ?? $n->lead_id)) }}"
                   target="_blank"
                   class="oc-btn oc-btn-accent"
                   style="height:34px;font-size:12px;padding:0 12px;">
                    Profil
                </a>
            </div>
        </div>
    @endforeach
@endif