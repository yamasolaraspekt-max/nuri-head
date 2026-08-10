# Befund — die Entscheidung gegen Abgleich-Buchführung liegt im Repo und ist nicht in Kraft

```yaml
melder: planner
art: "BEFUND — Antwort auf ac07a1c5, aber nicht die verlangte Handlung"
anlass: "Evaluator ac07a1c5: zwei Posten stehen als 'bei Yama' und sind erledigt.
         Verlangt: der Planner traegt sie nach (§16). Ich trage sie NICHT nach."
gemessen_am: "2026-08-10 abends"
ballbesitz: "yama (Regelkollision) — die Ableitung selbst waere ein Bauauftrag"
```

## Der gemeldete Sachverhalt ist richtig — selbst nachgemessen

```text
85b03d23  (A-08 Bau)      IN fork/main
b2f8c44b  (A-08 §10)      IN fork/main
c3d52f09  (A-04 Bau)      IN fork/main
```

**Beide Aufträge sind veröffentlicht.** Und die Statusseite sagt etwas anderes:

```text
A-08   Tafel: VEROEFFENTLICHT          Auftragsdatensatz Z.989: zustand RELEASE_FREI
A-04   Tafel: RELEASE_FREI / Yama      Auftragsdatensatz Z.895/896: RELEASE_FREI / yama
```

**Die Folge war real, nicht theoretisch:** Yama hat gefragt, wer A-08 freigeben soll — weil die
Statusseite ihm eine seit Stunden erledigte Aufgabe zuwies. *Und ich habe ihm zweimal denselben
Posten als offen gemeldet. Der Evaluator zählt es als vierten Fall dieser Klasse; einer davon ist
meiner.*

## Warum ich es trotzdem nicht nachtrage

**`docs/rollenkette/ENTSCHEIDUNG-KONSISTENZ.md` streicht genau diese Handlung.** Wörtlich:

```text
GESTRICHEN
  "Kein Ballbesitz-Feld mehr. Wer dran ist, ergibt sich aus dem letzten
   Uebergabestueck. Ein Feld, das jemand von Hand nachzieht, driftet — belegt."
  "Keine Tafel-Nachfuehrungs-Commits. Der Zustand ergibt sich aus den vorhandenen
   Uebergabestuecken. Wenn C vorliegt und D fehlt, liegt es beim Release-Pruefer.
   Das muss niemand aufschreiben."
NEU
  "Buchfuehrung wird ABGELEITET, nicht gefuehrt. Ein Werkzeug liest die
   Uebergabestuecke und erzeugt die Tafel. Kein Mensch traegt einen Zustand zweimal ein."
```

**Mit der Begründung, die aus dem eigenen Verlauf gemessen ist:**

```text
32 Buchfuehrungs-Commits  ->  NULL gefundene Fehler
20 sachliche Pruefungen   ->  20 echte Fehler, darunter der falsche Index in A-07-4
Musterbeispiel a9c80f78: "Ballbesitz auf planner korrigiert — mein Votum gab den Ball
zurueck, das Feld stand noch auf plan-pruefer." Zwei Orte in Uebereinstimmung gebracht,
die NIE haetten getrennt sein duerfen.
```

> **Würde ich die zwei Felder jetzt von Hand richtigstellen, wäre das exakt der Commit, den diese
> Entscheidung als wertlos gestrichen hat.** Und er käme in einer Woche wieder, weil die Ursache
> nicht der falsche Feldinhalt ist, sondern **dass es zwei Orte für einen Zustand gibt.**

## Der eigentliche Befund — die Entscheidung ist nicht in Kraft

```text
im Repo seit          10.08. 19:11 — als Teil von 212 Dateien im Commit 1e933a64
                      "SICHERUNG (Vertretung Yama): docs/rollenkette"
Buchfuehrungs-Commits seither    6   (Tafel 3x, Zaehler 2x, plus e563fb7d um 20:12)
Commits mit Produktivcode        2
Verhaeltnis                      3 : 1
```

**Das Verhältnis 3:1 ist dasselbe, das die Entscheidung als Problem diagnostiziert (32:9 ≈ 3,6:1).**
In der ersten Stunde nach ihrer Ankunft wurde sie sechsmal übergangen.

> **Und der Grund ist kein Ungehorsam, sondern der Weg ins Repo:** Die Entscheidung kam **in einem
> Sicherungs-Commit mit 212 Dateien**, nicht als Regeländerung mit Ankündigung. Niemand hat sie
> gelesen, weil niemand wusste, dass es sie gibt.
>
> *Die Entscheidung sagt selbst: „Wer nach einer Regeländerung startet, liest sie." **Diese
> Regeländerung wurde nie angekündigt** — sie ist als Beifang einer Rettungsaktion angekommen.
> Das ist keine Schuld des Sichernden: er hat gerettet, was ungesichert war, und das war richtig.*

## Was ich vorschlage — drei Wege, die Wahl ist Yamas

```text
V1  IN KRAFT SETZEN, dann ableiten.
    Die Entscheidung wird als Regeländerung angekuendigt; die Tafel-Nachfuehrung
    endet ab dann. Danach ein Bauauftrag fuer das ableitende Werkzeug
    (liest die Uebergabestuecke, erzeugt die Tafel).
    KOSTET: einen Bauauftrag. SPART: die 3:1-Quote.
    RISIKO: bis das Werkzeug steht, ist die Tafel veraltet UND wird nicht nachgefuehrt.
            Genau die Luecke, in der Yama heute zweimal falsch informiert wurde.

V2  ABLEITEN ZUERST, dann in Kraft setzen.
    Werkzeug bauen, dann die Handnachfuehrung beenden. Keine Luecke.
    KOSTET: die Nachfuehrung laeuft noch etwas weiter (mit ihrer Fehlerquote).

V3  AUSSETZEN.
    Die Entscheidung wurde nie angekuendigt, also gilt sie als nicht gefasst;
    Tafel-Nachfuehrung bleibt bis auf Weiteres, jemand ist ausdruecklich zustaendig.
    KOSTET: die gemessene Nullwert-Quote bleibt.
    EHRLICH: das ist die Wahl, die niemand aufschreiben will, aber sie ist zulaessig —
             eine Regel, die niemand kannte, hat niemand gebrochen.
```

**Ich empfehle V2.** *Die Lücke in V1 ist genau der Schaden, der heute schon eingetreten ist — man
würde die Ursache abschalten, bevor der Ersatz steht. V3 lässt eine gemessene Nullwert-Praxis
laufen, aber wenigstens ohne Loch. V2 kostet am wenigsten und hat kein Fenster, in dem niemand
zuständig ist.*

## Was ich NICHT entscheide

- **Ob die zwei Felder jetzt trotzdem gerichtet werden.** Wenn Yama V3 wählt oder eine
  Übergangsanweisung gibt, mache ich es sofort — es sind zwei Zeilen.
- **Ob §16 durch diese Entscheidung berührt ist.** §16 sagt „EINE Statuswahrheit"; die Entscheidung
  sagt, wie sie entsteht. Ob das ein Widerspruch oder eine Präzisierung ist, gehört nicht dem
  Planner.
- **Die Testdaten aus `ac07a1c5`** (user 268/269, doc 36). Der Vorschlag des Evaluators — Nutzer
  entfernen, **Dokument behalten**, weil doc 36 das einzige `HausplanerDocument` in
  `ticket_testing` und Gegenstand jeder Sichtprobe am Leer-Pfad ist — ist fachlich richtig und
  braucht nach §15 einen eigenen Auftrag plus Yamas Freigabe. *Ich habe nichts angefasst.*

```yaml
fehlerklasse: PROZESS
verursacher: "niemand einzeln — eine Regelaenderung ohne Ankuendigung"
ballbesitz: yama
mein_anteil: "zwei Meldungen an Yama ueber Posten, die erledigt waren"
```
