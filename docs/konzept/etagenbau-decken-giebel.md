# Konzept — Etagenbau: Decken, Fußbodenaufbau, Giebel-/Dreieckwände (Planner, 2026-07-23)

**Auslöser (Yama):** Für den echten Etagenbau fehlen im 3D-Planer drei Dinge — **Decken** (ohne sie keine
zweite Etage), **Fußbodenaufbau**, und **Giebel-/Dreieckwände**, die sich **automatisch** aus dem darüber
liegenden Dach berechnen. Dringend.

## Gemessener Ist-Stand (read-only)
- **Geschosse existieren** im Modell: `Level { elevation, defaultWallHeight, floorThickness, sortOrder }`.
  Also ist Stapeln vorbereitet — **aber `floorThickness` ist nur eine Zahl**, es gibt **kein Decken-Bauteil**.
- **Wände sind rechteckige Extrusionen** einer einzigen `height` (`WallNode`). **Kein Ober­kanten-Profil,
  keine Giebelform.**
- **RoofNode kennt `traufhoeheMm` + Neigung/Azimut**, und `dachRoh` liefert die Dachflächen (SSOT). → Der
  Giebel kann daraus abgeleitet werden, ohne zweite Wahrheit.
- **Fußbodenaufbau-Rechenkern existiert bereits:** `geometry/wandaufbau.ts` → `berechneUWert(schichten,
  {bauteil:'boden'})` (Rsi 0.17 / Rse 0.00). Nur noch andocken.
- **Loch-Vorbild vorhanden:** `geometry/dachAusschnitt.ts` (maßhaltige Öffnungs-Polygone) → Vorlage für die
  **Deckenöffnung** (Treppendurchbruch).

## Feature A — Decke (Geschossdecke/Slab) — DER BLOCKER, zuerst
**Entscheidung:** additiver **`CeilingNode` (type 'ceiling')** je Level — ein waagrechtes Bauteil mit
`polygon` (Umriss, default aus `roomDetection`/Gebäude-Umriss des Levels), `schichten: Schicht[]`
(Fußbodenaufbau, s. B) und `oeffnungen?: Loch[]` (Treppendurchbruch, Vorbild `dachAusschnitt`).
- **Warum Node, nicht nur Level-Zahl:** die Decke trägt Aufbau (U-Wert/Dämmung), Öffnungen und den Umriss —
  das ist ein Bauteil, kein Skalar. `Level.floorThickness` bleibt als Default/Fallback bestehen.
- **Bezug Etage:** die Oberkante der Decke von Level N = Fußboden-Nullebene von Level N+1
  (`level[N+1].elevation = level[N].elevation + wandHöhe + deckenDicke`). Damit „baut" eine zweite Etage
  überhaupt erst auf etwas auf.
- **Erzeugen:** Werkzeug **„Decke"** (eigenes Icon) — ein Klick „Decke aus Grundriss" nimmt den
  Level-Umriss; danach editierbar. Treppen-Node stanzt automatisch eine Öffnung (Loch-Polygon).
- **3D:** Slab-Mesh aus `polygon` minus `oeffnungen`, Dicke = Σ Schichten (oder `floorThickness`), auf
  Oberkante-Wand des Levels. SSOT: die Decke ist die eine Quelle ihrer Geometrie.

## Feature B — Fußbodenaufbau (Schichten der Decke/des Bodens)
**Entscheidung:** der `CeilingNode.schichten` ist der Fußbodenaufbau; **Rechnung ruft die vorhandene Engine**
`berechneUWert(schichten, {bauteil:'boden'})` — **kein neuer Rechenkern** (Byte-Treue).
- **Panel** (wie Batch-1-Muster `EnginewerkzeugPanel`): Schichten (Estrich/Dämmung/Trittschall…) → `T.*`;
  Ergebnis: Gesamtdicke, R/U-Wert, Prüfungen. Die **Gesamtdicke speist die Deckendicke** und damit die
  Etagen-Elevation (B → A gekoppelt: eine Wahrheit für die Dicke).
- **Erd­geschoss-Boden** (gegen Erdreich) ist derselbe Node-Typ mit `bauteil:'boden'`; die Decke zwischen
  Etagen ebenso (ggf. `bauteil:'boden'` von oben betrachtet). Eine Bauart, ein Panel.

## Feature C — Giebel-/Dreieckwände (automatisch aus dem Dach)
**Entscheidung:** **Auto-Giebel im Renderer aus `dachRoh` (SSOT), kein Modell-Zwang.** Die Wand bleibt im
Modell `height` (= Traufhöhe); **beim Rendern wird ihre Oberkante an der Dach-Unterseite gekappt.**
- **Wie:** existiert ein Dach auf dem Level, fragt der Wand-Renderer entlang der Wandlinie (start→end) die
  Dachhöhe aus `dachRoh` ab und zieht die Wand bis dorthin hoch → **Giebel = Dreieck** (Satteldach),
  **Pentagon/Trapez** (Walm/Verschneidung). Nutzt `traufhoeheMm` als Basis, die Dachflächen als Oberkante.
- **Warum Render-Ableitung:** keine zweite Geometrie-Wahrheit, additiv, kein 422 — die Wand-Daten ändern
  sich nicht. „Dach drauf → Dreieck rechnet sich" ist damit **automatisch**.
- **Manuelle Dreieckwand** (Sonderfall ohne Dach): optionales `oberkanteProfil?: Punkt[]` am `WallNode`
  (additiv, **späterer** Slice) — erst bauen, wenn der Auto-Giebel steht.

## Reihenfolge (jeweils eigener P→G→E)
1. **Decke (A)** — der Blocker: `CeilingNode` additiv + Werkzeug/Icon + Slab-3D + Deckenöffnung (Treppe).
2. **Fußbodenaufbau (B)** — `schichten` an der Decke + Panel, das `wandaufbau('boden')` aufruft; Dicke → Etage.
3. **Auto-Giebel (C)** — Wand-Renderer kappt an `dachRoh`; Giebel-Tests (Satteldach → Dreieck, Walm → Pentagon).
4. (später) manuelles `oberkanteProfil` für Dreieckwände ohne Dach.

## Guardrails (durchgängig)
Additiv (optionale Felder → kein 422); **eine Wahrheit** (`dachRoh` fürs Dach, `wandaufbau` fürs Boden-U,
Decke = eine Geometrie-Quelle); Ports nur aufgerufen; Schema-Regen bei jedem Zod-Feld; nur `auto/`-Branch.
