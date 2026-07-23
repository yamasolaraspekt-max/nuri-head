# Generator-Auftrag — Werkzeuge als Icons in die Topbar (statt Fähigkeiten-Liste)

**Rolle:** Generator (VS Code). **Heimat-App:** `ticket`. **Ausgestellt von:** Planner, 2026-07-23.
**Korrektur (Yama, berechtigt):** Werkzeuge gehören als **Icons in die Werkzeugleiste oben**, nicht als
Text-Liste. Der Hausplaner hat bereits eine Icon-Werkzeugleiste (`HausplanerApp.tsx` ~Z.731, datengetrieben
aus der Tool-Registry). **Nichts von Batch 0 wird verworfen — nur die Darstellung wechselt.**

## Ziel & Entscheidung
1. **Registry behalten** (Batch-0-Datenquelle: Name · Icon · Zustand · Funktion/Tooltip).
2. **Darstellung: Icons in der Topbar**, nicht Liste. In der bestehenden Werkzeugleiste zwei Gruppen:
   - **Zeichen-/Bedien-Werkzeuge** (Wand/Fenster/Tür/Treppe/Dach/Auswahl…): schon als Icons — unverändert,
     neue (Dachformen) mit aufnehmen.
   - **Fachplaner/Berechnungen** (bisher „schläft"-Engines: Heizlast/FBH/Heizkörper/PV/Abwasser/Wandaufbau…):
     eigene **Icon-Gruppe**; jedes ein Icon-Button mit Tooltip (Name + Funktion); Klick öffnet das
     **Eingang→Ergebnis-Panel** (Batch-1-Muster `EnginewerkzeugPanel`).
3. **Text-Liste `FaehigkeitenNavi` aus der Nutzer-Ansicht entfernen** — Status „aktiv/schläft" bleibt nur
   als internes Kontroll-Bild (HTML-Übersichten), nicht in der Seitenleiste.

## Icons
Bestehende `/icons/<id>.svg` (InDesign-Toolkit-Stil) weiter; fehlende Fachplaner-Icons schlicht ergänzen
(Linien, 24er-viewBox). Noch-kein-Panel: Icon **gedimmt + Tooltip „Panel folgt"** (Farbe UND Text), nicht
ausgeblendet — Sichtbarkeit bleibt, aber als Icon.

## Nahtstellen
`HausplanerApp.tsx` (~Z.731): Fachplaner-Icon-Gruppe ergänzen (datengetrieben, Klick→Panel).
`FaehigkeitenNavi.tsx` aus der Studio-Seitenleiste aushängen. `T`-Tokens, 0 Hex, A11y.

## Abnahme (Evaluator)
1. Werkzeuge als **Icons in der Topbar** mit Tooltip; keine Text-Liste mehr.
2. Fachplaner-Engines als Icon-Gruppe; Klick öffnet Panel (mind. die gebauten).
3. Registry = eine Wahrheit; Zeichen-Werkzeuge ohne Regression (UI-2-SSOT intakt).
4. 0 Hex, A11y (gedimmtes Icon trägt Text), 3-Viewport-Sicht. Additiv, nur `auto/`-Branch, kein Push.

## Guardrails
Registry unverändert nutzen; bestehende Werkzeugleiste nicht brechen; kein Beifang. **UI/UX-Slice — Logik
der Werkzeuge/Engines unberührt.** Meldung „umgesetzt" → Evaluator.
