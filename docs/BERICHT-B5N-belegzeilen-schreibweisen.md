# Baubericht B5N — die Barriere schlug bei richtiger Arbeit an

```yaml
auftrag: "B5N"
rolle: "generator"
blatt: docs/auftraege/aktiv/B5N-belegzeilen-schreibweisen.md
basis_sha: 8870387a
gebaut_auf: 2766e0ac
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **Das ist die Nachbesserung meiner eigenen Barriere, und der Fehler war meiner.** *Ich habe den
> Befund gegen mein eigenes Muster nachgemessen, bevor ich ihn übernommen habe — er hält.*

## Was geändert wurde: eine Zeile

```diff
-B5_BELEGZEILE='…\.[A-Za-z]{1,5}:[0-9]+|:[0-9]+:|Trefferzeile'
+B5_BELEGZEILE='…\.[A-Za-z]{1,5}:[0-9]+|:[0-9]+:|Trefferzeile|Z\.[0-9]+|Zeile [0-9]+'
```

**Drei Formen, zwei Alternativen** — *`Z.217-268` beginnt mit `Z.217` und ist damit mit abgedeckt.*
`B5_ZAEHLWORT` bleibt unberührt: **der Fehler lag auf der Belegseite, nicht bei der Erkennung des
Zählworts.** *Wer beides anfasst, kann hinterher nicht sagen, welche Änderung gewirkt hat.*

## B5N-1 · Die drei Formen, jede EINZELN gefahren

*Eine Sammelaussage „erkennt jetzt alles" genügt nicht — das steht so im Blatt.*

| Botschaft | B5-Warnung |
|---|---|
| `vier Treffer gezaehlt, gelesen in Z.217` | **0** |
| `vier Treffer gezaehlt, gelesen in Z.217-268` | **0** |
| `vier Treffer gezaehlt, gelesen in Zeile 171` | **0** |

**Rot vorher, am Muster selbst gemessen:** *`Z.217` gegen `B5_BELEGZEILE` in der alten Fassung →
**kein Treffer**, die Form fiel durch.*

## B5N-2 · Der tragende Punkt — sie schweigt NICHT überall

*Dieselbe Probe zweimal, einmal mit und einmal ohne Beleg:*

```text
"vier Treffer gezaehlt, also ist es gebaut"      ->  B5-Warnung: 1   ← beisst weiter
"vier Treffer gezaehlt, gelesen in Z.217"        ->  B5-Warnung: 0   ← schweigt zu Recht
```

> **Ohne diesen Gegenbeleg wäre die Nachbesserung eine Abschaltung mit anderem Namen.** *Der
> gefährlichere der beiden Fehler wäre nicht die falsche Warnung, sondern das falsche Schweigen —
> **Schweigen sieht aus wie Erfolg**.*

## B5N-3 · Die erkannten Formen bleiben erkannt

| Botschaft | B5-Warnung |
|---|---|
| `vier Treffer, siehe enginePanels.ts:241` | **0** |
| `vier Treffer, siehe :2076:` | **0** |
| `vier Treffer, Trefferzeile unten` | **0** |

*Und im Diff sichtbar: die drei alten Alternativen stehen **zeichengleich** am Anfang des Musters,
angehängt wurde nur hinten.*

## B5N-4 · `must_preserve` — mit beiden Lesarten der Zahl

```text
git diff --numstat -- scripts/commit-pruefen.sh   ->   11   1
```

**Die „1" ist die geänderte Musterzeile selbst, keine entfernte Zeile** — hier ausgeschrieben, damit
niemand rechnen muss. *Zur Zeilennummer, weil meine eigene Einfügung sie verschoben hat:* **vorher
stand `B5_BELEGZEILE` in Zeile 534** *(die Nummer, die das Blatt nennt)*, **jetzt in Zeile 544** —
die zehn Kommentarzeilen davor sind neu.

```diff
-B5_BELEGZEILE='[A-Za-z0-9_./-]+\.[A-Za-z]{1,5}:[0-9]+|:[0-9]+:|Trefferzeile'
```

> **Das Kriterium sagt „genau EINE geänderte Zeile (534) und 0 gelöschte". Beides gilt — aber
> `numstat` kann es nicht so zeigen:** *eine geänderte Zeile erscheint dort immer als eine Löschung
> plus eine Einfügung.* **Ich melde die Zahl, wie das Werkzeug sie ausgibt, und die Zeile, damit der
> Evaluator sieht, dass es dieselbe ist.** *Von den 11 Einfügungen sind **10 Kommentar** — der Grund
> der Änderung, im Stil der drei Barrierenblöcke daneben; die elfte ist die neue Musterzeile.*

**Die anderen Barrieren und die Torfunktionen, einzeln gezählt:**

| | vorher | nachher |
|---|---|---|
| `TICKET_ROLLE` (B4) | 4 | 4 |
| `B6_SUMMENWORT` | 2 | 2 |
| `B7_MEHRFACH` | 2 | 2 |
| `read-tree HEAD` (Index-Angleichung) | 6 | 6 |
| `B5_ZAEHLWORT` | 2 | **3** |

> **Die letzte Zeile ist ein Fehlalarm meiner eigenen Prüfung, und ich habe ihn nachgemessen statt
> ihn zu melden:** *der dritte Treffer steht in **Zeile 543 — meinem neuen Kommentar** („`B5_ZAEHLWORT`
> bleibt unberührt"). Die Zuweisung selbst (`533`) ist zeichengleich.* **Ein Wort ist kein Beleg,
> erst die Stelle ist einer — H-6, diesmal gegen mein eigenes Messwerkzeug.**

`resources/**` und `app/**`: **0 Dateien berührt.** Tor-Suite: **61 pass, 0 fail.**
Rückweg: `git apply --check -R` → **Exit 0**, Arbeitsbaum unangetastet.

## B5N-5 · Der zweite Befund — gemessen, nicht angenommen

*Das Blatt verlangt zu **messen**, ob es in `scripts/__tests__` eine Stelle gibt, an der B5s Muster
geprüft wird — und die drei Formen dort zu ergänzen, falls ja.*

```text
grep -rn 'B5_BELEGZEILE|B5-WARNUNG|BELEGZEILE' scripts/__tests__/   ->  0 Treffer
Testdateien im Ordner: 5
```

> **Es gibt keine solche Stelle.** *Die 61 Zusagen der Tor-Suite decken Rollenmarke, Pfadprüfung und
> Index-Angleichung ab — **keine einzige prüft B5, B6 oder B7**.* **Die Lücke wird durch diesen
> Auftrag also nicht größer, aber sie bleibt:** *eine Barriere ohne Test ist eine Behauptung über
> sich selbst.* **Eigener Vorgang, hier nur benannt — vierter Bericht in Folge mit diesem Satz.**

## B5N-6 · §3 an beiden Orten

*Nach der verankerten Methode, unmittelbar vor der ersten Änderung:*

```text
Tafelzeile     grep -cE '^\| \*\*[A-Z]+-?[0-9]+.*IN_ARBEIT' docs/STATUS.md   ->  1
Zustandsfeld   grep -cE '^zustand: *IN_ARBEIT'              docs/STATUS.md   ->  1
beide zeigen auf B5N
```

## Ein Befund, den ich NICHT hier behebe

**Beim Committen der vorigen Runde hat `B7` bei mir selbst angeschlagen — auf einem Zitat.** *Meine
Botschaft zitierte den Titel des zurückgenommenen Sonden-Commits („…an vier Fundorten") und enthielt
das Wort „Herkunft" null Mal; also feuerte die Warnung.* **Das ist dieselbe Klasse wie B5s eigener
Fall 1** *(ein CSG-Treffer im Dateikopf, als „gebaut" gezählt)*: **die Barriere kann Zitat und
Behauptung nicht unterscheiden.**

> *Nicht hier behoben, und das ist die Anweisung des Blattes:* **„Keine Verschärfung … wer bei dieser
> Gelegenheit zusätzliche Fälle einfangen will, schneidet dafür einen eigenen Auftrag."** *Es
> betrifft außerdem B7, nicht B5. Gemeldet, damit es nicht als vierte Meldung endet — genau der Weg,
> aus dem dieser Auftrag entstanden ist.*

## Rückfallpunkt am Bautag

```text
fork/auto/hausplaner-integration   5579a6c0
lokal HEAD                         a1751fbe
```

**Unverändert seit dem A-17-Bericht: die Bauten dieser Runde sind noch nicht außerhalb der
Maschine.** *Der Transport gehört Yama.*

## Berührte Dateien

```text
scripts/commit-pruefen.sh                        +11 / -1  (Zeile 534 plus 10 Zeilen Begruendung)
docs/BERICHT-B5N-belegzeilen-schreibweisen.md    dieser Bericht
docs/STATUS.md                                   Zustand an beiden Orten
```
