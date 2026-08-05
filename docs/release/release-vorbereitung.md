# Release-Vorbereitung — Grundlagen des Release-Prüfers

**Rolle:** Release-Prüfer nach `docs/ARBEITSREGELN.md` §4/§10/§11 · **Angelegt:** 05.08.2026
**Zweck:** §10 verlangt, dass Artefakt-Reproduzierbarkeit, Migration/Rückweg und Smoke-Test bei der
Abnahme **vorbereitet** sind. Diese Seite hält die commit-unabhängigen Grundlagen fest; die
eigentliche Release-Prüfung läuft je Auftrag auf dem exakten Abnahme-Commit und endet im §11-Votum.

## 1. Mess-Infrastruktur (steht)

```text
Getrennter Checkout (§6):  /Users/yamanuri/Documents/ticket-release-pruefung
                           git worktree, detached auf dem jeweils zu pruefenden Commit
                           node_modules KOPIERT (nie symlinken — Worktree-Regel)
Wiederholbarer Ablauf:     git checkout --detach <SHA>  ->  npm run build:hausplaner
                           -> shasum -a 256 gegen git show <SHA>:public/hausplaner/hausplaner.js
```

## 2. Artefakt-Reproduzierbarkeit — GEMESSEN 05.08.

```text
Commit 789de20f ("K-N4: das getrackte Bundle ist jetzt der v3-Bau"):
  frischer Bau:      d39dbb4d… · 1 445 040 B
  getracktes Bundle: d39dbb4d… · 1 445 040 B
  => BYTE-GLEICH. Die Kette Quellen -> vite -> Bundle ist deterministisch.
ABER an HEAD (89f373d9): 18 Insel-Dateien / +562 Zeilen seit dem Bundle-Commit geaendert
  => das getrackte Bundle ist gegen HEAD VERALTET. Vor jedem RELEASE_FREI ist der Neubau am
     Release-Kandidaten Pflicht (Grundtor Punkt 8/9) — Bauordnung: "nie mergen, immer neu bauen".
```

## 3. Release-Ziel: `main` gegen `auto/hausplaner-integration` — GEMESSEN 05.08. an 89f373d9

```text
Zweig vor main: 319 Commits · Zweig hinter main: 3 (d8612a63 Merge vom 01.08., 39b18514, d7052aa0)
Release-Diff nach Kategorie: 61 resources/planner · 49 docs/auftraege · 31 docs/product-data ·
  10 app/Services · 9 tests/Feature · 5 app/Http · 2 public/hausplaner · Rest docs
=> Ein Merge nach main braucht zuerst die Rueckfuehrung der 3 main-Commits in den Zweig
   (oder den Nachweis, dass ihr Inhalt fachlich enthalten ist) — sonst enthaelt der
   Release-Diff Rueckbauten. Prueft der Release-Pruefer je Kandidat.
```

## 4. Migrationen im Release — GEMESSEN

```text
NEU gegenueber main (beide additiv, beide mit down()):
  2026_08_02_150000_add_capabilities_to_supplier_connections.php        down: 1 · drop: 1
  2026_08_03_120000_add_product_identity_columns_and_suggestions_table  down: 1 · drop: 2
Rueckweg-Probe (vorwaerts + zurueck gegen ticket_testing) laeuft je Release-Kandidat.
```

## 5. Bekannte Release-Blocker (Stand 05.08. 09:0x)

```text
B1  DECISION_BLOCKED (docs/BEFUND-ZWEI-REGELWERKE.md): ZWEI Regelwerk-Fassungen (Zweig 1.0/1.1
    vs. governance/arbeitsregeln-v1.1-20260804 mit 1.3). Entscheidung: Yama. Solange offen,
    ist der Prozess selbst nicht eindeutig — kein RELEASE_FREI ueber diesen Punkt hinweg.
B2  Kein CI (.github/workflows fehlt) — alle Tore laufen lokal; §10 "CI erneut gruen" heisst
    hier: das volle lokale Grundtor am Kandidaten wiederholen und protokollieren.
B3  Kein Auftrag ist ABGENOMMEN — A-01 NACHBESSERN (08:55, CODE), A-02 CODE_FERTIG (ca5f80e4,
    2. Runde), A-03 CODE_FERTIG (26e378a5).
```

## 6. Smoke-Test-Plan (Skelett, wird je Release konkretisiert)

```text
S1  Version/Commit am Ziel  = Release-SHA (git rev-parse / Deploy-Marker)
S2  php artisan migrate:status                -> alle Release-Migrationen "Ran"
S3  Anmeldung + /admin erreichbar (HTTP 200, Session)
S4  Hausplaner Objekt-Flaeche: laden, Szene sichtbar, Konsole ohne neue Fehler
S5  Speichern-Roundtrip: PUT dokument -> 200, Neuladen -> Werte identisch (inkl.
    geometrieHerkunft/freigabe — B10-Kette)
S6  409-Konfliktpfad: veraltete revision -> 409, Daten unveraendert
S7  Logs: keine neuen ERROR-Eintraege in den ersten Minuten
Abbruchweg: Rollback auf vorherigen Release-SHA + php artisan migrate:rollback --step=<n>
            (beide Migrationen tragen down(); Bestandsdaten additiv, kein Datenverlust)
```
