# Der Commit-Konflikt — gemessen, nicht entschieden

**03.08.2026 · Planner** · *Eskalation des Generators nach §70, aufbereitet für Yama*

---

## Der Widerspruch steht zwischen zwei gleichrangigen Dokumenten

```text
docs/BETRIEBSORDNUNG.md · TEIL 2 §2.1        zuletzt geaendert 02.08.2026
  „Alle Straenge AUTONOM. Pflicht-Stopps als Wartepunkte auf Yama ENTFALLEN.
   Zyklus Entwurf -> Bau -> Pruefung -> COMMIT -> naechster Posten laeuft
   OHNE YAMA in Serie bis Strang-Ende."

CLAUDE.md · GIT-DISZIPLIN                    zuletzt geaendert 01.08.2026
  „Commits nur auf Yamas ausdrueckliches Wort — JEDER Commit, auch reine
   Doku-/Konserven-Commits; Scheiben werden vorbereitet und zur Abnahme
   vorgelegt, gesetzt wird erst nach Freigabe."
```

**Beide können nicht gleichzeitig gelten.** *Das eine sagt: committen ohne Yama. Das andere: kein
Commit ohne Yama.*

**Und die Rangfolge zwischen ihnen ist NICHT geregelt.** CLAUDE.md klärt nur das Verhältnis nach
unten: *„Die Bauordnung steht **unter** BETRIEBSORDNUNG.md/CLAUDE.md — bei Konflikt gelten
diese."* **Untereinander steht nichts.**

## Was das gekostet hat — gemessen

```text
Generator: Aufrufe von commit-pruefen.sh   ->  0
           wartende fertige Scheiben        ->  4   (Z-06 · W-07 · W-06 · Ledger)
           Wartezeit                        ->  16 Stunden
Planner:   Commits am 02.08.                ->  31
```

**Zwei Rollen, dasselbe Regelwerk, entgegengesetztes Verhalten.** *Der Generator hat sich an die
engere Regel gehalten und gewartet. Ich habe nach der weiteren gehandelt, ohne den Konflikt auch
nur zu bemerken.* **Er hat richtig gehandelt, ich nicht** — und ich habe ihm obendrein
vorgeworfen, er komme „nicht durch das Tor".

## Die drei Wege, mit ihrem Preis

```text
A  BETRIEBSORDNUNG gewinnt
   Die Rollen committen auf den eigenen Arbeitszweig, ohne zu fragen. Push, main,
   Tags und upstream bleiben Tor 2 und damit Yamas.
   PREIS: Yama sieht Aenderungen erst im Ledger, nicht vorher. Ein falscher Commit
          ist per `git revert` zurueckdrehbar - kein Verlust, aber ein Eintrag
          in der Historie, den niemand bestellt hat.
   WIRKT: der Generator baut sofort weiter. W-09, S-14 und B11 bleiben richtig,
          verlieren aber ihre Dringlichkeit.

B  CLAUDE.md gewinnt
   Niemand committet ohne Yamas Wort. Fertige Scheiben werden VORGELEGT.
   PREIS: Yama ist bei jedem Commit im Weg - gemessen waeren das gestern 31 Freigaben
          gewesen. Und die Arbeit liegt weiter im Baum, wo sie ein `checkout`
          vernichten kann.
   WIRKT: mein heutiges Verhalten war regelwidrig und muesste enden.

C  Die Trennung wie bei PW-01 — SICHERN gegen VEROEFFENTLICHEN
   Commit auf den eigenen Arbeitszweig = SICHERUNG, jederzeit erlaubt, weil er
   nichts kaputtmachen kann, was nicht schon lokal ist.
   Push · main · Tags · upstream · --force = VEROEFFENTLICHUNG, Tor 2, Yama allein.
   PREIS: eine Regeländerung, die geschrieben und gelesen werden muss.
   WIRKT: loest den Konflikt, statt eine Seite zu ueberstimmen - und benutzt die
          Unterscheidung, die im Projekt schon einmal getragen hat.
```

## Was ich NICHT tue

**Ich entscheide das nicht.** *Es ist Yamas Regelwerk, und die Wahl zwischen A, B und C ist eine
Frage der Kontrolle, keine der Messung.* **Bis zur Antwort gilt die engere Regel (B)** — so wie
der Generator es seit sechzehn Stunden hält.

*Für meine 31 Commits von gestern heißt das: sie stehen im Baum, sie sind gemeldet, und ob sie so
bleiben, entscheidet Yama mit derselben Antwort.*

## Und ein Nebenbefund, der dazugehört

**Der Evaluator hat um 00:35 meine noch nicht vorgelegte Richtigstellung an W-09 mitcommittet** —
er hat dieselbe Datei gegengelesen und dabei alles darin mitgenommen. *Kein Schaden, der Inhalt
stimmt. Aber die Zuschreibung ist falsch, und es ist F-05 (Beifang) in einer Form, die R13
nicht abdeckt: nicht der Index war schuld, sondern dass zwei Rollen dieselbe Datei anfassen.*

**Das ist kein Argument gegen Weg A** — im Gegenteil: **je länger Arbeit unverbucht liegt, desto
wahrscheinlicher fasst sie ein Zweiter an.**
