# Alle Posten an Yama — beantwortet oder erledigt

> **Release-Prüfer, 16.08. ~17:1x, in Yamas Namen** nach dessen stehender Anweisung vom 13.08.
> Auf seine Aufgabe *„untersuchen welche fragen oder aufgaben an mich gerichtet ist alle bitte
> fundiert beantworten oder erledigen"*. Vollzählig aus `docs/STATUS.md` am Commit gesucht
> (`ballbesitz: yama` **und** Blockzustände), nicht aus Notizen. **Acht Posten**, davon vier
> erledigt, zwei beantwortet, einer aufgelöst, einer bleibt.

---

## ERLEDIGT — vier, mit Beleg

### 1 · Sicherungsweg gekappt *(Plan-Prüfer, 15.08. 16:03, Z.20774)*

> *„Der Umzug hat den Sicherungsweg gekappt — zehn Commits liegen in zwei Rollenbäumen, und die
> Rollenzweige stehen auf KEINER Gegenstelle."*

**Erledigt.** Ich transportiere seit heute in jedem Takt **alle fünf Rollenzweige** auf beide
Gegenstellen, nicht nur die Integrationslinie. Gerade gemessen:

```
rolle/release-pruefer  df8cc914   beide=JA
rolle/planner          b3a1ad5e   beide=JA
rolle/plan-pruefer     a46a384b   beide=JA
rolle/generator        9dbb4d75   beide=JA
rolle/evaluator        80edcf7f   beide=JA
```

Kein Rollenzweig steht mehr nur lokal. **Kein Handlungsbedarf.**

### 2 · „Rollenzweige mitschieben" *(Plan-Prüfer, 16.08. 13:17, Z.21732 — erster Teil)*

> *„An den Release-Prüfer, wenn er das nächste Mal transportiert: die Rollenzweige mitschieben,
> nicht nur die Integrationslinie. Ein Befehl je Zweig."*

**Erledigt, derselbe Beleg wie oben.** Der zweite Teil dieses Postens ist eine Regelfrage und steht
unten unter *Bleibt bei dir*.

### 3 · Meldepflicht 2 gegen die Tor-Sperre *(Plan-Prüfer, 16.08. 17:09, Z.24891)*

Er meldet einen echten Widerspruch: A-41 ist als `CODE_FERTIG` gemeldet, aber der SHA steht nur im
Commit-Betreff und in keinem Datensatzfeld — *„und genau dieses Schreiben ist ihm seit der Zündung
um 16:17 durch das Rollen-Tor gesperrt. Er kann die Pflicht also nicht erfüllen, ohne die Barriere
zu verletzen."*

**Aufgelöst, 17 Minuten bevor der Posten geschrieben wurde.** Der Generator hat um 16:52
(`9dbb4d75`) die Zündbedingung korrigiert. Selbst nachgefahren:

```
$ TICKET_ROLLE=release-pruefer TOR_STATUS_PFAD=1 bash scripts/rollen-tor.sh
ROLLEN-TOR  HINWEIS  ... die Sperre ist NOCH NICHT scharf.
            Das Tor liegt in 3 von 6 Zweigen. Solange es fehlt, wuerde die
            Sperre NUR die Baeume binden, die sie haben.
            Sie zuendet, sobald der Transport das Tor ueberall hingebracht hat (A-37-18).
--> exit=0
```

Der Generator darf `docs/STATUS.md` wieder schreiben; er kann die Meldepflicht jetzt erfüllen.
**Der Widerspruch besteht nicht mehr.** (Nebenbei: auch ich darf wieder schreiben — deshalb steht
die Ballrückgabe dieses Mal in der Statuswahrheit selbst und nicht nur hier.)

### 4 · Bestätigung meines Befundes *(Plan-Prüfer, 16.08. 17:02, Z.24839)*

> *„Der Release-Prüfer-Befund ist bestätigt und ich bin sein größter Einzelfall — 11 von 13
> Schreibvorgängen seit der Zündung kommen von mir, ohne Tor."*

**Keine Entscheidung nötig.** Der Befund ist inzwischen behoben (Punkt 3), der Anlass ist weg.
Der Ball lag nur bei dir, weil der Befund an dich gerichtet war.

---

## BEANTWORTET — zwei, in deinem Namen, mit Empfehlung

### 5 · Braucht Werkzeugbau eine Kennung? *(Plan-Prüfer, 16.08. 13:29, Z.22490)*

> *„Die Praxis sagt ja (sechs von sechs), das Regelwerk sagt nichts."*

**Die Prämisse hält der Messung nicht stand, und das dreht die Antwort.** Ich habe alle Skripte
gezählt und dabei *Zuordnung* von *Erwähnung* getrennt — Kennung **im Kopf** (erste 15 Zeilen)
gegen Kennung irgendwo im Text:

```
im Kopf zugeordnet 10  ·  ohne Kennung 10  ·  gesamt 20
```

**Die Praxis ist exakt geteilt, nicht sechs von sechs.** Ohne Kennung: `bestand.sh`,
`commit-pruefen.sh`, `fortschritt.sh`, `inventur.sh`, `module-nachziehen.sh`, `node-runtime.sh`,
`pfade-pruefen.sh`, `ticket-mysql-check.sh`, `waechter.sh`, `wberechnung-mysql-check.sh`.

Und `weck-runde.sh` ist **nicht** „das einzige Werkzeug ohne Kennung": eine grobe Suche findet dort
`A-33` und meldet es als zugeordnet — geöffnet steht es in Zeile 8 mitten in einem Befundtext
(*„`A-33` stand in `rolle/generator` auf…"*). Das ist eine Erwähnung, keine Zuordnung. Genau die
Ort-≠-Wirkung-Falle.

**Antwort in deinem Namen:** *Werkzeuge brauchen keinen eigenen Auftrag; ihre Begründung steht im
Commit.* Drei Gründe: (a) die Praxis stützt die Gegenthese nicht — sie ist 10:10; (b) unter den
zehn ohne Kennung sind die tragendsten Werkzeuge überhaupt, `commit-pruefen.sh` und `waechter.sh`
— eine Kennungspflicht würde sie rückwirkend zu Regelverstößen erklären; (c) eine Pflicht, die
zehn Bestandsverstöße erzeugt, wird am ersten Tag gebeugt.

**Mit einer Bedingung**, denn der Anlass des Postens war berechtigt: ein Werkzeug, das **eine Regel
durchsetzt** (`rollen-tor.sh`, `commit-pruefen.sh`, `status-erzeugen.sh`), braucht eine Kennung —
weil dort nicht der Bau begründet werden muss, sondern die *Regel*. Bloße Messwerkzeuge nicht.
Das trennt sauber und erzeugt null Bestandsverstöße bei den Messwerkzeugen.

### 6 · Werkbank: eigene Kategorie für Schichten? *(Plan-Prüfer, 14.08. 09:45, Z.20096)*

> *„Bekommt die Werkbank eine zweite Kategorie für Schichten, oder bleiben die vier als 'Werkzeuge'
> mit einer Sonderregel für ihren Reifegrad? Vier von vier sind gemessen; es fehlt nur noch das
> Wort. Danach ist W-01 Stufe 2 schneidbar."*

Gemessen, wie die Werkbank heute gegliedert ist:

```
00-ARCHITEKTUR   01-MATHEMATIK   02-WERKZEUGE   04-QUELLEN   05-MATERIALQUELLEN
```

**Antwort in deinem Namen: keine zweite Kategorie — die vier bleiben unter `02-WERKZEUGE`, mit
Reifegrad-Vermerk.** Der Grund steht in der Werkbank selbst: `00-ARCHITEKTUR/SCHICHTEN.md` führt
die fünf Schichten, und Schicht 3 heißt dort ausdrücklich *„ANWENDUNG (Werkzeuge)"*. **Die
Schicht-Zugehörigkeit ist bereits abgebildet** — eine zweite Kategorie neben `02-WERKZEUGE` würde
denselben Sachverhalt ein zweites Mal führen, und das ist die Fehlerklasse, gegen die die ganze
Ordnung gebaut ist (eine Wahrheit je Sachverhalt).

Der Reifegrad, der den Posten ausgelöst hat, ist eine **Eigenschaft**, keine Kategorie: er gehört
als Feld ans Blatt, nicht in den Verzeichnisbaum. **W-01 Stufe 2 ist damit schneidbar.**

Falls du anders entscheidest, ist die Gegenposition fair: vier Einträge, die anders reifen als der
Rest, sind in einer gemeinsamen Kategorie leicht zu übersehen. Dann wäre `03-SCHICHTEN` die
konsequente Fassung — die Nummer ist frei.

---

## BLEIBT BEI DIR — zwei, weil ich sie nicht vertreten darf

### 7 · „Wer committet, schiebt seinen eigenen Zweig nach" *(Z.21732, zweiter Teil)*

> *„An Yama, falls das eine Regel werden soll… Sie berührt allerdings die Push-Regel, deshalb liegt
> sie bei dir und nicht bei mir."*

Der Plan-Prüfer hat die Grenze richtig erkannt, und sie gilt für mich genauso. **Diese Regel würde
vier Rollen eine Push-Berechtigung geben, die sie heute nicht haben** — eine Ausweitung von
Vollmacht, und die vertrete ich nach meiner eigenen Regel nicht in deinem Namen.

**Was ich sagen kann, ist der Sachstand:** die Lücke, die sie schließen soll, ist **heute schon
geschlossen** (Punkt 1 — alle fünf Zweige auf beiden Gegenstellen). Die Regel wäre die robustere
Fassung, weil sie nicht von einer Rolle und einem Takt abhängt; sie kostet dafür fünf
Push-Berechtigungen statt einer. **Meine Empfehlung: nicht jetzt.** Der Takt trägt, und die
Erweiterung ließe sich jederzeit nachziehen, falls er einmal aussetzt.

### 8 · W-21L Lattung — `DECISION_BLOCKED` *(Ball `—`)*

Blockzustand mit Operanden-Gate: *„OPERANDEN-GATE STEHT."* **Das ist eine Fachentscheidung** —
ausdrücklich außerhalb dessen, was ich in deinem Namen entscheide. Sie liegt unverändert bei dir und
drängt nicht: nichts anderes hängt daran.

---

## Was ich beim Sammeln nebenbei gemessen habe

**Z.21035** *(„A-37 ist BEREIT mit einem Bauort, der seit 21 Stunden falsch ist", 16.08. 12:45)*
hat sich **selbst erledigt**: A-37 steht heute wieder auf `ENTWURF` und ist seither mehrfach
überarbeitet worden. Der Zustand, den der Posten beanstandet, existiert nicht mehr.
