# Übergabe an die nächste Planner-Sitzung — 01.08.2026, 19:20

*Diese Seite ist zum Einfügen in eine neue Sitzung gedacht. Sie ersetzt kein Nachmessen —
**jede Zahl darin ist mit dem danebenstehenden Befehl geprüft und veraltet ab der ersten Minute.***

---

## 1. Wer du bist

Du bist der **Planner** im Governance-Zyklus für **ticket** (CRM/ERP, Laravel 11, LIVE mit echten
Kunden). Der Skill `governance-zyklus` gilt — lies ihn, bevor du etwas planst.

**Du entwirfst, du baust nicht, du nimmst nicht ab.** Der Generator baut, der Evaluator nimmt ab,
der Prüfer misst quer. **Rollentrennung ist der einzige Grund, warum eine Prüfung etwas wert ist.**

**Yamas stehendes Ziel: das Frontend fertigstellen** — konkret: *Geschosse bauen und eine saubere
Zwischendecke ziehen.* Alle Antworten auf **Deutsch**.

---

## 2. Der erste Handgriff jeder Runde

```
cat docs/STAND.md
```

**`docs/STAND.md` ist das Arbeitsgedächtnis** — eine Seite, wird ÜBERSCHRIEBEN statt angehängt.
Der Ledger `docs/handoff-status.md` ist Archiv (1,8 MB) und **keine Lage**. Abschnitt 4
„ZURÜCKGENOMMEN" vor jedem Zitat aus älteren Papieren prüfen.

---

## 3. Wie du an das Repo kommst

**Nur über `mcp__remote-devices__device_bash`** (ggf. per ToolSearch laden):

```
cd /sessions/<deine-session>/mnt/Documents/ticket && <befehl>
```

Auf Yamas Mac liegt dasselbe unter `/Users/yamanuri/Documents/ticket`, der Merge-Worktree unter
`../ticket-main`. **Die App läuft unter `http://ticket.test`** (Herd), NICHT `127.0.0.1:8000`.

**Bricht die Brücke weg** („not connected to the bridge"): Desktop-App zu oder Rechner schläft.
Dann **ein einziger Prüfbefehl je Runde**, sonst nichts, und Yama einmal informieren — nicht
wiederholen. Am 31.07. war sie 16 Stunden weg; nichts ging verloren, weil alles committet war.

---

## 4. Harte Regeln — nicht verhandelbar

```text
NIEMALS pushen. Auch nicht nach gruener Abnahme. Pushen macht ausschliesslich Yama.
NIEMALS auf `upstream` (raminsadid2021 ist ein fremdes Konto). NIEMALS --force.
NIE `git add -A` oder `git add .` - immer `git commit -- <pfade>`, nur eigene Pfade.
KEIN `rm`/`unlink` auf dem Mount. Locks nur per `mv` nach `.git/_locks_beiseite/<datum>/`.
Persistierte Schema-Werte NICHT umbenennen: type: wall|window|door|ceiling, objectType,
  zoneType, routeType.
Tor 2 (Merge nach main / Deploy) gehoert Yama allein.
Der Planner schreibt in `docs/` - und in `scripts/`, wenn es Planner-Werkzeug ist.
Zugangsdaten nicht anfassen. KEINE Log-Inhalte zitieren (in laravel.log stehen auth_user_id,
  auth_employee_id, customer_id im Klartext).
Heredoc immer angefuehrt (<< 'ENDE'). Kein awk/sed mit deutschen Sonderzeichen.
Zeitzonen: Container UTC, Instanzen CEST - immer TZ=Europe/Berlin + --date=format-local:
Die ~3000 Kunden sind SEEDER-Beispieldaten.
Der Planner faehrt keine npm-Gates (esbuild-Plattformfehler, php exit 127 im Container).
  `node --test <datei>` auf der Geraete-VM geht und wird benutzt.
```

**Yamas Dauerregel für jeden Terminal-Vorgang (30.07. 22:52, wörtlich):**
*„du gibst mir nur eins ache die füge ich terminal an, dann schicke ich dir ein kopie hier dann
gibst du mir den zweiten befehl — nicht viele befehle in einem text, nur eins"*
**Ein Befehl je Nachricht. Ein `cd` wird NIE mit etwas anderem zusammengefasst.**

---

## 5. Stand am 01.08. um 19:20 — jede Zahl mit Befehl

```text
main    d8612a63    gemergt 01.08. 19:0x - 80 Dateien, 7060 Zeilen, `git diff 99f38da7 main` LEER
Zweig   fb612517    auto/hausplaner-integration, 13 Commits voraus
Merges auf main: 3   (`git rev-list --count auto/hausplaner-integration..main`)
Etikett: vor-merge-2026-08-01-1045

Kennzahl Produktivcode - `config` gehoert dazu, es fehlte bis 01.08.:
  git log -1 --date=format-local:'%d.%m. %H:%M' --pretty='%h %ad %s' \
    -- resources app tests routes public database scripts config
```

**Auf `main` liegen abgenommen:** Z-01 · AUF-38-P1+P2 · PB-043 T2 · AUF-91 · PB-047 · PB-023.

**Im Zweig, noch nicht auf main:** Z-02 (abgenommen) · Z-05 (gebaut, Votum offen) ·
mein Werkzeugbau vom 01.08. (Votum offen) · die geschnittenen Blätter.

**Eine bekannte Lücke, offen benannt:** auf `main` liegt `scripts/zaehle.mjs` in der Fassung **mit
zwei Löchern** — die Reparatur kam nach dem Merge-Punkt. Gefährlich ist das nicht (auf `main`
benutzt es niemand), aber es gehört in den nächsten Merge.

---

## 6. Die Werkzeuge, die es seit dem 01.08. gibt — benutze sie

```text
node scripts/auftrag-pruefen.mjs <blatt.md> [weitere...]
    Faehrt jeden `pruefung.befehl` aus dem YAML-Kopf. Sperrt bei:
    PB-019  Kopf fehlt oder misst nichts, Blatt aber `status: aktiv`
    S-01    genau EIN Blatt mit `status: aktiv`
    S-06    weniger als zwei baubare Blaetter (aktiv|bereit)          F-08
    S-07    ein Kriterium ist schon VOR dem Bau erfuellt              F-07
    S-08    Ausgangswert weicht von der Messung ab (nur Meldung)      F-04
    S-09    Kopf ohne `status`                                        F-08b
    S-10    der Baum hat sich waehrend der Messung bewegt             F-03/F-12

node scripts/zaehle.mjs <datei> <muster> [--wort] [--mit-kommentaren] [--raute]
    Zaehlt OHNE Kommentare (F-09) und optional mit Wortgrenze (F-11).
    Exitcode immer 0 - anders als `grep -c`, das bei null Treffern exit 1 liefert.

bash scripts/pfade-pruefen.sh [verzeichnis...]
    Findet tote Code-Verweise in Markdown. Papiere koennen sich mit
    <!-- pfade-pruefen: historisch --> ausnehmen.

bash scripts/commit-pruefen.sh "Botschaft" pfad [weitere...]
    Prueft VOR dem Commit: existiert, nicht leer, wirklich geaendert, Syntax traegt.
    Raeumt die Lock-Reste im selben Aufruf. BENUTZE DAS STATT `git commit`.

node scripts/statische-inline-stile.mjs [datei]
    Die fuehrende Zahl fuer das AUF-38-Programm.
```

**Zusagen: 57, 0 fail** (`node --test scripts/__tests__/auftragPruefen.test.mjs scripts/__tests__/zaehle.test.mjs`)

---

## 7. Die Schlange — Stand 19:20

| Blatt | Zustand |
|---|---|
| **Z-05** Konturwerkzeug | `aktiv`, **gebaut** — Votum offen |
| **AUF-38-P3** FussUndUeberlagerungen | `bereit`, Generator baut gerade (Baum zeigt die Dateien) |
| **AUF-38-P4+P5** Kopfrahmen + Rest | `bereit` |
| **Z-03+Z-04** Fangtyp + Erweiterung | `bereit` |
| **Z-06** Decke aus Kontur | `gesperrt` — **wird `bereit`, sobald Z-05 ein Votum hat** |

**Z-06 ist der Posten, auf den Yama wartet.** Kette: Z-02 (fertig) → Z-05 (gebaut) → **Z-06**.

**Bewusst NICHT geschnitten:** Z-07…Z-11 · PB-024-N1 (17 fehlende `--sa-`-Tokens im CRM) ·
PB-024-N2 (Canvas-Farben zur Laufzeit auflösen?) · die drei haltbaren Werkzeug-Blätter
(`drehen`, `erkennung-bestaetigen`, `pv-modul`) · die 18 wackeligen Werkzeug-Spezifikationen neu
messen. **Grund: R16 verlangt zwei baubare Blätter, nicht fünfzehn.** S-06 meldet, wann sie
gebraucht werden.

---

## 8. Was bei Yama liegt

```text
push          13 Commits liegen nur auf seiner Platte - kein Backup ausserhalb der Maschine
Papierstopp   docs/ hat 980 Dateien, der Ledger 1,8 MB (PB-042)
PB-048        sollen die 18 dichtesten Blades in den Browser?
PB-029/030    Wissensregister - sein Material, andere App
docs/product-data/  ein unverfolgtes Verzeichnis, das keine der drei Rollen angekuendigt hat
```

---

## 9. Die Fehlerklassen — `docs/auftraege/FEHLERKLASSEN.md`

**15 von 16 haben eine Barriere.** Die einzige offene:

**F-10 Lock-Reste** — auf diesem Mount **nicht behebbar** (`unlink` verboten), nur gemildert.
*Das ist so benannt und kein offener Posten.*

**R9 gilt:** bei der zweiten Wiederholung derselben Klasse kommt eine **technische Barriere**,
kein dritter Vorsatz. Wer einen Befund einordnet, trägt ihn **vor** dem Ledger-Eintrag dort ein.

---

## 10. Die zwei harten Regeln aus den Fehlern

**A — Kein Blatt geht raus, bevor jeder Befehl darin einmal gelaufen ist**, auch gegen einen
absichtlich ROTEN Fall (`VORLAGE.md` Regel 9). *Vier von vier Blättern trugen einmal ein
Kriterium, das nie hätte laufen können.*

**B — Keine Arbeit liegt länger als zwanzig Minuten uncommittet.** *Ein Reset am 30.07. fraß zwei
Rollen ihre Voten.*

---

## 11. Fallen, die schon Zeit gekostet haben

```text
`grep -c` liefert bei NULL Treffern exit 1 - ein Kriterium bricht am Ausgangswert ab.
   -> `grep -o ... | wc -l` oder `node scripts/zaehle.mjs`
`| tail -n` schneidet `# pass`/`# fail` ab UND die Pipe schluckt den Exitcode.
Die Validator-Denylist ueberspringt jeden Befehl mit `>` (auch `=>` und `->`).
`stat -c %s` ist GNU und laeuft NICHT auf Yamas Mac -> `wc -c <`.
Konva loest KEINE CSS-Variable auf: `ctx.fillStyle = 'var(--x)'` wird verworfen und die
   zuletzt gueltige Farbe gemalt - still falsch, nicht leer.
Zaehler zaehlen Kommentare mit, wenn man sie laesst (F-09, am 01.08. zweimal zugeschnappt:
   751 tote Pfade statt 75).
`docs/_playground-archiv/` ist das Archiv einer ANDEREN App - ihre Pfade nicht mitmessen.
Die Papiere nennen Pfade oft relativ zur Insel-Wurzel (`app/x.ts` meint
   `resources/planner/hausplaner/app/x.ts`).
```

---

## 12. Der Merge — wenn er ansteht

**14 Schritte, einer je Nachricht.** `docs/merge-anleitung.md` ist das Nachschlagewerk, **keine
Kopiervorlage**.

```text
1   cd /Users/yamanuri/Documents/ticket                  (allein!)
2-5 Vorpruefung: rev-list --count auto..main  (erwartet die Zahl der bisherigen Merges = 3)
    rev-list --count main..auto   ·   git log auto..main (jeder mit ZWEI Eltern)  ·  git status
6   git tag vor-merge-JJJJ-MM-TT-HHMM main
7   git worktree list   (../ticket-main steht schon)
8   cd ../ticket-main                                    (allein!)
9-10 git status  ·  git log -1 --oneline  (muss der aktuelle main sein)
11  git merge --no-ff <abgenommener-sha> -m "..."
12-13 git log -1 --oneline  ·  **git diff <sha> main MUSS LEER SEIN**
14  cd /Users/yamanuri/Documents/ticket
```

**Gemergt wird auf den letzten Commit MIT Evaluator-Votum, nie auf die Zweigspitze.**
Der Rückweg `git reset --hard vor-merge-...` gilt **ausschliesslich im Merge-Worktree** — im
Hauptbaum wirft derselbe Befehl den Arbeitstag weg.

---

## 13. Zum Ton

Yama will **Fakten mit Befehl**, keine Unverbindlichkeit. Er hat am 31.07. wörtlich gesagt:
*„schaffe fakten, wann passiert was … komm mit fakten um die ecke"* — und über die Instanzen:
*„euer größtes Problem ist, dass ihr Gedächtnisverlust habt."*

**Deshalb:** keine Zahl ohne Befehl, kein Datum ohne Zahl, und **eigene Fehler zuerst benennen**.
Am 01.08. hat der Generator ein Blatt von mir zurückgegeben, weil es nicht baubar war — *das ist
der Weg, den die Governance vorsieht, und er hat funktioniert.* Widerspruch gehört **vor** den Bau,
nicht in die Abnahme.
