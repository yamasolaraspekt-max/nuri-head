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
- **⚠ ÜBERHOLT am 21.08.2026 durch Yamas Entscheidung (Weg a) — das Nicht-Ziel bleibt als Beleg
  stehen und gilt nicht mehr.** Der neue Stand ist **`A-37-25`**: der versionierte `pre-commit`-Hook
  gehört in **diesen** Auftrag.

  **Yamas Wortlaut**, weitergereicht vom Dirigenten *(`ereignisse/SPEZ-planner-A-37/dirigent-antwort-nichtziel-kein-hook.yaml`, 00:15)*:

  > **„Wiederaufnahme des Generators erst, wenn A-37 eine wirksame `pre-commit`-Barriere enthält und
  > deren Negativprobe belegt"** — und **„A-37 einschließlich wirksamem `pre-commit`-Hook"**.

  **Der Anlass, gemessen und nicht behauptet:** sechs Generator-Commits entstanden direkt im
  gemeinsamen Checkout. **Das Tor wirkt heute ausschließlich in `commit-pruefen.sh`** — also nur,
  wenn jemand es *aufruft*. **Ein nacktes `git commit` erreicht es nie.** Belege siehe `A-37-25`.
  Quellen des Wortlauts: `docs/auftraege/ABSCHLUSSMODUS-2026-08-21.md` @ `rolle/dirigent`, Abschnitt
  *„Yama-Entscheidung … Weg (a)"*, und `/Users/yamanuri/.ticket-steuerung/README.md`.

  *(Der bisherige Wortlaut bleibt als Beleg stehen — A-20-4:)*

  > ~~- **Kein Hook.** Der versionierte `pre-commit`-Hook ist ein eigener Auftrag — dieses Tor läuft
  > weiter über den ausdrücklichen Aufruf.~~ **Der Satz war richtig, solange niemand am Tor vorbei
  > committete.** Er ist nicht falsch geschrieben, sondern **abgelaufen** — dieselbe Klasse wie die
  > feste Suite-Zahl in `A-37-11` und die Zeilennummer in `A-37-19`.

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
- **A-37-5** · **Negativfall fehlende Kennung:** `TICKET_ROLLE` leer → **exit 3**.
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
  **Das Tor selbst ist an dieser Stelle vorbildlich:** es trennt vier Lagen — heil, YAML-Syntax,
  Modulauflösung, Laufzeit — und meldet den Modulfehler **als solchen** statt als Kopf-Fehler.
  *Der Mangel liegt nicht im Tor, sondern in der Deklaration, an der es hängt.*
- **A-37-18** · **DAS TOR MUSS IN JEDEM ROLLENBAUM DER LISTE VORHANDEN SEIN.**
  `git ls-files scripts/rollen-tor.sh` ergibt in **jedem** Baum der Liste aus **`A-37-22`** genau **1**.

  **⚠ BERICHTIGT am 22.08.2026 — die Zahl „sechs" ist abgelaufen, und zwar genau so, wie dieses
  Blatt es an zwei anderen Stellen selbst verbietet.** Hier stand *„IN ALLEN SECHS BÄUMEN"* und
  *„jedem der sechs Arbeitsbäume"*. **Gemessen am 22.08. sind es sieben** — `ticket-rolle-dirigent`
  ist nach dem Schnitt dazugekommen. **Ein Kriterium, das eine Zahl nennt, misst ab dem nächsten
  Rollenzugang die Zeit statt den Bau** — wörtlich der Befund, mit dem `A-37-11` schon einmal von
  *„1750"* auf *„gegen den Bau-Stand"* umgestellt wurde, und die Mahnung aus `A-37-19`
  (*„diese Nummer gehört mitgemessen, nicht zitiert"*). **Deshalb trägt das Kriterium jetzt keine
  Zahl mehr, sondern verweist auf die eine Liste, die auch `rueckweg.py` und das Tor binden.**
  *Wächst die Liste, wächst dieses Kriterium mit — ohne dass jemand es anfassen muss.*
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

---

## ERWEITERUNG 22.08.2026 — A-37-22 bis A-37-27

**Herkunft:** Yamas Vorgaben, zugestellt über `rollen/planner.yaml` gen 6
(`SPEZ-planner-A-37`, Digest `a438e052…`). **Jeder „Rot"-Beleg unten ist am 22.08. im Worktree
`ticket-rolle-planner` auf `1cd33614` einmal gefahren worden** — kein Wert stammt aus einer Notiz.
*(Regel „Zustand messen vor Vorlage": eine Zahl ohne Erhebungsbefehl ist keine Zahl.)*

**Warum die Rot-Belege dreierlei Art sind und getrennt bleiben müssen:**

| Art | was der Beleg zeigt | Beispiel hier |
|---|---|---|
| **Produkt** | die gebaute Sache fehlt | `A-37-25`: `.githooks/pre-commit` existiert nicht |
| **Vergleich** | zwei Quellen sagen Verschiedenes | `A-37-27`: `package.json` nennt `js-yaml`, das Lockfile nicht |
| **Schutz** | die Barriere greift an dieser Stelle nicht | `A-37-24`: `BEFUNDNOTIZEN.md` kommt in beiden Toren 0× vor |

*Ein Produkt-Rot verschwindet, sobald jemand eine Datei anlegt. Ein Schutz-Rot verschwindet erst,
wenn die Barriere den Fall auch wirklich abweist — deshalb verlangen die Schutz-Kriterien unten
ausnahmslos eine **ausgelöste** Negativprobe mit Rohausgabe und `echo $?`, nicht einen Grep-Treffer.*

- **A-37-22** · **`scripts/rueckweg.py` KENNT DIE ROLLENBÄUME ÜBER `(Pfad, Zweig)`-PAARE, NICHT ÜBER NAMEN.**

  **Verlangt:** eine feste Liste von Paaren aus **Pfad** und **erwartetem Zweig**. Vor jedem Merge
  wird der tatsächlich ausgecheckte Zweig gegen den erwarteten geprüft; **weicht er ab, wird der Baum
  übersprungen und gemeldet, nie gemergt.** Namensgleiche außerhalb der Liste werden **gemeldet und
  ausgeschlossen**. `ticket-release-pruefung` steht als **vollwertiger Eintrag** in der Liste,
  `ticket-rolle-dirigent` kommt dazu.

  **Messbefehle und ihr heutiges (rotes) Ergebnis:**
  ```
  sed -n '75,81p' scripts/rueckweg.py                       -> BAEUME = 5  (ticket, planner,
                                                               plan-pruefer, generator, evaluator)
  grep -cE 'branch|symbolic-ref|abbrev-ref' scripts/rueckweg.py  -> 0
  grep -n 'release' scripts/rueckweg.py                     -> genau 1 Treffer, Zeile 118
  ```
  **Drei getrennte Mängel, jeder für sich ausreichend:**
  1. **Auswahl über den Namen** — `:128 pfad = f'{WURZEL}/{name}'`. Es gibt keine Pfad-Zweig-Bindung.
  2. **Der Zweig wird nirgends geprüft.** `lage()` (`:91-113`) liest `HEAD`, `status --porcelain`
     und `rev-list --count` — **nie**, auf welchem Zweig der Baum steht. *Ein Baum auf fremdem Zweig
     wird `--ff-only` nachgezogen, solange der Merge technisch durchgeht.*
  3. **`ticket-release-pruefung` kommt genau einmal vor: bei `:118` als Quelle des ZIEL-SHA**
     (`rev-parse fork/auto/hausplaner-integration`) — und steht **nicht** in `BAEUME`.
     **Das ist schärfer als „Sonderfall": der Baum, der das Ziel definiert, wird selbst nie
     nachgezogen.** Fällt er aus, ist das Ziel unlesbar und `main()` gibt `2` zurück — die ganze
     Kette hängt an einem Baum, den die eigene Liste nicht führt.

  **Warum eine längere Liste NICHT genügt** *(Befund der Vorgängersitzung `20c9c319`, 23:32,
  am 22.08. nachgemessen)*: unter `~/Documents` tragen **15** Bäume ein `ticket`-Präfix, aber nur
  **7** sind Rollenbäume. Die Regel `ticket-rolle-*` liefert **6** — davon ist einer
  (`ticket-rolle-release`, detached `4630d658`) der tote Rest aus `P2H-09`, während der lebende
  `ticket-release-pruefung` fehlt. **Ein weiterer Gleichnamiger liegt im Scratchpad einer fremden
  Sitzung** (`…/303cefb6-…/scratchpad/ticket-rolle-generator`, detached `f374c73a`) und steht in
  `git worktree list` des gemeinsamen Repos. *Wer über den Namen sucht, kann ihn erwischen und misst
  dann an `f374c73a` statt an `abd1719c`.*
  **Erhebungsbefehl:** `git worktree list --porcelain` im Integrations-Checkout, ausgewertet nach
  `worktree`/`branch`/`detached`.

  **Absage-Regel:** Findet der Bauende einen Baum der Liste **nicht** vor, wird er **gemeldet und
  übersprungen** — der Lauf endet mit dem Code für *nicht messbar* (`2`), **nie** mit `0`.
  Das ist die Zusage, die im Kopf der Datei bereits steht (*„eine ausgefallene Messung ist KEIN
  Ergebnis"*); das Kriterium dehnt sie von den drei Vorbedingungen auf die **Baumauswahl** aus.

- **A-37-23** · **DAS ROLLEN-TOR KENNT DEN `dirigent` — MIT TECHNISCH BEGRENZTEM SCHREIBBEREICH.**

  **Verlangt:** siebter Eintrag `dirigent` → Verzeichnis `ticket-rolle-dirigent`, Zweig
  `rolle/dirigent`, in **derselben** Zuordnungsform wie die sechs davor (`:205-210`). **Dazu eine
  Pfadgrenze, die das Tor durchsetzt und nicht nur beschreibt:** erlaubt sind `docs/konzept/`,
  `docs/regelwerk/` und Steuerungsblätter unter `docs/auftraege/`; **abgewiesen** werden Produktcode
  (`app/`, `resources/`), `docs/STATUS.md` und `docs/BEFUNDNOTIZEN.md`.

  **Messbefehle und ihr heutiges (rotes) Ergebnis:**
  ```
  grep -c 'dirigent' scripts/rollen-tor.sh          -> 0     (auf rolle/planner UND in der Integration)
  grep -c 'rolle/dirigent' scripts/rollen-tor.sh    -> 0
  ```
  **Rot ist hier ein Schutz-Rot:** das Tor kennt die Rolle nicht, weist sie also mit *„unbekannte
  Rollenkennung"* ab — **der Dirigent kann in seinem eigenen Baum nicht committen**, und zugleich
  gibt es **keine** Grenze, die ihn von `docs/STATUS.md` fernhält, sobald er bekannt ist.
  *Beide Hälften gehören in ein Kriterium: eine Rolle bekannt machen, ohne ihren Bereich zu
  begrenzen, tauscht eine Sperre gegen ein Loch.*

  **⚠ ZUM VORGRIFF `5c9afbc7` — ENTSCHIEDEN: ÜBERNEHMEN, NICHT ERSETZEN.** *(Yamas Vorgabe stellte
  beides frei; die Entscheidung ist begründet, nicht gewählt.)*
  **Gemessen, und das ist der Grund:**
  ```
  git merge-base --is-ancestor 5c9afbc7 HEAD                          -> NEIN
  git merge-base --is-ancestor 5c9afbc7 auto/hausplaner-integration   -> NEIN
  git branch -a --contains 5c9afbc7                                   -> nur rolle/dirigent
  ```
  **Der Vorgriff existiert ausschließlich auf `rolle/dirigent` und ist in keinem Baum sichtbar, in
  dem gebaut wird.** Sein Inhalt ist inhaltlich richtig und additiv — er setzt
  `dirigent) SOLL_VERZ="ticket-rolle-dirigent"; SOLL_ZWEIG="rolle/dirigent" ;;` in K2-Form und
  ergänzt die `Bekannt:`-Zeile um `dirigent`.
  **Die Gefahr ist nicht der Inhalt, sondern die Doppelung:** baut der Generator denselben Eintrag
  in seinem Baum neu, entstehen **zwei Fassungen derselben `case`-Zeile und derselben
  `Bekannt:`-Zeile**, die beim Integrieren kollidieren.
  **Verlangt deshalb ausdrücklich:** der Bauende prüft **vor** dem Schreiben mit
  `git merge-base --is-ancestor 5c9afbc7 HEAD`, ob der Vorgriff bereits in seinem Stand liegt.
  **Liegt er:** übernehmen, nicht neu schreiben. **Liegt er nicht:** den Eintrag **wortgleich zu
  `5c9afbc7`** setzen, damit der spätere Zusammenlauf keinen Konflikt erzeugt.
  **Belegt durch:** `git show 5c9afbc7 -- scripts/rollen-tor.sh` im Bau-Bericht, und nach dem Bau
  `grep -c 'dirigent)' scripts/rollen-tor.sh` → **genau 1**, nicht 2.

  **Nebenbefund, gefunden beim Messen dieses Kriteriums und hier benannt statt still behoben:**
  die Tabelle im Tor führt bei `:210` weiterhin `release-pruefer → ticket-rolle-release`, während
  der lebende Baum `ticket-release-pruefung` heißt. Heute fängt die **K2**-Kante das über den Zweig
  ab (Evaluator-Schritt I: exit `0` mit eigener Meldung). *Es ist dieselbe Namensfalle wie in
  `A-37-22` — sie gehört in dieselbe Liste, sobald `A-37-22` gebaut ist, und ausdrücklich **nicht**
  in eine stille Textänderung hier.*

- **A-37-24** · **`docs/BEFUNDNOTIZEN.md` STEHT UNTER DERSELBEN SCHREIBBARRIERE WIE `docs/STATUS.md`.**

  **Verlangt:** Beide Tore behandeln `docs/BEFUNDNOTIZEN.md` **gleich** wie `docs/STATUS.md` —
  Schreiben nur durch den **Integrator** im Integrations-Checkout; jede andere Rolle wird abgewiesen.

  **Messbefehle und ihr heutiges (rotes) Ergebnis** *(Messbeleg des Evaluators `3f7f61d6`, am 22.08.
  nachgefahren — beide Zahlen bestätigt)*:
  ```
  grep -c 'BEFUNDNOTIZEN' scripts/rollen-tor.sh      -> 0        gegen  'STATUS.md' -> 8
  grep -c 'BEFUNDNOTIZEN' scripts/commit-pruefen.sh  -> 0        gegen  'STATUS.md' -> 9
  ```
  **Das ist ein Vergleichs-Rot und es wiegt schwerer als eine fehlende Zeile:** seit `A-42`
  (Bau `26c46f31`) liegen **172 Befundblöcke** in `docs/BEFUNDNOTIZEN.md`, die vorher in
  `docs/STATUS.md` standen und **dort geschützt waren**. *Der Umzug hat den Inhalt bewegt und den
  Schutz zurückgelassen* — **jede Rolle kann die Datei heute aus jedem Baum schreiben.**

  **Negativ- und Positivprobe, beide ausgelöst:** `docs/BEFUNDNOTIZEN.md` in der Pfadliste aus
  `ticket-rolle-planner` → `KEIN COMMIT`, Rückgabe ≠ 0, Rohausgabe im Bericht; dieselbe Datei als
  `integrator` im Integrations-Checkout → exit `0`.
  **Absage-Regel:** Greift die Sperre auch nur für **eine** der Rollen nicht, ist das Kriterium
  **nicht** erfüllt — ein Schutz, der fünf von sechs Fällen trifft, ist im sechsten wirkungslos.

  **Vorratsposten, ausdrücklich NICHT Teil dieses Kriteriums** *(Evaluator, ohne Wertung)*:
  `a26-ball-drift`, `a30-datensatz-paar`, `a33-kennungen-nachziehen` und `status-erzeugen` kennen
  `BEFUNDNOTIZEN` **0×**. **Heute wirkungslos, gemessen:** alle vier arbeiten auf
  Auftragsdatensätzen, und die sind vollständig in `docs/STATUS.md` geblieben (**104 = 104** vor wie
  nach dem Umzug). *Die Lücke wirkt erst, wenn dort ein Feld mit `zustand` oder `ballbesitz` landet.*
  **Als Vorrat notiert, damit der nächste Umzug ihn nicht neu findet — kein Bauauftrag.**

- **A-37-25** · **EIN WIRKSAMES `pre-commit`-TOR — DER NACKTE `git commit` MUSS SCHEITERN.**

  **Dieses Kriterium hebt das Nicht-Ziel „Kein Hook" auf** (siehe dort: als ÜBERHOLT gekennzeichnet,
  Yamas Entscheidung Weg a vom 21.08.). *Ohne diese Kennzeichnung widerspräche das Blatt sich selbst
  — genau der Fehler, den es unter „Rückweg und Entdeckung" für `exit 1`/`exit 5` schon einmal
  behoben hat.*

  **Messbefehle und ihr heutiges (rotes) Ergebnis:**
  ```
  ls .githooks/                      -> commit-msg  post-commit        (kein pre-commit)
  test -x .githooks/pre-commit; echo $?   -> 1
  git config core.hooksPath          -> .githooks                      (der Pfad ist gesetzt!)
  sed -n '84p' .githooks/commit-msg  -> [ "$ist_merge" = "1" ] || exit 0
  ```
  **Die vierte Zeile ist der eigentliche Befund und sie ist ein Schutz-Rot:** `core.hooksPath` ist
  **gesetzt**, es gibt also einen wirksamen Hook-Pfad — **aber `commit-msg` steigt bei allem, was
  kein Merge ist, sofort mit `exit 0` aus.** *Der Hook läuft, und er lässt jeden normalen Commit
  durch.* **Die gesamte Rollenbindung hängt heute daran, dass jemand `commit-pruefen.sh` freiwillig
  aufruft.** Genau daran ist sie nachweislich gescheitert: sechs Generator-Commits im gemeinsamen
  Checkout, und der Stopp per `SIGSTOP` war die einzige verbliebene Abhilfe.

  **Verlangte Negativproben — jede tatsächlich ausgelöst, mit Rohausgabe und `echo $?`:**

  | # | Lage | verlangt |
  |---|---|---|
  | a | nackter `git commit` im **falschen** Worktree | **abgewiesen**, Meldung nennt erwarteten und gefundenen Baum |
  | b | `TICKET_ROLLE` **nicht gesetzt** | **abgewiesen**, **exit 5** *(der für diesen Fall festgelegte Code, `A-37-5`)* |
  | c | richtiger Baum, **falscher Zweig** ausgecheckt | **abgewiesen** |
  | d | `integrator` im Integrations-Checkout, begrenzter Integrationscommit | **durchgelassen**, exit `0` |
  | e | Merge-Commit | der vorhandene `commit-msg`-Hook bleibt **unverändert wirksam** |

  **Positivprobe nicht vergessen:** die richtige Rolle im richtigen Baum committet normal weiter.
  *Ein Tor, das nur sperrt, ist von einem kaputten nicht zu unterscheiden — dieselbe Begründung wie
  bei `A-37-2`.*

  **⚠ SITZUNGSIDENTITÄT — BERICHTIGT am 22.08. nach DoR-Restpunkt 1 (`plan-pruefer-NICHT_ERTEILT.yaml`,
  Votum `1568610f`/`c74a9141`). Meine erste Fassung war gegen den alten Fehler richtig und gegen den
  neuen falsch, und sie hätte gebaut Schaden angerichtet.**

  *(Die überholte Fassung bleibt als Beleg stehen — A-20-4:)*

  > ~~Sitzungsidentität — Befund `79285cf2`, in ein Kriterium überführt: wo der Hook eine Sitzung
  > identifiziert, besteht die Kennung aus **Sitzungs-ID + PID des Sitzungsprozesses + Prozess-
  > Startkennung**, **nie** aus der Shell-PID einer Werkzeugrunde. **Gemessen:** von vier `pid`-Feldern
  > im Steuerungssystem trugen **drei** eine Zahl, zu der kein Prozess mehr existierte; die Shell-PID
  > einer einzigen Sitzung wechselte in vier aufeinanderfolgenden Aufrufen `76231 → 80694 → 80830`,
  > während der Sitzungsprozess konstant blieb. *Ein Tor, das Lebendigkeit über die Shell-PID prüft,
  > prüft bei drei von vier Rollen eine tote Zahl.*~~
  >
  > **⚠ BERICHTIGUNG AM ZITAT SELBST (22.08., Anmerkung 2 des DoR-Votums `a248eaaf`):** meine erste
  > Fassung dieses Blocks brach das Zitat nach *„Werkzeugrunde"* ab — **drei von sieben Zeilen**,
  > während drei Zusagen im selben Commit *„null Löschungen"* und *„die überholte Fassung bleibt
  > vollständig stehen"* behaupteten. **Verschwunden waren genau die Messwerte**, die den Befund
  > tragen (`76231 → 80694 → 80830`, *„von vier `pid`-Feldern … trugen drei"*, *„bei drei von vier
  > Rollen eine tote Zahl"*) — der Satz *„der Befund bleibt richtig"* stand damit zwei Zeilen weiter
  > **ohne seinen Nachweis**. Oben vollständig wiederhergestellt. *Ein gekürzter Beleg ist die
  > stillste Art, eine Aussage unbelegt zu machen — und die Kürzung stand ausgerechnet unter der
  > Zusage, nichts zu löschen.*
  >
  > **Der Satz wehrt die Shell-PID ab — das war der Befund aus Sitzung `79285cf2…`, und er bleibt
  > richtig.** *(Das durchgestrichene Zitat behält die alte Schreibweise wörtlich — es ist der Beleg,
  > nicht die Aussage.)* **Falsch ist, dass er PID und Startkennung zu Bestandteilen der IDENTITÄT
  > macht.** Bei einer per `--resume` getakteten Sitzung gehört die eingetragene PID **per
  > Konstruktion** einem beendeten Lauf; die Kennung ist damit nicht stabil, obwohl die Sitzung
  > durchgehend arbeitet.
  > *Der Begründungssatz „während der Sitzungsprozess konstant blieb" traf auf die messende Sitzung
  > `79285cf2…` zu und auf die Planner-Sitzung nicht.*

  **⚠ ZWEITE BERICHTIGUNG — WAS `agentenarchitektur-v2.md` §8 WIRKLICH SAGT.** *(Selbst gemessen,
  nachdem meine erste Fassung eine fremde Folgerung ungeprüft übernommen hatte; Auflage aus
  `plan-pruefer-berichtigung-paragraph8.yaml`, dessen Gegenmessung ich nachgefahren habe.)*

  **§8 trennt zwei Sperren, und die Trennung ist bereits richtig:**

  | Gegenstand | Regel in §8 | gebunden an |
  |---|---|---|
  | **Lease** (`active/`) | `:162` — darf **nur** entfernt werden, wenn `heartbeat_bis` verstrichen ist | die **Sitzung** |
  | **Vergabesperre** (`counter.lock/owner.yaml`) | `:154-156` — Entfernung nur, wenn die Prozessidentität nachweislich nicht mehr existiert | den **Lauf** |

  **Messbefehl:** `grep -n 'PID'` über §8 → **genau zwei Treffer**, `:154` und `:156`, **beide** unter
  der Absatzüberschrift *„Recovery der Sperre selbst"*. **Die Lease-Übernahme bei `:162` nennt keine
  PID.** *§8 entzieht eine Lease also nicht über die PID — für die Lease trägt es Punkt 4 und 6 der
  Zielregel bereits.*

  **Und die Bindung der Vergabesperre an den Lauf ist richtig, nicht falsch:** `counter.lock` soll
  laut `:126`/`:137`/`:148` nur den **kurzen Vergabevorgang** überdauern. **Für einen Lauf ist die
  Prozessidentität die zutreffende Kennung.** *Was für die Lease falsch wäre, ist für die
  Vergabesperre richtig.* **Auch `fail closed` (`:157-159`) greift hier nicht:** es gilt, wenn die
  Lebendigkeit *„nicht eindeutig messbar"* ist, und §8 nennt dafür zwei Fälle — **fremder Host,
  fehlende Startkennung**. Bei einer lokal zurückgebliebenen Sperre mit eingetragener Kennung
  antwortet `ps` eindeutig (**exit 1** = nachweislich nicht mehr existent, an vier beendeten Läufen
  dieser Sitzung geprüft, Verfahren an beiden Enden verifiziert) — **das ist genau die Bedingung, unter
  der `:155-156` die Entfernung erlaubt.** Kein Entzug und kein Stillstand.

  **Was hier bleibt, ist deshalb kein Mangel an §8, sondern eine Ergänzung:** **Yamas Zielregel fügt
  für die Lease hinzu, was §8 dort schon trägt, und macht es zur Abnahmebedingung** — mit dem
  ausdrücklichen Verbot (Punkt 6), aus einer alten PID auf „verwaist" zu schließen. *Genau dieser
  Schluss war der Fehler meiner ersten Fassung — nicht der von §8.*

  **DIE ZIELREGEL — Yamas Wortlaut vom 22.08., sechs Punkte, einzeln abzunehmen:**

  | # | Regel | wo abgenommen |
  |---|---|---|
  | 1 | **Stabile Identität ist allein die Sitzungs-ID.** Sie überlebt den Prozess. | **A-37-25** |
  | 2 | **`pid` und `prozess_start` gelten je LAUF**, nicht je Sitzung. | **A-37-25** |
  | 3 | Während Schreibarbeit wird der **Heartbeat** regelmäßig erneuert (atomar, tmp + `mv`). | **Z0-I2** |
  | 4 | **Übernahme nur bei abgelaufenem Heartbeat UND fehlendem aktuellem Lauf** — beides. | **Z0-I2** |
  | 5 | Das **Fencing-Token** bleibt maßgeblich. | **Z0-I2** |
  | 6 | **Eine alte PID allein erklärt eine Lease NIEMALS für verwaist.** | **A-37-25** |

  **⚠ ZUR ZUSCHNITTGRENZE, weil der Plan-Prüfer sie ausdrücklich offengelassen hat** *(„Wo die Grenze
  liegt, entscheidet der Planner")*: **Alle sechs Punkte stehen hier zusammen, weil sie eine Regel
  sind und getrennt ihren Sinn verlieren** — abgenommen werden sie an zwei Orten. **`A-37-25` baut
  ein `pre-commit`-Tor, keine Lease-Verwaltung.** Das Tor muss wissen, **woran es eine Sitzung
  erkennt** (1, 2) und **was es daraus nicht schließen darf** (6). **Heartbeat-Erneuerung, Übernahme
  und Fencing (3–5) sind Mechanik der Claim-Sperre und gehören nach `Z0-I2`** — sie hier abzunehmen
  hieße, Lease-Verwaltung in einen Commit-Hook zu bauen. *`README.md:66` weist die Barriere beiden
  Aufträgen zu; diese Tabelle sagt, welcher Punkt bei welchem liegt, damit keiner zwischen ihnen
  verschwindet.*

  **GEMESSEN AM LAUFENDEN SYSTEM — die Sitzung `ef8ec540` lief binnen 40 Minuten unter VIER Prozessen:**
  ```
  88928  Start 00:00:48  (--session-id)   Läufe 1-3     ps-exit 1   tot
  97092  Start 00:16:24  (--resume)       Messung P-P   ps-exit 1   tot
  12334  Start 00:32:38  (--resume)       Lauf 4        ps-exit 1   tot
  16345  Start 00:40:08  (--resume)       Lauf 5        ps-exit 0   lebt
  ```
  **Erhebung:** `ps -o pid=,lstart= -p <PID>`, Exit-Code **direkt** gelesen — nicht hinter einer Pipe
  *(der Plan-Prüfer hat seinen ersten Versuch genau daran verworfen)*. **Verfahren an beiden Enden
  verifiziert:** lebende PID → exit `0` mit Startzeit, `999999` → exit `1` leer.
  **Der Lebensnachweis ist der laufende Lauf:** `ps -axo command=` findet genau **einen** Prozess,
  dessen Kommandozeile die Sitzungs-ID trägt (`claude -p --resume <sitzungs-id>`).
  **Der Realfall ist protokolliert, nicht konstruiert:** um **00:17:18** wurde die Planner-Lease mit
  `owner.pid 88928` erteilt — **dieser Prozess war da bereits tot**, während die Sitzung durchgehend
  arbeitete und um 00:21:57 dieses Blatt schrieb.

  **Warum das ein eigener Absatz bleibt:** *jede* der vier PIDs war zum Zeitpunkt ihres Eintrags
  richtig. **Die Registrierung war nie falsch — sie ist abgelaufen**, dieselbe Klasse wie die feste
  Zahl in `A-37-11`, die Zeilennummer in `A-37-19`, die „sechs Bäume" in `A-37-18` und das Nicht-Ziel
  „Kein Hook". **Ein Tor, das Lebendigkeit an einer gespeicherten PID misst, prüft eine Aussage mit
  Verfallsdatum, ohne das Datum zu kennen.**

  **Negativprobe, ausgelöst:** Eintrag mit **toter** `owner.pid`, aber gültigem Heartbeat und
  laufendem `--resume`-Prozess derselben Sitzungs-ID → **die Sitzung gilt als lebend**, eine
  Übernahme wird abgewiesen. **Gegenprobe:** kein laufender Prozess mit dieser Sitzungs-ID **und**
  abgelaufener Heartbeat → Übernahme erlaubt.
  **Absage-Regel:** Ein Bau, der eine Sitzung oder Lease **allein wegen abweichender PID** für tot
  erklärt, erfüllt dieses Kriterium **nicht** — auch dann nicht, wenn alle fünf Negativproben oben
  grün sind.

  **Zum Beleg `79285cf2` — Restpunkt 2 der DoR, berichtigt:** die Zeichenfolge steht in diesem Blatt
  neben echten Commit-SHAs (`26c46f31`, `e5aa5af7`, `e9e6ee5b`) und liest sich wie einer.
  **Sie ist keiner:** `git cat-file -e 79285cf2^{commit}` → **exit 128**. Es ist die **Sitzungs-ID**
  `79285cf2-4231-4f71-8dfc-3306e3371109` (Sitzungsprozess-PID 70499), Quelle
  `ereignisse/SITZUNG-70499-ROLLENWECHSEL/sitzung-70499-meldung.yaml`. **Ab hier im Blatt immer als
  „Sitzung `79285cf2…`" geschrieben.** *Eine gekürzte UUID und ein gekürzter SHA sehen gleich aus —
  und ein Beleg, den man nicht öffnen kann, ist die stille Variante eines toten Verweises.*

  **⚠ `--no-verify` IST DIE GRENZE UND WIRD ALS SOLCHE DOKUMENTIERT, NICHT ÜBERSPIELT.**
  Ein Git-Hook ist mit `git commit --no-verify` umgehbar; **das ist technisch nicht verhinderbar.**
  **Verlangt ist deshalb ein ausdrücklicher Absatz im Bau-Bericht und im Skriptkopf**, der sagt:
  *diese Barriere ist gegen Gewohnheit und Versehen wirksam, nicht gegen Absicht.*
  **Absage-Regel:** Wer `--no-verify` als „gelöst" meldet, hat das Kriterium **nicht** erfüllt —
  eine benannte Grenze ist der Liefergegenstand, eine behauptete Dichtheit ein Mangel.

- **A-37-26** · **EIN ZUSTANDSCOMMIT, DEN DIE ERZEUGUNG NICHT ERKENNT, DARF NICHT DURCHGEHEN.**

  **Verlangt:** Trägt ein Commit-Betreff `zustand:`, so wird er gegen das Muster aus
  `scripts/status-erzeugen.sh` geprüft. **Passt er nicht — insbesondere bei mehr als einer Kennung
  oder mehr als einem Bau-SHA —, wird der Commit abgewiesen**, mit Nennung des erwarteten Wortlauts.

  **Messbefehl und heutiges (rotes) Ergebnis** — der Fall ist real und liegt im Bestand:
  ```
  git log -1 --format=%s e9e6ee5b
    -> generator: zustand: A-38 · A-42 · CODE_FERTIG · evaluator · bau 0f731c22 26c46f31 — …
  ```
  **Gemessen gegen `WORTLAUT` aus `scripts/status-erzeugen.sh:195`** (das Muster erlaubt bei `:190`
  **genau eine** `kennung`-Gruppe): **NICHT ERKANNT.** Gegenprobe mit einer künstlichen Ein-Kennungs-
  Fassung: **ERKANNT**. *Das Muster arbeitet korrekt — der Betreff passt nicht.*

  **Warum dieser Fall der gefährlichste der drei ist** *(Befund der Vorgängersitzung, 23:22)*:
  `e9e6ee5b` trug **drei** unabhängige Mängel — der Commit war **leer**, der Betreff nannte **zwei
  Kennungen**, und er nannte **zwei Bau-SHAs**. **Ein leerer Commit fällt auf.** **Ein Betreff, den
  das Muster nicht erkennt, fällt nicht auf: er sieht richtig aus und wird still übergangen.**
  Folge im Bestand: **weder `A-38` noch `A-42` bekamen aus diesem Commit einen Zustandswechsel**,
  und `docs/STATUS.md` führte `A-42` weiter auf `BEREIT/generator`, obwohl der Bau seit Stunden lag.

  **Negativprobe (ausgelöst):** Betreff mit zwei Kennungen → **abgewiesen**.
  **Positivprobe:** Betreff mit einer Kennung und einem Bau-SHA → **durchgelassen**.
  **Absage-Regel:** Das Kriterium ist **nicht** über einen Grep auf das Wort `zustand:` erfüllbar —
  verlangt ist die Prüfung gegen **dasselbe** Muster, das `status-erzeugen.sh` benutzt. *Zwei
  Muster für dieselbe Frage sind eine zweite Wahrheit und driften auseinander.*

- **A-37-27** · **`js-yaml` MUSS AUCH IM LOCKFILE ALS WURZEL-ABHÄNGIGKEIT STEHEN.**

  **Dies ist der Rest von `A-37-21`, und er ist heute halb erfüllt** — deshalb ein eigenes Kriterium
  statt einer stillen Erweiterung des alten. *(`A-37-21` verlangt wörtlich „als direkte
  `dependency` deklariert"; das ist teilweise geschehen, und die Lücke sitzt an einer anderen Stelle
  als beim Schreiben von `A-37-21` vermutet.)*

  **Messbefehle und ihr heutiges Ergebnis — ein Vergleichs-Rot:**
  ```
  package.json      dependencies['js-yaml']      -> FEHLT
  package.json      devDependencies['js-yaml']   -> ^4.1.0        (gesetzt in e5aa5af7)
  package-lock.json packages[""].dependencies    -> js-yaml FEHLT
  package-lock.json packages[""].devDependencies -> js-yaml FEHLT
  package-lock.json packages["node_modules/js-yaml"].version -> 4.1.1, ohne dev-Markierung
  ```
  **Die Deklaration ist in `package.json` angekommen und im Lockfile nicht.** Das Paket liegt dort
  weiterhin **nur als transitive Auflösung** über `puppeteer → cosmiconfig → js-yaml`. **Damit ist
  die Lücke, gegen die `A-37-21` geschrieben wurde, sachlich noch offen:** wer `puppeteer` entfernt
  oder `cosmiconfig` seine Abhängigkeit ändert, nimmt `js-yaml` mit — **und das Tor scheitert
  geschlossen und weist JEDEN `.md`-Commit ab, für jede Rolle.**

  **Der bauende Generator hat das selbst offengelegt** (`e5aa5af7`, wörtlich): *„ein späteres
  `npm install` wird den Lockfile umschreiben, um `js-yaml` als Wurzel-Abhängigkeit zu führen; das
  habe ich NICHT ausgelöst, weil der Lockfile geteilt ist und ein Lauf unbeteiligte Pakete mitzieht."*
  **Das ist die richtige Vorsicht und genau der Grund, warum es ein eigenes Kriterium braucht** —
  der Schritt ist bekannt, riskant und deshalb liegengeblieben. *Ein bekannter, benannter Restpunkt
  ohne Kriterium wird nicht behoben, sondern zitiert.*

  **Verlangt:** `js-yaml` steht in `package.json` **und** als Wurzel-Eintrag im Lockfile,
  **ohne dass unbeteiligte Pakete ihre Fassung ändern.**
  **Messbar, und die zweite Hälfte ist die eigentliche Zusage:**
  ```
  npm ci --dry-run                                        -> exit 0
  git diff --numstat package-lock.json                    -> nur js-yaml-bezogene Zeilen
  git diff package-lock.json | grep -c '"version"'        -> keine fremde Paketfassung geändert
  ```
  **Absage-Regel:** Zieht der Lauf **andere** Pakete mit, wird **abgesagt und gemeldet**, nicht
  committet. *Ein grüner Lauf, der nebenbei sechs Fassungen bewegt, ist die zweite Wahrheit aus dem
  `node_modules`-Nicht-Ziel — eine Ebene tiefer.* **Ob `dependencies` oder `devDependencies` der
  richtige Block ist, entscheidet der Bauende messend, nicht schätzend:** maßgeblich ist, ob das Tor
  nach `npm ci --omit=dev` noch läuft. **Läuft es nicht, gehört `js-yaml` nach `dependencies`.**

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
