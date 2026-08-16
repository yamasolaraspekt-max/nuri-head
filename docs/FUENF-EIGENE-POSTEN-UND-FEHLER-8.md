# Die fünf Posten bei mir — und ein achter eigener Fehler

> **Release-Prüfer, 16.08. ~21:4x.** Eingelöste Zusage vom letzten Takt: *„bei mir liegen fünf
> Posten, die ich bis heute nie gezählt habe — die nehme ich mir im nächsten Takt vor."*
> Alle fünf mit `scripts/yama-posten.py release-pruefer` gefunden, jeder einzeln gemessen.

## FEHLER 8 — ich habe von einer Datei auf einen ganzen Baum geschlossen

Der Posten von **19:22** meldet: *„Die Namensfalle ist K2, und die Zahl dazu stimmt nicht:
`ticket-rolle-release` trägt 7460 Dateien."* **Er trifft, und zwar gegen mich.**

```
was ich damals mass:   ls-files scripts/rollen-tor.sh   ->  0
was ich daraus machte: "der leere, abgeloeste Rest aus P2H-09"
was tatsaechlich gilt: ls-files (ALLE)                  ->  7460
                       Dateien auf Platte               -> 24908
```

**Ich habe eine Datei gezählt und einen Baum beschrieben.** Die 0 war richtig — für
`rollen-tor.sh`. Daraus „leer" zu machen, war ein Schluss, den die Messung nicht trug. Diese
Formulierung steht in mindestens zwei meiner Meldungen und hat einen Baum mit 7460 getrackten
Dateien als vernachlässigbar dargestellt.

**Warum das zählt:** ich habe an derselben Stelle vorgeschlagen, ihn zu beseitigen (*„gehört benannt
oder beseitigt"*). Eine Löschempfehlung auf Grundlage einer Einzeldatei-Messung — hätte jemand sie
befolgt, wären 7460 Dateien gegangen.

**Behoben ist die Aussage hier; die Empfehlung nehme ich zurück.** Was mit dem Verzeichnis geschieht,
ist eine Löschentscheidung und gehört Yama — jetzt mit der richtigen Zahl.

## Die fünf Posten, Stand

| | Posten | Stand |
|---|---|---|
| **Z.22218** 14:38 | K6 löst den gemeinsamen Checkout, Transporteur bleibt gesperrt | **ERLEDIGT** — K2 ist gebaut (`rollen-tor.sh:26/29/38`), das Tor meldet die Namensfalle als HINWEIS mit Begründung und lässt durch |
| **Z.24396** 16:20 | W-17/1s eigener `basis_sha` | **ERLEDIGT** — `8faca79c` existiert als Commit, W-17/1 steht auf `BETRIEBSBESTAETIGT` |
| **Z.23531** 15:26 | Das Tor liegt in DREI Fassungen vor | **fast** — heute **zwei**: `d8d6fac1` (generator, evaluator, release-pruefer, Integration) gegen `d6487996` (planner, plan-pruefer). Der Unterschied sind 53 Zeilen (K4 von „abweisen" auf „durchlassen und melden") — **Rückstand, kein Widerspruch**, die beiden haben den Generator-Commit von 21:33 noch nicht |
| **Z.25589** 19:22 | Die Namensfalle ist K2, 7460 Dateien | **TRIFFT** — siehe Fehler 8 oben |
| **Z.19325** 14.08. | Drei Namen, zwei Kopien | **berücksichtigt, nicht behoben** — siehe unten |

## Zu Z.19325 — der Posten hat weiterhin recht

```
backup-private   nurihead.git
fork             nuri-head.git   \
origin           nuri-head.git   /  dieselbe URL
upstream         raminsadid2021/nuri-head.git
```

**Drei Refs, zwei Kopien** — genau wie gemeldet. Mein Handeln ist richtig: ich pushe auf `fork` und
`backup-private`, also auf beide *echten* Kopien, und melde „beide Gegenstellen". **Meine Messung
zählt trotzdem drei**, weil der Takt sie so vorgibt (*„lokaler Ref vs origin/fork/backup-private je
einzeln"*).

Das ist kein Fehler, sondern eine Redundanz — `origin` und `fork` müssen zwangsläufig gleich sein.
**Sie hat aber einen Wert, den ich nicht wegreden will:** liefe eine der beiden Referenzen
auseinander, wäre das ein echter Befund. Solange sie es nicht tun, kostet die dritte Messung nichts.

**`upstream` existiert** und ist in jedem Takt ausdrücklich verboten. Er wird von mir nie berührt —
gemessen: in keinem meiner heutigen Pushes kommt er vor.

## Was ich nicht kann

Auch diese fünf kann ich **nicht schließen** — die Ballrückgabe läuft über `docs/STATUS.md`, und die
ist seit 19:36 nur für den Integrator offen. Zwei sind belegt erledigt, einer trifft gegen mich,
einer ist Rückstand, einer bleibt sachlich richtig.
