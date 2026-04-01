{{-- STYLES FOR SUPPLIER PRICES --}}
@section('style')
@parent
<style>
    .supplier-card {
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 18px 45px rgba(15,23,42,.08);
        border: 1px solid rgba(15,23,42,.06);
    }

    .supplier-card .card-header {
        border-bottom: 1px solid rgba(15,23,42,.05);
        padding: 0.9rem 1.25rem;
    }

    .supplier-card .card-body {
        padding: 1rem 1.25rem 1.1rem;
    }

    .supplier-section-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0;
    }

    .supplier-section-subtitle {
        color: #6b7280;
        font-size: 0.8rem;
    }

    .supplier-badge-stat {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #9ca3af;
    }

    .supplier-stat-value {
        font-weight: 600;
        font-size: .9rem;
    }

    .supplier-form-card {
        border-radius: 14px;
        background: #f9fafb;
        border: 1px solid rgba(148,163,184,.35);
        padding: 0.85rem 1rem;
        margin-top: .9rem;
    }

    .supplier-form-card h6 {
        font-size: .9rem;
        font-weight: 600;
        margin-bottom: .4rem;
    }

    .supplier-form-card .form-group {
        margin-bottom: 0.5rem;
    }

    .supplier-form-card label {
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #6b7280;
        margin-bottom: 0.15rem;
    }

    .supplier-form-card .form-control,
    .supplier-form-card .custom-select {
        height: 34px;
        padding: .2rem .5rem;
        font-size: .8rem;
    }

    .supplier-price-table thead th {
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #6b7280;
        border-top: none;
        white-space: nowrap;
    }

    .supplier-price-table tbody td {
        font-size: .82rem;
        vertical-align: middle;
    }

    .supplier-price-empty {
        padding: .7rem 0;
        text-align: center;
        font-size: .82rem;
        color: #9ca3af;
    }

    #supplier-calc-info {
        display: none;
        margin-top: .35rem;
        padding: .35rem .55rem;
        border-radius: 10px;
        background: #ecfdf3;
        border: 1px dashed #16a34a;
        font-size: .75rem;
        color: #166534;
    }

    #supplier-price-errors {
        display: none;
        margin-top: .4rem;
        font-size: .78rem;
    }

    input[type="number"].no-spinner::-webkit-inner-spin-button,
    input[type="number"].no-spinner::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type="number"].no-spinner {
        -moz-appearance: textfield;
    }

    .supplier-filters .form-control-sm {
        height: 32px;
        font-size: .78rem;
        padding: .1rem .4rem;
    }

    .supplier-filters label {
        font-size: .78rem;
        margin-bottom: 0;
        color: #6b7280;
    }
</style>
@endsection

{{-- LIEFERANTEN & PREISE --}}
<div class="card supplier-card mb-2">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div>
            <h4 class="supplier-section-title">
                <i class="feather icon-truck mr-50"></i> Lieferanten &amp; Preise
            </h4>
            <small class="supplier-section-subtitle">
                Hinterlegen Sie Einkaufspreise, Rabattgruppen und Verfügbarkeit je Lieferant.
            </small>
        </div>

        <div class="d-flex align-items-center">
            <div class="mr-2 text-right">
                <div class="supplier-badge-stat">Aktive Lieferanten</div>
                <div class="supplier-stat-value" id="supplier-count">0</div>
            </div>

            <button type="button"
                    class="btn btn-sm btn-outline-primary mr-1"
                    data-toggle="modal"
                    data-target="#distributors">
                <i class="feather icon-plus"></i> Neuer Lieferant
            </button>

            <button type="button"
                    class="btn btn-sm btn-outline-success"
                    id="saveDistributorStage">
                <i class="feather icon-save"></i> Abschnitt speichern
            </button>
        </div>
    </div>

    <div class="card-body">

        @php
            $discountGroupOptions = '';
            foreach($discount_group as $dg) {
                $discountGroupOptions .= "<option value='{$dg->id}' data-percent='{$dg->discount}'>{$dg->discount_group} - {$dg->discount}%</option>";
            }
        @endphp

        {{-- Lieferanten-Auswahl (Multi) --}}
        <div class="mb-1">
            <label class="font-weight-600 small text-uppercase text-muted d-block mb-50">
                Lieferanten dem Produkt zuordnen
            </label>
            <div class="d-flex">
                <select id="distributor"
                        name="distributor[]"
                        class="form-control"
                        multiple="multiple"
                        style="width:100%">
                    {{-- Options via AJAX --}}
                </select>

                <button type="button"
                        class="btn btn-icon btn-outline-primary ml-1"
                        data-toggle="modal"
                        data-target="#distributors"
                        title="Neuen Lieferanten anlegen">
                    <i class="feather icon-plus"></i>
                </button>
            </div>
            <small class="text-muted d-block mt-25">
                Wählen Sie hier alle Lieferanten, die grundsätzlich dieses Produkt anbieten.
            </small>
        </div>

        {{-- Filter / Suche --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap mb-1 supplier-filters">
            <div class="form-inline mb-50">
                <label class="mr-50 mb-0 small text-muted">
                    Suche:
                </label>
                <input type="text"
                       id="distributorPriceSearch"
                       class="form-control form-control-sm"
                       placeholder="Lieferant, Art.-Nr., Verfügbarkeit…">
            </div>

            <div class="form-inline mb-50">
                <label class="mr-50 mb-0 small text-muted">Verfügbarkeit:</label>
                <select id="availabilityFilter"
                        class="form-control form-control-sm">
                    <option value="">Alle</option>
                    <option value="Sofort Lieferbar">Sofort Lieferbar</option>
                    <option value="Auf Anfrage">Auf Anfrage</option>
                    <option value="Nicht verfügbar">Nicht verfügbar</option>
                </select>
            </div>
        </div>

        {{-- Tabelle: Lieferantenpreise --}}
        <div class="table-responsive">
            <table class="table table-sm table-hover supplier-price-table" id="distributor_price">
                <thead class="thead-light">
                    <tr>
                        <th>Lieferant</th>
                        <th>Art.-Nr.</th>
                        <th>UVP<br><small class="text-muted">Listenpreis</small></th>
                        <th>EK-Preis</th>
                        <th>Rabattgruppe</th>
                        <th>Rabatt &euro;</th>
                        <th>Rabatt %</th>
                        <th>Datum</th>
                        <th>Verfügbarkeit</th>
                        <th class="text-right">Aktion</th>
                    </tr>
                </thead>
                <tbody id="supplier-prices-tbody">
                    @isset($distributorPrices)
                        @foreach($distributorPrices as $row)
                            @include('admin.product.product.partials._distributor_price_row', ['row' => $row])
                        @endforeach
                    @endisset
                </tbody>
            </table>
        </div>
        <div id="supplier-prices-empty"
             class="supplier-price-empty {{ isset($distributorPrices) && count($distributorPrices) ? 'd-none' : '' }}">
            Noch keine Preise hinterlegt. Legen Sie unten einen ersten Lieferantenpreis an.
        </div>

        {{-- Formular: Lieferantenpreis anlegen / bearbeiten --}}
        <div class="supplier-form-card">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="mb-0">
                    <i class="feather icon-calculator mr-50"></i> Preisrechner &amp; Neuer Lieferantenpreis
                </h6>
                <small class="text-muted">
                    Füllen Sie mindestens UVP plus Rabatt oder Einkaufspreis aus.
                </small>
            </div>

            <form id="supplier-price-form" method="post" action="{{ route('product.distributor.storeSingle', $data->id) }}">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        {{-- Lieferant --}}
                        <div class="form-group">
                            <label>Lieferant</label>
                            <select id="supplier_distributor_id"
                                    name="distributor_id"
                                    class="form-control">
                                <option value="">Bitte wählen…</option>
                                @foreach($distributors ?? [] as $dist)
                                    <option value="{{ $dist->id }}">{{ $dist->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Rabattgruppe --}}
                        <div class="form-group">
                            <label>Rabattgruppe</label>
                            <select id="supplier_discount_group_id"
                                    name="discount_group_id"
                                    class="form-control">
                                <option value="">Keine</option>
                                {!! $discountGroupOptions !!}
                            </select>
                        </div>

                        {{-- Art.-Nr. --}}
                        <div class="form-group">
                            <label>Artikelnummer</label>
                            <input type="text"
                                   class="form-control"
                                   name="article_no"
                                   placeholder="Art.-Nr. des Lieferanten">
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="form-row">
                            <div class="form-group col-6">
                                <label>UVP / Listenpreis (&euro;)</label>
                                <input type="number"
                                       id="sp_price"
                                       name="price"
                                       class="form-control no-spinner"
                                       step="0.01"
                                       inputmode="decimal"
                                       placeholder="z. B. 999,00">
                            </div>
                            <div class="form-group col-6">
                                <label>EK-Preis (&euro;)</label>
                                <input type="number"
                                       id="sp_purchase_price"
                                       name="purchase_price"
                                       class="form-control no-spinner"
                                       step="0.01"
                                       inputmode="decimal"
                                       placeholder="z. B. 749,00">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-6">
                                <label>Rabatt &euro;</label>
                                <input type="number"
                                       id="sp_discount_price"
                                       name="discount_price"
                                       class="form-control no-spinner"
                                       step="0.01"
                                       inputmode="decimal"
                                       placeholder="z. B. 250,00">
                            </div>
                            <div class="form-group col-6">
                                <label>Rabatt %</label>
                                <input type="number"
                                       id="sp_discount_percent"
                                       name="discount_percent"
                                       class="form-control no-spinner"
                                       step="0.01"
                                       inputmode="decimal"
                                       placeholder="z. B. 25">
                            </div>
                        </div>

                        <div id="supplier-calc-info">
                            <span id="supplier-calc-text"></span>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Preisdatum</label>
                            <input type="date"
                                   class="form-control"
                                   name="price_date"
                                   value="{{ now()->toDateString() }}">
                        </div>

                        <div class="form-group">
                            <label>Verfügbarkeit</label>
                            <select name="availability"
                                    class="form-control">
                                <option value="">Bitte wählen…</option>
                                <option value="Sofort Lieferbar">Sofort Lieferbar</option>
                                <option value="Auf Anfrage">Auf Anfrage</option>
                                <option value="Nicht verfügbar">Nicht verfügbar</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status"
                                    class="form-control">
                                <option value="Published">Veröffentlicht</option>
                                <option value="Unpublished">Entwurf</option>
                            </select>
                        </div>

                        <div class="text-right mt-1">
                            <button type="submit"
                                    class="btn btn-sm btn-success">
                                <i class="feather icon-save mr-25"></i>
                                Preis speichern
                            </button>
                        </div>
                    </div>
                </div>

                <div id="supplier-price-errors" class="alert alert-danger"></div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Neuer Lieferant (unverändert, nur leicht gestrafft) --}}
<div class="modal fade text-left" id="distributors" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel16">
                    <i class="feather icon-truck mr-50"></i> Neuer Lieferant
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <form id="distributorForm" class="form-horizontal" novalidate enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        {{-- Stammdaten --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Unternehmen / Marke</label>
                                <input type="text" class="form-control" name="name" required>
                                <p class="text-danger mb-0" id="name-error"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Adresse</label>
                                <input type="text" class="form-control" name="address" required>
                                <p class="text-danger mb-0" id="address-error"></p>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group mb-0">
                                <label>Logo <code>.PNG</code></label>
                                <input type="file" class="form-control" name="image" required>
                                <p class="text-danger mb-0" id="image-error"></p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- Ansprechpartner --}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center mb-50">
                                <h6 class="mb-0">Abteilungen & Ansprechpartner</h6>
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        id="add_distributor">
                                    <i class="feather icon-plus"></i> Zeile hinzufügen
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm" id="add_distributor_department">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Abteilung</th>
                                            <th>Ansprechpartner</th>
                                            <th>Position</th>
                                            <th>E-Mail</th>
                                            <th>Mobil</th>
                                            <th>Festnetz</th>
                                            <th>Büro</th>
                                            <th class="text-right">Aktion</th>
                                        </tr>
                                    </thead>
                                    <tbody id="distributor_department_body">
                                        <tr>
                                            <td><input type="text" class="form-control" name="d[0][d_department]" placeholder="Abteilung"></td>
                                            <td><input type="text" class="form-control" name="d[0][name]" placeholder="Gesprächspartner"></td>
                                            <td><input type="text" class="form-control" name="d[0][position]" placeholder="Position"></td>
                                            <td><input type="email" class="form-control" name="d[0][email]" placeholder="E-Mail"></td>
                                            <td><input type="text" class="form-control" name="d[0][phone]" placeholder="Mobilnummer"></td>
                                            <td><input type="text" class="form-control" name="d[0][home]" placeholder="Festnetznummer"></td>
                                            <td><input type="text" class="form-control" name="d[0][office]" placeholder="Büro-Telefonnummer"></td>
                                            <td class="text-right">
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger distributor_remove">
                                                    <i class="feather icon-trash-2"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveDistributorBtn">
                        <i class="feather icon-save"></i> Speichern
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Schließen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
