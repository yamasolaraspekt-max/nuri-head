 
    <div class="card mb-1 " style=" background: #f1f1f1;   " >
        <div class="card-header  d-flex justify-content-between align-items-center mb-2 mt-2"  style=" background: #f1f1f1;   border-bottom: 2px solid #569ad8;" > 
            <h2 class="content-header-title float-left primary ">PRODUKT & DIENSTLEISTUNG</h2>

            <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1 waves-effect waves-light" id="addRow">
                <i class="feather icon-plus"></i> 
            </button>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
               <table class="table table-bordered table-hover mb-0" id="inquiryProductTable">
                    <thead class="thead-light text-center">
                        <tr>
                            <th>
                                <img src="{{ asset('images/icons/produkt.svg') }}" alt="" style="width: 62px;"><br>
                                Produkt
                            </th>
                            <th>
                                <img src="{{ asset('images/icons/dienstleistung.svg') }}" alt="" style="width: 62px;"><br>
                                Dienstleistung
                            </th>
                            <th>
                                <img src="{{ asset('images/icons/abteilung.svg') }}" alt="" style="width: 62px;"><br>
                                Abteilung
                            </th>
                            <th>
                                <img src="{{ asset('images/icons/mitarbeiter.svg') }}" alt="" style="width: 62px;"><br>
                                Innendienst
                            </th>
                            <th>
                                <img src="{{ asset('images/icons/mitarbeiter.svg') }}" alt="" style="width: 62px;"><br>
                                Außendienst
                            </th>
                            <th>
                                <img src="{{ asset('images/icons/kaufinteresse.svg') }}" alt="" style="width: 56px;"><br>
                                Interesse
                            </th>
                            <th>
                                <img src="{{ asset('images/icons/real.svg') }}" alt="" style="width: 56px;"><br>
                                Realisierungszeit
                            </th>
                            <th>
                                <img src="{{ asset('images/icons/aktion.svg') }}" alt="" style="width: 56px;"><br>
                                Aktion
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- JS will append rows here -->
                    </tbody>
                </table>

            </div>
        </div>
    </div> 
