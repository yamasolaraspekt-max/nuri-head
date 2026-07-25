# ⇒ PLANNER-ENTSCHEIDUNG: ID-Sprache der Werkzeuge

**Datum:** 25.07.2026 · **Posten:** AUF-20 · **Gemessen gegen** `8a250e8`
**Status:** entschieden vom Planner. Yama kann überstimmen — die Entscheidung folgt aber aus dem
Bestand, nicht aus Geschmack, deshalb wird sie hier getroffen statt weitergereicht.

---

## Die Messung, die es entscheidet

**Die Domäne ist bereits englisch — und zwar dort, wo es zählt: im persistierten Schema.**
`domain/scene-document-v2.schema.json` ist die eine Wahrheit, die der PHP-Validator liest. Sie kennt
ausschließlich englische Werte:

```
type          = wall | window | door | opening | object | zone | route | roof | ceiling
objectType    = radiator | heat_pump_indoor | heat_pump_outdoor | buffer_tank |
                hot_water_tank | battery | inverter | wallbox | furniture | sanitary | stair
zoneType      = room | underfloor_heating | pv_area | maintenance_area | sound_area | restricted_area
routeType     = heating_pipe | water_pipe | refrigerant_line | electrical_line | pv_dc_line | drainage
```

**Die deutschen Werkzeug-IDs stehen dort NICHT.** Gemessen: `wand · fenster · tuer · dach · decke ·
treppe · auswahl` → **je 0 Treffer** in `scene-document-v2.schema.json`.

Daraus folgt zweierlei:

1. **Ein Umbenennen der 9 Werkzeug-IDs berührt keine Bestandsdaten, keine Migration, kein 422.**
   Sie sind reine UI-Kennungen, nichts davon wird gespeichert. Die Dauerdirektive
   („ticket-Daten sind unantastbar") ist nicht berührt.
2. **Die deutschen IDs sind bereits heute die *zweite* Benennung desselben Sachverhalts.** Das
   Werkzeug `wand` erzeugt einen Knoten `type: 'wall'`; die Übersetzung passiert im Kopf des
   Lesenden. Genau das verbietet „eine Wahrheit je Sachverhalt".

## Entscheidung

**Werkzeug-IDs werden englisch — passend zum Schema und zum 110er-Paket.**

- Die **110 Paket-IDs bleiben unverändert.** Umbenannt werden die **9** Registry-IDs:
  `auswahl→select · wand→wall · fenster→window · tuer→door · dach→roof · decke→slab · treppe→stairs ·
  loeschen→delete · duplizieren→duplicate`.
- **Labels bleiben deutsch** (`label: 'Wand'`). Der Nutzer sieht unverändert Deutsch — geändert wird
  die Kennung, nicht die Sprache der Oberfläche.
- **Die Hausregel bleibt: Code spricht deutsch, Daten sprechen englisch.** Deutsche Datei- und
  Funktionsnamen (`wandGeometrie`, `dachVerschneidung`, 19 der 49 Geometrie-Dateien) bleiben, wie sie
  sind. Umbenannt werden ausschließlich **Datenwerte**, nicht Bezeichner.

## Zwei Konflikte zwischen Paket und Schema — hier gewinnt das Schema

Die Konflikt-Regel der Bauordnung lautet: *„Bei Struktur-Konflikt passt sich immer der neue Code dem
ticket-Schema an — nie umgekehrt."* Zwei Fälle:

| Werkzeug | Paket sagt | Schema sagt | **es gilt** |
|---|---|---|---|
| Decke | `slab` | `type: 'ceiling'` | **`ceiling`** — die Wahrheit steht im Schema |
| Treppe | `stairs` | `objectType: 'stair'` | **`stair`** — Einzahl, wie im Schema |

Der Adapter bildet die beiden Paket-IDs auf die Schema-Werte ab. **Kein Schema wird angefasst, kein
Zod regeneriert, kein 422 riskiert.**

## Umfang, gemessen — damit niemand „mal eben" sagt

210 Treffer der neun IDs in rund 30 Dateien (`wand` 30 · `fenster` 35 · `tuer` 26 · `dach` 13 ·
`decke` 11 · `treppe` 35 · `auswahl` 38 · `loeschen` 14 · `duplizieren` 8). Mechanisch, aber nicht
trivial — betroffen sind Registry, Aktivierung, Zonen-Kuratierung, Commands, Fixtures und Tests.
**Das ist ein eigener Auftrag mit eigener Abnahme, kein Beifang von AUF-21.**

## Folge für die Tafel

- **AUF-20** → erledigt durch diese Entscheidung.
- **AUF-21** (Paket einsortieren) → **entsperrt**, aber neu geschnitten:
  - **I1** — die 110 SVGs ablegen, Sprite und Galerie unter `docs/`. Rein additiv, kein Code.
  - **I2** — Adapter Paket→`ToolDefinition`, neuer Fach-Katalog, die 47 DTP-Reste belegt stillgelegt.
  - **I3** — `canPin`/`priority` in die Zonen-Kuratierung (schließt „angeheftet").
- **AUF-24** neu — die 9 Registry-IDs umbenennen. **Vor I2**, sonst baut I2 den Adapter gegen eine
  Benennung, die gleich danach wechselt.
