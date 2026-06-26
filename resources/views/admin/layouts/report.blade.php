
<div id="saReportModalBackdrop" class="sa-report-modal-backdrop" style="display:none;">
    <div class="sa-report-modal">
        <div class="sa-report-modal-topline"></div>

        <div class="sa-report-modal-head">
            <div class="sa-report-modal-titlebox">
                <span class="sa-report-modal-icon">
                    <i data-lucide="clipboard-check"></i>
                </span>

                <div>
                    <h3 id="saReportModalTitle">Bericht</h3>
                    <p id="saReportModalSub">—</p>
                </div>
            </div>

            <button type="button" onclick="closeSaReportModal()" class="sa-report-modal-close">
                <i data-lucide="x"></i>
            </button>
        </div>

        <div class="sa-report-modal-body">
            <div class="sa-report-info-grid">
                <div class="sa-report-info-box">
                    <span>Mitarbeiter</span>
                    <strong id="saReportModalEmployee">—</strong>
                </div>

                <div class="sa-report-info-box">
                    <span>Typ</span>
                    <strong id="saReportModalType">—</strong>
                </div>

                <div class="sa-report-info-box">
                    <span>Ziel</span>
                    <strong id="saReportModalTarget">—</strong>
                </div>

                <div class="sa-report-info-box">
                    <span>Zeitpunkt</span>
                    <strong id="saReportModalTime">—</strong>
                </div>
            </div>

            <div class="sa-report-full-text">
                <div class="sa-report-full-head">
                    <span>
                        <i data-lucide="message-square-text"></i>
                        Berichtstext
                    </span>
                </div>

                <p id="saReportModalReport">—</p>
            </div>
        </div>

        <div class="sa-report-modal-foot">
            <button type="button" onclick="closeSaReportModal()" class="sa-report-modal-secondary">
                Schließen
            </button>

            <a href="{{ route('admin.report.index') }}" class="sa-report-modal-primary">
                Reports öffnen
                <i data-lucide="arrow-right"></i>
            </a>
        </div>
    </div>
</div>