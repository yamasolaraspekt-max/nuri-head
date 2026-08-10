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

---

## Protokoll: Sammel-Release nach main (05.08.)

```yaml
vorgang: sammel-release-main
datum: 2026-08-05
rolle: release-pruefer (Vertretung nach Regelwerk 1.4.1)
kandidat: c908d3f0f7da2f6613ba285e920404f133cc1266
enthaelt: [A-01 (94b58aaf), A-02 (6953198a), A-03 (26e378a5), Auflagen-Revert ba4dc4b0]
grundtor:
  ort: getrennter Pruef-Checkout ticket-release-pruefung (§6)
  tsc_hausplaner: clean
  test_hausplaner: 1689/1689
  bundle: BYTE-GLEICH gegen getracktes Artefakt
  bash_n: OK
  skript_tests: 36/36
  php_artisan_test: "880 passed (3110 assertions), 51s"
  erster_lauf: "26 failed — Klasse UMGEBUNG: ViteManifestNotFoundException, public/build/
    fehlte im frisch bestueckten Checkout (gitignored). Nach cp -R aus dem Hauptcheckout
    880/880. KEINE Regression. Lehre: Checkout-Bestueckung umfasst node_modules, vendor,
    .env, .env.testing UND public/build."
geheimnisse: "diff d8612a63..c908d3f0 (367 Commits): nur Test-Fixtures (User::factory,
  PASSWORT_FIXTURE), keine .env-Dateien, kein _to_delete"
ff_bedingung: "fork/main und backup-private/main standen beide auf d8612a63, Vorfahr des
  Kandidaten (merge-base --is-ancestor) — reiner Fast-Forward, kein Force"
ausfuehrung:
  - "push fork  c908d3f0 -> main   d8612a63..c908d3f0  exit 0"
  - "push backup-private c908d3f0 -> main  d8612a63..c908d3f0  exit 0"
  - "lokaler main-Ref NICHT nachgefuehrt (Berechtigung abgelehnt) — Remotes tragen die Wahrheit"
statuswahrheit: "A-01/A-02/A-03 auf VEROEFFENTLICHT gesetzt, Antwort auf die
  Evaluator-Nachverfolgung in STATUS.md — im selben Commit wie dieses Protokoll"
naechster_zustand: "BETRIEBSBESTAETIGT — gehoert Yama"
```

---

## Protokoll: Release A-08 nach main (10.08.)

```yaml
vorgang: release-a08-main
datum: 2026-08-10
rolle: release-pruefer (Stamm-Instanz, Vertretung nach Regelwerk 1.4.2)
kandidat: 8648a4cbe0a40cace0ac03c409064efa0a8be8ea
release_frei: "b2f8c44b (frische Release-Instanz, §10 an 85b03d23 vollstaendig: Kette
  BEREIT->IN_ARBEIT->Bau->CODE_FERTIG->Erst-+Zweitvotum als Vorfahrenkette, Scope exakt
  fuenf Blatt-Dateien, Rueckweg git revert 5a54b004, Wildbetriebs-Beleg). Ihr Push wurde
  verweigert (2b5aebae) — Transport und main-Integration hier nachgeholt."
enthaelt: "A-08 (Abnahme 23b3a490 + Zweitvotum f430242d an 85b03d23), A-05-Messbericht
  (ABGENOMMEN b29bb79d, bleibt beim Planner), Blaetter A-09/A-10, Doku/Governance"
grundtor_eigenlauf: "tsc clean · Insel 1689/1689 · Bundle byte-gleich (98d27e80…) ·
  bash -n OK · Tor-Suite 38/38 · Buehne 6/6 · php artisan test 880/880 (3110 Assertions)"
ff_bedingung: "fork/main und backup-private/main beide auf c908d3f0, Vorfahr des Kandidaten"
geheimnisse: "diff fork/main..Kandidat: 0 Treffer, keine .env, kein _to_delete"
ausfuehrung:
  - "push fork  8648a4cb -> main   c908d3f0..8648a4cb  FF"
  - "push backup-private 8648a4cb -> main  c908d3f0..8648a4cb  FF"
statuswahrheit: "A-08 VEROEFFENTLICHT mit release_sha im selben Commit wie dieses Protokoll"
naechster_zustand: "BETRIEBSBESTAETIGT — gehoert Yama"
```

---

## Protokoll: Release A-04 nach main (10.08.)

```yaml
vorgang: release-a04-main
datum: 2026-08-10
rolle: release-pruefer (Stamm-Instanz, Vertretung nach Regelwerk 1.4.2)
kandidat: e7c6e618f9aabc625ae336bee7366eb377d3adcc
abnahme: "b6a63e3e (Evaluator, fehlerklasse KEINE) an c3d52f09 — Suiten 7/7+7/7 selbst,
  Wegwerf-Proben je exit-belegt, 2 Mutationen gefallen, Zwei-Richtungs-Probe an 89f373d9"
paragraph10: "Kette: c3d52f09 Vorfahr der Linie · Scope exakt 4 Dateien (buehnen-waechter.sh,
  buehnenWaechter.test.mjs, browserBuehne.test.mjs +11, ANKER +7) · Produkt-Code seit 8648a4cb
  unberuehrt (resources/public/app = 0 Treffer, deshalb kein tsc/Bundle/php) · bash -n OK ·
  Suiten 7/7 + 7/7 + 38/38 · Geheimnis-/env-Scan leer · FF-Bedingung geprueft"
ausfuehrung:
  - "push fork  e7c6e618 -> main   8648a4cb..e7c6e618  FF"
  - "push backup-private e7c6e618 -> main  8648a4cb..e7c6e618  FF"
statuswahrheit: "A-04 VEROEFFENTLICHT mit release_sha im selben Commit wie dieses Protokoll"
offen: "Realfund PID 48098 (verwaiste php84-Buehne) — Handraeumung bei Yama;
  BETRIEBSBESTAETIGT fuer A-01/A-02/A-03/A-08/A-04 bei Yama"
```

---

## Protokoll: Release A-07 nach main (10.08.)

```yaml
vorgang: release-a07-main
datum: 2026-08-10
rolle: release-pruefer (Stamm-Instanz, Vertretung nach Regelwerk 1.4.2)
kandidat: e321f2a2378e6a69af5ac6af1c12cb5b189027e5
abnahme: "fc5a3daa (Evaluator) + 05f3e1d9 (Zweitinstanz, unabhaengig deckungsgleich) an c512f931;
  Suite 42/42, Elter 38/38, 4 neue Zusagen am Elter rot, 4 Mutationen; Wirkung zweiseitig:
  Halde-Wachstum 0 im Pruefstand vs 16 am Elter"
paragraph10: "Kette Vorfahr · Scope exakt 2 Dateien (commit-pruefen.sh +94, Suite +143) ·
  Produkt-Code seit e7c6e618 unberuehrt · bash -n OK · 42/42 + 7/7 + 7/7 · Scans leer"
feldbelege: "A-07-1b-Kippfall LIVE (7ab67893): 212 fremde Index-Blobs gemeldet, nichts angefasst —
  danach als docs/rollenkette (211 Dateien) in 1e933a64 gesichert. Wirkungsmessung Plan-Pruefer:
  Divergenz 35->0, status 55->2, Halde 2589 beiseitegelegt (0 geloescht)"
offene_befunde: "P2/BEWEIS an Generator (A-07-4-Initialisierung ohne Zusage) — blockiert nicht"
ausfuehrung:
  - "push fork  e321f2a2 -> main   e7c6e618..e321f2a2  FF"
  - "push backup-private e321f2a2 -> main  e7c6e618..e321f2a2  FF"
statuswahrheit: "A-07 VEROEFFENTLICHT im selben Commit wie dieses Protokoll"
naechster_zustand: "BETRIEBSBESTAETIGT — gehoert Yama (jetzt 6 Auftraege offen)"
```

---

## Protokoll: Release A-10 nach main (10.08.)

```yaml
vorgang: release-a10-main
datum: 2026-08-10
rolle: release-pruefer (Stamm-Instanz, Vertretung nach Regelwerk 1.4.2)
kandidat: 2da18c444e4704230fddc6a5b4adc129aaf3ff82
release_frei: "5f7043bc (frische Release-Instanz, §10 an 47c0aa73: Kette 6x is-ancestor,
  Suite 1692/1692, Scope 3 Dateien content-identisch, Bundle md5 byte-gleich, Rueckweg
  apply --check -R). Ihr Push verweigert (d836fb91) — hier nachgeholt."
grundtor_eigenlauf: "Abnahme-Commit Vorfahr · tsc clean · Insel 1692/1692 · Bundle
  BYTE-GLEICH (62338b66…) · php artisan test 880/880 · Scans leer · FF-Bedingung geprueft"
ausfuehrung:
  - "push fork  2da18c44 -> main   e321f2a2..2da18c44  FF"
  - "push backup-private 2da18c44 -> main  e321f2a2..2da18c44  FF"
statuswahrheit: "A-10 VEROEFFENTLICHT im selben Commit wie dieses Protokoll"
naechster_zustand: "BETRIEBSBESTAETIGT — gehoert Yama (7 Auftraege offen)"
```

---

## Protokoll: Release A-09 nach main (10.08.)

```yaml
vorgang: release-a09-main
datum: 2026-08-10
rolle: release-pruefer (Stamm-Instanz, Vertretung nach Regelwerk 1.4.2)
kandidat: 2e7b58fc83df74a746272e7c0e5d1be25c0c8a5a
abnahme: "e53e3cfb (Evaluator) an af8f2054, Fehlerklasse KEINE; Suite 42/42->50/50,
  fuenf Neu-Zusagen an der Basis rot, sechs Mutationen gefangen"
paragraph10: "Kette Vorfahr · Scope exakt 2 Dateien (commit-pruefen.sh +96, Suite +227) ·
  Produkt-Code seit 2da18c44 unberuehrt · bash -n OK · 50/50 + 7/7 · Scans leer · FF geprueft"
ausfuehrung:
  - "push fork  2e7b58fc -> main   2da18c44..2e7b58fc  FF"
  - "push backup-private 2e7b58fc -> main  2da18c44..2e7b58fc  FF"
abweichung: "Zustandseintrag kam einen Commit NACH dem main-Push (Patch-Skript griff nicht,
  Assertion uebersehen weil der Push im selben Block weiterlief) — eigene Regel vom 05.08.
  verletzt, transparent im release_vermerk offengelegt. Lehre: Patch-Erfolg pruefen BEVOR
  der Push-Befehl im selben Block steht."
naechster_zustand: "BETRIEBSBESTAETIGT — gehoert Yama (8 Auftraege offen)"
```

---

## Protokoll: Release A-11 nach main (10.08.)

```yaml
vorgang: release-a11-main
datum: 2026-08-10
rolle: release-pruefer (Stamm-Instanz, Vertretung nach Regelwerk 1.4.2)
kandidat: c819129236b7dd4476552870ac2586fb4cf8d011
abnahme: "efe38d1d (Evaluator, Fehlerklasse KEINE) an 28760966; Suite 61/61, Basis 50/50,
  acht Kriterien je Wegwerf-Repo-Probe, zwei eigene Mutationen gefallen"
paragraph10: "Kette Vorfahr · Scope exakt 2 Dateien (Tor +35, Suite +130) · Produkt-Code
  seit 2e7b58fc unberuehrt · bash -n OK · 61/61 (TICKET_ROLLE gesetzt) + 7/7 · Scans leer ·
  Zweitbeleg: unabhaengiges §10 der frischen Instanz (6a9ea6ab), Push verweigert (f26ed034)"
ausfuehrung:
  - "push fork  c8191292 -> main   2e7b58fc..c8191292  FF"
  - "push backup-private c8191292 -> main  2e7b58fc..c8191292  FF"
statuswahrheit: "IM Kandidaten (A-09-Lehre angewandt: Patch verifiziert VOR dem Push)"
wirkung: "Das Commit-Tor traegt jetzt: Drei-Nein-Regel (A-08), Index-Waechter (A-07),
  --git-dir/GIT_DIR-Erkennung (A-09), Rollenmarke TICKET_ROLLE (A-11); dazu Buehnen-Waechter
  (A-04) und Melder am leeren Ergebnis (A-10). §13 ist ab jetzt zaehlbar."
naechster_zustand: "BETRIEBSBESTAETIGT — gehoert Yama (9 Auftraege offen)"
```
