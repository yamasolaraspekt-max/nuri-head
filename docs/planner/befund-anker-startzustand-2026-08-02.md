# BEFUND — der L-01-Anker schlägt im Startzustand fehl, obwohl alles in Ordnung ist

**Planner · Strang `hausplaner-3d` · 02.08.2026, 11:3x**
*Erste Bestandsmessung an der laufenden Oberfläche. Yama hat den Browser freigegeben; gemessen
wurde nur gelesen — kein Klick, keine Eingabe, nichts angelegt.*

---

## Was gemessen wurde

```text
http://ticket.test/admin/hausplaner/studio     angemeldet, HTTP 200
document.title            "SA-DESK - Hausplaner — Studio"      ✓
#hausplaner-root          da, 1077x701 px, 1 Kind              ✓
#hausplaner-scene         da, 0 Kinder
document.querySelectorAll('canvas').length     ->  0           ✗
```

**Der Anker, den ich in 18 Blätter geschrieben habe, ist an dieser Stelle ROT:**

> *„VOR jeder anderen Zahl: HTTP 200, `querySelectorAll('canvas')` mindestens 1,
> `document.title` enthält Hausplaner."*

## Warum er rot ist — und warum die Anwendung trotzdem heil ist

**Der Bildschirm zeigt die Startansicht:**

```text
„Was möchtest du planen?"
„Noch kein Projekt geöffnet. Ein Vorhaben beginnt unten mit Hausplaner —
 oder mit einem der Fachplaner, die auch ohne Gebäude laufen."
Kopfzeile: Übersicht · Geführte Planung · Expertenmodus
Oben links: „Testfläche — wird nicht gespeichert"
```

**Die Bühne existiert erst, wenn ein Projekt offen ist.** Ohne Projekt gibt es keinen Canvas —
und das ist richtig so, nicht kaputt.

**Der Anker misst also die Bühne, ohne den Zustand zu nennen, in dem sie überhaupt existiert.**
Wer ihn im Startzustand fährt, bekommt rot und hält die Anwendung für defekt. *Das ist F-06 in
einer neuen Ausprägung: die Zusage prüft eine Gestalt und nennt die Vorbedingung nicht.*

**Und es ist mein Fehler, achtzehnfach:**

```text
grep -rl "querySelectorAll('canvas')" docs/auftraege/ | wc -l   ->  18
```

## Die Korrektur — der Anker bekommt seinen Zustand

**Der Anker lautet ab sofort dreistufig, und die erste Stufe ist neu:**

```text
1  SEITE      HTTP 200 · document.title enthaelt "Hausplaner"
              · #hausplaner-root existiert und ist groesser als 0x0
2  ZUSTAND    Ist ein Projekt offen? Steht "Noch kein Projekt geoeffnet" auf dem Schirm,
              ist der Startzustand erreicht - dann wird ein Projekt geoeffnet ODER der
              Test endet hier mit dem Vermerk STARTZUSTAND, nicht mit rot.
3  BUEHNE     ERST DANN: querySelectorAll('canvas') mindestens 1
```

**Warum Stufe 1 `#hausplaner-root` statt `canvas` prüft:** der Mount-Punkt ist das, was beweist,
dass die Insel überhaupt geladen hat. **Er war vorhanden, während der Canvas fehlte** — genau die
Unterscheidung, die dem alten Anker fehlte.

## Zwei Konsolenfehler — NICHT unser Strang, gemeldet statt angefasst

```text
[EXCEPTION] chat-BvaPqhwG.js   TypeError: Cannot read properties of null (reading 'addEventListener')
[ERROR]     chat-BvaPqhwG.js   Reverb WS probe error: ws://ticket.test:6001/app/…
```

**Beide kommen aus dem Chat-Bündel, nicht aus dem Hausplaner.** Der erste ist ein Zugriff auf ein
Element, das es auf dieser Seite nicht gibt; der zweite ist der WebSocket-Dienst, der nicht läuft.
**Keiner von beiden berührt die Insel** — der Hausplaner hat auf dieser Seite **null** eigene
Fehler geworfen.

*Nicht angefasst: `chat-*.js` gehört nicht zum Strang `hausplaner-3d`. Gemeldet, damit es jemand
weiß, der zuständig ist.*

## Was die Messung nebenbei über den Bestand sagt

```text
Drei Modi in der Kopfzeile     Uebersicht · Gefuehrte Planung · Expertenmodus
Zwei Einstiege                 Sanierungsplan (in Entwicklung) · Hausplaner (Neubau/Gesamtgebaeude)
Fachplaner laufen autark       Fenster, Tueren, Treppen, Heizkoerper - auch ohne Gebaeude
Hinweis oben links             „Testflaeche — wird nicht gespeichert"
```

**Für die Blätter heißt das:** ein Browsertest, der zeichnen will, muss **zuerst ein Projekt
öffnen** und sollte den Expertenmodus wählen. *Das steht in meinen Blättern als „angemeldet,
Expertenmodus" — aber ohne den Schritt davor.*

---

**Der Wert dieser Messung liegt nicht im Fehler, sondern darin, dass ich ihn ohne die laufende
Oberfläche nie gefunden hätte.** Achtzehn Blätter tragen einen Anker, den ich aus dem Code
abgeleitet habe. *Genau meine Klasse: ich habe die Stelle gemessen, nicht die Wirkung.*
