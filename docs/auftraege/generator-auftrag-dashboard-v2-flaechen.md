# ⇒ GENERATOR — AUFTRAG Dashboard v2: „Der Rahmen steht"

**Von:** Planner · **An:** Generator (nativ) · **Stand:** 25.07.2026
**Grundlage:** `docs/fahrplan-dashboard-versionen.md` (Versionsschnitt) · `docs/planner/tool-dashboard-current-state.md` (festgelegtes Design, §8 Slices)
**Anlass:** Yama, 25.07.: *„wir haben dashboard design fest gelegt sollst als erstes fertig gestellt werden v1 usw"*
**Heimat-App:** ticket · **Ballbesitz nach Umsetzung:** Evaluator

---

## 0. Ausgangslage — gemessen am Code, nicht aus dem Fahrplan abgeschrieben

Messung am Stand `7f1ecd6`, Datei `resources/planner/hausplaner/app/HausplanerApp.tsx` (**1.431 Zeilen**):

| Was | Befund | Belegstelle |
|---|---|---|
| Aktives Werkzeug | liegt im geteilten UI-State, **eine** Wahrheit | `:131` Kommentar, `:133` `usePlannerUiStore((s) => s.activeToolId)` |
| Werkzeugleiste | **linke Schiene, 220 px**, nicht Topbar; datengetrieben aus der Registry | `:795` `width: 220`, `:797` UI-3-Kommentar, `:798` `werkzeugTools().map` |
| Eigenschaftenpanel | **rechte Spalte, 268 px**, ohne Tabs, greift nur bei `length === 1` | `:1098`, `:1099` `width: 268`, `:251` `selectedNodeIds.length === 1` |
| Werkzeug-**Optionen** | existieren, aber **hand­verdrahtet in der Kopfzeile** zwischen Undo/Redo und Speichern: Fenstertyp/Türtyp-`<select>` | `:655`–`:666` |
| Werkzeug-**Hinweise** | existieren bereits in der Statusleiste, je Werkzeug | `:1422`–`:1425` |
| Flächen­rechnung | `innerWidth − 220 − 268` | `:590` |
| Abgelehnte Commands | Store hält **genau eine** Meldung als `string \| null`, **keine Liste**; `CommandAbgelehnt.grund` geht dabei verloren | `store/hausplanerStore.ts:34`, `:110`; `domain/commands.types.ts:70-78` |
| Auswahl setzen | vorhanden: `selectNodes(ids)` | `store/hausplanerStore.ts:89` |
| Ehrliche Zustände (v1) | vorhanden und wiederverwendbar: `StudioZustand` + `ZustandBadge` | `app/studioUi.tsx:28`, `:39` |
| Testumgebung | `node:test` mit `--experimental-strip-types`, **kein jsdom, kein testing-library** | `package.json:10` |

**Zwei Konsequenzen, die diesen Auftrag formen:**

1. **§19 „Kontext-Options-Leiste" ist keine Neuerfindung, sondern ein Umzug.** Die Optionen sind da,
   sie sitzen nur am falschen Ort (Kopfzeile, vermischt mit Undo/Redo/Speichern) und sind an genau
   zwei Werkzeuge hartgeschrieben. v2.1 gibt ihnen eine eigene Zeile und einen Ort, an dem weitere
   andocken können.
2. **Es kann keine Render-Tests geben.** Alles, was bewiesen werden soll, muss als **reine Funktion**
   außerhalb des JSX liegen. Deshalb schreibt dieser Auftrag vier kleine, testbare Module vor und
   lässt das JSX bewusst dünn.

---

## 1. Ziel & Entscheidung

**Nach v2 hat das Dashboard alle Flächen, die das festgelegte Design vorsieht — auch dort, wo dahinter
noch keine Funktion liegt.** Das ist Yamas stehende Regel, wörtlich umgesetzt:

> *„wir machen erst layout fertig auch wenn die funktion nicht programmiert sind bleiben ohne funktion da"*

Eine leere Fläche ist **nur dann zulässig**, wenn sie ihren Zustand ausspricht — mit **Text und Symbol,
nicht nur Farbe**. Dafür gibt es keine Neuerfindung: `ZustandBadge` aus v1 (`app/studioUi.tsx:39`) mit
den vier Zuständen `verfuegbar` · `voraussetzung` · `nur_ergebnis` · `in_entwicklung` wird
wiederverwendet. **Wer eine neue leere Fläche ohne Badge baut, hat den Auftrag nicht erfüllt.**

**Die härteste Entscheidung dieses Auftrags: v2 ändert den Store nicht.** Kein neues Feld, kein neues
Command, kein Zod. Alles, was v2 zeigt, wird aus dem gelesen, was schon da ist. Das hält v2 additiv,
hält `schema:hausplaner:check` ohne Regen grün und hält v2 **außerhalb** des Sperrbereichs von AUF-1.

---

## 2. Schnitt in zwei Batches (wie bei v1, weil es dort getragen hat)

- **Batch 1 = v2.1 Kontext-Options-Leiste + v2.2 Panel-Tabs** — beides in `HausplanerApp.tsx`, plus
  ein reines Modul.
- **Batch 2 = v2.3 Projektbrowser + v2.4 Prüfungscenter + v2.5 Command-Palette** — drei reine Module
  plus ihre Anbindung.

Batch 1 wird **berichtet und abgenommen**, bevor Batch 2 beginnt. Zwei Batches, zwei Berichte, zwei
Voten — kein Sammel-Commit über alles.

---

## 3. Nahtstellen Batch 1

### v2.1 — Kontext-Options-Leiste (§19 / UI-4)

- **Ort:** neue Zeile **direkt unter** der Bedien-Werkzeugleiste (die endet bei `:788`), **vor** dem
  Canvas-`<div>` (`:793`). Volle Breite, `flex: '0 0 auto'`, `borderBottom: 1px solid T.hair`,
  Hintergrund `T.surface2`. Höhe schlank halten (Padding `'5px 14px'`, Schriftgröße 12).
- **Bauform:** eine **lokale** Komponente `KontextOptionenLeiste` in derselben Datei, unmittelbar
  neben `OpBtn` — dem etablierten Muster dieser Datei folgend. **Keine neue Datei**, denn die
  Zerlegung des Monolithen ist v4 und braucht vorher Charakterisierungstests (Risiko R3).
- **Inhalt:** links das Label des aktiven Werkzeugs (`toolNach(activeToolId)?.label`), dann ein
  Trenner, dann die Optionen. Die Zuordnung Werkzeug → Optionen ist **ein einziger `switch`** über
  `activeToolId`. Bedingung und Steuerelement liegen damit im selben `case` und können nicht
  auseinanderlaufen — das ist der Grund für den `switch` und gegen eine Parallelliste.
- **Umzug, byte-treu:** die Fenstertyp/Türtyp-Auswahl aus `:655`–`:666` wandert **unverändert** in den
  `case 'fenster'` / `case 'tuer'`. Gleiche States (`fensterTypWahl`/`tuerTypWahl`), gleiche
  Optionslisten, gleiche `onChange`. Am Platzierungspfad (`:428`) ändert sich **nichts**. In der
  Kopfzeile bleibt an dieser Stelle **nichts** zurück.
- **`default`-Zweig:** Text *„Für dieses Werkzeug sind noch keine Optionen hinterlegt."* plus
  `<ZustandBadge zustand="in_entwicklung" />`. Genau das ist die ehrliche leere Fläche.
- **Erweiterungspunkt, nicht bauen:** der `switch` ist die Stelle, die in v5 durch einen Deskriptor
  aus der Registry ersetzt wird. Als Kommentar vermerken, **nicht** vorwegnehmen.

### v2.2 — Eigenschaftenpanel bekommt Tabs (§20 / UI-5-Fläche)

- **Neues Modul `app/dashboard/panelTabs.ts`** (reine Daten, keine Abhängigkeit auf React):
  ```ts
  export type PanelTabId = 'allgemein' | 'beziehungen' | 'pruefungen' | 'historie';
  export interface PanelTab { id: PanelTabId; label: string; zustand: StudioZustand; }
  export const PANEL_TABS: readonly PanelTab[] = [ … ];
  ```
  Zustände: `allgemein` → `verfuegbar`; `beziehungen` → `in_entwicklung`; `pruefungen` →
  `in_entwicklung` (wird in **Batch 2** auf `verfuegbar` gehoben); `historie` → `in_entwicklung`.
  Die vier Tabs sind damit **Daten**, nicht Markup — deshalb prüfbar.
- **Anbindung:** im Panel (`:1098`) unter der Überschrift „Eigenschaften" eine Tab-Leiste aus
  `PANEL_TABS`. Aktiver Tab als **lokaler** `useState<PanelTabId>('allgemein')`. Das ist bewusst
  **kein** Store-Feld: der Wert hat genau einen Leser, und ob Panelzustand in den UI-State gehört,
  ist eine v4-Frage (F1). Zweite Wahrheit entsteht dadurch nicht.
- **`allgemein` zeigt den heutigen Panelinhalt — unverändert.** Kein Verhalten des bestehenden Panels
  wird angefasst, auch nicht die Auge/Schloss-Zeile aus v1 (`:1100`–`:1112`).
- Die anderen drei Tabs zeigen je einen kurzen Satz, was dort einmal stehen wird, **plus** ihr
  `ZustandBadge`. Kein Blindtext, kein „keine Daten".
- **A11y:** `role="tablist"` / `role="tab"` / `aria-selected`, Pfeiltasten links/rechts wechseln den
  Tab, sichtbarer Fokus. Der aktive Tab ist **nicht nur farblich** markiert (Schriftschnitt +
  Unterstrich), WCAG 1.4.1.

---

## 4. Nahtstellen Batch 2

### v2.3 — Projektbrowser (§32 / UI-8)

- **Neues Modul `app/dashboard/projektBaum.ts`**, reine Funktion:
  `projektBaum(nodes, roofs, level) → { gruppe: string; eintraege: { id, label, typ }[] }[]`.
  Gruppiert nach Bauteiltyp (Wände · Öffnungen · Dächer · Treppen · Objekte · Zonen), Reihenfolge
  fest, leere Gruppen werden **weggelassen** (nicht als leere Kästen gerendert).
- **Ort:** als **dritter Abschnitt in der bestehenden 220-px-Schiene**, unter `FaehigkeitenNavi`
  (`:815`–`:818`), über der „Erweiterbar"-Fußzeile. **Keine neue Spalte** — damit bleibt die
  Flächenrechnung `:590` (`− 220 − 268`) unberührt. Ob der Browser später eine eigene Spalte bekommt,
  ist eine Willensfrage an Yama (§10) und blockiert nichts.
- **Funktion, weil sie geschenkt ist:** Klick auf einen Eintrag ruft `selectNodes([id])` — vorhanden
  (`store:89`). Der aktuell ausgewählte Eintrag wird hervorgehoben (Hintergrund **und** Schriftschnitt).
- **Leerzustand:** *„Noch keine Bauteile in diesem Geschoss."* mit `ZustandBadge zustand="voraussetzung"` —
  denn das ist die Wahrheit: es fehlt eine Voraussetzung, nicht eine Funktion.

### v2.4 — Prüfungscenter (§34 / UI-10)

- **Neues Modul `app/dashboard/befunde.ts`**, reine Funktion:
  `befundeAus(letzteAblehnung: string | null) → Befund[]` mit `Befund { id, text, schwere: 'hinweis' | 'fehler' }`.
  Ergebnis ist heute **0 oder 1 Eintrag** — mehr gibt der Store nicht her, und v2 ändert den Store nicht.
- **Anbindung:** Tab `pruefungen` im Panel; `PANEL_TABS`-Zustand für `pruefungen` wird in diesem Batch
  von `in_entwicklung` auf `verfuegbar` gehoben.
- **Leerzustand wörtlich:** *„Keine offenen Befunde."* — **nicht** „keine Daten". Mit
  `ZustandBadge zustand="verfuegbar"`.
- **Ehrlich benennen, was fehlt:** unter der Liste ein ruhiger Hinweis, dass derzeit nur die **zuletzt**
  abgelehnte Aktion geführt wird. Dass der Store `CommandAbgelehnt.grund` verwirft und keine Historie
  hält, ist ein **eigener Posten für v3** (Store-Änderung) — hier nur dokumentieren, nicht bauen.

### v2.5 — Command-Palette (§30 / UI-9)

- **Neues Modul `app/dashboard/palette.ts`**, reine Funktion:
  `palettenEintraege(kontext: AktivierungsKontext, filter: string) → PaletteEintrag[]` mit
  `{ id, label, shortcut?, enabled, grund: string | null }`. Quelle ist `alleTools()` aus der Registry;
  `enabled`/`grund` kommen **ausschließlich** aus `resolveToolState` — keine zweite Aktivierungslogik.
  Filter: `label` **und** `id`, ohne Groß-/Kleinschreibung; Reihenfolge bleibt Registry-Reihenfolge
  (aktivierbare zuerst, deaktivierte darunter, jeweils Registry-Reihenfolge erhalten).
- **Öffnen:** `Strg+K` / `⌘+K`, in denselben Tastatur-Handler, der bereits die Werkzeug-Shortcuts führt
  (`:538`). **Vor dem Bau prüfen und im Bericht belegen**, dass `k` keine Registry-Shortcut-Kollision
  hat (`shortcutKollisionen()` steht dafür bereit) und `Strg+S` unberührt bleibt.
- **Bedienung:** Overlay mittig, Filterfeld mit Autofokus, ↑/↓ bewegt, Enter aktiviert, **Esc schließt**.
  Deaktivierte Einträge sind nicht auslösbar und tragen ihren **Grund als sichtbaren Text**, nicht nur
  als ausgegraute Farbe.
- **A11y in v2:** `role="dialog"`, `aria-modal="true"`, `aria-label`, Autofokus, Esc. Ein vollständiger
  Fokus-Käfig ist **v6** — hier nicht bauen, aber als Kommentar vermerken.

---

## 5. Was ausdrücklich NICHT zu diesem Auftrag gehört

- **Keine Store-Änderung.** Kein Feld, kein Command, kein Zod, kein Schema-Regen. Wird der Store
  angefasst, ist der Auftrag verlassen.
- **`app/tools/toolPresentation.ts` wird nicht angefasst** — weder gelesen noch geschrieben. Die Zonen
  sind **Welle A2 / v5** und liegen hinter **AUF-1**. Genau deshalb ist v2 nicht gesperrt.
- **Keine Zerlegung des Monolithen.** `HausplanerApp.tsx` wächst in v2; das ist bewusst. Die Zerlegung
  ist v4 und beginnt mit Charakterisierungstests (R3).
- **Keine Mehrfachauswahl, kein Rubber-Band, keine gemeinsamen Werte** — das ist v3.
- **`faehigkeiten.ts` / `FaehigkeitenNavi.tsx` bleiben unverändert.**
- **Keine neue Farbe.** Jede Farbe kommt aus `T` (`studioDaten.ts`). **Kein roher Hex, kein `rgb()`**
  in `app/*` — Posten T1 ist mit 0 rohen Werten erfüllt und bleibt es.
- **Kein Beifang** in `domain/*`, `geometry/*`, `renderers/*`, PHP.

---

## 6. Kantenliste

1. **Kein Geschoss / keine Szene:** `scene === null` oder `level === null` — Optionsleiste,
   Projektbrowser und Panel dürfen nicht werfen; sie zeigen ihren Leerzustand.
2. **Aktives Werkzeug ohne Registry-Eintrag:** `toolNach(activeToolId)` liefert `undefined` — die
   Optionsleiste zeigt den `default`-Zweig, kein Absturz, kein leeres Label.
3. **Werkzeugwechsel mitten in einem Zug:** `wandStart`/`treppeStart` gesetzt und das Werkzeug wechselt
   — das bestehende Verhalten (`:806` setzt beide auf `null`) bleibt **unverändert**.
4. **Aktives Werkzeug fällt aus dem Kontext:** der Rückfall auf `'auswahl'` (`:176`–`:181`) greift
   weiter; die Optionsleiste folgt ohne Zwischenzustand.
5. **Auswahl verschwindet, während ein Tab offen ist:** `beziehungen`/`historie` bleiben stehen und
   zeigen ihren Zustand; kein automatischer Tab-Sprung.
6. **Projektbrowser mit vielen Knoten:** bei > 200 Einträgen je Gruppe nicht ungebremst rendern —
   Gruppe zeigt Kopf + Anzahl und ist zusammengeklappt. Kein virtuelles Scrollen bauen (v6).
7. **Palette bei leerem Filter:** zeigt alle Einträge; bei Filter ohne Treffer *„Kein Werkzeug passt
   zu dieser Eingabe."* — kein leerer Kasten.
8. **Palette und Tastatur-Shortcuts gleichzeitig:** solange die Palette offen ist, dürfen die
   Werkzeug-Shortcuts **nicht** durchschlagen (sonst tippt man im Filter und wechselt das Werkzeug).
9. **`letzteAblehnung` wechselt schnell:** die Befundliste zeigt immer den aktuellen Wert; kein
   Aufsummieren, weil der Store keine Historie hält.
10. **`imStudio`-Modus:** die neue Zeile darf die Studio-Shell nicht überlaufen (`height: '100%'`,
    `:643`).

---

## 7. Abnahmekriterien (prüfbare Aussagen, keine Eindrücke)

**Batch 1**
1. `npm run tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` · `build:hausplaner` — alle
   **Exit 0**. Der Schema-Check ist grün **ohne** Regen (kein Zod berührt).
2. Testzahl **vorher und nachher** genannt; nachher ≥ vorher, kein einziger Test wandert von grün nach rot.
3. `app/dashboard/panelTabs.ts` existiert, exportiert genau **vier** Tabs in der Reihenfolge
   `allgemein · beziehungen · pruefungen · historie`; ein Test belegt Anzahl, Reihenfolge und dass
   **jeder** Tab einen `zustand` aus `StudioZustand` trägt.
4. `git diff` zeigt in `app/tools/*`, `store/*`, `domain/*`, `geometry/*`, `renderers/*` **keine
   einzige Zeile**.
5. Die Fenstertyp/Türtyp-Auswahl ist aus der Kopfzeile **verschwunden** und in der neuen Zeile
   **vorhanden** — mit denselben Optionswerten. Beleg: `git diff` beider Stellen.
6. Grep über `resources/planner/hausplaner/app/` nach `#[0-9a-fA-F]{3,6}` und `rgb(` liefert
   außerhalb von `studioDaten.ts` **null** Treffer.

**Batch 2**
7. Gates erneut alle vier **Exit 0**.
8. Je ein Test für `projektBaum`, `befunde`, `palette`, mindestens:
   - `projektBaum`: leere Szene → `[]`; gemischte Knoten → erwartete Gruppen in fester Reihenfolge;
     leere Gruppen fehlen.
   - `befunde`: `null` → `[]`; Meldung → genau **ein** Befund mit dem unveränderten Text.
   - `palette`: Anzahl = `alleTools().length`; jeder deaktivierte Eintrag hat `grund !== null`;
     Filter trifft `label` **und** `id`; aktivierbare stehen vor deaktivierten.
9. **Gegen-Beweis, den der Generator selbst führt und berichtet:** in `palette.ts` die
   `resolveToolState`-Abfrage durch ein hart gesetztes `enabled: true` ersetzen — mindestens ein Test
   **muss** rot werden. Wird er es nicht, deckt der Test die Aktivierung nicht ab. Danach zurückbauen,
   `git diff` muss leer sein.
10. `PANEL_TABS['pruefungen'].zustand === 'verfuegbar'` und der Leerzustand lautet wörtlich
    *„Keine offenen Befunde."*
11. Der Beleg, dass `Strg/⌘+K` kollisionsfrei ist (`shortcutKollisionen()`-Ausgabe im Bericht) und
    `Strg+S` weiterhin speichert.
12. **Jede** neu entstandene leere Fläche trägt ein `ZustandBadge`. Der Bericht listet sie einzeln
    auf: Fläche → Zustand → Text.

---

## 8. Guardrails

- **Staging nur nach Pfad.** `git commit -m "…" -- <pfade>`. **Nie `-A`, nie `.`** — am 25.07. hat ein
  Commit ohne Pfadangabe sechs fremde Dateien mitgenommen.
- **Zwei Commits, nicht einer.** Batch 1 und Batch 2 getrennt, jeder mit eigenem Bericht in
  `docs/handoff-status.md`.
- **`.git/*.lock`** niemals mit `rm` entfernen — nur `mv` nach `.git/_locks_beiseite/<datum>/`.
- **Kein Push zu `upstream`** (`raminsadid2021/nuri-head.git` = fremdes Konto). Push nur `fork` +
  `backup-private`, ausschließlich über `push-integration-sicher.command`, nie `--force`.
- **Kein `main`-Merge, kein Deploy.** Tor 2 gehört Yama; der Live-Stand hat ~3000 Kunden.
- **Der Generator meldet „umgesetzt", nie „abgenommen".** Kein Selbst-Abnehmen.
- **Taucht Nötiges außerhalb des Umfangs auf: zurückgeben, nicht mitbauen.**

---

## 9. Bericht

Je Batch ein Block in `docs/handoff-status.md`:
`## ⇒ GENERATOR-BERICHT — Dashboard v2 Batch <n> UMGESETZT` mit den vier Exit-Codes, der Testzahl
vorher/nachher, der Dateiliste des Commits, der Aufzählung aus Kriterium 12 (Fläche → Zustand → Text),
dem Gegen-Beweis aus Kriterium 9 und dem, was zurückgegeben wird. Danach Tafelstatus auf
`BERICHTET — wartet auf Evaluator`.

---

## 10. Offene Willensfragen an Yama — sie blockieren diesen Auftrag **nicht**

Der Auftrag ist so geschnitten, dass jede dieser Fragen später ohne Rückbau beantwortet werden kann.
Bis dahin gilt die jeweils genannte, bewusst konservative Vorentscheidung.

1. **Projektbrowser links in der 220-px-Schiene oder als eigene Spalte?** Vorentscheidung: in der
   bestehenden Schiene — dann bleibt die Flächenrechnung unberührt.
2. **Command-Palette auf `Strg/⌘+K`?** Vorentscheidung: ja, weil branchenüblich und kollisionsfrei.
3. **Welche vier Panel-Tabs bleiben dauerhaft?** Vorentscheidung: `Allgemein · Beziehungen · Prüfungen ·
   Historie` nach §20. `Beziehungen` und `Historie` sind die unsichersten — sie stehen in v2 ohnehin
   nur als Fläche und lassen sich streichen, ohne dass etwas nachbricht.
