# Baubericht B6 — eine Summe braucht eine Erhebung, keine Sammlung

```yaml
auftrag: "B6"
rolle: "generator"
blatt: docs/auftraege/aktiv/B6-summe-braucht-erhebung.md
basis_sha: 1e09280d
gebaut_auf: 30457e2b
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **Auch dieser Bericht steht unter beiden Regeln.** *Jede Zahl mit einem Befund führt ihre
> Trefferzeilen mit (B5), und jede Summe nennt ihre Menge (B6).*

## Was gebaut wurde

Die siebte Barriere warnt, wenn eine Botschaft ein **Summenwort mit einer Zahl** trägt und **keine
Menge** nennt. Die Regel steht als **Abschnitt 18c** in `docs/ARBEITSREGELN.md`, ausdrücklich mit der
Abgrenzung zu B5. Und die Zeile steht im Prüfweg **aller fünf Rollen**, direkt unter der von B5.

**Form und Stelle sind von B5 übernommen, nichts neu erfunden** — Warnung statt Abbruch, nach dem
Fehler-Riegel, ohne `FEHLER` und ohne `exit`. *Das war die ausdrückliche Wiederverwendungsansage des
Blattes.*

---

## B6-1 · Die Warnung feuert

**Rot-Lage aus dem Blatt, unmittelbar vor der ersten Änderung gemessen:**

```text
grep -cE 'B6|Summe.*Menge' scripts/commit-pruefen.sh   ->  0
(kein Treffer — A-10: das leere Ergebnis wird gemeldet, nicht überschrieben)
```

**Jetzt, mit Trefferzeilen:**

```text
grep -cE 'B6|Summe.*Menge' scripts/commit-pruefen.sh   ->  9

543:# ── B6: EINE SUMME BRAUCHT EINE ERHEBUNG, KEINE SAMMLUNG ──────────────
551:#   B6  nie gesagt, WORUEBER gezaehlt wird -> Gegenmittel: die Menge zuerst benennen
555:# ERLAUBT  "acht Bausteine, zusammen 1.593:    Summe MIT Menge
557:# VERBOTEN "ueber 640 Zeilen Prozessebene"     Summe OHNE Menge
564:B6_SUMMENWORT='…'
565:B6_MENGE='…'
566:if printf '%s' "$BOTSCHAFT" | grep -qE "$B6_SUMMENWORT" \
567:   && ! printf '%s' "$BOTSCHAFT" | grep -qE "$B6_MENGE"; then
568:  echo "B6-WARNUNG  Summenbehauptung ohne genannte Menge (Pfad, Muster, Aufzaehlung)." >&2
```

**Die Mechanik:** ein **Summenwort mit einer Zahl in Reichweite** löst aus (`insgesamt`, `zusammen`,
`Summe`, `Gesamtzahl`, sowie `über/rund/etwa/ca. N Zeilen|Dateien|Bausteine|Module|Komponenten|
Einträge`), eine **Mengennennung** hält zurück (Pfad, Dateiendung, `grep`/`find`, die Wörter Menge ·
Erhebung · erhoben · Pfad · Muster, oder eine Aufzählung mit `·`). *Die Zahl-in-Reichweite-Bedingung
steht ausdrücklich in der Kantenliste des Blattes — sonst fängt „insgesamt" jeden Fließtext.*

## B6-2 · Sie feuert NICHT bei einer Summe MIT Menge

**Probe 1 — der Vorfall selbst, wörtlich:**

```text
$ … commit-pruefen.sh "ueber 640 Zeilen Prozessebene gefunden" a.txt
B6-WARNUNG  Summenbehauptung ohne genannte Menge (Pfad, Muster, Aufzaehlung).
f032f1e generator: ueber 640 Zeilen Prozessebene gefunden
```

**Probe 2 — dieselbe Summe, aber mit Menge:**

```text
$ … commit-pruefen.sh "acht Bausteine, zusammen 1.593 Zeilen: StartView 267 ·
                       ConfigWizard 271 · EngineFlaeche 196" a.txt
B6-WARNUNG: 0        B5-WARNUNG: 0
d712905 generator: acht Bausteine, zusammen 1.593 Zeilen: StartView 267 · …
```

*Der Commit läuft beide Male durch. **Genau die zwei Botschaften aus dem Blatt, und sie verhalten
sich verschieden — das ist der Gegenbeleg, ohne den die Barriere eine Belästigung wäre.***

## B6-3 · Sie feuert NICHT bei Einzelzahlen

Die drei Formen aus dem Blatt, **jede einzeln** gefahren:

| Botschaft | B6-Warnung | B5-Warnung |
|---|---|---|
| `Suite 1692/1692 gruen` | **0** | 0 |
| `0 Platzhalter` | **0** | 0 |
| `StartView.tsx 267 Zeilen` | **0** | 0 |

*Und der Kantenfall aus der Kantenliste — „insgesamt" ohne Zahlenbezug:*

```text
"insgesamt ist die Lage jetzt uebersichtlich"   ->   B6-Warnung: 0
```

## B6-4 · Kein Abbruch

Vorfassung **aus dem Commit** geholt (`git show HEAD:scripts/commit-pruefen.sh`), drei Pfade:

| Pfad | ohne B6 | mit B6 |
|---|---|---|
| **Erfolg** | `0` | `0` |
| **Fehler** (Pfad existiert nicht) | `1` | `1` |
| **Ohne Rollenmarke** | `2` | `2` |

## Der Härtetest — wie oft schlüge B6 an echten Botschaften an?

**Menge, ausdrücklich benannt** *(sonst wäre dieser Absatz selbst ein B6-Fall)*: **die letzten 60
Commits von `HEAD` auf `auto/hausplaner-integration`, alle fünf Rollen, ganze Botschaft je Commit.**

```text
Summenwort getroffen:                    5 von 60
davon ohne Mengennennung -> Warnung:     2 von 60
```

**Und die zwei sind gelesen, nicht nur gezählt** (B5 auf die eigene Messung angewandt):

```text
a088a608  generator: W-07N IN_ARBEIT …
80261c87  evaluator: W-07N NACHBESSERN an b86e41fc …

beide ausgeloest durch dieselbe Wendung:   "rund 148 Zeilen"
im Satz:  "…der Scope sagt kursiv 'NICHT im Scope: die anderen fuenf Blaetter
           von W-07', geaendert sind zwei davon mit rund 148 Zeilen"
```

> **Beides sind echte Treffer, keine Fehlalarme** — *eine Summe über eine Menge, deren Glieder nicht
> genannt sind: „zwei davon" sagt die Anzahl, aber nicht **welche**. **Und das ist ausgerechnet der
> Satz, um den sich der ganze Scope-Streit `-8` gedreht hat.** Hätte die Warnung damals gestanden,
> wäre die Frage „welche zwei Blätter?" beim Schreiben gestellt worden statt zwei Runden später.*

*Zur Ehrlichkeit über diese Zahl: **2 von 60 ist eine Messung an dieser Stichprobe, keine Quote.**
Ich rechne sie nicht hoch.*

## B6-5 · Die Abgrenzung zu B5 steht im Regeltext

`docs/ARBEITSREGELN.md`, **Abschnitt 18c**, direkt hinter 18b (B5) — als Tabelle, damit die beiden
nicht verschmelzen:

| | Der Fehler | Das Gegenmittel |
|---|---|---|
| **B5** | gezählt und die Zeilen nicht gelesen | denselben Lauf ohne `-c` fahren |
| **B6** | nie gesagt, worüber gezählt wird | die Menge zuerst benennen, dann erheben |

*Mit dem Vorfall, der zeigt, warum B5 hier nicht geholfen hätte: **jede einzelne Zeilenzahl war
richtig** — falsch war, dass fünf von acht Dateien nie in der Menge waren.*

## B6-6 · Der Prüfweg — schon gemessen, jetzt genutzt

*Bei B5 gemessen, und das Ergebnis gilt unverändert:* eine Datei `PRUEFWEG*.md` **existiert nicht**;
der Prüfweg je Rolle liegt in `4-WAS-ICH-ABLIEFERE.md`, fünfmal gleich gebaut. B5s Zeile steht dort
seit `157576c2` in Zeile 14 (in allen fünf nachgemessen). **B6s Zeile steht jetzt als Zeile 15
darunter:**

```text
| **Menge** je Summe | B6: Summe? Dann Menge zuerst benennen (Pfad, Muster, Abgrenzung) und
  vollständig erheben. Was beim Suchen nebenbei auffiel, ist ein FUND — und wird als Fund
  gemeldet, nicht als Summe |
```

## B6-7 · `must_preserve`

| | Ergebnis |
|---|---|
| **gelöschte Zeilen, gesamter Bau** | **0** (`45+0` Regelwerk · `32+0` Tor · `1+0` je Rollenblatt) |
| **`resources/**` / `app/**` geändert** | keine Datei |
| **hinzugefügt** | **0** |
| **entfernt** | **0** |
| **Tor-Suite** | `node --test scripts/__tests__/commitPruefen.test.mjs` → **61 pass, 0 fail** |

*Die bestehenden Torfunktionen — Rollenmarke, Pfadprüfung, Index-Angleichung, und jetzt auch B5 —
sind unangetastet; die 61 Zusagen decken sie ab und laufen unverändert durch.*

## B6-8 · §3, an beiden Orten, unmittelbar vor der ersten Änderung

Gemessen **mit der am selben Tag verankerten Methode** (`db1ee790`), beide Zahlen und beide
Trefferzeilen:

```text
Tafelzeile    grep -cE '^\| \*\*[A-Z]+-?[0-9]+.*IN_ARBEIT' docs/STATUS.md   ->  1
Zustandsfeld  grep -cE '^zustand: *IN_ARBEIT'              docs/STATUS.md   ->  1

  docs/STATUS.md:36     | **B6** Summe braucht Erhebung | **`IN_ARBEIT`** | **Generator** |
  docs/STATUS.md:2011   zustand: IN_ARBEIT
```

*Nachtrag zur Zeilenangabe, weil sie sich während des Baus bewegt hat:* **`ed7ccb70` („WURZELFIX 2")
hat 35 Commits in den Zweig gebracht**, darunter meinen eigenen B6-Zustand; die Zustandszeile steht
seither auf **`docs/STATUS.md:2015`**, der Auftrag darüber ist unverändert `B6`. *Die Messung wurde
nach dem Merge wiederholt — beide Orte weiterhin **1 und 1**, mein Bau im Arbeitsbaum unversehrt
(`B6-WARNUNG` 1× im Tor, `## 18c` 1× im Regelwerk, die Prüfwegzeile 1× je Rollenblatt).*

> **Deckungsgleich — und das ist neu.** *Mit dem alten Muster `[AW]-[0-9]+` hätte die Tafelzeile hier
> wieder **0** gemeldet und B6 wäre unsichtbar gewesen, genau wie B5 es war. Mein eigener B5-Befund
> greift damit zum ersten Mal zu meinen Lasten — die Schranke sieht mich jetzt.*

## Reihenfolge — die Zurückstellung ist aufgelöst, nicht vergessen

*Der Plan-Prüfer hatte gesetzt: „**REIHENFOLGE B5 dann B6**, damit der zweite Bau auf dem ersten
aufsetzt statt gegen ihn."* In der vorigen Runde hatte der Evaluator die B5-Abnahme auf **derselben
Datei** geclaimt; ich habe B6 deshalb zurückgestellt und A-18 gebaut. **B5 steht seit `b7ab49c5` auf
ABGENOMMEN (7/7)** — die Abnahme ist durch, die Datei frei, B6 setzt auf B5 auf. *Der Planner hat
diesen Fall inzwischen als Regel aufgeschrieben: **§3 zählt den Bau, nicht die Prüfung.***

## Grenzen, benannt

**Was das Tor nicht kann:** prüfen, ob die Menge **vollständig** ist. *Es kann nur fragen, ob eine
genannt wurde — Nicht-Ziel des Blattes, und es gehört hierher, damit niemand die Warnung für eine
Erhebungsprüfung hält.*

**Ein Weg daran vorbei, bewusst hingenommen:** eine Botschaft, die irgendeinen Pfad nennt, hält die
Warnung zurück — auch wenn dieser Pfad zu einer *anderen* Aussage gehört als die Summe. *Die
Alternative wäre ein Auslöser, der bei jeder Zahl anschlägt, und den verbietet B6-3.*

**Kein Test für B5 oder B6 in der Suite.** *Beide sind nur über Probeläufe belegt. Der Test wäre die
richtige nächste Stufe; er steht in keiner der beiden Scopes, und ich habe sie nicht erweitert —
inzwischen wäre es **ein** Test für beide Barrieren, nicht zwei.*

**Eine Werkzeugsache, die anderen Zeit spart:** *`grep` ist hier `ugrep`, und es bricht bei
Ausdrücken wie `.{0,45}(a|b|c).{0,45}` mit `exceeds complexity limits` ab — zweimal in Folge mit
Zeitüberschreitung, bis ich es einzeln gesucht habe. **Wer eine Trefferstelle im Umfeld lesen will,
sucht die Wörter einzeln statt in einer Alternation mit Kontextfenster.***

## Berührte Dateien

```text
scripts/commit-pruefen.sh                                    +32 / -0  (Warnblock 543-573)
docs/ARBEITSREGELN.md                                        +45 / -0  (18c + Fassung 1.6)
docs/rollenkette/rollen/1-planner/4-WAS-ICH-ABLIEFERE.md     je +1 / -0
docs/rollenkette/rollen/2-plan-pruefer/4-WAS-ICH-ABLIEFERE.md
docs/rollenkette/rollen/3-generator/4-WAS-ICH-ABLIEFERE.md
docs/rollenkette/rollen/4-evaluator/4-WAS-ICH-ABLIEFERE.md
docs/rollenkette/rollen/5-release-pruefer/4-WAS-ICH-ABLIEFERE.md
docs/BERICHT-B6-summe-braucht-erhebung.md                    dieser Bericht
docs/STATUS.md                                               Zustand an beiden Orten
```
