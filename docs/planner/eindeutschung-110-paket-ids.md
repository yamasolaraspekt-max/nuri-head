# ⇒ COWORK — Eindeutschung der 110 Paket-Werkzeug-IDs (führende Tabelle für Generator I2)

**Angelegt:** 25.07.2026 · **Rolle:** Cowork (misst/plant) · **Status:** **führende Wahrheit** — von Yama zum Committen freigegeben.
Reine Datenarbeit in `docs/`, **kein Code angefasst**.
**Gemessen/abgeleitet aus:** `docs/planner/tool-registry-paket.json` (110 Einträge, deutsche Labels + Kategorien belegt), `docs/planner/entscheidung-id-sprache-werkzeuge.md` (Yama-Überstimmung „alles auf Deutsch“), `domain/scene-document-v2.schema.json` (Schutzwerte).

> **Duplikat-Auflösung (Yama, 25.07.):** Zwei Cowork-Instanzen bauten dieselbe Tabelle. **Diese hier ist die führende**; die parallele `docs/planner/werkzeug-namen-deutsch.md` wurde stillgelegt (`058b652`). Grund ist sachlich, nicht Reihenfolge: diese Fassung markiert die **16 schema-gebundenen IDs einzeln** mit exaktem Schutzwert (inkl. `slab→ceiling`, `stairs→stair`) — der Unterschied zwischen „funktioniert“ und **422 beim Speichern**. Lehre: „ein Posten, ein Strang“ gilt auch innerhalb von Cowork, auch bei reiner `docs/`-Arbeit.

## Wozu

Yama hat entschieden: **alles, was der Mensch sieht oder anfasst, ist deutsch** — Labels, Werkzeug-IDs, Icon-Dateinamen, Kategorien, Hilfetexte. Das Paket liefert schon deutsche **Labels** und **Kategorien**, aber die **IDs sind noch englisch** (`wall`, `window`, `u-value`…). Diese Tabelle liefert die deutsche **ID** je Werkzeug, damit der Generator sie bei **I2** (Adapter Paket→`ToolDefinition`) **übernimmt statt sie zu erfinden**.

**Konvention** (aus den 9 bestehenden Registry-IDs abgeleitet: `tuer`, `loeschen`): ASCII, klein, Umlaute `ä/ö/ü→ae/oe/ue`, `ß→ss`; Mehrwortbegriffe mit Bindestrich. Der erste Begriff vor „/“ bestimmt die ID.

## ⛔ Die harte Grenze — 16 IDs sind schema-gebunden (DAUERDIREKTIVE)

Die Eindeutschung betrifft **UI-ID · Icon-Dateiname · Label**. Sie betrifft **NICHT** die im Szenendokument **gespeicherten Werte** (`type`, `objectType`, `zoneType`, `routeType`). Diese stehen in `scene-document-v2.schema.json`, werden vom PHP-Validator gelesen und liegen in gespeicherten Szenen — sie umzubenennen wäre eine **Datenmigration an Bestandsdaten** (eigener Posten) und erzeugt sonst 422. **Der Adapter bildet die deutsche UI-ID auf den englischen Schutzwert ab.** Die 16 betroffenen Zeilen sind unten mit ⛔ markiert. Sonderfall: Paket `slab`→Schema **`ceiling`**, Paket `stairs`→Schema **`stair`** (Schema gewinnt).

## Entschieden von Yama, 25.07. (vormals Cowork-Vorschläge, jetzt ratifiziert)

| ID | Entscheidung | Label | Begründung (Yama) |
|---|---|---|---|
| `pan` | **`hand`** | Hand | „verschieben“ ist mit `move` belegt |
| `wizard` | **`assistent`** | Assistent | halb englisch bereinigt |
| `command-palette` | **`befehlspalette`** | Befehlspalette | englisch bereinigt |
| `orbit` | **`umkreisen`** | Umkreisen | englisch bereinigt |
| `elevation` | **`aufriss`** | Ansicht / Aufriss | Fassade ist `facade`; ein Aufriss ist eine **Projektionsart**, keine Fassade |
| `brick` | **`klinker`** | Klinker-Verband | Kategorie Fassade ⇒ Klinker = Vorsatzschale; **Mauerwerk** (tragend) gehört zu `wall` |
| `beam` | **`unterzug`** | Unterzug | steht neben `column`/Stütze; horizontal tragend zwischen Stützen = **Unterzug** (Hochbau); Balken ist der Zimmerei-Begriff mit eigenen Werkzeugen |

`pdf` bleibt Akronym `pdf` (keine Eindeutschung eines Kürzels).

## Die vollständige Tabelle (110)

Legende: **📌** = `canPin` im Paket · ⛔ = schema-gebunden (Wert bleibt englisch, Adapter mappt) · *(9)* = deckt eine der 9 bestehenden Registry-IDs (Konvergenz) · **★** = von Yama entschieden (siehe oben).

| # | Kategorie | engl. Paket-ID | dt. Label | **dt. ID** | Schema-Schutzwert |
|---|---|---|---|---|---|
| 1 | Auswahl | `select` | 📌 Auswahl | `auswahl` *(9)* |  |
| 2 | Auswahl | `direct-select` | 📌 Direktauswahl | `direktauswahl` |  |
| 3 | Auswahl | `box-select` | 📌 Rechteckauswahl | `rechteckauswahl` |  |
| 4 | Auswahl | `lasso-select` | 📌 Lassoauswahl | `lassoauswahl` |  |
| 5 | Bearbeiten | `move` | 📌 Verschieben | `verschieben` |  |
| 6 | Bearbeiten | `rotate` | 📌 Drehen | `drehen` |  |
| 7 | Bearbeiten | `scale` | 📌 Skalieren | `skalieren` |  |
| 8 | Bearbeiten | `mirror-horizontal` | 📌 Horizontal spiegeln | `horizontal-spiegeln` |  |
| 9 | Bearbeiten | `mirror-vertical` | 📌 Vertikal spiegeln | `vertikal-spiegeln` |  |
| 10 | Bearbeiten | `copy` | 📌 Kopieren | `kopieren` |  |
| 11 | Bearbeiten | `duplicate` | 📌 Duplizieren | `duplizieren` *(9)* |  |
| 12 | Bearbeiten | `delete` | 📌 Löschen | `loeschen` *(9)* |  |
| 13 | Bearbeiten | `lock` | 📌 Sperren | `sperren` |  |
| 14 | Bearbeiten | `unlock` | 📌 Entsperren | `entsperren` |  |
| 15 | Bearbeiten | `show` | 📌 Einblenden | `einblenden` |  |
| 16 | Bearbeiten | `hide` | 📌 Ausblenden | `ausblenden` |  |
| 17 | Bearbeiten | `group` | 📌 Gruppieren | `gruppieren` |  |
| 18 | Bearbeiten | `align` | 📌 Ausrichten | `ausrichten` |  |
| 19 | Bearbeiten | `distribute` | 📌 Verteilen | `verteilen` |  |
| 20 | Zeichnen | `line` | 📌 Linie | `linie` |  |
| 21 | Zeichnen | `polyline` | 📌 Polylinie | `polylinie` |  |
| 22 | Zeichnen | `rectangle` | 📌 Rechteck | `rechteck` |  |
| 23 | Zeichnen | `polygon` | 📌 Polygon | `polygon` |  |
| 24 | Zeichnen | `circle` | 📌 Kreis | `kreis` |  |
| 25 | Zeichnen | `arc` | 📌 Bogen | `bogen` |  |
| 26 | CAD | `trim` | 📌 Trimmen | `trimmen` |  |
| 27 | CAD | `extend` | 📌 Verlängern | `verlaengern` |  |
| 28 | CAD | `offset` | 📌 Versatz | `versatz` |  |
| 29 | CAD | `split` | 📌 Teilen | `teilen` |  |
| 30 | CAD | `join` | 📌 Verbinden | `verbinden` |  |
| 31 | Architektur | `wall` | 📌 Wand | `wand` *(9)* | ⛔ `type: wall` |
| 32 | Architektur | `room` | 📌 Raum | `raum` | ⛔ `zoneType: room` |
| 33 | Architektur | `door` | 📌 Tür | `tuer` *(9)* | ⛔ `type: door` |
| 34 | Architektur | `window` | 📌 Fenster | `fenster` *(9)* | ⛔ `type: window` |
| 35 | Architektur | `stairs` | 📌 Treppe | `treppe` *(9)* | ⛔ `objectType: stair` |
| 36 | Architektur | `roof` | 📌 Dach | `dach` *(9)* | ⛔ `type: roof` |
| 37 | Architektur | `dormer` | 📌 Gaube | `gaube` |  |
| 38 | Architektur | `roof-window` | 📌 Dachfenster | `dachfenster` |  |
| 39 | Architektur | `column` | 📌 Stütze | `stuetze` |  |
| 40 | Architektur | `beam` | 📌 Unterzug / Träger | `unterzug`★ |  |
| 41 | Architektur | `opening` | 📌 Öffnung | `ffnung` | ⛔ `type: opening` |
| 42 | Architektur | `floor` | 📌 Boden | `boden` |  |
| 43 | Architektur | `slab` | 📌 Decke / Bodenplatte | `decke` *(9)* | ⛔ `type: ceiling` |
| 44 | Architektur | `section` | 📌 Schnitt | `schnitt` |  |
| 45 | Architektur | `elevation` | 📌 Ansicht / Aufriss | `aufriss`★ |  |
| 46 | Ansicht | `zoom-in` | 📌 Vergrößern | `vergroessern` |  |
| 47 | Ansicht | `zoom-out` | 📌 Verkleinern | `verkleinern` |  |
| 48 | Ansicht | `fit-view` | 📌 Alles anzeigen | `alles-anzeigen` |  |
| 49 | Ansicht | `pan` | 📌 Hand | `hand`★ |  |
| 50 | Ansicht | `orbit` | 📌 Umkreisen | `umkreisen`★ |  |
| 51 | Ansicht | `grid` | 📌 Raster | `raster` |  |
| 52 | Ansicht | `snap` | 📌 Fang | `fang` |  |
| 53 | Messen | `measure-distance` | 📌 Distanz messen | `distanz-messen` |  |
| 54 | Messen | `dimension` | 📌 Bemaßen | `bemassen` |  |
| 55 | Messen | `measure-angle` | 📌 Winkel messen | `winkel-messen` |  |
| 56 | Messen | `measure-area` | 📌 Fläche messen | `flaeche-messen` |  |
| 57 | Messen | `measure-volume` | 📌 Volumen messen | `volumen-messen` |  |
| 58 | Import | `import-file` | 📌 Datei importieren | `datei-importieren` |  |
| 59 | Import | `import-image` | 📌 Bild importieren | `bild-importieren` |  |
| 60 | Import | `calibrate` | 📌 Kalibrieren | `kalibrieren` |  |
| 61 | Import | `crop` | 📌 Beschneiden | `beschneiden` |  |
| 62 | Import | `set-north` | 📌 Nordrichtung setzen | `nordrichtung-setzen` |  |
| 63 | Import | `recognize` | 📌 Grundriss erkennen | `grundriss-erkennen` |  |
| 64 | Import | `ai-assistant` | 📌 KI-Assistent | `ki-assistent` |  |
| 65 | Import | `approve-detection` | 📌 Erkennung bestätigen | `erkennung-bestaetigen` |  |
| 66 | Material | `material` | 📌 Material zuweisen | `material-zuweisen` |  |
| 67 | Material | `texture` | 📌 Textur / PBR | `textur` |  |
| 68 | Material | `paint` | 📌 Material aufnehmen | `material-aufnehmen` |  |
| 69 | Fassade | `facade` | 📌 Fassadensystem | `fassadensystem` |  |
| 70 | Fassade | `brick` | 📌 Klinker / Verband | `klinker`★ |  |
| 71 | Bauphysik | `insulation` | 📌 Dämmung | `daemmung` |  |
| 72 | Bauphysik | `u-value` | 📌 U-Wert | `u-wert` |  |
| 73 | Bauphysik | `thermal-envelope` | 📌 Thermische Hülle | `thermische-huelle` |  |
| 74 | Bauphysik | `ventilation` | 📌 Lüftung | `lueftung` |  |
| 75 | Heizung | `radiator` | 📌 Heizkörper | `heizkoerper` | ⛔ `objectType: radiator` |
| 76 | Heizung | `floor-heating` | 📌 Fußbodenheizung | `fussbodenheizung` | ⛔ `zoneType: underfloor_heating` |
| 77 | Heizung | `pump` | 📌 Pumpe | `pumpe` |  |
| 78 | Heizung | `heat-pump` | 📌 Wärmepumpe | `waermepumpe` | ⛔ `objectType: heat_pump_indoor / _outdoor` |
| 79 | Heizung | `hydraulic-balance` | 📌 Hydraulischer Abgleich | `hydraulischer-abgleich` |  |
| 80 | TGA | `pipe` | 📌 Rohrleitung | `rohrleitung` | ⛔ `routeType: heating_pipe / water_pipe` |
| 81 | Sanitär | `sanitary` | 📌 Sanitäranschluss | `sanitaeranschluss` | ⛔ `objectType: sanitary` |
| 82 | Bad | `bath` | 📌 Badewanne | `badewanne` |  |
| 83 | Bad | `shower` | 📌 Dusche | `dusche` |  |
| 84 | Bad | `toilet` | 📌 WC | `wc` |  |
| 85 | Küche | `kitchen` | 📌 Küchenplanung | `kuechenplanung` |  |
| 86 | Küche | `cabinet` | 📌 Schrank | `schrank` |  |
| 87 | Küche | `appliance` | 📌 Gerät | `geraet` |  |
| 88 | Elektro | `electric` | 📌 Elektroplanung | `elektroplanung` |  |
| 89 | Elektro | `socket` | 📌 Steckdose | `steckdose` |  |
| 90 | Elektro | `switch` | 📌 Schalter | `schalter` |  |
| 91 | Elektro | `light` | 📌 Leuchte | `leuchte` |  |
| 92 | Elektro | `distribution-board` | 📌 Verteiler | `verteiler` |  |
| 93 | PV | `pv-module` | 📌 PV-Modul | `pv-modul` | ⛔ `zoneType: pv_area (indirekt)` |
| 94 | PV | `battery` | 📌 Batteriespeicher | `batteriespeicher` | ⛔ `objectType: battery` |
| 95 | PV | `wallbox` | 📌 Wallbox | `wallbox` | ⛔ `objectType: wallbox` |
| 96 | Workflow | `wizard` | 📌 Assistent | `assistent`★ |  |
| 97 | Workflow | `workflow` | 📌 Prozessübersicht | `prozessuebersicht` |  |
| 98 | Workflow | `handoff-package` | 📌 Übergabepaket | `bergabepaket` |  |
| 99 | Workflow | `approve` | 📌 Freigeben | `freigeben` |  |
| 100 | Zusammenarbeit | `comment` | 📌 Kommentar | `kommentar` |  |
| 101 | Zusammenarbeit | `history` | 📌 Historie | `historie` |  |
| 102 | Zusammenarbeit | `revision` | 📌 Revision | `revision` |  |
| 103 | Prüfung | `check` | 📌 Prüfen | `pruefen` |  |
| 104 | Prüfung | `warning` | 📌 Warnungen | `warnungen` |  |
| 105 | Prüfung | `error` | 📌 Fehler | `fehler` |  |
| 106 | System | `settings` | 📌 Einstellungen | `einstellungen` |  |
| 107 | System | `search` | 📌 Suche | `suche` |  |
| 108 | System | `command-palette` | 📌 Befehlspalette | `befehlspalette`★ |  |
| 109 | System | `export` | 📌 Exportieren | `exportieren` |  |
| 110 | System | `pdf` | 📌 PDF / Planblatt | `pdf` |  |

## Prüfsummen (maschinell)

- **110** Werkzeuge, **22** Kategorien · **0** ID-Kollisionen im deutschen Satz.
- **9** Paket-IDs konvergieren exakt auf die bestehenden Registry-IDs — Beleg, dass dies eine **Zusammenführung** ist, keine Parallelbenennung.
- **16** Zeilen schema-gebunden (⛔) — deren gespeicherter Wert bleibt englisch.
- **7** IDs von Yama entschieden (★): `hand · assistent · befehlspalette · umkreisen · aufriss · klinker · unterzug`.

## Ballbesitz

Führende Vorlage für **Generator I2** — er übernimmt die Spalte **dt. ID** direkt und baut den Adapter so, dass die 16 ⛔-Zeilen auf ihren englischen Schema-Wert mappen. Cowork hat **keinen Code** angefasst.

---

## Nachtrag Planner, 25.07. — Status und Geltung

**Diese Tabelle ist ab jetzt committet und damit übergeben.** Solange sie nur im Arbeitsbaum lag,
existierte sie für den Generator nicht — genau deshalb trägt `werkzeugPaket.ts` aus I2 (`289ccc8`)
noch die **englischen** Paket-IDs. Das wird mit **AUF-31** nachgezogen; I2 selbst bleibt gültig.

**Führend:** diese Datei. `docs/planner/werkzeug-namen-deutsch.md` (`1c97c65`) ist **stillgelegt**,
Trail erhalten — eine Wahrheit je Sachverhalt.

**Ratifiziert und eingearbeitet:** die fünf Kuratier-Overrides (`hand · assistent · befehlspalette ·
umkreisen · aufriss`) und die zwei Fach-Labels (`brick` = Klinker-Verband, Kategorie Fassade;
`beam` = Unterzug, weil neben `column`/Stütze im Hochbau).
