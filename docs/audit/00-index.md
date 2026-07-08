# AUDIT MASTER-01 — Index (Phase 1 + 2)

> **Status: IN ARBEIT (Start 2026-07-08).** Rein lesend. Live-System (~3000 Kunden). Gesetz: nichts bricht, alles belegt, jede Abweichung offen. Ergebnis dieser Phase endet an **STOPP 1** (P0-Liste + Architektur-Urteil je Bereich GRÜN/GELB/ROT + Reihenfolge-Empfehlung).

## Dokumente
- **00-index.md** (dies) — Scope, Methode, 15 Kern-Aktionen, Fortschritt.
- **01-fehler-inventur.md** — 1A: Routen-Sweep, 15-Aktionen-Durchspiel, Daten-Integrität, Sicherheit, JS-Konsole. Je Fund: Fundort · Repro · Schwere P0–P3 · Rolle.
- **02-architektur.md** — 1B: Doppel-Wahrheiten, Gott-Klassen, Schichten, Datenmodell, Konzept-Treue.
- **03-swot.md** — 1C: Stärken/Schwächen/Chancen/Risiken, belegt.

## Verbindlicher Rahmen (Weichen = Gesetz)
Kette: **Kunde (`new_leads`) → Objekt (`lead_alternative_adds`, Objekt-ID=`alternative_id`) → Gewerk (`lead_product_lists`) → Angebot (`offers`) → Auftrag (`deals`) → Rechnung (`invoices`, führend; `deal_invoices` schlafend)**. 6 Phasen (`lead_stages`): Lead·Angebot·Auftrag·Montage·Abnahme·Abschluss. Objekt klammert · Planner=Feld-Wahrheit · EIN Katalog · in-house-FiBu.

**TABU-Zonen (nicht als Fehler werten, nicht anfassen):** Nuriva-APIs byte-identisch · Video/Jitsi · Invoice-/Accounting-Zone · laufende Stränge Cut-over/Heizkörper/B2a · Legacy Bitrix/NIBE/IMAP.

## Die 15 Kern-Aktionen (Yama bestätigt 2026-07-08) — Durchspiel-Grundlage 1A
| # | Kern-Aktion | Kette / Tabelle | Rolle |
|---|---|---|---|
| 1 | Anfrage/Kunde anlegen (+ Duplikat-Check) | `new_leads` | Vertrieb |
| 2 | Objekt zum Kunden anlegen | `lead_alternative_adds` | Vertrieb |
| 3 | Gewerk am Objekt anlegen („PV Müller") | `lead_product_lists` | Vertrieb |
| 4 | Lead durch die Phasen ziehen (Kanban) | `lead_stages` | alle |
| 5 | Angebot erstellen (aus Sets/Artikeln) | `offers` | Vertrieb |
| 6 | Angebot → Auftrag wandeln (Zusage) | `deals` | Innendienst |
| 7 | Materialliste/Bestellliste aus Auftrag | Bestell-/Materiallisten | Einkauf |
| 8 | Montageplan planen (Vorlage/Set, Personal) | `planner_items` | Projektleiter |
| 9 | Monteur-Aufgabe abschließen (Foto) + Rückfluss | `planner_items`→Büro | Monteur/PL |
| 10 | Aufmaß/Auslegung erfassen (Heizlast/PV) | Energie-Tools | Technik |
| 11 | Checkliste/Formular ausfüllen | `product_formulas` | Technik |
| 12 | Rechnung erstellen + festschreiben | `invoices` | Buchhaltung |
| 13 | Wiedervorlage/Aufgabe/Termin setzen | `personal_tasks` | alle |
| 14 | Kundendienst-Ticket/Reklamation anlegen | Tickets/Reklamationen | Service |
| 15 | Dokument/Foto in Kundenakte hochladen | Kundenakte | alle |

## Methode & Grenzen
- **Rein lesend.** Kein Mutations-HTTP auf Live-Daten. Das „Durchspielen" der 15 Aktionen erfolgt als **Code-Pfad-Trace** (Controller→Model→DB) + read-only GET-Proben + read-only SQL — mutierende Schritte (anlegen/löschen) werden **analysiert, nicht ausgeführt**. Wo nur statisch geprüft: **NICHT-VERIFIZIERT**-Marker.
- **JS-Konsole Top-10:** braucht laufenden Browser → als **manueller Nachtrag** markiert (nicht headless belegbar).
- Belege wörtlich (Datei:Zeile, SQL-Zahlen). Gelesen/Nicht-gelesen geführt.

## Fortschritt
- [x] Kontext-Pflicht gelesen (Glossar, Weichen 1–6, NAV-01)
- [x] 15 Kern-Aktionen bestätigt (Yama)
- [x] 1A Routen-Sweep (2363 Routen, 57 tot) · Daten-Integrität (P0 FK-Waisen) · Sicherheit (P0 anonyme Routen) → `01-fehler-inventur.md`
- [x] 1B Architektur (Doppel-Wahrheiten, Gott-Klassen) → `02-architektur.md`
- [x] 15-Aktionen-Code-Pfad-Trace (statisch, in 01)
- [x] 1C SWOT → `03-swot.md`
- [x] **STOPP 1** → `stopp-1.md` (P0-Liste + Architektur-Urteil GRÜN/GELB/ROT + Reihenfolge)
- [ ] JS-Konsole Top-10 (manueller Browser-Nachtrag)
- [ ] Phase 2 Zielbild (`docs/architektur/zielbild-domaenen.md`) → STOPP 2
- [~] entkoppelt: CODE-AUDIT-01 (`code-audit.md` + `bauordnung.md`) läuft
