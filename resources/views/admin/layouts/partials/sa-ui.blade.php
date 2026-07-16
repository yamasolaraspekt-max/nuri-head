{{--
    SA-UI-Tokens — EINE Wahrheit für die Aktionsfarbe & Statusfarben (CI Welle 3, 2026-07-16).
    Vorher definierten ~68 Seiten je eigene --primary-Werte (#93c21c, #8fc73e, #74b2d4 …).
    Jetzt referenzieren alle var(--sa-accent*); geändert wird nur noch HIER.
    Marke: Solar-Aspekt-Navy als Akzent (Arbeitsliste-Standard). Grün bleibt semantisch (Erfolg).
--}}
<style>
    :root {
        --sa-accent: #1C3F94;
        --sa-accent-hover: #16336f;
        --sa-accent-light: #e8edf7;
        --sa-accent-ink: #ffffff;

        /* Semantische Statusfarben (getrennt von der Marke) */
        --sa-danger: #dc2626;  --sa-danger-bg: #fdecec;
        --sa-warning: #d97706; --sa-warning-bg: #fdf3e6;
        --sa-success: #16a34a; --sa-success-bg: #e9f7ee;
        --sa-info: #2563eb;    --sa-info-bg: #e8f0fe;
    }
</style>
