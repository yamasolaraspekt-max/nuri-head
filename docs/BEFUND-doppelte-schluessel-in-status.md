# Befund — 17 doppelte Schlüssel in `docs/STATUS.md`, alle mit abweichenden Werten

```yaml
rolle: "generator"
art: "BEFUND, read-only — nichts geaendert, nichts geschnitten"
gemessen_am: "12.08.2026"
stand: 4c1d205b
anlass: "Nebenbefund des Evaluators in e5716bc0 — er zaehlte fuenf betroffene Bloecke und
         ordnete die Bereinigung dem Planner zu. Ich habe die Menge nachgemessen und sie
         ist groesser und schaerfer."
```

> **Die Gefahr ist nicht die Dublette, sondern die ASYMMETRIE.** *Der Evaluator hat sie in
> `e5716bc0` benannt:* **sein Takt-Parser nimmt das ERSTE Vorkommen, YAML nimmt das LETZTE.**
> *Solange beide Werte gleich sind, ist das folgenlos. Gemessen ist **keiner** der 17 Fälle
> folgenlos.*

## Die Messung

```text
yaml-Bloecke in docs/STATUS.md                                144
doppelte Schluessel mit GLEICHEM Wert (folgenlos)               0
doppelte Schluessel mit ABWEICHENDEM Wert                      17
```

**Null folgenlose Fälle.** *Wo ein Schlüssel doppelt steht, weichen die Werte ausnahmslos ab — was
plausibel ist: niemand schreibt denselben Wert zweimal, sondern jemand schreibt einen **neuen**
neben den alten, statt den alten zu ändern.*

## Die vier gefährlichen: `ballbesitz` auf abgeschlossenen Aufträgen

| Auftrag | erste Zeile *(Takt-Parser)* | letzte Zeile *(YAML)* |
|---|---|---|
| **W-01N** | `—  # Kette vollstaendig` | **`generator`** |
| **W-15/1** | `—  # Kette vollstaendig` | **`generator`** |
| **B7** | `—  # Kette vollstaendig` | **`generator (nach B5 und B6 — dieselbe Datei)`** |
| **A-21** | `—  # Kette vollstaendig` | **`generator`** |

**Alle vier sind fertig.** *Die zweite Zeile ist ein Rest aus der Zeit, als der Auftrag beim
Generator lag.* **Ein YAML-Leser sieht vier abgeschlossene Aufträge beim Generator liegen — die
Richtung, die „belegt" meldet, wo frei ist.**

### Nachtrag: EINE der vier ist meine, und das stand hier zuerst falsch

> **Ich hatte unten geschrieben „die 17 fremden Stellen" und die Bereinigung dem Planner
> zugeordnet.** *Für 16 stimmt das. Für **A-21** nicht — die Dublette ist meine.*

**Blockgenau nachgemessen: für jeden der vier Blöcke der Commit gesucht, in dem die Zahl der
`ballbesitz`-Zeilen von 1 auf 2 stieg:**

```text
W-01N    1 -> 2   d59741f9   plan-pruefer
W-15/1   1 -> 2   d59741f9   plan-pruefer
B7       1 -> 2   d59741f9   plan-pruefer
A-21     1 -> 2   869c560d   GENERATOR — mein eigener CODE_FERTIG-Commit
```

**Es ist derselbe Handgriff wie bei W-34:** *der Block trug bereits ein `ballbesitz` vor
`basis_sha:`, und ich habe beim Fertigmelden ein zweites direkt hinter `zustand:` gesetzt, statt das
vorhandene zu ändern.* **Zweimal am selben Tag, und beim zweiten Mal hatte ich den ersten schon
eingeräumt.**

> **Gemessen ist das kein Ausrutscher, sondern ein Muster:** *`65` meiner Commits auf
> `docs/STATUS.md` fügen eine `ballbesitz:`-Zeile hinzu.* **Wo der Block noch keine hatte, ist das
> richtig; wo er eine hatte, entsteht genau diese Dublette.** *Die Regel für mich lautet ab jetzt:
> vor dem Setzen zählen, ob das Feld schon existiert — und dann ändern statt anlegen.*

**Ich habe A-21s Dublette trotzdem NICHT bereinigt**, *obwohl sie meine ist und ich sie bei W-34
selbst entfernt habe.* **Grund: `A-22` ist inzwischen auf genau diese Menge geschnitten
(`ENTWURF`, Plan-Prüfer, Basis `e1a478fb` — dieser Befund).** *Eine Stelle jetzt zu beheben würde
seine Rot-Lage von 17 auf 16 verschieben, während seine DoR läuft.* **Ein bewegtes Ziel ist keine
Prüfgrundlage — dieselbe Zurückhaltung, die der Planner heute bei A-20 geübt hat.** *Sobald A-22
entschieden ist, gehört meine Zeile zu den ersten, die weg können.*

**Geöffnet und gelesen statt gezählt** (Pflichtprüfung 7): *`A-21` trägt `zustand:
BETRIEBSBESTAETIGT`, seine Tafelzeile trägt Ballbesitz `–`, die erste Blockzeile `— # Kette
vollstaendig` und die letzte `generator`.* **Die Tafelzeile und die erste Zeile stimmen überein;
allein die letzte weicht ab, und ausgerechnet sie liest YAML.**

## Die dreizehn übrigen — dieselbe Form, geringere Wirkung

```text
12x  release_vermerk    A-04 · A-08 · A-10 · A-13 · W-01/1 · W-02/1 · W-04/1 · W-05/1
                        W-11/1 · W-21/1   — je ein Vermerk der Stamm-Instanz und einer
                        einer frischen Instanz oder einer Sammel-Kontrolle
 1x  claim_abnahme      W-04/1   plan-pruefer neben evaluator
 1x  letztes_votum      A-05     evaluator 08.08. neben plan-pruefer 05.08.
 1x  eigener_messfehler A-20     zwei Messfehler des Evaluators, beide echt
```

> **Das sind AUFZEICHNUNGEN, keine Statusbehauptungen** — *zwei Release-Läufe, zwei Claims, zwei
> Messfehler.* **Für einen Leser sind sie wertvoll; für einen Parser sind sie eine Falle, weil er
> je nach Werkzeug den einen oder den anderen sieht.** *Sie zu löschen wäre falsch — A-20-4 verbietet
> es wörtlich. Der saubere Weg wäre ein Schlüssel je Vorgang (`release_vermerk_1`,
> `release_vermerk_2`) oder eine Liste.*

## Was ich getan habe und was nicht

```text
GETAN     gemessen, alle 144 Bloecke, und einen Fall geoeffnet statt ihn zu zaehlen.
          MEINE eigene Dublette bei W-34 in 4c1d205b entfernt — sie war meine: der Block
          trug schon ein ballbesitz, ich habe beim Fertigmelden ein zweites eingefuegt.

NICHT     die 17 Stellen angefasst — 16 fremde und EINE eigene (A-21, siehe Nachtrag).
          Datensatzpflege gehoert dem Planner; bei den dreizehn Aufzeichnungen ist die
          richtige Form ohnehin eine Entscheidung und keine Reparatur. Und seit A-22
          auf diese Menge geschnitten ist, waere jede Einzelbehebung ein bewegtes Ziel
          fuer seine DoR — auch meine eigene.
```

> **Ein Hinweis zur Reihenfolge, falls jemand bereinigt:** *bei den vier `ballbesitz`-Fällen ist die
> **erste** Zeile die richtige und die letzte der Rest — das ist genau umgekehrt zu dem, was YAML
> liest.* **Wer mechanisch „die letzte gewinnt" anwendet, macht es schlimmer.**
