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

---

## ⚠ NACHTRAG 10.08. — die Frage war falsch gestellt, und der Befund ist größer

**Der Evaluator hat in `a99547b1` eine Ebene höher gemessen als ich, und er hat recht.**

Ich habe gemessen, **dass** die Entscheidung nicht befolgt wird — eine Aussage über die Praxis. Er
hat gemessen, **dass sie gar keine Geltung hat** — eine Aussage über die Autorität:

```text
Erwaehnungen in docs/ARBEITSREGELN.md    0    (selbst nachgemessen)
eigene Geltungsklausel                   keine
ihr eigener Kopf                         "Yamas Frage … gemessen am eigenen Repo,
                                          keine Meinung, Zahlen"
                                          -> eine ANALYSE MIT EMPFEHLUNG, kein Inkraftsetzungsakt
dagegen ARBEITSREGELN §1, Z.10 woertlich:
  "Dieses Dokument ist die EINZIGE VERBINDLICHE QUELLE fuer Arbeitsablauf, Rollen,
   Uebergaben, Qualitaetstore, STATUSFUEHRUNG und Freigaben."
§16                                      benennt docs/STATUS.md NAMENTLICH als Statustraeger
```

> **„Damit setzt eine Datei ohne Autorität eine Regel mit Autorität aus."**
>
> **Meine Folgerung war deshalb falsch.** Ich habe gefragt *„wann setzen wir sie in Kraft"* — die
> Frage davor lautet: **sie ist es nicht.** Bis Yama sie in die Arbeitsregeln aufnimmt, gilt §16
> unverändert, und die zwei Felder **durften** nachgetragen werden. *Ich habe eine Analyse für eine
> Regel genommen und mich darauf berufen, eine geltende Regel nicht zu befolgen. Ein `grep` in
> `ARBEITSREGELN.md` hätte gereicht — zwei Zeilen, wie schon dreimal heute.*
>
> **`V3` fällt damit weg:** „aussetzen, weil nie angekündigt" ist keine Option, wenn nie etwas in
> Kraft war. **`V1`/`V2` brauchen einen Schritt 0:** Aufnahme in die Arbeitsregeln.

### Zwei Präzisierungen zu seinen Zahlen — beide bestätigen ihn

*Ich habe nachgemessen und dabei zunächst zwei Abweichungen gefunden. Beide sind meine.*

```text
"0 Erwaehnungen in STATUS.md"   Ich messe jetzt 8. Bei a99547b1 war es 1 — sein eigener
                                Eintrag. Die 8 stammen aus 874d6331, der REAKTION auf
                                seinen Befund. Seine Zahl war zum Messzeitpunkt richtig.
"keine Geltungsklausel"         Mein grep fand 1 Treffer: "| 87e74ba9 zwei verbindliche
                                Regelwerke |" — eine Tabellenzeile ueber einen Befund.
                                Mein Muster war zu breit. Seine Aussage stimmt.
```

*Ich hätte beinahe einen falschen Widerspruch gemeldet. Dass ich es nicht getan habe, liegt nur
daran, dass ich diesmal den Messzeitpunkt geprüft habe.*

### Und der Befund reicht weiter als die eine Datei

**Zwölf Verweise in ALLEN FÜNF Rollenblättern** — selbst gemessen:

```text
1-planner/5-WAS-ICH-NICHT-DARF.md     "Ein Statusfeld nachziehen | ENTSCHEIDUNG-KONSISTENZ"
2-plan-pruefer/1-AUFTRAG.md           "Keine Buchfuehrung (siehe …)"
3-WAS-ICH-LESE.md   in ALLEN fuenf    als PFLICHTLEKTUERE gefuehrt
4-WAS-ICH-ABLIEFERE.md in allen fuenf verweist darauf
```

**Es ist also kein Einzelfall — es ist strukturell.** Jede Rolle liest die Datei als Pflichtlektüre
und richtet ihr Verhalten danach. *Auch mein Verbot, Statusfelder nachzutragen, stammt von dort.*

**Und dieselbe Frage trifft den ganzen Ordner:**

```text
'rollenkette' in docs/ARBEITSREGELN.md    0
'rollenkette' in CLAUDE.md                0
'rollenkette' in docs/STATUS.md           4    (nachtraeglich)
```

> **§1 beansprucht die Alleinverbindlichkeit ausdrücklich für „Rollen"** — und
> `docs/rollenkette/rollen/` definiert fünf Rollen mit Auftrag, Verboten und Pflichtlektüre. Das ist
> ein direkter Geltungskonflikt, nicht nur bei `ENTSCHEIDUNG-KONSISTENZ.md`.

**Die Trennung, die ich vorschlage — und sie entschärft das meiste:**

```text
FACHLICH   Werkbank, Formelsammlung, 23 Werkzeugblaetter, Materialquellen.
           §1 beansprucht Fachwissen NICHT (nur Ablauf, Rollen, Uebergaben,
           Qualitaetstore, Statusfuehrung, Freigaben — woertlich gemessen).
           Yama hat damit heute ausdruecklich beauftragt. -> KEIN Konflikt.

PROZESSUAL Die fuenf Rollenblaetter und ENTSCHEIDUNG-KONSISTENZ.md.
           Sie regeln Rollen, Uebergaben und Statusfuehrung — genau §1s Gegenstand.
           -> KONFLIKT, solange sie nicht in die Arbeitsregeln aufgenommen sind.
```

*Entlastend: die **fünf Rollen selbst** sind in den Arbeitsregeln verankert (Planner 18, Generator 26,
Evaluator 20, Plan-Prüfer 11, Release-Prüfer 9 Erwähnungen). **Es fehlt nicht die Rolle, es fehlt die
Geltung ihrer ausführlichen Beschreibung.** Wer nach den Rollenblättern arbeitet, arbeitet nicht
gegen die Regeln — er arbeitet nach einer Ausarbeitung, die niemand in Kraft gesetzt hat.*

**Mein eigener Anteil daran, unbequem:** Ich habe heute meine Rolle **aus diesem Ordner** bestätigt,
als du mich gefragt hast. Die Rolle selbst ist gedeckt (§1 nennt den Planner), die Beschreibung, auf
die ich mich berufen habe, ist es nicht. *Das ändert nichts an ihrem Inhalt — er ist gut und er hat
heute mehrfach getragen — aber ich habe „steht im Repo" mit „gilt" verwechselt. Zum elften Mal
dieselbe Klasse an einem Tag, und wieder hat es jemand anderes gefunden.*

```yaml
fehlerklasse: SPEC
verursacher: "niemand einzeln — eine Ausarbeitung ohne Inkraftsetzung"
ballbesitz: yama
mein_anteil: "zwei Meldungen ueber erledigte Posten · eine Weigerung auf falscher
              Rechtsgrundlage · meine Rollenbestaetigung aus einem nicht in Kraft
              gesetzten Ordner"
erledigt_inzwischen: "die zwei Felder sind in 874d6331 nachgetragen (Vertretung Yama)"
zu_entscheiden: "Aufnahme der Rollenblaetter und der Konsistenz-Entscheidung in
                 docs/ARBEITSREGELN.md — oder ausdrueckliche Feststellung, dass
                 docs/rollenkette/ Ausarbeitung ohne Regelrang ist"
```
