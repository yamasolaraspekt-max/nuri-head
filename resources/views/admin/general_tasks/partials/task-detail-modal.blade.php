<div class="gt-modal-backdrop" id="gtTaskDetailModal">
    <div class="gt-modal gt-detail-modal">
        <div class="gt-modal-h">
            <div>
                <h3 class="gt-modal-ttl">Aufgabendetails & Schritte</h3>
                <div class="gt-help">Hier siehst du alle Informationen und kannst Schritte erledigen oder wieder öffnen.</div>
            </div>
            <button class="gt-btn-ic" type="button" onclick="gtCloseModal('gtTaskDetailModal')">×</button>
        </div>

        <div class="gt-modal-b">
            <div class="gt-detail-layout">
                <main class="gt-detail-main">
                    <div class="gt-detail-title" id="gtDetailTitle">—</div>
                    <div class="gt-detail-desc" id="gtDetailDescription">—</div>

                    <div class="gt-progress-box" style="margin-top:14px">
                        <div class="gt-progress-head">
                            <span>Fortschritt</span>
                            <span id="gtDetailProgressText">0%</span>
                        </div>
                        <div class="gt-progress-track">
                            <div class="gt-progress-fill" id="gtDetailProgressFill" style="width:0%"></div>
                        </div>
                    </div>

                    <div class="gt-detail-step-list" id="gtDetailSteps"></div>
                </main>

                <aside class="gt-detail-side">
                    <div class="gt-sidebar-card-title">Übersicht</div>
                    <div class="gt-card-detail-strip" style="grid-template-columns:1fr">
                        <div class="gt-card-detail-pill">
                            <div class="gt-card-detail-label">Status</div>
                            <div class="gt-card-detail-value" id="gtDetailStatus">—</div>
                        </div>
                        <div class="gt-card-detail-pill">
                            <div class="gt-card-detail-label">Modus</div>
                            <div class="gt-card-detail-value" id="gtDetailMode">—</div>
                        </div>
                        <div class="gt-card-detail-pill">
                            <div class="gt-card-detail-label">Geplante Zeit</div>
                            <div class="gt-card-detail-value" id="gtDetailPlanned">Optional</div>
                        </div>
                        <div class="gt-card-detail-pill">
                            <div class="gt-card-detail-label">Tatsächliche Zeit</div>
                            <div class="gt-card-detail-value" id="gtDetailActual">Optional</div>
                        </div>
                    </div>

                    <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap">
                        <button type="button" class="gt-btn-soft" id="gtDetailEditBtn">
                            <i data-lucide="pencil" style="width:15px;height:15px"></i>
                            Bearbeiten
                        </button>
                        <button type="button" class="gt-btn-soft" id="gtDetailReportBtn">
                            <i data-lucide="message-square-plus" style="width:15px;height:15px"></i>
                            Bericht
                        </button>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</div>
