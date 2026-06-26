<div class="gt-modal-backdrop" id="gtReportModal">
    <div class="gt-modal lg">
        <div class="gt-modal-h">
            <h3 class="gt-modal-ttl">Kommentar / Bericht</h3>
            <button class="gt-btn-ic" type="button" onclick="gtCloseModal('gtReportModal')">×</button>
        </div>

        <form id="gtReportForm">
            @csrf
            <input type="hidden" id="gtReportTaskId">

            <div class="gt-modal-b">
                <div id="gtReportHistory" class="gt-detail-box">Berichte werden geladen...</div>

                <div class="gt-form-grid">
                    <div class="gt-form-group">
                        <label class="gt-label">Typ</label>
                        <select class="gt-select" id="gtReportType">
                            <option value="comment">Kommentar</option>
                            <option value="report">Arbeitsbericht</option>
                        </select>
                    </div>

                    <div class="gt-form-group">
                        <label class="gt-label">Arbeitszeit</label>
                        <input class="gt-input" type="number" min="0" step="0.25" id="gtReportHours" placeholder="z.B. 1.5">
                    </div>

                    <div class="gt-form-group" style="grid-column:1/-1">
                        <label class="gt-label">Text *</label>
                        <textarea class="gt-textarea" id="gtReportText" required placeholder="Was wurde gemacht oder was ist wichtig?"></textarea>
                    </div>
                </div>
            </div>

            <div class="gt-modal-f">
                <button type="button" class="gt-btn-soft" onclick="gtCloseModal('gtReportModal')">Schließen</button>
                <button type="submit" class="gt-btn">Speichern</button>
            </div>
        </form>
    </div>
</div>
