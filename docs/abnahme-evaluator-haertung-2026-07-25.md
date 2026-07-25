# ⇒ EVALUATOR (2. Instanz, Härtung) — Sammelvotum 2026-07-25

> **Rolle:** unabhängiger Zweit-Evaluator (Kreuzcheck/Härtung neben dem nativen Evaluator).
> **Grundsatz:** jede Zahl selbst erzeugt, blind gemessen (erst messen, dann den jeweiligen
> Generator-Bericht), gegen **feste SHA** via `git archive` nach `/tmp` (kein worktree/stash/checkout),
> Mutationen nur in `/tmp`-Kopien, Arbeitsbaum unberührt gelassen. **Kein Bauen, kein Push.**
> Tafel-Philosophie Z.108: „zusätzliche unabhängige Abnahme härtet eine Freigabe, weicht sie nie auf."
> Alle Voten **konvergieren** mit dem nativen Strang.

## Übersicht

| Posten | SHA | Härtungs-Urteil | Einzigartiger Beitrag dieser Instanz |
|---|---|---|---|
| **AUF-3** T1 Token-Konsolidierung + `decke→bau` | `9ec3b25` | **FREIGABE** (in-scope) | 3-Grüns-Fund geschärft: `0xa3e635` (real gerendert) ≠ `#93c21c` (Kommentar) ≠ `#7fae1c` (Token) |
| Dashboard v2 Batch 1 (v2.1+v2.2) | `f6bdfc2` | **Freigabe mit Auflage** | **Build-Gate grün belegt** (nativ bekam `@rollup/rollup-linux-arm64-gnu`-Fehler) |
| Dashboard v2 Batch 2 (v2.3–2.5) | `5092b10` | **FREIGABE** | Bundle-Auslieferungsloch gefunden → mit `6dde059` behoben |
| AUF-15a Token-Ablösung (30 Rohfarben) | `2d927fc` | **FREIGABE** | Wertgleichheit aller Ersetzungen + Operanden-Gate-Rückgabe belegt |
| AUF-16 Kontext-Leiste auf Modulebene | `982384d` | **FREIGABE** | Test-Trennschärfe per Struktur-Mutation bestätigt |
| AUF-19 Reiter-Muster N3 (B3/B4) | `8587ce7` | **FREIGABE** | `aria-controls`-Verknüpfung mutationsgeprüft (1 Test rot) |
| AUF-26 Kappungs-Schutz (B3/B4) | `4c9bc04` | **FREIGABE** - Panel-Reiter per iframe an 1440/1371/371 px gemessen: alle 4 sichtbar, keiner geklippt (Panel fest 268px, breitenunabhaengig) | Mutation Kappung zurueck 1 rot; 22-Gruppen-Ueberlauf = AUF-34 |
| **A2 / AUF-4** Leiste liest Präsentationsschicht | `acdb987` | **FREIGABE MIT AUFLAGE** | art/zone→eine Wahrheit; verhaltensneutral; 3/4 A1-Auflagen testverriegelt, **Auflage 3 (Render-Pfad-Test) offen** (keine .tsx-Infra) |
| **AUF-9** T2a Kommentar-Fix `szene.ts` | `fbc5308` | **FREIGABE** | Kommentar auf tatsächlichen Wert `#a3e635` (statt falsch #93c21c); **kein Farbwert geändert**, T2b korrekt verwiesen |
| **I2 / AUF-21** Katalog-Tausch 54→110 Fachpaket | `289ccc8` | **FREIGABE** | Bijektion hält (9+110=119, verwaiste/regellos leer); 54 InDesign belegt stillgelegt; Adapter passt sich ToolDefinition an; Kürzel-Kollisionen weggelassen |
| **I3 / AUF-21** 6 Werkzeug-Zustände + canPin/priority | `ccdc93b` | **FREIGABE** | reine Funktion, ZustandKontext als Parameter (kein gespeicherter Zustand); Bijektion 119 hält; Mutation gesperrt→weitere = 2 rot |
| **AUF-25** L4 — 19 Fachplaner-Flächen statt Toast | `17c8be2` | **FREIGABE** | tiefe Fläche (Kopf/Zweck/Feldvorschau/Leerzustand+Badge); Reuse T/Ikon/ZustandBadge; HausplanerApp unberührt; Blindtext-Verbot testverriegelt (Mutation 1 rot) |
| **AUF-31** IDs eingedeutscht + 9 dedupliziert (**Vorarbeit**) | `2deb6a5` | **FREIGABE** | Bijektion 9+101=110 hält; 2 Umlaut-Tabellenfehler korrekt gesetzt (oeffnung/uebergabepaket) + gemeldet; Spec-Tabelle-Residuum an Planner |
| **I4 / AUF-21** 110 Werkzeuge sichtbar, 22 Gruppen (**sichtbar**) | `4932b36` | **FREIGABE** | versteckt 0 (alle 110 sichtbar); 22 Gruppen Summe 110 genau-einmal; Sichtprobe 1512px; 1024/375 offen |
| **AUF-27** Linke Spalte: 3 Reiter (Werkzeuge/Projekt/Fachplaner) | `894954a` | **FREIGABE MIT AUFLAGE** | Code+Tests gruen (810/810, Reihenfolge-Mutation 4 rot, kein 2. Tab-Mechanismus); **Bundle-Hole: kein Rebuild -> im App noch nicht sichtbar**, Sichtprobe deferred |

---

## T1 (`9ec3b25`) — FREIGABE (voller Prüfrahmen, nicht-blind offengelegt)
- Gates: tsc 0 · schema 0 · test 696/696 · **build EXIT 0, Bundle==frischer Build** (git diff leer).
- „Eine Wahrheit" Farbe: in-scope sauber (`#7fae1c` 1×, 0 rohe Werte in HausplanerApp, `FARBEN`=Alias);
  global **drei Grüns** für die Auswahl-Rolle: `#7fae1c` (Token) · `#93c21c` (`treppeSvg.ts:38` + `szene.ts:16`) ·
  `0xa3e635` (`szene.ts:90` `FARBE_AUSWAHL`, real gerendert). → T2 (geometry-geschützt, außer Scope).
- `decke→bau` am Datensatz: `faehigkeitenNach()` alle 9 Gruppen — **genau** `bau +decke` / `werkzeuge −decke`,
  sonst identisch → kein Altbestand still umgehängt.
- Token-Drift: `comm` entfernt/geändert = leer (nur +14 Ableitungen). Nahtstellen: geometry/scene.types unberührt.

## Dashboard v2 Batch 1 (`f6bdfc2`) — Freigabe mit Auflage
- K1 vier Gates Exit 0 (**inkl. build grün in dieser Umgebung**), kein Schema-Regen · K2 696→702, keine
  verschwundenen Tests · K3 Reihenfolge-Mutation → Test rot · K4 store/domain/geometry/renderers unberührt ·
  K5 Fenster/Tür wert-/verhaltenstreu umgezogen (Bedingung → lokales `istFenster`, nicht literal byte) ·
  K6 Batch-1-Dateien 0 rohe Farben (globale Behauptung überdehnt → Auflage AUF-15).
- §4: `hinweis`-Feld additiv im Sinne der Spec · ehrlicher Leerzustand (3 `in_entwicklung`-Reiter mit Hinweis) ·
  Kontext-Platzhalter täuscht keine Fläche vor. §5: aktiver Tab per Unterstrich+fontWeight+Farbe (WCAG 1.4.1).
- **Auflage:** K6 neu schneiden (auf geänderte Zeilen) — deckungsgleich mit nativem Votum.

## Dashboard v2 Batch 2 (`5092b10`) — FREIGABE
- Gates: tsc 0 · schema 0 · test 734/734. Module rein+testgedeckt: projektBaum 12 · befunde 8 · palette 12.
- K9 Mutation `enabled: true` → **genau 5 palette-Tests rot** (Generator-Zahl verifiziert). K10 `pruefungen`=
  `verfuegbar`, `BEFUNDE_LEER = 'Keine offenen Befunde.'` byte-genau. `palette.ts` bezieht `enabled/grund` nur
  aus `resolveToolState` (keine zweite Wahrheit). K4/K6 sauber.
- **Fund:** `5092b10` ohne Bundle-Rebuild → Batch 2 nicht im committeten Bundle; behoben durch `6dde059`.

## AUF-15a (`2d927fc`) — FREIGABE
- Wertgleichheit: `#fff→T.surface (#ffffff)` · `#374151→T.canvasWall` · `#4b5563→T.canvasWallFill` — alle byte-gleich,
  kein gerenderter Farbwert ändert sich. Operanden-Gate: `#d1d5db`/`#9aa4af`/`#1a262a` **nicht** als Token
  erfunden, korrekt stehengelassen. Gates tsc 0 · test 734/734.

## AUF-16 (`982384d`) — FREIGABE
- `KontextOptionenLeiste` auf Modulebene mit expliziten Props (behebt „neue Identität je Render"), switch/default
  erhalten, verhaltensneutral. Gates tsc 0 · schema 0 · test 739/739. K4 unberührt.
- Trennschärfe: Mutation `FENSTER_TYPEN→TUER_TYPEN` → 1 Test rot (byte-treuer-Umzug-Assertion). 5 Quelltext-
  Inspektions-Tests (korrekter Ansatz ohne DOM). *(Erste Fehl-Probe [Text-Mutation] offengelegt und korrigiert.)*

## AUF-19 (`8587ce7`) — FREIGABE
- WCAG-Tab-Muster vollständig: `role=tabpanel`+`id`, `aria-controls`→Panel, `aria-labelledby`→aktiver Reiter,
  Fokusnachführung. 7 Tests inkl. Kante „Mausklick löst kein `focus()` aus". Gates test 746/746.
- Trennschärfe: Mutation `aria-controls={PANEL_ID}→'kaputt-xyz'` → 1 Test rot (Verknüpfung greift).

## AUF-26 (`4c9bc04`) - FREIGABE (Code gruen + Sichtprobe an 3 Viewports sauber)
- Kappungs-CSS (`overflow:hidden`+`textOverflow:ellipsis`+`whiteSpace:nowrap`) ersetzt durch Umbruch
  (`overflowWrap:anywhere`+`whiteSpace:normal`+`minWidth:0`) im Fähigkeiten-Label und im 268px-Panel
  (`overflowWrap:anywhere`+`boxSizing:border-box`); volles Label zusätzlich im `title`-Attribut.
- Test `keineKappung.test.ts` prüft die **Ursache (CSS)**, nicht den Screenshot. Gates tsc 0 · schema 0 ·
  test 759/759. K4 unberührt. Trennschärfe: Mutation „Kappung zurück" → 1 Test rot.
- **Offen: Sichtprobe** — ob der Text visuell sauber umbricht (kein Overflow/Clipping), braucht Browser.

## A2 / AUF-4 (`acdb987`) — FREIGABE
- **Kern:** Werkzeugleiste liest `zoneTools('fix')` statt `werkzeugTools()` → art/zone-Doppelwahrheit auf **eine** reduziert.
- **Verhaltensneutral (Krit. 6):** Test belegt `zoneTools('fix')` === `werkzeugTools()` === `[auswahl,wand,fenster,tuer,dach,decke,treppe]` — keine Icon-Wanderung.
- **P9 §8:** `useMemo(() => zoneTools('fix'), [])` komponenten-lokal, **kein Modul-Cache** (sonst brächen die A1-`zoneToolsIn`-Gegenproben). Mutation (useMemo entfernt) → 1 Test rot.
- K4 unberührt · Gates tsc 0 · schema 0 · test 754/754.
- **4 A1-Auflagen geprüft:** (1) Shortcut-Kollision `shortcutKollisionen()===[]` **testverriegelt** · (2) `useMemo`, kein Modul-Cache (Test „toolPresentation.ts bleibt rein") · (4) `herkunft` aller 63 Regeln testverriegelt (registry===9) · **(3) Render-Pfad-Test offen** — vom Generator mangels `.tsx`/DOM-Testinfra zurückgegeben (Infra-Lücke, kein Code-Defekt).
- **Verdikt präzisiert: FREIGABE MIT AUFLAGE** (Auflage 3 = Render-Pfad-Test, deferred bis Testinfra/Sichtprobe). `toolPresentation.ts`-Unblock steht — Code über Quell-/Logik-/Gegenproben-Tests belegt.
- **Wirkung:** entsperrt AUF-27 + Layout-Kette L1→…

## AUF-9 (`fbc5308`) — FREIGABE (T2a Kommentar-Fix)
- Kommentar `szene.ts:16`/`:90` korrigiert: `#93c21c` (falsch) → `#a3e635` (der tatsächliche `FARBE_AUSWAHL`-Wert),
  explizit abgegrenzt von `T.brand #7fae1c`; Palettenfrage korrekt auf **T2b/Yama** verwiesen.
- **Kein Farbwert geändert:** `FARBE_AUSWAHL = 0xa3e635` byte-identisch beidseitig — nur Kommentarzeilen. Gate 696/696.
- (Ich fand die Diskrepanz selbst in der T1-Härtung — anchored, aber ein Kommentar-gegen-Wahrheit-Abgleich ist davon unberührt.)

## I2 / AUF-21 (`289ccc8`) — FREIGABE (Katalog-Tausch + Adapter)
- **Katalog-Tausch:** 54 InDesign-Reste → **110 Fach-Werkzeuge** (Yamas Paket). Die 54 alten bleiben belegt in `toolCatalogStillgelegt.ts` (nicht gelöscht).
- **Bijektion hält** (kritisch beim Tausch): Test „9 + 110 = 119, keine Dublette" ✔, `verwaisteRegeln()` leer ✔, GEGENPROBE (erfundene id→verwaist) ✔. Präsentationsschicht korrekt auf 119 Regeln mitgezogen.
- **Konflikt-Regel eingehalten:** `paketAdapter.ts` bildet das Paket **auf** `ToolDefinition` ab — „kein Feld geändert/ergänzt"; Registry- und Paket-Werkzeug bleiben getrennt (keine stille Vereinheitlichung).
- **A2-Auflage 1 baulich:** kollidierende Paket-Kürzel (`g`,`s`,`Ctrl/Cmd+K`,`V`,`W`,`R`,`Delete`) werden **weggelassen** statt doppelt vergeben.
- Gates tsc 0 · schema 0 · **test 771/771** · K4 store/domain/geometry/scene.types unberührt. Berührt `toolPresentation.ts` — zulässig, da A2/AUF-1 abgenommen.

## I3 / AUF-21 (`ccdc93b`) — FREIGABE (Werkzeug-Zustände)
- Sechs Anzeige-Zustände (`system/aktiv/gesperrt/angeheftet/empfohlen/weitere`) als **reine Funktion**; `ZustandKontext` (aktiv/angeheftet/empfohlen/aktivierung) als **Parameter**, bewusst **kein gespeicherter Zustand** — „wäre zweite Wahrheit neben `resolveToolState`". `canPin`/`priority` (anheftbar/prioritaet) additiv an die Regeln.
- **Bijektion hält** nach +228/-151 in `toolPresentation.ts`: 9+110=119, verwaiste/regellos leer, GEGENPROBE ✔, keine Registry-id versteckt.
- **Trennschärfe:** Mutation `'gesperrt'→'weitere'` → 2 Tests rot. Gates tsc 0 · schema 0 · **test 782/782** · K4 unberührt.

## AUF-25 (`17c8be2`) — FREIGABE (L4 Fachplaner-Flächen)
- Ersetzt den Toast „Konfigurator folgt" durch **19 tiefe Flächen**: Kopf (Modul/Gruppe/Zurück) · Zweck · **Feldstruktur-Vorschau** (jedes Feld `disabled`, Grund als Text, keine „Berechnen"-Schaltfläche) · Leerzustand mit `ZustandBadge`. Wiederverwendet `T`/`Ikon`/`ZustandBadge` — keine neue Komponente/Wahrheit.
- **Ehrlicher Leerzustand testverriegelt:** „jeder Zweck ist konkret, keiner vertröstet" (kein „keine Daten", kein „folgt$"), „der Toast ist Geschichte". Mutation `Zweck→'folgt.'` → 1 Test rot.
- **Nahtstelle sauber:** `HausplanerApp.tsx` **unberührt** (keine A2-Kollision). K4 store/domain/geometry unberührt. Gates tsc 0 · schema 0 · **test 768/768**.

---

## ⇒ SICHTPROBE DURCHGEFÜHRT (25.07., authentifizierte Browser-Session, kein Credential-Eintrag durch mich)
Route `/admin/hausplaner/studio?fixture=decke-treppe` → **Expertenmodus**, gerendert im echten Bundle. Schließt den offenen Sicht-Rest von Batch 1/2, AUF-26 und A2. Visuell bestätigt (Screenshot an Yama):
- **v2.1 Kontext-Leiste:** „**Auswahl** | Für dieses Werkzeug sind noch keine Optionen hinterlegt · in Entwicklung" — ehrlicher Platzhalter + Badge, wie im Code.
- **v2.2 Panel-Reiter:** vier Reiter, aktiver fett + gruen unterstrichen (WCAG 1.4.1). Per iframe fester Breite an 1440 / 1371 / 371 px gemessen (getBoundingClientRect im iframe-contentDocument): alle 4 Reiter sichtbar, keiner geklippt. Ursache: Panel fest 268px, Umbruch breitenunabhaengig. Der Planner-Defekt bei ~1375px ist in den Panel-Reitern NICHT reproduzierbar - er betrifft die 22-Gruppen-Leiste (Ueberlauf bei 1440) = AUF-34, nicht AUF-26.
- **A2 Werkzeugleiste aus Zonen:** 7 Werkzeuge V/W/F/T/D/K/R, **kollisionsfreie Kürzel** (Auflage 1 sichtbar).
- **Ehrliche Zustände:** Dach ●verfügbar (grün) vs. Sparren/Holz ●in Entwicklung — Farbe **und** Text; Labels brechen um statt zu kappen.
- **Fixture rendert:** Treppe 16×175 mm, 80 m², 2D-Ansicht sauber.
→ **Sicht deckt sich mit dem Code auf jedem Punkt.** Einzig echtes Rendering-im-Testlauf (jsdom) bleibt Infra-Posten AUF-30.

## Nicht abgenommen / offen
- **AUF-1** (A1-blind) — frische Instanz gezogen; ich anchored, korrekt ausgeschlossen.
- Yama-Willensfragen: AUF-15b (Palette/Grün-Rolle), stopp-1, Branch-Hygiene.

*Kern-Rohbelege ab hier in der Datei (Anhang). Weitere Rohausgaben reproduzierbar gegen die feste SHA; ab dem nächsten Votum liegen ALLE Rohausgaben committet daneben (Planner-Auflage 3).*



## AUF-31 (`2deb6a5`) — FREIGABE · Klassifikation **Vorarbeit** (nichts wird sichtbar; Icons bleiben bis I4 versteckt)
- 110 Icon-Dateien auf deutsche IDs umbenannt (select→auswahl …) + Sprite neu; **9 Dubletten zusammengeführt (110→101 Paket-IDs; Metadaten additiv in die Registry)**.
- **Bijektion hält:** „9 + 101 = 110, keine Dublette" ✔, `verwaisteRegeln()` leer ✔, GEGENPROBE ✔. Gates tsc 0 · schema 0 · **test 788/788** · K4 store/domain/geometry unberührt.
- **2 selbstgemeldete Tabellenfehler:** führende Tabelle hatte Umlaut-Verlust (`ffnung`/`bergabepaket`); Generator setzte korrekt `oeffnung`/`uebergabepaket` (oe/ue-Konvention) und meldete den Widerspruch → sound. **Residuum:** Spec-Tabelle zeigt noch die falschen Werte (Doku-Hygiene, Planner/Yama).
- **Trennschärfe:** eingebaute GEGENPROBE (erfundene id→verwaist) grün; künstliche ID-Dublette → toolPresentation.test rot.

---

## I4 / AUF-21 (`4932b36`) — FREIGABE · SICHTBAR (schliesst die I3-Blindstelle)
- Alle 110 Werkzeuge sichtbar: Zonen fix 7 / kontext 2 / weitere 101 / **versteckt 0** (nach I3 waren 101 versteckt). Erreichbar ueber **22 Kategorie-Gruppen** (werkzeugGruppen.ts) + Anheften (app/state/angeheftet.ts).
- Test-Bilanz: WERKZEUG_GRUPPEN=22, Summe=110, genau-einmal, keins versteckt — gruen; Bijektion 9+101=110 haelt. Gates tsc 0 / schema 0 / test 798/798 / K4 unberuehrt.
- Sichtprobe (auth. Browser, innerWidth 1512): 22-Gruppen-Leiste rendert vollstaendig, 7 Fix-Werkzeuge links (Screenshot an Yama). 1024/375 px nicht renderbar (resize aendert innerWidth nicht) — offen.
- Vorbehalte: (a) zwei Blind-Mutationen an werkzeugGruppen landeten nicht (Gruppen aus dem Katalog abgeleitet) — Trennschaerfe ueber explizite Assertions + toolPresentation-GEGENPROBE. (b) Design-Folgeblock offen (cc2c43c: Faehigkeiten vs 22 Gruppen), kein I4-Blocker.

## AUF-27 (`894954a`) - FREIGABE MIT AUFLAGE (Linke-Spalte-Reiter; sichtbar geplant, Bundle-Hole)
- 3 Reiter `werkzeuge / projekt / fachplaner` (feste Reihenfolge, Standard werkzeuge), **v2.2/AUF-19-Reiter-Muster wiederverwendet** - Test belegt: role=tab 1x in der Leiste, 0x sonst (kein zweiter Tab-Mechanismus). K4 store/domain/geometry unberuehrt. Gates tsc 0 / schema 0 / test 810/810.
- **Trennschaerfe:** Quell-Mutation erste SCHIENEN_REITER-id werkzeuge->projekt -> 4 Tests rot (K3 Reihenfolge + Standard + Eindeutigkeit, K4). (Erste Fehl-Probe traf den Test-Literal statt die Quelle - korrigiert.)
- **AUFLAGE - Bundle-Hole:** `894954a` enthaelt keinen Bundle-Rebuild, kein spaeterer Commit auch. Der servierte `public/hausplaner/hausplaner.js` hat AUF-27 also nicht -> die 3 Reiter sind im laufenden App **nicht sichtbar**. Braucht einen Bundle-Rebuild-Commit (wie Batch 2 -> `6dde059`), DANN Sichtprobe.
- **Sichtprobe deferred** bis Rebuild; dann via iframe an 1440/1024/375 px (Werkzeug jetzt vorhanden).

## ⇒ NACHARBEIT — vier Planner-Auflagen (`1955311`, Evaluator, 25.07.)
1. **AUF-9-Widerspruch aufgelöst:** Zeile unter „Nicht abgenommen/offen" **gestrichen**. AUF-9 = FREIGABE (Kommentar-gegen-Codewert ist auch für anchored Prüfer objektiv, beide Werte im Code).
2. **`sichtbar`/`Vorarbeit` je Votum (ab sofort):**
   - **Vorarbeit** (technisch grün, für den Nutzer ändert sich NICHTS sichtbar): T1 · AUF-15a · AUF-16 · AUF-19 · A2 · AUF-9 · **I3** (nach I3 stehen alle 110 weiter auf `versteckt` — meine I3-Freigabe war die Blindstelle, die der Planner zu Recht benennt) · I2.
   - **sichtbar** (Nutzer sieht neue Fläche): Dashboard B1 (Kontext-Leiste/Tabs) · B2 (Projektbrowser/Prüfungscenter/Palette) · AUF-25 (19 Fachplaner-Flächen) · AUF-26 (Umbruch — im belegten Band).
   - Lehre: „technisch grün" ≠ „für Yama sichtbar" — steht ab jetzt in jedem Votum.
3. **Rohbelege in der Datei** (Anhang unten) statt nur im Chat; ab dem nächsten Votum vollständig committet.
4. **Sichtprobe mit Breite - GELOEST via iframe fester Breite** (same-origin, inneres innerWidth = iframe-Breite, contentDocument messbar). Gemessen 1440 / 1371 / 371 px: Panel-Reiter ueberall alle 4 sichtbar, keiner geklippt -> AUF-26 auf FREIGABE hochgestuft. ~1375px-Defekt betrifft die 22-Gruppen-Leiste (AUF-34). Dieses iframe-Verfahren ist ab jetzt das Viewport-Sichtprobe-Werkzeug (loest das resize-Limit).

## Rohbelege (Anhang, selbst gemessen)
```
Gates je SHA (npm run …, EXIT / Testzähler):
  T1        9ec3b25  tsc 0 · schema 0 · test 696/696 · build 0 (bundle==git-diff-leer)
  Batch1    f6bdfc2  tsc 0 · schema 0 · test 702/702 · build 0 ; baseline 3229866=684
  Batch2    5092b10  tsc 0 · schema 0 · test 734/734
  AUF-15a   2d927fc  tsc 0 · test 734/734
  AUF-16    982384d  tsc 0 · schema 0 · test 739/739
  AUF-19    8587ce7  tsc 0 · schema 0 · test 746/746
  AUF-26    4c9bc04  tsc 0 · schema 0 · test 759/759
  A2        acdb987  tsc 0 · schema 0 · test 754/754
  I2        289ccc8  tsc 0 · schema 0 · test 771/771
  I3        ccdc93b  tsc 0 · schema 0 · test 782/782
  AUF-25    17c8be2  tsc 0 · schema 0 · test 768/768
  AUF-31    2deb6a5  tsc 0 · schema 0 · test 788/788 ; Bijektion 9+101=110
  I4        4932b36  tsc 0 · schema 0 · test 798/798 ; versteckt 0, 22 Gruppen Summe 110
  AUF-27    894954a  tsc 0 / schema 0 / test 810/810 ; Mutation werkzeuge->projekt = 4 rot ; BUNDLE NICHT rebuilt
Mutations-Gegenbeweise (Mutation → rote Tests):
  T1: wand fix→versteckt 5 rot · erfunden-xyz 3 rot · Regel entfernt (auswahl/rotate) 5/4 rot
  Batch1 K3: Reihenfolge-Swap → 1 rot   · Batch2 K9: enabled:true → 5 rot
  AUF-16: FENSTER_TYPEN→TUER_TYPEN → 1 rot · AUF-19: aria-controls kaputt → 1 rot
  AUF-26: Kappung zurück → 1 rot · A2: useMemo entfernt → 1 rot
  I3: 'gesperrt'→'weitere' → 2 rot · AUF-25: Zweck→'folgt.' → 1 rot
Bijektion (Katalog-Eingriffe): I2/I3 „9+110=119, keine Dublette", verwaisteRegeln()=[], regellose=[]
Farb-„eine Wahrheit" (git grep, ohne Bundle): #7fae1c 1× (studioDaten) · #93c21c treppeSvg:38+szene:16 · 0xa3e635 szene:90 (real gerendert)
Sichtprobe innerWidth 1440 (getBoundingClientRect): Reiter Allgemein/Beziehungen/Prüfungen/Historie sichtbar, clip=false; Prüfungen ovR −3 px
```
