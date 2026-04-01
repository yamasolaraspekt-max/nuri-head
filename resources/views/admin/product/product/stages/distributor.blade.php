{{-- resources/views/admin/product/product/stages/distributor.blade.php --}}

{{-- LIEFERANTEN & PREISE --}}
<div class="card shadow-sm mb-2" id="stage-distributors">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
        <div class="mb-50">
            <h4 class="card-title mb-0">
                <i class="feather icon-truck mr-50"></i> Lieferanten & Preise
            </h4>
            <small class="text-muted">
                Standard: Lieferant, Art.-Nr., EK & Verfügbarkeit. Optional können weitere Details ergänzt werden.
            </small>
        </div>

        <div class="d-flex align-items-center">
            <div class="mr-2 text-right">
                <div class="small text-muted">Aktive Lieferanten</div>
                <div class="font-weight-bold" id="supplier-count">0</div>
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

    <div class="card-body pt-1 pb-1">

        {{-- Advanced toggle --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap mb-1">
            <div class="custom-control custom-checkbox mb-50">
                <input type="checkbox" class="custom-control-input" id="toggleAdvancedPrices">
                <label class="custom-control-label" for="toggleAdvancedPrices">
                    Weitere Details hinzufügen (UVP, Rabatt, Rabattgruppe, Datum, Notiz)
                </label>
            </div>

            <small class="text-muted mb-50">
                <i class="feather icon-info mr-25"></i> Standard-Felder sind für schnelle Erfassung.
            </small>
        </div>

        <style>
            /* default: advanced hidden */
            #distributor_price .adv-col { display: none; }
            #distributor_price.adv-on .adv-col { display: table-cell; }
        </style>

        {{-- Lieferanten-Auswahl --}}
        <div class="mb-1">
            <label class="font-weight-600 small text-uppercase text-muted d-block mb-50">
                Lieferanten auswählen
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
                Tipp: Auswahl aktualisiert die Tabelle automatisch (für neu hinzugefügte Lieferanten werden Zeilen erzeugt).
            </small>
        </div>

        {{-- Filter / Suche --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap mb-1">
            <div class="form-inline mb-50">
                <label class="mr-50 mb-0 small text-muted">Suche:</label>
                <input type="text"
                       id="distributorPriceSearch"
                       class="form-control form-control-sm"
                       placeholder="Lieferant, Art.-Nr., Verfügbarkeit…">
            </div>

            <div class="form-inline mb-50">
                <label class="mr-50 mb-0 small text-muted">Verfügbarkeit:</label>
                <select id="availabilityFilter" class="form-control form-control-sm">
                    <option value="">Alle</option>
                    <option value="Sofort Lieferbar">Sofort Lieferbar</option>
                    <option value="Auf Anfrage">Auf Anfrage</option>
                    <option value="Nicht verfügbar">Nicht verfügbar</option>
                </select>
            </div>
        </div>

        {{-- Tabelle --}}
        <div class="price-grid">
            <table class="table table-sm table-hover" id="distributor_price">
                <thead class="thead-light">
                    <tr>
                        <th style="min-width:220px;">Lieferant</th>

                        {{-- DEFAULT (simple) --}}
                        <th>Art.-Nr.</th>
                        <th>EK-Einzelpreis</th>
                        <th>Verfügbarkeit</th>

                        {{-- ADVANCED (optional) --}}
                        <th class="adv-col">UVP<br><small class="text-muted">Listenpreis</small></th>
                        <th class="adv-col">Rabattgruppe</th>
                        <th class="adv-col">Rabatt €</th>
                        <th class="adv-col">Rabatt %</th>
                        <th class="adv-col">Datum</th>
                        <th class="adv-col">Notiz</th>

                        <th class="text-right">Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Zeilen werden per JS hinzugefügt --}}
                </tbody>
            </table>
        </div>

        <div class="mt-1 d-flex justify-content-between align-items-center flex-wrap">
            <button id="add_price"
                    type="button"
                    class="btn btn-sm btn-outline-success mb-50">
                <i class="feather icon-plus"></i> Zeile hinzufügen
            </button>

            <div class="text-right small text-muted mb-50">
                <span class="mr-1">
                    Summe Lieferanten: <span id="summary-supplier-count">0</span>
                </span>
                <span class="mr-1">
                    Ø Rabatt: <span id="summary-average-discount">0%</span>
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Neuer Lieferant --}}
<div class="modal fade text-left" id="distributors" tabindex="-1" role="dialog" aria-labelledby="distributorsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="distributorsLabel">
                    <i class="feather icon-truck mr-50"></i> Neuer Lieferant
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <form id="distributorForm" class="form-horizontal" novalidate enctype="multipart/form-data" autocomplete="off">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        {{-- Stammdaten --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Unternehmen / Marke</label>
                                <input type="text"
                                       class="form-control"
                                       name="name"
                                       required
                                       autocomplete="organization">
                                <p class="text-danger mb-0" id="name-error"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Adresse</label>
                                <input type="text"
                                       class="form-control"
                                       name="address"
                                       required
                                       autocomplete="street-address">
                                <p class="text-danger mb-0" id="address-error"></p>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group mb-0">
                                <label>Logo <code>.PNG</code></label>
                                <input type="file" class="form-control" name="image" required accept="image/png">
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
                                        {{-- Row 0 --}}
                                        <tr>
                                            <td><input type="text" class="form-control" name="d[0][department]"   placeholder="Abteilung" autocomplete="off"></td>
                                            <td><input type="text" class="form-control" name="d[0][contact_name]" placeholder="Gesprächspartner" autocomplete="name"></td>
                                            <td><input type="text" class="form-control" name="d[0][position]"     placeholder="Position" autocomplete="organization-title"></td>
                                            <td><input type="email" class="form-control" name="d[0][email]"       placeholder="E-Mail" autocomplete="email"></td>
                                            <td><input type="tel"   class="form-control" name="d[0][mobile]"      placeholder="Mobilnummer" inputmode="tel" autocomplete="tel"></td>
                                            <td><input type="tel"   class="form-control" name="d[0][phone]"       placeholder="Festnetznummer" inputmode="tel" autocomplete="tel-national"></td>
                                            <td><input type="tel"   class="form-control" name="d[0][office]"      placeholder="Büro-Telefonnummer" inputmode="tel" autocomplete="tel-extension"></td>
                                            <td class="text-right">
                                                <button type="button" class="btn btn-sm btn-outline-danger distributor_remove" title="Zeile entfernen">
                                                    <i class="feather icon-trash-2"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- Template for JS row add --}}
                            <template id="distributor-dept-row-template">
                                <tr>
                                    <td><input type="text" class="form-control" name="d[__i__][department]"   placeholder="Abteilung" autocomplete="off"></td>
                                    <td><input type="text" class="form-control" name="d[__i__][contact_name]" placeholder="Gesprächspartner" autocomplete="name"></td>
                                    <td><input type="text" class="form-control" name="d[__i__][position]"     placeholder="Position" autocomplete="organization-title"></td>
                                    <td><input type="email" class="form-control" name="d[__i__][email]"       placeholder="E-Mail" autocomplete="email"></td>
                                    <td><input type="tel"   class="form-control" name="d[__i__][mobile]"      placeholder="Mobilnummer" inputmode="tel" autocomplete="tel"></td>
                                    <td><input type="tel"   class="form-control" name="d[__i__][phone]"       placeholder="Festnetznummer" inputmode="tel" autocomplete="tel-national"></td>
                                    <td><input type="tel"   class="form-control" name="d[__i__][office]"      placeholder="Büro-Telefonnummer" inputmode="tel" autocomplete="tel-extension"></td>
                                    <td class="text-right">
                                        <button type="button" class="btn btn-sm btn-outline-danger distributor_remove">
                                            <i class="feather icon-trash-2"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>

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
