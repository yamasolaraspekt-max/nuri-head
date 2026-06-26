<div class="absence-modal-overlay" id="absenceRequestModalOverlay">
    <div class="absence-modal">
        <div class="absence-modal-header">
            <div>
                <h3>Abwesenheit beantragen</h3>
                <p>Urlaub beantragen oder Krankmeldung hochladen.</p>
            </div>

            <button type="button" class="close-btn" id="closeAbsenceRequestModalBtn">
                <i data-lucide="x"></i>
            </button>
        </div>

        <form id="absenceRequestForm" enctype="multipart/form-data">
            @csrf

            <div class="absence-type-switch">
                <label class="absence-type-option active" data-absence-type-option="leave">
                    <input type="radio" name="request_type" value="leave" checked>
                    <span>
                        <i data-lucide="plane"></i>
                        Urlaub
                    </span>
                </label>

                <label class="absence-type-option" data-absence-type-option="sick">
                    <input type="radio" name="request_type" value="sick">
                    <span>
                        <i data-lucide="heart-pulse"></i>
                        Krankheit
                    </span>
                </label>
            </div>

            <div class="absence-form-row">
                <div class="absence-form-group">
                    <label>Von</label>
                    <input type="date" name="start_date" id="absenceStartDate" required>
                </div>

                <div class="absence-form-group">
                    <label>Bis</label>
                    <input type="date" name="end_date" id="absenceEndDate" required>
                </div>
            </div>

            <div class="absence-form-group" id="absenceLeaveTypeGroup">
                <label>Urlaubsart</label>
                <select name="leave_type" id="absenceLeaveType">
                    <option value="Urlaub">Urlaub</option>
                    <option value="Sonderurlaub">Sonderurlaub</option>
                    <option value="Unbezahlter Urlaub">Unbezahlter Urlaub</option>
                    <option value="Berufsschule">Berufsschule</option>
                    <option value="Sonstiges">Sonstiges</option>
                </select>
            </div>

            <div class="absence-form-group">
                <label>Anfrage an</label>
                <select name="request_to" id="absenceRequestTo" required>
                    <option value="">Bitte auswählen</option>
                </select>
            </div>

            <div class="absence-form-group">
                <label>Grund / Kurzinfo</label>
                <input type="text" name="reason" id="absenceReason"
                    placeholder="z.B. Jahresurlaub, Arzttermin, Krankmeldung">
            </div>

            <div class="absence-form-group" id="absenceDocumentGroup" style="display:none;">
                <label>Krankmeldung / Dokument</label>
                <input type="file" name="document" id="absenceDocument" accept=".pdf,.jpg,.jpeg,.png,.webp">
                <small>PDF, JPG, PNG oder WEBP bis 10 MB.</small>
            </div>

            <div class="absence-form-group">
                <label>Beschreibung</label>
                <textarea name="description" id="absenceDescription" placeholder="Weitere Informationen..."></textarea>
            </div>

            <div class="absence-modal-actions">
                <button type="button" class="btn" id="cancelAbsenceRequestBtn">
                    Abbrechen
                </button>

                <button type="submit" class="btn btn-primary" id="submitAbsenceRequestBtn">
                    <i data-lucide="send"></i>
                    Antrag senden
                </button>
            </div>
        </form>
    </div>
</div>