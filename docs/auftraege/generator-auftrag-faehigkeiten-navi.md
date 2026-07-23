# Generator-Auftrag — Fähigkeiten-Navigation im Hausplaner (auf v9 verankert)

**Rolle:** Generator (Claude Code in VS Code). **Heimat-App:** `ticket`. **Ausgestellt von:** Planner, 2026-07-23 (v9-verankert).
**Priorität: HOCH.** **Ziel:** Kein importiertes Fach-/Rechen-Modul bleibt unsichtbare Leiche — alle erscheinen in
der **vorhandenen v9-Studio-Navigation** und werden bedienbar. **Kein neues Design** — es wird in die bestehende
Shell eingehängt.

## VERBINDLICHE DESIGN-VERANKERUNG (das ist der Kern dieses Auftrags)
Die Navi ist **nicht** neu zu erfinden. Sie IST die vorhandene v9-Synthese:
- **Shell:** `resources/planner/hausplaner/app/HausplanerStudio.tsx` (Kopf 62px, persistente linke Navigation
  266/66px mit Ein-/Ausklappen < 900px, Modus-Umschalter, Status-Pille, Toast). Hier andocken, nicht ersetzen.
- **Tokens (EINE Wahrheit):** ausschließlich `T` aus `resources/planner/hausplaner/app/studioDaten.ts`
  — Akzent **Teal `#12807d`** für Rahmen/Navigation, Marke **Grün `#7fae1c`** NUR für die Primäraktion,
  semantische Status **ok/warn/err** (`T.ok/T.warn/T.err`) für die Zustände. **Kein hartkodierter Hex**,
  kein zweites Farbsystem. Icons über `Ikon` aus `studioUi.tsx` (24er-viewBox).
- **Fach-Hubs schon vorhanden:** `FACH` in `studioDaten.ts` (Haustechnik · PV-Planer · Bauelemente · Bad ·
  Küche) — die Engines werden in die **passenden bestehenden Hubs** gehängt; fehlende Gruppe **Dach & Zimmerei**
  additiv ergänzen. Referenz-Optik: Artefakt **`hausplaner-navi-v9`** (liegt in Yamas Galerie) — es zeigt
  Zielbild und Zuordnung. (Die alten Skizzen `hausplaner-navi`/`faehigkeiten-landkarte` sind NUR Inhaltsliste,
  nicht das Design — deren Palette NICHT übernehmen.)

## Zuordnung Engine → Fach-Hub (aus der Landkarte)
- **Haustechnik:** `fbhAuslegung` 🔴, `heizkoerperLeistung` 🔴, `heizkreisVerteiler` 🔴, `heizkoerperTypen` 🟢.
- **PV-Planer:** `pvBelegung` 🔴.
- **Dach & Zimmerei (neuer Hub):** `dachformVorlagen` 🟡, `dachVerschneidung` 🔴, `dachUForm` 🔴,
  `dachAusschnitt` 🔴, `gaubeGeometrie` 🟡, `sparrenBerechnung`/`sparrenTrennung` 🔴, `schifterListe` 🔴,
  `holzMengen` 🔴, `holzBauteile` 🔴, `auswechslung` 🔴.
- **Bad:** `abwassergefaelle` 🔴. **Küche:** `kuecheArbeitsdreieck` 🔴.
- **Bau & Struktur:** `wandaufbau` 🔴, `masskette` 🟡, `bemassung` 🟢.

## Was gebaut wird
1. **Fähigkeiten-Registry (datengetrieben, EINE Wahrheit):** die bestehende `toolCatalog`/`toolRegistry`
   um die Bau-Fähigkeiten erweitern (Feld je Fähigkeit: id · label · hub · zustand g/y/r · modul-referenz).
   **Kein Zweit-Register.** Die Navigation rendert aus dieser Registry.
2. **Jede Fähigkeit bedienbar** — zwei Muster:
   - **Interaktive Werkzeuge** (Wand/Fenster/Tür/Treppe/Dach/Magnet/Verschieben): nur in die Navi einhängen,
     setzen `activeToolId` wie bisher.
   - **Reine Rechen-Engines** (🔴): je ein **Eingang→Ergebnis-Panel** in v9-Tokens (siehe Artefakt-Vorschau),
     das die **echte, getestete Engine-Funktion aufruft** (Modul NUR importieren/aufrufen, nie ändern —
     Byte-Treue der Ports). 🟢/🟡/🔴 = `T.ok`/`T.warn`/`T.err` (Farbe **und** Text — Barrierefreiheit).

## Reihenfolge (batchweise, jeder Batch ein eigener P→G→E-Zyklus)
- **Batch 0 (dieser Auftrag): v9-Navi-Schale + Registry** — alle Fähigkeiten erscheinen sichtbar in der
  vorhandenen Studio-Navigation mit Zustand. Der sofort sichtbare Schritt.
- **Batch 1: Haustechnik** (`fbhAuslegung`/`heizkoerperLeistung`/`heizkreisVerteiler`-Panels).
- **Batch 2: Zimmerei/Holzliste** (`schifterListe`/`holzMengen`/`holzBauteile`/`sparrenTrennung`/`auswechslung`).
- **Batch 3: PV · Sanitär · Küche · Wandaufbau.**
- (L/T/U-Dach-Render + 187 Vorlagen laufen über W-3b Stufe 2a Teil 2 / Stufe 2b.)

## Gate (Generator selbst)
`tsc:hausplaner` 0 · `schema:hausplaner:check` 0 · `test:hausplaner` (≥ 632 + neue: „Navi listet alle
Registry-Fähigkeiten", „Panel X ruft Engine X und liefert Ergebnis") · `build:hausplaner` (nativ/x64).

## Abnahmekriterien (Evaluator in VS Code — Logik per Test, Optik gegen die UX-Rubrik am echten Rendern)
1. **Jede** Fähigkeit der Landkarte erscheint in der Navi (keine 🔴 mehr unsichtbar; Zustand angezeigt).
2. **v9-CI eingehalten (gemessen, nicht behauptet):** nur `T`-Tokens; Akzent Teal an Navigation, Grün nur
   Primäraktion, Status ok/warn/err semantisch; **kein hartkodierter Hex** im Diff (grep-Beweis).
   Kontrast AA; Zustand Farbe **und** Text; Tastatur/Fokus wie in der Shell (`:focus-visible` Teal).
3. Die bereits aktiven Werkzeuge funktionieren unverändert (keine Regression).
4. Mind. die Batch-Engines sind über ihr Panel bedienbar und rufen die **echte** Engine (Test belegt Ergebnis).
5. **Eine Wahrheit:** eine Fähigkeiten-Registry (kein Zweit-Register); Shell/Tokens wiederverwendet, nicht
   dupliziert; portierte `geometry/*`-Engines im git-Diff NICHT enthalten (nur aufgerufen).
6. Additiv, kein Beifang, nur `auto/`-Branch, **kein main-Merge/Push/Deploy** ohne Yamas Wort.

## Guardrails
Additiv; **v9-Shell + `T`-Tokens sind Pflicht** (kein Neu-Design, kein zweites Farbsystem); portierte
`geometry/*`-Engines NUR aufrufen (Byte-Treue); eine Wahrheit; Barrierefreiheit (Zustand Farbe **und** Text).
Meldung „umgesetzt" → Evaluator. Batch 0 zuerst (Sichtbarkeit), dann die Batches.

---

## NACHTRAG (verbindlich) — Fahrplan-Sortierung nach Abhängigkeit + Heizlast-Schritt
**Grundlage:** `docs/konzept/hausplaner-navigation.md` (Planner-Konzept, 2026-07-23). Diese Sortierung ist
**Teil des Bauauftrags**, nicht optional. Prinzip: jeder Planer darf erst kommen, wenn seine Eingänge aus
früheren Planern vorliegen (Kette Eingang→Ausgang), sortiert in 5 Phasen.

### AP-A — Registry trägt die Sortierung (Datenmodell)
Jede Fähigkeit in der einen Fähigkeits-Registry (toolCatalog-Erweiterung) bekommt **additiv** die Felder:
`phase` (1–5), `gewerk` ('huelle'|'waerme'|'ausbau'|'technik'), `eingang: string[]`, `ausgang: string[]`.
Aus diesen Feldern rendern **beide** Navigations-Sichten — kein zweites Register, keine hartkodierte Reihenfolge.

### AP-B — Geführter Fahrplan (STEPS in `studioDaten.ts`): Heizlast einziehen + Wärme sichtbar machen
Der heutige `STEPS`-Fahrplan hat **keinen Heizlast-Schritt** und versteckt die Wärme-Planer unter „TGA".
Das wird korrigiert. Ziel-Reihenfolge (Bookends „Projektgrundlagen" vorn, „Prüfung/Koordination" +
„Dokumentation/Rendering" hinten bleiben):
1. Projektgrundlagen · 2. Grundriss (Import/Geschosse) · 3. **Dach & Fassade** · 4. Fenster / Türen /
   Treppen · 5. Räume & Nutzung · 6. **Heizlast (NEU, DIN EN 12831)** · 7. **Wärme: Fußbodenheizung /
   Heizkörper / Wärmepumpe** (aus der Heizlast dimensioniert) · 8. Bad & Küche · 9. Elektro ·
   10. **TGA (Koordination)** · 11. **PV** · 12. Prüfung & Koordination · 13. Dokumentation & Rendering.
Harte Anforderungen: **(i)** ein `Heizlast`-Schritt **zwischen** Hülle/Räume und Wärme; **(ii)** die
Wärme-Planer als **eigener sichtbarer Schritt** aus der Heizlast abgeleitet, nicht unter TGA; **(iii)** PV
**nach** Elektro; **(iv)** `STEPS` sind präsentative Daten — additive Erweiterung, keine Logik-Kopplung.
Die Wärme-Schritt-`checks`/`aufgaben` benennen die echten Engines (`fbhAuslegung`/`heizkoerperLeistung`/
`heizkreisVerteiler`), damit Phase-1-Panels (Batch 1) direkt andocken.

### AP-C — Fachplaner-Hubs nach Phase gruppieren (`FACH`)
Die Direktzugriff-Sicht gruppiert nach Gewerk in Phasen-Reihenfolge:
- **Hülle:** Grundriss · Dach · Fenster · Tür.
- **Wärme:** Heizlast · Fußbodenheizung · Heizkörper · Wärmepumpe · Verteiler.
- **Ausbau:** Bad · Küche.
- **Technik & Energie:** Elektro · TGA · PV.
Bestehende `FACH`-Hubs (Haustechnik→**Wärme**, PV-Planer→**Technik&Energie**, Bad/Küche→**Ausbau**,
Bauelemente→**Hülle**) entsprechend umlabeln/zuordnen; „Dach & Zimmerei" additiv in **Hülle**.

### AP-D (vorbereitet, nicht in Batch 0) — Freigabe-Logik aus Eingang/Ausgang
Aus `ausgang → eingang` lässt sich ableiten: ein Schritt ist erst **startbar**, wenn seine Eingänge grün
sind (z. B. Heizlast erst nach vollständiger Hülle). In Batch 0 nur die **Datengrundlage** (AP-A) legen;
die aktive Sperre ist ein späterer eigener Slice.

### Abnahme-Ergänzung (Evaluator)
7. Registry trägt `phase/gewerk/eingang/ausgang`; beide Sichten rendern **daraus** (Beweis: Reihenfolge
   ändert sich mit den Daten, nicht per hartkodierter Liste).
8. `STEPS` enthält einen **Heizlast-Schritt** zwischen Hülle/Räume und Wärme; die Wärme-Planer sind ein
   sichtbarer, aus der Heizlast abgeleiteter Schritt (nicht unter TGA); PV nach Elektro.
9. Fachplaner-Hubs in Phasen-Reihenfolge (Hülle→Wärme→Ausbau→Technik&Energie).

---

## PLANNER-DISPOSITION (Evaluator-Befund String-Referenz) — 2026-07-23
**Existenz gemessen (read-only, Planner):** alle 13 referenzierten Engines liegen real in
`resources/planner/hausplaner/geometry/` — inkl. `pvBelegung.ts` **mit** `__tests__/pvBelegung.test.ts`.
Die Fähigkeits-Landkarte ist **nicht erfunden**; die frühere Matrix-Notiz „PV-Belegung nur Playground"
war **veraltet** (pvBelegung ist im ticket portiert). Existenz-Zweifel damit ausgeräumt.

**Berechtigter Rest-Befund (bleibt):** `engineModul: 'geometry/x…'` ist ein **String**, den tsc nicht prüft.
Ein Tippfehler/Drift bliebe grün. Das wird geschlossen — zweistufig:
- **AP-E (Batch 0, PFLICHT): Registry-Guard-Test.** Ein Test iteriert die Registry und **importiert jedes
  `engineModul` dynamisch** (`await import(pfad)`), prüft, dass Modul **und** der erwartete Haupt-Export
  existieren. Rot, sobald ein Pfad falsch/Modul fehlt. Das ist der Beweis, den der Evaluator zu Recht will —
  ohne die 13 Module eager in die Navi zu ziehen.
- **Stehende Regel (Batch 1–3):** Wird eine Engine in ihrem Eingang→Ergebnis-Panel verdrahtet, ersetzt der
  Panel-Code die String-Referenz durch einen **echten getippten Import** der Engine-Funktion — ab dann prüft
  tsc die Bindung hart. Der String ist nur die Batch-0-Brücke bis zum Panel.

**Abnahme-Ergänzung:** (10) Guard-Test vorhanden und grün; Gegen-Beweis: ein `engineModul` temporär
verfälschen ⇒ Test wird rot (Gate greift nachweislich).

### AP-E — Präzisierung (Evaluator-Befund: Export ≠ Modulname)
Der Guard-Test darf **nicht** annehmen `export == Modulname`. Beispiel belegt: `geometry/pvBelegung.ts`
exportiert `pvSchnellBelegung`. Der erwartete Haupt-Export je Fähigkeit steht in der Registry selbst
(bzw. in `faehigkeiten-landkarte-und-registry.md`, liegt auf `navi-batch0`). Der Test importiert das Modul
dynamisch und prüft **den dort deklarierten Export-Namen** — rot, wenn Modul ODER dieser Export fehlt.
NB: `faehigkeiten.ts`/das Mapping-Doc liegen auf `auto/hausplaner-navi-batch0` (nicht im w3b-2-Baum).
