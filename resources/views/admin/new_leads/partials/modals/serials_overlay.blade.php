    <div id="serialsOverlay" class="cp-overlay" aria-hidden="true">
        <div class="cp-dialog" role="dialog" aria-modal="true" aria-labelledby="snTitle">
            <div class="cp-header">
                <h5 id="snTitle" class="cp-title">Seriennummern verwalten</h5>
                <button type="button" class="cp-close" id="snCloseBtn" aria-label="Close">&times;</button>
            </div>

            <div class="cp-body">
                <div class="sn-info">
                    <strong id="serialsModalProductName">Produkt</strong>
                    <span class="sn-sep">–</span>
                    <span>Anzahl: <strong id="serialsModalCount">1</strong></span>
                </div>

                <div class="sn-table-wrap">
                    <table class="sn-table">
                        <thead>
                            <tr>
                                <th style="width:90px;">Pos.</th>
                                <th>Seriennummer</th>
                            </tr>
                        </thead>
                        <tbody id="serialsModalBody"></tbody>
                    </table>
                </div>
            </div>

            <div class="cp-footer">
                <button type="button" class="btn btn-secondary" id="snCancelBtn">Schließen</button>
                <button type="button" class="btn btn-primary" id="btnSerialsModalSave">Übernehmen</button>
            </div>
        </div>
    </div>
