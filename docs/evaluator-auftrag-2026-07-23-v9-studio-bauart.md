# Evaluator-Auftrag — v9-Studio + Bauart-Icon-Raster (2026-07-23)

**Rolle:** Evaluator (unabhängig, Gegen-Beweis — nicht die Behauptung des Generators nachbeten).
**Prinzip:** Grün nur mit selbst erhobenem Beleg, gemessen am echten Rendern/Bundle, nicht am Vorsatz.

## Scope — zwei Scheiben, getrennt bewerten

### Scheibe A — `970f0cc` Bauart-Icon-Raster (Fenster/Tür)
- 24 Fenster- + 24 Tür-Premium-SVGs unter `public/hausplaner/icons/{fenster,tuer}/`.
- `geometry/oeffnungsBauarten.ts`: Katalog (id/datei/label/oeffnungsArt), GETRENNT von den
  Zeichen-Vorlagen in `oeffnungsTypen.ts` (Standardmaße, unverändert — bitte Diff prüfen: keine).
- `domain/scene.types.ts` + `validation.ts`: `produkt.typ` additiv (zod `.strict()` erweitert).
- Panel: Icon-Raster im Fenster/Tür-Zweig, grün=gewählt, leitet Öffnungsart ab.

### Scheibe B — `14c4d0e` Studio v9 (Modus-Shell + Start + Guided + ConfigWizard + Treppen)
- `app/HausplanerStudio.tsx`: Kopf mit Modus-Umschalter + persistente Navigation; Bühne = Start /
  Guided / Experte (volle `HausplanerApp`).
- `app/StartView.tsx`, `app/GuidedView.tsx`, `app/ConfigWizard.tsx`, `app/studioDaten.ts`, `studioUi.tsx`.
- `geometry/treppenBauarten.ts` + 20 Treppen-SVGs (`icons/treppe/`); `geometry/treppeObjekt.ts`
  `typ` additiv durch die Parameter-Bridge.
- `app/HausplanerApp.tsx`: optionales `imStudio`-Flag (blendet eigene Markenzeile aus) — sonst unverändert.
- `main.tsx` mountet `HausplanerStudio`.

## Gegen-Beweis-Punkte (bitte selbst erheben)
1. **Additivität**: Öffnung/Treppe OHNE `typ` bleibt gültig; MIT `typ` gültig; Bridge round-trip
   (`treppeZuParametern`→`parametereZuTreppe`) erhält `typ` und übrige Felder. Eigener Test.
2. **Trennung der Kataloge**: `oeffnungsTypen.ts` (Vorlagen) ↔ `oeffnungsBauarten.ts` (Icons) —
   keine Namenskollision, `oeffnungsTypen.ts` unverändert ggü. Vor-970f0cc.
3. **Asset-Pfad**: Icons laden zur Laufzeit über `import.meta.url` (`icons/<art>/<datei>`), auch
   unter Subpfad. Am echten Rendern prüfen (Panel + ConfigWizard).
4. **Modus-Shell**: Start/Guided/Experte umschaltbar; Experte rendert die volle App OHNE doppelte
   Markenzeile (imStudio); 0 Pageerrors in allen Modi.
5. **ConfigWizard**: Bauart-Raster zeigt echte Premium-Icons, Live-Vorschau folgt der Wahl;
   „Übernehmen" ist bewusst noch Scaffold (Toast) — KEINE Modell-Schreibbehauptung prüfen.
6. **Reproduzierbarkeit**: Bundle aus gepinnten Deps (vite 6.4.1) deterministisch; `tsc` Exit 0;
   Suite grün (Generator: 284/284, +3 neu: treppenBauarten).

## Generator-Belege (zur Gegenprüfung, nicht als Beweis)
- Headless-Renders: Start/Guided/Experte, ConfigWizard „Fenster konfigurieren", Treppen-Bauart-Panel —
  alle mit echten Icons, grün=gewählt, Öffnungsart abgeleitet (Fenster: dreh-kipp→dreh→fest).
- tsc Exit 0; 284/284 Tests; Bundle-md5 (Generator) `b00c042e` (@14c4d0e).

## Governance-Randbefund (offen deklariert)
- One-Writer: `970f0cc` wurde committet, WÄHREND die Fenster-Wellen-Review lief; danach `14c4d0e`
  auf denselben `HausplanerApp.tsx` gestapelt. Auf ausdrückliche Anweisung des Auftraggebers
  („mach es live, arbeite durch") priorisiert. Beide Scheiben liegen jetzt als abgeschlossene
  Commits vor — bitte als solche unabhängig prüfen.
- Ballbesitz nach Review: Yama.

## Nächste Scheibe (noch NICHT gebaut, nicht hier prüfen)
- ConfigWizard „Übernehmen" → echter Modell-Schreibpfad (ADD/UPDATE_NODE) bzw. ConfiguratorPackage.
- Guided-Schrittstatus aus dem echten Szenenmodell ableiten (derzeit präsentativ).
