{{--
    Arbeitsliste — Design-Tokens (EINE Wahrheit fuers Design).
    Jeder Farb-/Abstands-/Schrift-Wert ist hier GENAU EINMAL definiert; View + Komponenten
    referenzieren ausschliesslich via var(--al-…). KEIN hartkodierter Farb-Hex ausserhalb dieser
    Datei. Bewusst auf .al-page GESCOPED (nicht global :root), damit die Tokens die bestehenden
    Layout-Variablen (--bg, --border-color, --brand-*) NICHT ueberschreiben (additiv, Bauordnung).
    Accent = Solar-Aspekt-Navy. Statusfarben SEMANTISCH getrennt (rot/amber/gruen/blau);
    Marken-Gruen wird NICHT als Status-Gruen verwendet.
--}}
<style>
    .al-page {
        /* Flaechen / Text */
        --al-bg: #ffffff;
        --al-bg-muted: #f5f6f7;
        --al-border: #e2e4e7;
        --al-text: #1c1f23;
        --al-text-muted: #5b616b;

        /* Marke (Accent = Navy, Weisstext AA-sicher) */
        --al-accent: var(--sa-accent, #93c21c);
        --al-accent-ink: #ffffff;
        --al-accent-hover: var(--sa-accent-hover, #7baa18);

        /* Semantische Statusfarben — je Status: Vollton, heller Grund, dunkle Tinte (AA) */
        --al-danger: #dc2626;
        --al-danger-bg: #fdecec;
        --al-danger-ink: #991b1b;

        --al-warning: #d97706;
        --al-warning-bg: #fdf3e6;
        --al-warning-ink: #92400e;

        --al-success: #16a34a;
        --al-success-bg: #e9f7ee;
        --al-success-ink: #15803d;

        --al-info: #2563eb;
        --al-info-bg: #e8f0fe;
        --al-info-ink: #1d4ed8;

        /* Abstaende (4 / 8 / 12 / 16 / 24 / 32) */
        --al-space-1: 4px;
        --al-space-2: 8px;
        --al-space-3: 12px;
        --al-space-4: 16px;
        --al-space-5: 24px;
        --al-space-6: 32px;

        /* Radius */
        --al-radius: 6px;
        --al-radius-pill: 999px;

        /* Schriftgroessen */
        --al-fs-xs: 12px;
        --al-fs-sm: 13px;
        --al-fs-md: 15px;
        --al-fs-lg: 18px;
        --al-fs-xl: 22px;

        /* Fokus-Ring */
        --al-focus: var(--sa-accent, #93c21c);

        color: var(--al-text);
        background: var(--al-bg);
        font-size: var(--al-fs-sm);
        line-height: 1.45;
        padding: var(--al-space-5);
        max-width: 1120px;
    }

    /* ---- Kopf ---- */
    .al-page-head {
        margin-bottom: var(--al-space-5);
    }

    .al-page-title {
        font-size: var(--al-fs-xl);
        font-weight: 800;
        margin: 0 0 var(--al-space-1) 0;
        color: var(--al-text);
    }

    .al-page-sub {
        font-size: var(--al-fs-sm);
        color: var(--al-text-muted);
        margin: 0;
    }

    /* ---- Sektion ---- */
    .al-section {
        border: 1px solid var(--al-border);
        border-radius: var(--al-radius);
        background: var(--al-bg);
        margin-bottom: var(--al-space-5);
        overflow: hidden;
    }

    .al-section-head {
        display: flex;
        align-items: center;
        gap: var(--al-space-2);
        padding: var(--al-space-3) var(--al-space-4);
        border-bottom: 1px solid var(--al-border);
        background: var(--al-bg-muted);
    }

    .al-section-icon {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
        color: var(--al-text-muted);
    }

    .al-section-title {
        font-size: var(--al-fs-md);
        font-weight: 700;
        margin: 0;
        color: var(--al-text);
    }

    .al-badge {
        margin-left: var(--al-space-1);
        min-width: 20px;
        height: 20px;
        padding: 0 var(--al-space-2);
        border-radius: var(--al-radius-pill);
        background: var(--al-accent);
        color: var(--al-accent-ink);
        font-size: var(--al-fs-xs);
        font-weight: 700;
        line-height: 20px;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-variant-numeric: tabular-nums;
    }

    .al-badge.is-zero {
        background: var(--al-border);
        color: var(--al-text-muted);
    }

    /* ---- Liste / Zeile ---- */
    .al-list {
        display: flex;
        flex-direction: column;
    }

    .al-row {
        display: flex;
        align-items: center;
        gap: var(--al-space-3);
        min-height: 40px;
        padding: var(--al-space-2) var(--al-space-4);
        border-top: 1px solid var(--al-border);
        text-decoration: none;
        color: inherit;
    }

    .al-row:first-child {
        border-top: none;
    }

    .al-row:nth-child(even) {
        background: var(--al-bg-muted);
    }

    .al-row:hover {
        background: color-mix(in srgb, var(--al-accent) 6%, var(--al-bg));
    }

    .al-row-main {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
        flex: 1 1 auto;
    }

    .al-row-title {
        font-size: var(--al-fs-sm);
        font-weight: 600;
        color: var(--al-text);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .al-row-meta {
        font-size: var(--al-fs-xs);
        color: var(--al-text-muted);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .al-row-amount {
        font-size: var(--al-fs-sm);
        font-weight: 700;
        color: var(--al-text);
        text-align: right;
        flex-shrink: 0;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .al-row-side {
        display: flex;
        align-items: center;
        gap: var(--al-space-3);
        flex-shrink: 0;
    }

    /* ---- Pill (Farbe UND Text) ---- */
    .al-pill {
        display: inline-flex;
        align-items: center;
        gap: var(--al-space-1);
        padding: 2px var(--al-space-2);
        border-radius: var(--al-radius-pill);
        font-size: var(--al-fs-xs);
        font-weight: 700;
        line-height: 16px;
        white-space: nowrap;
        border: 1px solid transparent;
    }

    .al-pill--danger {
        background: var(--al-danger-bg);
        color: var(--al-danger-ink);
        border-color: var(--al-danger);
    }

    .al-pill--warning {
        background: var(--al-warning-bg);
        color: var(--al-warning-ink);
        border-color: var(--al-warning);
    }

    .al-pill--success {
        background: var(--al-success-bg);
        color: var(--al-success-ink);
        border-color: var(--al-success);
    }

    .al-pill--info {
        background: var(--al-info-bg);
        color: var(--al-info-ink);
        border-color: var(--al-info);
    }

    /* ---- Buttons ---- */
    .al-btn {
        display: inline-flex;
        align-items: center;
        gap: var(--al-space-1);
        padding: var(--al-space-1) var(--al-space-3);
        border-radius: var(--al-radius);
        font-size: var(--al-fs-sm);
        font-weight: 600;
        line-height: 1.4;
        cursor: pointer;
        text-decoration: none;
        border: 1px solid transparent;
        white-space: nowrap;
    }

    .al-btn--primary {
        background: var(--al-accent);
        color: var(--al-accent-ink);
        border-color: var(--al-accent);
    }

    .al-btn--primary:hover {
        background: var(--al-accent-hover);
        border-color: var(--al-accent-hover);
        color: var(--al-accent-ink);
    }

    .al-btn--secondary {
        background: var(--al-bg);
        color: var(--al-text);
        border-color: var(--al-border);
    }

    .al-btn--secondary:hover {
        background: var(--al-bg-muted);
    }

    /* ---- Fokus (sichtbar, AA) ---- */
    .al-row:focus-visible,
    .al-btn:focus-visible,
    .al-page a:focus-visible {
        outline: 2px solid var(--al-focus);
        outline-offset: 2px;
        border-radius: var(--al-radius);
    }

    /* ---- Empty-State ---- */
    .al-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: var(--al-space-2);
        padding: var(--al-space-6) var(--al-space-4);
        color: var(--al-text-muted);
    }

    .al-empty-icon {
        width: 32px;
        height: 32px;
        color: var(--al-success);
    }

    .al-empty-title {
        font-size: var(--al-fs-md);
        font-weight: 700;
        color: var(--al-text);
        margin: 0;
    }

    .al-empty-text {
        font-size: var(--al-fs-sm);
        margin: 0;
        max-width: 40ch;
    }

    /* ---- Fehler-Zustand ---- */
    .al-error {
        display: flex;
        align-items: flex-start;
        gap: var(--al-space-2);
        padding: var(--al-space-3) var(--al-space-4);
        background: var(--al-danger-bg);
        color: var(--al-danger-ink);
        font-size: var(--al-fs-sm);
        border-top: 1px solid var(--al-danger);
    }

    .al-error-icon {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
    }

    /* Globaler Fehler-Zustand = eigenstaendiges Panel (nicht der In-Sektion-Streifen). */
    .al-error-panel {
        border: 1px solid var(--al-danger);
        border-radius: var(--al-radius);
    }

    .al-error-title {
        font-weight: 700;
        margin: 0 0 2px 0;
    }

    .al-error-text {
        margin: 0;
        opacity: .9;
    }

    /* Sektions-Leere = leise Inline-Zeile (untergeordnet), NIE der grosse globale Empty-Block. */
    .al-section-empty {
        margin: 0;
        padding: var(--al-space-3) var(--al-space-4);
        font-size: var(--al-fs-sm);
        color: var(--al-text-muted);
    }

    /* „mehr"-Zeile: dezent, am Fuss der Liste. Als Link (Accent) oder statischer Hinweis (muted).
       Nur Tokens, kein Hex — EINE Wahrheit fuers Design bleibt hier. */
    .al-section-more {
        display: flex;
        align-items: center;
        gap: var(--al-space-1);
        margin: 0;
        padding: var(--al-space-2) var(--al-space-4);
        border-top: 1px solid var(--al-border);
        background: var(--al-bg-muted);
        font-size: var(--al-fs-xs);
        font-weight: 600;
        color: var(--al-accent);
        text-decoration: none;
    }

    a.al-section-more:hover {
        background: color-mix(in srgb, var(--al-accent) 6%, var(--al-bg-muted));
        text-decoration: underline;
    }

    .al-section-more--static {
        color: var(--al-text-muted);
        font-weight: 400;
    }

    .al-section-more-icon {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
    }

    /* ---- Responsive (Handy vor Ort): Betrag/Pill nie abgeschnitten ---- */
    @media (max-width: 640px) {
        .al-page {
            padding: var(--al-space-4);
        }

        .al-row {
            flex-wrap: wrap;
            align-items: flex-start;
        }

        .al-row-main {
            flex: 1 1 100%;
        }

        .al-row-side {
            flex: 1 1 100%;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .al-row-amount {
            text-align: left;
        }
    }

    /* ---- Dark-Theme des Bestands: Flaechen/Text angleichen (Tokens bleiben EINE Wahrheit) ---- */
    html.dark .al-page {
        --al-bg: #1b1d21;
        --al-bg-muted: #23262b;
        --al-border: #34383f;
        --al-text: #e7e9ec;
        --al-text-muted: #a3a9b3;
        --al-accent: #4f79d6;
        --al-accent-ink: #0f1114;
        --al-accent-hover: #6b8fe0;
        --al-danger-bg: #3a1d1d;
        --al-danger-ink: #f4b4b4;
        --al-warning-bg: #3a2c16;
        --al-warning-ink: #f2ce93;
        --al-success-bg: #17311f;
        --al-success-ink: #a7e0bd;
        --al-info-bg: #1c2b47;
        --al-info-ink: #b6cdf6;
        --al-focus: #7ba0e8;
    }
</style>
