# Generator-Auftrag — Bauplaner-Werkzeugsatz kuratieren + fehlende Bauteile

**Rolle:** Generator (VS Code). **Heimat-App:** `ticket`. **Ausgestellt von:** Planner, 2026-07-23.
**Befund (Yama, berechtigt):** Die Topbar zeigt 54 Werkzeuge — fast alle **InDesign-Layout-Werkzeuge**, die
in einen Bauplaner nicht gehören. Gleichzeitig **fehlen tragende Bauteile** (Decke/Unterzug/Stütze/Träger/
Fundament) und die **Geschoss-Bedienung**.

## A — Topbar entrümpeln (Bau-Werkzeugsatz statt DTP)
**Raus** aus der Nutzer-Topbar (DTP/Layout, baufremd): Textwerkzeug, Seitenwerkzeug, Lückenwerkzeug,
Zeichenstift, Ankerpunkt hinzufügen/löschen/umwandeln, Bleistift, Glätten, Pfad löschen, Rechteck-/Ellipsen-/
Polygonrahmen, Schere, Notiz, Pipette, Fläche/Kontur/tauschen, Standardfarben, Container formatieren,
Normal-/Vorschauansicht, Objektstil, Seiten, Ebenen (DTP), Farbfelder, Preflight, Referenzpunkt,
Proportionen, Ausrichten/Verteilen (DTP-Layout).
**Bleibt** (bau-relevant, allgemein): Auswahl, Direktauswahl (Punkte/Kontur bearbeiten), Verschieben/Hand,
**Messen/Bemaßung**, Zoom, Drehen.
*(Nicht löschen — nur in der Nutzer-Topbar ausblenden; Registry-Eintrag darf bleiben, `sichtbar:false`/Gruppe
„erweitert". Eine Wahrheit, keine Datenlöschung.)*

## B — Bauteil-Werkzeuge (das echte Bauplaner-Set, als Icon-Gruppen)
- **Struktur:** Wand ✓ · **Stütze/Pfeiler** (neu) · **Unterzug** (neu, linear unter Decke) · **Träger/Überzug**
  (neu, linear) · **Decke** (bereits beauftragt: `generator-auftrag-decke-geschossdecke.md`) ·
  **Fundament/Bodenplatte** (neu, Platte wie Decke gegen Erdreich) · Dach ✓.
- **Öffnungen:** Fenster ✓ · Tür ✓.
- **Erschließung:** Treppe ✓.
- **Fachplaner (öffnet Panel, kein Zeichen-Modus):** Heizlast · FBH · Heizkörper · PV · Wandaufbau … (Icon-Gruppe).
**Modell (additiv):** Stütze = Punkt-Bauteil (Position + Querschnitt); Unterzug/Träger = Linien-Bauteil
(start/end + Höhe/Breite, wie `WallNode`, aber horizontal/tragend); Fundament = Platte (wie `CeilingNode`,
`bauteil:'boden'`). Je optionales Feld/eigene Sammlung (Muster `roofs`/`ceilings`) → kein 422. **Reihenfolge:**
Decke zuerst (Blocker), dann Unterzug/Stütze/Träger, dann Fundament — je eigener P→G→E-Slice.

## C — Geschoss-Bedienung
Kleine **Geschoss-Verwaltung** (Topbar-Umschalter oder schmales Panel): aktuelles Geschoss anzeigen,
**Geschoss anlegen / wechseln / aus Vorlage duplizieren** (Modell hat `levels` + `geschossVorlage.ts`).
Höhen-Stapel aus Wandhöhe + Deckendicke (siehe Decken-Auftrag) — eine Wahrheit.

## Abnahme (Evaluator)
1. Topbar zeigt nur den **Bau-Werkzeugsatz** (keine DTP-Werkzeuge mehr in der Nutzer-Ansicht); ausgeblendete
   bleiben in der Registry (keine Datenlöschung).
2. Bauteil-Gruppen vorhanden; die neuen (Stütze/Unterzug/Träger/Fundament) additiv, kein 422, mit Icon+Tooltip.
3. Geschoss anlegen/wechseln/duplizieren funktioniert; Stapel-Höhe konsistent.
4. Token-Disziplin (0 Hex), A11y, 3-Viewport; additiv, nur `auto/`-Branch, kein Push.

## Guardrails
Additiv; eine Wahrheit (Registry, `dachRoh`, `wandaufbau`); DTP-Tools nur ausblenden, nicht löschen; jede
neue Bauteil-Art ein eigener kleiner Slice (nicht alles in einem). Meldung „umgesetzt" → Evaluator.
