# Universelles Auslegungs-Framework (Zielrahmen, alle Domänen)

> **Status:** Design-Zielrahmen (Stufe 1, kein Bau). Hieran werden **B2a/B3/B4 und alle künftigen
> Konfiguratoren** ausgerichtet. Baut auf dem Geräte-Spec-Standard auf (`docs/spec-import/00-spec-standard.md`,
> `eignungspruefung.md`). Stand 2026-07-05.

## Kernprinzip
**Der Bedarf wird EINMAL gerechnet, die Kandidaten werden VIELE Male dagegen bewertet.** Der teure Teil
(Heizlast, Klima, Ertragsprofil) passiert einmal je Objekt; ein weiteres Gerät zu vergleichen kostet fast
nichts. Das trägt herstellerübergreifende Beratung (3–10 Varianten) — und ist die Rechtfertigung der Trennung
*Anforderungsprofil ↔ Bewertung*.

---

## 4.1 Das abstrakte Framework (einmal definiert, für alle Domänen gültig)

**Vier Phasen — drei Domänen-Steckplätze (S1/S2/S3) — ein geteilter Unterbau.**

| Phase | Datenobjekt | fix (geteilt) | Domänen-Steckplatz |
|---|---|---|---|
| **1 Bedarf einfrieren** | **Anforderungsprofil** (versioniert, am Projekt/Deal verankert; **Quelle je Eingabewert:** `gemessen`/`berechnet`/`geschaetzt`) | Versionierung, Projekt-Verankerung | **S1** — welche Bedarfsgrößen |
| **2 Kandidaten filtern** | Kandidatenliste | Filter-Mechanik, 2 Modi (Hersteller-intern / -übergreifend) | (Mindestprofil aus `eignungspruefung.md` §1) |
| **3 Bewertungs-Schleife** | **Bewertungsobjekt** (je Domäne identische Felder) + Datenlage-Ausweis | Datenlage-Ausweis (`verifiziert`/`ungeprueft`, fehlend = „—") | **S2** — welcher Rechenkern |
| **4 Vergleichs-Matrix** | **Projekt-Auslegung** + gespeicherte Varianten | Matrix-Rendering, Ampel-Muster (HK-Modul), v-c-2-Übernahme | **S3** — Kriterien + Ampel-Schwellen |

**Datenlage-Ehrlichkeit beginnt beim Bedarf, nicht erst beim Gerät:** jeder Eingabewert in Phase 1 trägt
seine Quelle. Eine WP „präzise" gegen eine **geschätzte** Heizlast zu rechnen ist der verbotene Zustand (§4.4).

**Nur EINMAL gebaut (domänen-unabhängig):** Filter-Mechanik · Varianten-Verwaltung · Matrix-Rendering ·
Datenlage-Ausweis · Übernahme ins Angebot (v-c-2-Muster, `89e175f`). **Je Domäne gebaut (die 3 Steckplätze):**
S1 Anforderungsprofil-Definition · S2 Bewertungs-Kern · S3 Kriterien-Satz.

---

## 4.2 Domänen-Ausprägungen

| Domäne | **S1 Anforderungsprofil** | **S2 Bewertungs-Kern** | **S3 Kriterien (Matrix-Zeilen)** | Slot |
|---|---|---|---|---|
| **a) Wärmepumpe** | Φ_HL (Heizlast) + Klima + **Vorlauf (HK-Modul-Kopplung!)** + WW + Sperrzeiten | `WpKennlinieService` / `BivalenzService` / `JazService`-Port | Deckung @ Auslegungspunkt · Bivalenzpunkt · JAZ · Jahresstrom · Schall · Invest/Förderung/Amortisation | B2a (+B3) |
| **b) PV-Anlage** | Dachflächen/Ausrichtung/Neigung/Verschattung + Jahresverbrauch/Lastprofil | `PvProjektService`/PVGIS-Ertrag + `StringBuilderService`/`InverterSizingService` | kWp · spez. Ertrag · Eigenverbrauchsquote · **String-Passung (U_sys/MPP-Fenster!)** · Wirtschaftlichkeit | B2a (+B3) |
| **c) Batterie** | Lastprofil + PV-Ertragsprofil + Autarkie-Ziel | Speicher-Simulation (B2-Kandidat, **fehlt noch**) + `batterie_wr_kompatibilitaet` (**W-C4-Referenz-Katalog!**) | Autarkiegrad · Zyklen/Lebensdauer · nutzbare kWh · WR-Kompatibilität · €/kWh gespeichert | B2/B3 |
| **d) Wechselrichter** | meist **Unter-Auslegung von b)**; eigene Domäne nur bei Repowering/Tausch | `InverterSizingService` | DC/AC-Ratio · MPP-Fenster je String · Phasen · Wirkungsgrad | B2a |
| **e) Fenster** (später) | Bauteil-Liste aus Gebäudeaufnahme (U-Wert Bestand, Maße, Orientierung) | U-Wert-Delta → Heizlast-Reduktion — **nutzt DENSELBEN Heizlast-Kern (Sanierungs-Kopplung!)** | U_w · g-Wert · ΔΦ_HL · Kosten/Förderung · Amortisation | B5+ |
| **f) Fußbodenheizung** | Raum + Φ_HL + Bodenaufbau | EN-1264-Kern (`FussbodenheizungService`) | Vorlauf-Absenkung · Verlegeabstand · Aufbauhöhe | B3-Ende |
| **g) Sanierungspaket** (Meta) | Gebäude + IST-Zustand | Kandidaten = **Maßnahmen-Kombinationen** (Fenster+WP+FBH); Bewertung = Vorher/Nachher-Heizlast + Gesamtwirtschaftlichkeit | Vorher/Nachher-Φ_HL · Gesamt-€ · Förderquote · CO₂ | B3/B4 |

**g) zeigt:** das Framework trägt auch **Bündel** — der Energiekonzept-Bundler (Modul Q, **B4**) dockt genau
hier an (mehrere Varianten/Maßnahmen in einem Angebot). Ein Kandidat ist dann kein Gerät, sondern ein Paket.

---

## 4.3 Die Verbindungs-Regel (der eigentliche Gewinn)

**Domänen teilen den Bedarf** — das Anforderungsprofil ist **pro Projekt einmal** vorhanden und
domänen-übergreifend lesbar, nicht je Konfigurator neu erfasst. Regel: **„Daten fließen vorwärts, nie doppelt
erfasst."**

```
  Gebäudeaufnahme ┐
                  ├─► [Heizlast-Kern B2a] ─► Φ_HL ─┬─► (a) WP-Auslegung
     Klima (B2b) ─┘                                ├─► (e) Fenster  (liefert ΔΦ_HL zurück ─┐)
                                                    ├─► (f) Fußbodenheizung                 │
                                                    └─► (g) Sanierungspaket ◄───────────────┘
                                                            (Vorher/Nachher-Φ_HL)
  Dach + Verbrauch ─► [PVGIS/PvProjekt B2a] ─► PV-Ertragsprofil ─► (b) PV-Anlage
                                                    │
  Lastprofil ───────────────────────────────────────┴─► (c) Batterie (Autarkie)
```

- **Φ_HL** (aus a) ist Input für **e, f, g**; **e** speist eine Heizlast-**Reduktion** ΔΦ_HL zurück (Sanierung).
- **PV-Ertragsprofil** (aus b) ist Input für **c** (Batterie-Autarkie).
- Der **Vorlauf** koppelt a ↔ HK-Modul (live) ↔ f (FBH senkt Vorlauf → bessere JAZ in a).

---

## 4.4 Reihenfolge-Ehrlichkeit (was VOR jeder Domäne fertig sein muss)

| Domäne | Voraussetzung | „präzise gegen geraten" verboten heißt hier |
|---|---|---|
| **a) WP** | **B2a** (Heizlast) + **B2b** (Klima) | keine WP-Deckung ohne belastbares Φ_HL |
| **b) PV** | B2a (PVGIS/String) | kein Ertrag ohne Dach/Verschattung |
| **c) Batterie** | **b) fertig** (PV-Profil) + Speicher-Sim (fehlt) + W-C4 | keine Autarkie ohne PV-Ertragsprofil |
| **d) WR** | b) (ist dessen Unter-Auslegung) | — |
| **e/f/g** | **a) fertig** (Φ_HL als Bezug) | keine Sanierungs-Wirtschaftlichkeit ohne Vorher-Heizlast |

**Keine Domäne verspricht einen Vergleich ohne belastbaren Bedarf.** Solange die Vorbedingung fehlt, bleibt
die Domäne im Zielrahmen sichtbar, aber nicht gebaut — der Datenlage-Ausweis (§4.1) macht jede Schätzung im
Bedarf sichtbar, damit nie „präzise gegen eine geratene Zahl" gerechnet wird.

---

## Andockpunkte (bestehende, belegte Bausteine)
- **live in ticket:** HK-Vorlauf-Kopplung (`CompatibilityService`/`RadiatorPerformanceService`),
  v-c-2-Übernahme (`89e175f`), Prüfer-Regelquelle = Import-Regelquelle (`SpecSchema`), Datenlage-Ausweis
  (`verifikations_status` aus dem Spec-Standard).
- **Teil B (Port/Neubau nötig):** Heizlast (B2a) · Klima (B2b) · WpKennlinie/Bivalenz/JAZ (B2a) ·
  PVGIS/InverterSizing/StringBuilder (B2a) · Wirtschaftlichkeit (B3) · FBH EN-1264 (B3) ·
  **Speicher-Simulation (fehlt ganz)** · Energiekonzept-Bundler (B4) · Fenster (B5+).
- **Referenz-Kataloge (W-C4):** `batterie_wr_kompatibilitaet` (Domäne c), `materials`/`konstruktionen`/
  `baualtersklassen` (Domänen e/g Heizlast-Input).
- **Datenobjekte (neu, B2a→B4):** Anforderungsprofil (versioniert, Quelle je Wert) · Bewertungsobjekt ·
  Auslegungs-Varianten am Projekt — der rote Faden durch alle Domänen; Schemas entstehen mit den B-Slots.
