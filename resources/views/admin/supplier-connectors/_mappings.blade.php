<div class="map-card">
    <div class="map-card-header">
        <div>
            <h2 class="map-card-title">Feld-Mappings</h2>
            <div class="map-card-desc">
                Lege fest, welches Feld vom Lieferanten in welches Laravel-Feld gespeichert wird.
                Beispiel: <strong>NEW_ITEM-DESCRIPTION</strong> → <strong>products.product</strong>.
            </div>
        </div>
    </div>

    <div class="map-info">
        Guter Start für IDS/OCI:
        <strong>NEW_ITEM-DESCRIPTION → products.product</strong>,
        <strong>NEW_ITEM-EAN → products.ean</strong>,
        <strong>NEW_ITEM-VENDORMAT → distributor_prices.article_no</strong>,
        <strong>NEW_ITEM-PRICE → distributor_prices.purchase_price</strong>.
    </div>

    <form method="POST" action="{{ route('admin.supplier-connectors.mappings.store', $connection) }}"
        style="margin-bottom:18px;">
        @csrf

        <div class="map-grid">
            <div>
                <label class="map-label">Quelle vom Lieferanten</label>
                <input class="map-input" name="source_field" placeholder="z.B. NEW_ITEM-DESCRIPTION">
            </div>

            <div>
                <label class="map-label">Laravel Tabelle</label>
                <select class="map-select" name="target_table">
                    @foreach(['products', 'brands', 'distributors', 'distributor_prices'] as $table)
                        <option value="{{ $table }}">{{ $table }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="map-label">Laravel Feld</label>
                <input class="map-input" name="target_field" placeholder="z.B. product">
            </div>

            <div>
                <label class="map-label">Umwandlung</label>
                <select class="map-select" name="transformer">
                    <option value="">Keine Umwandlung</option>
                    @foreach(['text', 'html_strip', 'decimal', 'integer', 'uppercase', 'lowercase', 'boolean'] as $transformer)
                        <option value="{{ $transformer }}">{{ $transformer }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="map-grid-bottom">
            <div>
                <label class="map-label">Standardwert</label>
                <input class="map-input" name="default_value" placeholder="Optionaler Standardwert">
            </div>

            <div>
                <label class="map-label">Sortierung</label>
                <input class="map-input" name="sort_order" type="number" value="0">
            </div>

            <label class="map-check">
                <input type="checkbox" name="is_required" value="1">
                Pflichtfeld
            </label>

            <label class="map-check">
                <input type="checkbox" name="is_active" value="1" checked>
                Aktiv
            </label>
        </div>

        <div style="margin-top:14px;">
            <button class="map-btn map-btn-primary" type="submit">
                <i data-lucide="plus-circle"></i>
                Mapping hinzufügen
            </button>
        </div>
    </form>

    @if($connection->mappings->count())
            <div class="map-table-wrap">
                <table class="map-table">
                    <thead>
                        <tr>
                            <th>Quelle</th>
                            <th>Tabelle</th>
                            <th>Feld</th>
                            <th>Umwandlung</th>
                            <th>Standard</th>
                            <th>Pflicht</th>
                            <th>Aktiv</th>
                            <th>Sort</th>
                            <th style="text-align:right;">Aktion</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($connection->mappings as $mapping)
                                        <tr>
                                            <form method="POST" action="{{ route('admin.supplier-connectors.mappings.update', $mapping) }}">
                                                @csrf
                                                @method('PUT')

                                                <td>
                                                    <input class="map-input" name="source_field" value="{{ $mapping->source_field }}">
                                                </td>

                                                <td>
                                                    <select class="map-select" name="target_table">
                                                        @foreach(['products', 'brands', 'distributors', 'distributor_prices'] as $table)
                                                            <option value="{{ $table }}" @selected($mapping->target_table === $table)>
                                                                {{ $table }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>

                                                <td>
                                                    <input class="map-input" name="target_field" value="{{ $mapping->target_field }}">
                                                </td>

                                                <td>
                                                    <select class="map-select" name="transformer">
                                                        <option value="">-</option>
                                                        @foreach(['text', 'html_strip', 'decimal', 'integer', 'uppercase', 'lowercase', 'boolean'] as $transformer)
                                                            <option value="{{ $transformer }}" @selected($mapping->transformer === $transformer)>
                                                                {{ $transformer }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>

                                                <td>
                                                    <input class="map-input" name="default_value" value="{{ $mapping->default_value }}">
                                                </td>

                                                <td style="text-align:center;">
                                                    <input type="checkbox" name="is_required" value="1" @checked($mapping->is_required)>
                                                </td>

                                                <td style="text-align:center;">
                                                    <input type="checkbox" name="is_active" value="1" @checked($mapping->is_active)>
                                                </td>

                                                <td>
                                                    <input class="map-input" name="sort_order" type="number" value="{{ $mapping->sort_order }}">
                                                </td>

                                                <td>
                                                    <div class="map-row-actions">
                                                        <button class="map-btn map-btn-primary" type="submit">
                                                            Speichern
                                                        </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.supplier-connectors.mappings.destroy', $mapping) }}"
                                                onsubmit="return confirm('Mapping wirklich löschen?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="map-btn map-btn-danger" type="submit">
                                                    Löschen
                                                </button>
                                            </form>
                            </div>
                            </td>
                            </tr>
                        @endforeach
            </tbody>
            </table>
        </div>
    @else
    <div class="sc-empty">
        <strong>Noch keine Mappings vorhanden.</strong>
        Lege zuerst fest, welche Felder vom Lieferanten in deine Artikel-, Hersteller- und Preisfelder übernommen werden
        sollen.
    </div>
@endif
</div>