# A-38 — 41 von 309 Commits laufen am Tor vorbei, und alle sind Merges

```yaml
auftrag: "A-38"
werkzeug: "— (Werkzeug der Rollenkette, kein Hausplaner-Werkzeug)"
art: "BAU — ein versionierter commit-msg-Hook plus core.hooksPath.
      KEINE Aenderung an docs/STATUS.md, KEIN Hausplaner-Code, KEINE Migration."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: 0f05f8bf
prioritaet: P1
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 14.08. 23:00 — Claim VOR dem Schnitt."
kennung_geprueft: "A-38 hat NULL Treffer in docs/STATUS.md und NULL Blaetter in
                   docs/auftraege/aktiv/. A-01 bis A-37 sind vergeben. Frei."
anlass: "Beim Pruefen, ob ein pre-commit-Hook noetig ist, gemessen statt angenommen:
         41 von 309 Commits der letzten 48 h tragen keine Rollenmarke — ausnahmslos Merges."
gebaut_in: "ticket-rolle-generator (rolle/generator)"
hinweis_basis: "rolle/generator steht auf bc2125d9, der Basis-SHA 0f05f8bf ist DREI Commits neuer.
                Das Objekt ist im gemeinsamen Lager erreichbar, der BRANCH aber nicht dort.
                Der Generator zieht vor dem Bau nach — sonst baut er am falschen Stand.
                Dasselbe gilt fuer A-37 (Basis bc2125d9, dort passend)."
staut_hinter: "A-37 — beide sind ungeprueft. Der Plan-Pruefer entscheidet die Reihenfolge."
```

## Der Befund, gemessen

```
Commits letzte 48 h                                309
davon ohne Rollenmarke                              41    = 13 %
davon Merges                                        41    = ALLE
Merges gesamt                                       32
davon MIT Rollenmarke                                4    -> 28 ohne
'merge' in scripts/commit-pruefen.sh                 4 Treffer, keine Pruefung
.githooks / core.hooksPath                          nicht vorhanden / nicht gesetzt
```

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
  **Rot:** heute geht er durch — 28 von 32 Merges belegen es.
- **A-38-3** · **Positivfall:** Ein Merge mit `integrator: …` **und** Herkunftszeile kommt durch,
  exit 0. **Ohne diesen Beleg ist der Hook von einem kaputten nicht zu unterscheiden.**
- **A-38-4** · **Ein normaler Commit ist unberührt** — `commit-pruefen.sh` bleibt allein zuständig,
  der Hook greift nicht ein. Beleg: ein Rollen-Commit läuft wie bisher.
- **A-38-5** · **`core.hooksPath` ist gesetzt** und der Befehl dafür steht im Bericht.
  **Rot:** `git config core.hooksPath` → leer.
- **A-38-6** · **Der Hook greift in einem ZWEITEN Worktree**, ohne dort eingerichtet zu werden.
  **Messbar:** derselbe Negativfall aus `ticket-rolle-evaluator` → abgewiesen. *(Das ist der
  eigentliche Zweck der Versionierung und der einzige Beleg, der ihn trägt.)*
- **A-38-7** · **Alle sechs Kanten behandelt**, K6 (`--no-verify`) **ausdrücklich als nicht
  verhinderbar benannt** — ein Schutz, dessen Grenze verschwiegen wird, erzeugt falsches Vertrauen.
- **A-38-8** · **Kein Nicht-Ziel berührt.** `git show --stat` nennt weder `resources/`, `app/`,
  `docs/STATUS.md` noch `scripts/commit-pruefen.sh`.
- **A-38-9** · **Suite grün und Zahl unverändert** (Stand `0f05f8bf`: 1750), `tsc exit=0`.

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
