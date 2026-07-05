# Universelles Auslegungs-Konzept (Zielrahmen, gerätetyp-agnostisch)

> **Status:** Design-Zielrahmen (Stufe 1, kein Bau). Hieran werden **B2a/B3/B4** ausgerichtet. Baut auf dem
> Geräte-Spec-Standard auf (`docs/spec-import/00-spec-standard.md`, `eignungspruefung.md`). Stand 2026-07-05.

## Kernprinzip
**Der Bedarf wird EINMAL gerechnet, die Kandidaten werden VIELE Male dagegen bewertet.** Der teure Teil
(Heizlast, Klima, Vorlauf) passiert einmal je Objekt; der Vergleich eines weiteren Geräts kostet fast nichts.
Das macht herstellerübergreifende Beratung (3–10 Varianten) praktisch umsonst — und ist die Rechtfertigung
für die saubere Trennung *Anforderungsprofil ↔ Bewertung*.

---

## Die vier Phasen

### Phase 1 — Bedarf einfrieren  →  Datenobjekt **Anforderungsprofil** (versioniert)
Kunde/Objekt → normiertes Anforderungsprofil, **unabhängig vom Gerät**.
- **Bausteine:** Heizlast Φ_HL (**B2a**, DIN EN 12831-Kern-Port) · Klima/Norm-Außentemperatur (**B2b**) ·
  Vorlauftemperatur — **gekoppelt an das Heizkörper-Modul** (min. Vorlauf aus `CompatibilityService`/
  `RadiatorPerformanceService`, bereits live) · Warmwasser-Bedarf · Sperrzeiten (EVU).
- **Eigenschaft:** versioniert (jede Änderung = neue Version; alte Auslegungen bleiben nachvollziehbar).
- **Warum einfrieren:** ist der Bezugspunkt für ALLE Kandidaten — darf sich während des Vergleichs nicht ändern.

### Phase 2 — Kandidaten filtern  →  Kandidatenliste
Aus dem Katalog nur Geräte mit **`auslegungsstatus='auslegungsfaehig'`** (Prüfer, `eignungspruefung.md` §3).
`nur_handelsdaten` erscheint hier nicht (bleibt im Angebots-/Bestellkontext nutzbar).
- **Zwei Modi, ein Mechanismus** (nur der Filter unterscheidet sich):
  - **innerhalb eines Herstellers** (z. B. „welche Buderus passt"),
  - **herstellerübergreifend** (NIBE vs. Viessmann vs. Buderus).
- **Baustein:** `SpecEligibilityService` + Katalog (`products` + Spec-Tabellen).

### Phase 3 — Bewertungs-Schleife  →  Datenobjekt **Bewertungsobjekt** (je Kandidat identisch)
Je Kandidat dasselbe Bewertungsobjekt gegen das eine Anforderungsprofil:
- Deckung am **Auslegungspunkt** (Φ_max ≥ Φ_HL?) · **Bivalenzpunkt** + Strom-Split (`BivalenzService`-Port) ·
  **JAZ** (`WpKennlinieService`/`JazService`-Port) · Jahresstrom · Schall · **Wirtschaftlichkeit** (**B3**,
  `SanierungsWirtschaftlichkeitService`).
- **Datenlage-Ausweis zwingend:** je Wert sichtbar `verifiziert` vs. `importiert_ungeprueft`; fehlende Werte
  als **„—"**, nie geraten (erbt aus `herkunft.verifikations_status`).
- **Baustein:** die portierten Rechenkerne (B2a/B2), Datenlage aus dem Spec-Standard.

### Phase 4 — Vergleichs-Matrix  →  Datenobjekt **Auslegungs-Varianten** (am Projekt)
Kandidaten = Spalten, Kriterien = Zeilen, **Ampel-Muster aus dem Heizkörper-Modul** (grün/gelb/rot je Kriterium).
- Gewählte Variante → **Projekt-Auslegung** → **Angebots-Positionen** über das bereits gebaute
  **v-c-2-Übernahme-Muster** (`89e175f`: Provenance, Replace-per-Variante).
- **Nicht gewählte Varianten (3–10) bleiben gespeichert** — Beratungsnachweis + Rohmaterial für das
  **B4-Energiekonzept** (mehrere Szenarien in einem Bundle).

---

## Gerätetyp-Agnostik — dasselbe Muster, andere Achsen

| Phase | Wärmepumpe | PV-Anlage | Batterie | (später) Fenster |
|---|---|---|---|---|
| 1 Bedarf | Heizlast Φ_HL, Vorlauf, WW | Dachfläche, Ausrichtung, Verbrauch | Autarkieziel, Lastprofil | U-Wert-Ziel, Fläche |
| 2 Filter | WP `auslegungsfaehig` | Modul+WR `auslegungsfaehig` | Speicher `auslegungsfaehig` | Fenster-Spec |
| 3 Bewertung | Deckung, JAZ, Bivalenz, € | Ertrag (PVGIS), String-Passung (`InverterSizingService`), € | nutzbare kWh, Zyklen, Autarkie, € | U-Wert, g-Wert, € |
| 4 Matrix | Ampel-Vergleich → Variante | Ampel-Vergleich → Variante | Ampel-Vergleich → Variante | Ampel-Vergleich |

Das Vier-Phasen-Gerüst (Anforderungsprofil → Filter → Bewertungsobjekt → Matrix/Variante) ist **identisch**;
je Gerätetyp wechseln nur Bedarfsgrößen, Mindestprofil (`eignungspruefung.md` §1) und Bewertungskriterien.

> **Batterie-Ehrlichkeit:** Phase 3 „nutzbare kWh/Zyklen/Autarkie" braucht Felder, die die heutigen Kerne
> **nicht lesen** (nur WR-Kompatibilität, `eignungspruefung.md` §1.4). Echte Batterie-Auslegung ist damit ein
> **B3/B4-Neubau** (Bedarfsseite Lastprofil + Bewertungslogik) — kein Datenimport-Problem, sondern ein
> Rechenkern, der noch fehlt. Hier als Lücke benannt, nicht überspielt.

---

## Andockpunkte (bestehende, belegte Bausteine)
- **live in ticket:** Heizkörper-Vorlauf-Kopplung (`CompatibilityService`/`RadiatorPerformanceService`),
  v-c-2-Übernahme-Muster (`89e175f`), Prüfer-Regelquelle = Import-Regelquelle (`SpecSchema`).
- **Teil B (Port nötig):** Heizlast-Kern (B2a) · Klima (B2b) · WpKennlinie/Bivalenz/JAZ (B2a) ·
  PVGIS/InverterSizing (B2a) · Wirtschaftlichkeit (B3) · Energiekonzept-Bundler (B4).
- **Datenobjekte (neu, B2a→B4):** Anforderungsprofil (versioniert) · Bewertungsobjekt · Auslegungs-Varianten
  am Projekt. Diese drei sind der rote Faden; ihre Schemas entstehen mit den jeweiligen B-Slots.
