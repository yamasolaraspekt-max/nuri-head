# Startblock — AP-3: Plattform-Klammer (Konfigurationsprojekt-Weiche, read-only Konzept)

**Stand:** 2026-07-14 · **read-only Vorbereitung — KEIN Bau, kein Commit, kein Push, keine Migration, kein Seeder, keine Datenänderung.**
**Kapitel:** 2 (Gemeinsamer Konfigurationsarbeitsraum) — der **Kern-Unlock**. **Grundlage:** Gesamtfahrplan §8 (AP-3), Gap-Analyse §4/§5/§6, `docs/architektur-entscheidungen.md` (Weiche 5), `docs/hierarchie-objekt-projekt-bestandsaufnahme.md`.

## Ziel
Die **Weiche** entscheiden und ein read-only **Konzept** vorlegen: Wie klammert ticket mehrere Gewerke/Energiesysteme je Objekt in einem gemeinsamen Konfigurationsarbeitsraum — **ohne** zweite Objekt-/Projekt-Wahrheit, **ohne** Migration in diesem Schritt.

## Warum jetzt
Kern-Unlock: Kapitel 6/7/8/9/12 hängen daran. Ohne diese Klammer bleibt jedes Fachmodul (WP, PV, Speicher) eine Insel; die Rechner-Welt (`/admin/energie/*`) bleibt vom Objekt getrennt (Lücken L-1/L-2/L-5).

## Ergebnisdokument
`docs/bereich2-konfigurationsprojekt-klammer-konzept.md` (neu, read-only Konzept + Weiche).

## Die zu entscheidende Weiche
**Reicht die bestehende Objekt-Klammer (`lead_alternative_adds` + n×`lead_product_lists` + `Anforderungsprofil`) als „Konfigurationsprojekt", oder braucht es eine schlanke read-model-Sicht darüber?**
- **Option A (empfohlen, additiv):** keine neue Tabelle. Ein read-model „Konfigurationsprojekt-Sicht je Objekt" aggregiert on-the-fly: Objekt → alle Gewerk-Zeilen → je Gewerk Reife (`OfferReadinessService`) + Auslegung (`Anforderungsprofil`) + Belastbarkeit (AP-1) + fehlende Operanden. Vorbild (Konzept, nicht Code): playground `PlanungskontextController` (read-only Lesefassade „was fehlt je Gewerk").
- **Option B:** eigene „Konfigurationsprojekt"-Entität über den Gewerken. **Kollidiert mit Weiche 5** („das Objekt klammert, kein Projekt-Container darüber") → nur wählen, wenn A fachlich nicht trägt. **Migration = eigener, später zu beauftragender Posten** (nicht in AP-3).

## Pflichtprüfung im Konzept (read-only)
1. Bestätigen: Objekt = `lead_alternative_adds`, FK-fest, gefüllt (Hierarchie-Bestandsaufnahme).
2. Wie werden mehrere Gewerke je Objekt heute geführt (`lead_product_lists` je `product_id`)? Wird die Vielfachheit real gelebt (heute 1:1:1)?
3. Welche read-only Bausteine existieren schon und werden nur aggregiert? (`OfferReadinessService`, `AuslegungVorschlagService`, `WpAngebotsWorkflowService`/4a-Cockpit, `WpKatalogMatchingService`, UX-2-Tabblock.)
4. Wie wird die 4a-Cockpit-Sicht von „je Gewerkzeile" auf „je Objekt, aufgeklappt nach Gewerken" generalisiert — ohne Bruch, additiv?
5. Wie speist die Rechner-Welt (`/admin/energie/*`) später in die Klammer (nur Konzept-Naht, kein Bau)?
6. Status/Version/Verlauf: `lead_stages` (Weiche 1) + `Anforderungsprofil`-Versionierung — reicht das, oder Lücke?

## Nicht-Ziele
Keine neue Tabelle/Migration, kein playground-`anlagen_*`-Import (nur Blaupause), keine zweite Objekt-/Projekt-Wahrheit, kein Schreibpfad, keine UI-Vollausführung (nur Wireframe-Konzept), keine Rechner→Profil-Verdrahtung (eigener späterer Slice).

## Vorgehensweise
1. Ist-Beleg (Objekt-Klammer, Gewerk-Zeilen, vorhandene read-only Aggregatoren).
2. Option A vs. B bewerten gegen Weiche 5 + DAUERDIREKTIVE („eine Wahrheit").
3. read-model-Skizze (welche Felder je Objekt/Gewerk, woher, read-only).
4. Arbeitsraum-Wireframe-Konzept (Ticket-CI, Kap. 14; Fachagenten-Pflicht: Konzeption/Workflow/Architektur/Frontend).
5. Empfehlung + erste kleine, additive Bau-Slices (getrennt zu beauftragen).

## Risiken / Stop
- **Fachliche Weiche** — bei Unsicherheit A vs. B: **STOPP + Yama** (Geschäftsregel, hängt an Arbeitsweise Kunde↔Objekt↔Gewerk).
- Kein Bau vor Yama-Entscheidung der Weiche.

## Yama-Abnahme
**Erforderlich** — die Weiche ist eine Architektur-/Geschäftsregel-Entscheidung. Evaluator strikt read-only.

*Ende Startblock AP-3.*
