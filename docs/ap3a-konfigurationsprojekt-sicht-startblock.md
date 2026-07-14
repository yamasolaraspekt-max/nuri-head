# Startblock — AP-3a: Read-only Konfigurationsprojekt-Sicht je Objekt

**Stand:** 2026-07-14 · **read-only Vorbereitung — KEIN Bau, kein Commit, kein Push, keine Migration, keine neue Tabelle, kein Schreibpfad, kein `offer_details`-Write, keine Preis-/Katalog-/3D-/PV-/Formular-/Historien-Änderung, keine Änderung an bestehenden WP-Gates.**
**Kapitel:** 2 (Konfigurationsarbeitsraum). **Grundlage:** `docs/ap3-plattform-klammer-weiche.md` (Option E bestätigt), `docs/gesamtfahrplan-gebaeude-energie-angebot.md`, `docs/ap2-pv-inventur.md`, AP-1 (`HeizlastBelastbarkeit`).

## 1. Ziel
Eine **objektbezogene, read-only Übersicht**, die alle aktivierten Gewerke/Module eines Objekts zusammenführt: Kundenwunsch/aktivierte Module, Status je Modul, Angebotsreife je Modul, Datenlage/Belastbarkeit, vorhandene Auslegungen, fehlende Daten, nächste Aktion, Technik vs. Preisfähigkeit, Modul-Abhängigkeiten. Hebt das WP-4a-Cockpit von „je Gewerkzeile" auf **„je Objekt"** — ohne neue Wahrheit.

## 2. Ausgangsbefund (verifiziert, read-only)
- **Objekt→Gewerke:** `LeadAlternativeAdd::leadProductLists()` (hasMany `alternative_id`) — jede `lead_product_list` = ein aktiviertes Modul am Objekt (`product_id` = Gewerk).
- **Modulstatus:** `lead_product_lists.status`/`work_status`/`stage` + `lead_stages`/`LeadStageSubStage` (Weiche 1).
- **Reife/Belastbarkeit/Auslegung:** vorhandene read-only Services `OfferReadinessService` (WP-Katalog + `generischerKatalog` für Nicht-WP), `HeizlastBelastbarkeit` (AP-1, im Reife-DTO als `heizlast_belastbarkeit`), `AuslegungVorschlagService` (WP-Vorschau, mit `verbindlich`/`belastbarkeit`).
- **Vorbild:** `WpAngebotsWorkflowService` (4a) — dieselbe Aggregations-/Technik-Preis-Trennung, aber je `lead_product_list`.
- **Gewerk-Kennung:** WP = `article_groups.id=2`. Nicht-WP (PV/Speicher/Wallbox…) = alles ≠ 2. WP↔PV-Kopplung nur als weiches Formularfeld `mit_pv_koppeln` (keine harte Abhängigkeitstabelle).
- **PV-Zustand (AP-2):** kein PV-Auslegungsservice am Kern; `PvProjektService` **gebrochen** (fehlende `StringBuilderService`) → **darf NICHT aufgerufen werden**. PV/Speicher/Wallbox erscheinen als „noch nicht verdrahtet".

## 3. Prüfpunkte (beantwortet)

**1. Bester Einstiegspunkt?**
**Eigene read-only Route + Controller + View** (`GET /objekt/{alternative}/konfiguration`, z. B. `objekt.konfiguration`), **rein additiv (nur neue Dateien)**. Ein Link aus dem bestehenden Objektprofil (`new.lead.profile.object`) ist **optionaler Folge-Schritt** (berührt eine Bestands-Blade → dann Archiv). Slice-1 bevorzugt: eigene Seite, **kein** Eingriff ins große Objektprofil-Blade.

**2. Welcher Service aggregiert?**
Ein **neuer** `KonfigurationsprojektService` (analog `WpAngebotsWorkflowService`, aber je Objekt):
- lädt `LeadAlternativeAdd` + `leadProductLists` (minimal) — **liest** `lead_alternative_adds`, `lead_product_lists`, `lead_stages`.
- je Modul: `OfferReadinessService::fuer($lpl)` (Reife + `heizlast_belastbarkeit`).
- **nur WP (product_id=2):** zusätzlich `AuslegungVorschlagService::fuer($lpl)` (Vorschau, read-only) — liefert `verbindlich`/`belastbarkeit`/Auslegung-vorhanden.
- **PV/Speicher/Wallbox/Nicht-WP:** **read-only Platzhalter** (`verdrahtet=false`), **kein** Service-Aufruf (kein `PvProjektService`).
- baut Objekt-Ebene: aktivierte Gewerke, je Modul Status/Reife/Belastbarkeit/Technik-vs-Preis/nächste Aktion + Modul-Abhängigkeits-Hinweise.
**Kein Schreibpfad, kein neuer Katalog, keine neue Tabelle.**

**3. Welche Daten dürfen im DTO erscheinen?**
Objekt: `alternative_id`, `vorgang_label='Objekt #id'`, Gewerks-Liste. Je Modul: `lead_product_list_id`, Gewerk-Label (`article_group`), Status/Phase, Reife-`percent`, `technik_prozent`/`preis_moeglich`, `heizlast_belastbarkeit.stufe/label/verbindlich`, `auslegung_vorhanden`, `verdrahtet` (bool), `naechste_aktion`, `blocker_offen`. **Keine** Preis-/Katalog-Werte, **keine** Kundendaten.

**4. Wie PII-arm?**
Wie 4a: DTO trägt **keine** Kundendaten (Name/Adresse/Telefon/E-Mail) — nur IDs + Gewerk-Labels + Status/Prozent. Objekt-Label = `Objekt #{alternative_id}` (nicht `object_name`/Adresse). Test: `json_encode(DTO)` enthält keinen gesetzten PII-String.

**5. Wie Non-WP dargestellt?**
Non-WP-Modul: Reife über `generischerKatalog` (blocker-Kern), Auslegung als **„Fachmodul noch nicht verdrahtet"** (`verdrahtet=false`), Belastbarkeit `n/a`. Kein Fehler, kein 500.

**6. Wie PV/Speicher/Wallbox als „noch-nicht-verdrahtet"?**
Explizites Feld `verdrahtet=false` + Label „Auslegung folgt (AP-2/PV-Slices)". Graue Kachel, keine Auslegungs-/Ranking-Zahlen. Klar getrennt von „aktiviert, aber Daten fehlen".

**7. Wie sichtbar, dass WP weiter ist als PV?**
Objekt-Sicht sortiert/zeigt je Modul den Reifegrad; WP zeigt echte Reife + Belastbarkeit + Auslegung-vorhanden, PV zeigt `verdrahtet=false`. Optional eine Objekt-Gesamtampel, die den heterogenen Stand ehrlich abbildet (kein Durchschnitt, der PV-Nichtverdrahtung verschleiert).

**8. Wie Preisfähigkeit-Blockade (OMD/IDS) anzeigen?**
Wie 4a: Technik und Preis **getrennt**; `preis_moeglich=false` + Hinweis „OMD/IDS offen" je Modul. Reife/Technik unberührt.

**9. Welche Tests?**
- Objekt mit WP+PV: DTO listet beide; WP mit Reife+Belastbarkeit+Auslegung, PV `verdrahtet=false`.
- WP-Modul: `heizlast_belastbarkeit` + `auslegung_vorhanden` korrekt durchgereicht.
- Non-WP/PV: kein 500, `verdrahtet=false`, kein `PvProjektService`-Aufruf.
- **Kein-Write:** Row-Counts (`anforderungsprofile`, `anforderungsprofil_werte`, `offers`, `offer_details`, `lead_product_lists`) vorher==nachher.
- **PII-arm:** `json_encode(DTO)` ohne Kundenname.
- Objekt ohne Module / nicht existentes Objekt: sauber (leer / not-applicable).
- Route: auth 302, non-numeric 404, render 200 + Marker.

**10. Welche Bestandsdateien müssten geändert werden?**
**Slice-1: KEINE** — nur neue Dateien (`KonfigurationsprojektService`, `KonfigurationsprojektController`, View, Route-Zeile im `offers`/`objekt`-Block, Test). Die Route-Zeile in `routes/web.php` ist die **einzige** Bestands-Änderung (eine additive `Route::get`-Zeile) → Archiv nach Muster (wie 4a: `web.php.original` + MANIFEST). Objektprofil-Link = **optionaler** Folge-Schritt (dann Blade-Archiv).

**11. Rückfallpfad/Archiv:**
`_archiv/2026-07-14/ap3a-konfigurationsprojekt-sicht/` mit `web.php.original` + MANIFEST. Rückfall = neue Dateien löschen + Route-Zeile zurück. Kein Schema/Daten berührt → path-scoped.

**12. Evaluator:** strikt read-only, keine git-Schreibbefehle, kein Commit/Push. Getrennte Instanz.

## 4. Scope-Grenzen (hart)
read-only · kein Schreibpfad · keine Migration/Tabelle · kein `offer_details`-Write · keine Preis-/Katalog-/3D-/PV-Implementierung · keine Formular-/Historien-Migration · **keine Änderung an bestehenden WP-Gates** (`OfferReadinessService`/`OfferReadinessGate`/`HeizlastBelastbarkeit` werden nur **gelesen**, nicht geändert) · kein `PvProjektService`-Aufruf.

## 5. Vorgehensweise (NACH Freigabe)
1. `KonfigurationsprojektService::fuerObjekt(int $alternativeId): array` — Aggregation über vorhandene read-only Services.
2. `KonfigurationsprojektController::show(int $alternative, KonfigurationsprojektService $s)` → View.
3. View `admin/konfiguration/objekt.blade.php` (Ticket-CI, kein Alpine): Objekt-Kopf + Gewerk-Kacheln (WP verdrahtet, PV/Speicher/Wallbox grau/„folgt"), Technik/Preis getrennt, Belastbarkeits-Chip (AP-1), nächste Aktion, Abhängigkeits-Hinweise. Verlinkt je WP-Modul aufs bestehende 4a-Cockpit (`offers.workflow`).
4. Route additiv + Archiv + Test.

## 6. Stop-Kriterium / Yama-Abnahme
- Startblock endet hier. **Bau erst nach Yama-Freigabe.**
- **STOPP + melden**, falls im Bau (a) eine Migration/Tabelle nötig scheint, (b) ein Schreibpfad nötig würde, (c) der Scope über die read-only Aggregation hinausginge, (d) `PvProjektService` o. Ä. doch aufgerufen werden müsste.
- Yama-Abnahme vor Bau-Freigabe und vor jedem Commit/Push. Evaluator strikt read-only.

*Ende Startblock AP-3a. Read-only, kein Bau, kein Commit, kein Push.*
