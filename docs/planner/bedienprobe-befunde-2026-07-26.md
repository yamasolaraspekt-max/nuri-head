# Bedienprobe 26.07. — vier Ablaeufe von Hand, drei Befunde nachgemessen

**Herkunft:** Bedienprobe mit Playwright gegen Buendel `022021f` (gebaut aus `cca1837`),
Dokumentations-HEAD danach `461dbee`. Nichts gespeichert, kein Projektcode geaendert.
**Rolle:** Diese Probe ist **keine Abnahme** — sie hat keinen Posten geprueft, sondern die
laufende Anwendung bedient. Sie ist damit das, was uns bisher fehlte: eine Messung **quer** zu den
Posten statt entlang.

Ich habe die drei tragenden Behauptungen **nicht geglaubt, sondern im Code nachgesehen.** Alle drei
stimmen, und zwei davon stehen woertlich in unseren eigenen Kommentaren.

---

## Befund 1 — Wandlaenge aendern loest die Ecke. **Bestaetigt, und der Code sagt es selbst.**

Die Probe: 8.000 mm auf 6.000 mm geaendert. Es wandert nur das Wandende, der Anfang bleibt fest,
die Verbindung zur unteren Wand reisst.

```
app/HausplanerApp.tsx:604
  // Bearbeiten: Wand-Laenge exakt setzen -> Wandende entlang der Achse verschieben (MOVE_NODE).
app/HausplanerApp.tsx:612
  executeCommand({ type: 'MOVE_NODE', nodeId: selectedWall.id,
                   position: { start: selectedWall.start, end } });
```

`start` wird unveraendert durchgereicht, `end` neu gerechnet. **Es gibt keine Zeile, die eine
Nachbarschaft kennt** — also kann auch keine sie erhalten. Das ist kein Fehler in der Umsetzung,
das ist der Umfang, der nie beauftragt wurde.

**Warum das mehr ist als Bedienkomfort — und wo ich aufhoere zu behaupten:** eine Wand, deren Ende
sich vom Anfang der Nachbarwand loest, hinterlaesst eine Luecke. `geometry/roomDetection.ts` leitet
Raeume aus geschlossenen Zuegen ab, und `zone/room` ist laut Schema **ausschliesslich abgeleitet**.
Ob eine solche Luecke die Raumerkennung still kippen laesst — **das habe ich nicht gemessen.** Es
ist die eine Frage, die vor jeder Gestaltungsidee beantwortet gehoert, denn davon haengt ab, ob
das hier ein Komfort-Posten ist oder ein Richtigkeits-Posten.

Der Vorschlag aus der Probe (vorher waehlen: Anfang halten, Ende halten, aus der Mitte) ist gut und
verfrueht. **Zuerst die Messung, dann die Bedienung.**

## Befund 2 — `fangKern` ist gebaut, getestet und an nichts angeschlossen. **Bestaetigt.**

```
$ grep -rl fangKern .
./__tests__/fangKern.test.ts
./geometry/fangKern.ts
```

**Die einzige Datei, die den Fang-Kern benutzt, ist seine eigene Testdatei.** Die Zeichenflaeche
fangt stattdessen selbst, mit festem Radius:

```
app/HausplanerApp.tsx:825
  // 1) Endpunkt-Snap (150 mm Radius) hat Vorrang.
```

Fester Radius in Millimetern heisst: die gefuehlte Maus-Toleranz aendert sich mit dem Zoom. Bei
weit herausgezoomter Ansicht sind 150 mm ein Bruchteil eines Pixels — der Fang wirkt dann kaputt,
obwohl er tut, was dasteht.

**Das ist dieselbe Sorte Schaden wie AUF-50, nur an anderer Stelle.** Heute Nachmittag gemessen:
83 Werkzeuge sind aktivierbar und haben keinen Empfaenger. Hier: eine Fanglogik mit Endpunkt-,
Mittelpunkt-, Raster- und Orthogonalregeln, gruen getestet, von niemandem gerufen. **Der gruene
Test beweist, dass sie richtig rechnet — nicht, dass sie jemand fragt.**

Daraus eine Regel, die ich mir selbst aufschreibe: *ein Test, der nur den eigenen Modul-Namen als
Aufrufer hat, ist ein Hinweis auf einen fehlenden Empfaenger, kein Beleg fuer eine Funktion.*
Das ist mit einem Zaehler pruefbar und waere ein besserer Waechter als jede Absichtserklaerung.

## Befund 3 — Werkzeug bleibt nach dem Platzieren aktiv. **Bestaetigt, und es ist Absicht.**

Nach dem Setzen eines Fensters bleibt das Fensterwerkzeug aktiv; der naechste Klick setzt das
naechste. Bei `dach`, `decke` und `treppe` springt der Modus dagegen von selbst auf `auswahl`
zurueck (Zeilen 940, 957, 988), bei `wand`/`fenster`/`tuer` nicht.

**Die Uneinheitlichkeit ist der Befund, nicht das Bleiben.** Serienweises Setzen ist bei Fenstern
und Waenden richtig und bei einem Dach sinnlos. Was fehlt, ist die **Anzeige** des Unterschieds:
die Fusszeile sagt beim Wandzug `Esc beendet den Zug`, beim Fenster sagt sie das nicht.

## Befund 4 — keine Direktbearbeitung am Objekt. **Bestaetigt, kein Fehler.**

Masse nur im Eigenschaftenpanel, keine Griffe, keine Inline-Eingabe. Das war nie gebaut. Es ist ein
Wunsch, kein Mangel, und es ist ein grosser Wunsch — Griffe brauchen Treffer-Erkennung auf
Teilflaechen, und genau die hat AUF-35b als **Renderer-Anteil zurueckgegeben**: der Renderer
reduziert den Treffer sofort auf `nodeId`. **Die Vorarbeit dafuer liegt, der Anschluss fehlt.**
Dritter Fall desselben Musters an einem Tag.

## Nebenbefund — `Cannot read properties of null (reading 'addEventListener')`

Trat waehrend der Probe durchgehend auf, ohne die geprueften Ablaeufe zu stoeren. **Ein Fehler, der
nichts kaputtmacht, ist trotzdem ein Fehler** — vor allem ist er Laerm, der den naechsten echten
Fehler in derselben Konsole zudeckt. Nicht gemessen: ob er aus dem Hausplaner kommt oder aus der
umgebenden Seite. Das ist billig zu klaeren und gehoert vor die Deutung.

---

## Was daraus **nicht** wird

Nach §14 entsteht hier **kein einziger neuer Posten**, solange AUF-38, AUF-52, AUF-48 und AUF-50
offen sind. Die Probe hat vier Wuensche und vier Befunde geliefert; sie kommen auf die
Befundliste und warten.

Der Test dafuer ist der aus §14: *"Welchen offenen Posten kann ich ohne diesen hier nicht
abschliessen?"* — fuer alle vier lautet die Antwort **keinen**. Also kein Posten.

**Eine Ausnahme pruefe ich, sobald gemessen ist:** faellt Befund 1 so aus, dass eine geloeste Ecke
die Raumerkennung still kippt, ist das kein Komfort mehr, sondern falsche Zahlen aus richtig
aussehender Geometrie — und dann gehoert er vorgezogen. Diese Messung ist selbst kein Posten,
sondern eine Frage an eine bestehende Datei, und sie dauert Minuten.

## Rangfolge, wenn die Reihe frei wird

1. **Befund 1** (nach der Messung) — die einzige Stelle, an der etwas still falsch werden koennte.
2. **Befund 2** — `fangKern` anschliessen und den Radius in Bildschirm-Pixeln rechnen. Der Kern ist
   da und abgenommen; das ist Anschlussarbeit, keine Neuentwicklung.
3. **Befund 3** — Fusszeile pro Werkzeug. Klein, Spur B.
4. **Befund 4** — Griffe am Objekt. Gross, haengt am Renderer-Anteil aus AUF-35b, gehoert hinter
   AUF-48 (`HausplanerApp.tsx` zerlegen), sonst waechst die Datei weiter.
