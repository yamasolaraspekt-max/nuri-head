# AP-3 — Plattform-Klammer-Weiche (Gemeinsamer Konfigurationsarbeitsraum)

**Stand:** 2026-07-14 · **read-only** · **kein Bau, kein Commit, kein Push, keine Migration, keine Dateiänderung außer diesem Dokument, keine Architekturentscheidung heimlich umgesetzt.**
**Kapitel:** 2 (Kern-Unlock). **Grundlage:** `docs/architektur-entscheidungen.md` (Weiche 5 ratifiziert), `docs/hierarchie-objekt-projekt-bestandsaufnahme.md`, `docs/gesamtfahrplan-gebaeude-energie-angebot.md`, `docs/ap2-pv-inventur.md`, `docs/ap3-plattform-klammer-startblock.md`, CLAUDE.md (DAUERDIREKTIVE „eine Wahrheit", additiv, Weiche 5 „das Objekt klammert").
**Ziel:** Entscheiden, wie der gemeinsame Konfigurationsarbeitsraum technisch verankert wird, damit WP, PV, Speicher, Wallbox, Dach/Geometrie, Fenster/Fassade und Gesamtenergie **nicht** als einzelne Silos entstehen.

---

## 0. Kernbefund vorab (belegt)

> **Die Klammer muss nicht erfunden werden — sie existiert bereits in Bausteinen:**
> - **Objekt = `lead_alternative_adds`** (Weiche 5 ratifiziert: *das Objekt klammert, der Auftrag führt aus*), FK-fest, gefüllt.
> - **Aktivierte Module + Modulstatus = `lead_product_lists`** (je `product_id`/Gewerk je Objekt; `status`/`work_status`/`stage`/`stage_history`) — der „welche Gewerke laufen am Objekt"-Träger existiert.
> - **Phasen-Wahrheit = `lead_stages`/`LeadStageSubStage`** (Weiche 1).
> - **Versionierter Auslegungs-/Daten-Snapshot-Träger = `Anforderungsprofil`** (polymorph verankerbar an **Objekt** *oder* **Gewerk**; `ERLAUBTE_ANKER=[LeadAlternativeAdd, LeadProductList]`, `customer` bewusst NICHT; append-only `entwurf|aktiv|abgeloest`; `gebaeude_geometrie`-JSON-Hook).
> - **Positions-Wahrheit = `offer_details.sections`** + `CatalogPriceGuard`.
>
> → **Es gibt eine Klammer-Lösung OHNE Migration** (Frage 14 = ja). Der Weg ist **Verknüpfung + Aggregation**, kein Neubau.

---

## 1. Antworten auf die 15 Pflichtfragen

**1. Was ist die führende Klammer?**
**Das Objekt (`lead_alternative_adds`)** — ratifiziert (Weiche 5). Nicht Anforderungsprofil (das ist Träger, nicht Klammer), nicht LeadProductList (das ist *ein* Modul, nicht die Klammer über mehreren), nicht Angebot/OfferFolder (nachgelagert), **keine** neue Konfigurationsprojekt-Tabelle (Weiche 5 verbietet einen Projekt-Container über den Gewerken; würde zweite Wahrheit erzeugen).

**2. Wo hängt der dynamische Kundenwunsch?**
Zweistufig: **(a)** „welche Gewerke gewünscht/aktiviert" → `lead_product_lists` (je Gewerk eine Zeile am Objekt) + Wunsch-Felder; **(b)** „fachlicher Bedarf/Ziel" (Zieltemperatur, Autarkiewunsch, …) → `Anforderungsprofil`-Werte (`anforderungsprofil_werte`, EAV mit `datenlage`/`quelle`). Bereits so für WP; für PV/Speicher/Wallbox additiv über die Schlüssel-Registry.

**3. Wo hängen aktivierte Module?**
`lead_product_lists` je `(customer_id, alternative_id, product_id)` — ein „aktiviertes Modul" = eine Gewerkzeile am Objekt. Existiert.

**4. Wo hängt Modulstatus?**
`lead_product_lists.status`/`work_status`/`stage` + **`lead_stages`/`LeadStageSubStage`** als Phasen-Wahrheit (Weiche 1). Reife on-the-fly zusätzlich über `OfferReadinessService` (nicht persistiert).

**5. Wo hängen Datenprüfung und Daten-Snapshot?**
`Anforderungsprofil`-**Versionierung** = der Snapshot (append-only, genau eine `aktiv`, `abgeloest_durch_id`). Datenprüfung = `datenlage`/`quelle`/`erfassungsweg` je Wert + **Belastbarkeit (AP-1, `HeizlastBelastbarkeit`)** + `ergebnis_hinweis`. Keine neue Snapshot-Tabelle nötig.

**6. Wo hängen Formularwerte?**
Bedarfsformular-Rohwerte: `ProductFormula` + `LeadProductChecklistValue` (an Gewerk/Lead). Die **auslegungsrelevanten** Werte werden über einen Adapter in `Anforderungsprofil`-Werte überführt (führende Auslegungswahrheit) — so wie der `AnforderungsprofilHeizlastAdapter` es für WP tut. Keine Doppelhaltung: Rohformular ≠ Auslegungswahrheit, klar getrennt.

**7. Wo hängen WP-Auslegung, PV-Auslegung, Speicher, Wallbox?**
Alle in **`Anforderungsprofil`-Werten**, verankert am Gewerk (`LeadProductList`) bzw. gemeinsamen Objekt (`LeadAlternativeAdd`), über **je-Gewerk-Schlüssel in der `SchluesselRegistry` + je-Gewerk-Adapter**. WP existiert (`AnforderungsprofilHeizlastAdapter`); PV/Speicher/Wallbox = **additive Registry-Schlüssel + Adapter (Code, keine neue Tabelle)**. So wird jede Auslegung ein Fachmodul am selben Kern statt eigenes Silo.

**8. Wo hängt Geometrie/3D/Dach?**
Kurzfristig am vorhandenen **`anforderungsprofile.gebaeude_geometrie`-JSON-Hook** (heute Heizlast-Raumgeometrie). Mittelfristig eigenes **gemeinsames Geometriemodell (Kapitel 4)**, das WP-Hülle und PV-Dach speist — **erst** dieses, dann 3D-PV (AP-2-Risiko: sonst drittes Geometrie-Silo). AP-3 legt nur die Naht fest, baut keine Geometrie.

**9. Wie bleibt `offer_details.sections` die einzige Angebotspositions-Wahrheit?**
Die Klammer erzeugt **keine** Positionen. Auslegung liefert nur **read-only Vorschlags-`sections`** (`AuslegungVorschlagService`, `preis_status='katalog_anker_fehlt'`, `component_id=null`). Der **einzige** Schreibpfad in `offer_details` bleibt die bewusste Angebotsübergabe (Kapitel 12, blockiert bis Preisanker OMD/IDS). Kein Klammer-Baustein schreibt Positionen.

**10. Wie wird verhindert, dass PV ein neues Silo wird?**
PV-Auslegung/-Ertrag kommt an **denselben** `Anforderungsprofil`-Kern (PV-Schlüssel + PV-Adapter) statt in eigene PV-Auslegungstabellen; PV-Ertrag = `PvgisErtragService` (eine Wahrheit, Legacy-Doppelspur stilllegen). Die bestehenden objektgebundenen PV-Tabellen (`solar_systems`, `p_v_roofs`, `profitability_*`) werden **nicht** zur zweiten Auslegungswahrheit — sie bleiben Detail-/Katalog-/Layout-Speicher, die Auslegungswahrheit liegt im Profil.

**11. Wie wird verhindert, dass WP weiter Sonderpfad bleibt?**
Der WP-Adapter (`AnforderungsprofilHeizlastAdapter`) wird als **Muster** verallgemeinert: PV/Speicher/Wallbox bekommen denselben Adapter-/Registry-Mechanismus. Das 4a-Cockpit (`WpAngebotsWorkflowService`) wird von „je WP-Gewerkzeile" auf „je Objekt, aufgeklappt nach Gewerken" generalisiert → WP wird der **Normalfall**, nicht der Sonderweg.

**12. Welche bestehenden ticket-Tabellen/Services können wiederverwendet werden?**
`lead_alternative_adds` (Objekt), `lead_product_lists` (Module+Status), `lead_stages`/`LeadStageSubStage` (Phasen), `anforderungsprofile`/`anforderungsprofil_werte` + `SchluesselRegistry` + `AnforderungsprofilService` + `AnforderungsprofilHeizlastAdapter` (Muster), `gebaeude_geometrie`-Hook, `offer_details`/`CatalogPriceGuard` (Positionen/Preis), `OfferReadinessService`/`OfferReadinessGate`, `HeizlastBelastbarkeit` (AP-1), `AuslegungVorschlagService`, `WpKatalogMatchingService`, `WpAngebotsWorkflowService`+4a-Cockpit, UX-2 Objektprofil-Tabblock.

**13. Welche neuen Tabellen wären wirklich nötig?**
Für die **Klammer selbst: KEINE.** PV/Speicher/Wallbox = Registry-Schlüssel + Adapter (Code). Optional *später* (kein Muss, eigener Posten): ein schlankes read-model/Sicht (kein Schema). Geometrie-Konsolidierung = Kapitel 4 (separat). Eine echte `configuration_projects`-Tabelle wäre nur bei Option B nötig — und ist **nicht empfohlen**.

**14. Gibt es eine Option ohne Migration?**
**Ja** — Option **E** (und ihr Kern A+C unter dem Objekt) nutzt ausschließlich Bestand. Der erste Bau-Slice (read-only Konfigurationsprojekt-Sicht) braucht **null Migration**.

**15. Wenn Migration nötig ist: STOPP und begründen.**
Für den empfohlenen Weg (E) + ersten Slice ist **keine Migration nötig** → **kein STOPP**. Sobald ein späterer Slice eine echte neue Tabelle verlangt (z. B. Option B `configuration_projects`, oder ein normalisiertes Geometriemodell in Kapitel 4), gilt: **STOPP + eigener begründeter Migrations-Posten mit Yama-Freigabe**, kein stiller Migrationsbau.

---

## 2. Optionen A–E — Bewertung

Legende: 🟢 gut · 🟡 bedingt · 🔴 problematisch.

### Kurzcharakter
- **A** — Klammer über das bestehende versionierte **Anforderungsprofil** erweitern (Profil wird die Klammer).
- **B** — Neue additive Tabelle **`configuration_projects`/`konfigurationsprojekte`**.
- **C** — **LeadProductList** als alleinige Klammer.
- **D** — **OfferFolder/Angebot** als Klammer.
- **E** — **Hybrid:** Objekt klammert + Anforderungsprofil trägt Auslegung/Snapshot je Gewerk + `lead_product_lists` = aktivierte Module/Status + **optionale** read-model-Sicht (keine neue Tabelle).

### Bewertungsmatrix

| Dimension | A (Profil-Klammer) | B (neue Tabelle) | C (LeadProductList) | D (OfferFolder) | **E (Hybrid, empfohlen)** |
|---|---|---|---|---|---|
| **fachliche Wahrheit** | 🟡 Profil ist Träger, nicht Klammer | 🔴 zweiter Projekt-Begriff (contra Weiche 5) | 🔴 ein Modul ≠ Klammer über mehreren | 🔴 Angebot ist nachgelagert, nicht Auslegungs-Klammer | 🟢 Objekt klammert (Weiche 5), Profil trägt |
| **techn. Tragfähigkeit** | 🟡 überlädt Profil-Semantik | 🟡 tragfähig, aber Overhead | 🔴 kann Mehrfach-Gewerke nicht klammern | 🔴 koppelt Config an Angebots-Lebenszyklus | 🟢 nutzt vorhandene FK-Ketten |
| **WP-Kompatibilität** | 🟢 | 🟡 (Umbau) | 🟡 | 🔴 | 🟢 (WP schon so) |
| **PV-Kompatibilität** | 🟢 (Schlüssel/Adapter) | 🟡 | 🟡 | 🔴 | 🟢 (Adapter-Muster) |
| **Speicher/Wallbox** | 🟢 | 🟡 | 🟡 | 🔴 | 🟢 |
| **3D/Geometrie** | 🟡 (JSON-Hook da) | 🟡 | 🔴 (kein Objekt-Bezug für Hülle) | 🔴 | 🟢 (Hook + Kapitel 4) |
| **UI/UX-Arbeitsraum** | 🟡 Profil-zentriert | 🟡 eigene Projekt-Seite | 🔴 kein Objekt-Überblick | 🔴 Angebots-zentriert | 🟢 Objekt-Sicht, Gewerk-Kacheln |
| **Rechte/Rollen** | 🟢 (Profil hat created_by) | 🟡 neue Entität = neue Rechte | 🟢 | 🟡 | 🟢 (Objekt/Gewerk-Rechte da) |
| **Historie/Snapshot** | 🟢 (Versionierung) | 🟡 (neu bauen) | 🔴 (kein Snapshot) | 🟡 | 🟢 (Profil-Versionierung) |
| **Risiko** | 🟡 Semantik-Überladung | 🔴 zweite Wahrheit / Weiche-5-Bruch | 🔴 klammert nicht | 🔴 falsche Richtung | 🟢 additiv, klein |
| **Migrationsbedarf** | 🟢 keiner | 🔴 neue Tabelle(n) → STOPP | 🟢 keiner | 🟡 | 🟢 **keiner** |
| **Rückfallpfad** | 🟢 | 🔴 (Tabelle + Daten) | 🟢 | 🟡 | 🟢 (nur read-model/Code) |
| **kleinster Bau-Slice** | mittel | groß | klein, aber unzureichend | mittel | **klein & tragfähig** |

### Begründung der Ausschlüsse
- **B** widerspricht **Weiche 5** direkt (kein Projekt-Container über den Gewerken) und erzeugt einen zweiten „Projekt"-Begriff neben dem Objekt → **zweite Wahrheit**, Migration, STOPP-pflichtig. Nur wählen, wenn E fachlich nachweislich nicht trägt.
- **C** kann die **Vielfachheit** (mehrere Gewerke/Systeme je Objekt) nicht klammern — eine `lead_product_list` **ist** ein Modul, nicht die Klammer über mehreren.
- **D** koppelt die Konfiguration an den **Angebots**-Lebenszyklus (nachgelagert) — Auslegung entsteht **vor** dem Angebot; falsche Richtung, bricht „offer_details = einzige Positions-Wahrheit" nicht, aber macht die Klammer angebotsabhängig.
- **A** ist fast richtig, überlädt aber das Profil: das Profil ist der **Auslegungs-/Snapshot-Träger je Gewerk**, nicht die Klammer über den Gewerken. Die Klammer ist das **Objekt**. A ist damit ein Teil von E, nicht die ganze Antwort.

---

## 3. Empfehlung: **Option E (Hybrid)**

**Das Objekt (`lead_alternative_adds`) klammert. `lead_product_lists` trägt die aktivierten Module + Status. Das `Anforderungsprofil` trägt je Gewerk die versionierte Auslegung + Snapshot + Datenprüfung (inkl. AP-1-Belastbarkeit) + Geometrie-Hook. `offer_details.sections` bleibt die einzige Positions-Wahrheit. Eine optionale read-only „Konfigurationsprojekt-Sicht je Objekt" aggregiert das alles — ohne neue Tabelle.**

Warum E:
- **Konform mit Weiche 5** (Objekt klammert) und der DAUERDIREKTIVE (eine Wahrheit, additiv).
- **Keine Migration**, kein zweiter Projekt-Begriff, voll rückbaubar.
- **WP, PV, Speicher, Wallbox** hängen als **gleichwertige** Fachmodule am selben Kern (Registry + Adapter) → kein WP-Sonderpfad, kein PV-Silo.
- **Geometrie** über den vorhandenen Hook, sauber übergebbar an das gemeinsame Geometriemodell (Kapitel 4), ohne 3D vorzuziehen.

---

## 4. Kleinster erster Bau-Slice (AP-3-Slice-1) — Vorschlag, noch KEIN Bau

**„Konfigurationsprojekt-Sicht je Objekt" — read-only read-model.**
- **Inhalt:** Ein Service (analog `WpAngebotsWorkflowService`), der **je Objekt** aggregiert: alle `lead_product_lists` (aktivierte Gewerke) → je Gewerk Reife (`OfferReadinessService`) + Belastbarkeit (AP-1) + Auslegungsstatus (`Anforderungsprofil` aktiv? Werte-Datenlage) + „was fehlt". Liefert ein DTO „Objekt X: WP 60 %/eingeschränkt, PV nicht aktiviert, Speicher offen …".
- **UI (optional, klein):** eine Objekt-Sicht mit Gewerk-Kacheln (Ticket-CI), die das 4a-Cockpit von „je Gewerkzeile" auf „je Objekt" hebt. Kein Alpine.
- **Scope-Grenzen:** **kein** Schreibpfad, **keine** neue Tabelle, **keine** Migration, **kein** `offer_details`-Write, **keine** PV-/Preis-/Kataloglogik, **keine** Geometrie/3D. Reine Aggregation vorhandener read-only Bausteine.
- **Tests:** je Objekt korrekte Gewerk-/Reife-/Belastbarkeits-Aggregation; kein Write (Row-Counts vorher==nachher); Non-Objekt/leeres Objekt sauber.
- **Rückfallpfad:** reiner Datei-Revert (neuer Service + optional Blade), kein Schema/Daten berührt → path-scoped.
- **Warum als erstes:** schließt konzeptionell die Klammer sichtbar, bereitet PV/Speicher/Wallbox-Anbindung vor, ohne irgendetwas zu bauen, das eine spätere Weiche umwerfen könnte.

**Danach (je eigener Startblock + Yama-Freigabe):** PV-Schlüssel-Registry + PV-Adapter (AP-2 PV-S3), dann Rechner→Profil-Verdrahtung (L-1), `BivalenzService` (L-2), Varianten (Kapitel 7), Geometrie-Konzept (Kapitel 4), Speicher/Wallbox, Gesamtenergie.

---

## 5. STOPP

Read-only Weiche abgeschlossen. Nur `docs/ap3-plattform-klammer-weiche.md` erstellt; keine Code-/Schema-/Datenänderung, kein Bau, kein Commit, kein Push, keine Architekturentscheidung umgesetzt. **Klare Empfehlung: Option E**, kleinster erster Bau-Slice = read-only „Konfigurationsprojekt-Sicht je Objekt", **ohne Migration**. Nächster Schritt laut Auftrag: **STOPP** — Yama entscheidet A/B/C/D/E und gibt (oder nicht) den ersten Bau-Slice frei.

*Ende AP-3.*
