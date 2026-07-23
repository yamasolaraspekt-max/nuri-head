# Generator-Auftrag — Werkzeug-Dashboard (Design + Bau)

**Rolle:** Generator (Claude Code in VS Code). **Heimat-App:** `ticket`. Ausführen, „umgesetzt" melden;
Evaluator (VS Code) prüft. **Ausgestellt von:** Planner (Cowork), 2026-07-23.
**Basis-Branch:** aus dem Integrations-Tip `auto/hausplaner-ui-3b` (590700c). Neuer Branch `auto/hausplaner-ui-4`.

---

## TEIL 1 — Ist-Stand (was steht, was fehlt)

**Steht (Datenschicht):** `app/tools/toolTypes.ts` (ToolDefinition + Metadaten), `toolCatalog.ts`
(`TOOL_KATALOG`, 54 CAD-Tools), `toolRegistry.ts` (`TOOL_DEFINITIONS`, 8 Basis-Tools), `activation.ts`
(`resolveToolState`), `toolContext.ts` (`baueAktivierungsKontext`), UI-State (`uiState.ts`,
activeTool/selection). Commands `SET_NODES_SICHTBAR/GESPERRT` (Auge/Schloss).

**Fehlt (UI):** eine React-Dashboard-Oberfläche, die den Katalog rendert. Heute rendert
`HausplanerApp.tsx` nur die alte 6-Werkzeug-Leiste (hardcoded `werkzeugIcon`). Kein ToolButton/Tooltip/
Dashboard, keine Icons aus dem 65-Paket, keine aktiv/inaktiv-Darstellung aus `resolveToolState`.

---

## TEIL 2 — Vollständiges Dashboard-Design (verbindlich, nach `ux-design`)

**Grundhaltung:** dichtes, tägliches Werkzeug — kompakt, klare Hierarchie, ~90 % neutral, Marke nur als
Akzent für das aktive Werkzeug, semantische Statusfarben getrennt. Nächstes Werkzeug in < 2 s findbar.

**Layout-Zonen (Desktop-first):**
1. **Vertikale Werkzeugleiste (links, schmal ~52px):** die *häufigen* Werkzeuge als Icon-Buttons,
   nach Gruppen mit dünnen Trennern (Auswahl · Zeichnen · Transformation · Ausrichtung · Fang/Ansicht).
   Aktives Werkzeug = Marken-Akzent (Hintergrund-Tint + linker 2px-Balken). Klick setzt `activeToolId`.
2. **Voll-Dashboard (aufklappbares Panel / „Alle Werkzeuge", Shortcut z. B. `Ctrl+.`):** der komplette
   `TOOL_KATALOG`, **nach `group` gruppiert**, mit **Suchfeld** (filtert über label/meaning/usageArea/
   shortcut) und **Gruppen-Filter-Chips**. Jede Kachel = `ToolButton` (Icon + Label + Shortcut-Badge).
3. **Kontext-Optionsleiste (oben, unter der Topbar):** zeigt die Optionen des *aktiven* Werkzeugs
   (später; jetzt Platzhalter/leer zulässig).

**`ToolButton` (Anatomie):** Icon (aus `/icons/<id>.svg`), Label, optional Shortcut-Badge. Zustände:
- **aktiv-wählbar:** normal; Hover = leichte Erhebung.
- **ausgewählt (activeToolId):** Marken-Akzent-Tint + Rahmen/Balken.
- **deaktiviert:** ausgegraut (reduzierter Kontrast, aber **WCAG-lesbar**), **nicht klickbar**, `aria-disabled`.
  Der Grund (aus `resolveToolState().reason`) erscheint im Tooltip — **Status nie nur über Farbe**.

**`ToolTooltip` (Inhalt aus den Metadaten):** Titel (`tooltip.title`/label), Body (`tooltip.body`/meaning),
Einsatz (`tooltip.usage`/„Einsatzbereich: {usageArea}"), Shortcut. Bei deaktiviert zusätzlich die
**Deaktivierungs-Begründung** oben, farblich als „nicht verfügbar" markiert (Farbe **und** Text).
Öffnet nach ~400 ms Hover, per Tastatur (Fokus) ebenfalls; ARIA `role="tooltip"`.

**Aktiv/Inaktiv-Logik:** je Tool `resolveToolState(tool, ctx)` mit `ctx = baueAktivierungsKontext({
workspace, view, selectionTypes, selectionStates, permissions, projectState })` aus UI-State + Store.
Bei Selektionswechsel neu auswerten (memoisiert, kein Canvas-Vollrender).

**Barrierefreiheit (Pflicht):** Kontrast ≥ AA (4,5:1); Fokus sichtbar; Tab-Reihenfolge sinnvoll; Status
Farbe **+** Icon/Text; Tooltip per Tastatur erreichbar.

**Zustände der Ansicht:** Leerzustand der Suche („Kein Werkzeug für ‚{query}'"); geladen; keine Auswahl
(Tools mit minSelection deaktiviert + Grund). Keine destruktiven Ein-Klick-Unfälle (Löschen bestätigt).

**Token/CI:** Farben/Abstände/Radien aus dem gemeinsamen Token-System (`references/design-tokens.md`),
Marke nur Akzent (aktives Werkzeug + Kopf), Graustufen als Basis.

---

## TEIL 3 — Umsetzung (Bau)

1. **Icons stagen:** die 65 SVGs aus dem Paket nach `public/hausplaner/icons/<id>.svg` (oder
   `resources/planner/hausplaner/assets/icons/`), Katalog-`icon`-Pfade darauf ausrichten.
2. **Komponenten (React, aus den Vue-Vorlagen nachgebaut — NICHT Vue einbinden):**
   - `app/dashboard/ToolTooltip.tsx` — Tooltip-Inhalt oben beschrieben.
   - `app/dashboard/ToolButton.tsx` — Button + Zustände + Tooltip; Props: `tool: ToolDefinition`,
     `zustand: WerkzeugZustand`, `aktiv: boolean`, `onSelect`.
   - `app/dashboard/ToolDashboard.tsx` — gruppiert, Suche, Filter-Chips; liest `TOOL_KATALOG`, wertet
     je Tool `resolveToolState` aus.
   - Schmale **Werkzeugleiste** (häufige Tools) analog, teilt `ToolButton`.
3. **Verdrahten in `HausplanerApp.tsx`:** aktives Werkzeug aus `uiState` (bestehend); Auswahl der 6
   Kern-Tools auf die Katalog-IDs mappen (`auswahl→selection`, `wand→…` bzw. Bau-Tools ergänzen), damit
   **eine Wahrheit** (keine zweite Tool-Liste). Dashboard per Button/Shortcut ein-/ausblenden.
4. **Auge/Schloss** (UI-3a) als Werkzeuge/Aktionen im Dashboard: dispatchen `SET_NODES_SICHTBAR/GESPERRT`
   auf die aktuelle Auswahl.

## Gate (selbst, vor „umgesetzt")
`npm run tsc:hausplaner` (0) · `npm run test:hausplaner` (≥ vorher; füge Komponenten-nahe Logiktests
hinzu, z. B. „Dashboard filtert nach Suchbegriff", „deaktiviertes Tool liefert Grund") · `npm run
build:hausplaner`.

## Abnahmekriterien (Evaluator in VS Code — teils Sichtprüfung im Browser)
1. Dashboard rendert **alle 54 Katalog-Tools**, nach Gruppe sortiert, mit Icon + Tooltip.
2. **Deaktivierte** Tools zeigen den **Grund** im Tooltip (z. B. „align-left braucht mind. 2 Objekte"),
   sind nicht klickbar, `aria-disabled`.
3. **Suche/Filter** funktioniert (Begriff + Gruppen-Chip).
4. Aktives Werkzeug ist markiert; Klick wechselt `activeToolId` (eine Wahrheit, keine zweite Liste).
5. Kontrast AA gemessen; Fokus sichtbar; Status nicht nur Farbe.
6. `tsc`/`test`/`build` grün; **`roofType`-Enum unverändert**; Commit-Scope ohne Beifang.

## Bauordnung / Guardrails
Additiv; eine Wahrheit (UI-State = aktives Werkzeug, Katalog = Tool-Quelle); Icons sind eigen gezeichnet
(keine Adobe-Originale). Nur `auto/`-Branch, **KEIN main-Merge/Push/Deploy** ohne Yamas Wort.
**Hinweis Sichtprüfung:** die optische Abnahme (Aussehen/Klick) macht der Evaluator/Yama im Browser —
der Generator belegt Logik per Tests; „fertig" erst nach der Sichtprüfung.
