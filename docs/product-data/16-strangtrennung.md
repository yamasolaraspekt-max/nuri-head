# 16 · Zwei Stränge, eine Werkbank — warum die Rollen durcheinanderkommen

> **Rolle:** Planner · **Stand:** 02.08.2026 · **Heimat-App:** `ticket` · **Spur:** B (Verfahren)
> **Anlass:** Yamas Befund, dass Generator, Evaluator und Prüfer aus zwei Richtungen Aufträge
> für zwei verschiedene Bereiche bekommen.
> **Legende:** BELEGT · BEWERTUNG · ANNAHME · OFFEN

---

## 1 · Der Befund in einem Satz

**Die Rolle ist modelliert, der Strang nicht.**

Planner, Generator, Evaluator und Prüfer beschreiben eine **Fließrichtung** — wer entwirft, wer
baut, wer abnimmt. „3D-Hausplaner" und „Produktdaten/IDS" beschreiben einen **Gegenstand**. Das
sind zwei senkrecht zueinander stehende Achsen, und im heutigen System existiert nur die erste.

Ein Generator ist deshalb ein **globaler** Generator. Er sieht `docs/auftraege/`, findet 131
Blätter aus beiden Strängen und hat kein Merkmal, an dem er erkennt, welche ihn angehen. Dass es
bisher gutgeht, liegt daran, dass du die Instanzen von Hand zuweist — nicht daran, dass das
System es trägt.

---

## 2 · Die Belege

### 2.1 · Der Auftragskopf kennt kein Strang-Feld — BELEGT

`docs/auftraege/AUFTRAGSSCHEMA.md` §1 führt: `id` · `status` · `spur` · `heimat` · `ziel` ·
`nicht_ziel` · `population_command` · `pfade` · `ausschluesse`.

`grep -n "strang\|Strang\|bereich" docs/auftraege/AUFTRAGSSCHEMA.md` liefert **null Treffer**.

`heimat: ticket` beantwortet **welche App**, nicht **welcher Strang**. Bei uns ist die Antwort
immer `ticket` — das Feld unterscheidet nichts.

### 2.2 · Die Strang-Worktrees existieren nicht mehr — BELEGT

`docs/BETRIEBSORDNUNG.md` §1.3 schreibt vor:

> Ein Worktree + eigener Branch `strang/<name>` je Bauer.

und §3.1: **„BAUER (eine Instanz je Strang, eigener Worktree)"**.

`git worktree list` sagt:

```
/Users/yamanuri/Documents/ticket                   ce75fc30 [auto/hausplaner-integration]
/Users/yamanuri/Documents/ticket-g1b-0             ba6dac39 [work/g1b-2-dry-run-mapper]  prunable
/Users/yamanuri/Documents/ticket-main              d8612a63 [main]                       prunable
/Users/yamanuri/Documents/ticket-strang-accounting 28b074b7 [strang/accounting]          prunable
/Users/yamanuri/Documents/ticket-strang-C          34976f8b [strang/C]                   prunable
/Users/yamanuri/Documents/ticket-strang-energie    20eb6c39 [strang/energie-adapter]     prunable
/Users/yamanuri/Documents/ticket-strang-formulare  f796b421 [strang/formulare]           prunable
```

**Sechs von sieben sind `prunable`** — ihre Verzeichnisse gibt es nicht mehr. Es bleibt **ein**
Worktree auf **einem** Branch, in dem alles gleichzeitig passiert. Die Regel steht, die Umsetzung
ist verfallen.

### 2.3 · `STRAENGE.md` beschreibt eine andere Welt — BELEGT

Die Datei trägt „Stand: 2026-07-05" und wurde zuletzt am 24.07. angefasst. Sie benennt die Stränge
`A`, `Katalog-ii`, `Heizkörper-iii`, `M3`, `B1`, `accounting` — keiner davon ist der heutige
3D-Hausplaner- oder Produktdatenstrang. Das Werkzeug, das der Koordinator laut §3.3 führen soll,
ist einen Monat alt.

### 2.4 · Es passiert bereits — BELEGT, heute

| Zeit | Vorgang |
|---|---|
| 09:22 | Ich stage meine Datei. Im selben Index liegt bereits `generator-auftrag-w04-…` des anderen Strangs. |
| 09:22 | Ich committe mit `git commit -- <pfad>` am Index vorbei — sonst hätte ich seine Datei mitgenommen. |
| 09:23 | Die andere Instanz misst gerade nach, ob ihre Umgebung das Fernziel erreicht (HTTP 403). **Denselben Befund hatte ich um 09:15.** |

Der letzte Punkt ist der teuerste: derselbe Messwert wird zweimal erarbeitet, weil es keinen Ort
gibt, an dem strangübergreifende Tatsachen stehen.

---

## 3 · Warum pfadgenaues `git add` das Problem nicht löst

Die Betriebsordnung §1.3 verbietet `git add -A` und verlangt ausdrückliche Pfade. Das ist richtig
und hat mich heute zweimal gerettet — **aber es schützt nur vor der eigenen Unachtsamkeit, nicht
vor der geteilten Werkbank.**

Ein Worktree hat **einen** Index. Sobald ich meine Datei stage, liegt sie neben der des anderen
Strangs. Committet er in diesem Moment ohne Pfadangabe, nimmt er meine mit — und umgekehrt.
Beide Seiten können sich vorbildlich verhalten und trotzdem kollidieren, weil die Kollision nicht
im Verhalten sitzt, sondern im geteilten Zustand.

**Das ist der Grund, warum §1.3 zwei Dinge verlangt und nicht eins:** ausdrückliche Pfade **und**
einen eigenen Worktree. Wir befolgen den ersten Teil und haben den zweiten verloren.

---

## 4 · Der Vorschlag — drei Stufen, aufsteigend nach Aufwand

### Stufe 1 · Sofort, kostet zwanzig Minuten

**1a · `strang` wird Pflichtfeld im Auftragskopf.**

```yaml
auftrag:
  id: AUF-IDS-LI-SV
  strang: produktdaten        # <- neu, Pflicht
  status: bereit
  spur: A
  heimat: ticket
```

Erlaubte Werte werden **einmal** festgelegt und stehen in `STRAENGE.md`. Vorschlag für heute:
`hausplaner-3d` · `produktdaten` · `werkzeuge` (der Strang, der `commit-pruefen.sh` und die
Validatoren baut).

**Warum das der stärkste Hebel ist:** Es gibt bereits `scripts/auftrag-pruefen.mjs`, das laut
`AUFTRAGSSCHEMA.md` §1 **jeden** YAML-Block in `docs/auftraege/` liest. Ein fehlendes oder
unbekanntes `strang` macht den Validator rot. Damit ist die Regel **geprüft, nicht gelesen** —
genau die Begründung, aus der das Auftragsschema selbst entstanden ist.

**1b · Die Instanz bindet sich an Rolle *und* Strang.**

Nicht „du bist Generator", sondern **„du bist Generator im Strang `produktdaten`"**. Erster Satz
jeder Instanz: Rolle und Strang ansagen. Danach gilt:

> Ein Auftrag, dessen `strang` nicht meiner ist, wird **nicht** bearbeitet — auch nicht „schnell
> nebenbei". Er wird gemeldet, nicht ausgeführt.

Das kostet nichts und ist die eigentliche Antwort auf deine Frage. Die Rollentrennung schützt
davor, dass jemand seine eigene Arbeit abnimmt. Die Strangbindung schützt davor, dass jemand
fremde Arbeit anfängt.

**1c · Eine Auftragstafel je Strang.**

`AUFTRAGSTAFEL.md` ist 86 KB. Niemand liest sie ganz, also steht am Ende nichts wirklich drin.
Aufteilen in `AUFTRAGSTAFEL-hausplaner-3d.md` und `AUFTRAGSTAFEL-produktdaten.md`; die alte wird
Archiv. Dasselbe Muster, das bei `handoff-status.md` → `STAND.md` schon einmal nötig war.

### Stufe 2 · Diese Woche

**2a · Ballbesitz je Strang statt global.**

`STAND.md` §2 führt heute eine Liste „Wer ist am Ball" — für alle. Sie wird eine Tabelle:

| Strang | Planner | Generator | Evaluator | Prüfer |
|---|---|---|---|---|
| hausplaner-3d | … | Z-05-N1 | Z-05 | PB-048 |
| produktdaten | ruht bis Messwerte | AUF-IDS-LI-SV | wartet | — |

Ein Blick sagt dann, ob ein Strang steht — heute verdeckt der laute Strang den leisen.

**2b · Ein Ort für strangübergreifende Tatsachen.**

Ein kurzer Abschnitt in `STAND.md`: **„Gilt für alle Stränge"**. Dort hinein gehören Sätze wie
der von heute Morgen:

> Push geht **nur** über VS Code auf Yamas Rechner. Aus einer Cloud-Instanz heraus scheitert
> `git push` mit `403 from proxy after CONNECT` — gemessen 02.08. 09:15. Nicht erneut messen.

Ohne diesen Ort misst jeder Strang jede Umgebungsgrenze selbst nach. Heute ist das zweimal
passiert, in zwei Stunden.

**2c · Sperr-Dateien je Strang benennen.**

`STRAENGE.md` kennt die Regel „ein Schreiber pro Datei" bereits, ist aber inhaltlich veraltet.
Neu füllen mit den heutigen Strängen und den heutigen Dateien — mindestens:
`docs/handoff-status.md`, `docs/STAND.md`, `docs/entscheidungen.md`,
`docs/deploy/RELEASE-MANIFEST.md`, `docs/auftraege/AUFTRAGSTAFEL*.md`.

### Stufe 3 · Strukturell, wenn Stufe 1 und 2 nicht reichen

**Getrennte Worktrees wiederherstellen**, wie §1.3 es ohnehin vorschreibt:

```bash
git worktree prune                          # die sechs toten Eintraege raeumen
git worktree add ../ticket-strang-produktdaten -b strang/produktdaten
git worktree add ../ticket-strang-hausplaner -b strang/hausplaner-3d
```

Damit hat jeder Strang **einen eigenen Index** — die Kollision aus §3 ist physisch unmöglich,
nicht nur verboten. Der Preis: zwei Verzeichnisse mehr, und ein Koordinator muss die Branches
zusammenführen (§3.3 sieht die Rolle bereits vor, sie ist nur nicht besetzt).

**BEWERTUNG.** Ich würde Stufe 3 **nicht sofort** machen. Getrennte Worktrees kosten Merge-Arbeit,
und solange nur zwei Stränge laufen und beide diszipliniert mit Pfaden arbeiten, tragen Stufe 1
und 2. Sobald ein dritter Strang dazukommt oder eine Kollision wirklich Arbeit zerstört, ist
Stufe 3 fällig — und dann sofort.

---

## 5 · Was ich ausdrücklich nicht vorschlage

- **Keine neue Rolle.** Der Koordinator aus §3.3 deckt das ab, falls es nötig wird. Ein weiteres
  Rollenwort in einem System, in dem „Prüfer" schon zwei Dinge heißt, macht es schlimmer.
- **Kein zweites Regelwerk.** Alles oben steht bereits in der Betriebsordnung oder ergänzt sie um
  **ein** Feld. Das Auftragsschema ist genau deshalb entstanden: nicht neue Regeln, sondern die
  vorhandenen maschinell durchsetzbar machen.
- **Keine Umbenennung der Rollen.** Das ist ein eigener Posten (`docs/verfahren-rollen-und-ablauf.md`
  §5), und er sollte nicht mit dieser Sache vermischt werden.

---

## 6 · Die Reihenfolge, wenn du nur eines machst

**Nimm 1a und 1b.** Ein Pflichtfeld und ein Satz in der Instanzanweisung. Zusammen lösen sie das
Problem, das du benannt hast: der Generator erkennt am Auftrag selbst, ob er ihn angeht, und der
Validator schlägt an, wenn jemand das Feld vergisst.

Alles andere ist Aufräumen — nützlich, aber nicht die Ursache.

## 7 · Ledger

```
PLANNER 2026-08-02 · Strangtrennung · Spur B · Heimat ticket
  Befund: die Rolle ist modelliert, der Strang nicht. AUFTRAGSSCHEMA kennt kein strang-Feld
  (grep = 0 Treffer); heimat:ticket unterscheidet nichts, weil immer ticket.
  Sechs von sieben Worktrees sind prunable - die Trennung nach BETRIEBSORDNUNG 1.3 ist verfallen,
  alles laeuft in einem Worktree auf einem Branch mit EINEM Index.
  Belegte Kollision heute 09:22 und doppelte Messung desselben 403-Befunds um 09:15/09:23.
  Vorschlag: Stufe 1 (strang als Pflichtfeld + Instanzbindung + Tafel je Strang),
  Stufe 2 (Ballbesitz je Strang, Ort fuer stranguebergreifende Tatsachen, Sperr-Dateien),
  Stufe 3 (getrennte Worktrees) erst wenn noetig.
  Ballbesitz: Yama - 1a und 1b aendern Direktiven, das darf nur er.
```
