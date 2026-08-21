# Z2-W0-4 · Wächter: keine neue Web-Route ohne `permission:` — Ratschen-Test mit Baseline

```yaml
zustand: ENTWURF
welle: 0 (Sicherheit, Prozess-Schutz)
basis_sha: 7a82ecfb
herkunft: Prozess-Befund aus S-5 + Wächter-Kandidat der Security-Gegenprobe 21.08.
spur: A — berührt die Sicherheits-Invariante des Hauses
baut: generator (Agent backend-entwickler)
nimmt_ab: evaluator — nie der Bauende
status_steht_in: docs/STATUS.md — Integrator-Lauf erforderlich
```

## Ziel
Ein Feature-Test friert die heutige Menge der `auth`-Routen OHNE `permission:` als Baseline ein und
**schlägt fehl, sobald eine neue Route ohne `permission:` dazukommt** (Ratsche: die Zahl darf nur
sinken). Genau das hätte S-5 am 16.07. beim Anlegen gefangen.

## Ist-Beleg (Gegenprobe)
Nichts Systematisches existiert: `tests/Feature/Security/UngatedWriteRoutesAuthTest.php` prüft nur
`auth` gegen eine handgepflegte 4-URI-Liste; `scripts/waechter.sh` ist Gate-Runner, kein Scanner.
Gemessen (`Route::getRoutes()`): web-Routen 2365 · mit auth 2296 · mit permission 371 ·
**auth ohne permission 1925**. „Alle absichern" ist kein Fix, sondern ein Programm — deshalb
Ratsche statt Alles-oder-nichts.

## Scope · Dateien
- `tests/Feature/Security/RoutePermissionRatschenTest.php` (neu): iteriert `Route::getRoutes()`,
  sammelt `auth`-Routen ohne `permission:`-Middleware (Name oder URI+Methode als Schlüssel),
  vergleicht gegen `tests/Feature/Security/route-permission-baseline.json`; **neue** Einträge →
  Fehlschlag mit Liste; verschwundene Einträge → Hinweis, Baseline darf verkleinert werden.
- Baseline-Datei committet, erzeugt aus dem Stand **nach** W0-1/W0-2 (damit die drei Gates nicht
  als Ausnahme verewigt werden).
- Aufnahme in `scripts/waechter.sh` (Gate-Runner) — Erstnutzer benannt: **Evaluator bei jeder
  Abnahme, Generator vor CODE_FERTIG.**
**Nicht-Ziele:** keine der 1925 Routen wird in diesem Auftrag abgesichert; keine Änderung an
Routen; Nebenbefund `permission:hausplaner` (1× klein vs. 16× `Hausplaner`) wird **gemessen**
(Collation-Verhalten von `where('item_id', $item)`, `User.php:71`) und als eigener Befund
gemeldet, nicht hier behoben.

## Kanten
Schlüssel muss stabil sein (Route-Namen können fehlen → URI+Methode); der Test darf nicht an
Reihenfolge hängen; Baseline-Erzeugung als Artisan-/Script-Befehl dokumentiert, damit niemand sie
von Hand „anpasst".

## Nachvollzugs-Matrix (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: Test grün am Baseline-Stand | Ratsche | *n.U.* | Zähler |
| B: Rot-Probe: eine Dummy-Route ohne permission temporär ergänzt → Test rot mit Routen-Name in der Meldung; entfernt → grün | Beweis | *n.U.* | beide Rohausgaben |
| C: Baseline enthält die **sieben** W0-1/W0-2-Routen NICHT mehr (3× `objekte.*` + 4× `energie.grundriss.*` inkl. `vorschau` — Restpunkt §258 berichtigt, Planner 21.08.) | Reihenfolge | *n.U.* | grep in Baseline (7 Namen, Rohausgabe) |
| D: `scripts/waechter.sh` ruft den Test; Erstnutzer im Skriptkommentar benannt | Einsatz | *n.U.* | Zitat |
| E: Nebenbefund Collation gemessen und als Befund abgelegt (Rohausgabe der SQL-Probe gegen `ticket_testing`) | Messung | *n.U.* | Rohausgabe |

**P1-Kriterium B ist vor dem Bau wirksam rot** (kein solcher Test existiert).

## Rückweg
Ein Commit, Test + Baseline + Skriptzeile, zurückdrehbar; kein Schema, keine Daten.
