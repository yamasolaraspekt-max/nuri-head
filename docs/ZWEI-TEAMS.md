# ZWEI TEAMS, EIN BAUM — die Trennung, ab sofort gültig

**Planner Hausplaner, 02.08.2026, 09:3x.** *Anlass: Yama richtet ein zweites vollständiges Team
ein. Diese Seite ist zuerst eine MESSUNG, dann eine Regel — sie sagt, wo die Grenze schon liegt,
bevor sie eine zieht.*

---

## 1. Die Grenze existiert bereits — gemessen, nicht erfunden

```text
git log --since='2026-08-01 20:00' --name-only | Verzeichnisse zaehlen
  31  resources/planner/hausplaner/**        <- Team HAUSPLANER
   1  routes/ · 1 app/Models · 1 app/Http/Controllers
git show --stat 0a92d5fa   (Auftrag des zweiten Planners)
  docs/product-data/15-auftrag-li-sv.md      <- Team BESCHAFFUNG
ls docs/product-data/ | wc -l   ->  14 Dateien
```

**Zwei Teams arbeiten seit gestern parallel, und keines wusste vom anderen.** Das unverfolgte
Verzeichnis `docs/product-data/`, das in der Übergabe vom 01.08. als *„von keiner der drei Rollen
angekündigt"* stand, ist das Arbeitsverzeichnis des zweiten Planners.

**Und die drei uncommitteten PHP-Dateien** (`DatanormController`, `ProductImage`, `routes/web.php`),
die Yama am 01.08. als seine bestätigt hat, liegen **exakt in der Domäne von Team Beschaffung** —
Datanorm und Produktbilder sind Beschaffung, nicht Hausplaner. *Das ist eine Vermutung mit Indiz,
keine Feststellung: der zweite Planner soll sie bestätigen oder verwerfen.*

## 2. Die Schreib-Heimat je Team

| | **Team HAUSPLANER** | **Team BESCHAFFUNG** |
|---|---|---|
| **Code** | `resources/planner/hausplaner/**` | `app/**` · `routes/**` · `config/**` · IDS/Datanorm |
| **Aufträge** | `docs/auftraege/**` | `docs/product-data/**` |
| **Planner-Papiere** | `docs/planner/**` | `docs/product-data/**` |
| **Lage** | `docs/STAND.md` | `docs/STAND-beschaffung.md` *(anzulegen)* |
| **Ledger** | `docs/handoff-status.md` | `docs/handoff-beschaffung.md` *(anzulegen)* |

**Die Mehr-App-Regel gilt jetzt zwischen den Teams:** *lesen überall frei, schreiben nur in der
eigenen Heimat.* Braucht Hausplaner eine Änderung in `app/`, geht das als eigener Vorgang an Team
Beschaffung — **nicht quer hineingeschrieben.**

## 3. Was GEMEINSAM ist — und wem es gehört

**Diese sechs Dateien brauchen beide Teams. Sie dürfen nicht doppelt existieren, sonst driften die
Regeln auseinander und keine Prüfung ist mehr belastbar.**

```text
docs/BESCHLUSS-fehlervermeidung.md     B1-B9. Gilt fuer BEIDE Teams unveraendert.
docs/auftraege/FEHLERKLASSEN.md        F-01…F-17. Eine Liste, beide tragen ein.
scripts/commit-pruefen.sh              das Commit-Tor
scripts/auftrag-pruefen.mjs            der Validator
scripts/zaehle.mjs · zeile-ersetzen.mjs  die Messwerkzeuge
docs/ZWEI-TEAMS.md                     diese Seite
```

**Schreib-Heimat fuer die gemeinsamen Dateien: wer sie ANGELEGT hat.** Heute heisst das:
`scripts/**` und die vier `docs/`-Papiere oben gehoeren Team Hausplaner. **Team Beschaffung
schreibt dort nicht selbst, sondern meldet** — im eigenen Ledger, mit dem Wort BEFUND und dem
Pfad. *Genau so hat der Generator gestern `git --no-optional-locks` gemeldet, und es hat in
zwanzig Minuten funktioniert.*

**Ausnahme, ausdruecklich: FEHLERKLASSEN.md darf jedes Team ergaenzen** — eine Fehlerklasse, die
man erst melden muss, wird nicht eingetragen. **Nur anhaengen, nie umschreiben.**

## 4. Was heute schon kollidiert ist — und was Worktrees davon lösen

```text
02.08. 09:22-09:24  VIER gleichzeitige Locks: HEAD.lock, index.lock (879 KB),
                    zwei next-index-*.lock. Ich habe 45 s gewartet und zweimal
                    gemessen, bevor ich sie beiseite geschoben habe.
01.08. 19:37 + 22:11  HEAD hat sich zweimal unter einer laufenden Messung bewegt.
```

**Ein eigener `git worktree` je Team gibt jedem einen eigenen Index und einen eigenen HEAD.**
`index.lock` und `HEAD.lock` sind dann **pro Worktree** — die haeufigste Kollision faellt weg.
`refs` und `objects` bleiben geteilt, also sieht jedes Team weiterhin die Commits des anderen.

**Das Muster steht schon im Repo:** `ticket-strang-accounting`, `ticket-strang-energie`,
`ticket-strang-formulare`, `ticket-g1b-0`, `ticket-main`.

**ABER — und das gehoert vor die Entscheidung, nicht danach:**

```text
git worktree list  meldet vom Mount aus JEDEN Worktree als `prunable`, weil die
/Users/...-Pfade aus der Geraete-VM nicht aufloesen. Ein `git worktree prune` von
dort meldet sie ALLE ab, auch ticket-main. Das ist am 01.08. gemessen worden.
Ein neuer Worktree macht diese Falle groesser, nicht kleiner.
```

**`git worktree add` ist eine Struktur-Aenderung am Repo. Sie gehoert Yama, nicht dem Planner.**

## 5. Was jedes Team auch getrennt NICHT selbst entscheidet

```text
Tor 1  Fachentscheidungen                        Yama
Tor 2  Merge nach main, Tags, --force, Deploy    Yama
Push   bis PW-01 gebaut ist und P-01 Teil 0 gemessen hat: NIEMAND
S-01   gilt JE TEAM: genau ein aktives Blatt IM EIGENEN Auftragsverzeichnis.
       Der Validator zaehlt heute ueber alle Blaetter, die man ihm uebergibt -
       jedes Team uebergibt also nur seine eigenen. Sonst sperrt S-01 falsch.
```

## 6. Die drei Fragen an das zweite Team — bitte im eigenen Ledger beantworten

```text
1  Stimmt die Domaenengrenze aus Abschnitt 2, oder fasst ihr Pfade an, die dort
   bei Hausplaner stehen?
2  Gehoeren die drei uncommitteten PHP-Dateien euch? (DatanormController,
   ProductImage $fillable 'title'->'name', routes/web.php eine IDS-Route weniger)
   Yama hat sie am 01.08. als seine bestaetigt - das Indiz spricht dagegen.
3  Gilt der Beschluss B1-B9 auch bei euch, oder trifft er eure Fehlerklassen nicht?
   Der Evaluator hat B2 als zu stark entlarvt; das war der wertvollste Beitrag des
   ganzen Abends. WIDERSPRUCH IST ERWUENSCHT.
```

---

**Solange die Worktrees nicht getrennt sind, gilt die alte Regel doppelt:** niemand stagt mit `-A`
oder `.`, jeder committet nur die Pfade, die er selbst geschrieben hat, und **wer merkt, dass der
HEAD sich unter ihm bewegt hat, hoert auf zu messen und meldet es.**
