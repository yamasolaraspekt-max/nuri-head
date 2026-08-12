# Baubericht B5 — ein Zählergebnis, das einen Befund trägt, braucht seine Trefferzeilen

```yaml
auftrag: "B5"
rolle: "generator"
blatt: docs/auftraege/aktiv/B5-zaehlergebnis-mit-trefferzeilen.md
basis_sha: 1734aa3b
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **Dieser Bericht steht unter seiner eigenen Regel.** *Jede Zahl darin, die einen Befund trägt,
> führt ihre Trefferzeilen mit. Wo eine Zahl selbst der Gegenstand ist, fehlen sie — mit Absicht.*

## Was gebaut wurde, in drei Sätzen

Das Tor warnt, wenn eine Commit-Botschaft ein **Zählwort ohne Belegzeile** trägt — Warnung, kein
Abbruch. Die Regel ist in `docs/ARBEITSREGELN.md` als **Abschnitt 18b** verankert, mit dem
Unterschied, ohne den sie falsch gelesen wird. Und die Zeile steht im Prüfweg **jeder der fünf
Rollen** — an der Stelle, an der er tatsächlich liegt, nicht an der vermuteten.

---

## B5-1 · Die Warnung existiert und feuert

**Rot-Lage aus dem Blatt, vor dem Bau selbst nachgemessen:**

```text
grep -cE 'Trefferzeile|B5' scripts/commit-pruefen.sh   ->  0
```

**Nach dem Bau, mit Trefferzeilen (derselbe Lauf ohne `-c`):**

```text
grep -cE 'Trefferzeile|B5' scripts/commit-pruefen.sh   ->  8

513:# ── B5: EIN ZAEHLERGEBNIS, DAS EINEN BEFUND TRAEGT, BRAUCHT SEINE TREFFERZEILEN ────
523:# Die Unterscheidung ist der Kern der Regel, und sie steht in der Warnung selbst, …
525:#   ZAHL ALS GEGENSTAND   "Suite 1692/1692", "0 Platzhalter"  -> Trefferzeilen waeren sinnlos.
533:B5_ZAEHLWORT='grep[^|]*-[A-Za-z]*c|--count|[Tt]reffer|[Vv]orkommen|[Ff]undstelle|…'
534:B5_BELEGZEILE='[A-Za-z0-9_./-]+\.[A-Za-z]{1,5}:[0-9]+|:[0-9]+:|Trefferzeile'
535:if printf '%s' "$BOTSCHAFT" | grep -qE "$B5_ZAEHLWORT" \
536:   && ! printf '%s' "$BOTSCHAFT" | grep -qE "$B5_BELEGZEILE"; then
537:  echo "B5-WARNUNG  Zaehlwort in der Botschaft, aber keine Belegzeile (datei.ext:zeile)." >&2
```

*Fünf der acht Treffer sind Kommentar, drei sind Code — genau die Unterscheidung, an der Fall 1 des
Blattes gescheitert ist (ein CSG-Treffer im Dateikopf, als „gebaut" gezählt). **Die Zahl allein
hätte auch hier nichts belegt.***

**Die Mechanik, in zwei Zeilen:** ein **Zählwort** löst aus (`grep -c`, `--count`, Treffer,
Vorkommen, Fundstelle, zähl…, „kommt … vor"), eine **Belegzeile** hält zurück (`datei.ext:123`,
`:123:`, das Wort Trefferzeile). Beides ist bewusst eng: *ein Auslöser, der jede Zahl fängt, ist
B5-2 verletzt, bevor er nützt.*

---

## B5-2 · Sie feuert NICHT bei legitimen Zahlen — beide Probeläufe

**Zum Aufbau, weil er den Nachweis erst gültig macht:** das Tor bindet sich in Zeile 44 per
`cd "$(dirname "$0")/.."` an **sein eigenes** Repositorium. Ein Aufruf des ticket-Tors aus einem
fremden Verzeichnis arbeitet deshalb weiter auf ticket — der erste Probelauf lief so ins Leere
(`FEHLT a.txt`). *Das ist kein Mangel, das ist die Repo-Bindung aus A-09.* Die Probe braucht daher
eine **Kopie des Tors im Wegwerf-Repo**, und genau so lief sie.

**Probelauf 1 — Zählwort ohne Belegzeile, soll feuern:**

```text
$ TICKET_ROLLE=generator bash scripts/commit-pruefen.sh "CSG kommt einmal vor, also ist es gebaut" a.txt
B5-WARNUNG  Zaehlwort in der Botschaft, aber keine Belegzeile (datei.ext:zeile).
            Zahl als Gegenstand ist in Ordnung. Traegt die Zahl einen BEFUND,
            fahre denselben Lauf ohne -c und nimm die Zeilen mit, die du gezaehlt hast.
            Warnung, kein Abbruch — der Commit laeuft weiter.
65cd92e generator: CSG kommt einmal vor, also ist es gebaut
INDEX ANGEGLICHEN  Standard-Index an HEAD angeglichen …
RUECKGABEWERT: 0
```

**Probelauf 2 — die drei legitimen Formen aus dem Blatt, soll schweigen:**

```text
$ TICKET_ROLLE=generator bash scripts/commit-pruefen.sh "Suite 1692/1692 gruen, 0 Platzhalter, 5 von 10 Blaettern fertig" a.txt
1354ec0 generator: Suite 1692/1692 gruen, 0 Platzhalter, 5 von 10 Blaettern fertig
INDEX ANGEGLICHEN  Standard-Index an HEAD angeglichen …
RUECKGABEWERT: 0

grep -c 'B5-WARNUNG' <ausgabe>   ->  0      (kein Treffer, deshalb keine Trefferzeile — A-10)
```

**Der Commit lief beide Male durch.** *Das ist der Punkt: die Warnung erscheint neben dem Ergebnis,
nicht statt seiner.*

---

## B5-3 · Kein Abbruch — der Rückgabewert bleibt unverändert

Das Blatt verlangt „denselben Commit einmal mit und einmal ohne die Warnung". Ich habe die
Vorfassung **aus dem Commit** geholt (`git show HEAD:scripts/commit-pruefen.sh`), nicht aus dem
Arbeitsbaum, und **drei Pfade** statt einem gemessen — ein Rückgabewert, der nur im Erfolgsfall
gleich bleibt, wäre kein Nachweis:

| Pfad | ohne B5 | mit B5 |
|---|---|---|
| **Erfolg** (Datei da, Rolle gesetzt) | `0` | `0` |
| **Fehler** (Pfad existiert nicht) | `1` | `1` |
| **Ohne Rollenmarke** (B4 greift) | `2` | `2` |

*Der Block steht **nach** dem Fehler-Riegel und setzt weder `FEHLER` noch `exit`. Er kann den
Rückgabewert nicht einmal versehentlich berühren.*

---

## B5-4 · Die Regel steht in ARBEITSREGELN — und die Barrierenliste gibt es nicht

Das Kriterium sagt „B5 steht in der Barrierenliste". **Diese Liste existiert nicht.** Gemessen:

```text
grep -n 'Barrierenliste' docs/ARBEITSREGELN.md     ->  kein Treffer
grep -rn 'Barrierenliste' docs/                    ->  kein Treffer
```

*Ein leeres Ergebnis wird gemeldet, nicht überschrieben (A-10). Ich habe die Liste **nicht
erfunden**, um das Kriterium wörtlich grün zu bekommen.*

**Was es stattdessen gibt, gemessen:** das Regelwerk verankert seit dem 12.08. die Hausregeln
**H-1 bis H-7** (§18a) — und **H-6 ruft B5 dort bereits auf**:

```text
docs/ARBEITSREGELN.md:767
  **Gehört zu B5:** *B5 verlangt, die Trefferzeilen zu lesen — H-6 sagt, warum.*
```

**Deshalb steht B5 jetzt als Abschnitt 18b direkt neben den Hausregeln** — an der Stelle, auf die
das Haus schon zeigt. Der Abschnitt trägt die Unterscheidung als Tabelle:

| | Beispiel | Was die Trefferzeilen leisten |
|---|---|---|
| **Zahl als Gegenstand** | „Die Suite zählt 1692" | **nichts** — die Zahl *ist* die Aussage |
| **Befund aus einer Zahl** | „CSG kommt einmal vor, also ist es gebaut" | **alles** — die Zeile entscheidet |

**Eine Abweichung, die der Evaluator entscheiden soll, nicht ich:** §19 ist in der Nummerierung in
sich uneinheitlich — *Fassung 1.4.2 trägt den 05.08., Fassung 1.3 den 12.08.* Ich habe **1.5** als
nächstfreie Nummer genommen und die Unstimmigkeit im Eintrag benannt, statt sie stillschweigend zu
begradigen. Das Regelwerk gehört nicht dem Generator.

---

## B5-5 · Der Prüfweg wurde GEMESSEN, nicht angenommen

Das Blatt sagt ausdrücklich: *„Ich habe es nicht gemessen und behaupte es deshalb nicht."* Also
zuerst die Messung, und sie hat **zwei** Antworten:

```text
find docs -iname '*pruefweg*' -o -iname '*rollenblatt*'   ->  0 Dateien
```

**Eine Datei namens `PRUEFWEG*.md` gibt es nicht** — die erste Hälfte der Scope-Alternative ist
leer. Die zweite trägt:

```text
docs/rollenkette/rollen/1-planner/        1-AUFTRAG · 2-WANN-BIN-ICH-DRAN · 3-WAS-ICH-LESE
docs/rollenkette/rollen/2-plan-pruefer/   · 4-WAS-ICH-ABLIEFERE · 5-WAS-ICH-NICHT-DARF
docs/rollenkette/rollen/3-generator/      (fünf Blätter je Rolle, in allen fünf gleich gebaut;
docs/rollenkette/rollen/4-evaluator/       beim Planner zusätzlich zwei SKILL-Blätter)
docs/rollenkette/rollen/5-release-pruefer/
```

**Der Prüfweg je Rolle liegt in `4-WAS-ICH-ABLIEFERE.md`** — dort steht die Pflichtfeld-Tabelle, und
ihre dritte Zeile ist bereits die nächste Verwandte von B5:

```text
docs/rollenkette/rollen/3-generator/4-WAS-ICH-ABLIEFERE.md:13
  | **gemessen an** je Zahl | Plausibilität: keine Zahl ohne Beleg |
```

*B5 schärft genau dieses Feld: „gemessen an" sagt **woran**, B5 sagt **was dabei zu lesen war**.*
Die neue Zeile steht in allen fünf Blättern, wörtlich gleich:

```text
| **Trefferzeilen** je Zählbefund | B5: trägt die Zahl einen Befund, ist die Zeile der Beleg —
  nicht die Zahl. Ist die Zahl selbst der Gegenstand („Suite 1692/1692"), entfällt das Feld |
```

---

## B5-6 · `must_preserve`, in drei Richtungen einzeln

| Richtung | Befehl | Ergebnis |
|---|---|---|
| **geändert** | `git rev-parse HEAD:<f>` gegen `git hash-object <f>`, über alle `resources`/`app` | **keine Abweichung** |
| **hinzugefügt** | `git ls-files --others --exclude-standard -- resources app` | **0** |
| **entfernt** | `git diff --name-only --diff-filter=D HEAD -- resources app` | **0** |

**Nur Einfügungen im Tor**, wie das Kriterium es verlangt:

```text
git diff --numstat -- scripts/commit-pruefen.sh   ->   30   0   scripts/commit-pruefen.sh
                                                       ↑    ↑
                                                  30 neu   0 gelöscht
```

**Die Tor-Suite bleibt grün:** `node --test scripts/__tests__/commitPruefen.test.mjs` → **61 pass,
0 fail**. *Rollenmarke, Pfadprüfung und Index-Angleichung sind unangetastet — die 61 Zusagen decken
sie ab und laufen unverändert durch.*

---

## B5-7 · §3, unmittelbar vor der ersten Änderung — und ein Befund darin

```text
grep -cE '^\| \*\*[AW]-[0-9]+.*`IN_ARBEIT`' docs/STATUS.md   ->  0
grep -c  '^zustand: IN_ARBEIT'               docs/STATUS.md   ->  1

Trefferzeile:  docs/STATUS.md:1993   zustand: IN_ARBEIT
               auftrag: "B5"   datei: docs/auftraege/aktiv/B5-zaehlergebnis-mit-trefferzeilen.md
```

> **Die beiden Zahlen widersprechen sich, und der Widerspruch betrifft diesen Auftrag selbst.**
> *Der kanonische §3-Befehl meldet **0 IN_ARBEIT**, während B5 läuft — weil sein Muster `[AW]-[0-9]+`
> ein Blatt namens `B5` nicht fassen kann.* **Die Schranke sagt „frei", wo etwas läuft — das ist die
> gefährliche Richtung.**

Ich melde beide Zahlen und ersetze die Messform nicht still; die Entscheidung liegt beim Evaluator.
*Der Befund selbst ist nicht neu — er ist bei Planner und Plan-Prüfer bereits gemessen (Muster
`[A-Z]+-?[0-9]+` findet 36 Tafelzeilen statt 31). **Neu ist nur, dass er hier zum ersten Mal am
eigenen laufenden Auftrag zuschlägt.***

---

## Grenzen, offen und benannt

**Was das Tor nicht kann und nie können wird:** beurteilen, ob eine Messung inhaltlich stimmt. Es
sieht ein Zählwort und das Fehlen einer Belegzeile — mehr nicht. *Das ist Nicht-Ziel des Blattes,
und es gehört in den Bericht, damit niemand die Warnung für eine Prüfung hält.*

**Zwei Wege an der Warnung vorbei, ohne Trick:** eine Botschaft, die einen Zählbefund ohne jedes
Zählwort formuliert („bei uns steht das nur an einer Stelle"), löst nichts aus. Und eine Botschaft
mit irgendeiner `datei.ext:12`-Angabe hält die Warnung zurück, auch wenn die Zeile zu einer *anderen*
Zahl gehört. **Beides ist bewusst hingenommen** — die Alternative wäre ein Auslöser, der jede Zahl
fängt, und den verbietet B5-2 aus gutem Grund.

**Kein Test für B5 in der Suite.** Die 61 Zusagen decken B4/A-07/A-08/A-09/A-11 ab, B5 ist nur über
die zwei Probeläufe belegt. *Ein Test wäre die richtige nächste Stufe — er steht nicht in der Scope
dieses Blattes, und ich habe die Scope nicht erweitert.*

**B5 und B6 teilen Stelle und Form.** Der Plan-Prüfer empfiehlt in `docs/STATUS.md:80` „**EIN Bau.
Geteilte Datei, Form, Prüfweg**". Gebaut ist hier **nur B5** — die Scope nennt B6 nicht. Der Boden
für B6 ist damit bereitet: derselbe Block im Tor, derselbe Abschnitt 18b, dieselbe Tabellenzeile in
den fünf Rollenblättern.

## Berührte Dateien

```text
scripts/commit-pruefen.sh                                    +30 / -0   (Warnblock, Zeile 513-541)
docs/ARBEITSREGELN.md                                        Abschnitt 18b + Fassungseintrag 1.5
docs/rollenkette/rollen/1-planner/4-WAS-ICH-ABLIEFERE.md     je +1 Tabellenzeile
docs/rollenkette/rollen/2-plan-pruefer/4-WAS-ICH-ABLIEFERE.md
docs/rollenkette/rollen/3-generator/4-WAS-ICH-ABLIEFERE.md
docs/rollenkette/rollen/4-evaluator/4-WAS-ICH-ABLIEFERE.md
docs/rollenkette/rollen/5-release-pruefer/4-WAS-ICH-ABLIEFERE.md
docs/BERICHT-B5-zaehlergebnis-mit-trefferzeilen.md           dieser Bericht
```
