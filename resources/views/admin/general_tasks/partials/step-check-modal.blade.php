<div class="gt-modal-backdrop" id="gtStepCheckModal">
    <div class="gt-modal">
        <div class="gt-modal-h">
            <div>
                <h3 class="gt-modal-ttl" id="gtStepCheckTitle">Schritt abschließen</h3>
                <div class="gt-help" id="gtStepCheckSubtitle">Der Abschluss wird mit Mitarbeiter und Zeit gespeichert.</div>
            </div>
            <button class="gt-btn-ic" type="button" data-step-check-cancel>×</button>
        </div>

        <div class="gt-modal-b">
            <input type="hidden" id="gtStepCheckTaskId">
            <input type="hidden" id="gtStepCheckStepId">
            <input type="hidden" id="gtStepCheckDone">

            <div class="gt-detail-box">
                <strong>Nach dem Speichern wird automatisch gespeichert:</strong>
                <div class="gt-help">Erledigt von: aktueller Mitarbeiter · Zeitpunkt: aktuelle Zeit · Fortschritt wird neu berechnet.</div>
            </div>

            <div class="gt-form-group">
                <label class="gt-label">Notiz / Grund *</label>
                <textarea class="gt-textarea" id="gtStepCheckReason" placeholder="z.B. Schritt erledigt, geprüft, Unterlagen vollständig..." required></textarea>
            </div>
        </div>

        <div class="gt-modal-f">
            <button type="button" class="gt-btn-soft" data-step-check-cancel>Abbrechen</button>
            <button type="button" class="gt-btn" id="gtStepCheckSaveBtn">Speichern</button>
        </div>
    </div>
</div>
