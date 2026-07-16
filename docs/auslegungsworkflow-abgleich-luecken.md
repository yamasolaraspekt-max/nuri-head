# Abgleich: dynamischer Auslegungsworkflow ↔ Gesamtfahrplan + Lückenliste

**Stand:** 2026-07-14 · **read-only** · kein Bau. Ergänzt `docs/gesamtfahrplan-gebaeude-energie-angebot.md`.
**Zweck:** Den heute existierenden dynamischen Auslegungsworkflow (`/admin/energie/*`) gegen die Plattform-Kapitel des Gesamtfahrplans stellen und die konkreten Lücken benennen — besonders nach dem Bau von AP-1 (Belastbarkeits-Gate).

---

## 1. Was der dynamische Auslegungsworkflow heute IST (belegt)

Der `/admin/energie/*`-Bereich ist ein **Stand-alone-Rechner-Werkzeug**, nicht am CRM-Objekt verankert:

| Endpoint | Controller | Eingabe | Persistenz | Anker an Objekt/Gewerk? |
|---|---|---|---|---|
| `energie.wp-auslegung(.berechnen)` | `EnergieAuslegungController::wpBerechnen` | **`Request` (Formular)** | keine (JSON zurück) | **nein** |
| `energie.wr-auslegung(.berechnen)` | `EnergieAuslegungController::berechnen` | Formular | keine | nein |
| `energie.heizlast` | `HeizlastController::wpBerechnen` | Formular; legt `HeizlastProjekt` an und **löscht es sofort** (Z. 171) | **transient** | nein |
| `energie.sanierung/energiekonzept/grundriss` | je eigener Controller | Formular | teils `heizlast_projekte`/Grundriss | nein |

**Belege:** `wpBerechnen(Request $request)` nutzt `JazService`, **nicht** `BivalenzService`; kein `Anforderungsprofil`, kein `lead_alternative_adds`/`lead_product_lists`-Bezug. `HeizlastController::wpBerechnen` ruft `WaermepumpenMatchService::kandidaten($heizlastKw, …)` mit frisch gerechneter Heizlast (Z. 180) und verwirft das Projekt.

**Zwei getrennte Welten:**
1. **Rechner-Welt** (`/admin/energie/*`): frei rechnen, kein Objektbezug, keine Speicherung ins Profil.
2. **Reife-/Angebots-Welt** (`OfferReadinessService`, `AuslegungVorschlagService`, 4a-Cockpit): liest die **führende Auslegungswahrheit `Anforderungsprofil`** (objekt-/gewerk-verankert). Hier greift **AP-1** (Belastbarkeits-Gate).

→ Die beiden Welten sind **nicht verdrahtet**: Was im Rechner-Tool berechnet wird, landet nicht automatisch als belastbarer Profil-Wert, den die Reife/das Cockpit lesen. Und `BivalenzService` (die Fach-Krone: Bivalenzpunkt/E-Stab/Laufstunden/Strom) ist an **keiner** Route hängend (0 Aufrufer).

---

## 2. Abgleich mit den Gesamtfahrplan-Kapiteln

| Kapitel | Fahrplan-Soll | Ist im dynamischen Workflow | Lücke |
|---|---|---|---|
| 2 Arbeitsraum | Klammer je Objekt | Rechner ohne Objektbezug | **Anker fehlt** |
| 4 Geometrie | eine Geometrie-Wahrheit | Grundriss-SVG (transient) + `raum_geometrien` | uneinheitlich, nicht am Profil |
| 6 Heizlast | belastbar, am Objekt-Anker | Rechner transient; Profil-Kette getrennt | **Verdrahtung Rechner→Profil fehlt** |
| 7 WP-Konfigurator | Bivalenz/Ranking am Kern, verbindlich nur belastbar | JAZ ohne Bivalenz; Match aus frischem kW; nicht am Profil | **Bivalenz unverdrahtet; Match nicht am Belastbarkeits-Gate** |
| 12 Angebotsübergabe | freigegebene Auslegung → `offer_details` | keine Übergabe aus dem Rechner | Übergabe fehlt (+ Preisanker OMD/IDS blockiert) |
| 13 Freigabe | freigegebene Version | keine Freigabe-Semantik | Freigabe-Marker fehlt |

---

## 3. Lückenliste (priorisiert, read-only Befund — kein Bau)

| # | Lücke | Kapitel | Schwere | Bau-Voraussetzung |
|---|---|---|---|---|
| L-1 | **Rechner-Ergebnis wird nicht als belastbarer Profil-Wert verankert** (Rechner-Welt ↔ Profil-Welt getrennt) | 6/7 | hoch | Klammer/Anker-Weiche (AP-3) |
| L-2 | **`BivalenzService` unverdrahtet** (0 Aufrufer) — E-Stab/Laufstunden/Strom/Bivalenzpunkt unerreichbar | 7 | hoch | Anker + Eingabe-Herkunft geklärt |
| L-3 | **WP-Match nicht am Belastbarkeits-Gate** — der `belastbar`-Parameter (AP-1) ist im `HeizlastController`-Aufrufer noch nicht gesetzt (Naht vorbereitet) | 7 | mittel | Profil-verankerte Auslegung |
| L-4 | **Kein Varianten-Vergleich** (monovalent/monoenergetisch/bivalent + Ranking + Begründung) | 7 | mittel | L-1/L-2 |
| L-5 | **Keine Objekt-Klammer** — Auslegung nicht objekt-granular sichtbar (mehrere Systeme je Objekt) | 2 | hoch | AP-3 |
| L-6 | **PV nicht am versionierten Kern** — eigener Silo, keine PV-Schlüssel in `SchluesselRegistry` | 8 | hoch | PV-Inventur (AP-2) |
| L-7 | **Geometrie uneinheitlich** (SVG vs. 2× three.js vs. `raum_geometrien` vs. `gebaeude_geometrie`-JSON) | 4 | mittel | Geometriemodell-Konzept |
| L-8 | **Übergabe Auslegung→`offer_details` blockiert** (Preisanker OMD/IDS inert) | 12 | mittel | OMD/IDS-Anbindung |
| L-9 | **Kein Freigabe-Marker** (belastbar via „fachlich freigegeben" nicht darstellbar) — offene AP-1-Abhängigkeit | 13 | niedrig | Freigabe-Workflow |
| L-10 | **`auslegungsheizlast_kw` toter Registry-Eintrag** (Adapter schreibt Auslegungswert unter `phi_hl_kw`) — kosmetisch | 6 | niedrig | Aufräumen bei Gelegenheit |

**Wirkung von AP-1 auf die Lücken:** AP-1 schließt die **Bewertungs**-Lücke in der Profil-Welt (geschätzte Heizlast wirkt nicht mehr voll reif) und legt die **Naht** für unverbindliches Ranking (L-3). Es schließt **nicht** L-1 (Rechner→Profil-Verdrahtung) und **nicht** L-2 (Bivalenz) — diese hängen an der Klammer/Anker-Weiche (AP-3) und sind bewusst spätere Slices.

---

## 4. Empfehlung zur Reihenfolge (bestätigt Fahrplan §4)

1. **AP-2 (PV-Inventur, read-only)** — schließt Wissenslücke L-6, blockiert nichts.
2. **AP-3 (Plattform-Klammer-Weiche)** — Voraussetzung für L-1/L-2/L-5.
3. Danach (nach Yama-Freigabe, je eigener Startblock): Rechner→Profil-Verdrahtung (L-1), `BivalenzService` verdrahten (L-2) + Match-Guard aktivieren (L-3), Varianten (L-4).

*Ende Abgleich. Read-only, kein Bau, kein Commit.*
