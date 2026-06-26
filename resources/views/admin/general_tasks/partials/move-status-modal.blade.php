<div class="gt-modal-backdrop" id="gtMoveStatusModal">
    <div class="gt-modal">
        <div class="gt-modal-h">
            <div>
                <h3 class="gt-modal-ttl">Aufgabe verschieben</h3>
                <div class="gt-help">Bericht ist optional. Du kannst beim Verschieben direkt Schritte als erledigt markieren.</div>
            </div>
            <button class="gt-btn-ic" type="button" onclick="gtCancelMoveStatus()">×</button>
        </div>

        <div class="gt-modal-b">
            <input type="hidden" id="gtMoveTaskId">
            <input type="hidden" id="gtMoveTargetStatus">

            <div class="gt-detail-box">
                <strong id="gtMoveTaskTitle">—</strong>
                <div class="gt-person-meta" id="gtMoveStatusLabel">—</div>
            </div>

            <div class="gt-form-group">
                <label class="gt-label">Optionaler Bericht / Notiz</label>
                <textarea class="gt-textarea" id="gtMoveReportText" placeholder="Optional: Was wurde gemacht oder warum wird die Aufgabe verschoben?"></textarea>
            </div>

            <div class="gt-form-group">
                <label class="gt-label">Schritte beim Verschieben aktualisieren</label>
                <div class="gt-help">Markiere hier die Schritte, die durch diesen Statuswechsel erledigt wurden.</div>
                <div class="gt-move-step-list" id="gtMoveStepList"></div>
            </div>
        </div>

        <div class="gt-modal-f">
            <button type="button" class="gt-btn-soft" onclick="gtCancelMoveStatus()">Abbrechen</button>
            <button type="button" class="gt-btn" id="gtConfirmMoveStatusBtn">Verschieben</button>
        </div>
    </div>
</div>
