# Generator-Auftrag — Sicht-Runde vorbereiten (nativ bauen + App am Tip öffnen)

**Rolle:** Generator (Claude Code in VS Code, **nativ auf dem Mac**). **Heimat-App:** `ticket`.
**Ausgestellt von:** Planner, 2026-07-23. **Zweck:** Die zwei sichtbaren Slices für die Evaluator-Browser-
Prüfung lauffähig machen. Bauen/servieren = du; **Browser fährt der Evaluator** (Rollentrennung). Kein Push.
Einbettungsseite: **`/admin/hausplaner/studio`** (Route `hausplaner.studio`, lädt `public/hausplaner/hausplaner.js`).

## Warum nativ
`build:hausplaner` läuft auf der ARM-Geräte-VM nicht (`@rollup/rollup-linux-arm64-gnu`-Bug) — **auf dem Mac
nativ schon**. Nur der native Build erzeugt den aktuellen Bundle, den die Studio-Seite lädt.

## Runde 1 — U-Dach-Optik @ `4b8eb04` (Branch `auto/hausplaner-w3b-2`)
1. Sicherstellen: `git rev-parse HEAD` == `4b8eb04` auf `auto/hausplaner-w3b-2` (kein checkout nötig, ist der Tip).
2. `npm run test:hausplaner` → **638/638** als Beleg in die Meldung kopieren (schließt L/T/U-Auflage 1).
3. `npm run build:hausplaner` → **Exit 0** (nativ). Bundle `public/hausplaner/hausplaner.js` frisch.
4. Lokalen App-Server starten (bestehender Weg, z. B. `php artisan serve` bzw. euer valet/herd) und
   `/admin/hausplaner/studio` öffnen.
5. **U sichtbar machen:** im Studio eine Kontur zeichnen, Dach aufsetzen, `roofType = u-shape` + `anbau`-Maße
   setzen (Länge/Breite, für U genügen `length/width`) — damit die U-Flächen aus `uFormFlaechen` real
   rendern. Kurze Szene reicht; keine Persistenz nötig (Scratch).
6. Meldung an den **Evaluator**: „4b8eb04 gebaut (Exit 0), 638/638, Studio offen mit U-Szene" → er fährt die
   3-Viewport-Sicht (1440/1024/375) und prüft **Lage/Orientierung** der U-Form (Geometrie ist test-belegt).

## Runde 2 — Batch-0-Navi-Optik @ `c553fbc` (Branch `auto/hausplaner-navi-batch0`)
1. `git checkout auto/hausplaner-navi-batch0` (Tip `c553fbc`). Arbeitsbaum vorher sauber (kein Beifang).
2. `npm run test:hausplaner` → grün (inkl. Registry-Guard-Test AP-E) als Beleg kopieren.
3. `npm run build:hausplaner` → **Exit 0** (nativ).
4. App-Server, `/admin/hausplaner/studio` öffnen — die **FaehigkeitenNavi** ersetzt hier die Attrappe.
5. Meldung an den **Evaluator**: „c553fbc gebaut, Studio offen" → er fährt die 3-Viewport-Sicht + die vier
   Fachagenten + Token-grep gegen `evaluator-auftrag-faehigkeiten-navi-optik.md`.

## Guardrails
Kein `--force`, **kein Push, kein main-Merge/Deploy**. Nur bauen/servieren + Belege liefern; das visuelle
Urteil ist der Evaluator. Nach beiden Runden: Ballbesitz Evaluator (zwei Voten). Zwischen den Runden Baum
sauber halten (`git status` prüfen, kein ungewolltes Adden).

## UPDATE 2026-07-23 — Reihenfolge
- **Runde 1 (U-Dach @ `4b8eb04`) ist JETZT dran** — keine Codeänderung offen, nur bauen/servieren + 638-Beleg.
- **Runde 2 (Navi) verschiebt sich auf den POST-FIX-Tip** von `navi-batch0` (nach `generator-auftrag-navi-
  batch0-fix.md`: Tokenisierung `T` + Guard-Test + Cleanup). Nicht `c553fbc` browsern — der ändert sich gleich.

## UPDATE 2026-07-23 (2) — Runde-2-Tip + Repositionierung
- **Batch-0-Fix-Tip = `4198561`** (navi-batch0; Tokenisierung + Export-Guard + fachIcon-Cleanup, vom
  Evaluator statisch grün). Runde 2 (Navi-Optik) läuft an **diesem** Tip.
- **Repositionierung nötig:** aktueller Baum steht auf `4b8eb04` (w3b-2). Für Runde 2 den Baum auf
  `auto/hausplaner-navi-batch0` (`4198561`) stellen (`git checkout` durch Generator/Yama, nicht Evaluator) +
  nativ bauen → Evaluator browst die Navi-Optik (3 VP) + fährt die 639-Gates selbst.
- **U-Optik (Runde 1):** an `4b8eb04` NICHT erreichbar (roofType-Select ohne u-shape). Wird prüfbar, sobald
  der Dach-UI-Slice (`generator-auftrag-dach-ui-formen-anbau.md`) durch ist — dann browst der Evaluator U.
