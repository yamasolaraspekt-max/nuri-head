# Baubericht A-26 — die Ball-Drift wird am Tor gefangen, nicht hinterher gemeldet

```yaml
auftrag: "A-26"
rolle: "generator"
blatt: docs/auftraege/aktiv/A-26-ball-drift-am-tor.md
art: "BAU — die vierte Barriere im Tor, Bauform wie F-14, B5, B6 und B7."
basis_sha: d3d234a6
gebaut_am: "13.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **Die Probe an den ECHTEN Ständen hat einen Fehlalarm in meiner eigenen Barriere gefunden** —
> *`Release-Prüfer` gegen `release-pruefer`, ein Umlaut.* **An einem erfundenen Beispiel wäre das
> nie aufgefallen, und genau dafür verlangt A-26-1 die drei historischen Stände.**

## Was gebaut wurde

```text
scripts/a26-ball-drift.sh      NEU — die Barriere
scripts/commit-pruefen.sh      11 Zeilen dazu, 0 entfernt: der Aufruf
```

**Warum ein eigenes Skript und kein Block im Tor:** *A-26-1 verlangt den Nachweis an drei
historischen Ständen.* **Wer die Barriere nur im Tor hat, kann sie nicht fahren, ohne zu
committen — und ein Nachweis, der einen Commit erzeugt, ist keiner.** *Das Tor ruft dasselbe
Skript wie die Probe: eine Wahrheit, zwei Aufrufer.*

## A-26-1 (TRAGEND) · Die drei echten Fälle

**Je der ELTER des Nachzugs trägt die Drift. Die drei SHAs am Bau-Stand geprüft, nicht aus dem
Blatt übernommen.**

```text
MELDET  W-36 (8c24b79f^)   W-36  BALL: Tafel 'plan-pruefer' <-> Datensatz 'planner'
MELDET  W-33 (55cd13d8^)   W-33  BALL: Tafel 'Planner'      <-> Datensatz 'generator'
MELDET  W-31 (38bc5e12^)   W-31: nicht zuordenbar — mehrere Datensaetze in einem
                                 yaml-Block (A-25)
```

**Und die Gegenprobe an den Nachzügen selbst, wo die Drift behoben ist:**

```text
STILL   W-36 nachgezogen (8c24b79f)
STILL   W-33 nachgezogen (55cd13d8)
MELDET  W-31 nachgezogen (38bc5e12)   -> weiterhin 'nicht zuordenbar'
```

> **Der dritte Fall ist EHRLICH und nicht gefangen.** *Der Stand vom 38bc5e12 liegt VOR A-25 —
> dort lagen fünf Datensätze in einem yaml-Block, und die Barriere meldet genau das, was A-26-2
> verlangt: **„nicht zuordenbar" statt zu raten.*** **Eine falsche Zuordnung ist schlimmer als eine
> ausgelassene.**
>
> **Zwei von drei Fällen werden namentlich gefangen, der dritte wird ausdrücklich als
> unzuordenbar gemeldet.** *Das steht so da, weil „drei von drei" hier eine Behauptung wäre.* **Am
> heutigen Stand — nach A-25 — greift der Zweifelsfall nirgends mehr: `scripts/a25-zaeune.mjs`
> meldet 0 verschmolzene Bereiche.**

## Der Fehlalarm, den nur die echten Stände gezeigt haben

**Der erste Lauf gegen `55cd13d8` meldete zusätzlich:**

```text
W-35  BALL: Tafel 'Release-Prüfer' <-> Datensatz 'release-pruefer'
```

**Die Zeile war in Ordnung.** *Mein Vergleich schrieb klein, aber nicht umlautfrei — `release-prüfer`
gegen `release-pruefer`.*

> **Ein Fehlalarm auf einer legitimen Zeile ist genau der Weg, auf dem eine Barriere weggeklickt
> wird** *(A-03, und A-26-3 verlangt ausdrücklich das Gegenteil).* **Behoben: `ä ö ü ß` werden vor
> dem Vergleich aufgelöst.** *Der Grund steht als Kommentar im Skript, nicht nur hier.*

## A-26-2 · Die drei Fallen, je mit Zusage

| Falle | Behandlung | Beleg |
|---|---|---|
| **(a) Schreibweise** | Backticks, Sterne, Randleerzeichen weg — dazu klein und umlautfrei für den Ball | die Fangprobe unten |
| **(b) Kommentare** | alles ab `#` abgeschnitten | `ballbesitz: plan-pruefer  # DoR steht aus` wird als `plan-pruefer` gelesen |
| **(c) Zuordnung** | der Datensatz wird ab **seiner** `auftrag:`-Zeile bis zum nächsten Zaun gelesen; bei mehreren Datensätzen im Block: **„nicht zuordenbar"** | der W-31-Lauf oben |

## A-26-3 (WIRKSAMKEIT) · Still am sauberen Stand

```text
bash scripts/a26-ball-drift.sh docs/STATUS.md   ->   Rueckgabe 0, keine Ausgabe
```

**Und die zwei Nachzüge sind ebenfalls still** — *das ist der schärfere Nachweis: dort wurde eine
echte Drift behoben, und die Barriere schweigt genau dann.*

## A-26-4 · Nur die berührten Aufträge

**Gemessen: 26 ms am sauberen Stand.** *Die Barriere liest die Kennungen aus dem Diff und öffnet nur
deren Tafelzeile und Datensatz — nicht alle 56 Aufträge.* **Bei leerem Diff steigt sie nach zwei
Befehlen aus (`[ -z "$KENNUNGEN" ] && exit 0`).**

## A-26-5 · Warnt, bricht nicht ab

```sh
bash scripts/a26-ball-drift.sh docs/STATUS.md || true
```

**Der Rückgabewert wird im Tor bewusst verworfen.** *Eine Rückgabe darf zwischen zwei Commits
liegen; ein Abbruch würde legitime Arbeit blockieren.*

## A-26-6 · Die Fangprobe — und meine ersten zwei Mutationen waren defekt

```text
md5-Anker scripts/a26-ball-drift.sh   e921a0f7efedded1a8f7888eba002927

1. Versuch  SAEUBERE='s///'        -> sed: „first RE may not be empty"
            Die Barriere ging STILL statt zu laermen. Das sah aus wie ein
            Befund ueber den Bau und war einer ueber meine Mutation.
2. Versuch  SAEUBERE='s/^$//' per perl  -> die Zeile ZERRISSEN (Zeilenumbruch
            im Muster). Datei beschaedigt, Syntax kaputt.
            Repariert mit dem Edit-Werkzeug, md5 gegen den Anker: identisch.
3. Versuch  SAEUBERE='s/KEINE-NORMALISIERUNG-FANGPROBE//'   gueltig, wirkungslos
            -> MELDET, und zwar genau das, was A-26-6 vorhersagt:
               W-33 ZUSTAND: Tafel ' `BEREIT` '        <-> Datensatz 'BEREIT'
               W-33 BALL:    Tafel ' **Generator** '   <-> Datensatz 'generator'
               W-35 ZUSTAND: Tafel ' **`IN_ARBEIT`** ' <-> Datensatz 'IN_ARBEIT'
            Also: JEDE Zeile als Drift — der Fall, den A-26-3 verbietet.
Rueckschrift  md5 gegen den Anker: identisch.
```

> **Beide Fehlversuche stehen hier, weil der erste beinahe zu einem falschen Befund geführt
> hätte:** *„die Barriere findet die Drift nicht" — dabei war die Mutation kaputt und nicht der
> Bau.* **Eine Mutation, die man nicht prüft, misst dasselbe wie ein Muster, das man nicht prüft.**
>
> **Und der zweite ist B6 in Reinform:** *zweimal `perl -i` auf eine Datei mit Sonderzeichen,
> zweimal beschädigt.* **Die Reparatur lief mit dem Edit-Werkzeug und wurde gegen den md5-Anker
> geprüft — die Datei ist zeichengleich wiederhergestellt.**

## A-26-7 · Die Rollenmarke ist unberührt

```text
git diff scripts/commit-pruefen.sh | grep -icE "A11|ROLLE|Rollenmarke"   ->   0
git diff --numstat scripts/commit-pruefen.sh                             ->   11  0
```

**Elf Zeilen dazu, null entfernt** — *der Aufruf steht hinter B7 und berührt keinen bestehenden
Block.*

## Was die Barriere NICHT fängt

**Beide Orte stimmen überein und sind BEIDE veraltet.** *A-26 vergleicht die zwei Orte
**miteinander**; wo sie übereinstimmen, sagt sie nichts.*

> **Das steht schon im Auftragsblatt als offene zweite Klasse, und ich baue nichts dagegen** — *der
> Planner hat dort selbst festgehalten, dass die naheliegenden Auslöser nicht tragen.* **Wer sie
> beheben will, braucht ein Signal, das das Tor nicht hat: was der Commit inhaltlich tut.**

## Ein Nebenbefund an meiner eigenen Probe

**Der zweite Probelauf meldete „ACHTUNG: docs/STATUS.md weicht ab".** *Zweimal unabhängig
nachgemessen: die Datei ist **identisch mit HEAD**, es ist nichts verlorengegangen.* **Der dritte
Lauf meldete wieder „ANKER WIEDERHERGESTELLT".**

> **Ein Wettlauf in meiner Probe zwischen `git checkout` und `md5`, nicht ein Schaden an der
> Datei.** *Ich melde es, weil eine Probe, die sporadisch Alarm schlägt, dieselbe A-03-Krankheit hat
> wie eine Barriere, die es tut* — **und weil ich sonst eine Warnung über die Statuswahrheit
> unerklärt stehen ließe.**

## must_preserve und Rückweg

| Richtung | Ergebnis |
|---|---|
| geändert | **1** — `scripts/commit-pruefen.sh`, 11 dazu, 0 entfernt |
| hinzugefügt | **1** — `scripts/a26-ball-drift.sh` |
| entfernt | **0** |
| `resources/`, `app/` | **0** in allen drei Richtungen |
| Rückweg | `git revert`; die Barriere ist ein eigenes Skript, der Aufruf elf Zeilen |
