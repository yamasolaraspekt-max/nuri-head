# A-38 — Merges laufen am Tor vorbei, und keiner trägt eine Rollenmarke

> **⚠ TITEL BERICHTIGT am 16.08. nach DoR Runde 3.** Er lautete *„41 von 309 Commits laufen am Tor
> vorbei"* — **beide Zahlen sind falsch gemessen** (am Planner-Baum statt am gemeinsamen Graphen)
> und stehen seit dem 15.08. berichtigt im Blatt: **59 von 497, 58 von 70 Merges ohne Marke.**
> **Der Titel trug die widerlegte Zahl weiter.** *Präzedenzfall A-33: dort hieß ein Blatt „zehn
> Tafelzeilen", gemessen waren es elf — es wurde stillgelegt und durch ein Blatt mit richtigem
> Namen ersetzt.* **Hier genügt die Berichtigung, weil der Dateiname keine Zahl trägt.** Der alte
> Titel steht in dieser Zeile als Beleg (A-20-4).

```yaml
auftrag: "A-38"
werkzeug: "— (Werkzeug der Rollenkette, kein Hausplaner-Werkzeug)"
art: "BAU — ein versionierter commit-msg-Hook plus core.hooksPath.
      KEINE Aenderung an docs/STATUS.md, KEIN Hausplaner-Code, KEINE Migration."
spur: A
heimat_app: ticket
dor_beleg: "NICHT ERTEILT — 3. Runde, siehe docs/STATUS.md. Restpunkte 16.08. behoben."
dor_schnitt_sha: "b6af3207"
dor_schnitt_regel: |
  NEU am 16.08., auf Vorschlag des Plan-Pruefers und weil es dreimal an einem Tag passiert ist:
  Eine DoR-Runde prueft den Stand DIESES SHA, nicht den Stand beim Lesen.
  Waechst das Blatt waehrend der Pruefung, gilt der Befund trotzdem — er bezieht sich auf
  einen benannten Stand, und der naechste Schnitt-SHA eroeffnet die naechste Runde.
  ANLASS, gemessen von ihm: A-37 wuchs zwischen BEREIT-Erteilung und Nachpruefung von
  11 Kriterien und 234 Zeilen auf 15 und 342 — vier Kriterien und 108 Zeilen in dreizehn
  Minuten. Kein Vorwurf an die Nachtraege, zwei gehen auf Yamas Gegenprobe zurueck.
  Der Befund gilt dem ZUSTAND: BEREIT heisst, der Generator darf ziehen, und er wuerde
  gegen gepruefte UND ungepruefte Kriterien bauen.
  Das ist dieselbe Klasse wie die abgelaufene Zahl in A-33 — eine Aussage ohne Standbezug
  laeuft ab, ohne dass der Schreibende es erfaehrt.
status_steht_in: docs/STATUS.md
basis_sha: 0f05f8bf
prioritaet: P1
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 14.08. 23:00 — Claim VOR dem Schnitt."
kennung_geprueft: "A-38 hat NULL Treffer in docs/STATUS.md und NULL Blaetter in
                   docs/auftraege/aktiv/. A-01 bis A-37 sind vergeben. Frei."
anlass: "Beim Pruefen, ob ein pre-commit-Hook noetig ist, gemessen statt angenommen:
         Commits ohne Rollenmarke sind AUSNAHMSLOS Merges. BERICHTIGT 16.08.: die
         urspruenglichen Zahlen 41 von 309 waren am Planner-Baum gemessen, dessen Graph
         kleiner war; im gemeinsamen Graphen sind es 59 von 497, und 58 von 70 Merges
         tragen keine Marke. Der Kern ist damit schaerfer, nicht schwaecher."
gebaut_in: "ticket-rolle-generator (rolle/generator) — BERICHTIGT ZURUECK am 15.08. 15:50.
            Der Grund fuer die Verlegung in den Integrations-Checkout ist ENTFALLEN: der
            Generator-Baum hat seit 15:36:54 node_modules samt typescript, gemessen. Der
            Plan-Pruefer hat A-38-9 dort gefahren: tsc exit 0, Suite 1763/1763.
            KEIN Blattfehler und kein Messfehler auf einer der beiden Seiten — die Zeitstempel
            liegen so: Blatt 15:30:37, release/node_modules 15:30:51, generator 15:36:54.
            Mein Befund hielt VIERZEHN SEKUNDEN. Die Umgebung ist unter dem Satz weggewandert.
            OFFEN UND YAMA VORGELEGT, siehe Nicht-Ziele: die zwei node_modules sind ECHTE
            Verzeichnisse mit je 323 MB, keine Symlinks — und Yamas Nicht-Ziel schliesst
            genau das aus."
hinweis_basis: "rolle/generator steht auf bc2125d9, der Basis-SHA 0f05f8bf ist DREI Commits neuer.
                Das Objekt ist im gemeinsamen Lager erreichbar, der BRANCH aber nicht dort.
                Der Generator zieht vor dem Bau nach — sonst baut er am falschen Stand.
                Dasselbe gilt fuer A-37 (Basis bc2125d9, dort passend)."
staut_hinter: "A-37 — beide sind ungeprueft. Der Plan-Pruefer entscheidet die Reihenfolge."
```

## Der Befund, gemessen

```
GEMESSEN IM GEMEINSAMEN GRAPHEN (berichtigt 15.08., siehe unten):
Commits letzte 48 h                                497
davon ohne Rollenmarke                              59
davon Merges                                        59    = ALLE
Merges gesamt                                       70
davon MIT Rollenmarke                               12    -> 58 ohne  = 83 %
'merge' in scripts/commit-pruefen.sh  (-i)           4 Treffer, keine Pruefung
.githooks / core.hooksPath                          nicht vorhanden / nicht gesetzt
```

**Der Messbefehl, damit die drei Zahlen nachrechenbar sind statt geglaubt** *(DoR Runde 3: „A-38s
drei Zahlen ohne Messbefehl")* — **im Integrations-Checkout zu fahren, nicht im Rollenbaum:**

```bash
cd /Users/yamanuri/Documents/ticket
G=$(git --no-optional-locks log --since='48 hours ago' --oneline | wc -l)
M=$(git --no-optional-locks log --since='48 hours ago' --merges --oneline | wc -l)
O=$(git --no-optional-locks log --since='48 hours ago' --format='%s' \
      | grep -cvE '^(planner|plan-pruefer|generator|evaluator|release-pruefer|integrator)(-[0-9]+)?:')
MM=$(git --no-optional-locks log --since='48 hours ago' --merges --format='%s' \
      | grep -cE '^(planner|plan-pruefer|generator|evaluator|release-pruefer|integrator)(-[0-9]+)?:')
echo "Commits $G · Merges $M · ohne Marke $O · Merges MIT Marke $MM"
```

**Die Zahlen sind flüchtig** *(Regel vom 16.08.)*: `--since='48 hours ago'` misst ein wanderndes
Fenster, und der Graph wächst rückwirkend beim Zusammenführen. **Der Bau prüft die Aussage, nicht
die Zahl:** *Merges ohne Rollenmarke existieren und werden vom Tor nicht gesehen.*

**⚠ Meine erste Zählung war am falschen Ort gemessen — 309/32/41 statt 497/70/59.** Der Prüfer
hat die Ursache benannt: **ich habe im Planner-Baum gezählt, dessen Graph kleiner war**, und die
Zahl **wächst rückwirkend**, sobald Zweige zusammengeführt werden. **Der Kern trägt unverändert
und ist sogar schärfer: 58 von 70 Merges ohne Rollenmarke, 83 Prozent** statt der von mir
genannten 28 von 32. *(Derselbe Fehlertyp wie am 14.08., als ich 701 statt 39 ungesicherte
Commits meldete: am falschen Gegenstand gemessen. Diesmal am falschen **Ort**.)*

**Ein Merge ist die Handlung, die künftig dem Integrator allein gehört — und sie ist heute die
einzige, die das Tor gar nicht sieht.** `git merge` erzeugt einen Commit, ohne dass
`commit-pruefen.sh` je aufgerufen wird. Keine Rollenmarke, kein Beifang-Blick, keine Barriere.

**Und der Regelfall ist der leere Body:**

```
56d61ddd  Merge commit 'b9a030e3' into HEAD      Body: (leer)
b9977f86  Merge commit 'e370490e' into HEAD      Body: (leer)
```

**Wer diese Zeile in einem halben Jahr liest, weiß nicht, welche Arbeit hier zusammenkam, wer sie
zusammengeführt hat und ob dabei etwas verloren ging.**

## Was es NICHT ist — vorhandenes geprüft

| vorhanden | deckt es ab? |
|---|---|
| `docs/merge-anleitung.md` *(168 Z., 30.07.)* | **nein** — sie regelt den Merge **nach `main`** (Tor 2, gehört Yama), nicht die Arbeits-Merges zwischen Bäumen |
| `docs/integration-merge-plan.md` | **nein** — Plan, kein Wächter |
| Das gehärtete Konflikt-Werkzeug des Release-Prüfers *(`907e1530`)* | **nein, und es ist die Nachbarschaft:** es löst **Konflikte in `docs/STATUS.md`** sicher auf („Regel 1: ABBRUCH statt Löschen"). A-38 fragt nicht, *wie* zusammengeführt wird, sondern *ob es protokolliert ist* |

**Der Bau prüft das Konflikt-Werkzeug vor dem ersten Zeichen Code und dockt an, statt zu doppeln.**

## Scope

### 1 · `.githooks/commit-msg` — versioniert, für alle Bäume

```
Auslöser: JEDER Commit, auch Merges — commit-msg laeuft immer.
Arbeit  : Ist es ein Merge (MERGE_HEAD existiert oder zwei Eltern)?
            ja  -> Botschaft muss tragen:  <rolle>: <text>
                   UND eine Herkunftszeile: 'zusammengefuehrt: <sha> <- <sha>'
            nein -> unveraendert durchlassen (commit-pruefen.sh bleibt zustaendig)
Rueckgabe: 1 bei fehlender Marke oder fehlender Herkunft.
```

### 2 · `core.hooksPath=.githooks`

**Der Grund, warum es versioniert sein muss:** `.git/hooks/` gilt **je Arbeitsbaum**. Bei sechs
Worktrees müsste man ihn sechsmal von Hand einrichten — und die siebte Rolle hätte ihn nicht.
**`core.hooksPath` ist Repo-weit und wirkt in allen Worktrees zugleich.**

### 3 · Die Einrichtung ist Teil des Auftrags, nicht Handarbeit

Ein Hook, den jeder selbst setzen muss, ist bei sechs Bäumen kein Schutz. **Der Bau liefert einen
Einzeiler, der `core.hooksPath` setzt, und A-38-6 belegt, dass er in einem zweiten Baum greift.**

## Nicht-Ziele

- **KEINE Änderung an `docs/STATUS.md`.**
- **KEIN Hausplaner-Code**, weder `resources/` noch `app/`.
- **Keine Konfliktauflösung** — die gehört dem Werkzeug des Release-Prüfers.
- **Kein Eingriff in `commit-pruefen.sh`** — A-37 arbeitet dort. **Zwei Aufträge an derselben Datei
  wären genau die Kollision, gegen die diese Umstellung läuft.**
- **Kein Blockieren von `git merge` selbst**, nur der Botschaft.

## Kanten

| # | Fall | Verlangtes Verhalten |
|---|---|---|
| K1 | **`git merge --no-commit`** | greift nicht — der Hook läuft erst beim Commit. Kein Sonderfall, aber im Bericht benennen |
| K2 | **Merge ohne Konflikt, Fast-Forward** | **kein** Commit entsteht → nichts zu prüfen |
| K3 | **`git commit --amend` an einem Merge** | wie ein Merge behandeln, die Eltern bleiben zwei |
| K4 | **Rebase / Cherry-Pick** | **nicht** betroffen — ein Cherry-Pick hat einen Elter |
| K5 | **Der Hook selbst ist kaputt** | muss mit **eigener** Ursache melden, nicht als Formfehler *(dieselbe Lehre wie A-37 Teil 3)* |
| K6 | **`--no-verify`** | umgeht jeden Hook. **Nicht verhinderbar** — ausdrücklich in den Bericht, nicht verschweigen |

## Abnahmekriterien

- **A-38-1** · `.githooks/commit-msg` existiert und ist ausführbar.
  **Rot am Basis-SHA:** `ls .githooks` → Verzeichnis existiert nicht.
- **A-38-2** · **Negativfall:** Ein Merge-Commit mit Botschaft `Merge branch 'x' into HEAD`
  wird **abgewiesen**. Rohausgabe.
  **Rot — auf FESTE SHAs umgestellt, die Quote ist als Beleg abgesetzt:**
  ```
  94d2b479 · 0f05f8bf · c1b3a774 · b1d343e6 · 9b42e777
  ```
  **Fünf Merges ohne Rollenmarke, 14.08. zwischen 22:14 und 22:53 — `0f05f8bf` ist der
  `basis_sha` dieses Blattes selbst.** Prüfbar mit
  `git --no-optional-locks log -1 --format='%s' <sha>` → keine Rollenmarke. **Unveränderlich,
  also trägt der Beleg einen SHA und keine Zahl.**

  **⚠ DIE ALTE ROT-LAGE HATTE EINE UHR — behoben, bevor sie abgelaufen ist.** Sie zitierte
  *„28 von 32"* aus einem `--since='48 hours ago'`-Fenster. **Der Plan-Prüfer (`4eac6684`) hat
  gemessen: der jüngste markenlose Merge fällt am 16.08. um 22:53 aus dem Fenster; ab dann misst
  wer A-38 prüft `0 von 102` und findet keinen Beleg für das Problem, das der Auftrag löst.**
  Gefallen war nicht nur die Quote (88 % → 83 % → 25 % → 4 %), sondern die **absolute Zahl von
  58 auf 5**.
  **Eine Rot-Lage, die von selbst grün wird, ohne dass jemand etwas behoben hat, ist keine
  Rot-Lage.** *(Zwei Haltbarkeiten, 16.08.: unveränderlich trägt SHA, flüchtig trägt Zeitstempel
  — eine flüchtige Messung taugt als Anlass, nie als Rot-Lage.)*

  **Und was NICHT folgt, weil die Zahl zu dieser Lesart einlädt:** A-38 ist nicht überflüssig
  geworden. **Seit 15.08. gibt es 97 Merges und davon 0 ohne Marke — das ist Disziplin, kein
  Mechanismus.** Sie hängt daran, dass der Release-Prüfer markiert. **Abgelaufen ist der Beleg,
  nicht der Zweck.** Genau deshalb prüft **A-38-2 den konstruierten Fall** und nicht die
  Vorgeschichte: er misst die **Wirkung** des Hooks und ist morgen so gültig wie heute.
- **A-38-3** · **Positivfall:** Ein Merge mit `integrator: …` **und** Herkunftszeile kommt durch,
  exit 0. **Ohne diesen Beleg ist der Hook von einem kaputten nicht zu unterscheiden.**
- **A-38-4** · **Ein normaler Commit ist unberührt** — `commit-pruefen.sh` bleibt allein zuständig,
  der Hook greift nicht ein. Beleg: ein Rollen-Commit läuft wie bisher.
- **A-38-5** · **`core.hooksPath` ist gesetzt** und der Befehl dafür steht im Bericht.
  **Rot:** `git config core.hooksPath` → leer.
- **A-38-6** · **Der Hook greift in einem ZWEITEN Worktree**, ohne dort eingerichtet zu werden.
  **Messbar:** derselbe Negativfall in einem **Wegwerf-Worktree, den der Generator selbst
  anlegt und danach entfernt** → abgewiesen. *(Das ist der eigentliche Zweck der Versionierung
  und der einzige Beleg, der ihn trägt.)*
  **⚠ BERICHTIGT 17:5x durch Selbstprüfung gegen P7.** Vorher stand hier
  `ticket-rolle-evaluator` — **ein FREMDER Rollenbaum.** Die drei P7-Fragen: **WER** — der
  Generator · **DARF er** — **nein**: ein Testcommit in fremdem Rollenbaum verletzt die
  Baumtrennung, und ein Hook feuert nicht bei `--dry-run`, es gäbe also keinen lesenden Weg ·
  **EXISTIERT die Eigenschaft** — ja, `core.hooksPath` gilt repo-weit.
  **Dieselbe Klasse wie A-37-18**, wo das Kriterium Transport verlangte, der dem Adressaten
  untersagt ist. **Die Aussage bleibt unverändert** — belegt werden muss, dass der Hook in
  einem Worktree greift, **in dem ihn niemand eingerichtet hat.** *Ein Wegwerf-Worktree
  erfüllt genau diese Bedingung und gehört dem, der ihn anlegt.*
- **A-38-7** · **Alle sechs Kanten behandelt**, K6 (`--no-verify`) **ausdrücklich als nicht
  verhinderbar benannt** — ein Schutz, dessen Grenze verschwiegen wird, erzeugt falsches Vertrauen.
- **A-38-8** · **Kein Nicht-Ziel berührt.** `git show --stat` nennt weder `resources/`, `app/`,
  `docs/STATUS.md` noch `scripts/commit-pruefen.sh`.
- **A-38-9** · **Suite grün und Zahl unverändert GEGEN DEN BAU-STAND**, `tsc exit=0`.
  **Messbar:** Zahl unmittelbar vor dem Bau erheben, nach dem Bau muss sie gleich sein.
  **Keine feste Zahl.** *(Berichtigt 15.08., wortgleich zu A-37-11.)*

## Die Lücke ist heute auf 40 % gewachsen — gemessen 19:2x

```
Commits am 16.08. gesamt        472
davon MERGES                    188   =  40 %   laufen am Tor vorbei
Nicht-Merges                    284
davon mit Rollenmarke           284   = 100 %   das Tor greift lueckenlos
```

> **Das Rollen-Tor erreicht 60 % der Commits. Bei denen wirkt es LÜCKENLOS — 284 von 284.
> Die anderen 40 % sieht es nie.**

**⚠ Die drei scheinbaren Ausreißer sind keine.** Eine erste Zählung ergab 281 und meldete drei
markenlose Commits. **Geöffnet statt gezählt:** `c425638d` und `a4694b21` tragen
`release-pruefer (in Yamas Namen): `, `4ed51b8f` trägt
`plan-pruefer (release-pruefer in Rollenwechsel): `. **Alle drei haben eine Rollenmarke — mit
einem Klammerzusatz dazwischen, den mein Zählmuster nicht erfasste.** *H-9 an der eigenen
Messung: die Schreibweise gezählt, nicht die Sache.*
**Das macht den Befund stärker:** die gesamte Lücke sind die Merges, und **nur** die.

**Der Anteil war beim Schnitt dieses Blattes deutlich kleiner.** Er ist heute gewachsen, weil der
Integrationslauf **188 Merges** erzeugt hat — **je besser der Rückfluss funktioniert, desto größer
wird die Lücke, die A-38 schließt.** *Das ist kein Nebeneffekt, das ist die Regel: ein Tor, das
Merges nicht sieht, wird mit jeder Integration blinder.*

**Und es entwertet die alte Rot-Lage nicht, es verschiebt sie:** heute früh lautete die Frage,
wie viele Merges eine Marke tragen. **Jetzt lautet sie, dass 188 Vorgänge das Tor gar nicht
passieren — unabhängig davon, was in ihrer Botschaft steht.**

## Rückweg und Entdeckung

- **Rückweg:** `git config --unset core.hooksPath` — eine Zeile, sofort wirksam, ohne Commit.
  **Das ist der billigste Rückweg, den ein Schutz haben kann.**
- **Entdeckung:** Meldet er zu viel, kann niemand mehr mergen und es fällt binnen Minuten auf.
  Meldet er zu wenig, fängt A-38-2 es vorher.
- **Der Fall, der beim Bauen übersehen wird:** K2. **Ein Fast-Forward erzeugt gar keinen Commit** —
  wer nur `git merge` beobachtet, hält den Hook für defekt, obwohl es nichts zu prüfen gab.

## Was dieser Auftrag nicht beantwortet

**Ob die Merges inhaltlich richtig waren.** Er erzwingt, dass Herkunft und Rolle **dastehen** —
nicht, dass sie **stimmen**. Die inhaltliche Prüfung ist das Integrationsprotokoll (`P2H-11`), und
das ist ein Erzeugnis des Integrators, kein Hook.

## Beilage: Tafelzeile und Datensatz (A-20)

**Wie bei A-37 nicht erfüllbar**, solange `docs/STATUS.md` im gemeinsamen Checkout liegt und ich im
Planner-Worktree arbeite. Beides liegt wortgleich bei:

```text
Tafelzeile:
| **A-38** | Merges am Tor vorbei: versionierter commit-msg-Hook | ENTWURF | plan-pruefer |
```

```text
Datensatz:
auftrag: "A-38"
zustand: ENTWURF
basis_sha: 0f05f8bf
ballbesitz: plan-pruefer
dor_beleg: "steht aus"
blatt: docs/auftraege/aktiv/A-38-merges-laufen-am-tor-vorbei.md
```

**Die Abweichung von A-20 wird gemeldet, nicht stillschweigend gemacht**, und endet mit `P2H-06`.
