# VOTUM A-37 — Rollen-Tor: Baum, STATUS-Sperre, drei Fehlerursachen

**evaluator · 20./21.08. · Prüfstand `f374c73a` · Claim `be46816f`**

## Ergebnis: NACHBESSERN — 20 von 21 Kriterien erfüllt, einer offen

Der offene Punkt ist **A-37-21** und er ist **nicht vom Generator allein behebbar** (siehe unten).
Alle übrigen 20 sind je einzeln am Lauf belegt, nicht am Text.

## Wie ich gemessen habe

Prüfstand: eigener Worktree am Fernstand, `node_modules`/`vendor` per `cp -al`. **§15 belegt mit
Gegenprobe:** `--env=testing` → `ticket_testing`, ohne → `ticket`. Der Beleg unterscheidet also
wirklich; ohne die zweite Zahl wäre er wertlos.

**Den Bau gesucht, nicht aus `bau_sha` genommen.** Das Feld nennt `374bb851` + `139872cb` — beide
an `scripts/commit-pruefen.sh`. Das Tor selbst (`scripts/rollen-tor.sh`) entstand in **vierzehn
weiteren** Commits. Scope also 16 Commits, 4 Dateien.

## Messtisch — jede Zeile, auch die nicht gefahrenen

| # | Ergebnis | Beleg |
|---|---|---|
| A-37-1 | **erfüllt** | `test -x` → 0; Rot am Basis `bc2125d9`: **0 Treffer** |
| A-37-2 | **erfüllt** | `generator`@`ticket-rolle-generator` → exit **0**, Ausgabe **leer**; zweiter Baum ebenso |
| A-37-3 | **erfüllt** | generator im Planner-Baum → exit **1**, nennt erwartet **und** gefunden |
| A-37-4 | **erfüllt** | richtiger Name, falscher Zweig → exit **1**, „gefunden: … auf HEAD" |
| A-37-5 | **erfüllt** | Tor ohne `TICKET_ROLLE` → **exit 5**, der berichtigten Tabelle folgend |
| A-37-6 | **erfüllt** | `docs/STATUS.md` aus Rollen-Worktree → **KEIN COMMIT** |
| A-37-7 | **erfüllt** | Integrations-Checkout als integrator: **0** Sperr-Treffer; Abbruch nur wegen `UNVERAENDERT` |
| A-37-8 | **erfüllt** | (a) `YAML-KOPF … 1 kaputte Bloecke, am Commit waren es 0` · (b) `MODUL` / **3** · (c) `LAUFZEIT ENOENT…` / **4** |
| A-37-9 | **erfüllt** | kaputter Block mit gesetztem `NODE_PATH` → exit **1**, bleibt scharf |
| A-37-10 | **erfüllt** | 16 Bau-Commits → **4** Dateien, alle `scripts/`; `app/` 0 · `node_modules` 0 · `docs/STATUS.md` 0 |
| A-37-11 | **erfüllt** | Suite **1765 pass / 0 fail**; `tsc:hausplaner` exit **0**, `error TS` **0** |
| A-37-12 | **erfüllt** | Marke gelesen (belegt durch -13/-14); Hash = `git hash-object package-lock.json`, **identisch** |
| A-37-13 | **erfüllt** | fremder Hash → exit **6**, Wort `MODULSTAND` + Hinweis `npm ci in diesem Baum` |
| A-37-14 | **erfüllt** | Marke stimmt → **0** Ausgabezeilen; Marke fehlt → eigene Meldung „MODULSTAND UNBEKANNT" |
| A-37-15 | **erfüllt** | `wc -w` = **8**; Feldnamen `hash zeit node npm` in dieser Reihenfolge |
| A-37-16 | **erfüllt** | `scripts/module-nachziehen.sh:149` schreibt die Marke; Hash trägt **dieses** Lockfile |
| A-37-17 | **erfüllt** | `grep -c K6` = **15**; alle sechs Kanten vertreten (K1 3 · K2 5 · K3 18 · K4 12 · K5 9 · K6 15) |
| A-37-18 | **erfüllt** | `ls-files` = **1 in allen sechs Bäumen**, Zweige je korrekt |
| A-37-19 | **erfüllt** | am Lauf: `ROLLENMARKE mit Zusatz erkannt: 'evaluator' (in Vertretung) — Betreff bleibt unveraendert.` |
| A-37-20 | **erfüllt, mit Anmerkung** | Codes **3** und **4** selbst erzeugt, **2** im Prüfer erreichbar — aber `commit-pruefen.sh` gibt für **alle** Ursachen **1** |
| A-37-21 | **NICHT ERFÜLLT** | `js-yaml` in `dependencies`: **keine** · `devDependencies`: **keine** |

**Nicht gefahren:** kein Kriterium. Browserabnahme entfällt — der Bau hat keine sichtbare Wirkung
(4 Shell-Skripte, 0 Dateien unter `resources/`).

## Der eine rote Punkt: A-37-21

Das Kriterium sagt wörtlich: *„Verlangt: `js-yaml` wird als direkte `dependency` deklariert."*

Vierfach gegengeprüft, bevor ich rot schreibe:
1. genau **eine** `package.json` im Repo,
2. **0** Treffer für `js-yaml` darin,
3. installiert ist es (4.1.1), aber **nur transitiv**: `puppeteer 24.39.1 → cosmiconfig 9.0.1 → js-yaml 4.1.1` — **exakt die Kette, die das Kriterium beschreibt**,
4. am Basis-SHA ebenso wenig deklariert, also nicht durch den Bau verloren.

**Der Generator hat den Mangel gemessen, dokumentiert und ausdrücklich nicht behoben**
(`commit-pruefen.sh:641-665`): *„Die Abhilfe ist EINE Zeile … und sie gehört nicht mir:
`package.json` und das Lockfile sind gemeinsamer versionierter Code, und eine Abhängigkeit
einzutragen ändert den Baum aller sechs Rollen."*

**Diese Einordnung halte ich für richtig** — sie ist dieselbe, die ich für `phpunit.xml` getroffen
habe. Damit ist der Punkt **nicht durch Nachbessern beim Generator zu schließen**: er würde ihn
erneut und mit derselben Begründung ablehnen. **Die Auflösung ist eine Entscheidung des Planners:**
entweder den Eintrag ausdrücklich beauftragen, oder das Kriterium anpassen. Beides ist eine Zeile.

## Zwei SPEC-Anmerkungen, keine Baumängel

1. **A-37-5 widerspricht sich selbst.** Die Kriterien-Überschrift sagt „→ **exit 3**", die in DoR
   Runde 3 berichtigte Tabelle elf Zeilen darunter sagt **5**. Der Bau folgt der Tabelle (gemessen:
   exit 5) und **dokumentiert den Widerspruch selbst** (`rollen-tor.sh:81-100`), ohne das Blatt
   anzufassen. Die Überschriftzeile wurde bei der Berichtigung nicht mitgezogen — **eine Zeile
   beim Planner.**
2. **Die Codetabelle schreibt 2/3/4 dem `commit-pruefen.sh` zu.** Gemessen vergibt sie der darin
   eingebettete YAML-Prüfer; `commit-pruefen.sh` selbst endet bei **allen drei** Ursachen mit **1**
   — im Bau an drei Stellen so dokumentiert (`:672`, `:676`, `:680`). Die **Unterscheidbarkeit**,
   um die es dem Kriterium geht, ist über die Meldungen `YAML-KOPF` / `MODUL` / `LAUFZEIT`
   gegeben und von mir je einzeln erzeugt. Wer den Rückgabewert als Unterscheidungsmerkmal liest,
   findet ihn nicht.

## Eigene Fehler in dieser Abnahme — vier, alle vor der Behauptung bemerkt

1. **Der Claim ging zuerst nach `docs/STATUS.md`** und wurde vom Rollen-Tor abgewiesen. Ohne die
   Frage „ist das vielleicht so gewollt?" hätte ich **Abnahmekriterium A-37-6 als Mangel gemeldet**.
   Änderung sofort zurückgenommen, md5 zurück auf `55747864`.
2. **A-37-7 zuerst falsch gemessen:** ich fuhr nur `rollen-tor.sh` statt des Commit-Wegs mit
   `docs/STATUS.md` — der Name des Kriteriums getroffen, seine Sache verfehlt.
3. **A-37-8 zweimal falsch aufgebaut:** erst Front Matter statt ```yaml-Zaun (der Prüfer liest
   `t.matchAll(/```yaml\n…/g)`, also fand er 0 Blöcke); dann `NODE_PATH` auf ein leeres Verzeichnis,
   was die lokale Auflösung gar nicht abschaltet. Erst der Kern in `ticket-rolle-planner` (Baum
   **ohne** `node_modules`) stellte den Fall her.
4. **A-37-10 zweimal „0 Dateien" gemeldet**, was bei 16 Commits nicht sein kann: `for c in $BAU`
   splittet in **zsh** nicht, der ganze String ging als ein Wort an `git show`, und `2>/dev/null`
   verschluckte es. Mit echtem Array neu.

Dazu die Hardlink-Falle: `cp -al` teilt die Inodes. Die Modulstand-Marke im Prüfstand und in meinem
Rollenbaum waren **dieselbe Datei** (Inode `71532527`). Ich habe den Link gelöst statt zu
überschreiben; md5 im Rollenbaum vorher wie nachher `99841c04`.

## Weitergabe

**NACHBESSERN**, aber der Ball geht **nicht** an den Generator: der einzige offene Punkt verlangt
eine Entscheidung über gemeinsamen versionierten Code. **Ball beim Planner** für A-37-21 und die
beiden SPEC-Anmerkungen.

**Den Zustand setze ich nicht** — `docs/STATUS.md` ist mir nach A-37-6 gesperrt, dem Kriterium, das
ich gerade abgenommen habe. Den Zustandswechsel trägt der **Integrator** nach.
