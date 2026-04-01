{{-- ===================== CARD VIEW ===================== --}}
@if($products->count())
    <div class="row product-card-grid">
        @foreach($products as $item)
            @php
                $distList = ($distributorsByProduct ?? collect())->get((int)$item->id, collect());

                $statusText = $item->status === 'Published' ? 'Aktiv' : 'Inaktiv';

                $favCount   = isset($favCountsByProduct)   ? ($favCountsByProduct[$item->id]   ?? 0) : 0;
                $stampCount = isset($stampCountsByProduct) ? ($stampCountsByProduct[$item->id] ?? 0) : 0;

                $plainShort = strip_tags($item->short_description ?? '');
            @endphp

            <div class="col-xl-3 col-lg-4 col-md-6 mb-1">
                <div class="card product-card h-100">
                    <div class="card-body d-flex flex-column">

                        {{-- Header: Checkbox + ID + Name + Brand/Status --}}
                        <div class="d-flex justify-content-between align-items-start product-card-header">
                            <div class="d-flex align-items-start product-card-header-left">
                                <label class="bulk-check">
                                    <input type="checkbox" class="product-select" value="{{ $item->id }}">
                                    <span></span>
                                </label>

                                <div>
                                    <div class="product-card-meta">
                                        Artikelnummer: {{ $item->id }} - Hersteller Nr. {{ $item->article_no ?? '–' }}
                                    </div>
                                    <h5 class="product-card-title">
                                        <a href="{{ url('/product_details/'.$item->id) }}" class="text-body">
                                            {{ \Illuminate\Support\Str::limit($item->product, 40) }}
                                        </a>
                                    </h5>
                                    <div class="product-card-submeta">
                                        {{ $item->model ?: 'Kein Modell' }}
                                    </div>
                                </div>
                            </div>

                            <div class="text-right product-card-header-right">
                                @if($item->brand_name)
                                    <div class="badge product-brand-badge">
                                        {{ $item->brand_name }}
                                    </div>
                                @endif

                                <div class="product-card-status {{ $item->status === 'Published' ? 'published' : 'unpublished' }}">
                                    {{ $statusText }}
                                </div>
                            </div>
                        </div>

                        {{-- Gruppe / Kategorie / Sub-Artikel --}}
                        <div class="product-card-taxonomy">
                            <div>
                                <strong>Artikelgruppe:</strong>
                                {{ $item->article_group ?? '–' }}
                            </div>
                            <div>
                                <strong>Sparte:</strong>
                                {{ $item->category ?? '–' }}
                            </div>
                            <div>
                                <strong>Artikel Kategorie:</strong>
                                {{ $item->sub_article ?? '–' }}
                            </div>
                        </div>

                        {{-- Listen-Badges --}}
                        <div class="product-card-lists d-flex flex-wrap">
                            @if($favCount > 0)
                                <span class="badge badge-pill badge-warning">
                                    <i class="feather icon-star mr-25"></i>
                                    In {{ $favCount }} Favoritenliste{{ $favCount > 1 ? 'n' : '' }}
                                </span>
                            @endif

                            @if($stampCount > 0)
                                <span class="badge badge-pill badge-danger">
                                    <i class="feather icon-award mr-25"></i>
                                    In {{ $stampCount }} Stempel-Liste{{ $stampCount > 1 ? 'n' : '' }}
                                </span>
                            @endif

                            @if($favCount === 0 && $stampCount === 0)
                                <span class="text-muted product-card-no-lists">
                                    In keiner Liste
                                </span>
                            @endif
                        </div>

                        {{-- Kurzbeschreibung (klickbar auf Detailseite) --}}
                        <div class="flex-grow-1 product-card-main"
                             data-details-url="{{ url('/product_details/'.$item->id) }}">
                            <div class="product-card-description">
                                {{ \Illuminate\Support\Str::limit($plainShort, 140) ?: 'Keine Kurzbeschreibung hinterlegt.' }}
                            </div>
                        </div>

                        {{-- Footer: Lieferanten + Aktionen + Listen-Menü --}}
                        <div class="product-card-foot">
                            <div class="product-card-dist">
                                @if($distList->count())
                                    @foreach($distList as $dist)
                                        <span class="badge product-dist-badge">
                                            <i class="fa fa-truck mr-25"></i>
                                            {{ $dist->distributor_name }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-muted product-no-distributor">
                                        Kein Lieferant verknüpft
                                    </span>
                                @endif
                            </div>

                            <div class="d-flex align-items-center product-card-actions">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ url('/product/edit/'.$item->id) }}" style="padding: 11px"
                                       class="btn btn-outline-secondary"
                                       title="Bearbeiten">
                                        <i class="feather icon-edit"></i>
                                    </a>

                                    {{-- DUPLICATE BUTTON --}}
                                    <button type="button" style="padding: 11px"
                                            class="btn btn-outline-info js-duplicate-product"
                                            data-product-id="{{ $item->id }}"
                                            title="Duplizieren">
                                        <i class="feather icon-copy"></i>
                                    </button>

                                    @if($item->status === 'Published')
                                        <a href="{{ url('/product_unpublish/'.$item->id) }}" style="padding: 11px"
                                           class="btn btn-outline-danger"
                                           title="Deaktivieren">
                                            <i class="feather icon-slash"></i>
                                        </a>
                                    @else
                                        <a href="{{ url('/product_publish/'.$item->id) }}" style="padding: 11px"
                                           class="btn btn-outline-success"
                                           title="Aktivieren">
                                            <i class="feather icon-check"></i>
                                        </a>
                                    @endif

                                    <a href="{{ route('product.destroy', $item->id) }}" style="padding: 11px"
                                       class="btn btn-outline-danger"
                                       onclick="return confirm('Dieses Produkt wirklich löschen?')"
                                       title="Löschen">
                                        <i class="feather icon-trash"></i>
                                    </a>
                                </div>

                                {{-- Listen-Menü (custom JS, kein Bootstrap JS) --}}
                                <div class="btn-group btn-group-sm list-menu-container">
                                    <button type="button" style="padding: 11px"
                                            class="btn btn-outline-primary js-menu-toggle"
                                            title="Zu Liste hinzufügen / entfernen">
                                        <i class="feather icon-folder-plus"></i>
                                    </button>

                                    <div class="dropdown-menu dropdown-menu-right custom-menu">
                                        {{-- ADD --}}
                                        <button type="button"
                                                class="dropdown-item js-add-to-list"
                                                data-product-id="{{ $item->id }}"
                                                data-list-type="favorite">
                                            <i class="feather icon-star mr-50"></i>
                                            Zu Favoriten-Liste hinzufügen
                                        </button>
                                        <button type="button"
                                                class="dropdown-item js-add-to-list"
                                                data-product-id="{{ $item->id }}"
                                                data-list-type="stamp">
                                            <i class="feather icon-award mr-50"></i>
                                            Zu Stempel-Liste hinzufügen
                                        </button>

                                        <div class="dropdown-divider"></div>

                                        {{-- REMOVE --}}
                                        <button type="button"
                                                class="dropdown-item js-remove-from-list"
                                                data-product-id="{{ $item->id }}"
                                                data-list-type="favorite">
                                            <i class="feather icon-x-circle mr-50"></i>
                                            Aus Favoriten-Liste entfernen
                                        </button>
                                        <button type="button"
                                                class="dropdown-item js-remove-from-list"
                                                data-product-id="{{ $item->id }}"
                                                data-list-type="stamp">
                                            <i class="feather icon-x-circle mr-50"></i>
                                            Aus Stempel-Liste entfernen
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div> {{-- /foot --}}

                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center text-muted py-3">
        Keine Produkte gefunden. Passen Sie Ihre Suche oder Filter an.
    </div>
@endif
