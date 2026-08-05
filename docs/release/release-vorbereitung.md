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

---

# Release-Protokoll (Vertretung nach ARBEITSREGELN 1.4)

## A-02 — Lock-Halter statt Ruhe · 05.08.2026

```yaml
auftrag: A-02
abnahme_commit: 6953198a
release_commit: 7358de65   # Arbeitszweig-Stand, enthaelt 6953198a + Governance-Linie v1.4
votum: RELEASE_FREI        # Umfang: TRANSPORT auf fork + backup-private (v1.3/§ Yama: Push ist
                           # Transport, nicht VEROEFFENTLICHT). Zielintegration main = Sammel-
                           # Release, ausdruecklich offen.
ci: pass                   # kein CI vorhanden (Blocker B2) — lokales Tor am Abnahme-Commit im
                           # getrennten Checkout erneut gefahren: bash -n OK · node --test
                           # commitPruefen 30/30 pass · Scope exakt 2 A-02-Pfade
artefakte_reproduzierbar: true   # nicht anwendbar fuer A-02 (kein Bundle beruehrt); Kette selbst
                                 # am 05.08. byte-gleich belegt (789de20f)
migration: nicht_anwendbar
rueckweg: pass             # ein Commit, revertierbar; keine Persistenz
smoke_test_plan: "nicht anwendbar (Werkzeug-Aenderung ohne Laufzeitwirkung in der App)"
befunde:
  - "AUFLAGE (Planner-Entscheid uebernommen): Zweitfassung ca5f80e4 auf work/a01-generator wird
     VOR dem A-01-Merge zurueckgenommen (§7 keine Nebenbaustellen)"
ausgefuehrte_vertretung:
  - "Push fork:            a4e2bd58 -> 7358de65 (FF, exit 0)"
  - "Push backup-private:  a4e2bd58 -> 7358de65 (FF, exit 0)"
  - "davor: Governance-Merge a4e2bd58 (v1.3 Yama-veroeffentlicht + Vertretungsregel als 1.4)"
offen:
  - "lokaler Zweig-Ref steht auf e30c7197 — Fast-Forward auf 7358de65 sobald der gemeinsame Baum
     ruhig ist (76 unverbuchte Eintraege, ARBEITSREGELN.md im Wegwerf-Index-Zustand)"
  - "zwei Statustraeger (STATUS.md / AKTUELLER_AUFTRAG.yaml) — Zusammenfuehrung = Planner"
```

## DB-Bereinigung in Yamas Auftrag · 05.08.2026 (mündliche Freigabe: „die Sache mit der DB übernimmst du")

```yaml
anlass: "Evaluator-Meldung in STATUS.md (13c65f6f): L-01-Probedaten vom 03.08. in der ARBEITS-DB ticket"
gemessen_vor_loeschung:
  - "hausplaner_documents 20-24 (alternative_id 139-143, 03.08. 23:11-23:26) — Probe-Zeichnungen auf ECHTEN Objekten"
  - "ZUSAETZLICH GEFUNDEN: hausplaner_documents 18,19 auf den Marken-Objekten selbst"
  - "lead_alternative_adds 990002, 990004 (lead 990001, 03.08. 14:1x, leere Namen) — die '2 von 3 Marken'"
  - "lead 990001: existiert nicht mehr (war die dritte Marke)"
rueckweg: "_to_delete/db-backup-probedaten-2026-08-05/zeilen.json · 27 201 B · 7 Dokumente + 2 Adds
           vollstaendig (inkl. scene_json) — NUR LOKAL, nicht in Git (Datenschutz)"
geloescht: "Transaktion: 7 hausplaner_documents + 2 lead_alternative_adds, exakt per ID"
verifiziert: "0 Reste · Bestand unberuehrt: Objekte 139-143 5/5 vorhanden, Objekt 203 vorhanden"
ticket_testing: "ausdruecklich NICHT angefasst (904/905 sind dort unkritisch, Evaluator-Einordnung)"
```

## A-01 + A-03 · 05.08.2026

```yaml
auftrag: A-01 (Dach aus Kontur - lesbare Absage)
abnahme_commit: 94b58aaf   # evaluator 2. Runde, fehlerklasse KEINE, Sichtbarkeit am Browser gemessen
release_commit: 2b1ef24a   # Arbeitszweig, Zielintegration per Merge
votum: RELEASE_FREI        # Transport-Umfang (fork + backup-private); main = Sammel-Release
ci: pass                   # lokales Tor am Abnahme-Commit, getrennter Checkout:
                           # tsc clean · Insel 1689/1689 · Auflagen-Revert ba4dc4b0 auf der Linie belegt
artefakte_reproduzierbar: true   # Bundle am Abnahme-Commit BYTE-GLEICH neu gebaut (98d27e80…)
migration: nicht_anwendbar
rueckweg: pass             # Insel-Commits, revertierbar, kein Schema
befunde: []
---
auftrag: A-03 (Browser-Buehne nur auf ticket_testing)
abnahme_commit: 26e378a5   # evaluator (frische Instanz), fehlerklasse SPEC als verbuchter Beitrag
release_commit: 2b1ef24a
votum: RELEASE_FREI        # Transport-Umfang
ci: pass                   # bash -n OK · browserBuehne 6/6 — der eine Erstlauf-Fail war MEINE
                           # Umgebung (vendor fehlte im Checkout), nach Kopie 6/6; klasse UMGEBUNG
artefakte_reproduzierbar: nicht_anwendbar
migration: nicht_anwendbar
rueckweg: pass
befunde: []
ausgefuehrte_vertretung:
  - "Zielintegration work/a01-generator (94b58aaf, enthaelt 26e378a5 + Auflagen-Revert) -> Arbeitszweig"
  - "Push fork:           5a7a341a -> 2b1ef24a (FF, exit 0)"
  - "Push backup-private: 5a7a341a -> 2b1ef24a (FF, exit 0)"
```
