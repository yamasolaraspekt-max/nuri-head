# AUDIT 03 — SWOT (Phase 1C, belegt)

> Aus 1A (Fehler) + 1B (Architektur). Fair-Maßstab: gewachsenes Live-System, kein Greenfield. Belege verweisen auf 01/02.

## ✅ Stärken (SCHÜTZEN — Haus-Qualitätsmaßstab)
| Stärke | Beleg | Prinzip |
|---|---|---|
| FK-Kanban-Kette (Hook+Fold+Fallback+Wächter) | `lead_stages`/`lead_stage_sub_stages` aktiv (403 Refs), Weiche-1-konform | Eine Phasen-Wahrheit, Zähl-Invariante |
| Test-Harness ([TEST-HARNESS], Teardown-0, Idempotenz) | 148 Unit grün, Feature-Suite | Reproduzierbar, restlos rückbaubar |
| Objekt-Klammer (Kunde→Objekt→Gewerk) | Glossar ratifiziert, Kette sauber verdrahtet | Ein Rückgrat für alle Domänen |
| `invoices`-Schiene sauber + gegated | `InvoiceMiddleware`; deal_invoices nur Kommentare (Arch 1c) | Eine Umsatz-Wahrheit, Rechte explizit |
| In-house-FiBu (neu, getestet) | app/Services/Accounting, 20 Tests, GoBD-Gates | Service-Schicht + Beweis-Tests = Vorbild |
| Formular-Engine (eval-frei) | FS-07 `new Function` raus, Server-Autorität | Sicherheit vor Bequemlichkeit |
| Branchen-Fachlogik (Heizlast/PV/WP) | app/Services/Heizkoerper, Energie-Tools | Alleinstellung, echter Fachwert |

## ❌ Schwächen (nach Betriebsschaden priorisiert)
| Schwäche | Beleg | Schaden |
|---|---|---|
| **Anonyme Schreibrouten** auf HR/Lohn/Kunde/Belegkette | SEC-0 (P0): web.php:1738/1772/3540, ImageController, CustomerStageController | Datenverlust/-manipulation **ohne Login** |
| **IDOR verbreitet** (Account-Takeover, Lohn, Medizin) | SEC-1 (P0): UserController:544, EmployeeController:687, LeaveController, EmployeeSickController | jeder Login trifft fremde Daten |
| Gott-Klassen | NewLeadsController 14.054 · PlannerPlan 11.097 · offer-config-Blade 25.064 (Arch 2) | Bus-Faktor, unwartbar, Bruch-anfällig |
| Logik/RBAC in Blades | 81 Live-Blades raw `DB::`, RBAC in `offer_view.blade.php:495` (Arch 3) | Schichtbruch, N+1, Rechte-Umgehung |
| Status-Zoo | 139 varchar-Status, 12 Spalten/Gewerk (Arch 1a/4a, DI-5) | wackelige Auswertungen, stille Divergenz |
| 57 tote Routen | RT-1 (500 bei Aufruf) | Funktionsbrüche im Alltag |
| FK-Waisen | DI-1 (P0): 19 Objekte kundenlos | „Kunde nicht gefunden" |

## 🅾️ Chancen
- **Auslegungs-Suite** (Heizlast/PV/WP) als Alleinstellung ausbauen (Marktvorteil).
- **Formular-Engine + Smartrouting** (FS-02/04/05 gebaut) → kontextgeführte Pflicht-Checks über alle Gewerke.
- **In-house-Accounting** (FiBu i–viii) → Controlling/BWA/DATEV ohne Kanzlei-Abhängigkeit.
- **Kundendienst/Wartungsverträge** (Bereich 9) = wiederkehrender Umsatz.

## ⚠️ Risiken (Wachstums-Bruchstellen)
| Risiko | Auslöser | Beleg/Schwelle |
|---|---|---|
| N+1-Kollaps | summaries + 120 Query-in-Blade | bei 200 Karten/Board spürbar (bekannte Schuld) |
| Gott-Klassen unwartbar | jede Änderung an 14k-Zeilen-Controller | Bus-Faktor, hohes Regressionsrisiko |
| Status-Divergenz | 139 freie varchar wachsen | bei mehr Schreibpfaden stille Fehler |
| Skalierung 10k Kunden | fehlende Indizes (Stichprobe), breite Kern-Tabellen | Query-Last (Teil 1D CODE-AUDIT-01 vertieft) |
| Parallel-Instanzen | mehrere Bau-Stränge auf main | Vendor-/Test-DB-Regeln (STRAENGE) mildern |

**Selbstkritik:** SWOT ruht auf statischer Analyse + read-only SQL; Live-Last (echte Query-Zeiten, JS-Konsole) NICHT gemessen. Chancen sind Einschätzung, keine Marktdaten.
