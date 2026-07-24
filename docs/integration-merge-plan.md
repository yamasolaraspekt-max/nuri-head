# Integrations-Merge-Plan — `auto/hausplaner-integration`

**Zweck:** die abgenommenen grünen Slices in EINEN stabilen `auto/`-Baum vereinen → ein Whole-Stack-Gate,
ein Tor-2-Merge-Ziel. **Nur `auto/`, kein Push, kein main-Merge (Tor 2 = Yama).**

## Basis & Methode
- **Basis: `auto/hausplaner-decke @ d59e26c`** — trägt den **Modell-Superset** (`RoofNode.anbau`/`RoofShape`
  **UND** `CeilingNode`/`ceilings?`), dazu Capture/Sizing/Fixture-Infra + beide Fixturen (u-dach, decke-treppe)
  + Decke-Render. Gemessen: `decke` fasst die Roof-Geometrie-Dateien NICHT an (kein Verlust beim Übernehmen).
- **Methode: echter 3-Wege-`git merge auto/hausplaner-w3b-2`** (gemeinsamer Vorfahr `e9334bb`) — additive
  Änderungen beider Seiten auto-gemergt; einzige Kollision war das gebaute Bundle (frisch gebaut, nie gemergt).
- Danach additiv: Navi-CI (aus `navi-batch0 @ 2011798`), CLAUDE.md-Skill-Block (aus `dach-ui`).

## Auflösung je Datei / Gruppe
| Datei / Gruppe | Herkunft | Auflösung |
|---|---|---|
| `domain/scene.types.ts` | decke (Superset) | Union: `anbau`/`RoofShape` **und** `CeilingNode`/`ceilings?` — auto-gemergt, beides vorhanden. |
| `domain/validation.ts`, `commands.types.ts`, `applyCommand.ts` | decke + w3b-2 | Union: Ceiling-Schema/-Commands (decke) + Roof-Regeln (w3b-2) auto-gemergt. |
| `renderers/three-d/dachMesh.ts` | **w3b-2 kanonisch** | Footprint-Anker für u+l/t in EINER `quelle`-Schleife (`polygonBbox`), `ltFormFlaechen`. **Subsumiert dach-ui `66ad448` — NICHT separat gemergt** (keine zweite Anker-Wahrheit). |
| `geometry/dachVerschneidung.ts` | w3b-2 | L/T-Verschneidungsflächen (byte-treuer Port) — unberührt übernommen. |
| `renderers/three-d/platzierung.ts` | w3b-2 | Gehrung-Helfer (`wandSegmentGrundriss`/`…PrismaThree`). |
| `renderers/three-d/szene.ts` | **Union (auto-gemergt)** | Decke-Slab-Render + Capture/Sizing (decke) **UND** Gehrung-Prisma-Wandpfad (w3b-2) — beide Seiten in disjunkten Regionen, sauber vereint. |
| `renderers/three-d/{capture,deckenMesh}.ts`, `fixtures/studioFixtures.ts`, `main.tsx`, `adapter.ts` | decke | Capture/Sizing/Fixtures (u-dach + decke-treppe), `snapshot()`-Adapter — übernommen. |
| `app/studioUi.tsx`, `app/FaehigkeitenNavi.tsx` | **navi-batch0 `2011798`** | Navi-CI: `ZustandBadge` als geteilter Baustein, „aktiv" = Status-Grün `T.ok` (nicht Marke). |
| `domain/scene-document-v2.schema.json` | regeneriert | `npm run schema:hausplaner` = **Union-Schema** (Roof + Ceiling); `schema:check` 0 (keine Drift). |
| Tests (`__tests__/*`) | Union | verriegelte Tests aller Slices im selben Lauf: Platzierung (center==bbox), L/T-Flächen, Gehrung-Dichtheit, Decke/Treppenauge, snapshotLeerMarker. `verschneidungRender` = w3b-2-Fassung („l/t rendern jetzt", ersetzt „l/t leer"). |
| `CLAUDE.md` | dach-ui | Skill-Pflicht-Block (rein additiv, 15 Zeilen, keine Löschung). |
| `public/hausplaner/hausplaner.js` | frisch gebaut | nie gemergt — `npm run build:hausplaner` auf dem vereinten Quellstand. |

## Guardrails / offen
- **Vereinigung grün (nicht Teilmenge):** tsc 0 · schema 0 · **test 676/676** · build 0 — alle Slice-Tests
  im selben Lauf, nichts verloren. Byte-treue Ports (`dachUForm`/`dachVerschneidung`/`gaubeGeometrie`) unberührt.
- **`.claude/skills/`**: liefert der Planner separat (landet additiv im Baum) — noch ausstehend.
- **Nur `auto/`, kein Push, kein main-Merge.** Der Merge ist ein eigener, abnehmbarer Integrations-Slice.
