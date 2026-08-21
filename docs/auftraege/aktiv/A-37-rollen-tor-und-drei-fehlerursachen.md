# A-37 — Das Tor muss wissen, in welchem Baum es steht: Rollenbindung, STATUS-Sperre und drei unterscheidbare Fehlerursachen

```yaml
auftrag: "A-37"
werkzeug: "— (Werkzeug der Rollenkette, kein Hausplaner-Werkzeug)"
art: "BAU — Erweiterung von scripts/commit-pruefen.sh plus ein neues Pruefskript.
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
basis_sha: bc2125d9
prioritaet: P0
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 14.08. 22:35 — Claim VOR dem Schnitt."
kennung_geprueft: "A-37 hat NULL Treffer in docs/STATUS.md und NULL Blaetter in
                   docs/auftraege/aktiv/. A-01 bis A-36 sind vergeben. Frei."
anlass: "Der rollende Umzug laeuft seit 14.08. 22:20. Fuenf Rollen haben eigene Worktrees.
         Nichts hindert eine Rolle daran, im falschen Baum zu schreiben — das Tor kennt den
         Baum nicht (0 Treffer fuer 'worktree' in 743 Zeilen)."
gebaut_in: "ticket-rolle-generator (rolle/generator) — BERICHTIGT ZURUECK am 15.08. 15:50.
            Der Grund fuer die Verlegung in den Integrations-Checkout ist ENTFALLEN: der
            Generator-Baum hat seit 15:36:54 node_modules samt typescript, gemessen. Der
            Plan-Pruefer hat A-37-11 dort gefahren: tsc exit 0, Suite 1763/1763.
            KEIN Blattfehler und kein Messfehler auf einer der beiden Seiten — die Zeitstempel
            liegen so: Blatt 15:30:37, release/node_modules 15:30:51, generator 15:36:54.
            Mein Befund hielt VIERZEHN SEKUNDEN. Die Umgebung ist unter dem Satz weggewandert.
            OFFEN UND YAMA VORGELEGT, siehe Nicht-Ziele: die zwei node_modules sind ECHTE
            Verzeichnisse mit je 323 MB, keine Symlinks — und Yamas Nicht-Ziel schliesst
            genau das aus."
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

### 3 · Drei Fehlerursachen, drei Meldungen — **✅ VORAB GEBAUT am 15.08., ohne DoR**

> **⚠ TEIL 3 IST BEREITS GEBAUT** — `374bb851`, Generator, **auf Yamas ausdrückliche Anweisung und
> ohne DoR**. Er hat es als Erstes gemeldet statt es einzureihen: *„Das melde ich zuerst, weil es
> eine Ausnahme ist: A-37 steht auf ENTWURF beim Planner und ist NICHT BEREIT; ich habe es nicht
> gezogen."* **Teil 1 und Teil 2 hat er ausdrücklich nicht angefasst** — die gehören in den Auftrag,
> wenn er `BEREIT` ist.
>
> **Selbst nachgemessen:** `grep -cE 'MODUL|LAUFZEIT'` → **5** *(war 0)*. Die drei Fälle sind
> einzeln gefahren, mit Rückgabecodes **2 / 3 / 4** und Rohausgabe im Bau-Bericht. **Die Barriere
> ist nicht abgeschwächt:** dieselbe Datei im selben Baum **mit** `NODE_PATH` gibt `BLOECKE 0
> KAPUTT 0`, Rückgabe 0.
>
> **Ein zweiter Mangel kam dazu, der in keinem Auftrag stand** und den der Release-Prüfer an
> **seinem eigenen** kaputten YAML fand: `t.match` auf `:501` lief **ohne `g`-Flag** und las genau
> **einen** Block je Datei. Bei einem Auftragsblatt ist das richtig — bei `docs/STATUS.md` mit
> **302** Blöcken sind es **0,3 %** der Datei.
>
> **Und die Lösung wiegt schwerer als der Fund.** Hätte der Generator einfach das `g` gesetzt, wäre
> **ab sofort jeder Commit an `docs/STATUS.md` gesperrt** — wegen **25** Blöcken, die seit Wochen
> liegen und die der Schreibende nicht verursacht hat. **Dieselbe Falle wie Mangel 1, nur
> andersherum: eine Barriere, die zu oft und am Falschen sperrt.** Stattdessen vergleicht der
> Prüfer den Arbeitsstand gegen den **committeten** Stand derselben Datei und sperrt **nur, wenn die
> Zahl wächst**. *Kaputte Blöcke dürfen schrumpfen, nie wachsen.* **Kein fester Schwellwert, der
> driftet — genau das hat er selbst an A-07-5 kritisiert.**
>
> **Was das für diesen Auftrag heißt:** **A-37-8 und A-37-9 sind erfüllt, bevor der Auftrag `BEREIT`
> ist.** Nach §5 darf ein Kriterium nicht schon erfüllt sein — **hier ist es das, und zwar durch
> eine benannte Ausnahme, nicht durch einen Schnittfehler.** Der Evaluator prüft sie am Bau-Commit
> `374bb851`; **neu zu bauen ist Teil 3 nicht.**

*(Der ursprüngliche Scope-Text bleibt als Beleg stehen — A-20-4.)*

`commit-pruefen.sh:501-503` meldet heute alles als Kopf-Fehler. **Neu:**

| Fall | Erkennung | Meldung |
|---|---|---|
| **Syntaxfehler** im YAML-Kopf | `yaml.load` wirft `YAMLException` | `YAML-KOPF <pfad> — der Kopf parst nicht` *(nur hier)* |
| **fehlende Modulauflösung** | `MODULE_NOT_FOUND` / `ERR_MODULE_NOT_FOUND` | `MODUL <pfad> — js-yaml nicht auflösbar. Dieser Worktree hat kein node_modules. Abhilfe: NODE_PATH=<integrations-checkout>/node_modules` |
| **sonstiger Laufzeitfehler** | alles Übrige | `LAUFZEIT <pfad> — <fehlermeldung>` |

**Der Node-Fehler darf nicht mehr nach `/dev/null`.** Er wird gelesen und ausgewertet.

### 5 · Lockfile-Prüfung — **Yamas Bedingung für (a), 16.08.**

**Ohne sie ist ein eigenes `node_modules` je Baum Disziplin. Mit ihr ist es Mechanik.**

```
Vor jedem Lauf vergleicht das Tor:
   git hash-object package-lock.json        (heute in allen vier Baeumen: d17b19a2…)
   gegen die Marke, aus der dieses node_modules erzeugt wurde

Abweichung -> ABBRUCH:
   "MODULSTAND — dieser Baum wurde aus einem anderen package-lock.json
    installiert. Abhilfe: npm ci in diesem Baum."
   Rueckgabe != 0. Kein Lauf, kein gruenes Ergebnis.
```

**⚠ Die Marke muss GESCHRIEBEN werden — npm liefert sie nicht.** Zwei naheliegende Wege sind
gemessen und beide tragen **nicht**:

| Weg | warum er scheitert |
|---|---|
| **`mtime`-Vergleich** `package-lock.json` gegen `node_modules/.package-lock.json` | **`git checkout` setzt die mtime neu, auch bei gleichem Inhalt** → Fehlalarm bei jedem Branchwechsel. *(Gemessen: im Generator-Baum steht der Lockfile auf 08-14 22:15, `.package-lock.json` auf 08-15 15:36 — der Abstand sagt nichts über den Inhalt.)* |
| **Hash von `node_modules/.package-lock.json`** | **Es ist eine andere Datei:** 404 Pakete gegen **466** im Lockfile. Kein Vergleich möglich — **und der Grund dahinter wiegt schwerer als die Zahl**, siehe unten. |

**Deshalb, entschieden statt geraten:** nach jedem `npm ci` schreibt der Baum die Marke selbst —

```
npm ci && git hash-object package-lock.json > node_modules/.aus-lockfile
```

**Das Tor liest sie und vergleicht.** Fehlt sie, ist der Modulstand **unbekannt** und nicht
etwa gültig — eigene Meldung, eigener Rückgabewert.

**⚠ Der zweite Grund für die Dateiwahl, und er ist der stärkere** *(Yamas Gegenprobe, 16.08., von
mir nachgemessen)*: **Die 62 fehlenden Einträge sind kein Rauschen — 61 davon tragen eine
`os`/`cpu`-Einschränkung, 0 sind dev-only.** npm legt nur ab, was zu dieser Maschine passt; diese
ist `darwin arm64`, also fehlt `@esbuild/darwin-x64` **zu Recht**.

| Datei | beantwortet |
|---|---|
| `package-lock.json` | **aus welcher Abhängigkeitswahrheit** installiert wurde — **maschinenunabhängig** |
| `node_modules/.package-lock.json` | **was auf DIESER Maschine gelandet ist** — **maschinenabhängig** |

**Liefe je ein Rollenbaum auf einer anderen Architektur, wäre `.package-lock.json` dort zu Recht
verschieden** — ein Tor darauf würde eine **richtige** Umgebung als falsch melden. Die gewählte
Datei ist die einzige, die *„welche Wahrheit"* von *„welche Maschine"* trennt.

**⚠ Und die Marke ist selbst eine flüchtige Messung — Yamas Nachtrag, und er wendet meine eigene
Regel von vor einer Stunde auf mein eigenes Werkzeug an.** Der Hash ist ein SHA über eine
unveränderliche Datei; **die Aussage der Marke ist es nicht:** *„dieses Modulverzeichnis stammt aus
diesem Lockfile"* gilt **ab** der Installation und kann ablaufen. **Praktisch zählt das, weil aus
demselben `lockfileVersion 3` verschiedene npm-Hauptversionen unterschiedlich auslegen** — steht in
einem Baum grün und im anderen rot bei gleichem Hash, ist die nächste Frage *„mit welchem npm?"*,
und die Antwort wäre nicht da. **Die Marke trägt deshalb vier Felder:**

```
npm ci && printf '%s  %s  node %s  npm %s\n' \
  "$(git hash-object package-lock.json)" "$(date -Iseconds)" \
  "$(node -v)" "$(npm -v)" > node_modules/.aus-lockfile

Heute waere das:  d17b19a2…  2026-08-16T…  node v26.5.0  npm 11.17.0
```

**Das Tor liest und vergleicht ausschließlich das ERSTE Feld.** Die übrigen drei kosten nichts und
beantworten die Frage, die **nach** dem ersten Widerspruch kommt.

## Nicht-Ziele

- **KEINE Änderung an `docs/STATUS.md`** — weder Inhalt noch Struktur.
- **KEIN Hausplaner-Code.** Weder `resources/` noch `app/`.
- **⚠ ERSETZT am 16.08. durch Yamas §1-Entscheidung — neuer Wortlaut, wörtlich:**

  > **„Kein Modulverzeichnis wird versioniert oder ins Repo eingebracht. Je Baum ein eigenes
  > `node_modules`, ausschließlich aus dem `package-lock.json` DIESES Baumes erzeugt.
  > KEIN Prüfergebnis darf davon abhängen, in welchem Baum es lief."**

  **Der Grund ist nicht der Preis, sondern die zweite Wahrheit** *(Yama, 16.08.)*:
  `package-lock.json` liegt **im Repo**, jeder Rollen-Branch trägt seine eigene Fassung. Ändert
  **ein** Branch eine Abhängigkeit, ist ein geteiltes `node_modules` für **höchstens einen** Baum
  richtig und für die anderen fünf **still falsch** — *„Der Lauf schlägt nicht fehl. Er ist grün
  und er misst den falschen Stand."* **Das ist Richtung B, eine Ebene tiefer.**

  **Harte Links sind ausdrücklich NICHT der Weg:** sie lösen die Regelfrage nicht und **brechen
  still**, sobald ein Werkzeug eine Paketdatei an Ort und Stelle überschreibt.

  *(Der bisherige Wortlaut bleibt als Beleg stehen — A-20-4:)*

  > ~~- **Kein `node_modules` je Worktree**, kein Symlink, keine Modulkopie ins Repo
  > *(Yamas Bedingung)*.~~ **Die Zeile trug zwei Lesarten**, weil `ins Repo` sich auf alle drei
  > oder nur auf das letzte Glied beziehen konnte. **Yama hat die engere entschieden (L2)** — mit
  > der Begründung, dass eine Bedingung mit zwei Lesarten im Zweifel die engere hat, *„die weite
  > hätte ich mir sonst nachträglich zurechtgelegt."*

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
| K6 | **Eine andere Rolle im gemeinsamen Checkout, deren Baum SCHON STEHT** | **erlaubt**, aber **mit Hinweis** auf ihren wartenden Baum. **⚠ EIGENER FALL, NICHT eine Variante von K3** — siehe unten |

**⚠ K6 IST DIE KANTE, DIE DEN LAUFENDEN BETRIEB SCHÜTZT — und sie ist im ersten Bau ausgefallen.**

**Belegt statt vermutet** *(Plan-Prüfer, 16.08., das gebaute Skript aus `0ee521f7` geholt und
gefahren)*:

```
release-pruefer   exit 1  VERSTOSS      <- faehrt heute achtmal den Transport
evaluator         exit 1  VERSTOSS      <- hat A-33 in der Abnahme
integrator        exit 0  richtig
plan-pruefer      exit 0  (eigener Baum)
```

**Das Tor ist eingehängt — daraus wird `KEIN COMMIT`.** Es trifft genau die zwei Rollen, die gerade
tragen.

**Warum K6 durchgefallen ist, und es ist eine Blattschwäche, keine Bauschwäche:**

| | K3 | K6 |
|---|---|---|
| **Bedingung** | der Baum **existiert nicht** | der Baum **steht schon** |
| **Rolle** | noch nicht umgezogen | noch nicht umgezogen |
| **Verhalten** | durchlassen + melden | **durchlassen + melden** |

**Dieselbe Antwort, zwei verschiedene Bedingungen.** Wer K3 baut, hat das Gefühl, den Fall erledigt
zu haben — **im Code greift K3 aber nur, wenn das Verzeichnis fehlt.** Steht es, fällt der Fall in
den Schlusszweig und wird zum Verstoß. **Der Kopf des gebauten Skripts listet folgerichtig nur
K1–K5; K6 kommt dort nicht vor.**

**Die Begründung, die im gebauten Code selbst steht, gilt für K6 unverändert:** *„Ein Tor, das den
Umzug erzwingt, hält die Kette an statt sie zu schützen."* **K6 ist derselbe Gedanke für den Fall,
dass der Baum schon dasteht** — und ohne ihn erzwingt das Tor genau das, was K3 verhindern soll.

## Abnahmekriterien

- **A-37-1** · `scripts/rollen-tor.sh` existiert und ist ausführbar. **Erzeuger: DIESER Auftrag, Teil 1** — `scripts/rollen-tor.sh` wird vom **Generator** in `rolle/generator` gebaut; der Scope nennt ihn als Liefergegenstand. *(Im Kriterium genannt, damit P3 den Erzeuger nicht suchen muss.)*
  **Messbar:** `test -x scripts/rollen-tor.sh` → exit 0.
  **Rot am Basis-SHA:** `ls scripts/ | grep -c rollen-tor` → **0**.
  **⚠ Zum Messbefehl, vom Prüfer bemerkt und selbst nachgefahren:** `grep -c` gibt bei null
  Treffern die **0 aus und beendet mit 1**. **Unter `set -e` bricht das Skript an dieser Stelle
  ab, obwohl die Ausgabe richtig ist.** Wer den Rot-Beleg in einem Skript fährt, schreibt
  `ls scripts/ | grep -c rollen-tor || true` — **die Zahl stimmt, der Exit-Code täuscht.**
- **A-37-2** · **Positivfall: die richtige Rolle im richtigen Baum kommt durch.**
  **Messbar:** `TICKET_ROLLE=generator` im Verzeichnis `ticket-rolle-generator` auf `rolle/generator`
  → `rollen-tor.sh` exit **0**, keine Ausgabe. **Rohausgabe in den Bericht.**
  *(Dieses Kriterium ist das wichtigste und wird am leichtesten vergessen — ein Schutz, der nur
  sperrt, ist von einem kaputten nicht zu unterscheiden.)*
- **A-37-3** · **Negativfall Baum:** `TICKET_ROLLE=generator` im **Planner**-Worktree → exit **1**,
  Meldung nennt **erwarteten und gefundenen** Baum.
- **A-37-4** · **Negativfall Branch:** richtiger Baum, aber falscher Branch ausgecheckt → exit **1**.
- **A-37-5** · **Negativfall fehlende Kennung:** `TICKET_ROLLE` leer → **exit 5**.
  *(Berichtigt 21.08., Planner nach VOTUM A-37 / Gesamtauftrag v2 Phase 1: hier stand „exit 3" und
  widersprach damit der Tabelle unten — Code 3 ist dort die fehlende Modulauflösung — und dem
  Rückweg-Absatz, der **5** nennt. **Gemessen 21.08.:** `TICKET_ROLLE= bash scripts/rollen-tor.sh` →
  `exit=5`. Überschrift, Tabelle und Rückweg nennen jetzt denselben Wert.)*
  **Entschieden am 15.08. nach DoR-Restpunkt 3, benannt statt geraten:** `rollen-tor.sh` prüft
  **eigenständig**, denn bei direktem Aufruf ist keine andere Prüfung davor. **Drei
  unterscheidbare Codes**, damit die Quelle am Code ablesbar ist:

  | Code | Bedeutung | wer | Stand |
  |---|---|---|---|
  | **1** | Rolle und Baum passen nicht zusammen | `rollen-tor.sh` | zu bauen |
  | **2** | **YAML-Syntaxfehler** im Kopf | `commit-pruefen.sh` | **gebaut** (`374bb851`) |
  | **3** | **fehlende Modulauflösung** (`MODUL`) | `commit-pruefen.sh` | **gebaut** |
  | **4** | **sonstiger Laufzeitfehler** (`LAUFZEIT`) | `commit-pruefen.sh` | **gebaut** |
  | **5** | Rollenkennung fehlt **beim direkten Aufruf des Tors** | `rollen-tor.sh` | zu bauen |
  | **6** | **`MODULSTAND`** — Baum aus fremdem Lockfile installiert | `rollen-tor.sh` | zu bauen |
  | *(2)* | *Rollenkennung fehlt/falsche Form* | *`commit-pruefen.sh:59-65`, unberührt* | *vorhanden* |

  **⚠ BERICHTIGT am 16.08. nach DoR Runde 3 — es war eine Kollision, kein Formfehler.**
  Meine Fassung vom 15.08. vergab **3** für „Kennung fehlt beim direkten Aufruf". **Der Generator
  hatte am selben Tag in Teil 3 bereits `MODUL` auf 3 gelegt** (`374bb851`, gefahren und belegt).
  **Zwei Bedeutungen auf einem Code — und keine der beiden Seiten hat es bemerkt**, weil jede nur
  ihren eigenen Teil las. **Der Plan-Prüfer hat es gefunden** *(„MODULSTAND ohne eigenen Code bei
  einem bis 4 belegten Zahlenraum")*. Die Kennung liegt jetzt auf **5**, `MODULSTAND` auf **6**.
  *Die gebauten Codes 2/3/4 bleiben unberührt — ein fertiger Bau wird nicht umnumeriert.*

  **Das ist keine Verdopplung, sondern eine zweite Eintrittstür:** wer `commit-pruefen.sh` ruft,
  wird von `:59-65` mit **2** abgewiesen und erreicht das Tor gar nicht. Wer das Tor direkt ruft,
  braucht die Prüfung — sonst vergleicht es einen leeren Rollennamen mit einem Verzeichnis.
  *(Selbst nachgemessen: `:59-63` beendet mit **2**.)*
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
- **A-37-12** · **Lockfile-Prüfung im Tor.** **Messbar:** Marke `node_modules/.aus-lockfile`
  wird von `npm ci` geschrieben und vom Tor gelesen.
  **Rot am Basis-SHA und heute:** `grep -rl 'package-lock' scripts/` → **0**, `npm ci` → **0**,
  `hash-object` → **0**. *(Selbst nachgemessen 16.08.)*
- **A-37-13** · **Negativfall Modulstand:** Marke auf einen fremden Hash setzen → **Abbruch**
  mit dem Wort `MODULSTAND` und dem Hinweis `npm ci in diesem Baum`, Rückgabe ≠ 0.
- **A-37-14** · **Positivfall:** Marke stimmt → Lauf geht durch, **keine Ausgabe**.
  *(Und der dritte Fall getrennt: Marke **fehlt** → eigene Meldung „Modulstand unbekannt", nicht
  stillschweigend als gültig behandelt.)*
- **A-37-15** · **Die Marke trägt vier Felder MIT FELDNAMEN**, in dieser Reihenfolge:

  ```
  hash <sha>  zeit <iso8601>  node <version>  npm <version>
  ```

  **Messbar:** `wc -w < node_modules/.aus-lockfile` = **8**; `cut -d' ' -f2` liefert den Hash.
  **Rot:** die Datei existiert nicht.
  **⚠ BERICHTIGT am 16.08. nach DoR Runde 3:** die erste Fassung verlangte `≥ 6` **ohne das Format
  festzulegen**. Nachgerechnet: **vier reine Werte ergeben 4 Wörter und fallen durch**, mit
  Feldnamen sind es 8. *„Das Kriterium war genauer als die Zusage"* — dieselbe Klasse wie A-37-12,
  wo der Schreiber der Marke fehlte.
- **A-37-20** · **DIE RÜCKGABEWERTE MÜSSEN IM BAU AUCH VERGEBEN WERDEN.**
  **⚠ ZWEI BEFUNDE DES PLAN-PRÜFERS, beide zutreffend und zusammengehörig:**
  *(a)* **`exit 2` trägt im Blatt zwei Bedeutungen und im Bau mindestens vier** — das ist P5,
  auf dieses Blatt selbst angewandt.
  *(b)* **Die Codes 2, 3 und 4 werden im Bau GAR NICHT vergeben: alle drei Ursachen enden bei
  `exit 1`.** *Ein Kriterium, das drei unterscheidbare Ursachen verlangt, ist damit nicht
  erfüllt — auch wenn die Codetabelle im Blatt steht.*
  **Verlangt:** je Ursache ein eigener, im Bau **wirklich erreichbarer** Rückgabewert, belegt
  durch **je einen Lauf mit Rohausgabe und `echo $?`** — nicht durch die Tabelle.
  **Klarstellung 21.08. (Planner, Gesamtauftrag v2 Phase 1):** Die Unterscheidung 2/3/4 gilt für die
  **inneren** Prüfer (YAML-Syntax / Modulauflösung / Laufzeit — die Meldungsklassen `YAML-KOPF`,
  `MODUL`, `LAUFZEIT`); **nach außen** endet `commit-pruefen.sh` im Abweisungsfall mit **1**
  („KEIN COMMIT"), und `rollen-tor.sh` mit **1** (Baum/Branch) bzw. **5** (fehlende Kennung).
  Beides zusammen ist **kein** Widerspruch, sondern zwei Ebenen — das Blatt benennt sie ab jetzt
  getrennt, damit der Evaluator weiß, **welchen** Rückgabewert er wo misst.
  **Zählen der `raus()`-Aufrufe genügt nicht, wenn kein Pfad sie erreicht.**
- **A-37-19** · **DIE MARKENERKENNUNG MUSS ROLLENMARKEN MIT ZUSATZ ERKENNEN.**
  **Gemessen 19:4x:** die Erkennung sucht `^[a-z][a-z-]*(-[0-9]+)?: ` *(heute Zeile **150**,
  nicht mehr 73 — die Datei ist gewachsen; **diese Nummer gehört mitgemessen, nicht zitiert**)*.
  **Sie greift NICHT auf:**
  ```
  release-pruefer (in Yamas Namen): …     -> kein Treffer
  plan-pruefer (release-pruefer in Rollenwechsel): …  -> kein Treffer
  planner: …                              -> Treffer
  ```
  **Folge nach Zeile 151 ff.: das Tor hält den Betreff für markenlos und stellt die Rolle
  voran** — aus `release-pruefer (in Yamas Namen): …` würde `release-pruefer: release-pruefer
  (in Yamas Namen): …`. **Im Bestand stehen diese drei Betreffs sauber**, also sind sie **am Tor
  vorbei** entstanden oder mit einer älteren Fassung.
  **Verlangt:** entweder erkennt die Marke einen Klammerzusatz, **oder** der Zusatz ist
  ausdrücklich verboten und das Tor weist ihn ab. **Beides ist besser als stille Verdopplung** —
  und die Form ist real: `in Yamas Namen` und `in Rollenwechsel` sind genau die Fälle, in denen
  jemand für einen anderen handelt und es kenntlich macht. *Die Kenntlichmachung darf nicht
  bestraft werden.*
- **A-37-21** · **DIE ABHÄNGIGKEIT DES TORS MUSS DEKLARIERT SEIN.**
  **Befund des Generators (`8a1ad00d`), vom Plan-Prüfer bis zur Wirkung durchgemessen
  (`6c51931c`) — beide am Lauf, nicht am Text:**
  ```
  js-yaml ist NIRGENDS als dependency deklariert.
  Die Kette:  puppeteer ^24.39.1 (direkte dependency)
                -> cosmiconfig  -> js-yaml
                -> YAML-Pruefung in commit-pruefen.sh
  Fehlt js-yaml, greift FEHLER=1 und JEDER .md-Commit wird abgewiesen —
  fuer JEDE Rolle, denn alle schreiben Blaetter und Befunde.
  ```
  **Am Lauf belegt:** mit auffindbarem `js-yaml` → Trockenlauf `exit 0`; mit `NODE_PATH` auf
  ein leeres Verzeichnis → `exit 1` mit Meldung `MODUL` und Abhilfe. *(Beide `--trocken`, kein
  Commit, Probedatei entfernt, Baum wieder auf 0.)*
  **Verlangt: `js-yaml` wird als direkte `dependency` deklariert.** *Eine Barriere, die an einer
  transitiven Abhängigkeit hängt, fällt aus, sobald jemand ein unbeteiligtes Paket entfernt —
  und sperrt dann die gesamte Kette, ohne dass die Ursache am Fehlerbild ablesbar wäre.*
  **ENTSCHIEDEN 21.08. (Planner, nach VOTUM A-37 = NACHBESSERN 20/21, offen nur dieses Kriterium;
  Gesamtauftrag v2 Phase 1):** `js-yaml` wird als **direkte, versionierte `dependency`** in
  `package.json` aufgenommen (gleiche Hauptversion wie die heute transitiv geladene, damit kein
  Verhaltenswechsel), `package-lock.json` aktualisiert. **Nachbesserung = genau dieser Schritt,
  nichts sonst** (§12.2: Umfang ist der Befund). Kette: Plan-Prüfer bestätigt die Revision →
  Generator **im eigenen Worktree** → Evaluator prüft danach **alle** A-37-Kriterien erneut, nicht
  nur A-37-21 (Yama). Abnahmebeleg: `grep -n '"js-yaml"' package.json` → Treffer in `dependencies`;
  `npm ls js-yaml` zeigt den direkten Eintrag; Trockenlauf `commit-pruefen.sh --trocken` mit
  `NODE_PATH` auf ein leeres Verzeichnis zeigt weiterhin `MODUL` (Meldung bleibt, Abhängigkeit
  ist nur nicht mehr transitiv).
  **Das Tor selbst ist an dieser Stelle vorbildlich:** es trennt vier Lagen — heil, YAML-Syntax,
  Modulauflösung, Laufzeit — und meldet den Modulfehler **als solchen** statt als Kopf-Fehler.
  *Der Mangel liegt nicht im Tor, sondern in der Deklaration, an der es hängt.*
- **A-37-18** · **DAS TOR MUSS IN ALLEN SECHS BÄUMEN VORHANDEN SEIN.**
  `git ls-files scripts/rollen-tor.sh` ergibt in **jedem** der sechs Arbeitsbäume **1**.
  **Rot, gemeldet vom Integrator (`83296554`, 16:17):** *„die Barriere ist hier nicht
  vorhanden — `rollen-tor.sh` liegt bei mir 0 Mal im Index."*
  **Das ist nicht dasselbe wie K5.** K5 fragt, ob das Tor die Rolle `integrator` **kennt** —
  das tut es. **A-37-18 fragt, ob das Tor bei ihr überhaupt liegt.** Eine Barriere, die eine
  Rolle nicht kennt, weist sie ab; **eine Barriere, die in ihrem Baum fehlt, lässt alles durch
  und meldet nichts.** *Der erste Fall ist laut, der zweite still — und die sechste Rolle ist
  genau die, die als Einzige `docs/STATUS.md` schreiben darf.*
  **Messbar je Baum, nicht im Kopf gerechnet:** die sechs Pfade einzeln nennen, je mit Zahl.
  **⚠ BERICHTIGT vom Release-Prüfer (`d9fd6471`): es sind 3 von 6, nicht 2.** Ich hatte für
  „release" `ticket-rolle-release` gemessen — **den leeren, abgelösten Rest aus P2H-09**
  (detached HEAD, `ls-files` 0). **Sein Baum heißt `ticket-release-pruefung` und trägt das Tor.**
  *In der Sache ändert das nichts, A-37-18 bleibt richtig — aber der leere Gleichnamige ist eine
  Falle für jede nächste Messung und gehört benannt. (Beseitigen wäre eine Löschung: Yamas
  Entscheidung, nicht meine.)*

  **UND DER EIGENTLICHE BEFUND STEHT QUER ZUR ABSICHT — die Barriere wirkt verkehrt herum:**
  ```
  Tor vorhanden   generator · evaluator · release-pruefer   →  gesperrt, stehen still
  Tor fehlt       integration · planner · plan-pruefer      →  schreiben weiter
  ```
  **Nach der Zündung um 16:17 haben Planner und Plan-Prüfer `docs/STATUS.md` achtmal
  geschrieben.** *Sie umgehen nichts — das Tor liegt in ihren Bäumen gar nicht.* **Gesperrt sind
  genau die drei, die die Barriere haben und sich daran halten.**
  **Die Ausgestatteten stehen still, die Unausgestatteten schreiben weiter.** Das ist die
  Umkehrung dessen, was die Sperre bezweckt — **und derzeit der Grund, warum die Kette hängt:
  wer Zustände wechseln müsste, kann nicht; wer schneidet und prüft, kann.**
  *Ich bin dabei nicht Beobachter, sondern einer der beiden Schreibenden.*

  **SELBST NACHGEMESSEN 16.08. — Ausgangsmessung, in der Zahl überholt:**
  ```
  ticket (Integration)     0      ticket-rolle-generator    1
  ticket-rolle-planner     0      ticket-rolle-evaluator    1
  ticket-rolle-plan-pruefer 0     ticket-rolle-release      0
  ```
  **Vier Bäume ohne Tor, darunter der Integrations-Checkout und mein eigener.** Der Integrator
  hat seinen gemessen und einen Einzelfall gemeldet; **es ist die Regel, nicht die Ausnahme.**
  *Ein Befund, der als Einzelfall gemeldet wird, wird als Einzelfall behoben — deshalb steht die
  Erhebung über alle sechs hier und nicht die eine Null.*
- **A-37-17** · **ALLE SECHS KANTEN sind behandelt und JE EINZELN belegt.**
  **Messbar:** je Kante eine Rohausgabe im Bau-Bericht; `grep -c 'K6' scripts/rollen-tor.sh` ≥ 1.
  **Rot am Bau-Stand `0ee521f7`:** **K6 kommt im ganzen Skript null Mal vor**, fünf von sechs
  Kanten sind namentlich behandelt.
  **⚠ DIESES KRITERIUM HAT GEFEHLT, und das ist der Grund, warum der Bau ohne K6 grün sein
  konnte.** Sechzehn Kriterien, und **keines** nannte die Kanten. **Eine Kantenliste ohne
  Kriterium ist eine Empfehlung** — A-36 hatte dafür ein eigenes (`A-36-4`), A-37 nicht.
  *Gefunden vom Plan-Prüfer, nachdem der Bau schon lief.*
- **A-37-16** · **Die Marke wird auch GESCHRIEBEN, nicht nur gelesen.**
  **Messbar:** ein Skript oder eine dokumentierte Zeile erzeugt sie nach `npm ci`; nach einem
  Lauf existiert sie und trägt den Hash des Lockfiles **dieses** Baumes.
  **Rot:** heute schreibt sie **niemand** — npm legt nur `.package-lock.json` an, und das ist eine
  andere Datei *(404 gegen 466 Pakete)*. **Ohne diesen Punkt fordert A-37-12 eine Datei, die nie
  entsteht** — gefunden in DoR Runde 3.
  **Warum der dritte Fall der wichtigste ist, mit Yamas Begründung, die ich nicht hatte:**
  **`npm ci` löscht `node_modules` als erstes und legt es neu an.** Wird der Lauf unterbrochen,
  steht dort ein **halbes** Verzeichnis **ohne** Marke. **Ein Tor, das „keine Marke" als
  „vermutlich in Ordnung" liest, meldet den halb installierten Baum grün.** Deshalb ist es ein
  **eigenes** Kriterium und kein Nebensatz im Vergleich — *„der Unterschied zwischen einer Prüfung
  und einer Prüfung, die man beim Nachbessern versehentlich entfernt."*
- **A-37-10** · **Kein Nicht-Ziel berührt.** Die Prüfung läuft über die Ausgabe von `git show --stat`; **verboten sind Dateien unter** `resources/`,
  `app/`, kein `node_modules`, **nicht `docs/STATUS.md`**. **`docs/` im Übrigen ist ERLAUBT** — dieser Auftrag schreibt Blätter. *(Ausdrücklich getrennt, weil eine Prüfung sonst „keine Datei unter docs/" liest und den eigenen Liefergegenstand meldet.)*
- **A-37-11** · **Suite grün und Zahl unverändert GEGEN DEN BAU-STAND**, `tsc exit=0`.
  **Messbar:** Zahl **unmittelbar vor dem Bau** im Integrations-Checkout erheben und im Bericht
  nennen; nach dem Bau muss sie **gleich** sein. **Nicht** gegen eine feste Zahl prüfen.
  *(Berichtigt 15.08. nach DoR-Restpunkt 2: das Kriterium nannte 1750 vom Stand `bc2125d9`;
  seit A-35 sind es 1763, weil dieser Bau dreizehn Zusagen gebracht hat. **Wer die feste Zahl
  wörtlich misst, meldet eine Abweichung, die keine ist** — und in vier Wochen erst recht.
  Eine Zahl, die an einem alten Stand klebt, misst die Zeit, nicht den Bau.)*

## ⚠ Teil 5 fügt eine VIERTE Fehlerursache hinzu, die keinen eigenen Code bekommt

**Befund des Plan-Prüfers, zutreffend und zum Titel des Blattes im Widerspruch:** der Titel sagt
**drei** unterscheidbare Fehlerursachen. **Teil 5 fügt eine vierte hinzu — und sie bekommt keinen
eigenen Rückgabewert.**

> **Zusammen mit `A-37-20` ist das der ganze Befund:** *drei Ursachen sind benannt, eine vierte
> kam dazu, und im Bau enden alle vier bei `exit 1`.* **Die Codetabelle beschreibt einen Zustand,
> den es im Code nicht gibt.**

**Verlangt: entweder bekommt die vierte Ursache einen eigenen Code — dann trägt der Titel „vier"
— oder sie wird einer der drei zugeordnet.** *Ein Blatt, dessen Titel eine Zahl nennt und dessen
Scope eine andere baut, ist an genau dieser Stelle nicht abnehmbar.*

## Offene Befunde des Plan-Prüfers — Stand 16.08. abends, einzeln benannt

**Vier seiner zehn Befunde sind mit `A-37-18`, `A-37-19` und `A-37-20` in Kriterien überführt.
Die übrigen sechs stehen hier, weil sie den Zuschnitt betreffen und nicht ein einzelnes
Kriterium — sie gehören in die nächste DoR-Runde, nicht in eine stille Textänderung:**

```
1  A-37-5 ist am gebauten Stand NICHT ERFUELLBAR — meine Berichtigung hat den
   Widerspruch VERSCHOBEN, nicht behoben. Er hat es danach selbst berichtigt:
   der Generator hat recht, und der Unterschied ist nicht akademisch.
   -> DoR-Runde, nicht Textaenderung: es geht um den Zuschnitt von Teil 1.

2  A-37-2 setzt eine Bedingung voraus, die es nicht NENNT.
   -> Kriterium muss die Bedingung tragen oder auf sie verzichten.

3  A-37-6 ist gebaut mit einer offengelegten Abweichung, die RICHTIG ist und
   im Kriterium FEHLT. "Das ist die dritte dieser Art."
   -> das Kriterium muss der richtigen Abweichung folgen, nicht umgekehrt.

4  A-37 steht auf BEREIT mit einem ZURUECKGENOMMENEN Votum als Beleg.
   -> Zustandsfrage, gehoert dem Plan-Pruefer und Yama, nicht mir.

5  Zwei Planner-Commits entstanden AUSSERHALB des Planner-Zweigs — der Fall,
   den A-37 verhindern soll. Praezisiert: es sind DREI, sein eigener Filter
   hatte zwei versteckt.
   -> als basis_sha_lage in W-17/1 bereits vermerkt; hier als Beleg gefuehrt.

6  Von den fuenf Restpunkten der Runde 3 ist EINER offen, "und es ist eine
   einzige Zahl".
   -> die Zahl gehoert benannt, bevor die naechste Runde laeuft.
```

> **Ich trage sie hier ein und behebe sie NICHT im Text.** Vier davon ändern den Zuschnitt oder
> den Zustand des Auftrags — **das ist die DoR, und die gehört dem Plan-Prüfer.** *Ein Planner,
> der Zuschnittbefunde still in sein eigenes Blatt einarbeitet, entzieht sie der Prüfung.*

## Rückweg und Entdeckung

- **Rückweg:** ein neues Skript und ein Aufruf. **Rücknahme = Commit zurückdrehen.** Bis das Tor
  eingebunden ist, ändert sich nichts; danach ist der schlimmste Fall, dass es zu viel sperrt —
  und das fällt sofort auf, weil dann niemand mehr committen kann.
- **Entdeckung:** A-37-2 und A-37-7 sind die Positivproben. Meldet das Tor gar nichts, sind sie
  grün und trotzdem wertlos — deshalb verlangen A-37-3/4/5/6 **Rohausgaben mit einem Rückgabewert
  ≠ 0**, und zwar dem **für den Fall festgelegten**: A-37-3/4/6 → **1**, A-37-5 → **5**.
  *(Berichtigt 16.08.: hier stand pauschal „exit 1" und widersprach damit A-37-5. Ein Blatt, das
  sich an zwei Stellen selbst widerspricht, lässt den Bauenden wählen — und genau das verbietet es
  an anderer Stelle.)*
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

## SCHRITT I — unabhängige Prüfung der Sperrfälle (Evaluator, 16.08.)

```yaml
ergebnis: BESTANDEN
gemessen_am: 97f1dd00
grundlage: "Yamas Entscheidung 'dann bitte weg 1 machen', zugestellt vom Release-Pruefer (7d1cba86).
  Er hat mir ausdruecklich NUR den Umfang genannt und NICHT sein Ergebnis — deshalb ist dies eine
  unabhaengige Messung und keine Nachpruefung seiner Zahlen."

VORBEDINGUNG SELBST GEMESSEN, nicht aus dem Blatt uebernommen: |
  Commits mit Rollenmarke 'integrator': 3   (die Zuendbedingung)
  Tor liegt in 6 von 6 Zweigen              (A-37-18 erfuellt)
  -> die Sperre ist SCHARF. Ohne diese zwei Zahlen misst Schritt I nichts.

EIGENER MESSFEHLER, vor der Meldung gefunden: |
  Meine ersten elf Laeufe liefen im PRUEFSTAND-Worktree. Dort greift bei jeder Rolle K3
  ('hat noch keinen eigenen Baum'), weil das Verzeichnis keiner Rolle entspricht — die
  NEGATIVEN Faelle waren damit wertlos, sie zeigten die Baum-Kante statt der Pfad-Sperre.
  Neu gefahren in den ECHTEN Baeumen je Rolle. Die positiven Faelle trugen auch vorher,
  aber ein halb richtiger Aufbau ist kein Beleg.
```

### Positiv — die fünf Rollen an `docs/STATUS.md`, **jede in ihrem eigenen Baum**

| Rolle | Baum | exit | Meldung |
|---|---|---|---|
| planner | `ticket-rolle-planner` | **1** | `VERSTOSS … aendert docs/STATUS.md ausserhalb des Integrations-Checkouts` |
| plan-pruefer | `ticket-rolle-plan-pruefer` | **1** | dito |
| generator | `ticket-rolle-generator` | **1** | dito |
| evaluator | `ticket-rolle-evaluator` | **1** | dito |
| release-pruefer | `ticket-release-pruefung` | **1** | dito |

**Fünf von fünf gesperrt.** Die Meldung nennt beide Werte (erwartet/gefunden) und den Grund: *„Die Statuswahrheit hat EINEN Schreiber: den Integrator."*

### Negativ — muss durchlassen

| Fall | exit | Ergebnis |
|---|---|---|
| **Integrator** an `docs/STATUS.md`, im Integrations-Checkout | **0** | durchgelassen, nur Modulstand-Hinweis |
| planner · plan-pruefer · generator · evaluator · release-pruefer bei **anderen** Dateien, je im eigenen Baum | **0** | alle fünf durchgelassen |

**Sechs von sechs durchgelassen.** Die Sperre trennt also nach dem **Pfad**, nicht nach der Rolle allein.

### Die sechs Kanten — je einzeln gefahren, nicht am Buchstaben gezählt

| | Probe | Ergebnis |
|---|---|---|
| **K1** | `TICKET_ROLLE=plan-pruefer-2` im Plan-Prüfer-Baum | exit **0**, kein Verstoß — Instanzsuffix korrekt auf den Rollenstamm zurückgeführt |
| **K2** | Release-Prüfer in `ticket-release-pruefung`, Tabelle nennt `ticket-rolle-release` | exit **0** mit eigener Meldung: *„auf ihrem Zweig, aber in einem anderen Verzeichnis … Durchgelassen: der Zweig ist eindeutig"* — die Namensfalle ist **benannt**, nicht übersehen |
| **K3** | `planner` im Prüfstand ohne eigenen Baum | exit **0**, *„hat noch keinen eigenen Baum — durchgelassen"* |
| **K4** | Tor in einem Verzeichnis **ohne** Git-Repo | exit **2**, *„kein Git-Repository … (K4)"* — eigene Ursache, **kein** Rollenfehler, eigener Rückgabewert |
| **K5** | `integrator` im gemeinsamen Checkout | exit **0**, kein Verstoß — das ist sein Baum |
| **K6** | `evaluator` im gemeinsamen Checkout, eigener Baum steht | exit **0** mit Hinweis auf den wartenden Baum |

**Sechs von sechs behandelt**, jede mit eigener Meldung und eigenem Rückgabewert. K4 ist die einzige mit `2` — kein Wert trägt zwei Bedeutungen.

### Zwei Fallen, die der Release-Prüfer genannt hat — beide geprüft

- **Ein Zustands-Commit trägt genau EINE Kennung:** das Muster in `status-erzeugen.sh` erlaubt genau eine `<kennung>`-Gruppe je Betreff; zwei Kennungen erzeugen keinen zweiten Treffer.
- **Das Ballfeld verlangt eine Rolle, keinen Gedankenstrich:** in meinen eigenen zwei Voten heute (`A-41`, `W-17/1`) steht `ballbesitz: release-pruefer`, kein `—`.

**Schritt I ist bestanden.** Damit ist die Bedingung für `SCHREIBEND` aus meiner Sicht erfüllt; die Erteilung selbst gehört nicht mir.
