<div class="modal fade" id="tmDayModal" tabindex="-1" role="dialog"
     aria-labelledby="tmDayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Arbeitszeit bearbeiten – <span id="tmModalDayLabel"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="tmModalDate">

                <div class="form-row">
                    <div class="form-group col-6">
                        <label for="tmModalStart">Startzeit</label>
                        <input type="time" id="tmModalStart" class="form-control">
                    </div>
                    <div class="form-group col-6">
                        <label for="tmModalEnd">Endzeit</label>
                        <input type="time" id="tmModalEnd" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="tmModalBreak">Pause (Minuten)</label>
                    <input type="number" min="0" max="600" id="tmModalBreak"
                           class="form-control" value="0">
                    <small class="text-muted">z. B. 30 Minuten Mittagspause.</small>
                </div>

                <div class="alert alert-secondary py-50 px-75 mb-0" id="tmModalHoursPreview">
                    Geplante Stunden:
                    <strong><span id="tmModalHoursValue">0.00</span> h</strong>
                </div>
            </div>

            <div class="modal-footer">
                <button id="tmModalDeleteDay" class="btn btn-outline-danger mr-auto">
                    Tag löschen
                </button>
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    Abbrechen
                </button>
                <button id="tmModalSaveDay" type="button" class="btn btn-primary">
                    Speichern
                </button>
            </div>
        </div>
    </div>
</div>
