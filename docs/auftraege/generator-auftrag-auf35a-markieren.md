# ⇒ GENERATOR — AUFTRAG AUF-35a: „Markieren" — Mehrfachauswahl, Auswahlmodi, Hit-Test

**Vorher gelesen:** HEAD `8b2af94` · `package.json` (React 19 · Zustand 5 · Konva/react-konva ·
three) · `store/hausplanerStore.ts:30,64,77,87,89` (`selectedNodeIds`, `selectNodes`) ·
`app/HausplanerApp.tsx:369,388,405,414,419` (**fünfmal** `selectedNodeIds.length === 1`) ·
`:728–737` (Modifikatoren nur für Undo/Redo/Speichern/Palette) · `domain/commands.types.ts`
(`SET_NODES_GESPERRT`, `SET_NODES_SICHTBAR`) · `renderers/` (nur `three-d`, **kein** 2D-Modul)

**Angelegt:** 25.07.2026 · **Vom:** Planner · **Tafel:** AUF-35a · **Spur:** **A**
**Vorbedingung: AUF-34.** **Vorbedingung für AUF-35b** (Flächen-/Zonenauswahl).
**Grundlage:** Yamas Referenz-Implementierung „Markieren / Auswahl" vom 25.07.

---

## Ausgangslage, gemessen

**Das Datenmodell kann längst mehrere:** `selectedNodeIds: string[]` (`:30`), gesetzt über
`selectNodes(ids)` (`:89`). **Die Oberfläche kann genau eines:** an **fünf** Stellen steht
`selectedNodeIds.length === 1` — Dach, Wand, Öffnung und zwei weitere. Und die Modifikatortasten
werden beim Klick **gar nicht** ausgewertet; `shiftKey`/`ctrlKey` erscheinen nur in der
Tastaturbehandlung für Undo, Redo, Speichern und Palette.

Damit ist das genau der Slice, den `fahrplan-dashboard-versionen.md` §2 als **UI-5 ❌** führt:
*„Selektion + Panel-Tabs + Mehrfach — Panel ist per-Typ und nur `length===1`."*

## Ziel & Entscheidung

**Yamas Referenz ist der Bauplan — aber sie ist für Vue 3 und Pinia geschrieben, und der Hausplaner
ist React 19 mit Zustand.** Es gilt die Konflikt-Regel: **der neue Code passt sich dem Bestand an.**

### Übernommen wird, was reine Logik ist — und das ist der wertvolle Teil

1. **Auswahlmodi** `replace · add · remove · toggle` und die Ableitung aus der Eingabe:
   `Shift` → hinzufügen · `Strg/Cmd` → umschalten · `Alt` → entfernen · sonst ersetzen ·
   **Klick auf leere Fläche ohne Modifikator** → Auswahl aufheben.
   Als **reine Funktion** `aufloeseAuswahlmodus(event)` — ohne DOM testbar.
2. **Hit-Test** als reine Funktion: Toleranz in Pixeln, Treffer sortiert **erst nach Zeichenreihen-
   folge, dann nach Distanz**; unsichtbare und nicht wählbare Objekte fallen raus.
3. **Darstellungszustand als reine Funktion** `aufloeseDarstellung({ausgewaehlt, primaer, ueberfahren,
   gesperrt, ungueltig})` → Strichstärke, Deckkraft, Griffe, Schloss-Abzeichen. **Das ist exakt das
   Muster aus I3** (Zustand als Daten, nicht als Markup) — wiederverwenden, nicht neu erfinden.
4. **Nur IDs speichern.** Keine Meshes, keine SVG-Knoten, keine kopierten Objekte, **keine getrennte
   2D- und 3D-Auswahl**. Eine Auswahl, beide Ansichten.
5. **Auswahl verändert das Modell nicht ⇒ kein Undo.** Deckt sich mit `undoable: false` im
   Funktionsvertrag und mit der Regel, dass nur `applyCommand` das Dokument anfasst.

### Nicht übernommen

- **Kein Pinia, kein `defineStore`, keine `.vue`-Datei.** Der Auswahlzustand liegt bereits in
  `hausplanerStore.ts` — **ein zweiter Store wäre die zweite Wahrheit.**
- Additiv ergänzt werden dort lediglich **`primaerId`** und **`ueberfahrenId`**; beide fehlen heute
  und beide werden gebraucht (Primärobjekt fürs Panel, Hover für die Vorschau).

### Das Label

Yamas Referenz führt `label: "Markieren"`, `shortLabel: "Auswahl"`. **So wird es übernommen:**
sichtbar **„Markieren"**, Kurzform **„Auswahl"**, id bleibt `auswahl`, Kürzel bleibt `V`.
Damit ist die Benennungsfrage vom 25.07. beantwortet.

## Was NICHT dazugehört

- **Keine Flächen- oder Zonenauswahl** — das ist AUF-35b und braucht das hier als Fundament.
- Keine neuen Commands. `SET_NODES_GESPERRT`/`SET_NODES_SICHTBAR` existieren und werden **gelesen**,
  nicht ergänzt.
- Kein Rechteck-/Lasso-Aufziehen. Die Werkzeuge `rechteckauswahl`/`lassoauswahl` bleiben
  `in Entwicklung` — eigener Posten.
- `domain/*`, `geometry/*`, Zod, Schema, PHP, `public/*`.

## Kantenliste

1. **Gesperrtes Objekt:** wird **angezeigt und ist wählbar**, aber nicht bearbeitbar — das Panel
   zeigt den Grund. Es aus der Auswahl auszuschließen wäre falsch: man muss sehen können, was
   sperrt.
2. **Auswahl über Geschosswechsel:** `setActiveLevel` leert die Auswahl heute (`:87`). **Das bleibt
   so** — Objekte anderer Geschosse dürfen nicht in einer unsichtbaren Auswahl hängen.
3. **Primärobjekt beim Entfernen:** wird das primäre Objekt abgewählt, rückt das **zuletzt
   verbliebene** nach; ist die Auswahl leer, wird es `null`.
4. **Mehrfachauswahl gemischter Typen** (Wand + Fenster + Dach): das Panel darf nicht raten. Es
   zeigt eine **Mehrfach-Ansicht** mit Anzahl je Typ, keine Einzelfelder.
5. **Klick auf leere Fläche mit gedrücktem Modifikator** hebt die Auswahl **nicht** auf — sonst
   verliert man beim Danebentreffen die ganze Mehrfachauswahl.
6. **Fokus/Tastatur:** `Esc` hebt die Auswahl auf. Kein fokussierbares Steuerelement in einer im
   Rumpf definierten Komponente (Befund B1).

## Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` (**ohne Regen**) · `test:hausplaner` — **Exit 0**.
2. Testzahl vorher/nachher, Namen-Mengen verglichen, kein verschwundener Test.
3. Ein Test je Auswahlmodus: `replace · add · remove · toggle` — inklusive doppeltes Hinzufügen
   (keine Dublette) und Entfernen des Primärobjekts (Kante 3).
4. Ein Test belegt die **Ableitung aus der Eingabe**: Shift→add, Strg/Cmd→toggle, Alt→remove,
   sonst replace. Reine Funktion, ohne DOM.
5. Ein Test belegt den **Hit-Test**: Treffer mit höherer Zeichenreihenfolge gewinnt; bei gleicher
   Reihenfolge der nähere; unsichtbar und nicht wählbar werden übersprungen; Toleranz wirkt.
6. Ein Test belegt `aufloeseDarstellung` für alle fünf Zustände — **kein roher Farbwert**, nur Token.
7. **Die fünf `length === 1`-Stellen sind aufgelöst:** Grep über `HausplanerApp.tsx` nach
   `length === 1` liefert **keinen** Treffer mehr in der Panel-Auswertung. Rohausgabe im Bericht.
8. Ein Test belegt Kante 4: gemischte Mehrfachauswahl ⇒ Mehrfach-Ansicht mit Anzahl je Typ.
9. **Gegen-Beweis, selbst geführt:** die Sortierung im Hit-Test umdrehen → mindestens ein Test
   **muss** rot werden. Danach zurückbauen, `git diff` leer.
10. `git diff` zeigt null Zeilen in `domain/*`, `geometry/*`, `public/*`, PHP.
11. **Spalte „Sieht Yama das?": `sichtbar`** ⇒ Browser-Sichtprobe Teil der Abnahme, mit genannter
    Fensterbreite: **Shift-Klick wählt zwei Wände, das Panel zeigt die Mehrfach-Ansicht.**

## Guardrails

- Posten **auf der Tafel ziehen, bevor** die erste Zeile geschrieben wird.
- **Ein Commit**, Pfadangabe zwingend. **Nie `-A`, nie `.`**, `-m` **vor** dem `--`.
- `.git/*.lock` nur per `mv`. **Kein Push, kein Merge, kein Deploy.**
- **„umgesetzt", nie „abgenommen".**

## Bericht

`## ⇒ GENERATOR-BERICHT — AUF-35a Markieren`, mit den elf Kriterien als Rohausgabe, dem
Gegen-Beweis aus Kriterium 9, der Liste der aufgelösten `length === 1`-Stellen und dem Commit-Hash.
