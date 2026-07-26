# Tester-Master-Prompt — Einordnung. Der Rollenbrief des Erprobers, und ein Widerspruch

**26.07.2026, Planner.** Yama hat den Tester-Master-Prompt uebergeben: 41 Abschnitte, unabhaengige
Pruefung von Usability, Bedienintelligenz, Funktionsintelligenz, Maus/Touch/Stift/Tastatur.

**Das ist der Rollenbrief fuer die vierte Rolle**, die ich vor zwei Stunden skizziert habe. Er
fuellt aus, was ich nur umrissen hatte — Personas, Geraetematrix, Befundformat, Schweregrade,
Abbruchkriterien. **Er ist besser als mein Entwurf an drei Stellen** und steht an einer Stelle im
Widerspruch dazu. Beides schreibe ich hin.

---

## Teil 1 — Wo er meinen Entwurf schlaegt

1. **Das Befundformat (§34) ist vollstaendiger als meine Protokollpflicht.** Ich hatte gefordert:
   *"jede Handlung wird mitgeschrieben, Zufall ist erlaubt, Unreproduzierbarkeit nicht."* §34 sagt
   dasselbe praeziser — Ausgangszustand, Reproduktionsschritte, tatsaechliches gegen erwartetes
   Ergebnis, Beweis, Haeufigkeit. **Ich uebernehme §34 unveraendert.**
2. **Die Schweregrade P0–P3 (§34) sind das, was meiner Befundliste gefehlt hat.** Bisher ordne ich
   nach Urteil ("das ist der schlimme Fall"); ab jetzt nach einem Massstab, den jemand anderes
   nachvollziehen kann.
3. **§2 Grundsatz 18:** *"Der Tester darf keine Tests abschwaechen, nur damit der Testlauf gruen
   wird."* Dieser Satz gehoert eigentlich in unseren Rahmen und steht dort nicht.

## Teil 2 — Der Widerspruch, und er ist nicht klein

**Ich hatte geschrieben:** der Erprober bekommt *"ausdruecklich keine Abnahmekriterien — gaebe man
sie ihm, haette man einen zweiten Evaluator gebaut."*

**Der Tester-Prompt gibt ihm Freigabekriterien (§39) und ein Freigabeurteil (§3, §40):**
GRUEN / GELB / ROT / BLOCKIERT.

Das ist eine **zweite Abnahmeinstanz**. Und die Frage, die niemand beantwortet hat, ist die
einzige, die zaehlt: **was gilt, wenn Evaluator und Erprober verschieden urteilen?** Ohne Antwort
darauf hat der Ledger zwei Wahrheiten, und die Tafel weiss nicht, wohin ein Posten gehoert.

**Meine Aufloesung — sie kostet nichts und rettet beides:** die zwei urteilen ueber **verschiedene
Gegenstaende**.

| | Evaluator | Erprober |
|---|---|---|
| **Prueft** | den **Posten** gegen seinen Auftrag | die **Anwendung** gegen die Bedienung |
| **Frage** | Tut es, was bestellt wurde? | Was passiert, wenn jemand etwas tut, das niemand bestellt hat? |
| **Urteil geht an** | die Tafel (Posten ins Archiv) | Yama (Tor 1) |
| **Kann blockieren** | den Posten | **den Merge**, nicht den Posten |
| **Grundlage** | Abnahmekriterien des Auftrags | §39 Freigabekriterien, Personas, Geraete |

**Damit darf der Erprober ROT sagen, ohne den Zyklus zu zerreissen.** Sein ROT heisst nicht "der
Posten ist schlecht gebaut", sondern "die Anwendung ist in diesem Zustand nicht auslieferbar" —
und das ist eine Aussage, die ohnehin nur Yama treffen kann. **Ein Posten kann sauber abgenommen
und die Anwendung trotzdem rot sein.** Genau das ist heute der Fall, und es ist kein Widerspruch,
sondern die Wahrheit ueber unseren Stand.

---

## Teil 3 — Sein eigener Massstab auf unseren gemessenen Stand angewandt

Das ist die nuetzlichste Uebung des ganzen Papiers, und sie dauert zehn Minuten. §34 gibt die
Schweregrade, §39 die Freigabebedingung. Beides auf das anwenden, was heute **gemessen** vorliegt:

| Befund (gemessen heute) | Schweregrad nach §34 | Begruendung aus dem Papier |
|---|---|---|
| Wandlaenge loest Ecke ⇒ Raum verschwindet, 20,00 m² bleiben plausibel stehen | **P1** | *"falsche Geometrie"*, *"Objektbeziehungen brechen"*, *"unklare automatische Folgeaenderung"* |
| **Touch: 0 Behandlungen** in `app/` | **P1** | *"Touch oder Tastatur unbrauchbar"* — fuer Persona C (mobiler Bauleiter) ist die Anwendung nicht bedienbar |
| 83 Werkzeuge aktivierbar ohne Empfaenger | **P1/P2** | P1 fuer die fachlich zentralen (Heizkoerper, Rohrleitung), sonst P2 |
| Kein Doppelklick, keine Griffe | **P2** | *"unnoetig viele Schritte"*, *"unklare Rueckmeldung"* |
| Kontextmenue existiert nicht | **P2** | §32 des ersten Papiers setzt es als Grundmuster voraus |
| Shortcut-Kollisionen `G` und `S` | **P2** | *"inkonsistente Bedienung"* |
| 16 von 101 Werkzeugen mit Shortcut | **P2** | *"schwer auffindbare Funktion"* |
| `fangKern` nicht angeschlossen, Radius fest in mm | **P2** | *"unklare Rueckmeldung"*, Toleranz aendert sich mit Zoom |

**Daraus folgt ein Satz, den ich nicht abschwaeche:**

> **§39 verlangt fuer GRUEN: "keine offenen P0/P1". Wir haben nach diesem Massstab heute
> mindestens zwei offene P1. Der Hausplaner ist damit nach seinem eigenen Papier heute nicht
> freigabefaehig — er ist GELB, mit zwei benannten Restmaengeln.**

Das ist keine schlechte Nachricht. **Es ist das erste Mal, dass unser Stand eine Note hat, die
nicht ich vergeben habe.** Und es macht die Rangfolge, die ich Yama vorgelegt habe, zu einer
Rechnung statt zu einer Meinung: wer GRUEN will, muss die zwei P1 zuerst abtragen — die Wandecke
und Touch.

---

## Teil 4 — Was jetzt messbar ist und was Leerlauf waere

**Nicht alles in diesem Papier lohnt sich heute.** Der Unterschied ist nicht Aufwand gegen Nutzen,
sondern: **kann die Messung ueberhaupt etwas anderes ergeben als das, was wir schon wissen?**

### Lohnt sich sofort

- **§36 Property-based Tests.** Zufaellige Wandlaengen, Winkel, Konturen, Drag-Distanzen; geprueft
  wird *keine NaN, keine Selbstueberschneidung, kein verlorener Raumbezug, Undo exakt reversibel*.
  **Das laeuft in unserem vorhandenen Testrahmen, ohne Geraete, ohne Browser, ohne Touch.** Und es
  greift genau die Fehlerklasse an, die wir heute gemessen haben: **stille Geometrie-Schaeden.**
  Ein Satz *"die Zahl der Raeume aendert sich nur, wenn eine Wand entfernt oder hinzugefuegt
  wird"* haette Fall B1 gefunden, bevor ihn ein Mensch bemerkt.
  *Bester Nutzen je Aufwand im ganzen Papier.*
- **§12 Tastaturbedienung** — messbar, Tastatur gibt es.
- **§26 Accessibility** — teilweise da (15 `aria-label`, 4 `aria-disabled`, 2 `aria-pressed`),
  also kann die Messung etwas anderes als "null" ergeben.
- **§13 Auswahlwerkzeug** — vollstaendig vorhanden, also pruefbar.

### Waere heute Leerlauf

- **§10 Touchbedienung, §11 Stiftbedienung.** Es gibt **null** Touch-Behandlungen. Neunzehn
  Pruefpunkte und neun Negativtests gegen einen Code, der keinen einzigen Touch-Zweig hat, ergeben
  **einen** Befund, den wir schon haben. *Man misst nicht dreissig Mal dasselbe Nichts.*
- **§7 Geraetematrix / §8 Browsermatrix in voller Breite.** Vier Browser mal vier Geraeteklassen
  fuer einen Funktionsumfang ohne Touch-Code. **Die Matrix laeuft dort, wo das Ergebnis abweichen
  kann** — nicht dort, wo es feststeht.
- **§11 Palm Rejection, Screenreader-Ausgabe.** Playwright kann Touch *nachstellen*; einen Stift
  und einen echten Screenreader kann es nicht. **Ein nachgestellter Touch-Test auf touchlosem Code
  ist kein Beleg, sondern ein gruener Haken ohne Deckung** — und davor warnt §2 Grundsatz 18 selbst.

**Das ist keine Kuerzung des Anspruchs.** Sobald Touch gebaut ist, wird §10 und §11 in voller
Breite gefahren. Vorher ist es Beschaeftigung.

---

## Teil 5 — 22 Dokumente werden eine Liste

Der erste Master-Prompt fordert 10 Dokumente (§40), dieser 12 (§41). **Zusammen 22, mit erheblicher
Ueberschneidung** — `tool-usability-matrix` gegen `tool-test-matrix`, `mouse-touch-keyboard-rules`
gegen drei getrennte Testplaene, zweimal ein Staerken-Schwaechen-Bericht.

**Zwei Dokumentenwelten nebeneinander sind dasselbe Muster wie zwei Register:** sie driften
auseinander, und dann sagt eine von beiden etwas Falsches. Ich lege zusammen:

| bleibt | deckt ab |
|---|---|
| `bedienmodell-110-werkzeuge` (liegt) | usability-current-state, Heuristik, SWOT |
| `bestandsaufnahme-auf50-werkzeuge` (liegt) | tool-usability-matrix, tool-test-matrix |
| `vierte-rolle-erprober-benchmark` (liegt) | usability-test-plan, usability-task-catalog, release-verdict-Form |
| `bedienprobe-befunde` + Befundliste (liegt) | optimization-backlog, strengths-weaknesses-report |
| **`interaktionsmuster-inventar`** (neu) | interaction-pattern-inventory, object-editing-patterns, object-editing-test-matrix |
| **`maus-tastatur-touch-regeln`** (neu) | mouse-touch-keyboard-rules, mouse/touch-pen/keyboard-test-plan |
| **`barrierefreiheit-luecken`** (neu) | accessibility-gaps, accessibility-test-plan |
| **`smarte-interaktion-regeln`** (neu) | smart-interaction-rules, smart-assistance-test-plan |
| **`test-und-regressionsmatrix`** (neu) | automated-test-matrix, regression-test-plan, test-scope |

**Neun statt zweiundzwanzig, davon vier schon geschrieben.** Fuenf fehlen. Die schreibe ich.

---

## Teil 6 — Der Pruefblock je Werkzeug wird uebernommen

Yamas Nachtrag — Werkzeug / Hauptfunktion / Eingabemethoden / Positivtests / Negativtests /
Abhaengigkeiten / Abbruch / Persistenz / Ergebnis — **uebernehme ich unveraendert.** Er ist kurz
genug, dass ihn jemand 101 Mal ausfuellt, und vollstaendig genug, dass das Ausfuellen etwas wert
ist. **Er ersetzt meine Zeile "Probe des Erprobers" nicht, sondern haengt darunter:** der eine Satz
sagt, wozu das Werkzeug gut ist; der Block sagt, wie man es bricht.

---

## Was ich als Naechstes tue — und was ich ausdruecklich nicht tue

**Ich tue:** die fuenf fehlenden Dokumente in den Luecken der Wache, read-only, nur `docs/`.
Beginnend mit `maus-tastatur-touch-regeln`, weil dort die zwei Shortcut-Kollisionen und die
Touch-Null liegen — der groesste Erkenntnisgewinn je Aufwand.

**Ich tue nicht:** einen Posten anlegen (§14), einen Rahmen-Paragraphen schreiben, bevor Yama
entschieden hat, oder den Erprober starten. **Der Rollenbrief ist da, die Rolle ist nicht
beschlossen.**

**Fuenf Punkte liegen bei Yama** — vier von vorhin plus einer:

1. Ecke halten bei Laengenaenderung? *(jetzt als **P1** eingestuft nach seinem eigenen Massstab)*
2. Schema fuer Elektro/PV/Tragwerk erweitern, oder die 20 sichtbar gesperrt lassen?
3. AUF-48 vorziehen?
4. Telemetrie (§22 des ersten Papiers) und Datenmodell — Datenschutz und Tor 2.
5. **Neu: Erprober-Urteil = Empfehlung an Yama, nicht Freigabe der Tafel?** Ohne diese Festlegung
   haben wir zwei Abnahmeinstanzen ohne Schiedsregel.

**Und eine Feststellung, die ich nicht weicher formuliere:** Touch ist nach seinem eigenen
Massstab ein **P1**, und wir haben null Zeilen dafuer. Das ist kein Detail am Rand von AUF-50 —
das ist ein eigenes Vorhaben in der Groessenordnung von AUF-50 selbst. Wer die Anwendung auf einem
Tablet auf der Baustelle sehen will, plant es als solches ein.
