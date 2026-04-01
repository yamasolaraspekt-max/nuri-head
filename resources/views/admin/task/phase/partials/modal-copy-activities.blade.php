{{-- Drawer: Copy Activities --}}
<div id="copyDrawer" class="phase-copy-drawer">
    <div class="phase-copy-overlay"></div>

    <div id="copyDrawerPanel" class="phase-copy-panel">
        <form id="copyForm" class="h-100 d-flex flex-column">
            {{-- HEADER --}}
            <div class="phase-copy-header">
                <div>
                    <div class="phase-copy-title">Aktivitäten kopieren</div>
                    <div class="phase-copy-subtitle">
                        Quelle: <span id="sourcePhaseDetails" class="font-weight-bold"></span>
                    </div>
                </div>

                <button type="button" id="copyDrawerClose" class="btn btn-icon btn-outline-secondary btn-sm">
                    <i class="feather icon-x"></i>
                </button>
            </div>

            {{-- BODY --}}
            <div class="phase-copy-body">

                <div class="alert alert-secondary py-50 px-75 mb-75 small" id="targetSummary">
                    <strong>Ziel:</strong> Bitte Produkt, Bereich, Version, Stage &amp; Phase wählen.
                </div>

                {{-- STEP 1: Ziel definieren --}}
                <div class="mb-1">
                    <div class="small text-uppercase text-muted mb-50">Ziel definieren</div>

                    <div class="form-group mb-50">
                        <label class="small font-weight-600" for="targetProduct">Produkt</label>
                        <select id="targetProduct" class="form-control form-control-sm"></select>
                    </div>

                    <div class="form-group mb-50">
                        <label class="small font-weight-600" for="targetSection">Bereich</label>
                        <select id="targetSection" class="form-control form-control-sm"></select>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6 mb-50">
                            <label class="small font-weight-600" for="targetVersion">Version</label>
                            <select id="targetVersion" class="form-control form-control-sm"></select>
                        </div>
                        <div class="form-group col-6 mb-50">
                            <label class="small font-weight-600" for="targetStage">Stage</label>
                            <select id="targetStage" class="form-control form-control-sm"></select>
                        </div>
                    </div>

                    <div class="form-group mb-1">
                        <label class="small font-weight-600" for="targetPhase">
                            Ziel-Phase
                            <span class="text-muted">(wählen oder neu eingeben)</span>
                        </label>
                        <select id="targetPhase" class="form-control form-control-sm"></select>
                        <small class="form-text text-muted">
                            Neue Phase kann direkt hier als Text eingegeben werden.
                        </small>
                    </div>
                </div>

                <hr class="my-1">

                {{-- STEP 2: Aktivitäten auswählen --}}
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-50">
                        <div class="small text-uppercase text-muted">Aktivitäten</div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="selectAllActivities">
                            <label class="custom-control-label small" for="selectAllActivities">
                                Alle auswählen
                            </label>
                        </div>
                    </div>

                    <div id="activitiesList" class="phase-copy-activities">
                        {{-- Aktivitäten werden dynamisch gefüllt --}}
                    </div>
                </div>

      
            </div>

            {{-- FOOTER --}}
            <div class="phase-copy-footer">
                <button type="button"
                        class="btn btn-outline-secondary btn-sm mr-50"
                        id="copyDrawerCancel">
                    Abbrechen
                </button>
                <button type="submit"
                        class="btn btn-primary btn-sm">
                    Aktivitäten kopieren
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Drawer styles --}}
<style>
    .phase-copy-drawer {
        position: fixed;
        inset: 0;
        z-index: 1060;
        pointer-events: none;
    }

    .phase-copy-drawer.open {
        pointer-events: auto;
    }

    .phase-copy-overlay {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .phase-copy-drawer.open .phase-copy-overlay {
        opacity: 1;
    }

    .phase-copy-panel {
        position: absolute;
        top: 0;
        right: 0;
        height: 100%;
        width: 889px;
        max-width: 100%;
        background: #ffffff;
        box-shadow: -12px 0 35px rgba(15, 23, 42, 0.18);
        transform: translateX(100%);
        transition: transform 0.25s ease;
        display: flex;
        flex-direction: column;
    }

    .phase-copy-drawer.open .phase-copy-panel {
        transform: translateX(0);
    }

    .phase-copy-header {
        padding: 12px 16px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .phase-copy-title {
        font-size: 14px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .phase-copy-subtitle {
        font-size: 11px;
        color: #6b7280;
    }

    .phase-copy-body {
        padding: 10px 16px 12px;
        overflow-y: auto;
        flex: 1 1 auto;
    }

    .phase-copy-footer {
        padding: 8px 16px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    .phase-copy-activities {
        max-height: 280px;
        overflow-y: auto;
        padding-right: 4px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        padding:20px;
    }

    .phase-copy-activities > .custom-control {
        padding: 6px 10px;
        border-bottom: 1px solid #e5e7eb;
    }

    .phase-copy-activities > .custom-control:last-child {
        border-bottom: none;
    }

    @media (max-width: 576px) {
        .phase-copy-panel {
            width: 100%;
        }
    }
</style>
