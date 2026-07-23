# Generator-Auftrag — Batch 1: Haustechnik-Panels (Engines real bedienbar)

**Rolle:** Generator (Claude Code in VS Code). **Heimat-App:** `ticket`. **Ausgestellt von:** Planner, 2026-07-23.
**Basis:** der grüne Batch-0-Tip (nach `navi-batch0-fix`). **Geparkt** bis Batch 0 (Logik+Optik) FREIGABE.
Kein Browser-Zwang für die Logik, aber sichtbarer Slice → die Optik-Dimension (3 VP + Fachagenten) gilt.

## Ziel
Die drei 🔴-Heizungs-Engines aus der Landkarte werden **real bedienbar** — je ein Eingang→Ergebnis-Panel in
der FaehigkeitenNavi, das die **echte, getestete** Engine-Funktion **getippt importiert und aufruft** (das
ersetzt zugleich die String-Referenz durch einen tsc-geprüften Import — die stehende AP-E-Regel greift).

## Gemeinsames Muster (EINE Wahrheit — vor den Panels bauen)
Alle Engines liefern denselben Prüf-Typ (`{id, schwere:'info'|'warnung'|'fehler', meldung, bestanden}` + Flag
`bestanden`). Deshalb **zwei** wiederverwendbare Bausteine in `T`-Tokens (studioDaten.ts):
1. **`EnginewerkzeugPanel`** — Kopf (Titel/Zustand-Pille) · Spalte Eingang (Felder) · „Berechnen" (`T.brand`)
   · Spalte Ergebnis (Kennwerte) · **Prüfungsliste**. Ein Panel, per Konfig (Felder + Ergebnis-Mapping) je Engine.
2. **`Pruefungsliste`** — rendert `pruefungen[]`: `schwere` → `T.info/T.warn/T.err`, **Farbe UND Text**
   (Meldung sichtbar), `bestanden` als Häkchen/Kreuz. Kein Nur-Farbe-Signal (A11y).
Keine Engine wird geändert (Byte-Treue) — nur importiert/aufgerufen.

## Die drei Panels (aus den echten Signaturen)
### 1. Fußbodenheizung → `geometry/fbhAuslegung.ts` · `fbhAuslegung(e: FbhEingabe): FbhErgebnis`
- **Eingang:** `flaeche` (m²), `heizlast` (W), `verlegeabstand?` (cm), `sperrflaeche?` (m²),
  `maxKreisLaenge?` (m), `anbindungProKreis?` (m).
- **Ergebnis:** `nutzflaeche` m² · `spezifischeLeistung` W/m² · `anzahlHeizkreise` · `rohrProKreis` m ·
  `rohrlaengeGesamt` m · `bestanden` + `pruefungen`.

### 2. Heizkörper → `geometry/heizkoerperLeistung.ts`
- **Erforderliche Norm-Leistung:** `benoetigteNormleistung(raumheizlast, b: BetriebsBedingung)`.
- **Deckung eines gewählten Heizkörpers:** `bewerteDeckung(normLeistung, raumheizlast, b): DeckungErgebnis`.
- **Eingang:** `raumheizlast` (W), `vorlauf`/`ruecklauf`/`raumtemp` (°C), optional `normLeistung` (W, gewählter HK).
- **Ergebnis:** erforderliche Normleistung (W) · bei gewähltem HK: `betriebsLeistung` W · `deckungsgrad` % ·
  `ausreichend` + `hinweis`.

### 3. Heizkreis/Verteiler → `geometry/heizkreisVerteiler.ts` · `auslegeVerteiler(kreise: HeizkreisEingabe[]): VerteilerErgebnis`
- **Eingang:** Liste von Kreisen `{ raum?, leistung (W), vorlauf, ruecklauf (°C) }` (Mini-Tabelle, Zeilen add/del).
- **Ergebnis:** `abgaenge` · `gesamtDurchfluss` kg/h · je Kreis `spreizung` K + `durchfluss` kg/h · `pruefungen`.

## Kopplung (Fahrplan-Konzept)
`heizlast`/`raumheizlast` ist der **Ausgang des Heizlast-Schritts** (Drehscheibe). In Batch 1 ist es ein
**manuelles Eingabefeld**; die Auto-Befüllung aus dem Heizlast-Ergebnis ist eine spätere Verdrahtung
(eigener Slice, wenn der Heizlast-Rechner steht — siehe Backlog).

## Gate (Generator selbst)
`tsc:hausplaner` 0 · `schema:hausplaner:check` 0 (kein Modell/Schema-Eingriff) · `test:hausplaner`
(+ Tests: „Panel X ruft Engine X, Ergebnis == Engine-Rückgabe") · `build:hausplaner` (nativ/x64).

## Abnahme (Evaluator — Logik per Test, Optik im Browser)
1. Jedes Panel **importiert die Engine getippt** (kein String) und zeigt den **echten** Rückgabewert
   (Test: Panel-Ausgabe == direkte Engine-Ausgabe für dieselbe Eingabe).
2. **Ein** `EnginewerkzeugPanel` + **eine** `Pruefungsliste` (keine drei Kopien); Engines unverändert (Diff).
3. `pruefungen` als **Farbe UND Text** (`T.info/warn/err`); Zustand/Fehlerfall gestaltet (leere Eingabe → Hinweis).
4. **Token-Disziplin:** 0 Hex in den neuen Dateien (nur `T.*`); Marken-Grün = `T.brand`.
5. Additiv; keine Regression Batch 0; nur `auto/`-Branch, **kein Push**. 3-VP-Sicht + vier Fachagenten.

## Guardrails
Engines NUR aufrufen (Byte-Treue); eine Wahrheit (ein Panel-Baustein, `T`-Tokens); kein Beifang;
Meldung „umgesetzt" (4 Exit-Codes) → Evaluator, Pflicht-Stopp.
