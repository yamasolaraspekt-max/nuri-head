# Roadmap: wberechnung-Funktionen in ticket sichtbar machen

> **Zweck:** Ein belegter, einseitiger Fahrplan — *warum heute nichts sichtbar ist*, *was je Schicht fehlt*, und
> *ab welchem Meilenstein du es in der ticket-Navigation siehst*. Eingepasst an tickets Konventionen
> (Bereich 8 „Energie & Auslegung", Vuexy/Blade + Alpine, `ticket_testing`→produktiv-Disziplin, NAV-01 v2).
> **Bezugsdokumente (bindend):** `heizkoerper-bauplan.md`, `heizkoerper-bestandsanalyse.md`,
> `katalog-reconciliation-plan.md`, `wberechnung-transplant-vorbereitung-landkarte.md`, NAV-01. Stand 2026-07-04.

## 0. Ist-Stand (belegt) — warum du nichts siehst
- **Echte `ticket`-DB:** 0 Heizkörper-/Energie-Tabellen. Alles Gebaute liegt **nur auf `ticket_testing`**.
- **Nav heute:** „Heizkörper-Konfigurator" (`radiator.config.view` = nur Bestandsaufnahme, keine EN-442-Rechnung) und
  „PV-Planer (PVGIS)" (`admin.pvgis.index`) sind **tickets eigene dormante Prototypen**, nicht wberechnung.
- **Portiert:** keine Rechenkerne/Controller/Routen/Views. **Nav-Eintrag zu einer Engine: keiner.**
- **Fertig (nur Test-DB):** Heizkörper Stufe (i) Schema · (ii-a) Models · (ii-b) EN-442-Katalog (30 Kermi-Zeilen).
  Cut-over-Strang: WR/Bat/PV/WP Schema-Additive (Stufe i, nur Test-DB).

## 1. Die 5 Schichten (keine ist für Nutzer fertig)
Jede wberechnung-Funktion braucht **alle fünf**, damit du sie siehst:
1. **Datenschicht in der ECHTEN `ticket`-DB** (heute nur `ticket_testing`).
2. **Rechenkern + Controller portiert** — über die **Adapter-Schicht**, die aus tickets Katalog liest (Kern unverändert).
3. **Routen** unter ticket-Web-Guard.
4. **Views/UI im ticket-Design neu gebaut** (Vuexy/Blade + Alpine, W8) — der größte Handarbeits-Posten.
5. **Nav verdrahtet** — Bereich 8 „Energie & Auslegung" zeigt auf die neuen Routen.

## 2. Zwei Stränge (beide landen in Bereich 8)
- **Strang A — Heizkörper-Modul:** am weitesten (Fundament auf Test-DB steht). Wird **jetzt** gebaut. Erster
  sichtbarer Nav-Eintrag entsteht hier.
- **Strang B — Rest** (Energiekonzept, WP-/WR-Auslegung, PVGIS, Sanierung, Checks): die **Phase-1.4-Transplantation**,
  **an den Cut-over gebunden** (wberechnung wächst noch; vgl. Cut-over-Kriterium der Landkarte).

## 3. Meilenstein-Fahrplan (Strang A → erster sichtbarer Nav-Eintrag)
| MS | Inhalt | Gate davor | Sichtbar? | Aufwand |
|---|---|---|---|---|
| **M0** ✅ | Fundament auf Test-DB: Schema (i), Models (ii-a), EN-442-Katalog (ii-b) | — | nein | erledigt |
| **M1** | (ii-c) Ventiltechnik-Seeder (11 belegte Zeilen) | **Report-Freigabe** (`heizkoerper-ventiltechnik-quellen.md`) | nein | S |
| **M2** | (iii) IDS-Mapper (`SupplierArticleMapper`+`IdsMapper` auf Sonepar), OMD/Datanorm-Stubs | Supplier-Stack-Entscheid (A4) | nein | M |
| **M3** | (iv) Kompatibilitäts-Service (Regeln D3) **+ Rechenkern-Port** (`RadiatorPerformanceService`/`HydraulicService` via Adapter aus ticket-Katalog) | — | nein (nur API/Tests) | M |
| **M4** | (v) **UI + Nav** — Aufnahme (auf `radiator_installations`) · EN-442-Ergebnis mit Ampel · Stücklisten-Ansicht · **Nav-Eintrag Bereich 8** | Alpine-Freigabe (CLAUDE.md-Eintrag) | **✅ ERSTER SICHTBARER MEILENSTEIN** | L |
| **M5** | Migrationen (i)+(ii)+Zubehör **produktiv in `ticket`** | Prod-Migration-Gate (§5) | **✅ live für Nutzer** | S–M |

→ **Ab M4 siehst du das Heizkörper-Modul in der Navi** (auf einer Test-/Staging-Sicht); **ab M5 ist es für echte Nutzer live.**

## 4. Strang B (Rest) — nach Cut-over
| MS | Inhalt | Gate | Sichtbar |
|---|---|---|---|
| **B1** | Katalog-Reconciliation ausführen (PV/WR/WP-Spec additiv, wb-Katalogdaten importieren) — `katalog-reconciliation-plan.md` Stufen (i)–(ii) | Cut-over + Supplier-/Katalog-Entscheide | nein |
| **B2** | Rechenkerne+Controller portieren (WR-Sizing, WP-Kennlinie, PVGIS, Sanierung) via Adapter | B1 | nein |
| **B3** | Views neu bauen (Konfiguratoren, Ampeln, Protokoll-PDF) im ticket-Design | B2 | teils |
| **B4** | Bereich 8 vollständig verdrahten (Konfigurator/WR-WP-Auslegung/Energiekonzept) | B3 | **✅ Rest sichtbar** |
| **B5** | Plan-Import + Grundriss-Editor (schwerste Views, Queue/Storage) | eigene Welle (W6) | optional später |

## 5. Passend zu ticket — konkrete Einpassung (damit es sich nicht „fremd" anfühlt)
- **Navigation:** Bereich 8 „Energie & Auslegung" existiert im NAV-01-Plan. Der Nav-Eintrag entsteht **exakt wie bei
  Fläche 1** (Sidebar-Sektion mit `$safeRoute(...)`, `active_routes`, ggf. `permission`-Gate), NAV-01-Schicksals-Tabelle
  wird nachgeführt. Kein neues Nav-Muster.
- **UI:** Vuexy/Blade als Rahmen; **Alpine nur fürs Heizkörper-Modul** (W8, Eintrag in neuer `ticket/CLAUDE.md`). Das
  fertige Konfigurator-/Ampel-/Szenario-Muster kommt 1:1 aus wberechnung (Funktion), Aussehen = ticket (Vuexy).
- **Wiederverwendung statt Parallelbau:** Bestandsaufnahme = `radiator_installations` (erweitert, B3) · Zubehör/Stückliste
  = `master_set_components` (`type='zubehoer'`) + `deal_measurement_items` (B11/B14) · Katalog = `products`+Spec-Tabellen
  (ein Katalog, keine Doppelung).
- **Rechenkern unberührt & katalogtreu:** der Adapter rechnet `q_norm_w = q_norm_w_pro_m × width × anzahl` (Notiz Bauplan §6);
  bauart-Übersetzung `platte→kompakt` dokumentiert.
- **Test-Isolation:** jede Stufe zuerst gegen `ticket_testing` (Isolation verdrahtet), Rollback-Beweis via
  `scripts/ticket-mysql-check.sh`.

## 6. Produktions-Migration (M5) — die echte `ticket`-DB (~3.000 Kunden)
Damit M5 sicher ist:
1. **DB-Backup** unmittelbar davor (Snapshot/Dump).
2. **Nur additiv** — die Migrationen sind ausschließlich `ADD COLUMN`/`CREATE TABLE` (keine Änderung an Bestandsdaten,
   `ticket.radiators` unberührt) und **rollback-bewiesen** auf `ticket_testing`.
3. **Verbindungs-Guard**: Migration bewusst gegen `DB_DATABASE=ticket` fahren (nicht Override) — nach dem Backup,
   idealerweise im Randzeit-Fenster.
4. **Verifikation danach:** Tabellen vorhanden, Bestandszahlen unverändert, App bootet, bestehende Tests grün.

## 7. Offene Gates/Entscheidungen (blockieren „alle Funktionen sichtbar")
1. **Ventiltechnik-Report-Freigabe** (M1) — wartet.
2. **Supplier-Stack** (M2/A4): ticket-IDS vs. wberechnung-OMD/DATANORM-ETIM — eine Zielarchitektur.
3. **Alpine-Freigabe** (M4) — Eintrag „Alpine nur Heizkörper-Modul" in neuer `ticket/CLAUDE.md`.
4. **Prod-Migration-Fenster** (M5) — wann in die echte `ticket`-DB.
5. **Cut-over-Zeitpunkt** (Strang B) — wberechnung-Migrations-Kurve muss abflachen.

## 8. Empfohlene Reihenfolge (kürzester Weg zu „sichtbar")
**(ii-c freigeben) → M1 → M2 → M3 → M4 [erster Nav-Eintrag] → M5 [live].** Strang B erst, wenn wberechnung
sich beruhigt (Cut-over). Realistisch ist **M4 der Punkt, an dem die Investition sichtbar wird** — davor ist alles
Fundament (DB/Logik/Tests), das man in der Navi nicht sieht, aber ohne das die UI nichts anzuzeigen hätte.

> **Kurzfassung für die Entscheidung:** „Alle wberechnung-Funktionen in ticket" = Strang A bis **M5** (Heizkörper live)
> **plus** Strang B bis **B4** (Rest live) nach dem Cut-over. Der erste sichtbare Gewinn ist **M4**; davor liegen
> (ii-c)→(iv), die reine Fundament-/Logik-Arbeit sind.
