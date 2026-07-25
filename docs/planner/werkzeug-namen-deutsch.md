# ⇒ PLANNER — Deutsche Namen für die 110 Werkzeuge (Vorlage für I2)

**25.07.2026** · Grundlage: Yamas Anordnung *„ich will alles auf deutsch"* (Ledger, AUF-20 überstimmt).
**Zweck:** Der Generator soll bei I2 die deutschen IDs **übernehmen**, nicht erfinden.

## Regeln, nach denen diese Tabelle entstanden ist

1. **Die neun Bestands-IDs bleiben byte-genau, wie sie sind** — `auswahl · wand · fenster · tuer ·
   dach · decke · treppe · loeschen · duplizieren`. Kein einziger Aufrufer, kein Test, keine Fixture
   muss angefasst werden.
2. Alle übrigen IDs sind aus dem **deutschen Label des Pakets** abgeleitet — kleingeschrieben,
   Umlaute ausgeschrieben (`ä→ae`), Leer-/Sonderzeichen zu `-`.
3. **Alle 110 IDs sind eindeutig** — maschinell geprüft, keine Dublette.
4. **Icon-Dateien werden mitbenannt:** `public/hausplaner/icons/tools/<deutsche-id>.svg`.
   Die 110 Dateien liegen heute unter den englischen Paket-IDs (I1, `7bbf9ff`) und werden umbenannt.
5. **Gespeicherte Datenwerte bleiben unberührt** (`type: wall|window|door|ceiling`, `objectType`,
   `zoneType`, `routeType`). Das ist der Datenschutz aus der DAUERDIREKTIVE, keine Sprachfrage —
   ein Umbenennen dort wäre eine Migration an Bestandsdaten. **Der Adapter bildet die deutsche
   Werkzeug-ID auf den gespeicherten Typ ab.**

## Zuordnung nach Kategorie


### Ansicht  (7)

| Paket-ID | **deutsche ID** | Label | Icon-Datei |
|---|---|---|---|
| `fit-view` | **`alles-anzeigen`** | Alles anzeigen | `alles-anzeigen.svg` |
| `snap` | **`fangen`** | Fang | `fangen.svg` |
| `grid` | **`raster`** | Raster | `raster.svg` |
| `orbit` | **`umkreisen`** | Orbit | `umkreisen.svg` |
| `zoom-in` | **`vergroessern`** | Vergrößern | `vergroessern.svg` |
| `zoom-out` | **`verkleinern`** | Verkleinern | `verkleinern.svg` |
| `pan` | **`verschieben-ansicht`** | Pan / Hand | `verschieben-ansicht.svg` |

### Architektur  (15)

| Paket-ID | **deutsche ID** | Label | Icon-Datei |
|---|---|---|---|
| `floor` | **`boden`** | Boden | `boden.svg` |
| `roof` | **`dach`** | Dach | `dach.svg` · **Bestand, unverändert** |
| `roof-window` | **`dachfenster`** | Dachfenster | `dachfenster.svg` |
| `slab` | **`decke`** | Decke / Bodenplatte | `decke.svg` · **Bestand, unverändert** |
| `elevation` | **`fassade-ansicht`** | Fassade / Ansicht | `fassade-ansicht.svg` |
| `window` | **`fenster`** | Fenster | `fenster.svg` · **Bestand, unverändert** |
| `dormer` | **`gaube`** | Gaube | `gaube.svg` |
| `opening` | **`offnung`** | Öffnung | `offnung.svg` |
| `room` | **`raum`** | Raum | `raum.svg` |
| `section` | **`schnitt`** | Schnitt | `schnitt.svg` |
| `column` | **`stuetze`** | Stütze | `stuetze.svg` |
| `stairs` | **`treppe`** | Treppe | `treppe.svg` · **Bestand, unverändert** |
| `door` | **`tuer`** | Tür | `tuer.svg` · **Bestand, unverändert** |
| `beam` | **`unterzug-traeger`** | Unterzug / Träger | `unterzug-traeger.svg` |
| `wall` | **`wand`** | Wand | `wand.svg` · **Bestand, unverändert** |

### Auswahl  (4)

| Paket-ID | **deutsche ID** | Label | Icon-Datei |
|---|---|---|---|
| `select` | **`auswahl`** | Auswahl | `auswahl.svg` · **Bestand, unverändert** |
| `direct-select` | **`direktauswahl`** | Direktauswahl | `direktauswahl.svg` |
| `lasso-select` | **`lassoauswahl`** | Lassoauswahl | `lassoauswahl.svg` |
| `box-select` | **`rechteckauswahl`** | Rechteckauswahl | `rechteckauswahl.svg` |

### Bad  (3)

| Paket-ID | **deutsche ID** | Label | Icon-Datei |
|---|---|---|---|
| `bath` | **`badewanne`** | Badewanne | `badewanne.svg` |
| `shower` | **`dusche`** | Dusche | `dusche.svg` |
| `toilet` | **`wc`** | WC | `wc.svg` |

### Bauphysik  (4)

| Paket-ID | **deutsche ID** | Label | Icon-Datei |
|---|---|---|---|
| `insulation` | **`daemmung`** | Dämmung | `daemmung.svg` |
| `ventilation` | **`lueftung`** | Lüftung | `lueftung.svg` |
| `thermal-envelope` | **`thermische-huelle`** | Thermische Hülle | `thermische-huelle.svg` |
| `u-value` | **`u-wert`** | U-Wert | `u-wert.svg` |

### Bearbeiten  (15)

| Paket-ID | **deutsche ID** | Label | Icon-Datei |
|---|---|---|---|
| `hide` | **`ausblenden`** | Ausblenden | `ausblenden.svg` |
| `align` | **`ausrichten`** | Ausrichten | `ausrichten.svg` |
| `rotate` | **`drehen`** | Drehen | `drehen.svg` |
| `duplicate` | **`duplizieren`** | Duplizieren | `duplizieren.svg` · **Bestand, unverändert** |
| `show` | **`einblenden`** | Einblenden | `einblenden.svg` |
| `unlock` | **`entsperren`** | Entsperren | `entsperren.svg` |
| `group` | **`gruppieren`** | Gruppieren | `gruppieren.svg` |
| `mirror-horizontal` | **`horizontal-spiegeln`** | Horizontal spiegeln | `horizontal-spiegeln.svg` |
| `copy` | **`kopieren`** | Kopieren | `kopieren.svg` |
| `delete` | **`loeschen`** | Löschen | `loeschen.svg` · **Bestand, unverändert** |
| `scale` | **`skalieren`** | Skalieren | `skalieren.svg` |
| `lock` | **`sperren`** | Sperren | `sperren.svg` |
| `move` | **`verschieben`** | Verschieben | `verschieben.svg` |
| `distribute` | **`verteilen`** | Verteilen | `verteilen.svg` |
| `mirror-vertical` | **`vertikal-spiegeln`** | Vertikal spiegeln | `vertikal-spiegeln.svg` |

### CAD  (5)

| Paket-ID | **deutsche ID** | Label | Icon-Datei |
|---|---|---|---|
| `split` | **`teilen`** | Teilen | `teilen.svg` |
| `trim` | **`trimmen`** | Trimmen | `trimmen.svg` |
| `join` | **`verbinden`** | Verbinden | `verbinden.svg` |
| `extend` | **`verlaengern`** | Verlängern | `verlaengern.svg` |
| `offset` | **`versatz`** | Versatz | `versatz.svg` |

### Elektro  (5)

| Paket-ID | **deutsche ID** | Label | Icon-Datei |
|---|---|---|---|
| `electric` | **`elektroplanung`** | Elektroplanung | `elektroplanung.svg` |
| `light` | **`leuchte`** | Leuchte | `leuchte.svg` |
| `switch` | **`schalter`** | Schalter | `schalter.svg` |
| `socket` | **`steckdose`** | Steckdose | `steckdose.svg` |
| `distribution-board` | **`verteiler`** | Verteiler | `verteiler.svg` |

### Fassade  (2)

| Paket-ID | **deutsche ID** | Label | Icon-Datei |
|---|---|---|---|
| `facade` | **`fassadensystem`** | Fassadensystem | `fassadensystem.svg` |
| `brick` | **`klinker-verband`** | Klinker / Verband | `klinker-verband.svg` |

### Heizung  (5)

| Paket-ID | **deutsche ID** | Label | Icon-Datei |
|---|---|---|---|
| `floor-heating` | **`fussbodenheizung`** | Fußbodenheizung | `fussbodenheizung.svg` |
| `radiator` | **`heizkoerper`** | Heizkörper | `heizkoerper.svg` |
| `hydraulic-balance` | **`hydraulischer-abgleich`** | Hydraulischer Abgleich | `hydraulischer-abgleich.svg` |
| `pump` | **`pumpe`** | Pumpe | `pumpe.svg` |
| `heat-pump` | **`waermepumpe`** | Wärmepumpe | `waermepumpe.svg` |

### Import  (8)

| Paket-ID | **deutsche ID** | Label | Icon-Datei |
|---|---|---|---|
| `crop` | **`beschneiden`** | Beschneiden | `beschneiden.svg` |
| `import-image` | **`bild-importieren`** | Bild importieren | `bild-importieren.svg` |
| `import-file` | **`datei-importieren`** | Datei importieren | `datei-importieren.svg` |
| `approve-detection` | **`erkennung-bestaetigen`** | Erkennung bestätigen | `erkennung-bestaetigen.svg` |
| `recognize` | **`grundriss-erkennen`** | Grundriss erkennen | `grundriss-erkennen.svg` |
| `calibrate` | **`kalibrieren`** | Kalibrieren | `kalibrieren.svg` |
| `ai-assistant` | **`ki-assistent`** | KI-Assistent | `ki-assistent.svg` |
| `set-north` | **`nordrichtung`** | Nordrichtung setzen | `nordrichtung.svg` |

### Küche  (3)

| Paket-ID | **deutsche ID** | Label | Icon-Datei |
|---|---|---|---|
| `appliance` | **`geraet`** | Gerät | `geraet.svg` |
| `kitchen` | **`kuechenplanung`** | Küchenplanung | `kuechenplanung.svg` |
| `cabinet` | **`schrank`** | Schrank | `schrank.svg` |

### Material  (3)

| Paket-ID | **deutsche ID** | Label | Icon-Datei |
|---|---|---|---|
| `paint` | **`material-aufnehmen`** | Material aufnehmen | `material-aufnehmen.svg` |
| `material` | **`material-zuweisen`** | Material zuweisen | `material-zuweisen.svg` |
| `texture` | **`textur-pbr`** | Textur / PBR | `textur-pbr.svg` |

### Messen  (5)

| Paket-ID | **deutsche ID** | Label | Icon-Datei |
|---|---|---|---|
| `dimension` | **`bemassen`** | Bemaßen | `bemassen.svg` |
| `measure-distance` | **`distanz-messen`** | Distanz messen | `distanz-messen.svg` |
| `measure-area` | **`flaeche-messen`** | Fläche messen | `flaeche-messen.svg` |
| `measure-volume` | **`volumen-messen`** | Volumen messen | `volumen-messen.svg` |
| `measure-angle` | **`winkel-messen`** | Winkel messen | `winkel-messen.svg` |

### PV  (3)

| Paket-ID | **deutsche ID** | Label | Icon-Datei |
|---|---|---|---|
| `battery` | **`batteriespeicher`** | Batteriespeicher | `batteriespeicher.svg` |
| `pv-module` | **`pv-modul`** | PV-Modul | `pv-modul.svg` |
| `wallbox` | **`wallbox`** | Wallbox | `wallbox.svg` |

### Prüfung  (3)

| Paket-ID | **deutsche ID** | Label | Icon-Datei |
|---|---|---|---|
| `error` | **`fehler`** | Fehler | `fehler.svg` |
| `check` | **`pruefen`** | Prüfen | `pruefen.svg` |
| `warning` | **`warnungen`** | Warnungen | `warnungen.svg` |

### Sanitär  (1)

| Paket-ID | **deutsche ID** | Label | Icon-Datei |
|---|---|---|---|
| `sanitary` | **`sanitaeranschluss`** | Sanitäranschluss | `sanitaeranschluss.svg` |

### System  (5)

| Paket-ID | **deutsche ID** | Label | Icon-Datei |
|---|---|---|---|
| `command-palette` | **`befehlspalette`** | Command-Palette | `befehlspalette.svg` |
| `settings` | **`einstellungen`** | Einstellungen | `einstellungen.svg` |
| `export` | **`exportieren`** | Exportieren | `exportieren.svg` |
| `pdf` | **`pdf-ausgabe`** | PDF / Planblatt | `pdf-ausgabe.svg` |
| `search` | **`suche`** | Suche | `suche.svg` |

### TGA  (1)

| Paket-ID | **deutsche ID** | Label | Icon-Datei |
|---|---|---|---|
| `pipe` | **`rohrleitung`** | Rohrleitung | `rohrleitung.svg` |

### Workflow  (4)

| Paket-ID | **deutsche ID** | Label | Icon-Datei |
|---|---|---|---|
| `approve` | **`freigeben`** | Freigeben | `freigeben.svg` |
| `workflow` | **`prozessuebersicht`** | Prozessübersicht | `prozessuebersicht.svg` |
| `handoff-package` | **`ubergabepaket`** | Übergabepaket | `ubergabepaket.svg` |
| `wizard` | **`wizard-assistent`** | Wizard / Assistent | `wizard-assistent.svg` |

### Zeichnen  (6)

| Paket-ID | **deutsche ID** | Label | Icon-Datei |
|---|---|---|---|
| `arc` | **`bogen`** | Bogen | `bogen.svg` |
| `circle` | **`kreis`** | Kreis | `kreis.svg` |
| `line` | **`linie`** | Linie | `linie.svg` |
| `polygon` | **`polygon`** | Polygon | `polygon.svg` |
| `polyline` | **`polylinie`** | Polylinie | `polylinie.svg` |
| `rectangle` | **`rechteck`** | Rechteck | `rechteck.svg` |

### Zusammenarbeit  (3)

| Paket-ID | **deutsche ID** | Label | Icon-Datei |
|---|---|---|---|
| `history` | **`historie`** | Historie | `historie.svg` |
| `comment` | **`kommentar`** | Kommentar | `kommentar.svg` |
| `revision` | **`revision`** | Revision | `revision.svg` |

---

**Maschinell erzeugt und geprüft:** 110 Werkzeuge, 110 eindeutige deutsche IDs, 9 Bestands-IDs unverändert. Die Maschinenfassung liegt daneben als `werkzeug-namen-deutsch.json` (`{paket-id: {de, label, kategorie}}`) — direkt im Adapter verwendbar.

---

## ⛔ STILLGELEGT — zusammengeführt in `eindeutschung-110-paket-ids.md`

**25.07., Planner.** Zwei Cowork-Instanzen haben unabhängig voneinander dieselbe Namenstabelle
gebaut. **Das ist eine zweite Wahrheit und wird nicht nebeneinander gehalten.**
Führend ist **`docs/planner/eindeutschung-110-paket-ids.md`**. Diese Datei bleibt als Trail stehen,
wird aber **nicht** mehr gepflegt und **nicht** von I2 gelesen.

**Warum die andere gewinnt — ein sachlicher Grund, keine Reihenfolge:** Sie markiert die
**16 schema-gebundenen IDs einzeln mit ⛔ und dem exakten Schutzwert** (`wall`, `ceiling`,
`radiator`, `stair`, inklusive der Sonderfälle `slab→ceiling` und `stairs→stair`). Diese Datei hier
nennt die Grenze nur als Prosaregel. Der Unterschied ist der zwischen „funktioniert" und
**422 beim Speichern** — und damit genau der Punkt, an dem die DAUERDIREKTIVE greift.

**Was aus dieser Datei übernommen wird:** die Maschinenfassung `werkzeug-namen-deutsch.json`
bleibt nur gültig, wenn sie **aus der führenden Tabelle neu erzeugt** wird. Bis dahin gilt sie als
stillgelegt.

**Verursacht durch:** Der Planner hat die Tabelle begonnen, ohne zu prüfen, ob eine zweite
Cowork-Instanz auf demselben Posten sitzt. Die Regel „ein Posten, ein Strang" (AUF-22) gilt auch
**innerhalb** von Cowork, nicht nur zwischen nativ und Cowork. → als Ergänzung zu AUF-22 vermerkt.
