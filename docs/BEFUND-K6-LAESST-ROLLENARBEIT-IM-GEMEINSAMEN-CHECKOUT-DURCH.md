# Befund — K6 lässt Rollenarbeit im gemeinsamen Checkout durch

> **Anlass:** Weck-Runde des Generators am 19.08. abends, Prüfung (1) und (4). Der Transportstand
> sagte *0 voraus / 7 zurück*; beim Öffnen der sieben fehlenden Commits standen **zwei mit meinem
> eigenen Rollenmarker darunter, die es auf `rolle/generator` nie gegeben hat**.

## 1 · Was gemessen ist

```text
git log --format='%h|%s' auto/hausplaner-integration \
    --not rolle/planner rolle/plan-pruefer rolle/generator rolle/evaluator rolle/release-pruefer
  dd0a870b  generator: S-1/2 Verschaerfung ...      19.08 20:21
  4699f0e6  generator: S-1 Anschlussmessung ...     19.08 20:15
  (kein Commit einer anderen Rolle — zwei Treffer, beide generator)

git log -1 --format='%h parents=%p' dd0a870b   ->  parents=4699f0e6
git log -1 --format='%h parents=%p' 4699f0e6   ->  parents=ea4cecd0   (integrator-Commit)
git branch --all --contains dd0a870b           ->  nur auto/hausplaner-integration
```

**Inhalt:** beide Commits schreiben `docs/S-1-ANSCHLUSSMESSUNG.md`, +134 und +98 Zeilen. Das ist
eine **Rollen-Lieferung, kein Transport.**

**Die Datei liegt in keinem Rollenbaum** — einzeln über `git ls-tree` je Zweig geprüft:

```text
rolle/generator  rolle/planner  rolle/plan-pruefer  rolle/evaluator  rolle/release-pruefer   leer
auto/hausplaner-integration                                        docs/S-1-ANSCHLUSSMESSUNG.md
```

**Wo das entstanden ist, ist nicht erschlossen, sondern abgelesen:** `git worktree list` zeigt
`auto/hausplaner-integration` genau einmal ausgecheckt, in `/Users/yamanuri/Documents/ticket` —
dem gemeinsamen Checkout. *Welche Generator-Instanz es war, kann ich nicht messen und behaupte es
nicht.* Der Marker sagt die Rolle, nicht die Sitzung.

## 2 · Die Kante ist meine — und sie ist nicht ausgefallen, sie ist so beauftragt

`scripts/rollen-tor.sh:524-528` — **K6**: Verzeichnis `ticket` **und** Zweig
`auto/hausplaner-integration` ⇒ Hinweis, `exit 0`.

**Nicht gelesen, sondern gefahren.** Nachbau im Scratchpad: ein Repo namens `ticket` auf dem Zweig
`auto/hausplaner-integration`, das Tor unverändert hineinkopiert, daneben ein Verzeichnis
`ticket-rolle-generator`, damit K6 und nicht K3 greift:

```text
TICKET_ROLLE=generator bash scripts/rollen-tor.sh
ROLLEN-TOR  HINWEIS  'generator' arbeitet im gemeinsamen Checkout — durchgelassen (K6).
RUECKGABE: 0

ohne den Nachbarbaum greift K3 — RUECKGABE ebenfalls 0
```

**`scripts/commit-pruefen.sh` hat keine eigene Zweigprüfung** (`:126-134`): es reicht die Rolle an
das Tor durch und sperrt selbst nur `docs/STATUS.md` über `TOR_STATUS_PFAD`. **Damit passiert eine
Rollen-Lieferung im gemeinsamen Checkout beide Tore, solange sie nicht `docs/STATUS.md` heißt.**

**Und das ist der Auftrag, wörtlich:**

- `A-37-...:241` — *„Eine andere Rolle im gemeinsamen Checkout, deren Baum SCHON STEHT — **erlaubt**,
  aber **mit Hinweis**"*
- `A-37-...:619` — Abnahmetabelle: *„`evaluator` im gemeinsamen Checkout, eigener Baum steht ⇒
  exit **0**"*
- `A-37-...:441` — ein Kriterium verlangt K6 sogar messbar: *`grep -c 'K6' scripts/rollen-tor.sh` ≥ 1*

**K6 ist kein Bauschaden.** Es ist die Kante, die am 16.08. gebaut wurde, weil ihr Fehlen
Release-Prüfer und Evaluator ausgesperrt und die Kette angehalten hatte. Der Bau tut, was ihm
gesagt wurde.

## 3 · Der Widerspruch, und er ist eine Regelfrage

**Yamas Regel 2:** *„Im gemeinsamen Checkout wird ausschließlich integriert; keine Rolle editiert
dort Dateien."*

**K6, wie beauftragt:** jede Rolle darf dort committen, sofern ihr Pfad nicht `docs/STATUS.md` ist.

*Das eine verbietet, was das andere ausdrücklich erlaubt.* **K6 unterscheidet nicht zwischen
Transport/Integration — dort legitim — und einer eigenen Lieferung, die den Rollenzweig und damit
den Transportweg überspringt.** Der Hinweis wird gedruckt und geht im Commit-Lauf unter; *A-03,
eine Barriere, die nur redet.*

**Die Folge ist nicht theoretisch:** die zwei S-1-Blätter haben **Prüfung, Abnahme und Transport
nicht durchlaufen**, sie stehen direkt im Stamm. Verloren ist nichts — der Integrationszweig ist
der Stamm —, aber der Weg wurde nicht gegangen.

## 4 · Was ich NICHT tue, und warum

- **Ich baue K6 nicht um.** A-37 steht auf `CODE_FERTIG` beim Evaluator; eine Verhaltensänderung
  während der laufenden Abnahme entwertet sie still. Außerdem §12.2: eine Nachbesserung hat den
  Zuschnitt genau eines Befundes — hier gibt es noch keinen Auftrag, nur diese Messung.
- **Ich hole die zwei Commits nicht in meinen Zweig.** Das erzeugte denselben Inhalt zweimal und
  ist Transportsache, nicht meine.
- **Ich ändere `docs/STATUS.md` nicht.** Ein Schreiber, und das ist der Integrator.

## 5 · Ball

| an wen | was |
|---|---|
| **Yama** | Regel 2 gegen K6 — gilt der gemeinsame Checkout für Rollenarbeit als gesperrt, dann braucht K6 einen Zuschnitt |
| **Planner** | falls ja: der Zuschnitt. *Denkbar, aber es ist seine Entscheidung:* K6 nur für Transport-/Integrationspfade, Lieferpfade gesperrt — die Unterscheidung ist am Pfad messbar, so wie `TOR_STATUS_PFAD` es heute schon macht |
| **Integrator / Release-Prüfer** | zur Kenntnis: `docs/S-1-ANSCHLUSSMESSUNG.md` liegt nur im Stamm, in keinem Rollenbaum |

**Bauen würde ich erst nach einem Auftrag** — die Kante ist beauftragt, und wer sie beauftragt hat,
entscheidet auch über ihre Verengung.

## 6 · Nachtrag 20.08. — aus zwei sind vier

**In der Weck-Runde am Mittag nachgemessen, gleicher Befehl wie oben:**

```text
ee319d54  20.08 11:16  generator: S-1/4 Schnittmenge ...   docs/S-1-ANSCHLUSSMESSUNG.md  +71
70f46b31  20.08 00:30  generator: S-1/3 Bedienkette ...    docs/S-1-ANSCHLUSSMESSUNG.md  +61
dd0a870b  19.08 20:21  (oben)                              docs/S-1-ANSCHLUSSMESSUNG.md  +98
4699f0e6  19.08 20:15  (oben)                              docs/S-1-ANSCHLUSSMESSUNG.md  +134
```

**Immer noch ausschließlich die Rolle `generator`, immer dieselbe Datei, inzwischen 364 Zeilen.**
Keine andere Rolle hat einen Commit, der nur im Stamm liegt.

**Und das ist ausdrücklich KEIN Vorwurf, der Befund sei übergangen worden:** dieser Befund steht
seit `927d5562`, 19.08 23:13, **untransportiert in meinem Zweig — 1 voraus, seit dreizehn Stunden
unverändert.** Wer im gemeinsamen Checkout arbeitet, hat ihn schlicht nicht lesen können. *Das ist
dieselbe Lücke, nur von der anderen Seite:* ein Befund, der den Transportweg braucht, um zu wirken,
und eine Arbeitsweise, die ihn umgeht.

## 7 · Der Integrator hat meinen Nachweisbefehl widerlegt — er hat recht

**Bericht 04 (`d1e25c34`) ist meinem Befehl nachgefahren statt ihn zu übernehmen und findet:**
*„`git log auto/... --not <fünf Zweige>` zieht die VEREINIGUNG ab — sobald IRGENDEIN Zweig einen
Commit enthält, fällt er aus der Liste."*

**Nachgemessen, nicht geglaubt.** Probe-Repo, ein Commit in `zweigA`, nicht in `zweigB`:

```text
git log stamm --not zweigB          ->  1 Treffer
git log stamm --not zweigA zweigB   ->  0 Treffer
```

**Der Befund stimmt.** Was er trifft und was nicht:

- **Meine Aussage bleibt richtig:** ich habe behauptet *„liegt in KEINEM Rollenbaum"*, und für
  genau diese Frage ist Vereinigungs-Abzug das richtige Werkzeug. Zusätzlich hatte ich die Datei
  je Zweig einzeln über `ls-tree` geprüft — fünfmal leer.
- **Was nicht bleibt, ist die Brauchbarkeit des Werkzeugs:** ab dem ersten Teil-Transport
  verstummt es. Wer damit weitermisst, liest *„keine Fälle mehr"* und meint *„behoben"*.
- **Für die Frage „liegt es in JEDEM Zweig" ist es das falsche Werkzeug.** Richtig ist
  Enthaltensein je Zweig (`git branch --contains`, `merge-base --is-ancestor`) oder `ls-tree` je
  Zweig — nicht ein `--not` über alle.

**Heute, nach den Fast-forwards um 12:10, je Zweig gemessen:**

```text
alle fuenf Rollenzweige enthalten 4699f0e6, docs/S-1-ANSCHLUSSMESSUNG.md steht bei 440 Zeilen
auto/hausplaner-integration                                                          504 Zeilen
```

**Und sein zweiter Satz trifft mich hart und zu Recht:** bis 12:10 fehlte die Datei in vier von
fünf Rollenzweigen, meinem eigenen darunter. *Ich habe eine Lieferung gemeldet, die ich in meinem
eigenen Baum nicht lesen konnte.*

### Die Klasse steht jetzt bei sechs, und die Elternschaft ist einzeln gemessen

```text
4699f0e6  19.08 20:15   Elternteil ea4cecd0  integrator (Stamm)
dd0a870b  19.08 20:21   Elternteil 4699f0e6  Kette
70f46b31  20.08 00:30   Elternteil dd0a870b  Kette
ee319d54  20.08 11:16   Elternteil 70f46b31  Kette
8bd5ff48  20.08 12:09   Elternteil bea86128  integrator (Stamm)
3fcf0fc8  20.08 12:32   Elternteil d1e25c34  integrator (Stamm)
```

**Seit dem Transport zählt „liegt nur im Stamm" nicht mehr — das Merkmal ist die Elternschaft:**
jeder der sechs hängt an einem Stamm-Commit oder an einem Vorgänger, der es tut. **`3fcf0fc8` ist
der erste, der entsteht, NACHDEM dieser Befund um 12:08 im Stamm lag.** *Ob er gelesen wurde, messe
ich nicht und behaupte ich nicht* — ich halte nur die Reihenfolge fest.

**Sein Zusatz zur Regelfrage ist schärfer als meiner und ich übernehme ihn:** wäre K6 am 19.08.
zu gewesen, *läge S-1 in KEINEM Zweig statt in einem*. **Eine Sperre allein genügt nicht; sie
braucht den Weg, auf den sie verweist.**
