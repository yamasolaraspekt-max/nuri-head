# ⇒ GENERATOR — AUFTRAG Wizard-Welle A2: Die Werkzeugleiste liest die Präsentationsschicht

**Von:** Planner · **An:** Generator (nativ) · **Stand:** 25.07.2026, nach Abnahme A1
**Grundlage:** `c0ffe31` (A1 abgenommen, Evaluator-Re-Abnahme N1–N7 + unabhängiger Kreuzcheck)
**Heimat-App:** ticket · **Ballbesitz nach Umsetzung:** Evaluator

---

## 0. Ausgangslage — gemessen, nicht erinnert

A1 hat die Kuratierung als Daten gebaut (`app/tools/toolPresentation.ts`, 63 Regeln,
7 fix / 2 kontext / 15 weitere / 39 versteckt). **Diese Daten erreichen die Oberfläche bisher nicht.**

Gemessen am Stand `90d1b3c`:

- `zoneTools()` hat genau **einen** Verbraucher: `app/tools/faehigkeiten.ts:96`
  (`zoneTools('weitere')` für die Fähigkeiten-Navi).
- Die Werkzeugleiste in `HausplanerApp.tsx` (~Z.795) rendert **`werkzeugTools()`** aus
  `tools/toolRegistry.ts:168` — also `TOOL_DEFINITIONS.filter(t => t.art === 'werkzeug')`.
- `TOOL_DEFINITIONS` enthält **7× `art:'werkzeug'`** und **2× `art:'aktion'`**.
- Die Fix-Zone enthält **genau diese 7 ids in Registry-Reihenfolge** (`auswahl`, `wand`, `fenster`,
  `tuer`, `dach`, `decke`, `treppe`), die Kontext-Zone die 2 Aktionen (`loeschen`, `duplizieren`) —
  vom Evaluator in der Re-Abnahme namentlich belegt.

**Daraus folgt der Befund, der diesen Auftrag auslöst:** über die Zugehörigkeit zur Leiste
entscheiden heute **zwei** Mechanismen unabhängig voneinander — `art` in der Registry **und**
`zone` in den Präsentationsregeln. Sie stimmen momentan zufällig überein. Zwei Wahrheiten über
dieselbe Frage sind genau das, was A1 abschaffen sollte; A1 durfte die UI nur nicht anfassen.

---

## 1. Ziel & Entscheidung

**Die Leiste bezieht ihre Zugehörigkeit ausschließlich aus der Präsentationsschicht.**
`werkzeugTools()` wird in `HausplanerApp.tsx` durch **`zoneTools('fix')`** ersetzt.

Das ist heute **verhaltensneutral** — beide liefern dieselben 7 ids in derselben Reihenfolge.
Genau das ist der Sinn: die Umstellung ist beweisbar folgenlos, und danach entscheidet **eine**
Stelle. Wer künftig ein Werkzeug aus der Leiste nehmen oder hineinholen will, ändert eine Regel
in `toolPresentation.ts` — nicht ein `art`-Feld in der Registry.

Yamas stehende Regel gilt: **erst das Layout, Funktion darf noch fehlen.** A2 baut keine neue
Funktion; es verdrahtet die vorhandene Kuratierung an die vorhandene Leiste.

**Voraussetzung:** Die derzeit unfertige T1-Arbeit im Arbeitsbaum (`HausplanerApp.tsx`,
`studioDaten.ts`) ist **committet**, bevor A2 beginnt — A2 fasst dieselbe Datei an.

---

## 2. Nahtstellen — wo genau

- `resources/planner/hausplaner/app/HausplanerApp.tsx`
  - Import: `werkzeugTools` entfällt, `zoneTools` aus `./tools/toolPresentation` kommt hinzu.
    `toolFuerShortcut`/`toolNach` bleiben unverändert aus `toolRegistry`.
  - Der `.map()` über die Leiste liest `zoneTools('fix')`. **Sonst nichts** an diesem Block:
    gleiche Buttons, gleiche Icons, gleiche `resolveToolState`-Aktivierung, gleiche Stile.
  - Ergänzend erlaubt (und nur das): `aria-pressed={aktiv}` am Werkzeug-Button. Der aktive
    Zustand wird heute über Hintergrund **und** Schriftschnitt geführt, ist also nicht rein
    farbig (WCAG 1.4.1 gewahrt); `aria-pressed` schließt die Lücke für Screenreader.
- `resources/planner/hausplaner/__tests__/` — ein neuer Test (siehe §5).
- **Sonst keine Datei.**

---

## 3. Was ausdrücklich NICHT zu diesem Auftrag gehört

- **Kein Anheften/Pin, keine Persistenz, kein neues Store-Feld.** `usePlannerUiStore` bleibt
  unverändert (41 Zeilen, `activeToolId` + `activeWorkspace`). Persistenz ist eine offene
  Architektur-Entscheidung und wird gesondert vorgelegt.
- **Die Kontext-Zone wird NICHT gerendert.** Gemessener Grund: `loeschen` und `duplizieren`
  existieren bereits als Bedien-Buttons in der Operationsleiste (`OpBtn` „Auswahl duplizieren" /
  „Auswahl löschen", ~Z.776/777, verdrahtet auf `dupliziere` / `loescheAuswahl`). Ein zweites
  Rendern erzeugte doppelte Bedienung. Diese Doppelung sauber aufzulösen ist ein **eigener**
  Posten (A3), weil sie die Auswahl-Kontextlogik berührt.
- **`weitere` und `versteckt` bleiben unangetastet.** `FaehigkeitenNavi` und `faehigkeiten.ts`
  werden nicht umgebaut.
- **Kein T1-Beifang.** Farben/Tokens werden in A2 nicht angefasst.
- **Keine Umbenennung, kein Entfernen von `art`** in `toolTypes.ts`/`toolRegistry.ts`. `art`
  behält seine übrigen Aufgaben; A2 nimmt ihm nur die Leisten-Entscheidung ab.

---

## 4. Kantenliste

1. **Leere Zone:** liefert `zoneTools('fix')` `[]`, darf die Leiste nicht crashen — leerer
   Abschnitt, Rest der Navigation steht.
2. **Reihenfolge:** die Regeln liefern Registry-Reihenfolge. Kippt sie, wandern Icons — deshalb
   ist die Reihenfolge Abnahme-Kriterium, nicht nur die Menge.
3. **Aktives Werkzeug verschwindet:** wäre `activeToolId` ein Werkzeug, das nicht (mehr) in der
   Fix-Zone liegt, muss der bestehende Rückfall auf `'auswahl'` (~Z.179) weiter greifen.
   Nicht neu bauen — nur belegen, dass er unberührt bleibt.
4. **Shortcuts:** `toolFuerShortcut` arbeitet weiter auf der Registry. Ein Kürzel darf kein
   Werkzeug aktivieren, das nicht in der Leiste steht — heute unmöglich (Zone == art), aber als
   Testfall festhalten, damit es beim ersten Auseinanderlaufen auffällt.
5. **`resolveToolState`** bleibt der einzige Deaktivierungs-Mechanismus. Kein zweiter Filter.

---

## 5. Abnahmekriterien (überprüfbar, nicht gefühlt)

1. Alle vier Gates grün, selbst ausgeführt: `tsc:hausplaner`, `schema:hausplaner:check`,
   `test:hausplaner`, `build:hausplaner`. Zod unberührt ⇒ Schema-Check **ohne** Regen grün.
2. Testzahl steigt gegenüber `c0ffe31` (695) um die neuen Fälle; **0 fail**.
3. **Neuer Test „Leiste == Fix-Zone":** die von der Leiste gerenderte id-Liste ist identisch mit
   `zoneTools('fix')` — als Liste, **in Reihenfolge**.
4. **Gegenprobe, tatsächlich rot gesehen:** eine Fix-Regel (z. B. `dach`) auf `versteckt` setzen
   ⇒ die Leisten-Liste schrumpft auf 6 und mindestens ein Test wird rot. Testnamen nennen,
   Datei danach zurücksetzen, `git diff` leer.
5. **Beleg, dass die zweite Wahrheit weg ist:** `werkzeugTools` kommt in `HausplanerApp.tsx`
   nicht mehr vor (Grep-Beleg). Falls die Funktion danach gar keinen Verbraucher mehr hat, das
   **berichten, nicht löschen** — Entscheidung Planner.
6. **Verhaltensgleichheit heute:** die 7 ids der Leiste sind vor und nach der Änderung identisch,
   in gleicher Reihenfolge, gegen `git show` der alten Fassung ausgeführt — nicht abgeleitet.
7. Guardrail-Diffs leer: `toolTypes.ts`, `toolRegistry.ts`, `toolPresentation.ts`,
   `activation.ts`, `toolCatalog.ts`, `uiState.ts`, `domain/*`, `geometry/*`, `renderers/*`, PHP.

---

## 6. Guardrails

- **Eine Wahrheit:** nach A2 entscheidet nur `TOOL_PRESENTATION_RULES` über die Leisten-Zugehörigkeit.
- **Additiv:** kein Schema, kein Pflichtfeld, keine Migration, kein 422-Risiko.
- **UI-SSOT:** `activeToolId` bleibt allein in `usePlannerUiStore`; kein paralleler `useState`.
- **Kein `main`-Merge, kein Deploy** — Tor 2 gehört Yama.
- **Push nur** über `push-integration-sicher.command` zu `fork` + `backup-private`, nie `upstream`
  (`raminsadid2021` = fremdes Konto), nie `--force`.
- Der Generator meldet **„umgesetzt"**, nie „abgenommen".

---

## 7. Bericht

In `docs/handoff-status.md` als Block `## ⇒ GENERATOR-BERICHT — Wizard-Welle A2 UMGESETZT`:
Gate-Exit-Codes, Testzahl vorher/nachher, die 7 ids vorher/nachher in Reihenfolge, die rot
gesehenen Testnamen der Gegenprobe, der Grep-Beleg zu `werkzeugTools`, die leeren Guardrail-Diffs
und was offen bleibt. Ballbesitz danach an den Evaluator.
