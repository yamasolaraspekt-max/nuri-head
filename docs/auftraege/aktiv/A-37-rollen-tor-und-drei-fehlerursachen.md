# A-37 — Das Tor muss wissen, in welchem Baum es steht: Rollenbindung, STATUS-Sperre und drei unterscheidbare Fehlerursachen

```yaml
auftrag: "A-37"
werkzeug: "— (Werkzeug der Rollenkette, kein Hausplaner-Werkzeug)"
art: "BAU — Erweiterung von scripts/commit-pruefen.sh plus ein neues Pruefskript.
      KEINE Aenderung an docs/STATUS.md, KEIN Hausplaner-Code, KEINE Migration."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: bc2125d9
prioritaet: P0
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 14.08. 22:35 — Claim VOR dem Schnitt."
kennung_geprueft: "A-37 hat NULL Treffer in docs/STATUS.md und NULL Blaetter in
                   docs/auftraege/aktiv/. A-01 bis A-36 sind vergeben. Frei."
anlass: "Der rollende Umzug laeuft seit 14.08. 22:20. Fuenf Rollen haben eigene Worktrees.
         Nichts hindert eine Rolle daran, im falschen Baum zu schreiben — das Tor kennt den
         Baum nicht (0 Treffer fuer 'worktree' in 743 Zeilen)."
gebaut_in: "ticket-rolle-generator (rolle/generator) — NICHT im gemeinsamen Checkout"
```

## Warum jetzt, und warum P0

**Seit 22:20 haben fünf Rollen eigene Worktrees.** Der Schutz, der den Umzug tragen soll, existiert
nicht: **`commit-pruefen.sh` prüft die Rollenmarke, aber nicht den Baum.** Gemessen am Basis-SHA:

```
grep -c 'worktree' scripts/commit-pruefen.sh      ->  0   (in 743 Zeilen)
ls scripts/ | grep -iE 'rolle|worktree'           ->  keine Datei
```

**Eine Rolle, die aus Gewohnheit `cd ~/Documents/ticket` tippt, schreibt weiter im gemeinsamen
Baum — und nichts hält sie auf.** Der Umzug ist damit eine Bitte, keine Barriere.

**Und ein zweiter Mangel blockiert ihn unmittelbar:** ein frischer Worktree hat kein
`node_modules`. `commit-pruefen.sh:503` leitet den Node-Fehler nach `/dev/null` und meldet in
**jedem** Fehlerfall `YAML-KOPF <pfad> — der Kopf parst nicht`. **Die erste Rolle, die umzieht,
bekommt beim ersten Commit eine Fehlermeldung, die nicht zutrifft.** Sie wird den Umzug für kaputt
halten — und nach A-03 wird eine Barriere, die aus dem falschen Grund sperrt, weggeklickt.

## Scope — drei Teile

### 1 · `scripts/rollen-tor.sh` — Baum und Rolle gehören zusammen

```
Eingabe : TICKET_ROLLE (Umgebung) · git rev-parse --show-toplevel · aktueller Branch
Arbeit  : Zuordnung pruefen. Erwartet wird:
            <rolle>  ->  Verzeichnis .../ticket-rolle-<rolle>   auf Branch rolle/<rolle>
          Ausnahme: TICKET_ROLLE=integrator gehoert in den Integrations-Checkout
            (/Users/yamanuri/Documents/ticket) — und NUR dieser darf dort schreiben.
Ausgabe : bei Verstoss eine Zeile mit BEIDEN Werten (erwartet/gefunden), sonst still.
Rueckgabe: 1 bei Verstoss — dieses Tor SPERRT (anders als A-26/A-27/A-30, die melden).
```

**Warum dieses sperrt und die anderen melden:** Die drei vorhandenen Barrieren melden Befunde
*über den Inhalt* — dort ist ein Fehlalarm teuer und ein Durchlassen billig. Hier ist es umgekehrt:
**ein Commit im falschen Baum ist genau der Schaden, gegen den die ganze Umstellung gebaut wird.**

### 2 · `docs/STATUS.md` außerhalb des Integrations-Checkouts gesperrt

Sobald `docs/STATUS.md` in der Pfadliste steht **und** der Baum nicht der Integrations-Checkout ist:
**abweisen.** Mit dem Hinweis, dass die Statuswahrheit einen Schreiber hat.

**Übergangsklausel, ausdrücklich:** Solange **kein Integrator gestartet** ist, gilt die Sperre
**nur für bereits umgezogene Rollen** — wer noch im gemeinsamen Baum arbeitet, muss dort weiter
schreiben können, sonst steht die Kette. **Der Umschaltpunkt ist `P2H-06`** (alle vier umgezogen),
nicht der Bau dieses Auftrags.

### 3 · Drei Fehlerursachen, drei Meldungen

`commit-pruefen.sh:501-503` meldet heute alles als Kopf-Fehler. **Neu:**

| Fall | Erkennung | Meldung |
|---|---|---|
| **Syntaxfehler** im YAML-Kopf | `yaml.load` wirft `YAMLException` | `YAML-KOPF <pfad> — der Kopf parst nicht` *(nur hier)* |
| **fehlende Modulauflösung** | `MODULE_NOT_FOUND` / `ERR_MODULE_NOT_FOUND` | `MODUL <pfad> — js-yaml nicht auflösbar. Dieser Worktree hat kein node_modules. Abhilfe: NODE_PATH=<integrations-checkout>/node_modules` |
| **sonstiger Laufzeitfehler** | alles Übrige | `LAUFZEIT <pfad> — <fehlermeldung>` |

**Der Node-Fehler darf nicht mehr nach `/dev/null`.** Er wird gelesen und ausgewertet.

## Nicht-Ziele

- **KEINE Änderung an `docs/STATUS.md`** — weder Inhalt noch Struktur.
- **KEIN Hausplaner-Code.** Weder `resources/` noch `app/`.
- **Kein `node_modules` je Worktree**, kein Symlink, keine Modulkopie ins Repo *(Yamas Bedingung)*.
- **Keine Abschwächung** der vorhandenen Barrieren A-25/A-26/A-27/A-30 und der YAML-Prüfung.
- **Kein Hook.** Der versionierte `pre-commit`-Hook ist ein eigener Auftrag — dieses Tor läuft
  weiter über den ausdrücklichen Aufruf.

## Kanten

| # | Fall | Verlangtes Verhalten |
|---|---|---|
| K1 | **Rolle mit Instanzsuffix** (`plan-pruefer-2`) | Zuordnung über den Rollenstamm vor dem letzten `-<ziffer>` |
| K2 | **Der Planner-Worktree heißt `ticket-rolle-planner`, der Release-Baum aber `ticket-rolle-release`** *(nicht `-release-pruefer`)* | Die Zuordnung steht in **einer** Tabelle im Skript, nicht als Namensregel — Namen und Rollen sind **nicht** durchgängig gleich |
| K3 | **Ein Worktree, den es nicht gibt** (Rolle noch nicht umgezogen) | **durchlassen und melden** — Umzug ist freiwillig getaktet, kein Zwang |
| K4 | **`git rev-parse` schlägt fehl** (kein Repo) | abweisen mit eigener Ursache, **nicht** als Rollenfehler |
| K5 | **`integrator` im gemeinsamen Checkout, aber es gibt schon umgezogene Rollen** | erlaubt — das ist sein Baum |
| K6 | **Eine andere Rolle im gemeinsamen Checkout, die noch nicht umgezogen ist** | erlaubt, aber **mit Hinweis** auf ihren wartenden Baum |

## Abnahmekriterien

- **A-37-1** · `scripts/rollen-tor.sh` existiert und ist ausführbar.
  **Messbar:** `test -x scripts/rollen-tor.sh` → exit 0. **Rot am Basis-SHA:** `ls scripts/ | grep -c rollen-tor` → **0**.
- **A-37-2** · **Positivfall: die richtige Rolle im richtigen Baum kommt durch.**
  **Messbar:** `TICKET_ROLLE=generator` im Verzeichnis `ticket-rolle-generator` auf `rolle/generator`
  → `rollen-tor.sh` exit **0**, keine Ausgabe. **Rohausgabe in den Bericht.**
  *(Dieses Kriterium ist das wichtigste und wird am leichtesten vergessen — ein Schutz, der nur
  sperrt, ist von einem kaputten nicht zu unterscheiden.)*
- **A-37-3** · **Negativfall Baum:** `TICKET_ROLLE=generator` im **Planner**-Worktree → exit **1**,
  Meldung nennt **erwarteten und gefundenen** Baum.
- **A-37-4** · **Negativfall Branch:** richtiger Baum, aber falscher Branch ausgecheckt → exit **1**.
- **A-37-5** · **Negativfall fehlende Kennung:** `TICKET_ROLLE` leer → exit **1** *(die bestehende
  Prüfung in `commit-pruefen.sh:59-61` bleibt unberührt und wird nicht verdoppelt)*.
- **A-37-6** · **`docs/STATUS.md` aus einem Rollen-Worktree wird abgewiesen.**
  **Messbar:** Aufruf mit `docs/STATUS.md` in der Pfadliste aus `ticket-rolle-generator` → `KEIN COMMIT`.
  **Rot am Basis-SHA:** dieselbe Lage heute → der Commit **läuft durch** (0 Treffer für eine Sperre).
- **A-37-7** · **`docs/STATUS.md` aus dem Integrations-Checkout mit `TICKET_ROLLE=integrator`
  kommt durch** — der Positivfall zur Sperre.
- **A-37-8** · **Drei Fehlerursachen sind unterscheidbar.** Je ein Fall, Rohausgabe:
  (a) kaputter YAML-Kopf → `YAML-KOPF`, (b) `NODE_PATH` entfernt → `MODUL` **mit dem Wort
  `node_modules` und dem Abhilfe-Hinweis**, (c) Laufzeitfehler → `LAUFZEIT`.
  **Rot am Basis-SHA:** alle drei melden heute denselben Text — belegt an `:501-503`,
  `2>/dev/null` verschluckt die Ursache.
- **A-37-9** · **Die YAML-Prüfung bleibt scharf.** Ein tatsächlich kaputter Kopf wird weiterhin
  abgewiesen — **Gegenprobe mit gesetztem `NODE_PATH`**, exit ≠ 0.
- **A-37-10** · **Kein Nicht-Ziel berührt.** `git show --stat` nennt keine Datei unter `resources/`,
  `app/`, kein `node_modules`, **nicht `docs/STATUS.md`**.
- **A-37-11** · **Suite grün und Zahl unverändert** (Stand `bc2125d9`: 1750), `tsc exit=0`.

## Rückweg und Entdeckung

- **Rückweg:** ein neues Skript und ein Aufruf. **Rücknahme = Commit zurückdrehen.** Bis das Tor
  eingebunden ist, ändert sich nichts; danach ist der schlimmste Fall, dass es zu viel sperrt —
  und das fällt sofort auf, weil dann niemand mehr committen kann.
- **Entdeckung:** A-37-2 und A-37-7 sind die Positivproben. Meldet das Tor gar nichts, sind sie
  grün und trotzdem wertlos — deshalb verlangen A-37-3/4/5/6 **Rohausgaben mit exit 1**.
- **Der Fall, der beim Bauen am ehesten übersehen wird:** K2. Der Release-Worktree heißt
  `ticket-rolle-release`, die Rolle aber `release-pruefer`. **Eine Namensregel scheitert daran;
  eine Zuordnungstabelle nicht.**

## Was dieser Auftrag nicht beantwortet

**Ob der Umzug gelingt.** Das Tor verhindert das Schreiben im falschen Baum — es bewirkt nicht,
dass jemand umzieht. **`P2H-04` bleibt offen und liegt bei Yama.**

## Beilage: Tafelzeile und Datensatz (A-20)

**A-20 verlangt Blatt, Tafelzeile und Datensatz in EINEM Commit.** Das ist im laufenden Umzug nicht
erfüllbar: `docs/STATUS.md` liegt im gemeinsamen Checkout, ich arbeite im Planner-Worktree, und ein
Schreiben von beiden Seiten erzeugt genau die Kollision, die abgeschafft werden soll.

**Deshalb liegen beide Stücke hier bei, wortgleich zum Einsetzen** — durch die erste Rolle, die
`docs/STATUS.md` ohnehin anfasst, oder durch den Integrator:

```text
Tafelzeile:
| **A-37** | Rollen-Tor: Baum, STATUS-Sperre, drei Fehlerursachen | ENTWURF | plan-pruefer |
```

```text
Datensatz:
auftrag: "A-37"
zustand: ENTWURF
basis_sha: bc2125d9
ballbesitz: plan-pruefer
dor_beleg: "steht aus"
blatt: docs/auftraege/aktiv/A-37-rollen-tor-und-drei-fehlerursachen.md
```

**Das ist eine Abweichung von A-20 und wird als solche gemeldet, nicht stillschweigend gemacht.**
Sie ist auf den Umzug befristet und endet mit `P2H-06`.
