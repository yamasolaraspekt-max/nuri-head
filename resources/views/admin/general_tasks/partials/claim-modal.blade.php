<div class="gt-modal-backdrop" id="gtClaimModal">
    <div class="gt-modal">
        <div class="gt-modal-h">
            <h3 class="gt-modal-ttl">Aufgabe übernehmen</h3>
            <button class="gt-btn-ic" type="button" onclick="gtCloseModal('gtClaimModal')">×</button>
        </div>

        <form id="gtClaimForm">
            @csrf
            <input type="hidden" id="gtClaimTaskId">
            <input type="hidden" id="gtClaimStatus" value="in_progress">

            <div class="gt-modal-b">
                <div class="gt-form-grid">
                    <div class="gt-form-group">
                        <label class="gt-label">Ich habe heute Zeit</label>
                        <input class="gt-input" type="number" step="0.25" min="0" id="gtClaimHours" placeholder="z.B. 3">
                    </div>

                    <div class="gt-form-group">
                        <label class="gt-label">Ich erledige es bis</label>
                        <input class="gt-input" type="datetime-local" id="gtClaimDueAt">
                    </div>

                    <div class="gt-form-group" style="grid-column:1/-1">
                        <label class="gt-label">Grund / Notiz *</label>
                        <textarea class="gt-textarea" id="gtClaimNote" required placeholder="Warum übernimmst du diese Aufgabe oder was ist wichtig?"></textarea>
                    </div>
                </div>
            </div>

            <div class="gt-modal-f">
                <button type="button" class="gt-btn-soft" onclick="gtCloseModal('gtClaimModal')">Abbrechen</button>
                <button type="submit" class="gt-btn">Übernehmen</button>
            </div>
        </form>
    </div>
</div>
