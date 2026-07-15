# 3D-0B — Entscheidungsvorlage: kanonische Gebäudegeometrie

**Stand:** 2026-07-15 · **HEAD:** `59daa10`. Grundlage: 3D-0A (grün). Bindet G0c-2 (`gebaeude_geometrie` versioniert), ADR-0001, „eine Wahrheit je Sachverhalt".
**Dokumentstatus:** Ursprünglich Entscheidungsvorlage; mit Abschnitt „RATIFIZIERT" (unten) in eine **verbindliche Yama-/Planner-Entscheidung** überführt. Varianten/Bewertung/Risiken (F2/F3) bleiben als Entscheidungsgrundlage erhalten.

---

## RATIFIZIERT — Yama-/Planner-Entscheidung (2026-07-15)

Verbindliche Entscheidungen für den 3D-Gebäudeplaner-Strang:

1. **Kanonische Geometrie = Variante C (gestufter Übergang).** `gebaeude_geometrie` bleibt **die alleinige fachliche Schreibwahrheit**. G0c-2 (Objektbindung, Versionierung, Gate) + Heizlast-Golden-Master bleiben erhalten. **Kein** neuer paralleler Geometrieschreiber, **kein** dauerhaftes Dual-Write, **keine** sofortige Ablösung, **keine** automatische Migration, **keine** relationale Zweitwahrheit, **keine** Umstellung bestehender Leser in diesem Slice. Eine spätere relationale Struktur ist nur zulässig als (a) ausdrücklich freigegebene additive Zielstruktur, (b) zunächst abgeleitete Projektion/kontrollierter Import, (c) mit Paritätsnachweis gegen `gebaeude_geometrie`, (d) genau ein fachlicher Schreiber, (e) eigener Umschalt-/Rollback-Slice. Bis zur ausdrücklichen Cutover-Entscheidung: **`gebaeude_geometrie` = kanonische Schreibwahrheit**.
2. **Framework — 2D-Editor:** im bestehenden Ticket-Frontend (Blade + vorhandene Alpine-Strukturen, SVG/Canvas nach technischem Vergleich, bestehender Vite-Build). **Keine eigenständige SPA, kein zweites Frontend, keine Browser-Datenwahrheit.** Der Editor schreibt später ausschließlich über kontrollierte Ticket-Services ins kanonische Modell.
3. **Framework — 3D-Viewer:** isoliertes **Three.js-Modul** über den bestehenden Vite-Build, in einer Blade-Seite montiert, TypeScript/sauber typisiertes JS, **rein abgeleitet**, **kein eigener Geometrie-Store, keine DB-Kommunikation, kein Schreiben aus der Szene**; Meshes identifizierbar den kanonischen Element-IDs zugeordnet.
4. **React:** wird **nicht** als zweites produktives Frontend in `ticket` eingeführt. Das Playground-React/Three-Bundle bleibt **`nur_als_referenz`** — nicht kopieren/dekompilieren/rückübersetzen/produktiv nutzen. Beschaffung des Playground-Sourcecodes → eigener Inventurslice (Lizenz-/Framework-/Security-/Test-/Übernahmeprüfung); **darf die Entwicklung des kanonischen Modells nicht blockieren.**
5. **Azimut-Konvention (kanonisch):** `0°=Nord · 90°=Ost · 180°=Süd · 270°=West`, im Uhrzeigersinn, normalisiert auf `0 ≤ azimut < 360`, Dezimalgrade; Flächen-Azimut = Azimut der nach außen gerichteten Horizontalprojektion der Flächennormalen; **Nordbezug + Herkunft gespeichert/ableitbar**; wahrer Norden ≠ Plan-/Bildrotation (nicht still gleichsetzen). Abweichende Konventionen (z.B. PVGIS Süd=0, `InverterSizingService.php:69`) nur über **explizite Adapter** transformieren; **keine stillen Vorzeichen-/180°-Korrekturen** in Views/Renderern.
6. **Modelltiefe (verbindlich ab Schema):** `Objekt → Gebäude(e) → Geschoss(e) → {Räume, Wände, Öffnungen, Decken, weitere}`. Mehrere Gebäude je Objekt, mehrere Geschosse je Gebäude, mehrere Räume je Geschoss. Wände/Öffnungen **nicht** als anonyme Polygonteile; **stabile IDs** für alle fachlichen Elemente; Geschosse mit eindeutiger Höhen-/Ebenenreferenz; Räume aus belastbarer Topologie abgeleitet/bestätigt; Öffnungen fachlich an Wände gebunden; Einheit/Koordinatensystem am Modellvertrag festgelegt. Erste UI **darf** auf ein gewähltes Gebäude/Geschoss begrenzt sein — **das Schema nicht**.
7. **LiDAR/Scan/Mesh/Punktwolke:** Import-/Vorschlagsquellen. Kette: Scanrohdaten → versioniertes Scan-Zwischenformat → Vorschläge → Konfidenz/Warnungen → Kontrollmaße → Nutzerprüfung → Topologieprüfung → bestätigte Übernahme → kanonisches Modell. **Nie** direktes Schreiben in die kanonische Geometrie; **kein** Auto-Mesh-Modell; **keine** stillen Defaults.
8. **PyMuPDF:** produktive Erweiterung/Neunutzung **gesperrt** bis Lizenzklärung (AGPL v3). In diesem Slice **nicht** entfernen/aktualisieren/ersetzen/umbauen — nur dokumentieren. Späterer Entscheid: kommerzielle Lizenz / Alternative / isolierter Dienst / Entfernung.
9. **Google-Solar (o.ä.):** späterer **optionaler** Provider (Dachhinweise/Einstrahlung/Luftbilder/Segmente/Standort). **Darf nicht** kanonische Geometrie überschreiben/Hauptquelle für Flächen/Azimut werden/ohne Key zwingend sein/Secrets im Repo. Erfordert Feature-Flag, Secret via Umgebung, Timeout, graceful degradation, Herkunft/Zeitstempel, Datenqualitätskennzeichnung, keine Auto-Übernahme.
10. **Security — ungeschützte Demo-Routen** (`/roofs`,`/solar`,`/testnav*`): eigener kleiner **Security-Slice**. Vor jeder sichtbaren/extern erreichbaren Auslieferung des 2D-/3D-Planers müssen Demo-/Prototyp-Routen entfernt/geschützt/auf lokal beschränkt sein. **Nicht in diesem Slice** Routes/Middleware/Controller/Demos ändern. Blockiert nicht das Schema, aber spätestens Editor-Integration/Browser-Rollout/Viewer-Auslieferung.

**Nächster erlaubter Slice:** **3D-1A** (kanonischer JSON-Schema-/Modellvertrag). **Gesperrt** (eigener Startblock nötig): Migration, DB-Schema, 2D-Editor, Three.js-Viewer, Security-Routenfix, LiDAR, Playground-Portierung, P1b-2. **Dieser Governance-/Doku-Slice gibt 3D-1A nicht automatisch frei.**

---

## F1. Zu entscheidende Fragen (Kurzantworten aus 0A)
1. **Welche Quelle ist heute am nächsten an der Zielwahrheit?** → **`anforderungsprofile.gebaeude_geometrie`** (G1): objektverankert, versioniert (append-only, 1 aktiv/Objekt), gate-geschützt, von Heizlast konsumiert. Kein anderer Store ist versioniert oder objektverankert.
2. **`gebaeude_geometrie` erweitern oder durch `building_models` ablösen?** → Kernentscheidung, s. Varianten.
3. **Wie bleibt G0c-2 erhalten?** → Versionierung/Objektanker/Gate müssen in jeder Variante erhalten bleiben; kein destruktiver Umbau; append-only.
4. **Modellrevision?** → am Profil-Kopf (`version/status/abgeloest_durch_id`) bzw. an einer neuen versionierten Ergebniseinheit; genau eine aktive Revision.
5. **Unveränderliche Modellversionen?** → append-only; abgelöste Versionen bleiben lesbar (nicht gelöscht).
6. **Welche Geometrien migrieren?** → G2 `raum_geometrien` (Roh-2D) als Editier-Herkunft; playground G3 nur nach Schema-Rekonstruktion + Objekt-Mapping; A2 PV-Dach später andocken. Details 0C.
7. **Welche Stores read-only?** → G2 nach Umzug read-only; A1 `RoofAreaEstimator` bleibt Schätzer/lesend.
8. **Welche Stores sperren?** → G5 (wberechnung Mirror) irrelevant; hartkodierte MODULE_TYPES (U8) nie übernehmen.
9. **Wie lesen Heizlast/PV/Dach/Fenster/Türen/Fassade?** → alle aus dem **einen** kanonischen Modell (Adapter je Modul), keine eigene Geometrie-Kopie.
10. **Playground-/Scan-Daten einbinden?** → nur als **Vorschlag** über Import-/Scan-Pipeline → Gate → Modell; nie automatisch führend.
11. **Welche Daten nicht übernehmen?** → Demo-Gebäude (K3), UI-/Szenenstate (K4), unsichere Werte (K5: BBox-Dachfläche, geschätzter Azimut, hartkodierte Module).
12. **Wie zweite Wahrheit technisch verhindern?** → **Single-Write** (ein fachlicher Schreiber je Wert), Dual-Read erlaubt, **kein Dual-Write**; 3D-Viewer rein abgeleitet; Wächter-Test gegen Zweit-Schreibpfade (wie G0b/G0c bereits).

---

## F2. Varianten

### Variante A — `gebaeude_geometrie` bleibt kanonischer Hauptanker (erweitern)
Das bestehende JSON-Feld am Profil-Kopf wird um 3D-taugliche Struktur (Geschosse, Wände mit Anschlüssen, Höhen-Z, Dach-Vorbereitung, Nordwinkel, Einheit/Koord, Herkunft) **additiv** erweitert; Editor/Viewer arbeiten darauf.
- **Vorteile:** G0c-2 bleibt unverändert erhalten (Versionierung/Objektanker/Gate schon da); **keine** neue Tabelle; kleinste Migration; Heizlast-Adapter bleibt; geringstes Zweite-Wahrheit-Risiko.
- **Nachteile:** JSON-Blob wird groß/komplex (Mehrraum/Geschoss/Dach); keine relationale Abfragbarkeit einzelner Bauteile; Schema-Evolution im JSON diszipliniert zu halten.
- **Migrationsrisiko:** niedrig (additiv im JSON). **Komplexität:** niedrig-mittel. **Rückwärtskompatibilität:** hoch. **Testbarkeit:** gut (bestehende Fixtures/Gate). **Heizlast:** unverändert. **PV/Dach:** Dach als JSON-Teil, PV liest daraus. **Playground-Reuse:** Persistenz-Schema (U3) nur teil-nutzbar. **LiDAR-Eignung:** ausreichend (Scan → Vorschlag → JSON).

### Variante B — `building_models` als neue kanonische, relationale Wahrheit (kontrollierte Migration)
Neue additive Tabellen (`building_models`, `building_storeys`, `building_walls`, `building_rooms`, `building_openings`, …) mit Revision/Version; `gebaeude_geometrie` wird belegt stillgelegt (read-only nach Cut-over).
- **Vorteile:** saubere relationale Abfragbarkeit (Bauteile, PV-Flächen, Öffnungen einzeln); klare Revision/Historie; skaliert auf Mehrraum/Geschoss/Dach/Fassade; nahe am playground-Schema (U3).
- **Nachteile:** **mehrere Migrationen** (Pflicht-Stopps); Heizlast-Adapter muss umgestellt (Regressionsrisiko gegen die Golden-Heizlast); G0c-2-Invarianten neu nachzubauen; größter Umbau; Cut-over + Backfill nötig.
- **Migrationsrisiko:** hoch. **Komplexität:** hoch. **Rückwärtskompatibilität:** über Adapter. **Testbarkeit:** gut, aber viel Neu-Test. **Heizlast:** Umstellung + Äquivalenznachweis nötig. **PV/Dach:** sauber integrierbar. **Playground-Reuse:** hoch. **LiDAR:** sehr gut.

### Variante C — gestufter Übergang mit Kompatibilitätsadapter (empfohlen)
`gebaeude_geometrie` bleibt zunächst kanonisch (Variante A für 2D-Kern + Heizlast); parallel wird ein **Adapter/Schema-Vertrag** (3D-1A) definiert, der spätere relationale Ablösung (Variante B) ermöglicht, **ohne** heute die Heizlast-Kette zu brechen. Dach/PV/LiDAR docken über den Adapter an. Umstellung erfolgt slice-weise mit Dual-Read/Single-Write.
- **Vorteile:** G0c-2 bleibt sofort erhalten; kleinster Sofort-Schritt; Option auf relationale Struktur offen; jede Stufe einzeln prüfbar; kein Big-Bang.
- **Nachteile:** Adapter-Disziplin nötig; „welche Stufe wann" muss geführt werden (Fahrplan).
- **Migrationsrisiko:** niedrig (gestuft, additiv). **Komplexität:** mittel. **Rückwärtskompatibilität:** hoch. **Testbarkeit:** hoch (je Stufe Golden-Master). **Heizlast:** unverändert bis bewusster Umstieg. **PV/Dach:** über Adapter. **Playground-Reuse:** mittel-hoch. **LiDAR:** gut.

## F3. Empfehlung (Planner)
**Variante C (gestufter Übergang, `gebaeude_geometrie` bleibt zunächst kanonisch).** Begründung: erhält G0c-2 + Heizlast-Golden sofort, kleinster additiver Sofort-Schritt, hält die relationale Option (Variante B) über einen definierten Schema-Vertrag (3D-1A) offen, und verhindert per Single-Write die zweite Wahrheit. Die relationale Ablösung (Richtung B) erst, wenn Mehrraum/Geschoss/Dach den JSON-Blob nachweislich sprengen — als eigener, migrations-gestützter Slice mit Äquivalenznachweis gegen die Heizlast-Golden.

**Framework-Weiche (separat, Yama):** 3D-Viewer als **read-only abgeleitete Insel** (Three.js) ODER vorerst nur 2D. Der playground-Bundle-Reuse ist ohne Frontend-Source nur Referenz.

> **Diese Empfehlung ist KEINE Yama-Freigabe zur Migration oder Implementierung.** Sie ist die Entscheidungsgrundlage für 3D-0B/Framework-Weiche + 3D-1A.

## F4. Entscheidungsstand (historische Vorlage-Fragen)
> **Hinweis:** Punkte 1/2/3/6 sind durch den **RATIFIZIERT-Block oben (2026-07-15) entschieden** und haben Vorrang; diese Liste bleibt nur als Nachvollziehbarkeit der ursprünglichen Vorlage stehen.
1. Variante A/B/C → **entschieden: C** (RATIFIZIERT 1). 2. Framework 3D-Viewer → **entschieden: isolierte Three.js-Insel, React nur Referenz** (RATIFIZIERT 3/4). 3. Azimut-Konvention → **entschieden: 0°=Nord** (RATIFIZIERT 5). 6. Mehrraum/Geschoss-Modelltiefe → **entschieden: verbindliche Hierarchie** (RATIFIZIERT 6).
**Weiterhin offen (spätere eigene Entscheide):** 4. PyMuPDF-AGPL-Lizenz (gesperrt bis Klärung). 5. Google-Solar-Anbindung/Key. 7. Ob playground-Frontend-Source beschafft wird (eigener Inventurslice).
