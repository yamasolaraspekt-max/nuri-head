# DoR — A-37-22 bis A-37-27 · Votum des Plan-Prüfers

| Feld | Wert |
|---|---|
| Auftrag | `DOR-plan-pruefer-A-37`, generation 3, Lease `fencing_token: 1` |
| Prüfgegenstand | `docs/auftraege/aktiv/A-37-rollen-tor-und-drei-fehlerursachen.md` @ `3dde19ea` (rolle/planner) |
| Ausgangsstand | `1cd33614` — Elter von `3dde19ea`, selbst nachgeprüft |
| Vorlage des Planners | `ereignisse/SPEZ-planner-A-37/planner-CODE_FERTIG.yaml`, 22.08. 00:24:57 |
| **Votum** | **NICHT ERTEILT — 1 tragender Restpunkt, 2 Belegzeiger-Restpunkte** |
| Ball danach | Planner (Restpunkt 1), dann erneute DoR |

**Der Grund in einem Satz:** Sechs Kriterien, und jede einzelne Zahl darin hält der Nachmessung
stand — aber der Absatz zur Sitzungsidentität legt eine Kennung fest, die in derselben Nacht an der
Sitzung zerbrochen ist, die das Blatt geschrieben hat.

---

## 1 · Meldepflichten — selbst gemessen

| Prüfung | Befehl | Ergebnis |
|---|---|---|
| SHA existent | `git cat-file -e 3dde19ea^{commit}` | exit `0` |
| SHA in einem Feld | `planner-CODE_FERTIG.yaml` → `ergebnis_sha` | ja, nicht nur in Prosa |
| Elter = Ausgang | `git log -1 --format=%P 3dde19ea` | `1cd33614` ✔ |
| Zweiglage | `git branch -a --contains 3dde19ea` | nur `rolle/planner` |
| Scope-Diff | `git show --numstat` | `293  4  <ein Pfad>` — deckt sich mit der Zusage |
| Kriterienbestand | `grep -cE '^- \*\*A-37-[0-9]+\*\*'` | `1cd33614` → 21, `3dde19ea` → 27 |
| Lückenlosigkeit | `diff <(seq 1 27) <ist>` | exit `0` — 1..27, keine Dublette |

## 2 · Rot-Lagen — alle sechs selbst nachgemessen

**Vorfrage, die die Vorlage nicht stellt:** Sind die Prüfgegenstände zwischen Basis `1cd33614` und
dem heutigen Zweig `3b2e5334` überhaupt noch dieselben? Sonst wäre eine Rot-Lage womöglich längst
überholt. Über `git rev-parse <sha>:<pfad>` gemessen: `scripts/rueckweg.py`, `scripts/rollen-tor.sh`,
`scripts/commit-pruefen.sh`, `scripts/status-erzeugen.sh`, `.githooks/commit-msg`, `package.json`,
`package-lock.json` — **alle sieben byte-identisch.** Keine Rot-Lage ist durch die neun Commits
dazwischen erledigt worden.

| Kriterium | Zusage des Planners | meine Messung |
|---|---|---|
| A-37-22 | `BAEUME` = 5 | `scripts/rueckweg.py:75-81` → **5** ✔ |
| A-37-22 | Zweig wird nirgends geprüft | `grep -cE 'branch\|symbolic-ref\|abbrev-ref'` → **0** ✔ *(Muster tauglich: `rev-parse` → 5)* |
| A-37-22 | `release` genau 1× | `scripts/rueckweg.py:118` — einziger Treffer ✔ |
| A-37-22 | 15 Bäume, davon 7 Rollenbäume | `git worktree list --porcelain` → **15 / 7** ✔ |
| A-37-22 | `ticket-rolle-*` liefert 6, einer tot | **6**, davon `ticket-rolle-release` detached ✔ |
| A-37-23 | `dirigent` 0× im Tor | `scripts/rollen-tor.sh` → **0** ✔ *(Muster tauglich: `generator` → 14)* |
| A-37-23 | `5c9afbc7` nur auf `rolle/dirigent` | `is-ancestor` gegen HEAD **1**, gegen Integration **1**; `branch --contains` → nur dirigent-Zweige ✔ |
| A-37-24 | BEFUNDNOTIZEN 0× gegen STATUS 8× / 9× | `rollen-tor.sh` **0 : 8**, `commit-pruefen.sh` **0 : 9** ✔ |
| A-37-25 | kein `pre-commit` | `.githooks/` → `commit-msg`, `post-commit`; `test -x` → exit **1** ✔ |
| A-37-25 | `core.hooksPath` ist gesetzt | → `.githooks` ✔ |
| A-37-25 | `commit-msg:84` steigt aus | `[ "$ist_merge" = "1" ] \|\| exit 0` ✔ |
| A-37-26 | Muster erkennt `e9e6ee5b` nicht | Muster **wörtlich aus `scripts/status-erzeugen.sh:188-195` geschnitten** und gefahren → NICHT ERKANNT ✔ |
| A-37-27 | js-yaml nur devDep, im Lock nur transitiv | `package.json` devDep `^4.1.0`; Lock-Wurzel **ohne** Eintrag; `node_modules/js-yaml` 4.1.1 **ohne** dev-Markierung ✔ |

**Eine Probe habe ich hinzugefügt**, die die Vorlage nicht hat: derselbe Musterlauf gegen den
**echten** Zustandscommit `3b2e5334` (A-42-Eintrag des Integrators) → **ERKANNT**, `kennung=A-42`.
Damit ist die Aussage „das Muster arbeitet korrekt, der Betreff passt nicht" nicht nur an
künstlichen Proben belegt, sondern am echten Gegenbeispiel.

**Und die Grenze aus A-37-25 selbst gefahren,** statt sie zu glauben — Wegwerf-Repo, `pre-commit`
und `commit-msg` beide auf `exit 1`:

```
normaler Commit         -> exit 1, "PRE-COMMIT HAT GEFEUERT", kein Commit
mit --no-verify         -> exit 0, KEINE Hook-Ausgabe, Commit entsteht
ohne Flag, Gegenprobe   -> exit 1, Hook greift wieder
```

`--no-verify` übergeht **beide** Hooks. Der Absatz `:682-687` fasst das richtig: technisch nicht
verhinderbar, wirksam gegen Gewohnheit und Versehen, nicht gegen Absicht — mit Absage-Regel.

## 3 · Restpunkt 1 (tragend) — die Kennung zerbricht bei fortgesetzten Sitzungen

Das Blatt legt bei `:674-680` fest: die Kennung besteht aus **Sitzungs-ID + PID des
Sitzungsprozesses + Prozess-Startkennung**, „nie aus der Shell-PID einer Werkzeugrunde", und
begründet das mit: *„die Shell-PID … wechselte … während der Sitzungsprozess konstant blieb."*

**Gemessen in derselben Nacht, an der Sitzung, die dieses Blatt geschrieben hat:**

| Zeit | Ereignis |
|---|---|
| 00:00:48 | Prozess **88928** startet, Planner-Sitzung `ef8ec540` |
| 00:01:30 | Dirigent registriert 88928 in `sitzungen/planner.yaml` |
| 00:16:24 | Prozess **97092** startet — `claude -p --resume ef8ec540…`, **dieselbe Sitzung** |
| 00:17:18 | Lease erteilt auf `owner.pid 88928` — zu diesem Zeitpunkt bereits tot |
| 00:24:57 | `planner-CODE_FERTIG.yaml` meldet `pid: 88928`, `prozess_start: "Sat Aug 22 00:00:48 2026"` |
| 00:31 | **beide** PIDs tot; kein `claude`-Prozess trägt die Sitzungs-ID mehr |

Belegverfahren: `ps -o pid=,lstart= -p <PID>` mit **direkt** gelesenem Exit-Code, an beiden Enden
verifiziert (eigene Sitzung 88049 → exit 0 mit Startzeit; erfundene 999999 → exit 1, leer). Ein
erster Versuch las den Exit hinter einer Pipe und wurde verworfen.

**Die Sitzungs-ID überlebt den Prozess.** PID und Startkennung sind Eigenschaften des *Prozesses*,
nicht der *Sitzung*; `--resume` erzeugt einen neuen Prozess unter derselben Sitzung, und eine
rundenweise laufende Rolle (`claude -p`) hat zwischen zwei Runden **gar keinen** Prozess.

**Folge für den Bau:** `docs/konzept/agentenarchitektur-v2.md` (`0d897b0e`, dort `:154-158`) lässt
eine Sperre automatisch entfernen, wenn „PID + Startkennung nicht mehr übereinstimmen". Ein Tor, das
das Kriterium wörtlich umsetzt, hätte der Planner-Sitzung heute Nacht mitten in der Arbeit die Lease
entzogen — und würde es bei jedem Rundenwechsel wieder tun. Die dort genannte Alternative `flock`
(`:159-161`) deckt den Fall nicht ab: sie gibt die Sperre beim Prozessende frei, bei `--resume` also
ebenfalls, nur automatisch.

**Im Blatt nicht behandelt:** `resume` kommt **0×** vor, „Werkzeugrunde" genau 1× — und dort nur als
das, was die Kennung *nicht* sein soll.

**Was fehlt, ist nicht die Kennung, sondern ihr Verhalten:** Was gilt, wenn die eingetragene
Prozessidentität nicht mehr existiert, die Sitzung aber weiterläuft? Das ist eine Festlegung des
Planners, nicht meine.

## 4 · Restpunkt 2 — ein Belegzeiger in Commit-Form, der kein Commit ist

`:674` zitiert „Befund `79285cf2`". Gemessen: `git cat-file -e 79285cf2^{commit}` → exit **128**
(Verfahren verifiziert: `3dde19ea` → 0, erfundenes `deadbeef1` → 128). Es ist die **Sitzungs-ID**
`79285cf2-4231-4f71-8dfc-3306e3371109` (PID 70499), belegt in
`.ticket-steuerung/ereignisse/SITZUNG-70499-ROLLENWECHSEL/sitzung-70499-meldung.yaml`.

Der Befund selbst ist echt — nur seine Adressform nicht. Im selben Abschnitt stehen drei weitere
Backtick-Achtsteller (`26c46f31`, `e5aa5af7`, `e9e6ee5b`), **alle drei existieren als Commit.** Die
Form verspricht damit etwas, das dieser eine Zeiger nicht einlöst. *Nicht schlimm, aber genau die
Klasse, die ein Blatt unprüfbar macht, das sonst durchgängig prüfbar ist.*

## 5 · Restpunkt 3 (leicht) — Zeiger der Meldung gelten für den Ausgangsstand

`planner-CODE_FERTIG.yaml` nennt „die sechs offenen Plan-Prüfer-Befunde (:484-514) … gehören der DoR
(:516)". In `1cd33614` trifft das genau — `:484` ist die Abschnittsüberschrift. Im **gelieferten**
Stand `3dde19ea` steht sie bei `:773`; `:484` trägt dort einen Satz über `npm ci`. Der Zeiger zeigt
nicht ins Leere, sondern **auf etwas anderes** — die Meldung beschreibt ihr Ergebnis mit den
Koordinaten ihres Ausgangs.

## 6 · Ausdrücklich gewürdigt

- **Drei Arten von Rot getrennt** (Produkt / Vergleich / Schutz, `:508-518`) mit der Begründung, dass
  sie verschieden verschwinden — und daraus die Folge, dass Schutz-Kriterien eine *ausgelöste*
  Negativprobe verlangen statt eines Grep-Treffers. Das ist der Unterschied zwischen einer Prüfung
  und dem Anschein einer Prüfung.
- **Fünf Negativproben plus Positivprobe** (`:660-672`), letztere mit der richtigen Begründung: ein
  Tor, das nur sperrt, ist von einem kaputten nicht zu unterscheiden.
- **Absage-Regeln** an zwei Stellen — bei `--no-verify` und bei A-37-27 (zieht der Lauf fremde
  Paketfassungen mit, wird abgesagt statt committet).
- **A-37-18 verliert die feste Zahl** und verweist auf die Liste: eine Zahl im Kriterium misst ab dem
  nächsten Rollenzugang die Zeit statt den Bau. Dieselbe Umstellung wie seinerzeit bei A-37-11.
- **Das Nicht-Ziel „Kein Hook" (`:229`) bleibt stehen** und wird als ÜBERHOLT gekennzeichnet, statt
  gelöscht zu werden — die Hausform der beiden `node_modules`-Nicht-Ziele.
- **Eigene Berichtigung vor dem Commit offengelegt:** der erste Entwurf schrieb die A-37-26-Probe als
  gemessen, obwohl sie nur gelesen war; nachgeholt, dann committet. Das gehört genannt, weil es der
  Grund ist, warum die Zahlen dieser Vorlage tragen.

## 7 · Was ich nicht getan habe

Ich habe **kein** Kriterium formuliert und keine Formulierung vorgeschlagen — das ist Planner-Arbeit
(`rollen/plan-pruefer.yaml` gen 3, `verboten:`). Bei Restpunkt 1 benenne ich die **Lücke** und die
Messung, nicht ihre Schließung. Vor der Vorlage hatte ich dieselbe Ist-Lage zu A-37-22 unabhängig
gemessen (fehlende Bäume in `BAEUME`) und **bewusst nicht** an den Planner gemeldet: wer die Vorgabe
schreibt, kann sie nicht abnehmen. Er hat den schärfsten Punkt — `ticket-release-pruefung` bestimmt
bei `:118` das Ziel und steht nicht in der Liste — selbst gefunden.

---

**Ball: Planner** für Restpunkt 1 (und, wenn er ohnehin anfasst, 2 und 3). Danach erneute DoR durch
mich. Der Bau durch den Generator wartet auf das erteilte Votum.

---

## Nachtrag 00:38 — Yamas Zielregel gegen das Blatt gemessen

Nach dem Votum (`1568610f`, 00:34) hat der Dirigent um 00:36 Yamas Zielregel zugestellt
(`ereignisse/DOR-plan-pruefer-A-37/dirigent-antwort-zielregel-lease-yama.yaml`). Sie bestätigt den
Restpunkt wörtlich — *„Ein System, das eine Lease wegen der alten PID automatisch entfernt, könnte
einem arbeitenden Planner die Lease entziehen. Genau das muss der Plan-Prüfer jetzt in der DoR
beanstanden."* — und ist genauer als meine Fassung. Deshalb messe ich das Blatt gegen ihre sechs
Punkte, damit der Restpunkt abarbeitbar wird statt bloß benannt:

| # | Yamas Zielregel | in `3dde19ea` |
|---|---|---|
| 1 | stabile Identität = Sitzungs-ID | **getragen** (`:675`) |
| 2 | pro Lauf: aktuelle PID + Startkennung | **fehlt** — genannt ohne Laufbezug |
| 3 | während Schreibarbeit erneuerter Heartbeat | **fehlt** (`Heartbeat` 0×) |
| 4 | Übernahme nur bei abgelaufenem Heartbeat **und** fehlendem Lauf | **fehlt** (`verwaist` 0×) |
| 5 | Fencing-Token bleibt maßgeblich | **fehlt** (`Fencing` 0×) |
| 6 | eine alte PID allein erklärt nie „verwaist" | **fehlt** |

**Messfehler auf dem Weg dorthin, korrigiert:** ein erstes `grep -ciE 'lease'` meldete **27**
Treffer — sämtlich `Release-Prüfer` und `ticket-rolle-release`. Mit Wortgrenze gemessen kommt
**`Lease` im Blatt null Mal vor**, ebenso `Fencing`, `Heartbeat`, `verwaist`, `Z0-I2`, `Z0-I3`.

**Was das für den Zuschnitt heißt** — und hier höre ich auf, weil es Planner-Arbeit ist: Die Punkte
3 bis 5 beschreiben die Lease-Verwaltung und haben ihren Ort sichtbar in Z0-I2/Z0-I3
(`agentenarchitektur-v2.md` §8; `.ticket-steuerung/README.md:66` weist die Barriere ausdrücklich
A-37-Erweiterung **und** Z0-I2/Z0-I3 zu). **Die Punkte 2 und 6 dagegen gehören unausweichlich in
A-37-25 selbst**, denn dieses Kriterium sagt dem Tor, woran es eine Sitzung erkennt: Ein Tor, das
eine *gespeicherte* PID als Lebensnachweis nimmt, sperrt einen arbeitenden Lauf aus — heute Nacht
messbar geschehen. Wie die Grenze zwischen A-37 und Z0-I2/Z0-I3 gezogen wird, entscheidet der
Planner, nicht ich.

**Das Votum bleibt NICHT ERTEILT**, Restpunkt 1 jetzt in sechs prüfbaren Zeilen statt einer Prosa.
