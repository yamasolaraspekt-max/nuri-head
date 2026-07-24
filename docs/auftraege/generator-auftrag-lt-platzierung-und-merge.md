# Generator-Auftrag — L/T-Render-Platzierung: Anker aus 66ad448 in die EINE w3b-2-Schleife (SSOT)

**Status:** Fach-Freigabe deckt es ab (gleicher Anker wie U, von Yama „mein ja" freigegeben — KEINE neue
Fachentscheidung). Nur `auto/`-Branch, kein Push/Merge (Tor 2 = Yama).
**Auslöser:** Evaluator-Vorab-Befund (statisch, `w3b-2 @ f0d02f4`): `verschneidungsFlaechen` platziert u UND
l/t über `polygonSchwerpunkt(roof.polygon)` → L/T erbt die U-Fehlplatzierung. Am Code belegt (dachMesh.ts Z.151).

## Linsen-Befund (Fachprüfer-Panel, belegt)
- **Dach (dachdeckermeister):** Traufe/Kehle der L/T-Verschneidung müssen auf der Kontur sitzen; Schwerpunkt-
  Anker versetzt sie → rot. Anker muss Footprint-Bezug sein (wie die Wände in `platzierung.ts`).
- **Architektur (software-architekt):** `66ad448` (U-Fix, u-only) ist NICHT in w3b-2 (Basis `4b8eb04`); beide
  Branches ändern `verschneidungsFlaechen` → Merge-Konflikt + Gefahr zweier Anker-Wahrheiten. w3b-2 hat die
  bessere Struktur (u+l/t in EINER `quelle`-Schleife). Der 66ad448-Anker ist **quellen-agnostisch** (misst die
  projizierten Flächen selbst) ⇒ dieselbe Logik platziert u UND l/t aus EINER Wahrheit.

## Auflage (surgical, in `renderers/three-d/dachMesh.ts` → `verschneidungsFlaechen`, w3b-2)
1. `const c = polygonSchwerpunkt(roof.polygon)` + die Transform-Zeile `x: c.x + (ex*rx+ez*qx)*M, y: c.y + …`
   durch die **Footprint-Zentrierung aus 66ad448** ersetzen — angewandt auf die gemeinsame `quelle`:
   - Engine-Footprint-Bbox über ALLE `quelle`-Flächen: `grund(f,uv) = {ex,ez}` (nur x/z-Projektion),
     `engCx=(exMin+exMax)/2`, `engCz=(ezMin+ezMax)/2`.
   - Wand-Footprint: `ziel = polygonBbox(roof.polygon)` (`{cx,cy}` = Bbox-Mitte). `polygonBbox` existiert auf
     w3b-2 NICHT → als kleine reine Funktion mitliefern (oder inline min/max), wie auf dach-ui.
   - Transform: `dex=ex-engCx; dez=ez-engCz; w.x=ziel.cx+(dex*rx+dez*qx)*M; w.y=ziel.cy+(dex*ry+dez*qy)*M`.
   - `polygonSchwerpunkt` danach ungenutzt → entfernen (oder als `footprintZentrum` neu, falls anderswo nötig —
     prüfen: auf w3b-2 nur Z.151). uFormFlaechen/ltFormFlaechen/dachVerschneidung BYTE-TREU lassen (kein Beifang).
2. **L/T-Platzierungs-Test** analog `dachUFormPlatzierung.test.ts`, aber gegen eine l-shape- UND t-shape-Fixture
   (in `studioFixtures.ts` ergänzen, falls nicht vorhanden — additiv): Dach-Grundriss-Zentrum == Wand-Bbox-Mitte
   (mit Gegenprobe Schwerpunkt≠Mitte), Dach-Bbox ⊇ Wand-Bbox+Überstand. (Kein Innenhof bei L/T — die Kerbe-Prüfung
   ist U-spezifisch; für L/T stattdessen: der einspringende Winkel bleibt gedeckt/plausibel.)

## Abnahme (Evaluator, nach Bau)
- Gates selbst: tsc 0 · schema 0 (keine Modell-Änderung ⇒ Schema unberührt) · test grün inkl. neuer L/T-Tests.
- Sicht (`fixture=l-dach`/`t-dach`&capture=1): L/T sitzt bündig auf dem Grundriss, Kehle auf der Kontur.
- Regression: u-shape bleibt korrekt platziert (der Anker ist jetzt gemeinsam — u-Test 649 muss grün bleiben).

## Merge-Hinweis (Architektur → Yama, Tor 2)
Da beide Branches `verschneidungsFlaechen` anfassen: die **reparierte w3b-2-Version wird die kanonische**
(unified `quelle` + gemeinsamer Anker). Beim Zusammenführen von dach-ui (66ad448) und w3b-2 ist in DIESER
Funktion die w3b-2-Fassung zu nehmen — sie subsumiert den U-Fix. Bewusste Merge-Entscheidung, kein Auto-Merge.
