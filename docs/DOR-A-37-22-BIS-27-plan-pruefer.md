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

---

## Zweiter Nachtrag 00:42 — wo die beanstandeten Stellen entstanden sind

Ich habe den **Auftrag** gemessen, gegen den der Planner gearbeitet hat
(`.ticket-steuerung/rollen/planner.yaml`, generation 6, geschrieben 00:10:04). Das Ergebnis
berichtigt die Zuschreibung zweier Restpunkte und eine Aussage von mir selbst.

### Restpunkt 1 stammt wörtlich aus dem Auftrag

| Quelle | Wortlaut |
|---|---|
| Auftrag `:20` | „Sitzungsidentitaet = Sitzungs-ID + PID des Sitzungsprozesses + Prozess-Startkennung (nicht Shell-PID, Befund 79285cf2)" |
| Blatt `:675-676` | „die Kennung aus **Sitzungs-ID + PID des Sitzungsprozesses + Prozess-Startkennung**, **nie** aus der Shell-PID einer Werkzeugrunde" |

**Der Planner hat seinen Auftrag wortgetreu umgesetzt.** Die Regel, die ich als unvollständig
beanstande, ist nicht seine Formulierung — er hat sie übernommen, wie sie zugestellt wurde.

**Die Zeitachse zeigt dasselbe:**

| Zeit | Ereignis |
|---|---|
| 00:10:04 | Auftrag gen 6 mit der unvollständigen Kennung |
| 00:21:54 | Z0-I3-Vorgabe erhält Yamas Ergänzung „Identität bei headless Sitzungen" |
| **00:23:15** | **Planner-Commit `3dde19ea`** |
| 00:25:55 | mein Hinweis mit der Messung |
| 00:29:08 | Yamas Zielregel in sechs Punkten |
| 00:34:24 | mein Votum |

Die Präzisierung ist **sechs Minuten nach** der Lieferung entstanden. Das Votum bleibt **NICHT
ERTEILT** — Yama verlangt die Beanstandung ausdrücklich, und das Blatt trägt die vollständige Regel
nicht. Aber es ist **kein Ausführungsmangel, sondern ein nachgereichter Maßstab.**

*Bemerkenswert ist, dass der Auftrag den Mechanismus, der die Regel bricht, selbst benennt:* `:5`
sagt „Der Dirigent stoesst die Sitzung alle 4 Minuten headless an (`claude -p --resume`)". Die
Ursache stand im selben Dokument wie die Regel, die sie aushebelt — fünfzehn Zeilen darüber.

### Restpunkt 2 ebenfalls

`79285cf2` steht wörtlich im Auftrag `:20` als „Befund 79285cf2". Der Planner hat den Belegzeiger
übernommen, wie er zugestellt wurde. Dass es eine **Sitzungs-ID** und kein Commit ist, ist damit ein
Fehler der Zustellung, nicht der Verarbeitung. *(Der Restpunkt bleibt — ein Blatt sollte einen
Zeiger prüfen, bevor es ihn erbt. Aber die Quelle ist benannt.)*

### Berichtigung einer eigenen Aussage

Abschnitt 7 dieses Blattes sagt: *„Er hat den schärfsten Punkt — `ticket-release-pruefung` bestimmt
bei `:118` das Ziel und steht nicht in der Liste — selbst gefunden."* **Das ist falsch, und ich
nehme es zurück.** Der Auftrag `:17` gibt wörtlich vor: „ALLE Rollenbaeume … **inkl.
ticket-release-pruefung (heute Sonderfall :118)** und ticket-rolle-dirigent". Der Punkt *und* die
Zeilennummer standen in der Vorgabe.

**Was der Planner tatsächlich selbst geleistet hat** — und das bleibt bestehen: die Ist-Lage dazu
gemessen (`grep -n 'release'` → genau ein Treffer bei `:118`) und die Folge ausformuliert, dass der
Baum, der das Ziel bestimmt, selbst nie nachgezogen wird. Messen und Folgern ist seine Arbeit;
Finden war es nicht.

### Das Muster dahinter

**Ein Auftrag, der eine unvollständige Regel wörtlich vorgibt, erzeugt ein Blatt mit derselben
Lücke — und die DoR beanstandet dann den Ausführenden statt die Quelle.** Ohne diese Messung hätte
mein Votum drei Mängel dem Planner zugeschrieben, von denen zwei bei der Auftragserteilung
entstanden sind. Deshalb gehört zur DoR nicht nur das Blatt, sondern auch der Auftrag, gegen den es
gebaut wurde.

**Ball:** unverändert Planner für die Nachbesserung des Blattes — zusätzlich **Dirigent** für die
Herkunft: die Kennung in künftigen Aufträgen um den Laufbezug ergänzen und `79285cf2` als
Sitzungs-ID kenntlich machen, nicht als Commit.

---

# DoR Runde 2 — `213edd28` · **ERTEILT**, mit zwei Anmerkungen

| Feld | Wert |
|---|---|
| Prüfgegenstand | `213edd28` (rolle/planner), Elter `3dde19ea` ✔, Scope `79 / 7` in einem Pfad ✔ |
| Vorlage | `planner-CODE_FERTIG-2.yaml`, 00:43:14 · Lease `fencing_token: 2` |
| **Votum** | **ERTEILT** — die drei Restpunkte sind geschlossen, die Kriterien sind baureif |
| meine Lease | `fencing_token: 4` |

## Die drei Restpunkte — alle geschlossen

**Restpunkt 1 (tragend).** Yamas Zielregel steht als Tabelle mit sechs einzeln abnehmbaren Punkten.
Alle sechs Wortzahlen nachgezählt: `Heartbeat` 5, `Fencing` 2, `verwaist` 1, `resume` 6,
`Z0-I2` 4 — jede trifft. Dazu ausgelöste **Negativprobe** *und* **Gegenprobe** (`:738-741`) und eine
**Absage-Regel** (`:742-744`), die genau den Schaden ausschließt, den ich gemessen hatte.

**Seine Messung habe ich nachgefahren, solange sie messbar war** — Prozessangaben sind flüchtig:

```
88928  ps-exit 1  tot          97092  ps-exit 1  tot
12334  ps-exit 1  tot          16345  ps-exit 0  LEBT, Start "Sat Aug 22 00:40:08 2026"
```

Alle vier treffen, die Startzeit von `16345` wörtlich. Verfahren an beiden Enden verifiziert.
Auch sein Lebensnachweis-Kriterium hält: **genau ein** laufender Prozess trägt die Sitzungs-ID in
der Kommandozeile — der zweite scheinbare Treffer war mein eigener `grep`-Befehl.

**Die Zuschnittgrenze hat er entschieden** — Punkte 1, 2, 6 → `A-37-25`; 3, 4, 5 → `Z0-I2`, weil das
Tor wissen muss, woran es eine Sitzung erkennt und was es daraus **nicht** schließen darf, während
Heartbeat/Übernahme/Fencing Mechanik der Claim-Sperre sind. Alle sechs stehen trotzdem zusammen im
Blatt, damit keiner zwischen zwei Aufträgen verschwindet. **Das ist die Antwort auf die Frage, die
ich offengelassen hatte, und sie ist begründet statt gesetzt.**

**Restpunkt 2.** `79285cf2` erscheint jetzt als „Sitzung `79285cf2…`" mit voller UUID, Prozess-PID
und Quelle. Die alte Schreibweise steht nur noch **einmal** — in der durchgestrichenen Zitatzeile
`:680`, also genau dort, wo sie hingehört: *sie ist der Beleg, nicht die Aussage.*

**Restpunkt 3.** Standunabhängig gelöst, mit Abschnittsnamen statt Zeilennummer. Nachgemessen über
**drei** Stände: der Abschnitt liegt bei `:484` / `:773` / `:845` und der Ankersatz trifft in jedem
genau einmal. Das ist die A-34-Form, und sie hält.

## Anmerkung 1 — eine Zahl der Gegenprobe stimmt nicht

Die Vorlage sagt: *„Alle sechs standen im geprüften Stand `3dde19ea` bei 0×."* Für fünf trifft das.
**`Sitzungs-ID` stand dort bereits 1×** (`3dde19ea:675`) — und meine eigene Runde-1-Messung hatte
Punkt 1 ausdrücklich als **„getragen"** verzeichnet. Neu gekommen sind **fünf** Punkte, nicht sechs.
*Sachlich ändert das nichts; als Beleg ist die Zeile falsch.*

## Anmerkung 2 — „vollständig" trifft nicht zu

Drei Zusagen sagen dasselbe: `umfang: „… NULL Loeschungen"`, der Commit-Betreff *„Die überholte
Fassung bleibt vollständig stehen, nichts gelöscht"*, und das Blatt selbst *„(Die überholte Fassung
bleibt als Beleg stehen — A-20-4:)"*. **Gemessen hält das nicht:** Die alte Fassung war sieben
Zeilen lang, das durchgestrichene Zitat endet nach „Werkzeugrunde" — Zeile 3 von 7. Verschwunden
sind:

| aus der alten Fassung | in `3dde19ea` | in `213edd28` |
|---|---|---|
| `76231 → 80694 → 80830` | je 1× | **0×** |
| „von vier `pid`-Feldern … trugen drei" | 1× | **0×** |
| „prüft bei drei von vier Rollen eine tote Zahl" | 1× | **0×** |

**Was verloren ging, ist genau die Messung** — und damit der Beleg für den Satz, der zwei Zeilen
weiter steht: *„Der Satz wehrt die Shell-PID ab … und er bleibt richtig."* Diese Aussage steht jetzt
im selben Absatz ohne ihren Nachweis.

*Einschränkung, die dazugehört:* verloren ist nur die Stelle **im Blatt**. Der Stand `3dde19ea` ist
committet und liegt in der Kette (`is-ancestor` gegen den Integrationszweig → exit `0`), die
Messung ist also historisch erhalten. Deshalb ist es eine Anmerkung und kein Restpunkt.

## Warum ERTEILT und nicht NICHT ERTEILT

Die Linie, nach der ich entscheide, und ich schreibe sie hin, damit sie prüfbar ist:
**Ein Restpunkt, der ein Kriterium betrifft, verhindert die Erteilung. Eine Anmerkung, die die
Selbstbeschreibung der Lieferung betrifft, tut es nicht.** Beide Anmerkungen oben betreffen
Belegzeilen *über* die Arbeit, keine Kriterienzeile. Die sechs Kriterien A-37-22…27 sind messbar,
am heutigen Stand rot, mit ausgelösten Negativproben, Gegenproben und Absage-Regeln versehen — der
Generator kann bauen, und der Evaluator kann abnehmen.

**Ball: Generator** (`aa0cddd3`, eigener Worktree) für den Bau nach A-37-22…27.
**Planner** nachrichtlich für die beiden Anmerkungen — beim nächsten Anfassen des Blattes, nicht als
eigene Runde. Danach Z0-I1, Z0-I2 (dorthin gehen Punkte 3–5 dieser Zielregel), Z0-I3.

---

## Berichtigung 00:55 — mein eigener Fehler in Restpunkt 1, und was daran *nicht* stimmt

Der Planner hat um 00:50:53 gemeldet
(`ereignisse/SPEZ-planner-A-37/planner-befund-paragraph8-fehlzuordnung.yaml`), dass mein Satz zu §8
die Folge der falschen Sperre zuordnet. **Ich habe die Quelle selbst geöffnet, statt seinen Befund zu
übernehmen — und er hat recht.**

### Mein Fehler

Ich schrieb im Votum und im Ereignis: *„§8 (`0d897b0e:154-158`) entfernt eine Sperre automatisch,
wenn PID + Startkennung nicht mehr übereinstimmen — wörtlich umgesetzt hätte das Tor der arbeitenden
Planner-Sitzung **die Lease** entzogen."*

**Gemessen:** `PID` kommt in §8 **genau zweimal** vor, `:154` und `:156` — beide unter der
Absatzüberschrift **„Recovery der Sperre selbst"**, also `counter.lock/owner.yaml`, der kurzlebigen
**Vergabesperre**. Die Lease-Übernahme bei `:162` nennt **keine PID**: *„`active/` darf nur entfernt
werden, wenn `heartbeat_bis` verstrichen ist."* *(Muster tauglich: `Fencing` trifft 4× im selben
Abschnitt.)*

**§8 entzieht die Lease nicht über die PID.** Für die Lease trägt der Paragraph Yamas Punkt 4 bereits
richtig. Die zitierten Zeilen waren korrekt benannt; falsch war die Folge, die ich daran hängte.

### Was an seiner Ersatz-Folgerung *nicht* stimmt

Er setzt an die Stelle meines Fehlers eine schärfere These: bei `--resume` sei die Lebendigkeit über
die eingetragene PID *„strukturell nicht messbar"*, deshalb greife `fail closed` (`:157-159`) und
**jede weitere Vergabe** werde dauerhaft abgelehnt — *„Entzug ist laut, Stillstand ist leise."*

**Durchgespielt trifft das nicht.** `fail closed` greift laut Wortlaut nur, *„ist die Lebendigkeit
**nicht eindeutig messbar** (fremder Host, fehlende Startkennung)"*. Bei einer zurückgebliebenen
`counter.lock` eines beendeten Laufs ist beides gegeben — lokaler Host, eingetragene Startkennung —
und `ps` antwortet eindeutig:

```
ps -p 88928 -> exit 1     ps -p 97092 -> exit 1     ps -p 12334 -> exit 1
Verfahren verifiziert: eigene PID -> exit 0 · erfundene -> exit 1
```

Exit `1` heißt **nachweislich nicht mehr existent** — genau die Bedingung aus `:155-156`, unter der
die Sperre entfernt werden **darf**. Die Vergabe läuft weiter; es entsteht kein Stillstand.

**Der Grund dahinter ist der, den er selbst nennt und dann übergeht:** *„die Sperre soll ohnehin nur
innerhalb eines Laufs leben."* `counter.lock` gehört dem **Lauf**, nicht der **Sitzung** — und für
den Lauf ist die Prozessidentität die richtige Kennung. **Was für die Lease falsch ist, ist für die
Vergabesperre richtig.** §8 ist an dieser Stelle intakt: weder mein „Lease-Entzug" noch sein
„Stillstand" tritt ein.

### Was davon unberührt bleibt

**Restpunkt 1 bleibt zu Recht erhoben** — aber aus dem Grund, den der Planner selbst richtig
benennt: *Yamas Zielregel ist eine **Vorgabe**, keine Beschreibung von §8.* Das Blatt trug sie nur
halb; das ist unabhängig von der Fehlzuordnung. Ebenso unberührt: die Vier-Prozess-Messung, der
Realfall 00:17:18 und die Absage-Regel. **Mein ursprünglicher Befund — die Kennung ist bei
fortgesetzten Sitzungen nicht stabil — beruht auf der Prozessmessung, nicht auf §8, und steht.**

### Zum erteilten Votum

Sein Befund datiert 00:50:53, mein Votum `a248eaaf` auf 00:48:58 — **zwei Minuten früher.** Die
Erteilung erging ohne Kenntnis, nicht gegen sie. **Das ERTEILT bleibt:** der falsche Satz steht in
der *Begründung* von A-37-25, nicht in einem Kriterium; der Generator baut nach den sechs Punkten,
der Negativprobe und der Absage-Regel, die alle unberührt sind.

**Auflage, und sie ist ernst gemeint:** Der Ersatztext, den er im Scratchpad vorbereitet hat, darf
die Fehlzuordnung nicht durch eine zweite ersetzen. Nach meiner Messung ist die richtige Aussage
weder „§8 entzieht die Lease über die PID" noch „§8 führt zum Vergabe-Stillstand", sondern: **§8
bindet die Vergabesperre korrekt an den Lauf; Yamas Zielregel ergänzt für die Lease das, was §8 dort
schon trägt.** Ich prüfe den Ersatztext, wenn er als Commit vorliegt.

*Eine Berichtigung, die eine falsche Folge durch eine andere ersetzt, ist keine Berichtigung.*

---

## Prüfung des Ersatztextes `0579727c` — **Auflage erfüllt**, eine neue Anmerkung

| Feld | Wert |
|---|---|
| Prüfgegenstand | `0579727c`, Elter `213edd28` ✔, Scope `54 / 11` in einem Pfad ✔ |
| Vorlage | `planner-berichtigungen-CODE_FERTIG.yaml`, 00:59:37 · Lease `fencing_token: 3` |
| **Ergebnis** | **Auflage erfüllt.** Die Erteilung `a248eaaf` bleibt unberührt. |

### Auflage 1 — keine zweite Fehlzuordnung

| gemessen | `213edd28` | `0579727c` |
|---|---|---|
| „die Lease entzogen" | 1× | **0×** |
| „Stillstand ist leise" / „jede weitere Vergabe" / „dauerhaft abgelehnt" | 0× | **0×** |

Die falsche Folge ist raus, **und die widerlegte Ersatz-These ist nicht an ihre Stelle getreten.**
Stattdessen steht bei `:711-720` eine Tabelle, die beide Sperren mit ihrer je eigenen Bindung trennt
— Lease an `heartbeat_bis` (`:162`), Vergabesperre an die Prozessidentität (`:154-156`) — dazu der
Messbefehl und der Schluss: *„§8 entzieht eine Lease also nicht über die PID."* **Das ist genau die
Aussage, die meine Messung trägt**, und sie ist nicht von mir übernommen, sondern nachgefahren.

### Auflage 2 — das gekürzte Zitat

Alle fünf Belegstellen sind zurück, jede von **0× auf 2×**: `76231`, `80694`, `80830`,
„trugen drei", „eine tote Zahl". Und die Kürzung ist bei `:688-696` **als Berichtigung benannt**
statt stillschweigend geheilt — mit dem Satz, der die Sache trifft: *„Ein gekürzter Beleg ist die
stillste Art, eine Aussage unbelegt zu machen — und die Kürzung stand ausgerechnet unter der Zusage,
nichts zu löschen."*

Die Kriterien sind unberührt: 27, lückenlos, und der Diff hat **einen einzigen** Block
(`@@ -678,19 +678,62 @@`), der außerhalb jeder Kriterienzeile liegt.

### Neue Anmerkung — eine Zahl liegt wieder um eins daneben

`0579727c:729` sagt: *„an vier beendeten Läufen"*. Das zugehörige Ereignis zählt sie auf:
`(88928, 97092, 12334, 21343)`. **`16345` fehlt** — der Lauf, den sein eigenes Blatt zwei Abschnitte
weiter bei `:764` als „Lauf 5" führt und den ich um 00:44 selbst als lebend gemessen hatte
(Start `Sat Aug 22 00:40:08 2026`). Zum Zeitpunkt seiner Messung (Lauf `25914`, Start 00:56:50)
waren **fünf** Läufe beendet, nicht vier.

**Das ist die zweite Zahl dieser Art.** Anmerkung 1 des ERTEILT-Votums war *„alle sechs standen bei
0×"*, richtig waren fünf. Jetzt *„vier beendete Läufe"*, richtig sind fünf. **Beide Male liegt die
Zahl um eins daneben, und beide Male, weil ein Element übersprungen wurde, das an anderer Stelle
desselben Dokuments steht.** Die Sache trägt jeweils trotzdem — die Regel bleibt richtig, ob vier
oder fünf Läufe beendet sind. Als Beleg ist die Zeile falsch.

*Nebenbefund ohne Vorwurf, weil er dieselbe Klasse zeigt:* `:764` führt `16345` weiterhin als
„ps-exit 0 lebt". Inzwischen sind **alle sechs** Prozesse der Sitzung tot (`88928, 97092, 12334,
16345, 21343, 25914` je exit 1, Verfahren an beiden Enden verifiziert). Das ist kein Fehler, sondern
genau die Klasse, die das Blatt selbst benennt: *„Die Registrierung war nie falsch — sie ist
abgelaufen."* Eine Momentaufnahme, die als solche stehen bleiben darf.

### In eigener Sache

Er meldet in derselben Vorlage einen eigenen Messausfall: sein Commit-Aufruf lief mit `| tail -4`,
der Rückgabewert kam leer — **der Exit-Code war hinter der Pipe verschwunden**. Als Ausfall erkannt,
nicht als Ergebnis gewertet, direkt nachgemessen. Das ist dieselbe Falle, an der ich in Runde 1 meine
eigene Lebendprobe verworfen habe. *Dass beide Rollen sie in derselben Nacht unabhängig treten und
beide sie selbst bemerken, sagt mehr über die Falle als über die Rollen.*

**Ball: Generator.** Die Kriterien sind seit `a248eaaf` erteilt und durch beide Berichtigungen
unberührt geblieben.

---

## Beide Anmerkungen geschlossen — `762243b9`

| Prüfung | Ergebnis |
|---|---|
| SHA / Elter / Scope | existent, Elter `0579727c` ✔, **30 / 7** in einem Pfad ✔ |
| Kriterien | 27, lückenlos, zwei Diff-Blöcke, beide außerhalb jeder Kriterienzeile |
| „an vier beendeten Läufen" | **0×** |
| die fünf wiederhergestellten Belegstellen | unverändert: `76231` 2×, `80694` 2×, `80830` 2×, „trugen drei" 1×, „eine tote Zahl" 2× |

**Anmerkung 1 (die Zählfehler)** ist nicht durch eine korrigierte Zahl geschlossen, sondern durch
eine strukturelle Abhilfe: *„Die Zahl steht nicht mehr im Satz, sondern nur noch in der Tabelle, die
sie ohnehin führt. Eine Zahl, die an zwei Stellen steht, driftet an einer davon."* Und der Satz, der
den Unterschied macht: *„Sich vorzunehmen, künftig besser zu zählen, wäre keine Abhilfe, sondern ein
Vorsatz."* **Das ist die richtige Antwort auf einen zweimal wiederholten Zählfehler** — die Stelle
beseitigen, an der gezählt wird, statt sich Sorgfalt vorzunehmen.

**Anmerkung 2 (die Momentaufnahme)** ist besser gelöst als von mir vorgeschlagen. Ich hatte gesagt,
sie dürfe stehen bleiben. Er hat sie stehen lassen **und datiert**: Die Tabelle bei `:777-783` führt
jetzt alle acht Läufe, trägt ihren Erhebungsstand in sich und vermerkt bei `16345` ausdrücklich
*„(um 00:44 noch lebend)"*. **Nachgemessen um 01:13 ist auch `30651` beendet** — alle sieben PIDs
`exit 1`, Verfahren an beiden Enden verifiziert. Die Zeile sagt „LEBT" und ist trotzdem richtig,
weil sie ihren Zeitpunkt nennt. *Damit ist die Tabelle der Beleg ihres eigenen Kriteriums: wer sie
als Dauerauskunft liest, hält einen beendeten Prozess für lebend; wer sie als Momentaufnahme liest,
liest sie richtig — und genau diesen Unterschied muss das Tor treffen.*

**Mein eigener Messfehler in dieser Runde:** Ich habe diese Lieferung zunächst übersehen. Mein
`find -newermt` stand auf `01:10`, die Datei entstand um `01:08`, meine vorige Steuerungsprüfung lief
um `01:07` — **das Fenster hing am Berichtszeitpunkt statt lückenlos an der letzten Prüfung.**
Gefunden erst, als eine zweite Messung (Alterung je Rolle) sie unabhängig zeigte. Dazu ein zweiter:
Ich hatte „letzte Handlung je Rolle" nach Uhrzeit-**Strings** sortiert, wo `23:51` über `01:05`
steht; in Epoch neu gemessen kippte das Bild — der Takt läuft, nur der Dirigent steht.

**Das ERTEILT-Votum ist damit vollständig abgearbeitet.** Ball: **Generator**.

---

# DoR Nachschärfung — `99ea9183` · `fdc8d7d5` · `96b24ca3` — **ERTEILT**

| Feld | Wert |
|---|---|
| Auftrag | `DOR-plan-pruefer-A-37-nachschaerfung`, generation 6, Lease `fencing_token: 3` |
| Prüfgegenstand | drei Planner-SHAs, Kette `99ea9183` → `fdc8d7d5` → `96b24ca3` |
| **Votum** | **ERTEILT** — eine Anmerkung, kein Restpunkt |

## Meldepflichten, alle drei Stände

| SHA | Elter | Scope | Z0-I1 im Commit |
|---|---|---|---|
| `99ea9183` | `762243b9` ✔ | 89 / 0, ein Pfad | 0 |
| `fdc8d7d5` | `99ea9183` ✔ | 90 / 1, ein Pfad | 0 |
| `96b24ca3` | `fdc8d7d5` ✔ | 37 / 9, ein Pfad | 0 |

Hauptkriterien durchgehend **27, lückenlos 1…27**; Unterkriterien **22b/c/d/e = 4**. Die 198 geparkten
Z0-I1-Zeilen sind in keinem der drei Commits — das Parken hält.

## Die vier Kriterien, selbst nachgemessen

**A-37-22b.** Die Rot-Lage habe ich gegen **alle drei** Codestände geprüft, nicht nur gegen den, den
er vorfand: `TICKET_ROLLE` / `integrator` / `berechtigt` / `erlaubt` je **0** in `762243b9`,
`49972884` und `1155709d`. Sein neuer **Wirkungs-Messbefehl** trifft ebenfalls 0/0/0, mit je eigener
Ausgabedatei gemessen; `cmp` bestätigt, dass `rueckweg.py` in `1155709d` unverändert ist (`git log`
über den Pfad: 0 Commits). Gegenprobe, dass der Griff greifen *kann*: `TICKET_ROLLE` trifft in
**vier** anderen Dateien unter `scripts/` — die Datei ist leer, nicht der grep blind.

*Damit ist der Befund von 08:27 geschlossen:* Der alte Beleg zählte ein **Wort** und hätte nach dem
Vorab-Bau 2 Treffer gefunden, beide reiner Text. Der neue misst die **Wirkung**.

**A-37-22c.** Zwei Bäume mit Präfix `ticket-rolle-generator` (gezählt: 2), `rueckweg.py:128` wörtlich
`pfad = f'{WURZEL}/{name}'`, Ähnlichkeitsprüfung 0. Und die Entscheidung, die das Kriterium prägt,
steht drin: der Belegbaum ist **Absicht**, verlangt ist ihn zu *erkennen und zu melden*, nicht ihn zu
beseitigen — mit der Absage-Regel gegen stillen Ausschluss.

**A-37-22d.** Der Widerspruch zu 22b ist in **vier Stücke** aufgelöst (`:622-625`):
`preflight_authorisierung()` ohne jede Änderung im echten Checkout · Transportkern mit temporärem
Root nur im Wegwerf-Repo · Produktiv-Einstieg erst nach echtem Preflight · **Probe-Modus lehnt reale
Rollen-Worktrees aktiv ab**. Die Absage-Regel `:631-633` trifft den Kern: ein Transportkern ohne
Root-Parameter kann im Probe-Modus doch auf echte Bäume zeigen. **Widerspruchsfrei.**

**A-37-22e.** Die Rot-Lage hatte ich um 08:29 unabhängig vorgemessen: `generation` 0, `digest`/`sha256`
0, Aktionsstatus 0, `ticket-steuerung`/`rollen/` 0 — in `commit-pruefen.sh` **und** in `.githooks/`,
wo es zudem **keinen `pre-commit`** gibt. Seine Zahlen decken sich. Und er benennt die
**Teilstring-Falle** im Blatt selbst (`grep -ci 'ack'` → 5, alle in `package`/`getrackt`).

## Anmerkung — die dritte Zahl derselben Bauart

`:703` sagt: `grep -n 'mkdtempSync' scripts/__tests__/*.mjs` → **15 Treffer** in drei Dateien.
Sein Befehl wörtlich gefahren gibt **16** (2 + 12 + 2). Die Dateizahl stimmt, die Trefferzahl ist um
eins zu niedrig.

**Und ich präzisiere meine eigene frühere Anmerkung:** Ich hatte „18 in 4 Dateien" gemeldet — das ist
eine **andere Grundmenge** (`scripts/` rekursiv, plus `scripts/zeile-ersetzen.mjs` mit 2). 16 + 2 = 18.
Beide Zahlen sind für ihren jeweiligen Befehl richtig; falsch ist nur 15 gegen 16.

*Damit ist es die dritte Zahl dieser Art in dieser Kette* — „alle sechs" statt fünf, „vier beendete
Läufe" statt fünf, jetzt „15" statt 16. **Immer um eins, immer beim Zählen aus dem Gedächtnis neben
einer Liste, die danebensteht.** Seine strukturelle Abhilfe von vorhin — die Zahl nur noch dort
führen, wo sie ohnehin steht — greift hier noch nicht.

## Warum ERTEILT

Nach der Linie, die ich in Runde 2 hingeschrieben habe: **Ein Restpunkt betrifft ein Kriterium, eine
Anmerkung die Selbstbeschreibung.** Die Zahl 15/16 steht in einer *Berichtigung über die Messung*,
nicht in einer Kriterienzeile. Alle vier Kriterien sind messbar, gegen den heutigen Stand rot, mit
gefahrenen Messbefehlen, auslösbaren Negativproben und Absage-Regeln versehen; 22b und 22d sind
widerspruchsfrei; die Kriterien 1…27 sind unverändert; beide Vorab-Commits (`49972884`, `1155709d`)
sind mit SHA, Zeit und Lage gekennzeichnet und ausdrücklich als **nicht abnahmefähig** benannt.

**Zu würdigen ist ein eigener Fund von ihm**, den niemand verlangt hatte: Beim Nachmessen lieferten
ihm zwei Schleifen falsche Zahlen, weil sie für mehrere Stände *dieselbe* Zwischendatei beschrieben.
Aufgefallen ist es an einem Widerspruch in der Ausgabe selbst — eine Zeile 177 in einer angeblich
105 Zeilen langen Datei. *„Das war knapp: ich stand kurz davor, die Messung des Plan-Prüfers für
falsch zu erklären. Richtig war seine, falsch meine."* Die Lehre steht jetzt als Warnung im Blatt,
nicht als Vorsatz im Bericht — **je Stand eine eigene Datei, `cmp`-Gegenprobe, und: zwei Verfahren,
zwei Antworten, dann gilt keine von beiden.**

**Ball: Generator** (`aa0cddd3`) für den ergänzenden Bau nach 22b/c/d/e und den Rest 24…27.
