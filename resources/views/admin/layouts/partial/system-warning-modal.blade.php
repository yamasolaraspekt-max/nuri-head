<div
    id="globalSystemWarning"
    class="gsw-root gsw-hidden"
    aria-live="polite"
    aria-modal="true"
    role="dialog"
>
    <div id="globalSystemWarningBackdrop" class="gsw-backdrop"></div>

    <div class="gsw-shell">
        <div class="gsw-card">
            <button id="globalSystemWarningClose" type="button" class="gsw-close" aria-label="Schließen">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                    <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
                </svg>
            </button>

            <div class="gsw-visual">
                <div class="gsw-pulse"></div>

                <svg id="globalSystemWarningSvg" class="gsw-svg" viewBox="0 0 260 210" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="35" y="45" width="190" height="120" rx="24" class="gsw-svg-window"/>
                    <rect x="58" y="70" width="105" height="13" rx="6.5" class="gsw-svg-line-strong"/>
                    <rect x="58" y="96" width="145" height="10" rx="5" class="gsw-svg-line"/>
                    <rect x="58" y="118" width="125" height="10" rx="5" class="gsw-svg-line"/>
                    <circle cx="190" cy="145" r="38" class="gsw-svg-circle"/>
                    <path d="M190 124v28" class="gsw-svg-alert"/>
                    <circle cx="190" cy="164" r="4.5" class="gsw-svg-dot"/>
                    <path d="M80 158c10-18 28-28 50-26 19 1 31-8 39-21" class="gsw-svg-wave"/>
                </svg>
            </div>

            <div class="gsw-content">
                <div id="globalSystemWarningBadge" class="gsw-badge">
                    Systemhinweis
                </div>

                <h2 id="globalSystemWarningTitle" class="gsw-title">
                    Kurze Wartung
                </h2>

                <p id="globalSystemWarningMessage" class="gsw-message">
                    Wir führen gerade eine technische Änderung durch. Bitte speichern Sie Ihre Arbeit.
                </p>

                <div class="gsw-actions">
                    <button id="globalSystemWarningButton" type="button" class="gsw-button">
                        Verstanden
                    </button>
                </div>

                <p class="gsw-footer">
                    Diese Meldung wurde live vom System gesendet.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
.gsw-hidden {
    display: none !important;
}

.gsw-root {
    position: fixed;
    inset: 0;
    z-index: 999999;
    font-family: inherit;
}

.gsw-backdrop {
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at top left, rgba(245, 158, 11, .22), transparent 34%),
        radial-gradient(circle at bottom right, rgba(15, 23, 42, .35), transparent 34%),
        rgba(15, 23, 42, .58);
    backdrop-filter: blur(10px);
}

.gsw-shell {
    position: relative;
    min-height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
}

.gsw-card {
    position: relative;
    width: min(94vw, 720px);
    overflow: hidden;
    border-radius: 34px;
    background: rgba(255, 255, 255, .96);
    box-shadow: 0 30px 90px rgba(15, 23, 42, .35);
    border: 1px solid rgba(255, 255, 255, .65);
    display: grid;
    grid-template-columns: 260px 1fr;
    animation: gswIn .28s ease-out;
}

.gsw-close {
    position: absolute;
    right: 18px;
    top: 18px;
    z-index: 5;
    width: 38px;
    height: 38px;
    border-radius: 999px;
    border: 1px solid rgba(148, 163, 184, .35);
    background: rgba(255, 255, 255, .84);
    color: #0f172a;
    display: grid;
    place-items: center;
    cursor: pointer;
    transition: .18s ease;
}

.gsw-close:hover {
    transform: scale(1.05);
    background: #fff;
}

.gsw-visual {
    position: relative;
    min-height: 360px;
    display: grid;
    place-items: center;
    overflow: hidden;
    background:
        linear-gradient(145deg, var(--gsw-soft), rgba(255, 255, 255, .2)),
        radial-gradient(circle at 20% 20%, rgba(255,255,255,.7), transparent 32%);
}

.gsw-pulse {
    position: absolute;
    width: 180px;
    height: 180px;
    border-radius: 999px;
    background: var(--gsw-main);
    opacity: .13;
    animation: gswPulse 1.9s infinite ease-in-out;
}

.gsw-svg {
    position: relative;
    width: 220px;
    height: auto;
}

.gsw-svg-window {
    fill: rgba(255,255,255,.65);
    stroke: rgba(15,23,42,.08);
}

.gsw-svg-line-strong {
    fill: rgba(15,23,42,.72);
}

.gsw-svg-line {
    fill: rgba(15,23,42,.24);
}

.gsw-svg-circle {
    fill: var(--gsw-main);
}

.gsw-svg-alert {
    stroke: #111827;
    stroke-width: 8;
    stroke-linecap: round;
}

.gsw-svg-dot {
    fill: #111827;
}

.gsw-svg-wave {
    stroke: var(--gsw-main);
    stroke-width: 7;
    stroke-linecap: round;
    opacity: .8;
}

.gsw-content {
    padding: 54px 44px 38px;
}

.gsw-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border-radius: 999px;
    background: var(--gsw-soft);
    color: var(--gsw-dark);
    padding: 8px 13px;
    font-size: 12px;
    font-weight: 900;
    letter-spacing: .02em;
}

.gsw-badge::before {
    content: "";
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: var(--gsw-main);
    box-shadow: 0 0 0 5px var(--gsw-ring);
}

.gsw-title {
    margin: 18px 0 0;
    color: #0f172a;
    font-size: clamp(26px, 4vw, 38px);
    line-height: 1.05;
    font-weight: 950;
    letter-spacing: -.04em;
}

.gsw-message {
    margin: 16px 0 0;
    color: #475569;
    font-size: 15px;
    line-height: 1.75;
    white-space: pre-line;
}

.gsw-actions {
    margin-top: 26px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.gsw-button {
    border: none;
    border-radius: 18px;
    background: var(--gsw-main);
    color: #111827;
    padding: 13px 20px;
    font-size: 14px;
    font-weight: 950;
    cursor: pointer;
    box-shadow: 0 14px 30px var(--gsw-shadow);
    transition: .18s ease;
}

.gsw-button:hover {
    transform: translateY(-1px);
    filter: brightness(.98);
}

.gsw-footer {
    margin-top: 18px;
    color: #94a3b8;
    font-size: 12px;
}

.gsw-theme-amber {
    --gsw-main: #f59e0b;
    --gsw-soft: #fff7ed;
    --gsw-dark: #92400e;
    --gsw-ring: rgba(245, 158, 11, .18);
    --gsw-shadow: rgba(245, 158, 11, .28);
}

.gsw-theme-blue {
    --gsw-main: #38bdf8;
    --gsw-soft: #eff6ff;
    --gsw-dark: #075985;
    --gsw-ring: rgba(56, 189, 248, .18);
    --gsw-shadow: rgba(56, 189, 248, .25);
}

.gsw-theme-red {
    --gsw-main: #fb7185;
    --gsw-soft: #fff1f2;
    --gsw-dark: #9f1239;
    --gsw-ring: rgba(251, 113, 133, .18);
    --gsw-shadow: rgba(251, 113, 133, .25);
}

.gsw-theme-green {
    --gsw-main: #34d399;
    --gsw-soft: #ecfdf5;
    --gsw-dark: #065f46;
    --gsw-ring: rgba(52, 211, 153, .18);
    --gsw-shadow: rgba(52, 211, 153, .25);
}

.gsw-theme-purple {
    --gsw-main: #c084fc;
    --gsw-soft: #faf5ff;
    --gsw-dark: #6b21a8;
    --gsw-ring: rgba(192, 132, 252, .18);
    --gsw-shadow: rgba(192, 132, 252, .25);
}

@keyframes gswIn {
    from {
        opacity: 0;
        transform: translateY(18px) scale(.96);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes gswPulse {
    0%, 100% {
        transform: scale(.92);
        opacity: .11;
    }
    50% {
        transform: scale(1.18);
        opacity: .2;
    }
}

@media (max-width: 720px) {
    .gsw-card {
        grid-template-columns: 1fr;
    }

    .gsw-visual {
        min-height: 210px;
    }

    .gsw-svg {
        width: 180px;
    }

    .gsw-content {
        padding: 30px 24px 28px;
    }
}
</style>

<script>
(function () {
    const root = document.getElementById('globalSystemWarning');
    const backdrop = document.getElementById('globalSystemWarningBackdrop');
    const closeBtn = document.getElementById('globalSystemWarningClose');
    const actionBtn = document.getElementById('globalSystemWarningButton');
    const title = document.getElementById('globalSystemWarningTitle');
    const message = document.getElementById('globalSystemWarningMessage');
    const badge = document.getElementById('globalSystemWarningBadge');

    if (!root) return;

    let currentWarning = null;

    const labels = {
        development: 'Entwicklung läuft',
        uploading: 'Upload läuft',
        fixing: 'Fehlerbehebung',
        maintenance: 'Wartung',
    };

    function applyTheme(theme) {
        root.classList.remove(
            'gsw-theme-amber',
            'gsw-theme-blue',
            'gsw-theme-red',
            'gsw-theme-green',
            'gsw-theme-purple'
        );

        root.classList.add('gsw-theme-' + (theme || 'amber'));
    }

    function showWarning(warning) {
        currentWarning = warning;

        if (!warning || !warning.is_active) {
            hideWarning(true);
            return;
        }

        applyTheme(warning.theme);

        title.textContent = warning.title || 'Systemhinweis';
        message.textContent = warning.message || 'Bitte speichern Sie Ihre Arbeit.';
        actionBtn.textContent = warning.button_text || 'Verstanden';
        badge.textContent = labels[warning.type] || 'Systemhinweis';

        closeBtn.style.display = warning.can_close ? 'grid' : 'none';
        actionBtn.style.display = warning.can_close ? 'inline-flex' : 'none';
        backdrop.style.display = warning.show_backdrop ? 'block' : 'none';

        root.classList.remove('gsw-hidden');
        document.documentElement.style.overflow = 'hidden';
    }

    function hideWarning(force = false) {
        if (!force && currentWarning && !currentWarning.can_close) {
            return;
        }

        root.classList.add('gsw-hidden');
        document.documentElement.style.overflow = '';
    }

    closeBtn?.addEventListener('click', () => hideWarning(false));
    actionBtn?.addEventListener('click', () => hideWarning(false));

    async function loadCurrentWarning() {
        try {
            const response = await fetch('{{ route('system-warning.current') }}', {
                headers: {
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                showWarning(data.warning);
            }
        } catch (error) {
            console.warn('System warning could not be loaded.', error);
        }
    }

    function listenRealtime() {
        if (!window.Echo) {
            return;
        }

        window.Echo.channel('system-warning')
            .listen('.system.warning.updated', function (event) {
                showWarning(event.warning);
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        loadCurrentWarning();
        listenRealtime();
    });
})();
</script>