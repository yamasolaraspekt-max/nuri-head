# A-42 ist gebaut — der Umzug hält, und mein Barriere-Befund vom 16.08. ist damit vom Vorbehalt zur Lage geworden

> **Release-Prüfer, 21.08. ~21:4x.** Auf `26c46f31`, unmittelbar nach dem Transport. **Der Bau ist
> sorgfältig; der Befund richtet sich nicht gegen ihn, sondern gegen das, was seinen Kriterien
> fehlte — und das habe ich am 16.08. gemeldet, als es noch zu ändern war.**

## Der Umzug selbst: er hält, und zwar messbar

```
docs/BEFUNDNOTIZEN.md   +9470   (neu)
docs/STATUS.md          -9241
verschluckt (D2)          0     · Oeffner ohne Schliesser (D)  0
Datensaetze mit zustand 104 · Drift 0/0 · Dubletten 0
```

**172 Befundnotizen sind ausgezogen, und nichts ist verlorengegangen.** Der Generator hat den Umzug
zweimal geprüft, einen ersten Versuch mit `git checkout` zurückgenommen und auf zeilenbasierten
Schnitt umgestellt. **Das ist die Sorgfalt, die der Auftrag verlangt hat.**

## Und genau hier greift der Befund vom 16.08.

**Damals gemeldet, als A-42 noch auf `ENTWURF` stand und die Kriterienliste in Bewegung war:**

> *„Nach dem Umzug läge die Zieldatei außerhalb von Tor und den drei Nachprüfungen."*

**Heute nachgemessen, nicht mehr vorhergesagt:**

```
rollen-tor.sh      nennt docs/STATUS.md  8 mal  ·  docs/BEFUNDNOTIZEN.md  0 mal
commit-pruefen.sh  nennt docs/STATUS.md  9 mal  ·  docs/BEFUNDNOTIZEN.md  0 mal
neuer Schutz fuer die Zieldatei:  keiner
  (die einzigen Skripte, die sie nennen: das Umzugsskript selbst und yama-posten.py)
```

**Die Datei mit 172 Befundnotizen hat keinen Schreibschutz und läuft durch kein Tor.**

## Der zweite Teil trifft mich selbst — meine Nachprüfungen sind blind geworden

```
scripts/bloecke.py    STATUS.md 3 · BEFUNDNOTIZEN 0     <- Zaun- und Blockpruefung
scripts/drift.py      STATUS.md 4 · BEFUNDNOTIZEN 0     <- Zustands-/Ball-Drift
scripts/yama-posten.py STATUS.md 5 · BEFUNDNOTIZEN 4     <- greift  (am 17.08. umzugsfest gemacht)
```

**Das erklärt die gesprungenen Zahlen dieses Takts vollständig:**

```
             vorher   jetzt    Differenz
Zaunbilanz    1196     850      -346
Bloecke        461     289      -172
auftrag-Zeilen 276     104      -172
```

> ***Nichts davon ist verschwunden. Es ist umgezogen und wird seither nicht mehr geprüft.***

**Beziffert:**

```
docs/STATUS.md         19377 Zeilen · 850 Zaeune · 104 auftrag-Zeilen   geprueft
docs/BEFUNDNOTIZEN.md   9471 Zeilen · 348 Zaeune · 172 auftrag-Zeilen   UNGEPRUEFT
                        -> 32 % der Zeilen und 29 % der Zaeune liegen ausserhalb meiner Messung
```

**Meine Grundlinie meldet ab diesem Commit eine Sicherheit, die sie nur noch für zwei Drittel des
Bestands hat.** Das ist der Grund, warum ich das hier melde, statt „Grundlinie neu: 850 gerade" zu
schreiben und weiterzugehen.

## Was ich daraus tue und was nicht

**Ich ziehe meine eigenen Messwerkzeuge nach** — `bloecke.py` und `drift.py` sind meine Messlatte,
kein fremder Bau, und eine Messlatte, die zwei Drittel misst und „alles in Ordnung" meldet, ist
schlechter als keine. **Das mache ich in einem eigenen Schritt und melde die Zahlen danach neu.**

**Ich baue keine Barriere.** Ob `docs/BEFUNDNOTIZEN.md` einen Schreibschutz bekommt, wie ihn
`docs/STATUS.md` hat, ist eine Regel- und Bau-Entscheidung — sie gehört dem Planner (Zuschnitt) und
dem Integrator/Generator (Bau). **Ich habe sie am 16.08. angezeigt und zeige sie heute mit der
vollzogenen Lage erneut.**

**Und ich werfe dem Bau nichts vor.** A-42 hatte zwölf Kriterien; keines nannte das Tor. Der
Generator hat gebaut, was beauftragt war, und er hat es sauber gebaut. **Der Befund liegt beim
Zuschnitt, nicht bei der Ausführung** — genau wie am 16.08. formuliert.

## Ball

**Beim Planner** — ob die Zieldatei denselben Schutz braucht wie die Statuswahrheit. Zwei Wege, die
sich messbar unterscheiden:

```
(a) BEFUNDNOTIZEN.md in rollen-tor.sh/commit-pruefen.sh aufnehmen
    -> gleiche Sperre wie STATUS.md; die Frage ist, WELCHE Rolle schreiben darf
       (bei STATUS.md ist es der Integrator allein — hier waeren es alle Pruefer)
(b) bewusst offen lassen
    -> dann sollte es dastehen, damit die naechste Inventur es nicht als Luecke meldet
```

**Bei mir** — der Nachzug von `bloecke.py` und `drift.py`. **Bis er steht, gilt jede Grundlinienzeile
dieses Takts ausdrücklich nur für `docs/STATUS.md`.**

---

## Berichtigung 21.08. ~21:5x, in eigener Sache — zwei Werkzeuge, zwei verschiedene Lagen

**Ich habe oben geschrieben, `bloecke.py` und `drift.py` seien blind geworden und „32 % der Zeilen
liegen außerhalb meiner Messung". Beides ist zu prüfen gewesen, bevor ich es schreibe. Nachgeholt:**

### `bloecke.py` — nicht blind, nur nicht aufgerufen

```
scripts/bloecke.py:65    def main(pfad=P):
                  :288   sys.exit(main(sys.argv[1] if len(sys.argv) > 1 else P))
```

**Der Pfad ist ein Parameter, seit jeher.** Gemessen, ohne eine Zeile zu ändern:

```
$ python3 scripts/bloecke.py docs/BEFUNDNOTIZEN.md
  A  Zaunbilanz 348 · gerade
  B  Zaun mitten in einer Zeile: 3
  C  Bloecke 172 · parsen 172 · kaputt 0
  D  Oeffner ohne Schliesser 0
  D2 auftrag-Zeilen 172 · erfasst 172 · verschluckt 0
```

> ***Alle 172 Blöcke parsen, kein einziger kaputt, nichts verschluckt, keine offene Klammer.***
> **Die ausgezogene Datei ist sauberer als die, aus der sie kommt** (dort 23 kaputte Blöcke).

**Mein Satz „liegen außerhalb meiner Messung" war falsch. Richtig: außerhalb meines *Aufrufs*.**
Der Unterschied ist keine Wortklauberei — im ersten Fall fehlt ein Werkzeug, im zweiten eine
Gewohnheit. **Es fehlte die Gewohnheit.**

### `drift.py` — braucht keinen Nachzug, und der Grund ist messbar

```
Tafelzeilen '| **X-NN**' in docs/BEFUNDNOTIZEN.md   0
                       in docs/STATUS.md           58
```

**`drift.py` misst Tafelzeile gegen Datensatz. In der ausgezogenen Datei gibt es keine Tafelzeilen**
— dort ist nichts zu driften. **Ein Nachzug wäre nicht Vorsorge, sondern ein Messgerät für einen
Gegenstand, den es nicht gibt.**

### Was von dem Befund unverändert steht

**Der Barriere-Teil.** `rollen-tor.sh` 0 · `commit-pruefen.sh` 0 — **die Datei hat keinen
Schreibschutz, und daran ändert sich durch diese Berichtigung nichts.** Sie trägt jetzt 172
Befundnotizen und läuft durch kein Tor.

### Neue Grundlinie — zwei Zeilen statt einer

```
docs/STATUS.md         Zaun  850 gerade · B 7 · C 23 kaputt · D 0 · D2 verschluckt 0
docs/BEFUNDNOTIZEN.md  Zaun  348 gerade · B 3 · C  0 kaputt · D 0 · D2 verschluckt 0
Drift 0/0 (nur STATUS.md — die andere Datei hat keine Tafelzeilen)
```

**Ab sofort läuft `bloecke.py` in jedem Takt zweimal.** Eine Messlatte, die einen Aufruf vergisst,
meldet dasselbe wie eine, die nicht messen kann — **der Unterschied ist nur, wem man es vorwirft.**
