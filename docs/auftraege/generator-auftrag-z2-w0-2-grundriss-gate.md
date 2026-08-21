# Z2-W0-2 · Grundriss-Editor: Objektbindung wie beim Nachbarn `PlanUploadController`

```yaml
zustand: ENTWURF
welle: 0 (Sicherheit, LIVE)
basis_sha: 7a82ecfb
herkunft: Befund S-2 (docs/backlog/inventur-2026-08-21-z2.md), BESTÄTIGT durch security-reviewer 21.08.
spur: A — Autorisierung + Integrität fremder Kundenobjekte
baut: generator (Agent backend-entwickler)
nimmt_ab: evaluator — nie der Bauende
fachliche_gegenprobe: security-reviewer (Meldung)
status_steht_in: docs/STATUS.md — Integrator-Lauf erforderlich
```

## Ziel
`energie.grundriss.index/editor` nur mit `Hausplaner,read`; `energie.grundriss.speichern` nur mit
`Hausplaner,update` — identisch zur Nachbar-Prüfung `PlanUploadController::store:79`; fremdes
Projekt → 404 statt Leak.

## Ist-Beleg (Gegenprobe)
`routes/web.php:5658-5664` nur `auth` (Block ab `:5621`); `route:list`: editor/speichern MW
`['web', Authenticate]`. `GrundrissController.php`: `__construct` (`:44-49`) reine DI, keine
Middleware; `index()` (`:87-91`) listet ALLE Projekte mit Geometrie; `editor()` `:114`
`HeizlastProjekt::…->find($projekt)` ungescoped; `speichern()` `:261` `LeadAlternativeAdd::find`
ohne Check → `schreibeGeometrieVersion()` (`:317-332`) schreibt **neue aktive Anforderungsprofil-
Version an ein Fremdobjekt** (append-only, aber die aktive Version kippt). Vorhandener, unerwähnter
Schutz: `:170-172` `PlanUpload` besitzergebunden — deckt ein anderes Objekt.
Asymmetrie belegt: `PlanUploadController.php:79` `hasPermission('Hausplaner','update')` für
denselben Vorgang; dort zusätzlich `abort_unless($planUpload->user_id === auth()->id(), 403)` ×4.
**Reproduktion:** `POST /admin/energie/grundriss/speichern` mit `alternative_id=<fremd>` + gültigem
Polygon → 200 `{"anforderungsprofil_id":…,"version":N+1}`.

## Scope · Dateien
- `routes/web.php:5658-5664`: `permission:Hausplaner,read` auf index/editor, `permission:Hausplaner,update`
  auf speichern (Muster `:4988`).
- `GrundrissController.php:114`: Fremdprojekt → `abort(404)` statt Leak (Hausmuster: 404, nicht 403,
  damit keine Existenz geleakt wird); `index()` auf berechtigte Projekte eingrenzen ODER — wenn kein
  Ownership-Modell für `HeizlastProjekt` existiert — das Permission-Gate als hinreichend begründen
  und den Rest als **Folgeposten** ausweisen (im Baubericht entscheiden und belegen).
- `tests/Feature/Security/GrundrissGateTest.php` (neu): ohne Hausplaner → 403 auf allen drei; mit
  `read` → editor 200, speichern 403; mit `update` → speichern 200. **Nur `ticket_testing`.**
**Nicht-Ziele:** keine Änderung an der Geometrie-Logik; keine Migration; PlanUpload-Pfad unberührt.

## Nachvollzugs-Matrix (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: `route:list --name=energie.grundriss` zeigt die drei permission-Middlewares (Rohausgabe) | Gate | *n.U.* | route:list |
| B: ohne Hausplaner → 403 ×3; read → editor 200 / speichern 403; update → speichern 200 | Test | *n.U.* | Testnamen |
| C: `speichern` mit fremder `alternative_id` durch Nutzer MIT update-Recht: Verhalten im Baubericht benannt (Ownership-Modell vorhanden? ja/nein, mit Messung) | Ehrlichkeit | *n.U.* | Bericht |
| D: `git diff --numstat`: nur routes/web.php, GrundrissController.php, Testdatei | Grenze | *n.U.* | Rohausgabe |

**P1-Kriterium B ist vor dem Bau wirksam rot** (route:list ohne permission; Reproduktion 200).

## Rückweg
Ein Commit, zurückdrehbar; kein Schema; append-only-Daten unberührt.
