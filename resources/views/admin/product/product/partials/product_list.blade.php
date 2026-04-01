{{-- ===================== MODERN LIST VIEW ===================== --}}
@if($products->count())
    <style>
        .product-modern-list{
            display:flex;
            flex-direction:column;
            gap:14px;
        }

        .product-modern-head{
            display:grid;
            grid-template-columns: 42px 84px minmax(260px, 1.55fr) minmax(180px, 1fr) minmax(150px, .9fr) minmax(170px, 1fr) 120px 190px;
            gap:14px;
            align-items:center;
            padding:0 18px 10px 18px;
            color:#6b7280;
            font-size:11px;
            font-weight:900;
            text-transform:uppercase;
            letter-spacing:.06em;
        }

        .product-modern-item{
            background:#ffffff;
            border:1px solid rgba(15,23,42,.08);
            border-radius:18px;
            box-shadow:0 10px 30px rgba(15,23,42,.07);
            transition:all .18s ease;
            overflow:hidden;
        }

        .product-modern-item:hover{
            border-color:rgba(116,178,212,.55);
            box-shadow:0 18px 40px rgba(15,23,42,.12);
            transform:translateY(-2px);
        }

        .product-modern-row{
            display:grid;
            grid-template-columns: 42px 84px minmax(260px, 1.55fr) minmax(180px, 1fr) minmax(150px, .9fr) minmax(170px, 1fr) 120px 190px;
            gap:14px;
            align-items:center;
            padding:16px 18px;
        }

        .product-modern-cell{
            min-width:0;
        }

        .product-modern-mobile-label{
            display:none;
            font-size:11px;
            font-weight:800;
            color:#6b7280;
            text-transform:uppercase;
            letter-spacing:.05em;
            margin-bottom:4px;
        }

        .product-modern-id{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-width:52px;
            height:28px;
            padding:0 10px;
            border-radius:999px;
            background:#eff6ff;
            color:#2563eb;
            font-size:12px;
            font-weight:800;
        }

        .product-modern-media{
            width:68px;
            height:68px;
            border-radius:16px;
            overflow:hidden;
            border:1px solid rgba(148,163,184,.22);
            background:linear-gradient(180deg,#f8fafc 0%,#eef2f7 100%);
            display:flex;
            align-items:center;
            justify-content:center;
            box-shadow:0 4px 12px rgba(15,23,42,.08);
        }

        .product-modern-media img{
            width:100%;
            height:100%;
            object-fit:cover;
            display:block;
        }

        .product-modern-media-placeholder{
            color:#94a3b8;
            font-size:1rem;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .product-modern-title{
            display:flex;
            align-items:center;
            flex-wrap:wrap;
            gap:8px;
            margin-bottom:4px;
        }

        .product-modern-title a{
            font-size:15px;
            font-weight:800;
            color:#111827;
            text-decoration:none;
            line-height:1.35;
        }

        .product-modern-title a:hover{
            color:#2563eb;
            text-decoration:none;
        }

        .product-modern-sub{
            font-size:12px;
            color:#6b7280;
            line-height:1.55;
        }

        .product-modern-chip{
            display:inline-flex;
            align-items:center;
            padding:4px 9px;
            border-radius:999px;
            background:#f8fafc;
            border:1px solid #e5e7eb;
            font-size:11px;
            font-weight:700;
            color:#475569;
            margin:0 6px 6px 0;
            white-space:nowrap;
        }

        .product-modern-chip.brand{
            background:rgba(37,99,235,.07);
            border-color:rgba(37,99,235,.12);
            color:#1d4ed8;
        }

        .product-modern-chip.group{
            background:rgba(147,194,28,.10);
            border-color:rgba(147,194,28,.18);
            color:#6b8b12;
        }

        .product-modern-chip.dist{
            background:rgba(15,23,42,.04);
            border-color:rgba(148,163,184,.28);
            color:#334155;
        }

        .product-modern-meta-stack{
            display:flex;
            flex-direction:column;
            gap:6px;
        }

        .product-modern-badges{
            display:flex;
            flex-direction:column;
            gap:7px;
            align-items:flex-start;
        }

        .product-modern-list-badge{
            display:inline-flex;
            align-items:center;
            gap:6px;
            padding:6px 10px;
            border-radius:999px;
            font-size:11px;
            font-weight:800;
            line-height:1;
            white-space:nowrap;
        }

        .product-modern-list-badge.fav{
            background:#fffbeb;
            color:#b45309;
            border:1px solid rgba(245,158,11,.22);
        }

        .product-modern-list-badge.stamp{
            background:#fef2f2;
            color:#b91c1c;
            border:1px solid rgba(239,68,68,.20);
        }

        .product-modern-list-empty{
            font-size:12px;
            color:#94a3b8;
            font-weight:600;
        }

        .product-modern-status-wrap{
            display:flex;
            justify-content:flex-start;
        }

        .product-modern-actions{
            display:flex;
            align-items:center;
            justify-content:flex-end;
            gap:8px;
            flex-wrap:wrap;
        }

        .product-modern-btn-group{
            display:flex;
            align-items:center;
            gap:6px;
            flex-wrap:wrap;
        }

        .product-modern-btn{
            width:36px;
            height:36px;
            border-radius:10px;
            border:1px solid #e5e7eb;
            background:#ffffff;
            color:#475569;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            cursor:pointer;
            transition:all .16s ease;
            text-decoration:none;
        }

        .product-modern-btn:hover{
            transform:translateY(-1px);
            box-shadow:0 8px 18px rgba(15,23,42,.10);
            text-decoration:none;
        }

        .product-modern-btn.edit:hover{
            color:#2563eb;
            border-color:rgba(37,99,235,.28);
            background:#eff6ff;
        }

        .product-modern-btn.copy:hover{
            color:#0891b2;
            border-color:rgba(8,145,178,.28);
            background:#ecfeff;
        }

        .product-modern-btn.toggle:hover{
            border-color:rgba(34,197,94,.28);
            background:#ecfdf5;
            color:#15803d;
        }

        .product-modern-btn.disable:hover{
            border-color:rgba(239,68,68,.24);
            background:#fef2f2;
            color:#dc2626;
        }

        .product-modern-btn.delete:hover{
            border-color:rgba(239,68,68,.24);
            background:#fef2f2;
            color:#dc2626;
        }

        .product-modern-btn.folder:hover{
            color:#2563eb;
            border-color:rgba(37,99,235,.24);
            background:#eff6ff;
        }

        @media (max-width: 1399.98px){
            .product-modern-head,
            .product-modern-row{
                grid-template-columns: 42px 84px minmax(240px,1.45fr) minmax(160px,.95fr) minmax(130px,.8fr) minmax(150px,.95fr) 110px 170px;
            }
        }

        @media (max-width: 1199.98px){
            .product-modern-head{
                display:none;
            }

            .product-modern-row{
                grid-template-columns: 42px 84px 1fr;
                gap:14px;
                align-items:flex-start;
            }

            .product-modern-cell{
                grid-column:3;
            }

            .product-modern-cell.is-check,
            .product-modern-cell.is-image{
                grid-column:auto;
            }

            .product-modern-mobile-label{
                display:block;
            }

            .product-modern-actions{
                justify-content:flex-start;
            }

            .product-modern-status-wrap{
                justify-content:flex-start;
            }
        }

        @media (max-width: 767.98px){
            .product-modern-row{
                grid-template-columns: 1fr;
                padding:14px;
            }

            .product-modern-cell,
            .product-modern-cell.is-check,
            .product-modern-cell.is-image{
                grid-column:auto;
            }

            .product-modern-check-wrap{
                display:flex;
                align-items:center;
                justify-content:space-between;
            }

            .product-modern-media{
                width:100%;
                max-width:100px;
                height:100px;
            }
        }
    </style>

    <div class="product-modern-list">

        <div class="product-modern-head">
            <div>
                <label class="bulk-check mb-0">
                    <input type="checkbox" id="select-all-page">
                    <span></span>
                </label>
            </div>
            <div>Bild</div>
            <div>Artikel</div>
            <div>Gruppe / Kategorie</div>
            <div>Hersteller</div>
            <div>Lieferanten</div>
            <div>Status / Listen</div>
            <div style="text-align:right;">Aktionen</div>
        </div>

        @foreach($products as $item)
            @php
                $distList = ($distributorsByProduct ?? collect())->get((int)$item->id, collect());
                $statusText = $item->status === 'Published' ? 'Aktiv' : 'Inaktiv';
                $favCount   = isset($favCountsByProduct) ? ($favCountsByProduct[$item->id] ?? 0) : 0;
                $stampCount = isset($stampCountsByProduct) ? ($stampCountsByProduct[$item->id] ?? 0) : 0;
            @endphp

            <div class="product-modern-item">
                <div class="product-modern-row">

                    {{-- CHECKBOX --}}
                    <div class="product-modern-cell is-check">
                        <div class="product-modern-check-wrap">
                            <label class="bulk-check mb-0">
                                <input type="checkbox" class="product-select" value="{{ $item->id }}">
                                <span></span>
                            </label>
                        </div>
                    </div>

                    {{-- IMAGE --}} 
                        <div class="product-modern-cell is-image">
                            <div class="product-modern-mobile-label">Bild</div>

                            <button type="button"
                                    class="product-modern-media js-open-image-modal"
                                    data-product-id="{{ $item->id }}"
                                    data-product-name="{{ e($item->product) }}"
                                    data-image="{{ !empty($item->product_image) ? asset('images/products/' . $item->product_image) : '' }}"
                                    title="Bild ändern"
                                    style="padding:0; cursor:pointer;">
                                @if(!empty($item->product_image))
                                    <img src="{{ asset('images/products/' . $item->product_image) }}"
                                        alt="{{ $item->product }}">
                                @else
                                    <div class="product-modern-media-placeholder">
                                        <i class="feather icon-image"></i>
                                    </div>
                                @endif
                            </button>
                        </div>

                    {{-- ARTICLE --}}
                    <div class="product-modern-cell">
                        <div class="product-modern-mobile-label">Artikel</div>

                        <div class="product-modern-title">
                            <span class="product-modern-id">#{{ $item->id }}</span>
                            <a href="{{ url('/product_details/'.$item->id) }}">
                                {{ \Illuminate\Support\Str::limit($item->product, 70) }}
                            </a>
                        </div>

                        <div class="product-modern-sub">
                            <strong>Art.Nr.:</strong> {{ $item->article_no ?? '–' }}
                            &nbsp;·&nbsp;
                            <strong>Modell:</strong> {{ $item->model ?: 'Kein Modell' }}
                        </div>
                    </div>

                    {{-- GROUP / CATEGORY --}}
                    <div class="product-modern-cell">
                        <div class="product-modern-mobile-label">Gruppe / Kategorie</div>

                        <div class="product-modern-meta-stack">
                            <div>
                                <span class="product-modern-chip group">
                                    {{ $item->article_group ?? '–' }}
                                </span>
                            </div>
                            <div class="product-modern-sub">
                                {{ $item->category ?? '–' }}
                                @if(!empty($item->sub_article))
                                    &nbsp;·&nbsp; {{ $item->sub_article }}
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- BRAND --}}
                    <div class="product-modern-cell">
                        <div class="product-modern-mobile-label">Hersteller</div>

                        @if(!empty($item->brand_name))
                            <span class="product-modern-chip brand">{{ $item->brand_name }}</span>
                        @else
                            <span class="product-modern-list-empty">Kein Hersteller</span>
                        @endif
                    </div>
  
  
                   {{-- DISTRIBUTORS --}}
                    <div class="product-modern-cell">
                        <div class="product-modern-mobile-label">Lieferanten</div>

                        @if($distList->count())
                            @php
                                $visibleDistributors = $distList->take(2);
                                $remainingCount = max($distList->count() - 2, 0);
                            @endphp

                            <div class="product-modern-distributor-list">
                                @foreach($visibleDistributors as $dist)
                                    <div class="product-modern-distributor-item">
                                        <div class="product-modern-distributor-top">
                                            <i class="fa fa-truck"></i>
                                            <div class="product-modern-distributor-name">{{ $dist->distributor_name }}</div>
                                        </div>

                                        <div class="product-modern-distributor-price">
                                            <small>{{ $dist->display_price_label }}:</small>
                                            <span>
                                                {{ $dist->display_price !== null ? number_format((float)$dist->display_price, 2, ',', '.') . ' €' : '–' }}
                                            </span>

                                            @if($dist->price_badge === 'Günstigster')
                                                <span class="product-modern-distributor-badge cheapest">Günstigster</span>
                                            @elseif($dist->price_badge === 'Teuerster')
                                                <span class="product-modern-distributor-badge expensive">Teuerster</span>
                                            @endif
                                        </div>

                                        @if(!empty($dist->article_no) || !empty($dist->availability))
                                            <div class="product-modern-distributor-meta">
                                                @if(!empty($dist->article_no))
                                                    Art.-Nr.: {{ $dist->article_no }}
                                                @endif

                                                @if(!empty($dist->article_no) && !empty($dist->availability))
                                                    &nbsp;·&nbsp;
                                                @endif

                                                @if(!empty($dist->availability))
                                                    {{ $dist->availability }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                                @if($remainingCount > 0)
                                    <button type="button"
                                            class="product-modern-distributor-more js-open-distributor-modal"
                                            data-product-name="{{ e($item->product) }}"
                                            data-distributors='@json($distList->values())'>
                                        +{{ $remainingCount }} weitere
                                    </button>
                                @endif
                            </div>
                        @else
                            <span class="product-modern-list-empty">Keine Lieferanten</span>
                        @endif
                    </div>

                    {{-- ACTIONS --}}
                   <div class="product-modern-btn-group">
                        <a href="{{ url('/product/edit/'.$item->id) }}"
                        class="product-modern-btn edit"
                        title="Bearbeiten">
                            <i class="feather icon-edit"></i>
                        </a>

                        <button type="button"
                                class="product-modern-btn copy js-duplicate-product"
                                data-product-id="{{ $item->id }}"
                                title="Duplizieren">
                            <i class="feather icon-copy"></i>
                        </button>

                        <button type="button"
                                class="product-modern-btn"
                                style="color:#6b8b12; border-color:rgba(147,194,28,.22); background:#f4fae7;"
                                data-product-id="{{ $item->id }}"
                                title="In Cart hinzufügen"
                                onclick="event.preventDefault(); event.stopPropagation(); addProductToCart('{{ $item->id }}');">
                            <i class="feather icon-shopping-cart"></i>
                        </button>

                        @if($item->status === 'Published')
                            <a href="{{ url('/product_unpublish/'.$item->id) }}"
                            class="product-modern-btn disable"
                            title="Deaktivieren">
                                <i class="feather icon-slash"></i>
                            </a>
                        @else
                            <a href="{{ url('/product_publish/'.$item->id) }}"
                            class="product-modern-btn toggle"
                            title="Aktivieren">
                                <i class="feather icon-check"></i>
                            </a>
                        @endif

                        <a href="{{ route('product.destroy', $item->id) }}"
                        class="product-modern-btn delete"
                        onclick="return confirm('Dieses Produkt wirklich löschen?')"
                        title="Löschen">
                            <i class="feather icon-trash"></i>
                        </a>
                    </div>

                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center text-muted py-4">
        Keine Produkte gefunden. Passen Sie Ihre Suche oder Filter an.
    </div>
@endif