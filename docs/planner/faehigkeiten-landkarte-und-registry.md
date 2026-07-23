# Fähigkeiten-Landkarte + Registry-Konsolidierung (Batch 0, Startblock)

**Rolle:** Generator im Planner-Modus (read-only Ableitung aus dem CODE). **Stand:** 2026-07-23.
**Zweck:** Grundlage für die „Fähigkeiten-Navigation" (jede Engine sichtbar & bedienbar). Kein
Produktivcode, kein Commit — Entscheidungsvorlage. **Ist-Beleg aus dem Code**, nicht aus Papier.

> **UI-Slice ⇒ Pflicht-Fachagenten + Styleguide + Browser-Abnahme.** Batch 0 baut React-UI im
> Insel-Bundle → nach `CLAUDE.md` sind Konzeption/Workflow/Architektur/Frontend-Agent + `/admin/styleguide`
> + Screenshot-Abnahme (1440/1024/375) bindend.

---

## 1. Ist-Zustand (gemessen)

- **ZWEI Werkzeug-Register existieren** (die „eine Wahrheit" ist schon verletzt):
  - `app/tools/toolRegistry.ts` → `TOOL_DEFINITIONS` (**8 echte Hausplaner-Werkzeuge**: auswahl/wand/
    fenster/tuer/dach/treppe + loeschen/duplizieren), Typ `ToolDefinition`, `art:'werkzeug'|'aktion'`,
    Activation-Engine (`resolveToolState`), `werkzeugTools()`. **Korrekte Domäne.**
  - `app/tools/toolCatalog.ts` → `TOOL_KATALOG` (**54 InDesign-Tools**, UI-3b) mit literalen DTP-Semantiken
    (Textwerkzeug, Zeichenstift/Bézier, Rechteckrahmen, Farbfelder, Preflight …), `katalogTool()`.
    **Fach-fremd** für einen Bauplaner; kollidiert mit `CLAUDE.md` „Produkt bleibt manueller Profi-Planer".
- **Bestehende FACHPLANER-Navi** in `HausplanerApp.tsx` (`const FACHPLANER`, Attrappe): Gruppen
  `Haustechnik · PV-Planer · Bauelemente · Bad · Küche` mit Text-Items (teils leer) — **ohne Verhalten**.
- **49 geometry-Module** (ohne Tests): **13 echte Rechen-Engine-Panel-Kandidaten**, 31 interaktiv,
  5 Infrastruktur/Helfer (kein eigenes Panel).

---

## 2. Fähigkeiten-Landkarte (echte Export-Namen)

### 2a. Rechen-Engines → je ein Eingang→Ergebnis-Panel (die „🔴-Leichen")

| Fähigkeit | Modul | Echter Export | Eingang → Ausgang | Gruppe |
|---|---|---|---|---|
| Fußbodenheizung-Auslegung | `fbhAuslegung.ts` | `fbhAuslegung` | `FbhEingabe → FbhErgebnis` (Heizfläche, Rohrlänge, Kreise) | TGA/Heizung |
| Heizkörper-Leistung | `heizkoerperLeistung.ts` | `betriebsLeistung`, `benoetigteNormleistung`, `bewerteDeckung` | Normleistung + Bedingungen → Deckung (EN 442) | TGA/Heizung |
| Heizkreis-Verteiler | `heizkreisVerteiler.ts` | `auslegeVerteiler`, `kreisDurchfluss` | `HeizkreisEingabe[] → VerteilerErgebnis` (Abgänge, Massenstrom) | TGA/Heizung |
| Abwasser-Gefälle | `abwassergefaelle.ts` | `pruefeAbwasser`, `mindestGefaelle` | `AbwasserEingabe → AbwasserErgebnis` (DIN 1986-100) | Sanitär |
| Küchen-Arbeitsdreieck | `kuecheArbeitsdreieck.ts` | `bewerteArbeitsdreieck` | `Arbeitsdreieck → DreieckErgebnis` (DIN 18022) | Küche |
| PV-Schnellbelegung | `pvBelegung.ts` | `pvSchnellBelegung` | `PvEingabe → PvBelegung` (Modulzahl, kWp) | Energie-PV |
| U-Wert (Wandaufbau) | `wandaufbau.ts` | `berechneUWert` | `Schicht[] → UErgebnis` (DIN EN ISO 6946) | Bau/Wandaufbau |
| Fenster Uw/RC/Preis | `fensterProdukt.ts` | `berechneUw`, `preisFenster`, `rcMachbar` | `UwEingabe → UwErgebnis` (ISO 10077, EN 1627) | Fenster/Tür |
| Sparren-Vorbemessung | `sparrenBerechnung.ts` | `berechneSparren` | `SparrenEingabe → SparrenErgebnis` (Eurocode 5) | Dach/Zimmerei |
| Treppen-Auslegung | `treppenBerechnung.ts` | `berechneTreppe` | `TreppenEingabe → TreppenErgebnis` (DIN 18065) | Treppe |
| Holz-Mengen (BOM) | `holzMengen.ts` | `holzMengenAusListe` | `HolzStueck[] → HolzMengen` | Dach/Zimmerei |
| Holz-Bauteile (BOM) | `holzBauteile.ts` | `holzBauteileAusListe` | Holzliste → Pfetten/Grat/Kehl | Dach/Zimmerei |
| Schifter-Liste | `schifterListe.ts` | `klassifiziereSchifter`, `schifterMengen` | Fläche → Schiftsparren + Stückliste | Dach/Zimmerei |

> **Namensabgleich:** Alle 12 im Auftrag genannten Fähigkeiten existieren — die Auftrags-Bezeichnungen
> waren **Modulnamen**, nicht Export-Funktionen. Oben stehen die **echten Exports** (Byte-Treue: nur aufrufen).

### 2b. Interaktive Werkzeuge (setzen `activeToolId`/platzieren Bauteile — in die Navi einhängen)
Wand, Fenster, Tür, Dach, Treppe, Auswahl, Löschen, Duplizieren (schon in `TOOL_DEFINITIONS`), plus
Bauteil-/Katalog-Backing: Gauben/Aufbauten (`gaubeGeometrie`, `aufbauPlatzierung`), Öffnungen
(`oeffnungsTypen/-Bauarten`), Treppen-Zeichnung (`treppenTypen/treppe2D/3D/Svg`), Heizkörper
(`heizkoerperTypen`), Snapping/Editieren/Maßketten (`fangKern`, `editierGeometrie`, `masskette`, `bemassung`).

### 2c. Infrastruktur (NICHT als eigene Kachel; Bibliothek hinter den Werkzeugen)
`dachWerte`, `werkzeugRegistry`, `polygonFlaeche`, `configuratorPackage`, `integrationAbgleich`, `roomDetection`.

---

## 3. Registry-Konsolidierung — die eine Wahrheit (Kern-Entscheidung)

**Problem:** zwei Register + eine dritte Kategorie (Rechen-Engines) ohne Registry.

**Vorschlag (empfohlen):** **EINE** Fähigkeiten-Registry auf Basis von `toolRegistry.ts` (korrekte Domäne),
das `ToolDefinition`-Modell additiv zu einer **`Faehigkeit`** erweitert:

```
Faehigkeit = ToolDefinition + {
  art: 'werkzeug' | 'aktion' | 'engine'     // 'engine' neu = Rechen-Panel
  gruppe: 'dach-zimmerei' | 'tga-heizung' | 'energie-pv' | 'sanitaer' | 'kueche'
        | 'bau' | 'fenster-tuer' | 'treppe' | 'werkzeuge'
  zustand: 'aktiv' | 'schlaeft'             // schläft = registriert, noch kein Panel/Handler
  // nur für art:'engine':
  engineModul?: string                       // Doku-Referenz (z. B. 'geometry/fbhAuslegung')
  panel?: { eingang: FeldSpec[]; ausgang: FeldSpec[] }  // treibt das Eingang→Ergebnis-Panel
}
```

- **`TOOL_KATALOG` (InDesign 54):** wird **nicht** die Basis. Entscheidung nötig (siehe §5): entweder
  **stilllegen** (Beleg-Trail bleibt) oder nur die **CAD-sinnvolle Teilmenge** (Auswahl/Transform/
  Ausrichten/Navigation/Messen) in die eine `Faehigkeit`-Registry **remappen** — literale DTP-Tools
  (Text/Bézier/Rahmen/Farbfelder/Preflight) fliegen raus (Produkt-Scope-Regel).
- **Aktivierung/Zustand:** `resolveToolState` (schon vorhanden) liefert aktiv/deaktiviert + Grund →
  A11y „Farbe **und** Text" ist damit gedeckt.
- **Kein zweites Register:** die Navi rendert **nur** aus dieser einen `Faehigkeit`-Liste (datengetrieben).

---

## 4. Batch-0-Scope (nach Bestätigung) + Fachagenten

**Bauen:** (1) `Faehigkeit`-Modell + konsolidierte Registry (die 8 Werkzeuge + 13 Engine-Fähigkeiten als
`zustand:'schlaeft'` eingetragen, Gruppen gesetzt); (2) **Navigations-Schale** (React): linke Gruppen-Navi
+ Kachel-Hauptbereich (Name · Funktion · Zustand · Ein-/Ausgang), rein aus der Registry; (3) bestehende
FACHPLANER-Attrappe **ersetzen**. **Kein** Panel-Handler und **kein** Engine-Aufruf in Batch 0 (das sind
Batch 1–3) — Batch 0 macht nur **sichtbar** (Zustand angezeigt).

- **Konzeption:** ein Modell für Werkzeug/Aktion/Engine; „schläft" ehrlich markiert.
- **Workflow:** Navi → Gruppe → Kachel → (Werkzeug aktiviert | Engine-Panel — ab Batch 1).
- **Architektur:** eine Registry, `usePlannerUiStore` + `resolveToolState` wiederverwendet; `geometry/*` unberührt.
- **Frontend:** sa-ui-Tokens, Styleguide-Komponenten, Zustand Farbe **+** Text, 3 Viewports.

**Abnahme:** jede Landkarte-Fähigkeit erscheint in der Navi mit Zustand; aktive Werkzeuge unverändert
(keine Regression); eine Registry (git-Diff ohne `geometry/*`); tsc/schema/test/build grün.

---

## 5. Offene Entscheidungen (Yama)

1. **`TOOL_KATALOG` (InDesign 54):** stilllegen **oder** CAD-Teilmenge remappen? (Empfehlung: CAD-Teilmenge
   remappen, DTP-Rest raus — Produkt-Scope.)
2. **Basis-Branch für Batch 0:** aus `e9334bb` (freigegeben) als eigener Navi-Branch, unabhängig vom Dach-Strang?
3. **Gruppen-Schnitt bestätigen** (9 Gruppen oben) — oder an ein vorhandenes „faehigkeiten-landkarte"-Artefakt angleichen, falls es eins gibt.
