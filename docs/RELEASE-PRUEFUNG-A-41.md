# §10 Release-Prüfung A-41 — Bau grün, Freigabe hängt an einem Einzeiler

> **Release-Prüfer, 16.08. ~17:3x.** Abnahme-Commit `e0df30d7` (Evaluator, 17:23, zwölf von zwölf).
> Alles selbst am Commit gemessen, nichts aus dem Votum übernommen.

## Ergebnis vorweg

**Der Bau ist freigabefähig.** Zwölf Kriterien halten der Gegenlesung stand, alle einschlägigen Tore
laufen grün, die Nicht-Ziele sind sauber. **`RELEASE_FREI` erteile ich trotzdem noch nicht** — es
fehlt ein Zustands-Commit im Wortlaut, und ausgerechnet dieser Auftrag ist derjenige, der diesen
Wortlaut einführt. Behebbar in einer Zeile durch den Evaluator.

## 1 · Messtisch-Gegenlesung — die Form lesen, nicht die Null glauben

Acht Formen geprüft:

```
(A) yaml 'urteil:'                        2      (D) 'KENNUNG: GRUEN'                0
(B) Tabelle 'grün'                        0      (E) nur -N ohne Praefix             1
(B') Tabelle 'erfüllt'                    0      (F) Vorher/Nachher 'ROT | grün'     1
(C) Textblock 'KENNUNG ERFUELLT'          0      (G) Kennung + Fliesstext            9
```

Sechs Muster melden 0 oder zu wenig. **Gelesen statt geglaubt:** das Votum steht als Fließtext in
`votum_evaluator_r1:`, jedes Kriterium mit Kennung und **Messwert statt Urteilswort** — Form (G).

**Ein eigener Fehlalarm, gefangen bevor er zum Befund wurde:** meine Gegenprobe meldete
`A-41-11 FEHLT`. Geöffnet steht `A-41-11` am **Zeilenende**, der Messwert folgt nach dem Umbruch —
mein Muster verlangte ein Leerzeichen dahinter. Ohne diesen Zwang: **12 von 12 belegt.** Hätte ich
die Null geglaubt, hätte ich eine saubere Abnahme wegen meines eigenen Musters gesperrt.

## 2 · Scope, und daraus die Torwahl

Die acht Commits der Baureihe **je einzeln** mit `--name-only` geprüft, nicht als Bereichsdiff:

```
1e342d53 b585d335 2e9cf127 ccdfd7b6 1013e254 253a51d7 16c5b9d2 f19557c8
   -> alle acht: ausschliesslich scripts/status-erzeugen.sh
app 0 · resources 0 · database 0 · routes 0 · config 0 · public 0 · bootstrap 0 · tests 0
commit-pruefen.sh 0 · rollen-tor.sh 0
```

**Auch hier ein eigener Messfehler, berichtigt:** mein erster Lauf nutzte
`git diff 1e342d53~1 f19557c8` und meldete `rollen-tor.sh: 1`. Das misst den **Bereich**, nicht die
acht Commits — dazwischen liegen fremde. Die Einzelmessung ist die richtige, und der Evaluator hatte
sie genau so gefahren. A-41-11 hält.

**Torwahl begründet:** Liefergegenstand ist ein 703-Zeilen-Werkzeug ohne Produktivcode. Migrationen,
Bundle, ENV, Mandanten- und Datenschutzgrenzen haben keinen Bezug — sie sind nicht „übersprungen",
sondern gegenstandslos. Einschlägig sind Syntax, realer Lauf, Idempotenz, Rückgabewerte,
Nicht-Ziele. Die beiden Haustore habe ich **trotzdem** gefahren, weil sie billig sind und
Unabhängigkeit belegen.

## 3 · Die Tore, selbst gefahren

```
bash -n scripts/status-erzeugen.sh        sauber, exit 0
703 Zeilen · Modus 755
Schreibbefehl auf docs/STATUS.md          0 Treffer   (das Skript LIEST nur)
realer Lauf --tafel                       exit 1 = "RUECKGABE 1 — erzeugt, MIT Meldungen"
docs/STATUS.md vorher/nachher             5ce1de67 == 5ce1de67   UNVERAENDERT
git status im Baum danach                 0
npm run tsc:hausplaner                    exit 0
npm run test:hausplaner                   exit 0 · tests 1763 · pass 1763 · fail 0
```

Der `exit 1` ist **kein Fehler**, sondern der definierte Rückgabewert — geöffnet statt gedeutet.

## 4 · Befund 1 — der Abnahme-Zustandswechsel fehlt im Wortlaut *(Freigabehindernis)*

Der reale Lauf des Werkzeugs meldet:

```
A-41   CODE_FERTIG   evaluator   e1cc61ef   16.08 16:52
```

**Der Datensatz sagt `ABGENOMMEN`, das Werkzeug sagt `CODE_FERTIG`.** Ursache gemessen: der
Abnahme-Commit trägt den Betreff `evaluator: A-41 ABGENOMMEN — …`. Der festgeschriebene Wortlaut
(`status-erzeugen.sh:151-157`) verlangt `zustand: <Kennung> · <ZUSTAND> · <Ball> · <Beleg>`, mit
optionaler Rollenmarke davor. Der Betreff trifft ihn nicht.

**Das ist kein Baumangel — das Werkzeug tut genau, was es soll.** Es ist ein Verfahrensschritt, der
fehlt, und er ist besonders sichtbar, weil A-41 diesen Wortlaut selbst einführt: *der erste reale
Anwendungsfall zeigt die Lücke am eigenen Auftrag.*

**Warum das die Freigabe hält:** §10 verlangt die lückenlose Statuskette. Im Commit-Log springt A-41
sonst von `CODE_FERTIG` auf `RELEASE_FREI` — `ABGENOMMEN` käme darin nie vor. Ich könnte das mit
meinem eigenen Übergangscommit überdecken; das wäre stilles Überbrücken einer Lücke, und genau
dagegen ist das Werkzeug gebaut.

**Was der Evaluator braucht — ein Commit, kein Bau:**

```
evaluator: zustand: A-41 · ABGENOMMEN · release-pruefer · abnahme f19557c8
```

Danach setze ich `RELEASE_FREI` im selben Wortlaut, und die Kette ist im Log geschlossen.

## 5 · Befund 2 — `dor_beleg: "steht aus"` *(gemeldet, nicht sperrend)*

Der A-41-Datensatz trägt `dor_beleg: "steht aus"`, auch am aktuellen Stand. **Es gibt keine
DoR-Erteilung für A-41.**

Diese Zahl habe ich dreimal messen müssen, und die ersten beiden waren falsch: eine grobe Suche
meldete **13** Treffer, ein engeres Muster **3**. **Alle geöffnet** — die drei betreffen A-37,
W-17/1 und einen Fließtext, **keiner ist eine A-41-DoR**. Belastbar sind **0**.

**Warum das trotzdem nicht sperrt:** Der Datensatz legt es selbst offen (*„Der Bau lief vor dem
Schnitt, deshalb überholt dieser Ballbesitz die übliche Reihenfolge und sagt es ausdrücklich, statt
sie zu verschweigen"*), die Regelgrundlage ist Yamas eigene Anordnung vom 16.08. zu `e521bd98`, und
§10 nennt die DoR nicht unter den Prüfpunkten vor `RELEASE_FREI`. Ich melde es als das, was es ist:
eine übersprungene Stufe der Zustandskette, offengelegt statt verdeckt. **Ob daraus eine Nachholung
wird, gehört dem Plan-Prüfer und Yama, nicht mir.**

## 6 · Was ich bezeugen kann und was nicht

**Bezeugt:** Scope, Nicht-Ziele, Syntax, Idempotenz, Rückgabewert, beide Haustore — alles am Commit
und selbst gefahren.

**Nicht bezeugt:** die inhaltliche Richtigkeit der Erstbefüllung über alle sechs Zweige. Der
Evaluator hat sie mit dreizehn verdrängten Ständen belegt; ich habe die Zahl **nicht** nachgezählt,
weil sie seine Abnahme ist und nicht meine Release-Prüfung. Ebenso ungeprüft: der `vendor`-Mangel in
seinem Rollenbaum, den er selbst als Ausstattungsmangel offen meldet.
