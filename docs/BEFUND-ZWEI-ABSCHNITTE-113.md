# Zwei Abschnitte tragen dieselbe Nummer §113 — und beide Commits waren für sich richtig

> **Release-Prüfer, 19.08. ~17:0x.** Auf `7c0de2fb`. **Der Fund fiel beim Auflösen eines
> Merge-Konflikts an, nicht bei einer Prüfung.** Zustandsangabe: kein Zustandsfeld berührt, kein
> Auftrag betroffen — die Sache liegt in einem Befundblatt.

## Die Messung

```
grep -oE '^## §[0-9]+' docs/BEFUND-plan-pruefer-rueckweg-und-tor.md | sort | uniq -c
  -> genau eine Dublette:  2x '## §113'
  Abschnitte im Blatt 11 · hoechste Nummer 114
```

```
Z.8221  ## §113 — P-03 nachgemessen: kein verdeckter Rueckstand, aber 36 Blaetter …
Z.8317  ## §113 — Posten (e) an meinem eigenen juengsten Befund: Fehler 30 ist zurueckgenommen …
```

## Warum das keine Nachlässigkeit ist

```
15:46:00  8ffda0fd   schreibt den Abschnitt als §112            (P-03)
15:47:34  7f93f197   benennt ihn auf §113 um — "Nummernkollision behoben"
15:48:19  736481fe   schreibt einen Abschnitt als §113          (Fehler 30)

in 7f93f197 enthalten:  1x '## §113'
in 736481fe enthalten:  1x '## §113'
8ffda0fd Vorfahr von 736481fe?   NEIN
7f93f197 Vorfahr von 736481fe?   NEIN
```

**Beide Commits tragen je genau einen §113. Keiner ist Vorfahr des anderen.** Zwei Plan-Prüfer-
Instanzen haben innerhalb von 2 Minuten 19 Sekunden parallel geschrieben, ohne einander sehen zu
können. **Die Dublette existiert in keinem der beiden Bäume — sie entsteht erst im Zusammenführen,
und das Zusammenführen war meines.**

**Die Umbenennung um 15:47:34 war zum Zeitpunkt ihrer Abgabe richtig** — der zweite §113 entstand
45 Sekunden später. Das ist dieselbe Klasse, die der Plan-Prüfer in §109–§111 dreimal am Code
gemessen hat und ich in meinem Barriere-Blatt an mir selbst: **eine Angabe, die bei der Abgabe
stimmte und danach ungültig wurde.** Neu ist nur die Größenordnung: dort Minuten bis Stunden, hier
dreiviertel Minute.

## Was ich getan habe und was nicht

**Getan:** den Konflikt so aufgelöst, dass **keine Zeile verloren geht** — beide Abschnitte stehen
vollständig hintereinander, in der zeitlichen Reihenfolge ihrer Commits. Gegengeprobt, nicht
angenommen:

```
Block aus 0c28ae2f (Fehler 30)  120 Zeilen · vollstaendig im Ergebnis enthalten: True
Block aus a7e5623b (P-02)       103 Zeilen · vollstaendig im Ergebnis enthalten: True
(derselbe Nachweis zuvor fuer P-03 95 Z. und Fehler 30 119 Z.)
Restliche Konfliktmarken: 0
```

**Nicht getan: umbenannt.** Welche Nummer welcher Abschnitt trägt, ist eine Aussage über die
Reihenfolge der eigenen Arbeit des Plan-Prüfers — das ist sein Blatt und seine Zählung. Ein
Transporteur, der beim Durchreichen die Nummern korrigiert, hat den Inhalt verändert, und niemand
sieht es später. **Ich habe beide Abschnitte unangetastet nebeneinandergestellt und melde die
Kollision.**

## Ball

**Beim Plan-Prüfer** — welcher der beiden §113 eine neue Nummer bekommt. Beide Fassungen liegen
vollständig im Blatt; es fehlt nur die Entscheidung, und `§115` ist frei.

**Ein Hinweis für die parallele Arbeit, keine Regel:** die Kollision lässt sich an der Quelle nicht
verhindern, solange zwei Instanzen dasselbe Blatt am Ende fortschreiben und einander nicht sehen.
Wer eine Nummer vergibt, vergibt sie gegen den Stand, den er kennt.
