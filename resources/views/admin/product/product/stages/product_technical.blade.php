<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="card-title mb-0">Produkttechnische Details</h4>
            <small class="text-muted d-block">
                Strukturierte technische Felder, Hinweise und Besonderheiten zum Produkt.
            </small>
        </div>
        <div class="text-right">
            <span class="badge badge-light">
                <i class="feather icon-info mr-25"></i>
                Einträge: <span id="description-count">0</span>
            </span>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            {{-- LEFT: Eingabe / neue Zeilen --}}
            <div class="col-lg-5 border-right mb-2 mb-lg-0">
                <div class="d-flex align-items-center mb-1">
                    <i class="feather icon-plus-circle mr-50 text-primary"></i>
                    <h6 class="mb-0">Neue technische Details erfassen</h6>
                </div>
                <small class="text-muted d-block mb-2">
                    Erfassen Sie eine oder mehrere Zeilen und speichern Sie diese gesammelt.
                </small>

                <form id="productDescriptionForm">
                    @csrf
                    <input type="hidden" id="product_description_product_id" name="product_id">

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-1" id="description_table">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 24%">Überschrift</th>
                                    <th style="width: 46%">Beschreibung</th>
                                    <th style="width: 20%">Anmerkung</th>
                                    <th style="width: 10%" class="text-center">Aktion</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Zeilen werden per JS (#addRow) hinzugefügt --}}
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <button type="button"
                                class="btn btn-sm btn-outline-primary"
                                id="addRow">
                            <i class="feather icon-plus"></i> Zeile hinzufügen
                        </button>

                        <button type="submit"
                                class="btn btn-sm btn-outline-success">
                            <i class="feather icon-save"></i> Speichern
                        </button>
                    </div>
                </form>
            </div>

            {{-- RIGHT: Übersicht mit Suche / Filter / Sortierung --}}
            <div class="col-lg-7">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="d-flex align-items-center">
                        <i class="feather icon-list mr-50 text-secondary"></i>
                        <h6 class="mb-0">Bereits hinterlegte Details</h6>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="input-group input-group-sm mr-1" style="min-width: 180px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="feather icon-search"></i>
                                </span>
                            </div>
                            <input type="text"
                                   id="descriptionSearch"
                                   class="form-control"
                                   placeholder="Volltextsuche …">
                        </div>

                        <select id="descriptionFieldFilter"
                                class="form-control form-control-sm"
                                style="min-width: 160px;">
                            <option value="">Alle Überschriften</option>
                            {{-- Optionen werden dynamisch aus den vorhandenen Feldern aufgebaut --}}
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-hover table-striped mb-0" id="loadedDescriptions">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Überschrift</th>
                                <th>Beschreibung</th>
                                <th>Anmerkung</th>
                                <th style="width: 90px;" class="text-right">Aktion</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- DataTables füllt hier dynamisch --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
