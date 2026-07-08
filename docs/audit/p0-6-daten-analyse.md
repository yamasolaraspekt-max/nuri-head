# P0-6 — DATEN-Posten: Kaskaden-Analyse (Pflicht-Stopp vor Eingriff)

> ## ✅ AUSGEFÜHRT LOKAL (2026-07-08) — Yama-Freigabe + Klarstellung „Live=lokal"
> `SeedOrphanCleanupService::purge(dryRun:false)` auf lokaler `ticket`-DB (NICHT Hetzner). Zähl-Beleg:
> - verwaiste Objekte aktiv **19 → 0** (soft-deleted: 9,16,24,25,26,27,28,29,31,32,34,38,41,42,44,47,48,49,50)
> - Rechnungen aktiv **11 → 10** · Umsatz aktiv **205.194,48 → 204.194,48 €** (Δ −1.000, Test-Rechnung `id=21`)
> - **übersprungen (referenziert): 0** (alle 19 waren 0-referenziert; Laufzeit-Re-Check bestätigt)
> - Reversibel via `restore()`. Hetzner-Produktion unberührt (eigener Deploy-Tag).


> **Status: ANALYSE FERTIG, KEIN EINGRIFF.** Rein lesend. Yama hat P0-6 als eigenen Daten-Posten freigegeben mit den Auflagen: Kaskaden-Check zuerst · kein Blind-Delete · Backup-Bestätigung · reversibel · gegen `ticket_testing` proben vor Live.
> **⚠️ Diese Analyse lief auf der LOKALEN Dev-DB (Restore).** Für einen echten Eingriff auf der Live-DB muss dieselbe Analyse dort erneut laufen — die hiesigen Waisen sind erkennbar ein Seed-Batch (s. u.) und nicht zwingend repräsentativ für Live.

## Teil A — Die 19 kundenlosen Objekte (`lead_alternative_adds`)

**Befund: alle 19 sind LEER — 0 Referenzen über ALLE ~48 kindtabellen** (erschöpfend geprüft, nicht nur Stichprobe): keine Gewerke (`lead_product_lists`), Angebote, Aufträge, Rechnungen, Termine, Messungen, Projekte, Tasks, Notizen, Tickets, Historie — nichts.

**Herkunft belegt = Seed-Artefakt:** alle 19 mit identischem `created_at = 2026-06-29 01:52:04` (ein Batch), synthetische Namen („Objekt Kiel/Lübeck/…") + Muster-Adressen („Dorfstraße 120"). `lead_id` 11–52 zeigt auf nicht-existente `new_leads` (echte IDs 105–156). → Objekte eines alten Seed-Laufs, deren Eltern-Leads entfernt/re-ID't wurden.

Objekt-IDs: `9, 16, 24, 25, 26, 27, 28, 29, 31, 32, 34, 38, 41, 42, 44, 47, 48, 49, 50`.

**Einordnung nach Yamas Kriterium:** „wirklich leer → löschbar" trifft zu (keine Daten hängen dran, kein Verlust). Kein Sammelkunden-Reassign nötig. **Entscheidung Yama.**

## Teil B — Test-Rechnung `TST-OPEN-2337`

| Beweis | Wert |
|---|---|
| invoice_no | **TST**-OPEN-2337 (Test-Präfix) |
| type | `Rechnung` — **einzige**; die 10 echten sind `type='final'` (`RE-2026-*`) |
| deal_id | 36 (verwaist, existiert nicht in `deals`) |
| Betrag / Datum | 1.000,00 € rund / issue 2026-06-30, created 2026-06-30 22:30:27 (späterer Einzel-Insert) |
| weitere Test-Rechnungen? | **keine** (nur diese eine) |

**Umsatz-Wirkung:**
| | Anzahl | Σ total_amount |
|---|--:|--:|
| alle Rechnungen (Ist) | 11 | 205.194,48 € |
| ohne TST-OPEN-2337 | 10 | **204.194,48 €** |
| nur `type='final'` (echt) | 10 | 204.194,48 € |

→ Entfernen bereinigt den Umsatz exakt auf die Summe der echten `final`-Rechnungen (−1.000 €). **Beweisbar Testdaten.**

## Vorgeschlagenes Vorgehen (nach Yama-Entscheidung, NICHT ausgeführt)
1. **Reversibles Skript** schreiben: (a) `DELETE` der 19 leeren Objekte (nur wenn Live-Re-Check erneut 0 Referenzen zeigt), (b) `DELETE`/Markieren von invoices id=21. Vorher: `SELECT`-Snapshot der zu ändernden Zeilen in eine Rückroll-Datei.
2. **Gegen `ticket_testing` proben** (Idempotenz + Zähl-Invariante: invoices 11→10, Σ 205.194,48→204.194,48; Objekte −19).
3. **Backup-Bestätigung** (Ramin/DB-Stand) **vor Live**.
4. Eigener Commit, Zähl-Beleg vorher/nachher, kein Beifang.

## Offene Entscheidungen (Yama)
- **19 Objekte:** löschen (leer, empfohlen) — oder behalten/markieren?
- **TST-OPEN-2337:** hart löschen — oder als `storniert`/Testmarker behalten (Trail)?
- **Ziel:** nur lokale Dev-DB aufräumen — oder Skript für Live vorbereiten (dann Backup + Live-Re-Check + Ramin)?
