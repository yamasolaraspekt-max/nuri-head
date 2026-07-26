# Bedienmodell fuer 110 Werkzeuge — Konzept, SWOT und neuer Zuschnitt von AUF-50

**26.07.2026, Planner.** Yama: *"gerade wenn wir diese 110 werkzeugstools bauen, ist smarte
intelligente Bedienung sehr wichtig — darueber gruendlich Gedanken machen und dann konzeptionell
die Aufgabe formulieren, damit Workflow und Effizienz steigt."*

Alles hier Genannte ist **gemessen**, nicht eingeschaetzt. Wo ich etwas nicht gemessen habe, steht
es dabei.

---

## Der eine Befund, der alles ordnet

**Die Intelligenz ist gebaut. Sie ist nur hohl.**

Ich bin davon ausgegangen, dass eine kontextabhaengige Bedienung erst noch entstehen muss. Das ist
falsch. Sie laeuft bereits:

```
HausplanerApp.tsx:421   werkzeugKontext = baueAktivierungsKontext({ selectionTypes: ... })
toolContext.ts:31       count: e.selectionTypes.length
HausplanerApp.tsx:1409  const zustand = resolveToolState(tool, werkzeugKontext)
```

**Die Auswahl fliesst schon in die Werkzeugleiste.** 27 der 101 Werkzeuge tragen die Vorbedingung
`selection.count >= 1`, 3 tragen `selection.hasRoofFace`. Waehlt der Nutzer eine Wand, gehen sie
auf. Waehlt er nichts, sind sie gesperrt **mit deutschem Grund im Tooltip**.

Und dann passiert nichts. Denn von den 101 haben **7 einen Empfaenger**; 83 sind aktivierbar und
laufen ins Leere (11 stehen zu Recht gesperrt). **Das ist schlimmer als eine dumme Oberflaeche.**
Eine dumme Oberflaeche verspricht nichts. Diese hier zeigt dem Nutzer, dass sie ihn versteht — sie
geht im richtigen Moment auf, sie begruendet ihre Sperren — und liefert dann nicht. Beim dritten
Mal glaubt er der Leiste nicht mehr, auch dort nicht, wo sie recht hat.

**Daraus folgt die Reihenfolge des ganzen Vorhabens:** nicht erst Intelligenz bauen, sondern erst
die vorhandene **einloesen**.

## Wie weit das Muster reicht — 28 von 128

Ich habe die Insel durchgezaehlt: fuer jedes Modul, wer es importiert.

```
Module (ohne Testdateien)                                    128
Module, deren EINZIGER Aufrufer ihre eigene Testdatei ist     28
```

Etwa elf davon sind bestellt und warten auf **AUF-52** (die zwoelf Rechen-Engines). Bleiben rund
**siebzehn**, die niemand bestellt hat und niemand ruft — darunter genau die Bausteine, die eine
smarte Bedienung braucht:

| Modul | was es kann | wer es ruft |
|---|---|---|
| `geometry/fangKern.ts` | Endpunkt-, Mittelpunkt-, Raster-, Orthogonalfang | nur sein Test |
| `app/tools/auswahlDarstellung.ts` | entscheidet, **ob Griffe gezeichnet werden** | nur sein Test |
| `app/tools/trefferSuche.ts` | Treffersuche | nur sein Test |
| `geometry/grundriss.ts` | Gebaeudeumriss | nur sein Test |

`auswahlDarstellung.ts` ist der bitterste Fall. Die Regeln sind durchdacht — *"fuenf Anfasser an
fuenf Objekten waeren fuenfmal dieselbe Geste"*, *"an Gesperrtem gibt es nichts zu ziehen"* —, sie
sind gruen getestet, und **der Renderer fragt nie**. Wir haben die Entscheidung ueber Griffe
gebaut, bevor es Griffe gab.

**Ein gruener Test beweist, dass ein Modul richtig rechnet — nicht, dass jemand es fragt.**

---

## Was ein technischer Zeichner erwartet

Der Zielnutzer ist kein Gelegenheitsanwender. Er hat ArchiCAD, AutoCAD oder Revit in den Fingern
und bringt vier Erwartungen mit, die er nicht ausspricht, weil sie ihm selbstverstaendlich sind.

### 1. Er denkt in Zahlen, nicht in Pixeln

Er zeichnet nicht ungefaehr und korrigiert nach. Er **tippt die Zahl waehrend des Zeichnens**:
Richtung mit der Maus, Laenge ueber die Tastatur, Enter.

**Gemessen: das gibt es nicht.** Waehrend des Wandzugs wird nur der Cursor ausgewertet
(`wandStart` + Winkelraster); die Fusszeile sagt `Klick = naechster Wandpunkt · Esc beendet den
Zug`. Kein Zifferngriff, kein Eingabefeld. Der einzige numerische Weg fuehrt **nachtraeglich**
ueber das Eigenschaftenpanel — und genau der loest Ecken (siehe
`messung-raumerkennung-ecke-2026-07-26.md`).

*Das ist der groesste einzelne Effizienzhebel im ganzen Vorhaben und zugleich einer der kleinsten
Bauteile.*

### 2. Jedes Element hat drei Tiefen

| Geste | Erwartung | heute |
|---|---|---|
| Klick | auswaehlen | **da** |
| Doppelklick | die bestimmende Zahl aendern | **null Vorkommen in der ganzen Insel** |
| Griff ziehen | dasselbe sichtbar | Entscheidung gebaut, Renderer fragt nicht |

`grep -rn "dblclick|onDblClick|doubleClick"` ueber `resources/planner/hausplaner` liefert **keinen
einzigen Treffer**. Der Doppelklick — die selbstverstaendlichste Geste eines Zeichners — ist in
diesem Programm keine Geste.

### 3. Der Fang muss sagen, woran er fasst

Gemessen: fester Radius von **150 mm** (`HausplanerApp.tsx:825`), kein Hinweis worauf gefangen
wurde. Fest in Millimetern heisst: beim Herauszoomen wird der Fang gefuehlt unbrauchbar, beim
Hineinzoomen uebergriffig. Der `fangKern` mit vier Fangarten liegt daneben und wird nicht gerufen.

### 4. Er will nicht suchen

Bei 7 Werkzeugen sucht niemand. Bei 101 ist das Suchen die Arbeit. Die Daten fuer eine Leiste, die
sich selbst ordnet, sind **vollstaendig vorhanden**: `prioritaet: primary|secondary` und
`anheftbar` in jedem der 101 Eintraege, sechs Anzeigezustaende in `werkzeugZustand.ts` — darunter
`empfohlen`, gespeist vom Wizard, und `weitere` fuer den Ueberlauf. Was fehlt, ist die Anordnung.

---

## Das Bedienmodell in fuenf Saetzen

1. **Die Auswahl fuehrt, nicht der Werkzeugkasten.** Wer eine Wand waehlt, bekommt die Verben, die
   auf eine Wand passen. Der Mechanismus laeuft; er braucht Empfaenger, keine Erfindung.
2. **Drei Tiefen je Element: Klick, Doppelklick, Griff.** Dieselbe Sache, dreimal erreichbar —
   ueber Auswahl, ueber die Zahl, ueber das Ziehen.
3. **Die Zahl ist das Werkzeug.** Jede Geometrie ist waehrend der Entstehung und danach als Zahl
   eingebbar. Das Panel bleibt die ausfuehrliche Alternative, nicht der einzige Weg.
4. **Kein stummes Nichts.** Ein Werkzeug tut etwas, oder es sagt in einem deutschen Satz, warum
   nicht — und ob der Grund eine Vorbedingung ist oder ein fehlender Anschluss. Beides ist ehrlich;
   Schweigen ist es nicht.
5. **Die Leiste ordnet sich selbst.** Angeheftetes, Empfohlenes, Gesperrtes, Ueberlauf — die
   Zustaende sind gebaut, die Rangfolge steht in den Daten.

---

## SWOT — aus der Sicht des technischen Zeichners

### Staerken (gemessen)

- **mm-Ganzzahlwelt ohne Toleranzmagie.** Was 8000 heisst, ist 8000. Fuer einen Zeichner ist das
  die Grundvoraussetzung, und viele Programme haben sie nicht.
- **Jede Aenderung ist ein Command**, Undo/Redo fallen aus inversen Patches ab. Der Rueckweg ist
  nicht angebaut, er ist die Bauart.
- **Vorbedingungen sind Daten, keine Programmzweige** — 12 Vorbedingungen, jede mit deutschem
  Grund, fuenf davon ehrlich als "heute nicht erfuellbar" gefuehrt statt auf gruen verdrahtet.
- **Der Auswahlkontext ist bereits verdrahtet.** Kontextabhaengigkeit ist kein Zukunftsthema.
- **1256 Tests gruen**, durchgaengig deutsche Fachsprache.

### Schwaechen (gemessen)

- **0 Doppelklick-Griffe** in der gesamten Insel.
- **Keine Zahleneingabe waehrend des Zeichnens.**
- **83 Werkzeuge aktivierbar ohne Empfaenger** — vier `as`-Umdeutungen halten die Typpruefung still.
- **Keine Griffe am Objekt**; die Entscheidung darueber ist gebaut und wird nicht gefragt.
- **Fangradius fest in mm**, ohne Rueckmeldung woran.
- **Wandlaenge aendern loest Ecken** — und eine geloeste Ecke laesst Raeume verschwinden, im
  gemessenen Fall B1 unter Hinterlassung einer plausiblen 20,00-m²-Zahl.
- **28 von 128 Modulen ohne Aufrufer ausser dem eigenen Test.**

### Chancen

- **Der groesste Teil der Arbeit ist Anschlussarbeit, nicht Neubau.** Fang, Griff-Regel,
  Treffersuche, Rangfolge, Sperrgruende, Wegweiser — alles vorhanden und abgenommen.
- **Ein Empfaenger schaltet nicht ein Werkzeug frei, sondern eine Familie.** 27 Werkzeuge haengen
  an derselben Auswahl-Vorbedingung.
- **Die Vertraege liefern die Zuordnung frei Haus**: 110 Eintraege mit Familie, Eingaben,
  Ergebnissen, Seiteneffekten. Die Verdrahtungstabelle muss nicht erfunden, sie muss gelesen werden.
- **Der Doppelklick ist billig und wirkt sofort** — eine Geste, sechs vorhandene Bauteile.

### Risiken

- **Vertrauensverlust ist nicht rueckbaubar.** Wer dreimal auf ein Werkzeug klickt, das nichts tut,
  klickt beim vierten Mal nicht mehr — auch nicht auf die, die inzwischen funktionieren.
- **Stille falsche Zahlen.** Fall B1 ist bereits real: richtige Rechnung, unvollstaendige Liste,
  kein Hinweis. Das ist der einzige Befund, der nicht nach Bedienkomfort riecht, sondern nach
  Haftung.
- **Die Halde waechst.** Jedes weitere gebaute-und-nicht-angeschlossene Modul erhoeht die Zahl 28.
  Ein Modul ohne Aufrufer altert, ohne dass es jemand merkt.
- **AUF-48 ist Voraussetzung, nicht Nebensache.** `HausplanerApp.tsx` traegt 2.052 Zeilen. Griffe,
  Doppelklick und Direkteingabe kommen alle in dieselbe Datei. Wer sie vorher nicht zerlegt, baut
  drei Feature in eine Datei, die schon jetzt niemand mehr ueberblickt.

---

## Neuer Zuschnitt von AUF-50

**Alt** (Bestandsaufnahme, 20:45): vier Stufen nach Vertragsfamilien — `create` 40, `modify` 24,
`view`/`measurement` 12. Das ist eine Gliederung nach **Bauteilen**.

**Neu:** eine Gliederung nach **Bedienung**. Jede Stufe ist fuer sich benutzbar; keine Stufe
liefert Vorrat fuer eine spaetere.

### 50.1 — Der Empfaenger und das Ende des stummen Nichts

Zuordnungstabelle `werkzeugId -> Wirkung` in `app/tools/`; die vier `as Werkzeug`-Umdeutungen
entfallen. Ein Werkzeug ohne Wirkung sagt es. **Kein neues Werkzeug wird benutzbar — Absicht.**
Abnahme ist ein Gegen-Beweis, der heute rot sein muss: einen Eintrag entfernen, `tsc` faellt.
`store/` und `domain/` bleiben unberuehrt (gemessen: `executeCommand` ist der Empfaenger, er
existiert seit P0).

### 50.2 — Die Zahl

Laengeneingabe waehrend des Wandzugs; Doppelklick auf ein Mass oeffnet die Eingabe. **Der groesste
Hebel je Aufwand.** Setzt voraus, dass die Wandlaengen-Frage (Ecke halten) entschieden ist — sonst
baut diese Stufe einen bequemeren Weg in denselben Fehler.

### 50.3 — Die drei Tiefen

`auswahlDarstellung.griffe` anschliessen, Griffe am Primaerobjekt zeichnen, Ziehen auf `MOVE_NODE`.
Braucht den Renderer-Anteil, den AUF-35b ausdruecklich zurueckgegeben hat. **Gehoert hinter
AUF-48.**

### 50.4 — Der Fang, der spricht

`fangKern` als einzige Fanglogik anschliessen, Radius in Bildschirm-Pixeln, Fangart benennen.

### 50.5 — Die 13 erreichbaren Erzeugen-Werkzeuge

`oeffnung · gaube · dachfenster · heizkoerper · batteriespeicher · wallbox · schrank · geraet ·
badewanne · dusche · wc · fussbodenheizung · rohrleitung` — alle auf vorhandene Schema-Plaetze, in
Scheiben.

### 50.6 — Auswahl, Ansicht, Messen, Aendern

Der Rest. `measurement` erst, wenn entschieden ist, ob ein Mass ueberlebt oder mit dem Blatt geht.

---

## Was Yama entscheiden muss (drei Fragen, keine davon vom Planner)

1. **Ecke halten oder nicht?** Soll eine Laengenaenderung angeschlossene Waende mitnehmen? Davon
   haengen 50.2 und die Rangfolge von Befund 1 ab.
2. **Bekommen Elektro, PV, Tragwerk und freie CAD-Geometrie Plaetze im Szenen-Schema** — oder
   bleiben diese 20 Werkzeuge sichtbar gesperrt, bis die Fachplanung sie braucht?
3. **Wird AUF-48 vorgezogen?** 50.2 und 50.3 landen beide in `HausplanerApp.tsx`.

Nach §14 entsteht aus diesem Papier **kein neuer Posten** — AUF-50 steht auf der Tafel, dieses
Papier schneidet ihn neu zu.
