# Dashboard — Versionsfahrplan v1 … v6

**Rolle:** Planner · **Stand:** 25.07.2026, HEAD `7f1ecd6` auf `auto/hausplaner-integration`
**Anlass:** Yamas Anordnung — *„wir haben dashboard design fest gelegt sollst als erstes fertig gestellt werden v1 usw"*
**Geltung:** Dieser Fahrplan steht **vor** dem Layout-Fahrplan L1–L7 (`docs/fahrplan-frontend-layout-hausplaner.md`).
L1–L7 wird nicht verworfen, sondern in §4 in die Versionen eingehängt.

---

## 0. Welches „Dashboard-Design" ist gemeint — und woran ich das festgemacht habe

Gemessen, nicht angenommen. Es gibt im Repo genau **zwei** Dinge, die „Dashboard" heißen:

| Kandidat | Beleg | Urteil |
|---|---|---|
| **Blade-CRM-Dashboards** (12 Übersichten, Rollen-Konzept) | `docs/dashboard-konzept.md`, ein Commit `73b31df`, ausdrücklich *read-only Analyse*; §4.5 listet die Entscheidungen als **noch offen** | **nicht gemeint** — dort ist nichts „festgelegt", und der Strang ist bewusst getrennt (`docs/ux-frontend-audit.md`) |
| **Werkzeug-Dashboard des Hausplaners** | `docs/planner/tool-dashboard-current-state.md` (229 Z., 23 kB), Ledger-Kette Strang 5 „UI-Strang: Werkzeug-Dashboard", Branch `auto/hausplaner-dashboard-v1`, Evaluator-Votum, Merge `c4129ce` | **das ist es** — hier ist das Design **festgelegt** (§6 Architektur, §8 Slices S0/UI-1…UI-12, §9 Abnahmekriterien) und es läuft bereits **in Versionen** |

Das Design ist also nicht neu zu erfinden. Es steht in `tool-dashboard-current-state.md` §6/§8/§9.
Dieser Fahrplan tut nur eines: er **schneidet die festgelegten Slices auf Versionen** und ordnet sie nach
Yamas stehender Regel — *„wir machen erst layout fertig auch wenn die funktion nicht programmiert sind
bleiben ohne funktion da"*.

---

## 1. Was v1 ist — und dass es fertig ist (gemessen)

**v1 = abgeschlossen und abgenommen.** Belegkette, jede Zeile aus `git log` bzw. dem Ledger:

| Schritt | Commit | Inhalt |
|---|---|---|
| Batch 1 | `4cde0be` | Icon-Tooltips · Undo/Redo-Icons · Geschoss-Stepper |
| Batch 2 | `a1215a3` | vier **ehrliche Zustände** (`verfuegbar` / `voraussetzung` / `nur_ergebnis` / `in_entwicklung`) · Auge/Schloss · Speicher-Bestätigung |
| Generator-Bericht | `e4693f1` | Gate 684/684 |
| Evaluator-Votum | `ec517e9` | **FREIGABE mit EINER Auflage** (Kontrast `warnInk` 4.36 → AA-Text verfehlt) |
| Auflage gelöst | `050f55f` | `warnInk #a5620f → #9c5c0d` → 4.81 = **AA bestanden** |
| Re-Abnahme | `9bcc9c3` | **unbedingte FREIGABE** |
| Merge | `c4129ce` | `dashboard-v1` → `auto/hausplaner-integration` (Fast-Forward) |

**Das Muster aus v1 ist ab jetzt verbindlich für jede weitere Version:** jeder Zustand trägt
**Farbe UND Text UND Punkt UND `title`/`aria`**, null rohe Hex-Werte, jede verlustbehaftete Aktion ist
durch eine bewusste Bestätigung geschützt. Genau dieses Muster macht Yamas Layout-Regel überhaupt
tragfähig: eine Fläche ohne Funktion darf dastehen — aber sie muss **`in_entwicklung` ehrlich sagen**,
nicht so tun als könnte sie etwas.

---

## 2. Was heute wirklich steht (Messung am HEAD `7f1ecd6`, nicht aus dem Papier)

Zählung über `resources/planner/hausplaner/`:

| Slice aus §8 | Zustand | Beleg |
|---|---|---|
| **S0** Schema-Blocker | ✅ geschlossen | `schema:hausplaner:check` grün seit der v9-Welle |
| **UI-1** Ist-Inventar | ✅ | `docs/planner/tool-dashboard-current-state.md` |
| **UI-2** UI-State + Registry + Activation | ✅ | `app/state/uiState.ts` · `app/tools/toolRegistry.ts` · `app/tools/activation.ts` + Tests |
| **UI-3** Toolbar datengetrieben | **halb** | Werkzeugleiste liest Registry+Activation (`HausplanerApp.tsx:797`) — aber die **Zerlegung** des Monolithen fehlt: die Datei hat **1.431 Zeilen** |
| **UI-4** Kontext-Options-Leiste (§19) | ❌ **0 Dateien** | kein `optionsSchema`-Konsument im ganzen Bundle |
| **UI-5** Selektion + Panel-Tabs + Mehrfach (§7/§20) | ❌ **0 Dateien** | Panel ist per-Typ und nur `length===1` (`HausplanerApp.tsx:1098`) |
| **UI-6** Arbeitsbereiche (§9) | **Keim** | `activeWorkspace` existiert, hat aber real **genau einen** Wert (`architektur`) |
| **UI-7** Command-Zustandsmaschine (§25) | ❌ **0 Dateien** | Zeichnen läuft über lokale Punkt-States |
| **UI-8** Projektbrowser (§32) | ❌ **0 Dateien** | kein Szenen-Baum |
| **UI-8b** Sichtbarkeit/Sperre (§33) | **halb** | Commands da (`SET_NODES_SICHTBAR/GESPERRT`, v1 Batch 2) — **kein Panel** |
| **UI-9** Command-Palette (§30) | ✅ **gebaut** — `app/dashboard/palette.ts` 191 Z., 2 Testdateien, 3 Konsumenten (`ableitungen.ts`, `HausplanerApp.tsx`, `FussUndUeberlagerungen.tsx`) | nachgemessen 01.08.2026, Befund PB-035 |
| **UI-10** Prüfungscenter (§34) | **halb** | Guards da (`CommandAbgelehnt` in `applyCommand.ts`) — **kein Befund-Panel** |
| **UI-11/12** A11y/Responsive/Performance | offen | — |

**Nebenbefund, der bleibt:** die Zonen-Kuratierung aus Welle A1 (`toolPresentation.ts`, 63 Regeln,
4 Zonen) wird bis heute an **genau einer** Stelle gelesen — `tools/faehigkeiten.ts:96` — und **nicht**
von der Werkzeugleiste. Die Leiste liest die Registry, nicht die Zonen. Das ist kein Fehler, aber es ist
die Lücke, die Welle A2 schließen sollte. **Sie liegt hinter AUF-1** (Evaluator-Votum steht aus).

**Daraus folgt das Wichtigste an diesem Fahrplan:** v2 fasst `toolPresentation.ts` **nicht** an und ist
damit **nicht** von AUF-1 blockiert. Das Dashboard kann sofort weiterlaufen, während der Evaluator die
A1-Abnahme wiederholt.

---

## 3. Die Versionen

### **v2 — „Der Rahmen steht"** (Layout zuerst, Funktion wo sie geschenkt ist) — **JETZT**

Ziel in einem Satz: **Nach v2 hat das Dashboard alle Flächen, die das festgelegte Design vorsieht** —
auch dort, wo dahinter noch keine Funktion liegt. Das ist Yamas Regel, wörtlich umgesetzt.

- **v2.1 Kontext-Options-Leiste (§19/UI-4)** — schmale Leiste unter der Werkzeugleiste, zeigt die Optionen
  des **aktiven** Werkzeugs. Werkzeuge ohne Optionen zeigen ehrlich „keine Optionen für dieses Werkzeug".
  Quelle ist ausschließlich `usePlannerUiStore.activeToolId` + Registry — **kein zweiter Werkzeugzustand**.
- **v2.2 Eigenschaftenpanel bekommt Tabs (§20/UI-5-Fläche)** — Tab-Leiste `Allgemein · Beziehungen ·
  Prüfungen · Historie`. Heute trägt nur `Allgemein` Inhalt (das bestehende Panel, unverändert); die drei
  anderen stehen als Fläche mit `in_entwicklung`. Kein Verhalten des heutigen Panels ändert sich.
- **v2.3 Projektbrowser (§32/UI-8)** — linke Spalte, Baum Geschoss → Bauteile, gelesen aus dem Modell-Store.
  Klick wählt aus (`selectNodes`) — das ist **echte** Funktion und kostet nichts, weil der Store sie hergibt.
- **v2.4 Prüfungscenter (§34/UI-10)** — Panel-Tab „Prüfungen", gespeist aus den abgelehnten Commands
  (`CommandAbgelehnt`). Leerzustand sagt „keine offenen Befunde", nicht „keine Daten".
- **v2.5 Command-Palette (§30/UI-9)** — Tastenkürzel öffnet eine Liste aller Registry-Werkzeuge/-Aktionen;
  ausführbar ist genau das, was die Activation-Engine erlaubt, mit **Grund** bei deaktiviert.

Schnitt: **Batch 1 = v2.1 + v2.2** (beides in `HausplanerApp.tsx`), **Batch 2 = v2.3 + v2.4 + v2.5**.
Genau die Batch-Teilung, die bei v1 getragen hat.

### **v3 — „Auswahl, die trägt"** (§7/§20, UI-5 vollständig)
Rubber-Band, Typ-/Eigenschaftsfilter, Isolieren/Sperren über das neue Sichtbarkeits-Panel,
Mehrfachauswahl im Eigenschaftenpanel mit **gemeinsamen Werten** (3 Fenster gleichzeitig ändern).
Erst jetzt, weil das echte Funktion ist und die Flächen aus v2 schon dastehen.

### **v4 — „Ein Griff, ein Schritt zurück"** (§25/§37, UI-7 + Zerlegung)
`ActiveCommandState` (idle → awaiting-* → preview → valid/invalid → committing) ersetzt die lokalen
Zeichenpunkte; ein Ziehen = **eine** Undo-Transaktion. Dazu die Zerlegung des 1.431-Zeilen-Monolithen in
`<Toolbar/> <KontextLeiste/> <Eigenschaften/> <Projektbrowser/> <Statusleiste/> <Viewport2D/> <Viewport3D/>`.
Charakterisierungstests **vor** der Extraktion (Risiko R3 aus dem Design-Dokument).

### **v5 — „Arbeitsbereiche und die kuratierte Leiste"** (§9/UI-6 + Welle A2)
`activeWorkspace` bekommt echte Bereiche; die Werkzeugleiste liest die **Zonen** aus `toolPresentation.ts`
(fix/kontext/weitere/versteckt) statt die Registry roh; die 20 Fachplaner-Untermodule bekommen ihre Fläche
statt eines Toasts. **Vorbedingung: AUF-1 (A1-Abnahme) hat ein Votum.**

### **v6 — „Härtung und Abnahme"** (§35/§36/§37/§39, UI-11 + UI-12)
Responsive/Drawer, A11y-Tests (Kontrast **gerechnet**, Fokus, Tastatur, Status nie nur Farbe),
Performance-Budget (Werkzeugwechsel ohne Canvas-Vollrender, gemessen am Referenz-EFH),
Layout-Abnahmerunde in 1440 / 1024 / 375.

---

## 4. Wo L1–L7 bleibt

Der Layout-Fahrplan wird nicht doppelt gefahren — er wird eingehängt:

| aus L1–L7 | landet in |
|---|---|
| **L1** Werkzeugleiste liest Präsentationsschicht | **v5** (identisch mit Welle A2) |
| **L2/L3** Panel-Muster + 13 Engine-Panels | **v3** (braucht das Tab-Gerüst aus v2.2) |
| **L4** 20 Fachplaner-Untermodule bekommen Fläche | **v5** |
| **L5** Wizard-Status aus dem Modell | **v3** |
| **L6** echte Projekte + serverseitiges ConfiguratorPackage | nach v6 (eigener Strang, Backend) |
| **L7** Layout-Abnahmerunde | **v6** |

`docs/fahrplan-frontend-layout-hausplaner.md` bleibt als Inventur gültig (die gemessenen 3.343 Zeilen in
17 Dateien); seine Reihenfolge wird durch diesen Fahrplan ersetzt.

---

## 5. Was durchgehend gilt (aus dem festgelegten Design §9 + Bauordnung)

1. **Keine zweite Wahrheit.** UI-Zustand ≠ Modell-Zustand ≠ Fachrechnung. Kein zweiter `activeToolId`,
   kein zweiter Snapshot-/Hash-/Projektions-Mechanismus, keine zweite Farbquelle neben `T`.
2. **Additiv.** Kein Zod-Feld ohne `npm run schema:hausplaner` — sonst 422. v2 ändert kein Zod.
3. **Ehrlich statt hübsch.** Jede leere Fläche trägt einen der vier v1-Zustände, mit Text, nicht nur Farbe.
4. **Gates je Version:** `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` (Anzahl ≥ vorher)
   · `build:hausplaner`.
5. **Tor 1 (Fach-Freigabe) und Tor 2 (Merge/Deploy) bleiben bei Yama.** Der Generator meldet „umgesetzt",
   nie „abgenommen"; der Evaluator ist eine andere Instanz.

---

## 6. Was ich an Yama zurückgebe (blockiert nichts — v2 läuft weiter)

1. **Tastenkürzel der Command-Palette:** Vorschlag `Strg/⌘ + K`. Falls im CRM belegt, sag ein anderes.
2. **Projektbrowser links oder rechts?** Vorschlag **links** (Baum links, Eigenschaften rechts — das ist
   das Muster, das Profis aus CAD kennen). Rechts hättest du beides auf einer Seite.
3. **Panel-Tabs:** Vorschlag `Allgemein · Beziehungen · Prüfungen · Historie` aus §20. Wenn du einen Tab
   nicht willst, fliegt er raus, bevor er Fläche bekommt.
