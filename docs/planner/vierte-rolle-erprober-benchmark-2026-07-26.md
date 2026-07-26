# Die vierte Rolle: der Erprober — und ein Benchmark fuer den Funktionstest

**26.07.2026, Planner.** Yama: *"ab jetzt brauchen wir eine weitere Instanz. Wenn wir Funktionen
bauen, muss auch von einem Dritten aus verschiedenen Blickwinkeln und willkuerlich getestet werden.
Wir brauchen auch Benchmark fuer den Funktionstest."*

**Der Beweis, dass das noetig ist, liegt von heute vor** — und er ist unangenehm.

## Warum der Zyklus das nicht findet

Heute kamen aus einer einzigen Bedienprobe vier Befunde. Ich habe drei davon im Code nachgemessen,
alle drei stimmen. Einer davon — geloeste Ecke laesst Raeume verschwinden und hinterlaesst eine
plausible Flaechenzahl — ist eine Richtigkeitsfrage, keine Bedienfrage.

**Und trotzdem war kein einziges Votum falsch.** Jeder Posten wurde korrekt abgenommen, gegen seine
eigenen Abnahmekriterien, mit Gegen-Beweis. Der Evaluator hat sauber gearbeitet.

Das ist der Punkt: **die Fehler liegen nicht in den Posten, sondern zwischen ihnen.**

- Der Evaluator prueft: *"Tut es, was bestellt wurde?"*
- Niemand prueft: *"Was passiert, wenn jemand etwas tut, das niemand bestellt hat?"*

Ein Werkzeug, das niemand angeschlossen hat, verletzt kein Abnahmekriterium — es gab nie eines
dafuer. Eine Wandlaenge, die Ecken loest, verletzt keines — der Auftrag lautete "Laenge exakt
setzen", und genau das tut sie. **Beide Male ist die Arbeit richtig und das Ergebnis falsch.**

Diese Luecke schliesst keine zusaetzliche Sorgfalt der drei vorhandenen Rollen. Sie schliesst nur
eine Rolle, die **kein Abnahmekriterium in der Hand hat**.

---

## Die Rolle: der Erprober

**Auftrag:** die laufende Anwendung benutzen wie ein Fachmann, der sie nicht gebaut hat, und
berichten, was im Weg stand.

**Was er bekommt:** eine Rolle (technischer Zeichner / Architekt / Elektromeister), ein Ziel
("zeichne diesen Grundriss"), einen benannten Commit. **Keine Abnahmekriterien.**

*Das ist der ganze Trick.* Gibt man ihm Kriterien, hat man einen zweiten Evaluator gebaut, und der
findet dieselben Dinge wie der erste. Seine Staerke ist genau das, was ihm fehlt.

### Vier Grenzen, sonst zerfaellt der Rahmen

1. **Er nimmt nichts ab und stimmt nicht ab.** Er liefert **Befunde**, nie Voten. Zwei
   Abnahmeinstanzen waeren zwei Wahrheiten im Ledger.
2. **Er schreibt keinen Produktionscode.** Kein Vorschlag als Patch — Vorschlaege sind Text.
3. **Willkuer braucht ein Protokoll.** Er darf beliebig herumprobieren, aber **jede Handlung wird
   mitgeschrieben**, sodass ein Befund nachgespielt werden kann. Ein Befund ohne Weg dorthin ist
   eine Anekdote, und Anekdoten kann man nicht beheben. *Zufall ist erlaubt, Unreproduzierbarkeit
   nicht.*
4. **Er misst gegen einen benannten Commit** und schreibt `git status public/*` vor **und** nach
   jeder Sichtprobe mit (§13.6). Er baut nie selbst.

### Blickwinkel — nicht "gruendlicher", sondern anders

Reihum, einer je Durchgang. Redundanz findet dasselbe dreimal; Verschiedenheit findet Verschiedenes.

| Blickwinkel | Frage |
|---|---|
| **Der Eilige** | Wie schnell komme ich zum Ergebnis, wenn ich nichts lese? |
| **Der Genaue** | Kommt exakt die Zahl heraus, die ich wollte? |
| **Der Ungeschickte** | Was passiert bei Fehlbedienung, Abbruch, Esc, Doppelklick ins Leere? |
| **Der Wiederkehrer** | Ich mache morgen weiter — ist alles noch da und noch richtig? |
| **Der Zweifler** | Sieht es nur richtig aus, oder ist es richtig? |

Der **Zweifler** haette heute Fall B1 gefunden: eine glatte 20,00-m²-Zahl, die stimmt, in einer
Liste, die einen Raum nicht enthaelt.

---

## Der Benchmark: die Pruefstrecke

Ein Funktionstest ohne Zahlen ist eine Meinung. Der Benchmark macht "besser geworden" nachweisbar.

### Sechs feste Aufgaben, immer dieselben

| Nr | Aufgabe | Sollwert, pruefbar |
|---|---|---|
| **P1** | Rechteckiger Grundriss 8.000 x 5.000 mm, Wanddicke 240 | 4 Waende, **1 Raum, 40,00 m²** |
| **P2** | Trennwand bei x = 4.000 einziehen | **2 Raeume, je 20,00 m²** |
| **P3** | Tuer 1.010 mm, 2.000 mm ab Wandanfang der Suedwand | Oeffnung sitzt auf 2.000, nicht 1.987 |
| **P4** | Zwei Fenster 1.260 x 1.360, Bruestung 900, bei 1.500 und 4.500 | vier Masse exakt |
| **P5** | Ein Fenster nachtraeglich auf 1.600 x 1.500 aendern | neue Masse exakt, Wand unveraendert |
| **P6** | Untere Aussenwand auf 6.000 kuerzen, Grundriss geschlossen halten | **weiterhin 2 Raeume** |

**P6 faellt heute durch.** Das ist Absicht: eine Pruefstrecke, die alles besteht, misst nichts. Sie
muss die Stelle enthalten, an der es weh tut, sonst zeigt sie keinen Fortschritt.

### Fuenf Messgroessen

1. **Handgriffe** — Klicks + Tastenanschlaege je Aufgabe. Daneben der **Bestwert**: die kleinste
   Zahl von Eingaben, mit der die Aufgabe ueberhaupt loesbar waere. *Der Bestwert wird ausgezaehlt
   und im Bericht hingeschrieben, nie geschaetzt.* Die Kennzahl ist das Verhaeltnis.
2. **Leerlaeufe** — Handlungen, nach denen sich nichts geaendert hat. Heute die aussagekraeftigste
   Zahl von allen: jeder Klick auf eines der 83 Werkzeuge ohne Empfaenger ist einer.
3. **Masstreue** — |Ist − Soll| in mm, je Aufgabe. Null oder nicht null; ein Zeichenprogramm hat
   hier keinen Toleranzbereich.
4. **Rueckweg** — laesst sich jeder Schritt einzeln zuruecknehmen? Gezaehlt, nicht behauptet.
5. **Stille Abweichungen** — Ergebnisse, die richtig aussehen und es nicht sind. Keine Messzahl,
   sondern eine Liste. **Jeder Eintrag hier wiegt schwerer als alle vier anderen Zahlen zusammen.**

### Was der Benchmark ausdruecklich **nicht** misst

**Zeit.** Sekunden messen die Maschine, die Netzverbindung und die Tagesform des Pruefers — nicht
die Anwendung. Handgriffe sind reproduzierbar, Sekunden nicht. Wer Tempo will, bekommt es ueber
Handgriffe ehrlicher.

### Gefahren des Benchmarks selbst

- **Man optimiert, was gemessen wird.** Handgriffe lassen sich senken, indem man Rueckfragen
  weglaesst — und dann loescht ein Klick ein Geschoss. Deshalb steht Messgroesse 5 nicht am Ende,
  sondern ueber allen anderen.
- **Eine Pruefstrecke veraltet.** Sie deckt ab, was wir heute fuer wichtig halten. Sie ersetzt die
  Willkuer nicht — sie ist der feste Teil daneben. **Der Benchmark zeigt Fortschritt, der Erprober
  findet das Unbekannte.** Wer nur den Benchmark faehrt, sieht nur, was er schon kennt.
- **Die erste Messung ist die Grundlinie, nicht das Urteil.** Sie wird schlecht aussehen. Das ist
  ihr Zweck.

---

## Wo die Rolle im Rahmen sitzt

**Vorschlag §17.** Der Erprober ist die vierte Rolle neben Planner, Generator, Evaluator. Er ist
**kein Tor**: er blockiert nichts, er haelt nichts auf. Seine Befunde gehen an den Planner und
landen auf der Befundliste — und damit unter §14, wie jeder andere Befund auch.

**Ein Konflikt ist echt und ich glaette ihn nicht.** Gemessen als Engpass 3 der
Parallelbetriebs-Untersuchung: **es gibt nur eine servierte Anwendung** (`ticket.test`, Herd
serviert nur den Hauptbaum). Erprober und Evaluator greifen auf dieselbe zu, und der Generator
schreibt sie beim Bauen neu. Drei Moeglichkeiten:

**a)** Der Erprober laeuft nur, wenn im Abnahme-Stapel keine Sichtprobe wartet. Einfach, kostet
Wartezeit.
**b)** Er faehrt die Pruefstrecke **nach jedem Merge auf `main`** statt laufend — ein fester Takt,
kein Gedraenge, und der Benchmark bekommt genau die Punkte, die eine Kurve ergeben.
**c)** Ein zweiter servierter Baum. Loest den Engpass wirklich, ist aber ein Eingriff in die
Umgebung und gehoert Yama.

**Meine Empfehlung: b.** Der Benchmark will ohnehin feste Messpunkte statt Dauerbetrieb, und "nach
jedem Merge" ist ein Zeitpunkt, an dem der Baum ruhig ist und der Stand einen Namen hat. Willkuer
laesst sich jederzeit dazwischenschieben, wenn der Stapel leer ist.

## Was ich nicht entscheide

Ob die Rolle kommt, wann sie anfaengt und welche der drei Moeglichkeiten gilt — das ist Yamas
Entscheidung. Nach §14 waere eine vierte Instanz "etwas Neues"; die Ausnahme dieser Regel ist
**Yama selbst**, und er hat sie gerade in Anspruch genommen. Ich schreibe die Regel nicht in
`06-laufzeiten-und-takt.md`, bevor er die drei Punkte oben bestaetigt hat — sonst steht eine Regel
im Rahmen, die niemand beschlossen hat.


---

# Was das fuer den Planner heisst (Yama, 26.07.)

> *"Du als Planner sollst dich immer in die Lage des Erprobers versetzen, damit du weisst, wie du
> ihn zufriedenstellen kannst. Es muss easy sein — KISS, keep it simple."*

Das ist eine Regel ueber **meine** Arbeit, nicht ueber die des Erprobers. Sie hat zwei Teile.

## Teil 1 — Jeder Auftrag bekommt eine "Probe des Erprobers"

Kuenftig steht in jedem Auftrag, den ich schreibe, **ein Satz** in genau dieser Form:

> **Probe des Erprobers:** *Jemand, der dieses Programm nicht gebaut hat, tut ___ und sieht ___.*

Keine Feldnamen, keine Modulnamen, keine Commit-Nummern. Handlung und Beobachtung, in der Sprache
des Zeichners.

**Und die Regel dahinter: Kann ich diesen Satz nicht schreiben, ist der Auftrag nicht fertig.**
Nicht weil eine Zeile fehlt, sondern weil ich dann selbst nicht weiss, was der Nutzer davon hat.
Das ist ein besserer Test als jede Vollstaendigkeitspruefung an meiner Auftragsvorlage — meine
bisherigen Auftraege haetten ihn zum Teil **nicht** bestanden. AUF-50 in seiner alten Fassung war
"110 Werkzeuge funktionstuechtig machen"; darin steht keine Handlung und keine Beobachtung.

Beispiele fuer die sechs Stufen im neuen Zuschnitt:

| Stufe | Probe des Erprobers |
|---|---|
| **50.1** | Er klickt ein Werkzeug, das noch nichts tut, und **liest, dass es noch nicht angeschlossen ist** — statt ins Leere zu klicken. |
| **50.2** | Er zeichnet eine Wand, **tippt 4000, drueckt Enter**, und die Wand ist 4.000 mm lang. |
| **50.3** | Er klickt ein Fenster an und **sieht Griffe daran**; er zieht einen, das Fenster wird breiter. |
| **50.4** | Er faehrt an eine Ecke und **liest "Endpunkt"**, bevor er klickt. |
| **50.5** | Er waehlt "Heizkoerper", klickt in den Raum, und **ein Heizkoerper steht da**. |
| **50.6** | Er waehlt drei Waende und **sieht, welche Werkzeuge dafuer gelten**. |

Diese Saetze sind zugleich die Abnahmekriterien, die der Erprober ohne Einweisung pruefen kann.

## Teil 2 — KISS, konkret und nicht als Spruch

Bei 101 Werkzeugen ist die Versuchung gross, ein **kluges System** zu bauen: Modi, Kontexte,
Empfehlungen, Assistenten. Genau davor warnt die Regel. Vier Saetze, an denen ich mich messen
lasse:

1. **Eine Geste, die ueberall gilt, schlaegt fuenf kluge.** Der Doppelklick aendert **immer** die
   bestimmende Zahl — an der Wand die Laenge, am Fenster die Breite, am Mass den Wert. Kein
   Objekt bekommt eine Sonderbedeutung. Was man einmal lernt, gilt fuer alles.
2. **Zwei Zustaende muss der Nutzer verstehen, nicht sechs.** *Geht* oder *geht nicht, und hier ist
   der Grund.* Die sechs Anzeigezustaende der Leiste sind eine Sache der Darstellung — sie duerfen
   nie zu sechs Dingen werden, die man wissen muss.
3. **Ein Empfaenger, nicht neun.** Eine Zuordnungstabelle fuer alle Werkzeuge — keine eigene
   Mechanik je Vertragsfamilie. Neun Mechaniken sind neun Stellen, an denen es unterschiedlich
   kaputtgeht.
4. **Erst die Grundgeste, dann die Klugheit.** Wizard-Empfehlungen, Anheftungen und
   Selbstordnung der Leiste sind gebaut und warten. Sie kommen **nach** Doppelklick, Griff und
   Zahleneingabe — nicht davor. Eine kluge Leiste ueber einer Bedienung, die die Grundgesten nicht
   kann, ist eine Verkleidung.

**Die Gegenprobe zu KISS, damit sie nicht zum Vorwand wird:** einfach heisst *wenig zu lernen*,
nicht *wenig gebaut*. Ein Doppelklick, der an sechs Bauteilen dasselbe tut, ist mehr Arbeit als
sechs Sonderfaelle — und genau deshalb ist er die einfachere Loesung. Wer KISS benutzt, um Umfang
zu streichen statt Regeln zu vereinheitlichen, hat es falsch verstanden.

## Was ich an mir selbst korrigiere

Meine Auftraege sind bisher aus der Sicht des **Bauenden** geschrieben: welche Datei, welcher
Vertrag, welche Grenze, welcher Gegen-Beweis. Das bleibt richtig — der Generator braucht es.

Es fehlte die andere Seite. Ab jetzt steht sie oben, nicht unten: **erst die Probe des Erprobers,
dann die Bauanweisung.** Ein Auftrag, dessen Nutzen ich nicht in einem Satz sagen kann, ist kein
Auftrag, sondern eine Beschaeftigung.
