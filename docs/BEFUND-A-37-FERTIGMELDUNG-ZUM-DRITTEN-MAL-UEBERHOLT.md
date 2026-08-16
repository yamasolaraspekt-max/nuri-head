# A-37s Fertigmeldung ist zum dritten Mal überholt — und diesmal hat es niemand gemeldet

> **Release-Prüfer, 17.08. ~00:0x.** Auf `6c4c6fa7`. **Zustandsangabe statt Dringlichkeitsvermerk:**
> A-37 steht auf `CODE_FERTIG`, hat keinen Abnahme-Ball, und niemand läuft gerade hinein. Wer
> abnimmt, sollte das hier vorher lesen.

## Die Zeitachse

```
19:38   Fertigmeldung fb59f6cc — 18 Kriterien, Bau 97f1dd00
19:43   A-37-19 kommt ins Blatt (4a10abca)          -> Meldung ueberholt (1. Mal)
20:01   Fertigmeldung ea377567 — "19 von 19", Bau 1c36544e
20:42   A-37-20 kommt ins Blatt (1403e348)          -> Meldung ueberholt (2. Mal)
23:50   A-37-21 kommt ins Blatt (b6a79a66)          -> Meldung ueberholt (3. Mal)

heute   21 Kriterien im Blatt · letzte Fertigmeldung deckt 19
```

**Der Generator hat den ersten Fall selbst gemeldet und die Lehre daraus formuliert**, wörtlich in
`ea377567`:

> *„Bei einem Zustandswechsel ist der Beleg-SHA kein Beiwerk, sondern der halbe Inhalt. Ändert sich
> der Bau, ist die Meldung überholt, auch wenn das Wort gleich bleibt."*

**Die zweite und dritte Wiederholung laufen andersherum** — nicht der Bau ändert sich, sondern das
Blatt. Die Meldung wird von der anderen Seite überholt, und die Lehre greift trotzdem: eine
unveränderte Zustandsangabe mit veraltetem Deckungsumfang sieht gültig aus und ist es nicht.

## Was von den zwei neuen Kriterien gebaut ist

```
A-37-21  "DIE ABHAENGIGKEIT DES TORS MUSS DEKLARIERT SEIN"
         js-yaml in package.json        0
         letzter Generator-Commit       23:37  — VOR dem Kriterium (23:50)
         -> nachweislich UNGEBAUT

A-37-20  "DIE RUECKGABEWERTE MUESSEN IM BAU AUCH VERGEBEN WERDEN"
         verlangt je Ursache einen erreichbaren Rueckgabewert
         gemessen in rollen-tor.sh:  exit 0 (5x) · exit 1 (3x) · exit 5 · exit 6
         exit 2, 3, 4 als Anweisung:  null
```

**Bei A-37-20 habe ich die Stellen geöffnet, bevor ich etwas daraus mache** — und gut, dass ich es
getan habe: der Code begründet die Abweichung selbst.

```
Z.161  # Rueckgabe 5, NICHT 1. Die Zahl kommt aus der Codetabelle des Auftrags
       # (berichtigt 16.08. nach DoR Runde 3) und nicht aus meiner Wahl
```

**Die Codes wurden umnummeriert; 5 und 6 sind die neuen 2 und 3.** A-37-20 ist damit adressiert.
**Ob es erfüllt ist, entscheide ich nicht** — das Kriterium verlangt je Ursache einen Lauf mit
Rohausgabe und `echo $?`, und diese Läufe zu fahren ist Bau- und Abnahmearbeit, nicht meine. Ein
Nebenbefund fällt dabei ab, den ich nur benenne: **Zeile 84 des Skripts zitiert für denselben Fall
noch `exit 3`, während der Code `exit 5` vergibt** — Kommentar und Code im selben Skript
auseinander.

## Was das für die Abnahme heißt

```
wer A-37 heute abnimmt, prueft 21 Kriterien
die gueltige Fertigmeldung deckt              19
davon nachweislich ungebaut                    1  (A-37-21)
```

**Beim ersten Mal habe ich genau das gemessen und den Evaluator gewarnt, bevor er hineinlief** — der
Generator schreibt es selbst in seine Meldung. Dieselbe Warnung, dritter Fall, gleicher Adressat.

**Was fehlt, ist wieder ein Commit, kein Bau** — für A-37-20. **Für A-37-21 fehlt beides.**

## Rollengrenze

**Ich melde weder CODE_FERTIG neu noch nehme ich ab.** Die Fertigmeldung gehört dem Generator, die
Abnahme dem Evaluator, das Blatt dem Planner. Gemessen ist der Deckungsumfang; entschieden ist
nichts.

**Und die Beobachtung, die über A-37 hinausgeht:** ein Blatt, das nach der Fertigmeldung weiterwächst,
entwertet sie still. Dreimal an einem Auftrag in fünf Stunden. **Das ist keine Nachlässigkeit einer
Rolle** — jedes einzelne der drei Kriterien war ein guter Fund, und A-37-21 stammt aus einer Kette,
die Generator und Plan-Prüfer heute Nacht sauber aufgeklärt haben. Es ist eine Lücke im Ablauf: **es
gibt keinen Halt, der beim Ergänzen eines Kriteriums die zugehörige Fertigmeldung anfasst.**
