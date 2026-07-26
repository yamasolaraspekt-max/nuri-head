# AUF-50 — Stufenplan. Die zwei fehlenden Messungen

**Planner, 26.07.2026.** Am 20:45 stand im Ledger: *"Zwei Dinge messe ich noch, bevor 50.1 ein
Auftrag wird: wie viele der 101 heute `gesperrt` stehen, und ob der generische Empfaenger ohne
Aenderung an `store/` oder `domain/` moeglich ist — falls nicht, ist das eine Rueckgabe an Yama,
keine Entscheidung des Planners."*

Beide Messungen liegen jetzt vor. Ergebnis vorweg: **der Empfaenger ist ohne `store/` und ohne
`domain/` moeglich** — er existiert im Store sogar schon. Die Rueckgabe an Yama faellt trotzdem an,
aber an einer anderen Stelle als vermutet: nicht beim Empfaenger, sondern bei **20 der 40
Erzeugen-Werkzeuge**, fuer die das Schema keinen Platz hat.

---

## Messung A — wie viele der 101 stehen heute `gesperrt`

Gezaehlt aus `app/tools/werkzeugVertrag.ts` gegen die fuenf Vorbedingungen, die
`app/tools/vorbedingungen.ts` selbst als *heute nicht erfuellbar* fuehrt.

| Vorbedingung | Werkzeuge | erfuellbar? |
|---|---|---|
| `project.open` | 101 | ja |
| `permission.edit` | 61 | ja (`Hausplaner,update`) |
| `activeLevel.exists` | 34 | ja |
| `selection.count >= 1` | 27 | ja |
| `permission.import` | 8 | **nein** |
| `viewport.ready` | 5 | ja |
| `selection.hasRoofFace` | 3 | ja |
| `component.thermalRelevant` | 1 | **nein** |
| `heatingNetwork.connected` | 1 | **nein** |
| `heatEmitters.sized` | 1 | **nein** |
| `heatingLoad.approved` | 1 | **nein** |

**11 von 101 stehen gesperrt** — nicht 94, wie die Bestandsaufnahme nahelegen koennte:

- **8 Import** (`beschneiden` · `bild-importieren` · `datei-importieren` ·
  `erkennung-bestaetigen` · `grundriss-erkennen` · `ki-assistent` · `kalibrieren` ·
  `nordrichtung-setzen`) haengen alle an `permission.import`, einem Recht, das das CRM nicht kennt.
- **3 fachlich**: `u-wert` · `hydraulischer-abgleich` · `waermepumpe`.

Damit ist die Lage praeziser als bisher notiert: **90 Werkzeuge sind heute aktivierbar, 7 haben
einen Empfaenger, 83 haben keinen.** Die 83 sind der eigentliche Schaden — sie sehen benutzbar aus
und tun nichts. Ein gesperrtes Werkzeug luegt nicht; ein aktives ohne Empfaenger schon.

---

## Messung B — geht der Empfaenger ohne `store/` und ohne `domain/`?

**Ja.** Der Store fuehrt bereits eine generische Annahmestelle:

```
store/hausplanerStore.ts:51   executeCommand: (command: HausplanerCommand) => boolean;
```

Sie nimmt jeden der 19 Command-Typen, laeuft ueber `produceWithPatches`, und Undo/Redo/409 fallen
dabei von selbst ab. Es fehlt kein Empfaenger im Store. Es fehlt die **Zuordnung davor**.

### Wo die Luecke wirklich sitzt

`app/HausplanerApp.tsx` fuehrt sieben Modi:

```ts
type Werkzeug = 'auswahl' | 'wand' | 'fenster' | 'tuer' | 'dach' | 'treppe' | 'decke'
```

An **vier Stellen** wird eine Werkzeug-id aus der Leiste hineingereicht — jedes Mal per Umdeutung:

```
HausplanerApp.tsx:719    setWerkzeug(tool.id as Werkzeug);
HausplanerApp.tsx:1054   setWerkzeug(tool.id as Werkzeug);
HausplanerApp.tsx:1415   setWerkzeug(tool.id as typeof werkzeug);
HausplanerApp.tsx:1435   onAktivieren={(id) => { setWerkzeug(id as Werkzeug); ... }}
```

**Der Cast ist die Luecke.** Er behauptet dem Compiler gegenueber, jede der 101 ids sei einer der
sieben Modi. Klickt der Nutzer `heizkoerper`, steht danach `werkzeug === 'heizkoerper'` — und der
Klick-Handler hat fuer diesen Wert keinen Zweig. Es passiert nichts, ohne Fehler, ohne Meldung.
Das ist kein fehlendes Feature, das ist eine **abgeschaltete Typpruefung**: ohne die vier Casts
haette `tsc:hausplaner` die 94 fehlenden Empfaenger seit dem ersten Tag gemeldet.

**Folgerung fuer 50.1:** Der Empfaenger ist eine Zuordnungstabelle in `app/tools/` plus das
Entfernen der vier Casts. `store/` und `domain/` bleiben unberuehrt. **Keine Rueckgabe an Yama an
dieser Stelle** — der Planner darf entscheiden.

---

## Was das Schema traegt — und was nicht

Die 40 Erzeugen-Werkzeuge zerfallen sauber in zwei Haelften. Gemessen gegen
`domain/scene.types.ts` und `domain/scene-document-v2.schema.json` (beide Listen sind deckungsgleich,
nachgeprueft).

### 20 landen auf einem vorhandenen Platz — hier darf gebaut werden

| Werkzeug | Platz im Schema | Stand |
|---|---|---|
| `wand` | `wall` | angeschlossen |
| `fenster` · `tuer` | `window` · `door` | angeschlossen |
| `oeffnung` | `opening` | **frei** |
| `dach` | `roof` | angeschlossen |
| `gaube` · `dachfenster` | `roof.aufbauten` | **frei** |
| `decke` | `ceiling` | angeschlossen |
| `treppe` | `object/stair` | angeschlossen |
| `heizkoerper` | `object/radiator` | **frei** |
| `waermepumpe` | `object/heat_pump_*` | frei, aber **gesperrt** |
| `batteriespeicher` | `object/battery` | **frei** |
| `wallbox` | `object/wallbox` | **frei** |
| `schrank` · `geraet` | `object/furniture` | **frei** |
| `badewanne` · `dusche` · `wc` | `object/sanitary` | **frei** |
| `fussbodenheizung` | `zone/underfloor_heating` | **frei** |
| `rohrleitung` | `route/heating_pipe` · `water_pipe` | **frei** |

Sechs davon sind bereits verdrahtet, `waermepumpe` ist gesperrt. **13 Erzeugen-Werkzeuge sind
heute ohne jede Schema-Aenderung erreichbar.**

Drei Schema-Plaetze haben kein Werkzeug im Paket: `buffer_tank` · `hot_water_tank` · `inverter`.
Kein Handlungsbedarf, nur eine Notiz — das Paket ist nicht die ganze Wahrheit ueber das Schema.

### 20 haben keinen Platz — das ist die Rueckgabe an Yama

| Gruppe | Werkzeuge | was fehlt |
|---|---|---|
| Freie CAD-Geometrie | `bogen` `kreis` `linie` `polygon` `polylinie` `rechteck` | ein Knotentyp fuer Geometrie ohne Fachbedeutung |
| Erzeugte Ansichten | `aufriss` `schnitt` | Ansichten sind kein Szenen-Inhalt |
| Tragwerk | `stuetze` `unterzug` `boden` | kein `objectType`, `boden` ist keine Geschossdecke |
| Elektro | `leuchte` `schalter` `steckdose` `verteiler` | kein `objectType` fuer Elektro-Objekte |
| PV | `pv-modul` | `pv_area` ist eine Zone, kein Modul |
| Heizung | `pumpe` | kein `objectType` |
| Sonstige | `raum` `kuechenplanung` `sanitaeranschluss` | siehe unten |

Drei brauchen keine neue Liste, sondern eine **Entscheidung**:

- **`raum`** — `zone/room` gibt es, aber das Schema sagt ausdruecklich *"P0 aktiv NUR zoneType
  'room' und NUR abgeleitet (Raumerkennung)"*, Feld `derived`. Ein Raum-Werkzeug zeichnet von Hand.
  Das ist kein fehlender Platz, das ist ein **Widerspruch zur P0-Regel**. Frage an Yama: darf ein
  Raum von Hand entstehen, oder bleibt Raumerkennung die eine Wahrheit?
- **`sanitaeranschluss`** — `route/water_pipe` oder `object/sanitary`? Der Vertrag sagt
  `createdObjectIds` und laesst es offen.
- **`kuechenplanung`** — erzeugt laut Vertrag einen `kitchenPlanId`, also ein Plan-Objekt ueber
  mehreren Knoten. Das ist eine Struktur, kein Knoten.

**Warum das an Yama geht und nicht an den Planner:** eine `objectType`-Liste zu erweitern ist eine
Aenderung an einer **persistierten** Aufzaehlung. Sie beruehrt `scene.types.ts`, das JSON-Schema und
die Validierung, sie erzeugt Bestandsdaten, die aeltere Staende nicht lesen koennen, und sie ist
nicht folgenlos rueckdrehbar. Die DAUERDIREKTIVE verbietet das Umbenennen; das Hinzufuegen ist
formal erlaubt, aber es ist eine Schema-Entscheidung — und die gehoert nach der Bauordnung nicht
dem Planner.

---

## Der Stufenplan

**Neu gegenueber der Bestandsaufnahme:** dort standen vier Stufen mit Umfang ~78. Nach der Messung
ist der Umfang **kleiner und schaerfer geschnitten** — was nicht geht, geht raus, statt es zu
schaetzen.

### 50.1 — Der Empfaenger. Vier Casts weg, eine Tabelle hin

Kein neues Werkzeug wird benutzbar. **Das ist Absicht.** Diese Stufe macht sichtbar, was heute
unsichtbar scheitert.

- Eine Zuordnung `werkzeugId -> Wirkung` in `app/tools/` — drei Sorten Wirkung: *Modus setzen*
  (die 7 heutigen), *Command ausfuehren*, *nicht angeschlossen*.
- Die vier Casts in `HausplanerApp.tsx` entfallen; die Leiste reicht eine `string`-id an die
  Zuordnung, nicht an den Modus.
- Ein Werkzeug ohne Wirkung meldet das dem Nutzer in einem deutschen Satz, statt still nichts zu
  tun. Kein neuer Zustand: es ist derselbe Weg, den `gesperrt` schon geht — nur ist der Grund hier
  *"noch nicht angeschlossen"* und nicht *"Voraussetzung fehlt"*.
- **Abnahme:** ein Gegen-Beweis, der heute rot sein muss — die Zuordnung um einen Eintrag
  erleichtert, und `tsc:hausplaner` faellt. Genau das kann er heute nicht.
- **Nicht in dieser Stufe:** `store/`, `domain/`, jede Datei unter `renderers/`.

### 50.2 — Die 13 erreichbaren Erzeugen-Werkzeuge

`oeffnung` · `gaube` · `dachfenster` · `heizkoerper` · `batteriespeicher` · `wallbox` · `schrank` ·
`geraet` · `badewanne` · `dusche` · `wc` · `fussbodenheizung` · `rohrleitung`.

Jedes landet auf `executeCommand` mit einem vorhandenen Command. In Scheiben, nicht am Stueck.
`waermepumpe` bleibt aussen vor, solange die Vorbedingung unerfuellbar ist.

### 50.3 — Auswahl, Ansicht, Messen

`selection` (4) · `view` (7) · `measurement` (5). Sie schreiben nicht in die Szene, sondern in den
UI-Zustand — der Weg ist kuerzer, aber `measurement` braucht vorher eine Antwort darauf, ob ein
Mass ueberlebt oder mit dem Blatt verschwindet. **Diese Frage vor der Stufe stellen, nicht darin.**

### 50.4 — `modify` (20)

Setzt 50.2 voraus: was man nicht erzeugen kann, muss man nicht aendern koennen.

### Ausserhalb von AUF-50

- **8 Import** — haengen an `permission.import`. Rechte-Entscheidung, war schon in AUF-36 als
  Rueckgabe notiert. Gehoert zu Phase 2 des Fahrplans.
- **9 assign-or-calculate** — AUF-52.
- **15 workflow** · **2 domain** — brauchen die Auslegung, nicht die Zeichenflaeche.
- **9 Vertraege ohne Werkzeug im Paket** — nur eine Notiz, kein Posten.

---

## Was ich hier nicht entschieden habe

Nach §14 wird aus diesem Papier **kein** neuer Posten, solange AUF-38, AUF-52 und AUF-48 offen sind.
AUF-50 steht bereits auf der Tafel; dieses Papier schneidet ihn nur zu.

Und die 20 ohne Schema-Platz sind **kein Auftrag, den ich schreiben darf**. Sie sind eine Frage an
Yama, und sie hat eine kurze Form:

> Sollen Elektro (`leuchte` `schalter` `steckdose` `verteiler`), PV (`pv-modul`), Tragwerk
> (`stuetze` `unterzug`) und freie CAD-Geometrie eigene Plaetze im Szenen-Schema bekommen — oder
> bleiben diese Werkzeuge sichtbar gesperrt, bis die Fachplanung sie braucht?

Beides ist vertretbar. Das zweite ist ehrlicher zum heutigen Stand, das erste macht die Leiste
ganz. Was nicht vertretbar ist, ist der heutige Zustand: sie sehen aus wie das erste und
verhalten sich wie gar nichts.
