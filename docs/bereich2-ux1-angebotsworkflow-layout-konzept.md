# UX-1 — Bereich 2: Angebots-/Auslegungsworkflow — Layout- & Workflow-Konzept (READ-ONLY)

> **Status:** read-only Konzept. **Kein Bau, keine UI-Änderung, kein Commit, kein Push.** Backend + Frontend zusammen betrachtet. Fachagenten-Perspektiven (Konzeption · Workflow · Architektur · Frontend-Design) eingearbeitet.
> **Zweck:** Alle gebauten Bereich-2-Bausteine zu einem klaren, professionellen Workflow-Layout ordnen — von Lead bis Angebot.
> **Bezug:** Paket 1/1b (Reife-Panel/Kanban/Objektprofil) · 2a/2b (Reife-Gate) · P3-c (Auslegungs-Vorschau) · P3-d0a(+fix) (Katalog-Matching) · P3-d2b (OMD/IDS-Preis, blockiert) · Datum 2026-07-13.

---

## 0. Führende Wahrheiten (das Rückgrat — nicht verhandelbar)
| Sachverhalt | Führende Quelle |
|---|---|
| Auslegung (Heizlast/Bivalenz/Gerät) | `Anforderungsprofil` (versioniert, am Objekt/Gewerk verankert) |
| Bedarf (Formular) | `LeadProductChecklistValue` (WP-`ProductFormula`) |
| Angebotsreife (on-the-fly) | `OfferReadinessService` (keine zweite Statuswahrheit) |
| Angebotspositionen | `offer_details.sections` |
| Preis EK/VK | Katalog via `component_id` → `CatalogPriceGuard` (P1-a), gespeist aus OMD/IDS→`distributor_prices` |
| Reife-Gate | `OfferReadinessGate` (vor jedem Angebots-Create) |

**Grundsatz:** Jedes Panel **liest** eine dieser Wahrheiten; keine UI erzeugt eine zweite.

## 1. Bestandsaufnahme — Bausteine, Ort heute, Status
| Baustein | Route/Datei | Typ | Ort heute |
|---|---|---|---|
| Angebotsreife-Panel | `offers.angebotsreife.panel` / `angebotsreife_panel.blade` | read-only | Objektprofil (lazy, 1b-a) + eigene Seite |
| Reife-Badges + Filter | Kanban `kb-reife-*` + `offers.angebotsreife.index` (Batch) | read-only | Kanban-Board (1b-b) |
| WP-Formular/Bedarf | `ProductFormula` + `checklist.blade` + `FormulaEvaluationService`/`VisibleIfService` | read/write | Checklisten-Ansicht |
| Auslegungs-Vorschau (P3-c) | `offers.auslegung.vorschau` / `auslegung_vorschlag_panel.blade` | read-only | (noch nicht eingebettet) |
| WP-Katalog-Matching (P3-d0a) | `offers.wp-katalog-matching` / `wp_katalog_matching_panel.blade` | read-only Diagnose | (noch nicht eingebettet) |
| Reife-Gate (2a/2b) | `OfferReadinessGate` in Create-Pfaden + `useTemplate` | Server-Gate | unsichtbar (422 bei Blocker) |
| Angebots-Wizard | `offers.wizard` → `config.blade` (jQuery, `State.sections`) | write | Wizard |
| Preisstrang OMD/IDS | `SupplierConnection`/`distributor_prices`/`distributor_price_id` | **blockiert** (keine Anbindung) | inaktiv |

**Befund:** Drei read-only Panels (Reife, Auslegung, Matching) existieren, sind aber **verstreut/uneingebettet**. Der Preisstrang ist blockiert. Es fehlt eine **gemeinsame Bühne** je Objekt/Gewerk.

## 2. Wo gehört welches Panel hin? (Q1–Q4)

**Q2 — Objektprofil (`customer_object_profile.blade`, je WP-Gewerkzeile):** die **Diagnose-Heimat**.
- **Angebotsreife-Panel** (bereits lazy eingebettet) — „Wie weit ist der Vorgang?"
- **Auslegungs-Vorschau (P3-c)** — „Was schlägt die Auslegung als Positionen vor?" (read-only, nicht bepreist)
- **WP-Katalog-Matching (P3-d0a)** — „Warum noch kein Preis?" (Diagnose)
- → als **Tabs/Akkordeon** in EINEM „Angebots-/Auslegungs"-Block der Gewerkzeile, nicht drei lose Kacheln.

**Q4 — Kanban:** nur **Kurzsignal** — der Reife-Badge + Filter (bestehend). Kein Detail, keine Positionen. Das Board bleibt Übersicht.

**Q3 — Angebots-Wizard:** die **Entscheidungs-/Schreib-Heimat** — hier werden Positionen aus dem Vorschlag übernommen, bestätigt, bepreist (`CatalogPriceGuard`) und gespeichert (`offer_details.sections`). Das Reife-Gate sitzt davor.

**Reife-Gate:** unsichtbar-serverseitig; im UI nur als **verständliche Sperrmeldung** (422 „offene Pflichtpunkte …") + im Reife-Panel als offene Aufgaben.

## 3. Nutzerreise Lead → Angebot (Q5)
```
1. Lead/Objekt        → Kunde+Objekt erfasst (Bereich 1)
2. Bedarf             → WP-Formular ausfüllen (Checklist)
3. Auslegung          → Anforderungsprofil (Heizlast/Bivalenz) — Wahrheit
4. Reife-Check        → Angebotsreife-Panel: Ampel, offene Aufgaben, Blocker
   └─ Gate: blockierende Pflichtpunkte offen? → Angebot gesperrt (422)
5. Auslegungs-Vorschau (P3-c) → vorgeschlagene Positionen (unbepreist, markiert)
6. Preisfähigkeit     → Katalog-Matching (P3-d0a): bepreisbar? (heute: nein → OMD/IDS offen)
7. Angebot (Wizard)   → Positionen übernehmen/bestätigen → Preis → offer_details.sections
```
Sichtbar für den Nutzer als **Fortschrittsleiste** mit diesen Etappen; jede Etappe zeigt Status/Aufgaben.

## 4. Status, Blocker, Aufgaben, Fortschritt (Q6)
- **Eine Sprache, konsistent** (aus Paket 1 / P3-c / P3-d0a wiederverwenden):
  - **Ampel** grün/gelb/rot je Kennzahl/Position.
  - **Datenlage**-Badge: gemessen/berechnet/geschätzt/**fehlt**.
  - **„automatisch berechnet" vs. „zu bestätigen"** je Position.
  - **Preisstatus**: bepreisbar / „nicht bepreist · Katalog-Anker fehlt".
- **Blocker** = harte Reife-Kriterien offen → rot, Angebot gesperrt.
- **Aufgaben** = offene Pflichtpunkte als **konkrete To-dos** (nicht Rohwerte), z. B. „Zuständigkeit zuordnen", „Heizlast rechnen".
- **Fortschritt** = Reife-Prozent (aus echten Kriterien, on-the-fly) + Etappen-Leiste. **Kein** persistierter Status.

## 5. Trennung technische Auslegung ↔ kaufmännische Preisfähigkeit (Q7)
**Zwei klar getrennte Zonen** — das ist die zentrale UX-Aussage:
- **Technisch (grün machbar ohne Preis):** Heizlast/Bivalenz/Gerätebedarf, Auslegungs-Vorschau, Vorschlagspositionen. Kann vollständig „fertig" sein, **auch wenn kein Preis existiert.**
- **Kaufmännisch (Preisfähigkeit):** Katalog-Anker + OMD/IDS-Preis. Heute **blockiert** → jede Position trägt „nicht bepreist". Das darf die technische Reife **nicht** rot färben — sondern ist ein **eigener** Statusstrang.
- **UI:** zwei Spalten/Abschnitte „Technische Auslegung" ✓ und „Preisfähigkeit" ⚠ (blockiert bis OMD/IDS). So sieht der Nutzer sofort: *technisch fertig, kaufmännisch wartet auf Preisanbindung.*

## 6. Idealer 5–7-Schritt-Wizard (Q8)
Objekt-/gewerkverankert (nicht das Standalone-Energie-Tool). Jeder Schritt zuerst als **read-only Preview** prüfbar, Schreibpfad zuletzt:
1. **Start** — WP-Vorgang wählen; Reife-Ampel + „Was fehlt?".
2. **Bedarf** — WP-Formular (server-autoritative Sichtbarkeit/Pflicht, Operanden-Gate).
3. **Auslegung** — Heizlast/Bivalenz/Gerät aus dem Profil; Datenlage/Ampel.
4. **Vorschlag** — abgeleitete Positionen (P3-c), klar markiert, editierbar; **noch kein Preis.**
5. **Preisfähigkeit** — Katalog-Matching (P3-d0a): bepreisbar? Sonst Aufgabe „OMD/IDS-Anbindung / Zuordnung".
6. **Übernahme & Angebot** — Positionen in `State.sections`, bestätigen; Reife-Gate; `CatalogPriceGuard` bepreist; Speichern.
7. **(optional) Abschluss/Dokument** — Angebot prüfen/erzeugen.
*(Schritte 5/6 sind heute durch den Preisstrang blockiert — der Wizard zeigt das als klaren, nicht-fehlerhaften Wartezustand.)*

## 7. Zusammenführen / Entfernen (Q9)
- **Zusammenführen:** die drei read-only Panels (Reife · Auslegung · Matching) in **einen** „Angebots-/Auslegungs"-Block je Gewerkzeile (Tabs) — statt drei separater Seiten/Routen im Alltag.
- **Konsolidieren:** Badge-/Ampel-/Datenlage-Sprache **einheitlich** (heute je Panel leicht unterschiedlich inline gestylt) → gemeinsame kleine Badge-Konvention (kein 253-Farben-Wildwuchs, siehe UX-Audit).
- **Abgrenzen (nicht entfernen):** das Standalone-`EnergieAuslegungController`-Tool bleibt als **Referenz/Rechner**, ist aber **nicht** der Angebots-Pfad — im UI klar als „Rechner" separat, damit es nicht mit dem verankerten Workflow verwechselt wird.
- **Entfernen/vermeiden:** keine doppelten Positions-/Preis-Darstellungen; keine zweite Statuswahrheit; kein Alpine außerhalb erlaubter Scopes.

## 8. Browser-Testpfade je Schritt (Q10)
| Schritt | Pfad | Erwartung |
|---|---|---|
| Reife | Objektprofil-Block / `offers.angebotsreife.panel` | Ampel + Aufgaben; on-the-fly; keine PII-Leaks |
| Kanban-Signal | Board | WP-Badge farbcodiert, Filter; Nicht-WP unter „alle" |
| Bedarf | Checklist | Sichtbarkeit/Pflicht server-autoritativ; Operanden-Gate |
| Auslegung/Vorschau | `offers.auslegung.vorschau` | Positionen, Datenlage/Ampel, „nicht bepreist", kein Write |
| Preisfähigkeit | `offers.wp-katalog-matching` | Schnittmenge, roter Banner heute, Set-Herkunft, kein Anker |
| Gate | Create versuchen mit offenem Blocker | 422 „offene Pflichtpunkte", kein Angebot |
| Angebot | Wizard `offers.wizard` | Positionen bepreist (P1-a), Totals server; Reife-Gate |
Jeder read-only Schritt: nur GET, kein Schreib-Request, kein 500, keine PII, ohne Login Redirect.

## 9. Prioritäten (Vorschlag)
1. **UX-2 (nächster Bau-Slice, read-only zuerst):** die drei Panels im Objektprofil zu **einem Tab-Block** zusammenführen (Reife · Auslegung · Preisfähigkeit) — reine Anzeige, kein neuer Schreibpfad.
2. **Badge-/Ampel-Konvention** vereinheitlichen (kleiner Frontend-Konsolidierungs-Posten).
3. **Wizard-Etappenleiste** (Schritte 1–4 read-only sichtbar), Schreibpfad (5/6) erst wenn Preisstrang frei.
4. **Preisstrang** bleibt an OMD/IDS blockiert (separater Strang, P3-d2b).

## 10. Fachagenten-Perspektiven (Kurzfazit)
- **Konzeption:** technische Reife und kaufmännische Preisfähigkeit sind **getrennte** Fertigstellungen; das Angebot bleibt menschlich verantwortet.
- **Workflow:** eine durchgehende Etappenleiste Lead→Angebot; Gate vorgeschaltet; kein Sprung in die Preisstufe ohne technische Reife.
- **Architektur:** Panels lesen führende Wahrheiten, schreiben nichts; Einspeisung nur über den bestehenden Wizard-State + Save-Pfad; `component_id`-Anker/`CatalogPriceGuard` unverändert.
- **Frontend-Design:** ein Tab-Block je Gewerk statt verstreuter Seiten; einheitliche Badge-Sprache; kein „Datenhaufen" (Progressive Disclosure); Vuexy/jQuery, kein Alpine.

## Nicht-Ziele
Kein Bau · keine UI-Änderung · kein Commit/Push · keine neue Wahrheit · keine Preislogik-Änderung · kein Auto-Anker · reines Konzept. Nur dieses Dokument neu (nicht committet).
