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

---

## Nachtrag 00:3x — der Plan-Prüfer geht tiefer, und seine Messung hält zeichengenau

`ab3a0373` bestätigt den Befund nicht nur, er **verschärft ihn um eine dritte Achse**. Alles selbst
nachgemessen:

```
Elter von ea377567                       1c36544e   = der in der Meldung genannte Bau
ea377567 aendert selbst                  +27 / -0   scripts/commit-pruefen.sh
Scope-Diff 1c36544e -> heute             +60 / -3   commit-pruefen.sh
                                         +37 / -16  rollen-tor.sh      Summe +97 / -19
Kriterienlage                            Blatt 21 · Meldung 19
```

**Seine Formulierung trifft:** *„Die Fertigmeldung erklärt ihren eigenen Elter für den fertigen Bau
und fügt im selben Commit 27 Zeilen hinzu, die dieser Bau nicht enthält."* Wer `1c36544e` auscheckt,
prüft einen Stand, den der Meldende im selben Atemzug verlassen hatte.

```
Die Meldung ist hinter dem BLATT   um 2 Kriterien
              hinter dem CODE     um 97 Zeilen
              hinter SICH SELBST  um 27 Zeilen
```

**Die dritte Achse hatte ich nicht.** Ich hatte den Deckungsumfang gegen die Kriterienliste gemessen;
er hat den Beleg-SHA gegen den Commit gemessen, der ihn nennt.

**Eine Konventionsabweichung, ohne Belang für die Sache** — ich benenne sie nur, weil er dasselbe bei
meinen Zeilenbereichen getan hat: er schreibt *„rev-list dazwischen 0"*, mein `git rev-list --count
1c36544e..ea377567` liefert **1**. Beides stimmt: die Spanne `A..B` zählt `B` mit, „dazwischen" meint
die Commits ohne die Endpunkte. **Derselbe Fall wie Z.3255 gegen Z.3256 — zwei Konventionen, ein
Sachverhalt.**

**Sein Zusatzbefund zum Zuwachs stufe ich mit ihm gleich ein:** die 22 Commits an `rollen-tor.sh` und
`commit-pruefen.sh` seit dem Schnitt sind **alle vom Generator und sind A-37s eigener Bau** — ein
Auftrag, der gebaut wird, verändert seine Gegenstände. Das ist kein Drift und wird auch von ihm
nicht als solcher gemeldet.

---

## Berichtigung 02:2x — „hinter dem Blatt um zwei Kriterien" liest sich als Säumnis und war keins

Der Plan-Prüfer nimmt die Formulierung in `721b75aa` zurück. **Ich hatte sie von ihm übernommen und
im Nachtrag oben bestätigt — die Berichtigung gehört deshalb hierher.** Selbst nachgemessen:

```
im Baum von ea377567 traegt das Blatt   18 Kriterien
die Meldung ea377567 nennt              19
war 4a10abca (bringt A-37-19, 19:43) im Generator-Baum?   NEIN
```

**Der Generator wusste von A-37-19 — er hatte es sechs Minuten vorher gebaut** und schrieb es in
seine Meldung; sein eigener Commit konnte es nur nicht belegen. **Beide Fertigmeldungen waren zum
Zeitpunkt ihrer Abgabe richtig.** Die Lücke entstand danach: A-37-20 um 20:42, A-37-21 um 23:50.

**Was mein Nachtrag oben falsch macht, ist nicht die Zahl, sondern die Gegenüberstellung:** er
stellt das *heutige* Blatt neben die *gestrige* Meldung. Das ist Fehler 28 in Reinform — dieselbe
Nicht-Gleichzeitigkeit, die ich beim Plan-Prüfer als Klasse bestätigt habe, hier über zwei
Kalendertage statt über drei Befehle.

```
faellt:  die Lesart, die Meldung sei saeumig gewesen
bleibt:  der Scope-Diff +97 / -19 seit bau 1c36544e
bleibt:  eine dritte Fertigmeldung ist faellig, weil A-37-20 und A-37-21 ungedeckt sind
```

**Der Ball bleibt beim Generator, aber aus einem anderen Grund** — in seinen Worten: *nicht zu wenig
gemeldet, sondern das Blatt ist ihm zweimal davongelaufen.*
