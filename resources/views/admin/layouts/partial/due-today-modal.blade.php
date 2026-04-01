<div id="dueTodayModal" class="due-modal" aria-hidden="true" role="dialog" aria-labelledby="dueTodayLabel">
    <div class="due-modal-backdrop" data-due-close></div>

    <div class="due-modal-panel" role="document">
        <header class="due-modal-header">
            <div class="due-modal-title-wrap">
                <h5 id="dueTodayLabel" class="due-modal-title">Deine heutigen Aufgaben</h5>
                <p class="due-modal-subtitle">
                    Behalte deine Fälligkeiten im Blick – inklusive überfälliger Tasks.
                </p>
            </div>
            <button type="button" class="due-close-btn desktop-only" data-due-close aria-label="Schließen">
                <span>&times;</span>
            </button>
        </header>

        <section class="due-modal-body">
            <div id="dueProgressBar" class="due-progress-wrapper"></div>
            <div id="dueTodayList" class="due-list"></div>
        </section>

        <footer class="due-modal-footer mobile-only">
            <button type="button" class="due-btn-full" data-due-close>
                Schließen
            </button>
        </footer>
    </div>
</div>

<style>
/* === Base === */
.due-modal {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.25s ease;
}

.due-modal.is-open {
    pointer-events: auto;
    opacity: 1;
}

.due-modal-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(6px);
}

/* Panel */
.due-modal-panel {
    position: relative;
    z-index: 1;
    width: min(720px, 95vw);
    max-height: min(90vh, 720px);
    background: #ffffff;
    border-radius: 1rem;
    box-shadow: 0 24px 60px rgba(15, 23, 42, 0.35);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Header */
.due-modal-header {
    flex-shrink: 0;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 1.25rem 1.5rem;
    background: radial-gradient(circle at top left, #79b3d4, #689cb9);
    color: #ffffff;
}

.due-modal-title-wrap {
    max-width: calc(100% - 40px);
}

.due-modal-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    letter-spacing: 0.01em;
}

.due-modal-subtitle {
    margin: 0.35rem 0 0;
    font-size: 0.82rem;
    opacity: 0.9;
    line-height: 1.4;
}

.due-close-btn {
    border: none;
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
    border-radius: 999px;
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.18s ease;
}

.due-close-btn span {
    font-size: 1.4rem;
    line-height: 1;
    margin-top: -2px; 
}

/* Body */
.due-modal-body {
    flex: 1; /* Takes available space */
    padding: 1rem 1.5rem 1.25rem;
    overflow-y: auto; /* Scrollable content */
    -webkit-overflow-scrolling: touch;
}

/* Footer (Mobile Only) */
.due-modal-footer {
    padding: 1rem;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    display: none; /* Hidden by default on desktop */
}

.due-btn-full {
    width: 100%;
    padding: 0.8rem;
    border-radius: 0.75rem;
    border: none;
    background: #f1f5f9;
    color: #334155;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
}

/* Progress */
.due-progress-wrapper {
    margin-bottom: 1rem;
}

.due-progress {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.due-progress-track {
    width: 100%;
    height: 8px;
    border-radius: 999px;
    background: #e5e7eb;
    overflow: hidden;
}

.due-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #22c55e, #4ade80);
    width: 0%; 
    transition: width 0.5s ease;
}

.due-progress-label {
    font-size: 0.78rem;
    color: #6b7280;
}

/* List + Cards */
.due-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.due-card {
    display: flex;
    padding: 0.85rem;
    border-radius: 0.75rem;
    background: #f9fafb;
    border: 1px solid rgba(148, 163, 184, 0.35);
    box-shadow: 0 2px 5px rgba(0,0,0,0.02);
}

.due-card-border {
    width: 4px;
    border-radius: 999px;
    margin-right: 0.85rem;
    flex-shrink: 0;
}

.due-card-content {
    flex: 1;
    min-width: 0;
}

.due-card-title {
    font-size: 0.95rem;
    font-weight: 600;
    margin: 0 0 0.35rem;
    color: #111827;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.due-card-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    margin-bottom: 0.4rem;
}

.due-pill {
    display: inline-flex;
    align-items: center;
    padding: 0.15rem 0.6rem;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.02em;
}

/* Colors */
.due-pill-info      { background: #e0f2fe; color: #0284c7; }
.due-pill-warning   { background: #ffedd5; color: #c2410c; }
.due-pill-danger    { background: #fee2e2; color: #b91c1c; }
.due-pill-secondary { background: #f1f5f9; color: #475569; }
.due-pill-primary   { background: #ecfccb; color: #4d7c0f; }

.due-pill-overdue {
    background: #fee2e2;
    color: #b91c1c;
    border: 1px solid #fecaca;
}

.due-card-text {
    font-size: 0.82rem;
    color: #4b5563;
    margin: 0 0 0.3rem;
    line-height: 1.4;
}

.due-card-date {
    font-size: 0.75rem;
    color: #64748b;
    margin-top: 0.2rem;
    font-weight: 500;
}

/* === MOBILE OPTIMIZATION === */
@media (max-width: 640px) {
    .due-modal {
        align-items: flex-end; /* Align to bottom */
    }

    .due-modal-panel {
        width: 100vw;
        max-height: 85vh; /* Take up 85% of screen height */
        border-radius: 1.25rem 1.25rem 0 0; /* Rounded top corners only */
        margin: 0;
        animation: slideUp 0.3s ease-out;
    }

    .due-modal-header {
        padding: 1rem 1.25rem;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .desktop-only {
        display: none; /* Hide top close button on mobile to save space */
    }

    .mobile-only {
        display: block; /* Show bottom footer */
    }

    .due-modal-body {
        padding: 1rem;
        padding-bottom: 2rem; /* Extra space for scrolling */
    }

    .due-modal-footer {
        padding-bottom: calc(1rem + env(safe-area-inset-bottom));
    }
}

@keyframes slideUp {
    from { transform: translateY(100%); }
    to { transform: translateY(0); }
}
</style>