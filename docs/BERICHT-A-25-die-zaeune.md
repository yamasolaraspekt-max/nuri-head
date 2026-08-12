# Baubericht A-25 — jeder Datensatz bekommt seinen eigenen Zaun

```yaml
auftrag: "A-25"
rolle: "generator"
blatt: docs/auftraege/aktiv/A-25-die-zaeune-fehlen.md
art: "BAU — Zaeune setzen. KEIN Text geaendert, KEIN Wert, KEIN Datensatz entfernt."
basis_sha: fc0abdd5
gebaut_am: "13.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **Ein Befund gegen das Kriterium, und er weitet es:** *A-25-1b nennt **einen** unzuordenbaren
> Block. Gemessen sind es **FÜNF**.* **Das Kriterium sagt „jeder Block" und nennt einen als Beleg —
> ich habe alle fünf behandelt und melde die Differenz, statt sie stillschweigend zu schließen.**

## Was gebaut wurde

```text
scripts/a25-zaeune.mjs      NEU — misst, sichert ab, schreibt
docs/STATUS.md              20 Zeilen dazu, 0 entfernt
                              15 Zaunzeilen (5 x Schliesser + Leerzeile + Oeffner)
                               5 vorgang:-Felder
```

## A-25-1 (TRAGEND) · Das Muster im Wortlaut, und warum es so aussieht

**Das Kriterium verlangt das Muster im Bericht. Hier ist die Zaunlogik, wie sie im Werkzeug steht:**

```js
const m = z.match(/^(\s*)```(.*)$/);
const info = m[2].trim();
if (!offen) { if (info === '') return; offen = { start: i, info }; return; }
if (info === '') { bloecke.push({ ...offen, ende: i }); offen = null; return; }
lage[i] = bloecke.length;          // ```yaml INNERHALB eines Blocks ist INHALT
```

> **Nach CommonMark schliesst nur ein Zaun OHNE Info-String.** *`` ```yaml `` ist ein ÖFFNER und
> niemals ein Schliesser.* **Ein Muster, das jeden Zaun als Umschalter zählt, meldete beim Planner
> EINEN Bereich statt zwei und 35 Datensätze „ausserhalb" — und es könnte nach dem Bau NULL melden
> und grün sein, ohne dass etwas behoben wäre.**

**Vorher / nachher, mit demselben Werkzeug gemessen:**

```text
vorher    2 Bereiche mit mehreren Datensaetzen · 5 unzuordenbare Bloecke   -> ROT
nachher   0                                    · 0                        -> GRUEN
Bloecke gesamt   310 -> 315
```

## A-25-3 · Die sieben Datensätze, mit ihren Zeilen VOR dem Bau

```text
Zeile 1245-1317    73 Zeilen · 2 Datensaetze:  A-08 · A-09
Zeile 7726-8375   650 Zeilen · 5 Datensaetze:  W-06 · W-31 · A-24 · A-23 · A-22
```

**Die Zeilennummern weichen von denen im Auftragsblatt ab** *(dort `1243-1315` und `7525-8084`)* —
**und das ist kein Widerspruch, sondern der Grund, warum das Kriterium „am Bau-Stand erheben"
verlangt.** *Fünf Rollen schreiben in diese Datei; zwischen Schnitt und Bau ist sie um über 290
Zeilen gewachsen.* **Die Datensätze sind zeichengleich dieselben sieben.**

## A-25-4 · Zwei Bereiche, zwei verschiedene Fehlerformen

```text
1245   der Zaun ist FALSCH GESETZT: bei Zeile 1284 stand ein ```yaml MITTEN im Block,
       wo ein Schliesser stehen muesste. Nach CommonMark ist das INHALT — der Block
       lief einfach weiter.
7726   der Zaun ist KORREKT, aber fuenf Datensaetze liegen darin. Der Bereich trug am
       Morgen einen; vier sind im Lauf des Tages hineingewachsen.
```

**Beide werden von derselben Regel behoben** — *vor jeden Datensatz ab dem zweiten kommt
Schliesser + Leerzeile + Öffner* — **aber wer nur eine Form sucht, findet die andere nicht.**

## A-25-1b · Das Kriterium nennt EINEN Fall, gemessen sind es FÜNF

| Zeile *(vor dem Bau)* | Block beginnt mit | Zugehörigkeit |
|---|---|---|
| 3418 | `fehlerklasse: UMGEBUNG` | *Befund des Evaluators — Index trägt 16 unbeschlossene Löschungen* |
| 3596 | `fehlerklasse: BEWEIS` | *Befund des Evaluators — Stichprobe durch Vollerhebung ersetzt* |
| 4529 | `fehlerklasse: SPEC` | *Befund des Evaluators — die Entscheidung, die §16 aussetzt* |
| 4673 | `fehlerklasse: SPEC` | *Befund des Evaluators — Vertretungsentscheid zu Prozessprüfung 03* |
| **5475** | `ballbesitz: plan-pruefer` | **VORLAGE AN DEN PLANNER** — *der im Kriterium belegte Fall* |

**Jeder dieser Blöcke trägt ein `ballbesitz:`-Feld und keine Auftragskennung.** *Der fünfte ist der,
den der Planner einen Tag lang nicht gefunden hat — er sagt es inzwischen im Block selbst.*

> **Vier davon sind Befundblöcke einer Prüfrolle, und ihr `ballbesitz:` ist Prosa** — *etwa „offen —
> ich messe und melde, ich räume den Index eines anderen nicht auf".* **Für einen Zähler ist das
> derselbe Fall: ein Zustandsfeld ohne Zuordnung.** *Das Kriterium sagt „**jeder** Block, der
> Zustandsfelder trägt" und nennt einen als Beleg — ich habe alle fünf behandelt.*

**Die Form, die ich gewählt habe** *(das Kriterium überlässt sie ausdrücklich dem Bauenden)*: **ein
`vorgang:`-Feld mit der Überschrift des Abschnitts, in dem der Block steht.** *Keine erfundene
Auftragsnummer — eine Vorlage ist kein Auftrag, und ein Befund ist es auch nicht.*

## A-25-2 (SCHUTZGRENZE) · Kein Inhalt geändert

**Das Werkzeug prüft VOR dem Schreiben und bricht ab statt zu schreiben:**

```text
1  die sortierte Liste aller auftrag-Werte    vorher = nachher   69
2  die sortierte Liste aller zustand-Werte    vorher = nachher   60
3  ALLE Zeilen ausser Zaeunen und Leerzeilen  vorher = nachher
```

**Die dritte ist die schärfste** — *sie lässt genau zwei Sorten Änderung zu und sonst nichts.*

**Am Ergebnis gegengeprobt:**

```text
git diff --numstat docs/STATUS.md    ->   20  0  docs/STATUS.md
entfernte Zeilen                     ->   0
die 20 einzeln angesehen             ->   15 Zaunzeilen (davon 5 leer) + 5 vorgang:-Felder
```

> **Die fünf `vorgang:`-Zeilen sind NICHT vom Skript gesetzt.** *Sie hätten seine dritte Gegenprobe
> ausgelöst — zu Recht, denn sie sind Text.* **Sie stehen einzeln und zeilengenau gesetzt, jede mit
> der Überschrift ihres Abschnitts als Beleg.**

## A-25-6 · Nebenläufigkeit

```js
const kopfVorher  = execSync('git rev-parse HEAD')…
…                                   // messen, umbauen, dreifach gegenproben
const kopfNachher = execSync('git rev-parse HEAD')…
if (kopfVorher !== kopfNachher) { …abbrechen, NICHT schreiben… }
```

**Und es wird nur `docs/STATUS.md` geschrieben** — *keine fremde Datei angefasst.*

## A-25-5 · Die Fangprobe ist GEFAHREN

```text
md5-Anker docs/STATUS.md   65be639c14012c9d177bc2d8d044bb71
Mutation                   EINEN der neuen Schliesser entfernt (vor A-23)
Ergebnis                   ROT — „1 Bereich(e) mit mehreren Datensaetzen: A-24 · A-23"
                           und zwar an genau der mutierten Stelle
Rueckschrift               md5 gegen den Anker: identisch
Endstand                   GRUEN, 0 und 0
```

## Mein ganzer Bau steckt in einem FREMDEN Commit — und er hält trotzdem

**Während ich baute, hat der Planner `docs/STATUS.md` committet** *(`c8dd6d49`, W-05/1)* — **und
meine 20 Zeilen als Beifang mitgenommen.** *Gemessen: `git diff docs/STATUS.md` ist **leer**, HEAD
trägt alle fünf `vorgang:`-Felder und alle fünf neuen Zäune.*

> **Das ist inzwischen das neunte Mal, dass eigene Zeilen so wandern** — *und hier trifft es einen
> Bau, dessen ganzer Gegenstand die Struktur dieser Datei ist.* **Ich trage nichts nach: der
> Arbeitsbaum ist hash-identisch mit HEAD, es gibt nichts zu committen.** *Gemeldet, damit der
> Evaluator meinen Bau nicht in einem Commit von mir sucht.*

**Und die Wirkung ist am VERÖFFENTLICHTEN Stand gegengeprobt, nicht an meinem Arbeitsbaum:**

```text
node scripts/a25-zaeune.mjs   ->   GRUEN, 0 Bereiche · 0 unzuordenbare Bloecke
Datensaetze 70 · zustand-Felder 61
```

> **70 statt der 69, die mein Lauf zählte** — *der Planner hat mit demselben Commit einen neuen
> Datensatz eingefügt.* **Und er steht in seinem eigenen Zaun: die Regel greift bereits für Arbeit,
> die nach meinem Lauf entstanden ist.** *Das ist der bessere Beleg, als mein eigener Lauf ihn
> liefern konnte — er zeigt, dass nicht nur die sieben alten Fälle behoben sind, sondern dass der
> nächste Griff die Struktur nicht wieder zerstört.*

## Was ich NICHT getan habe

```text
KEIN Wert, KEIN Feldname, KEIN Vermerktext geaendert — die dritte Gegenprobe
  laesst nur Zaeune und Leerzeilen durch, und sie lief VOR dem Schreiben.
KEIN Datensatz entfernt oder zusammengefasst — 69 vorher, 69 nachher.
KEINE Reihenfolge geaendert — eingefuegt wird von hinten nach vorne, damit
  die Zeilennummern waehrend des Laufs nicht wandern.
KEINE Auftragsnummer erfunden fuer die fuenf Bloecke ohne Kennung.
DEN TAKT-SCAN des Evaluators NICHT angefasst — seine Methode gehoert ihm.
```

## must_preserve und Rückweg

| Richtung | Ergebnis |
|---|---|
| geändert | **1** — `docs/STATUS.md`, 20 Zeilen dazu, 0 entfernt |
| hinzugefügt | **1** — `scripts/a25-zaeune.mjs` |
| entfernt | **0** |
| `resources/`, `app/` | **0** in allen drei Richtungen |
| Rückweg | `git revert`; der Bau besteht aus Zaunzeilen und fünf Feldern |
