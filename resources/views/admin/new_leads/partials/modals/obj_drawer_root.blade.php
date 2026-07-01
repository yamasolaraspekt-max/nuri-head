    <div id="objDrawerRoot">
        <div id="objDrawerBackdrop"></div>

        <aside id="objDrawerPanel">
            <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                <div>
                    <h5 class="mb-0 font-weight-bold">Produkte zu Objekten zuordnen</h5>
                    <small id="drawerCustomerLine" class="text-muted"></small>
                </div>

                <div class="d-flex gap-2">
                    <button id="btnDrawerOpenCreate" class="btn btn-dark btn-sm mr-2">
                        <i class="feather icon-plus"></i> Neues Objekt
                    </button>
                    <button id="btnDrawerClose" class="btn btn-light btn-sm">
                        Schließen
                    </button>
                </div>
            </div>

            <div id="drawerCreatePanel" class="border-bottom bg-light p-3 drawer-hidden">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row mb-2">
                            <div class="col-6">
                                <label class="small text-muted">Objektname</label>
                                <input id="co_object_name" class="form-control form-control-sm"
                                    placeholder="z.B. EFH Musterstraße" />
                            </div>
                            <div class="col-6">
                                <label class="small text-muted">Anfrage Datum</label>
                                <input id="co_request_date" type="date" class="form-control form-control-sm" />
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <label class="small text-muted">Adresse (Google Maps)</label>
                            <input id="co_address_search" class="form-control form-control-sm"
                                placeholder="Adresse suchen…" />
                            <input id="co_full_address" type="hidden" />
                            <input id="co_street" type="hidden" />
                            <input id="co_postcode" type="hidden" />
                            <input id="co_city" type="hidden" />
                            <input id="co_lat" type="hidden" />
                            <input id="co_lon" type="hidden" />
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <label class="small text-muted">Ziel</label>
                                <input id="co_objective" class="form-control form-control-sm" placeholder="z.B. Angebot" />
                            </div>
                            <div class="col-6">
                                <label class="small text-muted">Notiz</label>
                                <input id="co_note" class="form-control form-control-sm" placeholder="Optional" />
                            </div>
                        </div>
                        <div class="d-flex align-items-center mt-3">
                            <button id="btnCreateObjectSave" class="btn btn-success btn-sm mr-2">Speichern</button>
                            <button id="btnCreateObjectCancel" class="btn btn-secondary btn-sm">Abbrechen</button>
                            <span id="createObjectMsg" class="ml-2 small text-muted"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="co_map" style="width: 100%; height: 260px; border-radius: 8px; border: 1px solid #ccc;">
                        </div>
                        <div class="mt-1 small text-muted">Marker verschieben oder in Karte klicken.</div>
                    </div>
                </div>
            </div>

            <div class="flex-grow-1 p-3" style="overflow-y: auto;">
                <div id="drawerLoading" class="text-center text-muted mt-5">
                    <div class="spinner-border text-primary mb-2" role="status"></div>
                    <div>Lade Daten...</div>
                </div>

                <div id="drawerObjectsGrid" class="drawer-hidden">
                    <div class="row" id="drawerObjectsCols"></div>
                </div>
            </div>
        </aside>
    </div>
