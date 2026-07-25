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
| **AUF-34** Arbeitsbereiche: 15 Themen / 5 Bereiche | `8b2b9e6` | **FREIGABE MIT AUFLAGE** | Bilanz 15 Themen/110 Werkzeuge ohne Verlust, 7 durchgaengig (leere supportedWorkspaces); Mutation Thema-entfernt 4 rot; **3. Bundle-Hole -> Sichtprobe deferred** |
| **AUF-37** Bundle-Rebuild (liefert AUF-27+34 aus) | `91d9592` | **FREIGABE** | committeter Bundle == frischer Build aus HEAD (byte-identisch); AUF-27+AUF-34 jetzt serviert. **Loest die Bundle-Holes von AUF-27 und AUF-34** |
| **AUF-36** Funktionsvertrag: 110 Werkzeuge sagen warum gesperrt (**sichtbar**) | `d106445` | **FREIGABE** | 3 Grenzen (kein 2. Aktivierungs-Engine/Ausfuehrungsschicht/erfundener Kontext); 12 Vorbedingungen + 5 ehrlich-unerfuellbar; Mutation Grund-verfaelscht 1 rot; Sichtprobe: Grund-Text rendert; 853/853 |

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

## AUF-34 (`8b2b9e6`) - FREIGABE MIT AUFLAGE (Arbeitsbereiche; sichtbar geplant, Bundle-Hole #3)
- 15 Themen auf 5 Arbeitsbereiche (statt 22 Kategorien nebeneinander). 7 durchgaengige Themen in jedem Bereich, sauber als **leere supportedWorkspaces** (nicht 5 Eintraege). Bilanz 15 Themen / 110 Werkzeuge ohne Verlust/Dublette. K4 store/domain/geometry unberuehrt. Gates tsc 0 / schema 0 / test 830/830.
- **Trennschaerfe:** ein durchgaengiges Thema (01-grundbedienung) aus DURCHGAENGIGE_THEMEN entfernt -> 4 Tests rot (K3 7-durchgaengig, K5' Bilanz 15/110, resolveToolState-Konsistenz, Kante 1).
- **AUFLAGE - Bundle-Hole #3:** `8b2b9e6` ohne Rebuild -> die 5-Bereiche-Leiste ist im laufenden App nicht sichtbar. Braucht Rebuild-Commit (wie AUF-37 fuer AUF-27). Die zwei Sichtprobe-Kriterien (kein Ueberlauf bei 1371, keine Wortumbrueche im Menue) pruefe ich per iframe NACH dem Rebuild.
- Muster: 3. Bundle-Hole (Batch 2 / AUF-27 / AUF-34) trotz neuer Bundle-Regel (0f06634) - an Planner.

## AUF-37 (`91d9592`) - FREIGABE + Sichtprobe-Nachtrag AUF-27 / AUF-34
- **Bundle-Rebuild ehrlich:** `91d9592` ist reiner Bundle (199+/199-); **committeter Bundle == frischer Build aus HEAD-Quellen (diff byte-identisch)** -> AUF-27 und AUF-34 sind jetzt wirklich ausgeliefert. Der Generator hat die Ursache des Musters benannt (Kriterium 'null Zeilen in public/*' erzeugte das Loch strukturell).
- **AUF-27-Auflage AUFGELOEST (Sichtprobe, iframe):** linke Reiter Werkzeuge/Projekt/Fachplaner rendern bei **1371 UND 375 px**. -> AUF-27 jetzt voll FREIGABE (sichtbar).
- **AUF-34-Auflage AUFGELOEST (Sichtprobe, iframe):** ARBEITSBEREICH-Leiste = 5 Bereiche; bei **1371 px eine Zeile, kein Ueberlauf**; bei **375 px sauberer Umbruch auf 3 Zeilen, volle Woerter, kein Wortumbruch**; Kategorie-Menue pro Bereich gefiltert (kein 22-ueber-3-Zeilen mehr). Die 2 Kriterien (kein Ueberlauf 1371, keine Wortumbrueche) erfuellt. -> AUF-34 jetzt voll FREIGABE (sichtbar).
- Damit sind alle drei Bundle-Holes (Batch2/AUF-27/AUF-34) geschlossen und die zwei sichtbaren Layout-Slices im Browser belegt (Screenshots an Yama).

## AUF-36 (`d106445`) - FREIGABE, SICHTBAR (Funktionsvertrag: 110 Werkzeuge sagen warum gesperrt)
- **3 Grenzen gewahrt:** nutzt `resolveToolState` (keine 2. Aktivierungs-Engine), `commandId` als Metadatum (keine 2. Ausfuehrungsschicht), 5 heute unerfuellbare Vorbedingungen mit ehrlichem Grund (kein erfundener Kontext). `werkzeugVertrag.ts` (+1397) + `vorbedingungen.ts` (+201).
- **Test prueft den GRUND, nicht nur Boolean:** je Vorbedingung ein erfuellter + ein verletzter Fall. Mutation: einen Grund-Text verfaelscht -> 1 Test rot. 12 Vorbedingungen alle zugeordnet (keine Zeile 'sonstige'), Bijektion 9+101=110. Gates tsc 0 / schema 0 / test 853/853, K4 unberuehrt.
- **Bundle:** eigener Rebuild-Commit (`368f2d7`, grep-Beleg) - der Generator folgt jetzt der Bundle-Regel aus meinem Fund.
- **Sichtprobe (iframe 1440, auth):** Kategorie-Menue oeffnet, Lock-Grund-Text rendert (hatVoraussetzungText true, 37 Elemente mit Grund). Kleine Notiz: aria-disabled=0 (Deaktiviert-Zustand ueber anderen Mechanismus; Grund-Text ist da = Kriterium erfuellt).

## ⇒ NACHARBEIT — vier Planner-Auflagen (`1955311`, Evaluator, 25.07.)
1. **AUF-9-Widerspruch aufgelöst:** Zeile unter „Nicht abgenommen/offen" **gestrichen**. AUF-9 = FREIGABE (Kommentar-gegen-Codewert ist auch für anchored Prüfer objektiv, beide Werte im Code).
2. **`sichtbar`/`Vorarbeit` je Votum (ab sofort):**
   - **Vorarbeit** (technisch grün, für den Nutzer ändert sich NICHTS sichtbar): T1 · AUF-15a · AUF-16 · AUF-19 · A2 · AUF-9 · **I3** (nach I3 stehen alle 110 weiter auf `versteckt` — meine I3-Freigabe war die Blindstelle, die der Planner zu Recht benennt) · I2.
   - **sichtbar** (Nutzer sieht neue Fläche): Dashboard B1 (Kontext-Leiste/Tabs) · B2 (Projektbrowser/Prüfungscenter/Palette) · AUF-25 (19 Fachplaner-Flächen) · AUF-26 (Umbruch — im belegten Band).
   - Lehre: „technisch grün" ≠ „für Yama sichtbar" — steht ab jetzt in jedem Votum.
3. **Rohbelege in der Datei** (Anhang unten) statt nur im Chat; ab dem nächsten Votum vollständig committet.
4. **Sichtprobe mit Breite - GELOEST via iframe fester Breite** (same-origin, inneres innerWidth = iframe-Breite, contentDocument messbar). Gemessen 1440 / 1371 / 371 px: Panel-Reiter ueberall alle 4 sichtbar, keiner geklippt -> AUF-26 auf FREIGABE hochgestuft. ~1375px-Defekt betrifft die 22-Gruppen-Leiste (AUF-34). Dieses iframe-Verfahren ist ab jetzt das Viewport-Sichtprobe-Werkzeug (loest das resize-Limit).

## AUF-35a - Markieren / Mehrfachauswahl (35fbfde, Bundle 4dce1cc) - FREIGABE

**Reihenfolge:** erst blind gegen 35fbfde gemessen, dann Generator-Bericht gelesen.
**Klasse: sichtbar** (interaktive Auswahl) - Sichtprobe Teil der Abnahme.

- **Gates selbst gefahren:** tsc 0 / schema:check 0 (Schema unveraendert) / test **874/874**.
- **Umfang (git show --stat 35fbfde):** 8 Dateien - markieren.test.ts (+203),
  auswahlModus.ts (+98, aufloeseAuswahlmodus), auswahlDarstellung.ts, auswahlUebersicht.ts,
  trefferSuche.ts (Hit-Test), HausplanerApp.tsx (+93), toolRegistry.ts (+5),
  store/hausplanerStore.ts (+31).
- **K4 / Store-Beruehrung geprueft (erste Slice, die den Store anfasst):** die +31 erweitern
  NUR den bestehenden Auswahl-UI-State - selectNodes bekommt primaerId (Primaerobjekt der
  Auswahl), ausdruecklich 'Kein zweiter Auswahlzustand'. **Kein SceneDocument, kein Schema**
  (schema:check gruen, git status leer). Additiv, keine zweite Wahrheit. -> K4 sauber.
- **Auswahlmodi (plattform-bewusst):** {}=replace, shift=add, ctrl/meta=toggle, alt=remove.
- **Mutations-Gegenbeweis (Kopie /tmp):** shift-Zweig 'add'->'replace' -> markieren.test
  21 pass -> **20 pass / 1 fail**. Zaehne bestaetigt.
- **2 bewusste Nicht-Bauten (ehrlich benannt, Folge AUF-35b):** Hover-Vorschau
  ('reine Anzeige, nie im Dokument') und shortLabel-Verdrahtung.
- **Sichtprobe (interaktiv, authentifiziertes iframe, innerWidth 1440, fixture decke-treppe):**
  Expertenmodus -> Werkzeug heisst jetzt 'Markieren' (V). Klick auf die Treppe -> selektiert:
  Eigenschaften-Panel wechselt auf 'Treppe' (BAUART-Auswahl, '16 Steigungen . 15 Auftritte,
  Steigung 175 mm . Auftritt 200 mm . DIN 18065', Sicht/Sperren erscheinen). panelZeigtTreppe=true.
  Bundle 4dce1cc ausgeliefert (servierbar, sonst Sichtprobe unmoeglich).

**Urteil: FREIGABE.** Auswahl-Logik rein/getestet, Modi mutationsfest, Store additiv ohne
zweite Wahrheit, Selektion sichtbar am echten Datensatz. Nicht-Bauten sind AUF-35b, kein Mangel.

## AUF-21 / I1 - 110 Werkzeug-Icons ablegen (7bbf9ff) - FREIGABE

**Reihenfolge:** erst blind gegen 7bbf9ff gemessen, dann Auftragstext gelesen.
**Klasse: Vorarbeit** (reine Assets; sichtbar erst, wenn der Katalog/Adapter sie referenziert
= I2, bereits FREIGABE, + Build). I1 allein aendert nichts am Schirm -> keine Sichtprobe fuer I1.

- **Bilanz (git show --diff-filter=A 7bbf9ff):** 110 Icon-SVG + _sprite.svg nach
  public/hausplaner/icons/tools/ ; 3 Referenz-Docs nach docs/planner
  (tool-registry-paket.json 1761 Z, werkzeug-galerie.html, werkzeug-inventar.md 223 Z).
- **Kein Code (Auftrag: 'reine Assets, kein Code'):** Liste der Nicht-Asset/Doc-Dateien im
  Commit ist **leer** - keine .ts/.tsx/.php/.blade beruehrt. Bestaetigt.
- **Icons echt (Gegen-Beweis gegen leere Platzhalter):** Stichprobe align/bath/door/dormer
  je 370-393 Bytes, valides '<svg xmlns=...>'. Nicht 0-Byte, nicht leer.
- **Baum heute:** weiterhin 110 Icons - der spaetere EN->DE-Rename (I4/AUF-31) ist
  verlustfrei (110 -> 110).

**Korrektur an mir selbst (Beweis gilt auch gegen mich):** mein erster Zaehl-Grep lief auf
`git show --stat` und zaehlte 106 - `--stat` kuerzt lange Pfade mit '...', mein auf den vollen
Pfad verankertes Muster uebersah die 4 langnamigen Dateien (approve-detection, distribution-board,
...). `--diff-filter=A --name-only` ist die belastbare Quelle: **110**. Dieselbe Fehlerklasse wie
frueher head-Abschnitt / TAP-Reporter - im Votum offengelegt statt ueberspielt.

**Urteil: FREIGABE.** 110 Assets korrekt platziert, kein Code, Icons real, Zahl selbst-korrigiert.
Damit ist das Icon-Paket AUF-21 komplett belegt: I1 (Assets) + I2/I3/I4 (Katalog/Anheften/Zonen)
alle FREIGABE.

## AUF-30 - Render-Pfad-Testinfra / esbuild-Loader (56cc734) - FREIGABE

**Reihenfolge:** erst blind gegen 56cc734 gemessen (sauberer /tmp-Auszug, weil FachFlaeche.tsx
im Repo-Baum gerade native AUF-33-WIP traegt), dann Commit-Nachricht gelesen.
**Klasse: Vorarbeit** (Test-Infrastruktur; nichts am Schirm) - keine Sichtprobe noetig.
**Erfuellt zugleich A1-Auflage 3** (Render-Pfad-Test), zweimal als 'nicht erfuellbar' zurueckgegeben.

- **Umfang (git show --stat):** 2 Dateien - test-hooks.mjs (+48), __tests__/renderPfad.test.ts (+71).
- **test-hooks.mjs eng wie behauptet:** load-Hook gibt fuer alles ausser .tsx an next() weiter
  (.ts bleibt bei Nodes Type-Stripping), nur .tsx laeuft ueber esbuild transformSync loader:'tsx';
  resolve-Hook ergaenzt .tsx bei endungslosen Importen. Keine neue Abhaengigkeit (esbuild via Vite da).
- **Gates im Auszug selbst gefahren:** schema:check 0 (im Testskript) . test **788/788 pass, 0 fail,
  0 skipped**, EXIT 0 . tsc:hausplaner EXIT 0 . build:hausplaner ok (1.21s).
- **renderPfad.test.ts laeuft echt** (korrekte Registrierung ueber test-register.mjs, nicht die
  Hook-Datei direkt): 6 Subtests ueber react-dom/server - Kopf/Gruppe/Zweck, Feldstruktur,
  Kante 4 (kein Berechnen-Knopf, alle Felder deaktiviert), Zustand als TEXT, Kante 2 (Herkunft),
  alle 19 Flaechen ohne Wurf.
- **Gegen-Beweis (zwei, /tmp-Kopie, Repo unberuehrt):**
  A) 'readOnly disabled' -> 'readOnly' entfernt disabled -> **'Kante 4' rot** (5 pass / 1 fail).
  B) '{flaeche.zweck}' -> Literal -> **'Kopf/Gruppe/Zweck im Markup' rot** (5 pass / 1 fail).
  Jede Mutation traf exakt ihre Aussage -> der Test geht wirklich durch den Render-Pfad.
- **Repo-Baum:** meine Messung hinterliess 0 Marker in FachFlaeche.tsx; das vorhandene M ist die
  native AUF-33-WIP (EngineFlaeche-Extraktion), nicht meine.

**Korrektur an mir selbst (Beweis gilt auch gegen mich):** mein erster Gegen-Beweis
(readOnly -> data_kaputt) blieb gruen - ein No-Op, weil das Feld BEIDE Attribute traegt und
'Kante 4' auf `disabled` prueft. Dieselbe Falle, die der Generator in seiner Commit-Nachricht
offengelegt hatte (erste Mutation traf einen Kommentar). Erst die praezise Mutation zaehlt.

**Urteil: FREIGABE.** Der esbuild-Loader schliesst die .tsx-Luecke ohne neue Abhaengigkeit,
eng gehalten (nur .tsx), und der erste Test durch den echten Render-Pfad hat belegte Zaehne.
A1-Auflage 3 ist damit erfuellt, nicht nur behauptet.

## AUF-33 L2 - engine-treppe Panel (Muster) (9d0c12a, Bundle 1c3aa31) - FREIGABE

**Reihenfolge:** erst blind gegen 9d0c12a gemessen (/tmp-Auszug, da FachFlaeche.tsx zeitweise
native WIP trug), dann Generator-Bericht gelesen.
**Klasse: sichtbar** - Sichtprobe Teil der Abnahme.

- **Umfang (git show --name-status):** 8 Dateien - NEU EngineFlaeche.tsx, dashboard/enginePanels.ts,
  __tests__/enginePanelTreppe.test.ts ; M FachFlaeche.tsx (FlaechenHuelle extrahiert), FaehigkeitenNavi.tsx,
  HausplanerApp.tsx, tools/faehigkeiten.ts, faehigkeiten.test.ts.
- **Drei Auftrags-Grenzen belegt (git-grep):**
  1) **Keine Rechenlogik im Panel** - 'DIN' nur in beschreibendem Text (Z. 89/90), keine Formel/
     Grenzwert/Math/Rundung. Test K3 verriegelt es.
  2) **Ruft berechneTreppe statisch** (enginePanels.ts Z. 20 static import, Z. 129
     'berechne: (werte) => berechneTreppe(...)'). Kein import(variable) - Test K6.
  3) **Kein Modell-Schreibzugriff** (applyCommand/dispatch/store/SceneDocument-Grep leer).
- **Reuse statt Wildwuchs:** FlaechenHuelle (Kopf/Zweck/Zurueck/Escape) aus FachFlaeche.tsx
  extrahiert, von L4-Vorschau UND Engine-Flaeche geteilt - kein zweiter Rahmen, kein zweiter
  Escape-Handler. Regel 'kuratieren, nicht wuchern' gewahrt.
- **Gates im Auszug selbst gefahren:** schema:check 0 . test **888/888 pass, 0 skip**, EXIT 0 .
  tsc 0 . build ok (1.17s). 14 enginePanelTreppe-Subtests gruen (K3/K4-Wertgleichheit x3/K5/K6/K7/
  Operanden-Gate/Fund-Fix).
- **Gegen-Beweis (zwei, /tmp-Kopie, Repo unberuehrt):**
  1) Fund-Fix brechen ('const a = p.bestanden' -> 'false', erzwingt Schwere auch bei bestanden)
     -> **'eine bestandene Pruefung zeigt ✓ erfuellt' rot** (13 pass / 1 fail).
  2) Operanden-Gate entschaerfen (fehlendePflichtfelder-Filter -> immer leer)
     -> **'ohne Geschosshoehe wird nicht gerechnet' rot** (13/1).
- **Sichtprobe (iframe 1440, fixture decke-treppe, Bundle 1c3aa31 serviert, alle L2-Strings per grep):**
  Fachplaner-Reiter -> 'Treppen-Auslegung . verfuegbar' klickbar (die EINE verfuegbare Engine der 13;
  'Dach/Fenster/Tuer/Treppe' verfuegbar sind Fachmodule/Werkzeuge, kein art:engine). Panel: 6 Felder,
  Geschosshoehe als Pflicht, **ehrlicher Leerzustand** ('Noch nicht gerechnet ... die Zahlen kommen
  aus der Engine, nicht aus dieser Flaeche'), Berechnen aktiv.
  - **Berechnen Wohnung:** '✓ Alle Pruefungen bestanden'; 8 Ergebnisse (16 Steigungen . 15 Auftritte .
    175/280 mm . Lauflaenge 4200 . Schrittmass 630 . Bequemlichkeit 105 . Sicherheit 455); 7 Pruefzeilen
    alle '✓ erfuellt'. Deckt sich mit dem Bericht.
  - **Umschalten Aussentreppe:** '✕ Eine Pruefung ist nicht bestanden'; **die Zahlen bleiben stehen**;
    2 '✕ Fehler' (Steigung 175>160, Auftritt 280<300 aussen) + 5 '✓ erfuellt'. **Fund-Fix am Schirm
    bestaetigt:** 'Laufbreite 1000 >= 1000 (aussen)' zeigt '✓ erfuellt' - genau die Zeile, die in der
    ersten Fassung falsch '✕ Fehler' trug. WCAG 1.4.1 (Zeichen UND Wort) erfuellt.

- **Vier Rueckgaben des Generators (kein Mangel an L2, gehoeren an Planner/Yama):** (1) Kriterium 5
  nicht erfuellbar - berechneTreppe liefert nur fehler/warnung, nie info (die Flaeche kann info
  darstellen, behauptet aber nicht, es komme aus den Daten); (2) Treppe hat keine L4-Navigationsflaeche
  - Produktfrage; (3) keine Persistenz ins SceneDocument (wie in 3c verlangt); (4) **L3 ist NICHT 13x
  dasselbe** - bei 11/12 der uebrigen Engines laesst sich der Eingang nicht aus dem Modell fuellen
  (HolzStueck[]/Holzliste/Schicht[]/HeizkreisEingabe[] setzen Zwischenergebnisse voraus). Die Naht,
  die der Auftrag frueh sehen wollte.

**Urteil: FREIGABE.** Das Muster steht sauber - reine Anzeige ueber der Engine, drei Grenzen gewahrt,
Fund-Fix mit Test verriegelt UND am Schirm belegt, Operanden-Gate greift. L3 darf kopieren, mit der
gemessenen Einschraenkung aus Rueckgabe 4 (nicht 13x identisch). Das Gate war gruen, als der Anzeige-
fehler im Bild stand - die Sichtprobe blieb der Fangmechanismus, wie schon bei AUF-34/AUF-36.

## AUF-39 L5 - Wizard-Schritte aus dem Modell (b3a6210, Bundle cb3d17e) - FREIGABE

**Reihenfolge:** erst blind gegen b3a6210 gemessen (/tmp-Auszug), dann Generator-Bericht.
**Klasse: sichtbar** - Sichtprobe Teil der Abnahme.

- **Umfang (git show --name-status):** 5 Dateien - NEU dashboard/fahrschritte.ts,
  __tests__/fahrschritte.test.ts ; M GuidedView.tsx, HausplanerStudio.tsx, studioDaten.ts.
  store/domain/geometry/renderers/public: null Zeilen (selbst geprueft).
- **Guardrail (kein zweiter Snapshot-/Hash-/Projektions-Mechanismus):** Grep auf
  snapshot/hash/projekt/createHash/JSON.stringify/structuredClone in fahrschritte.ts = **leer**.
  ableitenSchritte(scene) ist rein (kein Store/Datum/Zufall), liest das SceneDocument direkt.
- **Gates im Auszug:** schema:check 0 . test **900/900 pass, 0 skip** (888->900) . tsc 0 . build ok.
  12 fahrschritte-Subtests gruen (K2 liest-nicht-aendert . K3 deterministisch . K4 elf Titel/Reihenfolge .
  K5 leeres Dok kein gruener Schritt . K6 kein leerer/vertroestender Hinweis . K7 bebauteGeschosse/
  Oeffnungszahlen/verletzter Zwang warn . statusAus . nichts rendert Demo-Daten mehr).
- **Gegen-Beweis (zwei, /tmp-Kopie, Repo unberuehrt):**
  A) 'offen' liefert status:'ok' (leeres Dok wuerde gruen) -> **K5 'leeres Dokument liefert keinen
     gruenen Schritt' rot** + K7 Geschosse rot (10 pass / 2 fail). Kernkriterium hat Zaehne.
  B) Fensterzahl auf Tuerzahl gelegt -> **K7 '12 Fenster und 3 Tueren' rot** (11/1) - die
     Generator-Mutation unabhaengig nachgestellt.
- **Sichtprobe (iframe 1440, frisches Hausplaner-Projekt, Bundle cb3d17e serviert):**
  Gefuehrte Planung, **Schritt 2/11 'Import oder Grundriss'**: Status **'Offen'** (grau, NICHT gruen),
  Text 'Ob ... Massstab bestaetigt wurde, fuehrt das Dokument nicht ... Es sind keine Waende vorhanden',
  'Im Modell: 1 Geschoss . 0 Fenster . 0 Tueren . 0 Treppen'. Der alte Defekt ('Massstab erkannt 1:50 ok'
  auf leerem Dokument) ist weg - der Massstab wird ausdruecklich NICHT behauptet.
  **Gegenmessung des Modells (Expertenmodus, 'dasselbe Modell und dieselbe Revision'):** Projektbaum
  'Noch keine Bauteile', Eigenschaften 'Raeume: 0 . 0.00 m2', Canvas leer -> die Schritt-Zeile ist
  KORREKT, das Modell hat wirklich 0 Waende/Raeume.

- **Adjacent-Befund (NICHT AUF-39, bereits verfolgt, kein Blocker):** der GuidedView-Canvas in
  Schritt 2 rendert einen Demo-/Platzhalter-Grundriss (Raeume 'Wohnen/Kueche' mit Wandumriss), den
  das leere Modell nicht enthaelt - ein Canvas-vs-Modell-Mismatch. AUF-39 hat den Canvas nicht
  angefasst und den Mismatch nicht verursacht; es macht die Schritt-ZEILE ehrlich, waehrend der
  Canvas weiter das Demo zeigt. Bereits im Ledger 68a7f7e ('Wizard zeigt 5 Raeume erkannt neben
  0 Fenster'). Als bestaetigt und separat verfolgt vermerkt.

**Urteil: FREIGABE.** Der Stepper leitet ehrlich aus dem Modell ab - was das Modell nicht weiss,
wird nicht behauptet (Operanden-Gate). Kernkriterium (leeres Dokument => kein gruener Schritt) per
Test, Mutation UND Sichtprobe belegt; sechs Schritte ohne Modellgrundlage bleiben offen und sagen,
was fehlt. STEPS stillgelegt statt geloescht, Test verriegelt das Nicht-Rendern. Der Demo-Canvas
ist ein bekannter Nachbar-Posten, kein Mangel an dieser Ableitung.

## NACHTRAG - vertagte Sichtproben 1440/1024/375 nachgeholt (AUF-27 / AUF-34 / AUF-21-I4)

Die drei 'FREIGABE mit Auflage / Sichtprobe deferred'-Posten hatten nur Teil-Viewports
(1371/375 bzw. 1512). Jetzt gegen das aktuelle servierte Bundle (cb3d17e, traegt AUF-27/34/I4)
an allen drei Board-Viewports gemessen - iframe fester Breite, innerWidth == CSS-Breite bestaetigt,
Messwerte per getBoundingClientRect im contentDocument.

| Viewport | innerWidth | docOverflowX | Arbeitsbereich-Zeile (AUF-34) | 3 Reiter (AUF-27) | Werkzeuge (I4) |
|---|---|---|---|---|---|
| 1440 | 1440 | **0** | kein Ueberlauf, einzeilig | 3/3 da | sichtbar |
| 1024 | 1024 | **0** | Hoehe 27 px = einzeilig, Ueberlauf 0 | 3/3 da | sichtbar |
| 375  | 375  | 298 | Zeile bricht auf 82 px (3 Zeilen), **eigener Ueberlauf 0** | 3/3 da | sichtbar |

- **AUF-27 (drei Reiter Werkzeuge/Projekt/Fachplaner):** an 1440/1024/375 alle drei vorhanden.
  Auflage (Bundle-Rebuild via AUF-37) erfuellt, Reiter im laufenden App sichtbar. **Sichtprobe erledigt.**
- **AUF-34 (Arbeitsbereich-Leiste, kein waagerechter Ueberlauf / keine Wortumbrueche):** bei 1440
  und 1024 einzeilig ohne Ueberlauf; bei 375 bricht die Leiste auf drei Zeilen um, aber mit
  **eigenem Ueberlauf 0** (graceful, kein Abschneiden). Das deferred-Kriterium ('kein Ueberlauf
  bei 1371 px, keine Wortumbrueche im Menue') ist an den Zielbreiten erfuellt. **Sichtprobe erledigt.**
- **AUF-21/I4 (110 Werkzeuge sichtbar):** Werkzeugliste an 1440/1024/375 sichtbar; die frueher
  offenen 1024/375 sind damit nachgeholt. **Sichtprobe erledigt.**

- **Ehrlicher Befund bei 375 (NICHT AUF-27/34/I4):** die Seite hat bei 375 px ~298 px waagerechten
  Ueberlauf. Quelle gemessen: die **obere Aktionsleiste** (Geschoss . 2D/Split/3D . Gespeichert .
  'Speichern (Strg+S)' bei right=1156) - eine feste Horizontalzeile, die bei 375 nicht umbricht/scrollt.
  Das ist die Kopfleiste des **Expertenmodus** (Desktop-Ansicht; mobil laeuft die gefuehrte Planung),
  ein bekanntes Mobil-Thema (AUF-46-Umfeld), **nicht** von diesen drei Slices verursacht. Als
  Beobachtung vermerkt, kein Blocker fuer die drei Voten.

**Ergebnis:** alle sieben archivierten Posten (AUF-21 . 27 . 33 . 34 . 35a . 36 . 37) haben jetzt
ein vollstaendiges Votum inkl. der geforderten Sichtproben. Kein deferred-Rest mehr offen.

## AUF-43 - Geschoss-Bedienung verlaesst die Zeile (43a287f, Bundle 8fd6568) - FREIGABE

**Reihenfolge:** erst blind gegen 43a287f gemessen (/tmp-Auszug), dann Generator-Bericht.
**Klasse: sichtbar** - Sichtprobe Teil der Abnahme.

- **Umfang (git show --name-status):** 4 Dateien - NEU dashboard/GeschossFlaeche.tsx,
  dashboard/geschossStapel.ts, __tests__/geschossFlaeche.test.ts ; M HausplanerApp.tsx.
  store/domain/geometry/renderers/public: null Zeilen (selbst geprueft).
- **Guardrails belegt (git-grep + Test):**
  - Undo/Redo + 2D/Split/3D **nicht** in der Flaeche (nur in Kommentaren, die erklaeren warum) -
    Test K6 verriegelt es.
  - **kein neues Command, kein Schema-Eingriff** (Grep leer, schema:check 0).
  - **setActiveLevel bleibt einzige Wahrheit:** Stapel-Knopf ruft onWechseln->setActiveLevel;
    das einzige useState (name) ist der lokale Wert des Umbenennen-Felds, KEIN zweiter Auswahl-/
    Modellzustand. Test 'kein zweiter aktuelles-Geschoss-Merker' gruen.
  - **geschossStapel.ts rein** - nimmt Daten, gibt Daten, kein store/Datum/Zufall.
  - **Name genau einmal** (Stapel = Knopf-Liste, kein Select; Umbenennen in EINEM Feld) - Test K3
    'nicht Select und Eingabefeld denselben Wert' + 'App fuehrt keinen zweiten Geschoss-Waehler mehr'.
- **Gates im Auszug:** schema 0 . test **916/916 pass, 0 skip** (900->916) . tsc 0 . build ok.
  16 geschossFlaeche-Subtests gruen (Stapel oben->unten, aktiv markiert, unbekannte id->null kein Wurf,
  leere Liste, sortOrder-dann-elevation, Nachbar, K4 Hoehenlage mit Vorzeichen/Tausender, K5 Umbenennen
  ueber UPDATE_LEVEL undo-faehig, Loeschen letztes gesperrt).
- **Gegen-Beweis (zwei, /tmp-Kopie):**
  A) Sortierung umgekehrt (a,b->b,a) -> 'Stapel oben->unten' + 'keine Sortierumkehr' + Nachbar **3 rot**
     (13 pass / 3 fail; Generator meldete 2 - meiner ist strenger).
  B) aktiv-Erkennung '=== aktivId' -> '!==' -> aktiv/unbekannt/Nachbar/K4x2 **5 rot** (11/5).
- **Sichtprobe (iframe 1440, fixture decke-treppe, Bundle 8fd6568):** die 13-Element-Zeile ist weg,
  stattdessen EIN Knopf 'EG . +-0 mm . 1 von 1 v' (Kurzfassung Name/Hoehenlage/Position). Klick oeffnet
  Flaeche 'STAPEL . 1 GESCHOSS': aktives Geschoss hervorgehoben (Hintergrund UND fett), '0 darueber .
  0 darunter', **Name genau einmal** als beschriftetes Feld 'Name des aktiven Geschosses', Knoepfe
  + Geschoss / Duplizieren / Loeschen (Loeschen gesperrt = letztes Geschoss). Undo/Redo + 2D/Split/3D
  bleiben in der Kopfleiste, NICHT in der Flaeche.

- **Cross-Check zu meinem 375-Befund (Nachtrag oben):** AUF-43 hat die Geschoss-Controls aus der
  ueberlaufenden Kopfzeile gezogen - die fruehere Ueberhang-Quelle 'Speichern' bei right=1156 ist weg
  (weiteste Kante jetzt 841). Die Seite hat bei 375 aber **weiter ~298 px docOverflowX** aus dem
  uebrigen Desktop-Toolbar (Zoom/Ansicht/2D-3D). Der Expertenmodus hat eine faktische Desktop-Min-
  Breite; das ist das bekannte Mobil-Thema (AUF-46-Umfeld), NICHT AUF-43s Scope. AUF-43 verbessert
  die Struktur, loest die Mobil-Frage nicht (und soll es laut Auftrag nicht).

**Urteil: FREIGABE.** Vier unabhaengige Aufgaben aus einer 13-Element-Zeile geloest, ohne zweite
Wahrheit (setActiveLevel bleibt einzig), ohne neues Command/Schema; Stapel als reine Daten geprueft,
Name-Doppel beseitigt, am Schirm belegt. Mutationsfest. Der 375-Rest gehoert AUF-46, nicht hierher.

## AUF-45 - erster Schritt gezaehlt statt behauptet + B8 (b9861d7, Bundle ab7f2c1) - FREIGABE

**Reihenfolge:** erst blind gegen b9861d7 gemessen (/tmp-Auszug), dann Generator-Bericht +
Planner-Bestaetigung gelesen.
**Klasse: sichtbar** - Sichtprobe Teil der Abnahme.

- **Umfang (git show --name-status):** 6 Dateien - NEU tools/naechsterSchritt.ts +
  __tests__/naechsterSchritt.test.ts ; M HausplanerApp.tsx, dashboard/GeschossFlaeche.tsx,
  tools/vorbedingungen.ts, tools/werkzeugVertrag.ts. store/domain/geometry/renderers/public: null.
- **Guardrails belegt (Grep + Test):**
  - **naechsterSchritt liest nur resolveToolState** (keine eigene Sperr-Regel) - Test K3 'wertet
    keine Vorbedingung aus, zaehlt nur Zustaende'.
  - **keine Sperre gelockert** - Test K4 'die gesperrten Mengen sind exakt die gemessenen (73/53/28)'.
  - kein applyCommand/Store-Schreiben (Grep leer).
- **Gates im Auszug:** schema 0 . test **930/930 pass, 0 skip** (916->930) . tsc 0 . build ok.
  14 naechsterSchritt-Subtests gruen.
- **Gegen-Beweis (zwei, /tmp-Kopie):**
  A) Kandidaten-Filter '> 0' -> '>= 0' (ein Schritt der nichts loest qualifiziert) -> **1 rot** (13/1).
  B) 'pointerPosition' aus GESTEN_EINGABEN entfernt (Markieren gilt nicht mehr als gestenbasiert)
     -> **2 rot** ('Markieren braucht keine Optionen - nicht in Entwicklung' + 'Auftragsregel haette
     niemanden getroffen') (12/2).
- **Sichtprobe (iframe 1440, fixture decke-treppe, Bundle ab7f2c1):**
  - **Teil b / B8 sichtbar behoben:** Kontextleiste bei 'Markieren' liest 'Dieses Werkzeug braucht
    keine Optionen' - **kein 'in Entwicklung'-Badge** (0 Badges im View; vorher stand dort
    '...keine Optionen hinterlegt . in Entwicklung'). Der Platzhalter verwechselt 'braucht nichts'
    nicht mehr mit 'ist nicht fertig'.
  - **Teil a / Wegweiser dormant:** kein Schild erscheint - eine Szene traegt immer ein Geschoss,
    also feuert der 'Geschoss anlegen'-Hinweis nie. Deckt sich mit Test K6.
- **Zwei selbstkorrigierte Auftragsannahmen des Generators (belegt, testverriegelt):** (1) die blosse
  Haeufigkeit zeigt auf den falschen Schritt (im leeren Plan sperrt 'auswaehlen' 23 > Geschoss 22,
  aber auswaehlen kann man dort nichts) -> es gewinnt der gemessen meist-entsperrende Schritt;
  (2) die genannte Zahl ist die entsperrte Differenz (20), nicht die Zahl der Wartenden (22);
  (3) die Auftragsregel 'Werkzeug ohne eingaben' haette NIEMANDEN getroffen (kein Vertrag hat leere
  eingaben) -> stattdessen Gesten-Eingaben vs Optionen (trifft 3 Werkzeuge).

- **Teil a erscheint nie = Planner-Spezifikationsfehler, NICHT Generator-Mangel (Planner bestaetigt):**
  der Wegweiser ist korrekt gebaut, aber seine Vorbedingung (kein Geschoss) tritt nie ein, weil eine
  Szene immer ein Geschoss traegt. Der Generator hat das gemessen und zurueckgegeben; die Folge (wo
  gehoert der Hinweis hin) liegt als **AUF-57 beim Planner**. Zaehlt NICHT gegen dieses Votum.

**Urteil: FREIGABE.** Der Wegweiser zaehlt statt zu behaupten (reuse resolveToolState, keine zweite
Regel, keine Sperre gelockert), die Zahl ist die gemessene Differenz; B8 ist sichtbar behoben und
unterscheidet 'braucht nichts' von 'unfertig'. Mutationsfest. Die schlafende Teil-a-Anzeige ist ein
Spec-Fehler des Planners (AUF-57), kein Mangel der Umsetzung.

## AUF-51 - Zeichenflaeche laesst sich wirklich verschieben (74fdcb4, Bundle 31f33e6) - FREIGABE

**Reihenfolge:** erst blind gegen 74fdcb4 gemessen (/tmp-Auszug), dann Generator-Bericht.
**Klasse: sichtbar** (Richtigkeitsfehler) - Sichtprobe Teil der Abnahme.

- **Umfang (git show --name-status):** 3 Dateien - NEU dashboard/pan.ts + __tests__/pan.test.ts ;
  M HausplanerApp.tsx. store/domain/geometry/renderers/public: null (Konva-Stage sitzt in HausplanerApp,
  nicht in renderers/).
- **Der Fehler (bestaetigt am Code):** Buehne draggable OHNE Drag-Handler, Position gesteuerter Wert
  ohne Zustand (x=80); onMouseMove->setCursor rendert bei jeder Bewegung und setzte zurueck. weltPunkt
  liest stage.x() (echte Lage) -> Anzeige und Koordinate widersprachen sich beim Zuruckspringen.
- **Fix + drei Feinheiten (Grep + Test):** Pan-Zustand statt draggable-Entfernung (weltPunkt las die
  verschobene Lage schon korrekt). (1) null-Start = 'nie verschoben' -> Standardlage folgt Fensterhoehe
  (HausplanerApp Z.339 useState<Pan|null>(null)); (2) onDragMove schreibt WAEHREND des Ziehens (Z.1300),
  sonst ruckelt es gegen onMouseMove; (3) Herkunftspruefung e.target===e.currentTarget an BEIDEN
  Schreibstellen (Z.1300/1301), sonst ueberschreibt ein gezogenes Bauteil den Verschub.
- **Gates im Auszug:** schema 0 . test **938/938 pass, 0 skip** (930->938) . tsc 0 . build ok.
  8 pan-Subtests (null=nie verschoben, eigener Wert unabhaengig von Fensterhoehe, Drag-Handler jetzt da,
  nur die BUEHNE schreibt, weltPunkt liest echte Lage, nur mit Auswahl-Werkzeug).
- **Gegen-Beweis (zwei, /tmp-Kopie):**
  A) panAus ignoriert den Pan-Wert ('pan ?? standardPan' -> 'standardPan') -> **'selbst verschoben =>
     eigener Wert gilt' rot** (7/1; deckt Generator 'panAus ignoriert eigenen Wert = 1 rot').
  B) Herkunftspruefung '===' -> '!==' an der Buehnen-Schreibstelle -> **2 rot** ('Drag-Handler' +
     'nur die BUEHNE schreibt den Verschub') (6/2).
- **Sichtprobe (iframe 1440, fixture decke-treppe, Bundle 31f33e6, interaktiver Drag):** Buehne an
  leerem Raster um ~250/120 px gezogen -> der Inhalt wandert mit ('80.00 m2' von ~910 auf ~1140 px,
  links tauchen zuvor abgeschnittene Bemassungen 8240/7760 auf). **Danach zwei Klicks auf leeres Raster
  (loesen genau das Rendern aus, das frueher zuruecksetzte) -> der Verschub BLEIBT**, kein Zuruckspringen.
  Der Richtigkeitsfehler ist behoben; Test belegt die Zustands-Logik, die Sichtprobe die Konva-Verdrahtung.

**Urteil: FREIGABE.** Der Widerspruch (draggable ohne Zustand, Anzeige != Koordinate) ist mit einem
Pan-Zustand geloest statt die Funktion zu entfernen; die drei Feinheiten (null-Start, onDragMove,
Herkunftspruefung) sind begruendet und testverriegelt. Mutationsfest, und der bleibende Verschub ist
am Schirm belegt - genau der Teil, den ein Test ohne DOM/Konva nicht zeigen kann.

## AUF-47 - Speichern-Knopf + beide Statusanzeigen ehrlich (79bf47c, Bundle fca2fc6) - FREIGABE (Auflage nachtraeglich erfuellt)

**Reihenfolge:** erst blind gegen 79bf47c gemessen (/tmp-Auszug), dann Generator-Bericht.
**Klasse: sichtbar** - Sichtprobe Teil der Abnahme; visuelle Sichtprobe hier als AUFLAGE offen
(Browser-Tab-Gruppe dieser Session geschlossen - 'No tab available'; nicht sichtgeprueft, nicht als
gruen gefuehrt).

- **Umfang (git show --name-status):** 4 Dateien - NEU dashboard/speicherAnzeige.ts +
  __tests__/speicherAnzeige.test.ts ; M HausplanerApp.tsx, HausplanerStudio.tsx.
  store/domain/geometry/renderers/public: null.
- **Der Widerspruch (am Code bestaetigt):** Testflaeche setzt keine data-speichern-url, save() no-op -
  trotzdem gruener Knopf + Plakette 'Gespeichert - Rev. 1' neben 'Testflaeche - wird NICHT gespeichert'.
  Zwei Aussagen waren zu einer verschmolzen: 'nichts zu speichern' (speicherStatus) vs 'kann hier gar
  nicht speichern' (speichernUrl, wurde nie gelesen).
- **Fix (Grep + Test):** speicherAnzeige(status, kannSpeichern) ist rein - liefert Text/Gewichtung/
  Knopf-Sperre/Tooltip; kannSpeichern===false schlaegt jeden Zustand (Z.43 'if (!kannSpeichern)' ->
  'Testflaeche - wird nicht gespeichert'), 'Gespeichert' nur bei kannSpeichern (Z.66). **Keine Farbwerte
  in der Regel** (Grep leer; Oberflaeche bildet Gewichtung auf Token ab).
- **save()-No-Op unangetastet:** der Diff von HausplanerApp.tsx beruehrt KEINE save()-Zeile (git show
  grep leer); Test 'der save()-No-Op bleibt unangetastet - er war gewollt' verriegelt es.
- **Beide Anzeigen lesen dieselbe Regel:** Test 'auch die Studio-Kopfzeile liest die Regel - nicht ihre
  eigene Tabelle' (die zweite Anzeige, die Yama gesehen hatte); '- Rev. N' haengt an der Faehigkeit.
- **Gates im Auszug:** schema 0 . test **948/948 pass, 0 skip** (938->948) . tsc 0 . build ok.
  10 speicherAnzeige-Subtests (u.a. 'Gespeichert steht NIE auf einer Flaeche die nicht speichern kann',
  'Knopf wirklich sperrbar - vorher KEIN disabled', 'App liest Faehigkeit aus dem Store, raet nicht').
- **Gegen-Beweis (/tmp-Kopie):** Faehigkeitspruefung ausgehebelt ('!kannSpeichern' -> 'false') ->
  **2 rot** ('Plakette sagt Wahrheit in JEDEM Zustand' + 'Gespeichert steht NIE auf nicht-speichernder
  Flaeche') (8/2). Deckt Generator 'Faehigkeitspruefung ausgehebelt = 2 rot'.
- **Bundle-Beleg (statt Pixel):** das servierte Bundle fca2fc6 traegt die Strings 'Testflaeche' und
  'wird nicht gespeichert' (grep) - die ehrliche Anzeige ist im Artefakt ausgeliefert.

- **AUFLAGE (visuelle Sichtprobe):** beide Anzeigen am Schirm pruefen (Studio-Kopfzeile + Knopf/Plakette):
  auf der Testflaeche kein 'Gespeichert', keine 'Rev. N', Knopf gesperrt mit Grund im Tooltip. In dieser
  Sitzung nicht ausfuehrbar (Browser weg) - hole ich nach, sobald ein Tab verfuegbar ist. Ich habe die
  ALTE Luege ('Gespeichert - Rev. 1' auf der Testflaeche) in jedem frueheren Screenshot dieser Sitzung
  gesehen; Code + Test + Bundle-String belegen die Behebung, nur die Pixel-Bestaetigung fehlt.

**Urteil: FREIGABE MIT AUFLAGE.** Der Widerspruch ist an der richtigen Stelle geloest (Anzeige ehrlich,
save()-No-Op bewusst unberuehrt), beide Anzeigen lesen eine Regel, mutationsfest, im Bundle ausgeliefert.
Einzige offene Auflage: die visuelle Sichtprobe, in dieser Umgebung nicht fahrbar - nachzuholen.

## NACHTRAG AUF-47 - visuelle Sichtprobe nachgeholt (Auflage erfuellt) - FREIGABE

Browser wieder verfuegbar; die in der Abnahme offene visuelle Sichtprobe nachgeholt.
iframe 1440, fixture decke-treppe, Bundle fca2fc6, Expertenmodus. Gemessen (getBoundingClientRect/
innerText im contentDocument) UND am Screenshot:

- **'Gespeichert' kommt 0x vor** (vorher in jedem Screenshot dieser Sitzung sichtbar).
- **keine 'Rev. N'** irgendwo (revNummerDa=false).
- **'wird nicht gespeichert' 3x** - Top-Badge, Kopfzeilen-Plakette (genau die Stelle, wo vorher
  'Gespeichert . Rev. 1' stand, die Yama gesehen hatte) und Knopfbereich.
- **Speichern-Knopf disabled=true** (vorher gruen/primaer/aktiv), Tooltip nennt den Grund:
  'Diese Flaeche hat kein Speicherziel. Der Plan am Objekt wird gespeicher[t]...'.

Beide Statusanzeigen sagen dieselbe Wahrheit; die Luege ist weg. **Auflage erfuellt -> volle FREIGABE.**

## AUF-53 - Import-Recht = Hausplaner,add (b4e5f03, Bundle 581f457) - FREIGABE

**Reihenfolge:** erst blind gegen b4e5f03 gemessen (/tmp-Auszug), dann Generator-Bericht.
**Klasse: Vorarbeit** (kein sichtbarer Effekt in der Insel - die 8 Werkzeuge waren gesperrt und
bleiben gesperrt; die Wirkung ist server-seitig, zurueckgegeben). **Spur A (Rechte)** - besonders
genau gemessen.

- **Umfang (git show --name-status):** 3 Dateien - NEU __tests__/importRecht.test.ts ; M
  __tests__/werkzeugVertrag.test.ts, app/tools/vorbedingungen.ts.
- **Kein Tor-1 (selbst geprueft):** der Commit enthaelt KEINE Datei unter database/migrations/,
  app/Models/User.php, routes/, Controller. Nur Insel-Code + Tests. hasPermission unberuehrt (K3:
  weiterhin genau vier Aktionen).
- **Die is_read-Falle vermieden (Kern):** User::hasPermission schickt jede unbekannte Aktion in den
  default-Zweig = is_read; ein Recht 'Hausplaner,import' saehe geschuetzt aus und waere fuer jeden
  Leseberechtigten offen. Test K4 verriegelt: die Aktion 'import' taucht NIRGENDS als Berechtigungs-
  aktion auf. Stattdessen RECHT_IMPORTIEREN = 'Hausplaner,add' (Z.105), permission.import mappt darauf
  (Z.161 operator contains). add existiert als Spalte seit 2023, von keiner Route benutzt.
- **Sicherheits-Kernpruefung (Insel erteilt sich das Recht NICHT selbst):** HausplanerApp setzt
  permissions: [RECHT_BEARBEITEN] = ['Hausplaner,update'] (Z.407/919) - **nicht** 'Hausplaner,add'.
  Also bleiben die 8 Import-Werkzeuge in der Insel GESPERRT; sie werden NICHT faelschlich freigeschaltet.
  Der Generator hat ausdruecklich zurueckgegeben (Paragraph 4), das Recht nicht durchzureichen - ein
  von der Insel selbst erteiltes Recht schuetzt nichts und haette die acht fuer JEDEN geoeffnet.
- **Gates im Auszug:** schema 0 . test **956/956 pass, 0 skip** (948->956) . tsc 0 . build ok.
  8 importRecht-Subtests (K4 import-nirgends-Aktion, K3 hasPermission 4 Aktionen unberuehrt, K5 8
  Vertraege -> add, K6 ohne Recht alle 8 gesperrt gleicher Grund, K7 mit Recht -8 genau, 'schaltet
  keine Import-FUNKTION frei - 8 bleiben ohne Handler').
- **Gegen-Beweis (/tmp-Kopie):** Zuordnung 'Hausplaner,add' -> 'Hausplaner,update' (breit UND gezielt
  am Mapping-Wert) -> je **3 rot** (K5/K6/K7). Der Generator meldete 4; seine Mutation brach zusaetzlich
  den 'erfuellbar'-Test, vermutlich Zuordnung auf ein NICHT existierendes Recht - meine mappt auf
  update (existiert), daher bleibt 'erfuellbar' gruen. Differenz erklaert, Zaehne bestaetigt.
  (Beweis-statt-Bericht: gemeldet sind meine gemessenen 3, nicht die behaupteten 4.)
- **Sichtprobe (iframe 1440, Bundle 581f457):** kein sichtbarer Effekt in der Insel - der Sperrgrund
  'Keine Berechtigung zum Importieren' erscheint nicht als Klartext (vorkommen 0), er lebt als Tooltip/
  im 'weitere'-Ueberlauf an den gesperrten Werkzeugen. Konsistent mit Vorarbeit; der sichere Zustand
  (8 gesperrt) ist code-seitig belegt, nicht am Schirm.

**Urteil: FREIGABE.** Zwei Sicherheitsfallen sauber umgangen: die is_read-Vortaeuschung (Recht auf
add statt import) und das Selbst-Erteilen in der Insel (nicht durchgereicht, ehrlich zurueckgegeben).
Additiv, kein Tor-1, hasPermission unberuehrt, mutationsfest. Die tatsaechliche Durchsetzung gehoert
server-seitig und ist bewusst ausserhalb dieses Postens.

## AUF-44 - vier tote Versprechen raus aus der Icon-Zeile (47addd1, Bundle 0bde0d9) - FREIGABE

**Reihenfolge:** erst blind gegen 47addd1 gemessen (/tmp-Auszug), dann Generator-Bericht.
**Klasse: sichtbar** - Sichtprobe Teil der Abnahme.

- **Umfang (git show --name-status):** 2 Dateien - NEU __tests__/geplantKnoepfe.test.ts ;
  M HausplanerApp.tsx. store/domain/geometry/renderers/public: null.
- **Kern:** die Tafel nannte 2 '(geplant)'-Knoepfe, gemessen waren es FUENF (einpassen, drehen,
  distanz-messen, bemassen, pdf). VIER sind Dubletten - das Werkzeug existiert wirklich in seiner
  Themen-Gruppe; nur die tote Icon-Kopie ist raus. Der fuenfte ('Ansicht einpassen') hat KEIN
  Katalog-Werkzeug und bleibt - entfernen hiesse die Funktion tilgen -> Willensfrage zurueckgegeben.
- **Werkzeuge bleiben / Bilanz 110 (gegen die RICHTIGE Quelle geprueft):** ALLE = TOOL_KATALOG +
  TOOL_DEFINITIONS = **110**; drehen/distanz-messen/bemassen/pdf je imKatalog=true, Thema=true,
  Vertrag=true. einpassen fehlt korrekt (der 5.). AUF-59-Forderung 'kein Werkzeug verschwindet' gewahrt.
  *(Selbstkorrektur: mein erster Check gegen TOOL_PRESENTATION_RULES meldete 'FEHLT' - falsche Registry;
  der Test prueft Katalog+Registry+Thema+Vertrag, dort sind alle vier da. Beweis gilt auch gegen mich.)*
- **Gates im Auszug:** schema 0 . test **962/962 pass, 0 skip** (956->962) . tsc 0 . build ok.
  6 geplantKnoepfe-Subtests (vier verschwunden, Werkzeug je geblieben mit Thema+Vertrag, einpassen
  bleibt als einzige Nicht-Dublette, genau EIN geplant, verbliebener Knopf inert, Bilanz 110).
- **Gegen-Beweis (/tmp-Kopie):** einen entfernten Knopf ('drehen') wieder eingesetzt -> **2 rot**
  ('vier tote Versprechen verschwunden' + 'genau EIN geplant-Knopf uebrig') (4/2). Deckt Generator.
- **Sichtprobe (iframe 1440, fixture decke-treppe, Bundle 0bde0d9):** genau **1** '(geplant)'-Knopf
  ('Ansicht einpassen'); **keiner** der vier toten Versprechen (Auswahl 90 drehen / Abstand messen /
  Masskette / PDF-Planblatt) mehr in der Leiste. MESSEN & EXPORT sichtbar schlanker. Icon-Zeile 15->11.

**Urteil: FREIGABE.** Vier tote Icon-Versprechen entfernt, ohne ein Werkzeug zu verlieren (Bilanz 110,
jedes in Thema + Vertrag), der einzige Nicht-Dublette-Knopf bleibt inert und als Willensfrage benannt.
Mutationsfest, am Schirm belegt. Dieselbe Sorte Ehrlichkeit wie I2/B8: nichts vortaeuschen, was nicht wirkt.

## AUF-59 - Icon-Zeile macht drei Zustaende unterscheidbar (8f34fc5, Bundle ece8e43) - FREIGABE

**Reihenfolge:** erst blind gegen 8f34fc5 gemessen (/tmp-Auszug), dann Generator-Bericht.
**Klasse: sichtbar** - Sichtprobe Teil der Abnahme. Direkt aus Yamas Beobachtung.

- **Umfang (git show --name-status):** 4 Dateien - NEU dashboard/opKnopfZustand.ts +
  __tests__/opKnopfZustand.test.ts ; M HausplanerApp.tsx, __tests__/keineKappung.test.ts.
  store/domain/geometry/renderers/public: null.
- **Der Mangel (bestaetigt):** bedienbar vs gesperrt unterschieden sich AUSSCHLIESSLICH in der
  Icon-Farbe + Cursor; Rahmen/Grund/Deckkraft identisch; jeder Knopf trug einen Rahmen.
- **Fix (Grep + Test):** opKnopfZustand.ts rein, liefert **Token, keine Farben** (Grep: Farbwerte nur
  in Kommentaren, die den Alt-Zustand beschreiben; Test 'keine Farbwerte in der Regel - liefert Token').
  schalter-ein: Rahmen (einziger); bedienbar: kein Rahmen/weiss/Deckkraft 1; gesperrt: kein Rahmen/
  gedaempft/Deckkraft 0.6 -> gesperrt in DREI Merkmalen verschieden. **Regel liest gesperrt, entscheidet
  nichts** (Test 'keine Sperre aendert sich') - die eine Wahrheit fuer disabled bleibt aussen.
- **K2 (kein Test still verschwunden):** der AUF-26-Kappungs-Test wurde ERSETZT, nicht entfernt - neu
  verriegelt er, dass die Textknoepfe weg sind + spiegeleGrundriss je Richtung genau 1x + mirror-Icons da.
- **Gates im Auszug:** schema 0 . test **971/971 pass, 0 skip** (962->971) . tsc 0 . build ok.
  9 opKnopfZustand-Subtests (>=2 Merkmale je Paar, Rahmen nur am Schalter, gesperrt schlaegt aktiv,
  Regel liest nicht entscheidet, Token statt Farbe, Spiegel-Dublette weg aber Funktion+title bleiben).
- **Gegen-Beweis (/tmp-Kopie):** gesperrt auf EINEN Unterschied zurueckgedreht (Grund+Deckkraft = wie
  bedienbar, nur Icon-Farbe bleibt) -> **2 rot** ('gesperrt >=2 Merkmale' + 'jeder der drei >=2') (7/2).
  Deckt Generator. (Teil-Mutation nur Deckkraft = 1 rot - der Grund-Unterschied blieb; offengelegt.)
- **Sichtprobe (iframe 1440, fixture decke-treppe, Bundle ece8e43, getComputedStyle):** 11 Knoepfe,
  **genau 2 mit sichtbarem Rahmen** = 'Raster' + 'Fang' (die eingeschalteten Schalter), kein anderer.
  bedienbar (6): weisser Grund, Deckkraft 1. gesperrt (3): Grund rgb(242,244,246), **Deckkraft 0.6**.
  -> gesperrt vs bedienbar in **Grund UND Deckkraft**, nicht nur Farbe. **Spiegeln-Textknoepfe
  ('Links/Rechts','Oben/Unten') weg** - im Screenshot fehlt das 'Grundriss spiegeln'-Paar, das in
  jedem frueheren Screenshot dieser Sitzung stand.

**Urteil: FREIGABE.** Die drei Zustaende sind ueber mehrere Merkmale (Rahmen/Grund/Deckkraft)
unterscheidbar, nicht mehr allein ueber Icon-Farbe (Frontend-Linse: Zustand nicht nur Farbe); die
Regel liefert Token und entscheidet keine Sperre; die Spiegeln-Dublette ist auf EINEN Aufruf je
Richtung reduziert. Mutationsfest, am Schirm gemessen. Genau Yamas Beobachtung behoben.

## AUF-49 - Dialogfokus + Leertaste (f83cf11, Bundle c4e8cc4) - FREIGABE

**Reihenfolge:** erst blind gegen f83cf11 gemessen (/tmp-Auszug + Browser), dann Generator-Bericht.
**Klasse: sichtbar** - Sichtprobe (Fokus) Teil der Abnahme, wie vom Planner betont.

- **Umfang (git show --name-status):** 7 Dateien - NEU dashboard/dialogFokus.ts +
  __tests__/dialogFokus.test.ts ; M ConfigWizard/FachFlaeche/GuidedView/HausplanerStudio/StartView.
  store/domain/geometry/renderers/public: null.
- **Struktur (ohne DOM):** ConfigWizard hat jetzt role=dialog + aria-modal=true + aria-labelledby
  (Z.69; vorher 0). istAusloeser (Z.97-99): Enter UND Leertaste, preventDefault NUR bei Space
  (sonst scrollt die Seite). EINE Fokusregel fuer drei Dialoge (Reuse, Test 'keine zweite Falle').
- **Gates im Auszug:** schema 0 . test **982/982 pass, 0 skip** (971->982) . tsc 0 . build ok.
  11 dialogFokus-Subtests (Falle an beiden Raendern, tabindex=-1 NICHT in der Falle, Enter+Space
  WCAG 2.1.1, Space verhindert Scrollen/Enter nicht, ConfigWizard 'hatte nichts, jetzt alles',
  kein Dialog baut Escape mehr selbst).
- **Gegen-Beweis (/tmp-Kopie):** '% anzahl' aus naechsterIndex entfernt -> **3 rot** (Falle an beiden
  Raendern + Rand-Start + Ein-Knopf-dreht) (8/3). Deckt Generator 'Modulo entfernt = 3 rot'.
- **Browser-Sichtprobe (iframe 1440, Engine-Flaeche, activeElement SELBST gemessen):**
  Fokus nach Oeffnen im Dialog auf 'Zurueck zum Planer' (aktivElementImDialog=true); 6x Tab UND
  Shift+Tab -> Fokus bleibt jedes Mal im Dialog; Escape -> Dialog weg; nach Schliessen Fokus
  ausserhalb des Dialogs auf dem Oeffner. Alle vier Zusagen (rein/Falle/Escape/zurueck) belegt.
- **44px ehrlich zurueckgegeben:** Touch-Ziel gemessen 55x26 (Hoehe < 44) - die Zielgroessen sind
  NICHT angefasst (WCAG 2.5.5 eigener Posten). Die Planner-§2-44px-Erwartung ist zurueckgegeben,
  nicht erfuellt - ehrliche Scope-Trennung (wie AUF-45/57), kein Mangel an AUF-49.

**Urteil: FREIGABE.** Fokus haelt in allen drei Dialogen (rein, Falle beidseitig, Escape, Rueckgabe),
selbstgebaute Knoepfe hoeren jetzt auf Enter UND Leertaste, eine Regel statt drei. Mutationsfest, Fokus
selbst am activeElement gemessen. Die 44px sind ein sauber getrennter Folgeposten.

## Bestaetigung der drei bereits abgenommenen Posten gegen die Planner-Nachfragen (26.07.)

Der Planner-Stapel nennt vier; drei tragen bereits ein committetes FREIGABE-Votum (Tafel hinkte nach).
Gegen die konkreten Nachfragen nachgeprueft:
- **AUF-53 (Votum 1c41ec6):** K4 selbst gegen den Quelltext - 'Hausplaner,import' als **Aktion** = 0
  (einziger Treffer ist ein KOMMENTAR in vorbedingungen.ts:91). Tor 1: keine Datei unter routes/ oder
  database/migrations/ im Commit. **§4-Vollstaendigkeit (neue Frage):** die EINZIGEN rechte-annehmenden
  Stellen der Insel sind HausplanerApp:408/938 - beide erteilen sich nur 'Hausplaner,update'; keine
  weitere Stelle nimmt ein Recht an, 'add' wird nirgends selbst-erteilt. Die Rueckgabe ist vollstaendig.
- **AUF-59 (Votum 5522cf3):** 'keine Sperre geaendert' testverriegelt (Regel liest gesperrt, entscheidet
  nichts); Sichtprobe getComputedStyle: gesperrt-Menge unveraendert, Spiegeln-Funktion blieb (nur die
  Text-Darstellung wich dem Icon).
- **AUF-44 (Votum a2403c4):** Bilanz gegen die RICHTIGE Quelle (Katalog+Registry) = 110; die vier
  Werkzeuge (drehen/distanz-messen/bemassen/pdf) je Katalog+Thema+Vertrag vorhanden - nur die tote
  Icon-Kopie raus.

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
  AUF-34    8b2b9e6  tsc 0 / schema 0 / test 830/830 ; Mutation Thema-entfernt = 4 rot ; BUNDLE NICHT rebuilt
  AUF-37    91d9592  build 0 ; committeter Bundle == frischer Build (byte-identisch) ; Sichtprobe 1371+375: AUF-27 3 Reiter, AUF-34 5 Bereiche kein Ueberlauf/Umbruch
  AUF-36    d106445  tsc 0 / schema 0 / test 853/853 ; Mutation Grund-verfaelscht = 1 rot ; Bundle eigener Rebuild 368f2d7 ; Sichtprobe Grund-Text rendert
  AUF-35a   35fbfde  tsc 0 / schema 0 / test 874/874 ; Store +31 nur Auswahl-UI-State (kein SceneDocument) ; Mutation shift add->replace = 1 rot ; Bundle 4dce1cc ; Sichtprobe Klick-Treppe -> Panel 'Treppe'
  I1        7bbf9ff  diff-filter=A: 110 Icon-SVG + _sprite + 3 Referenz-Docs ; kein Code (Nicht-Asset-Liste leer) ; Stichprobe 370-393 B valide SVG ; Selbstkorrektur 106(--stat gekuerzt)->110
  AUF-30    56cc734  Auszug: schema 0 / test 788/788 0-skip / tsc 0 / build ok ; test-hooks nur .tsx via esbuild ; 6 renderPfad-Subtests ueber react-dom/server ; Gegen-Beweis A disabled-weg='Kante 4' rot, B zweck-Literal='Zweck im Markup' rot (je 5/1)
  AUF-33L2  9d0c12a  Auszug: schema 0 / test 888/888 0-skip / tsc 0 / build ok ; 3 Grenzen (keine Rechnung/statischer Aufruf/kein Modell-Schreiben) ; 14 Subtests ; Gegen-Beweis Fund-Fix + Operanden-Gate je 13/1 rot ; Sichtprobe 1440: Wohnung 7x erfuellt, Aussentreppe 2 Fehler+5 erfuellt (Fund-Fix am Schirm)
  AUF-39L5  b3a6210  Auszug: schema 0 / test 900/900 0-skip / tsc 0 / build ok ; Guardrail kein 2. Snapshot/Hash/Projektion (grep leer), ableitenSchritte rein ; 12 Subtests ; Gegen-Beweis A offen->ok = K5 rot (10/2), B fenster->tuer = K7 rot (11/1) ; Sichtprobe: frisch Schritt 2/11 'Offen', keine Waende, 0 Fenster/Tuer/Treppe, Expertenmodus bestaetigt 0 Bauteile ; Adjacent Demo-Canvas (68a7f7e, nicht AUF-39)
  Sichtprobe-Nachtrag  Bundle cb3d17e  1440: docOvfX 0, arb einzeilig, 3 Reiter, WZ sichtbar . 1024: docOvfX 0, arbH 27 einzeilig . 375: docOvfX 298 (Quelle: obere Aktionsleiste 'Speichern' right=1156, NICHT AUF-27/34/I4), arb bricht 82px arbOvfX 0, 3 Reiter da
  AUF-43    43a287f  Auszug: schema 0 / test 916/916 0-skip / tsc 0 / build ok ; Guardrails Undo/2D-3D nicht in Flaeche, kein Command/Schema, geschossStapel rein, setActiveLevel einzig, Name einmal ; 16 Subtests ; Gegen-Beweis Sortierung 3 rot + aktiv-Flip 5 rot ; Sichtprobe 1440 Knopf 'EG +-0 1 von 1' + Stapel-Flaeche ; 375 docOvf 298 bleibt (AUF-46, nicht AUF-43)
  AUF-45    b9861d7  Auszug: schema 0 / test 930/930 0-skip / tsc 0 / build ok ; naechsterSchritt liest nur resolveToolState (K3/K4), keine Sperre gelockert (73/53/28) ; 14 Subtests ; Gegen-Beweis Filter >0->>=0 = 1 rot, Gesten-Regex brechen = 2 rot (B8) ; Sichtprobe 1440: Markieren 'braucht keine Optionen' kein in-Entwicklung-Badge ; Wegweiser dormant (Geschoss immer da = Planner-Spec AUF-57)
  AUF-51    74fdcb4  Auszug: schema 0 / test 938/938 0-skip / tsc 0 / build ok ; Pan-Zustand null-Start + onDragMove + Herkunftspruefung (HausplanerApp 339/1300/1301) ; 8 Subtests ; Gegen-Beweis panAus ignoriert Wert = 1 rot, Herkunft ===->!== = 2 rot ; Sichtprobe 1440: Drag ~250/120 -> Inhalt wandert, 2 Klicks danach -> Verschub BLEIBT (kein Snap-back)
  AUF-47    79bf47c  Auszug: schema 0 / test 948/948 0-skip / tsc 0 / build ok ; speicherAnzeige rein, kannSpeichern===false schlaegt alles, save() unberuehrt (Diff leer) ; 10 Subtests ; Gegen-Beweis Faehigkeit ausgehebelt = 2 rot ; Bundle fca2fc6 traegt 'wird nicht gespeichert' ; visuelle Sichtprobe AUFLAGE (Browser weg, nicht sichtgeprueft)
  AUF-53    b4e5f03  Auszug: schema 0 / test 956/956 0-skip / tsc 0 / build ok ; kein Tor-1 (keine PHP/Migration/Route) ; is_read-Falle vermieden (K4 import nirgends Aktion), RECHT_IMPORTIEREN=Hausplaner,add ; Insel erteilt nur update (nicht add) -> 8 bleiben gesperrt ; 8 Subtests ; Gegen-Beweis Mapping->update = 3 rot (Generator 4, Delta erklaert) ; Sichtprobe: kein sichtbarer Effekt (Vorarbeit, Grund im Tooltip)
  AUF-44    47addd1  Auszug: schema 0 / test 962/962 0-skip / tsc 0 / build ok ; ALLE(Katalog+Registry)=110, drehen/distanz-messen/bemassen/pdf je Katalog+Thema+Vertrag=true (einpassen fehlt korrekt) ; 6 Subtests ; Gegen-Beweis Knopf wieder eingesetzt = 2 rot ; Sichtprobe 1440: genau 1 '(geplant)' (einpassen), 4 tote weg, 15->11 ; Selbstkorrektur FEHLT-Artefakt (falsche Registry)
  AUF-59    8f34fc5  Auszug: schema 0 / test 971/971 0-skip / tsc 0 / build ok ; opKnopfZustand rein (Token, keine Farbe), Regel liest gesperrt ; K2 Kappungs-Test ersetzt nicht entfallen ; 9 Subtests ; Gegen-Beweis gesperrt->1 Unterschied = 2 rot ; Sichtprobe 1440 getComputedStyle: 11 Knoepfe, 2 mit Rahmen (Raster/Fang), gesperrt Grund rgb(242,244,246)+Deckkraft 0.6 vs bedienbar weiss+1, Spiegeln-Textknoepfe weg
  AUF-49    f83cf11  Auszug: schema 0 / test 982/982 0-skip / tsc 0 / build ok ; ConfigWizard role=dialog/aria-modal (war 0), istAusloeser Enter+Space, eine Regel 3 Dialoge ; 11 Subtests ; Gegen-Beweis % anzahl weg = 3 rot ; Browser activeElement: Fokus rein 'Zurueck zum Planer', 6x Tab+ShiftTab bleiben im Dialog, Escape schliesst, Fokus zurueck ausserhalb ; 44px zurueckgegeben (Ziel 55x26 <44, nicht angefasst)
  AUF-47-Sicht  Bundle fca2fc6  Testflaeche: 'Gespeichert' 0x, 'Rev. N' 0x, 'wird nicht gespeichert' 3x (Top-Badge + Kopfzeile + Knopf), Speichern-Knopf disabled=true mit Grund-Tooltip -> Auflage erfuellt, volle FREIGABE
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
