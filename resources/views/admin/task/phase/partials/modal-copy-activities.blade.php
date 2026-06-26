{{-- resources/views/admin/task/phase/partials/modal-copy-activities.blade.php --}}

<div id="copyDrawer" class="phase-copy-drawer">
    <div class="phase-copy-overlay"></div>

    <div id="copyDrawerPanel" class="phase-copy-panel">
        <form id="copyForm" class="h-100 d-flex flex-column">
            <div class="phase-copy-header">
                <div>
                    <div class="phase-copy-title">Aktivitäten kopieren</div>
                    <div class="phase-copy-subtitle">
                        Quelle: <span id="sourcePhaseDetails" class="font-weight-bold"></span>
                    </div>
                </div>

                <button type="button" id="copyDrawerClose" class="phase-modal-close">
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <div class="phase-copy-body">
                <div class="phase-copy-summary" id="targetSummary">
                    <strong>Ziel:</strong> Bitte Produkt, Bereich, Version, Stage &amp; Phase wählen.
                </div>

                <div class="phase-section-kicker">Ziel definieren</div>

                <div class="phase-form-grid">
                    <div class="phase-form-group full">
                        <label class="phase-label" for="targetProduct">Produkt</label>
                        <select id="targetProduct" class="phase-select"></select>
                    </div>

                    <div class="phase-form-group full">
                        <label class="phase-label" for="targetSection">Bereich</label>
                        <select id="targetSection" class="phase-select"></select>
                    </div>

                    <div class="phase-form-group">
                        <label class="phase-label" for="targetVersion">Version</label>
                        <select id="targetVersion" class="phase-select"></select>
                    </div>

                    <div class="phase-form-group">
                        <label class="phase-label" for="targetStage">Stage</label>
                        <select id="targetStage" class="phase-select"></select>
                    </div>

                    <div class="phase-form-group full">
                        <label class="phase-label" for="targetPhase">
                            Ziel-Phase
                            <span style="font-weight:700;text-transform:none;color:#9ca3af;">
                                wählen oder neu eingeben
                            </span>
                        </label>
                        <select id="targetPhase" class="phase-select"></select>
                        <small class="form-text text-muted">
                            Neue Phase kann direkt hier als Text eingegeben werden.
                        </small>
                    </div>
                </div>

                <hr class="my-2">

                <div class="phase-copy-select-all">
                    <div class="phase-section-kicker mb-0">Aktivitäten</div>

                    <label class="phase-check-card mb-0" style="min-height:36px;padding:7px 10px;">
                        <input type="checkbox" id="selectAllActivities">
                        Alle auswählen
                    </label>
                </div>

                <div id="activitiesList" class="phase-copy-activities"></div>
            </div>

            <div class="phase-copy-footer">
                <button type="button" class="phase-btn-soft" id="copyDrawerCancel">
                    Abbrechen
                </button>

                <button type="submit" class="phase-btn">
                    <i class="feather icon-copy"></i>
                    Aktivitäten kopieren
                </button>
            </div>
        </form>
    </div>
</div>