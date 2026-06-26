<style>
    /* =========================================================
   DASHBOARD CALENDAR APPOINTMENT MAP
========================================================= */

.dash-cal-modal {
    width: min(1120px, 100%);
}

.dash-cal-modal-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--color-border);
}

.dash-cal-modal-header h3 {
    margin: 0;
    font-size: 1.18rem;
    font-weight: 900;
    letter-spacing: -.035em;
}

.dash-cal-modal-header p {
    margin: .2rem 0 0;
    color: var(--color-text-muted);
    font-size: .82rem;
    font-weight: 800;
}

.dash-cal-modal-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(340px, .95fr);
    gap: .9rem;
    align-items: start;
}

.dash-cal-detail-card,
.dash-cal-map {
    min-width: 0;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    background: var(--color-surface-2);
    overflow: hidden;
}

.dash-cal-detail-card {
    padding: 1rem;
}

.dash-cal-detail-card h4 {
    margin: 0 0 .8rem;
    font-size: .88rem;
    font-weight: 950;
    color: var(--color-text);
}

.dash-cal-detail-row {
    display: grid;
    grid-template-columns: 120px minmax(0, 1fr);
    gap: .8rem;
    padding: .55rem 0;
    border-top: 1px solid var(--color-border);
}

.dash-cal-detail-row:first-of-type {
    border-top: 0;
}

.dash-cal-detail-row span {
    color: var(--color-text-muted);
    font-size: .74rem;
    font-weight: 900;
}

.dash-cal-detail-row strong {
    min-width: 0;
    color: var(--color-text);
    font-size: .8rem;
    font-weight: 900;
    word-break: break-word;
}

.dash-cal-map {
    grid-row: span 2;
    background: #f8fafc;
}

.dash-cal-map-head {
    padding: .9rem;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: .85rem;
    border-bottom: 1px solid var(--color-border);
    background: var(--color-surface);
}

.dash-cal-map-title {
    min-width: 0;
    display: flex;
    align-items: flex-start;
    gap: .65rem;
}

.dash-cal-map-title i,
.dash-cal-map-title svg {
    width: 19px;
    height: 19px;
    margin-top: .1rem;
    color: var(--color-blue-dark);
    flex-shrink: 0;
}

.dash-cal-map-title strong {
    display: block;
    font-size: .86rem;
    font-weight: 950;
}

.dash-cal-map-title span {
    display: block;
    margin-top: .15rem;
    color: var(--color-text-muted);
    font-size: .75rem;
    line-height: 1.4;
    font-weight: 800;
}

.dash-cal-map-link {
    min-height: 34px;
    padding: .45rem .65rem;
    border-radius: .75rem;
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    background: var(--color-blue-soft);
    color: var(--color-blue-dark);
    border: 1px solid rgba(116, 178, 212, .35);
    font-size: .72rem;
    font-weight: 950;
    white-space: nowrap;
}

.dash-cal-map-link:hover {
    color: var(--color-blue-dark);
    text-decoration: none;
}

.dash-cal-map-frame {
    width: 100%;
    height: 360px;
    display: block;
    border: 0;
    background: var(--color-surface-2);
}

.dash-cal-map-empty {
    min-height: 360px;
    display: grid;
    place-items: center;
    padding: 1rem;
    text-align: center;
    color: var(--color-text-muted);
    font-size: .82rem;
    font-weight: 850;
}

@media (max-width: 900px) {
    .dash-cal-modal-grid {
        grid-template-columns: 1fr;
    }

    .dash-cal-map {
        grid-row: auto;
    }

    .dash-cal-map-frame,
    .dash-cal-map-empty {
        height: 300px;
        min-height: 300px;
    }
}
</style>
<div class="dash-cal-modal-overlay" id="dashCalAppointmentModal">
    <div class="dash-cal-modal">
        <div class="dash-cal-modal-header">
            <div>
                <h3 id="dashCalModalTitle">Termin</h3>
                <p id="dashCalModalSubtitle">Details werden geladen...</p>
            </div>

            <button type="button" class="close-btn" id="dashCalModalCloseBtn">
                <i data-lucide="x"></i>
            </button>
        </div>

        <div class="dash-cal-modal-body" id="dashCalModalBody">
            <div class="empty-state">Termin wird geladen...</div>
        </div>
    </div>
</div>